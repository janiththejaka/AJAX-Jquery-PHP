<?php

session_start();

include 'config/db_config.php';

if (isset($_POST['action'])) {

    $action = $_POST['action'];

    switch ($action) {
        case 'get_files':
            $files = getFiles();
            echo json_encode(['status' => true, 'data' => $files]);
            break;

        case 'upload_file':
            uploadFiles();
            break;

        case 'get_file':
            getFileforModal();
            break;

        case 'upload_file':
            uploadFile();
            break;

        case 'modify_file':
            modifyFile();
            break;

        case 'delete_file':
            deleteFile();
            break;

        default:

            echo json_encode([
                'status' => false,
                'msg' => 'Invalid action.'
        ]);

        break;
    }
}        
   
/* Utility Functions*/

function validateUploadedFile($file)
{
    if (!isset($_POST['fileName']) || !isset($_FILES['file'])) {

        echo json_encode([
            'status' => false,
            'msg' => 'File name and file are required.'
        ]);

        exit;
    }

    $file = $_FILES['file'];
    $remarkName = trim($_POST['fileName']);
    $originalFileName = $file['name'];
    

    if ($remarkName  == '') {

        echo json_encode([
            'status' => false,
            'msg' => 'File name cannot be empty.'
        ]);

        exit;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {

        echo json_encode([
            'status' => false,
            'msg' => 'File upload failed.'
        ]);

        exit;
    }

    // Maximum file size: 20 MB
    $maxFileSize = 20 * 1024 * 1024;

    if ($file['size'] > $maxFileSize) {

        echo json_encode([
            'status' => false,
            'msg' => 'File size must not exceed 20 MB.'
        ]);

        exit;
    }

    // Get file extension
    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    // Allowed extensions
    $allowedExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'jpg',
        'jpeg',
        'png'
    ];

    if (!in_array($extension, $allowedExtensions, true)) {

        echo json_encode([
            'status' => false,
            'msg' => 'This file type is not allowed.'
        ]);

        exit;
    }

    // Detect actual MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mimeType = $finfo->file($file['tmp_name']);

    // Allowed MIME types
    $allowedMimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ];

    if (
        !isset($allowedMimeTypes[$extension]) ||
        $mimeType !== $allowedMimeTypes[$extension]
    ) {

        echo json_encode([
            'status' => false,
            'msg' => 'The file type does not match its extension.'
        ]);

        exit;
    }
    return [
        'status' => true,
        'extension' => $extension,
        'mime_type' => $mimeType
    ];
}

/*--------------Action Function ---------------------*/

function getFiles() {
    global $conn;

   $sql = $sql = "SELECT
            uuid,
            original_file_name,
            remark_name,
            updated_at
            FROM file_archive
            WHERE is_active = 1
            ORDER BY updated_at DESC";

    $result = $conn->query($sql);
    $files = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }
    }
    return $files;
}


function uploadFiles()
{
    global $conn;
    /*Check required fields.*/

    if (!isset($_POST['fileName']) || !isset($_FILES['file'])) {

        echo json_encode(['status' => false,
            'msg' => 'Remark name and file are required.'
        ]);

        exit;
    }


    /*User-entered remark.*/

    $remarkName = trim($_POST['fileName']);

    if ($remarkName === '') {
        echo json_encode(['status' => false,'msg' => 'Remark name cannot be empty.']);
        exit;
    }

    /*Uploaded file. */

    $file = $_FILES['file'];

    /*
     * Validate file using our
     * common validation function.
     */

    $validation = validateUploadedFile($file);

    if (!$validation['status']) {echo json_encode($validation);
        exit;
    }

    $extension = $validation['extension'];
    $mimeType = $validation['mime_type'];

    /*Original filename from user upload.*/

    $originalFileName = $file['name'];

    /*Generate UUID.*/

    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );


    /*Server-side stored filename.*/

    $storedFileName = $uuid . '.' . $extension;
    /*
     * Physical upload directory.
     */
    $uploadDirectory = __DIR__ . '/uploads/file_archive/';
    $filePath = $uploadDirectory . $storedFileName;

    /* Path stored in database.*/
    $databaseFilePath ='uploads/file_archive/' . $storedFileName;

    /* Move uploaded file.*/
    if (!move_uploaded_file(
        $file['tmp_name'],
        $filePath)) {

        echo json_encode(['status' => false, 'msg' => 'Unable to save uploaded file.']);
        exit;
    }


    /* Insert database record. */

    $sql = "INSERT INTO file_archive (
                uuid,
                original_file_name,
                remark_name,
                stored_file_name,
                file_path,
                file_size,
                file_type
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssis',
        $uuid,
        $originalFileName,
        $remarkName,
        $storedFileName,
        $databaseFilePath,
        $file['size'],
        $mimeType
    );

    if (!$stmt->execute()) {

        /*
         * Database failed.
         *
         * Remove physical file.
         */

        if (file_exists($filePath)) {
            unlink($filePath);
        }


        echo json_encode(['status' => false, 'msg' => 'Unable to save file information.']);
        exit;
    }


    echo json_encode(['status' => true, 'msg' => 'File uploaded successfully.']);

    exit;
}

/* get file for the modal to edit */
function getFileforModal()
{
    global $conn;

    if (!isset($_POST['uuid'])) {

        echo json_encode(['status' => false,'msg' => 'File UUID is required.']);
        exit;
    }

    $uuid = $_POST['uuid'];

    $sql = "SELECT
                uuid,
                original_file_name,
                remark_name,
                file_size,
                file_type,
                created_at,
                updated_at
            FROM file_archive
            WHERE uuid = ?
            AND is_active = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => false,'msg' => 'File not found.']);
        exit;
    }

    $file = $result->fetch_assoc();

    echo json_encode(['status' => true, 'data' => $file]);
    exit;
}


function modifyFile()
{
    global $conn;
    /*
     * -------------------------
     * 1. Validate request
     * -------------------------
     */
    if (!isset($_POST['uuid'])) {
        echo json_encode(['status' => false, 'msg' => 'File UUID is required.']);
        exit;
    }

    if (!isset($_POST['remarkName'])) {
        echo json_encode(['status' => false, 'msg' => 'Remark name is required.']);
        exit;
    }

    $uuid = $_POST['uuid'];
    $remarkName = trim($_POST['remarkName']);

    if ($remarkName === '') {
        echo json_encode(['status' => false, 'msg' => 'Remark name cannot be empty.']);
        exit;
    }
    /*
     * -------------------------
     * 2. Get existing record
     * -------------------------
     */
    $sql = "SELECT
                uuid,
                original_file_name,
                remark_name,
                stored_file_name,
                file_path,
                file_size,
                file_type
            FROM file_archive
            WHERE uuid = ?
            AND is_active = 1";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        echo json_encode([ 'status' => false,'msg' => 'File not found while the modifying.']);
        exit;
    }

    $existingFile = $result->fetch_assoc();

    /*
     * -------------------------
     * 3. Check for new file
     * -------------------------
     */

    $hasNewFile = isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE;
    /*
     * -------------------------
     * 4. Remark-only update
     * -------------------------
     */

    if (!$hasNewFile) {

        $sql = "UPDATE file_archive
                SET remark_name = ?
                WHERE uuid = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ss',
            $remarkName,
            $uuid
        );

        if (!$stmt->execute()) {
            echo json_encode(['status' => false, 'msg' => 'Unable to update remark name.']);
            exit;
        }

        echo json_encode(['status' => true, 'msg' => 'File information updated successfully.']);
        exit;
    }
    /*5. New file exists */

    $newFile = $_FILES['file'];

    /* Validate using our common validation function.*/

    $validation =validateUploadedFile($newFile);

    if (!$validation['status']) {

        echo json_encode( $validation);
        exit;
    }

    $extension = $validation['extension'];
    $mimeType = $validation['mime_type'];
    $originalFileName = $newFile['name'];
   
     /* 6. Paths*/

    $uploadDirectory = __DIR__ . '/uploads/file_archive/';

    /*New final filename.*/

    $newStoredFileName =$uuid . '.' . $extension;
    $newFinalPath = $uploadDirectory . $newStoredFileName;
    $newDatabaseFilePath ='uploads/file_archive/' .$newStoredFileName;

    /*Existing physical file.*/
    $oldFilePath = __DIR__ . '/' .$existingFile['file_path'];

    /*
     * Temporary new filename.
     *
     * This avoids collision with
     * the existing file.
     */

    $temporaryFileName =$uuid .'_new_' . uniqid().'.' .$extension;
    $temporaryFilePath = $uploadDirectory .$temporaryFileName;

    /* 7. Save new filetemporarily */

    if (!move_uploaded_file( $newFile['tmp_name'], $temporaryFilePath)) {

        echo json_encode(['status' => false, 'msg' => 'Unable to save replacement file.']);
        exit;
    }

    /* 8. Backup old file */

    $oldBackupPath = $uploadDirectory .$uuid .'_old_' .uniqid() .'_' .$existingFile['stored_file_name'];

    if (file_exists($oldFilePath) &&!rename($oldFilePath, $oldBackupPath)) {

        /*
         * Cannot move old file.
         *
         * Remove temporary new file.
         */

        if (file_exists($temporaryFilePath)) {unlink($temporaryFilePath);}

        echo json_encode(['status' => false, 'msg' => 'Unable to prepare the existing file for replacement.']);
        exit;
    }


    /*
     * -------------------------
     * 9. Move new file
     * to final UUID filename
     * -------------------------
     */

    if (!rename($temporaryFilePath, $newFinalPath)) {

        /*
         * New file could not be
         * moved to final location.
         *
         * Restore old file.
         */

        if (file_exists($oldBackupPath)) {
            rename($oldBackupPath, $oldFilePath);
        }


        if (file_exists($temporaryFilePath)) {
            unlink($temporaryFilePath);
        }


        echo json_encode(['status' => false, 'msg' => 'Unable to prepare the replacement file.']);
        exit;
    }


    /*
     * -------------------------
     * 10. Update database
     * -------------------------
     */

    $sql = "UPDATE file_archive
            SET
                original_file_name = ?,
                remark_name = ?,
                stored_file_name = ?,
                file_path = ?,
                file_size = ?,
                file_type = ?
            WHERE uuid = ?";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssiss',
        $originalFileName,
        $remarkName,
        $newStoredFileName,
        $newDatabaseFilePath,
        $newFile['size'],
        $mimeType,
        $uuid
    );

    if (!$stmt->execute()) {

        /*
         * DATABASE UPDATE FAILED.
         *
         * Remove new final file.
         */

        if (file_exists($newFinalPath)) {
            unlink($newFinalPath);
        }
        /*
         * Restore old file.
         */

        if (file_exists($oldBackupPath)) {
            rename( $oldBackupPath, $oldFilePath);
        }

        echo json_encode(['status' => false, 'msg' => 'Unable to update file information.']);
        exit;
    }

    /*
     * -------------------------
     * 11. Database succeeded
     * -------------------------
     *
     * New file is now active.
     *
     * Old backup can be removed.
     */

    if (file_exists($oldBackupPath)) {unlink($oldBackupPath);}

    echo json_encode(['status' => true,'msg' => 'File modified successfully.']);
    exit;
}


?>