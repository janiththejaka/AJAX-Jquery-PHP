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
            uploadFiless();
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

    // Generate UUID
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

    // Generate server-side filename
    $storedFileName = $uuid;

    if ($extension != '') {
        $storedFileName .= '.' . $extension;
    }

    // Physical file path
    $uploadDirectory = __DIR__ . '/uploads/file_archive/';

    $filePath = $uploadDirectory . $storedFileName;

    // Path stored in database
    $databaseFilePath =
        'uploads/file_archive/' . $storedFileName;

    // Save physical file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {

        echo json_encode([
            'status' => false,
            'msg' => 'Unable to save uploaded file.'
        ]);

        exit;
    }

    // Save database information
    $sql = "INSERT INTO file_archive (
            uuid,
            original_file_name,
            remark_name,
            stored_file_name,
            file_path,
            file_size,
            file_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

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

        // Remove physical file if database insert fails
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        echo json_encode([
            'status' => false,
            'msg' => 'Unable to save file information.'
        ]);

        exit;
    }

    echo json_encode([
        'status' => true,
        'msg' => 'File uploaded successfully.'
    ]);

    exit;
}




function uploadFiless()
{
    global $conn;


    /*
     * Check required fields.
     */

    if (
        !isset($_POST['fileName']) ||
        !isset($_FILES['file'])
    ) {

        echo json_encode([
            'status' => false,
            'msg' => 'Remark name and file are required.'
        ]);

        exit;
    }


    /*
     * User-entered remark.
     */

    $remarkName = trim(
        $_POST['fileName']
    );


    if ($remarkName === '') {

        echo json_encode([
            'status' => false,
            'msg' => 'Remark name cannot be empty.'
        ]);

        exit;
    }


    /*
     * Uploaded file.
     */

    $file = $_FILES['file'];


    /*
     * Validate file using our
     * common validation function.
     */

    $validation = validateUploadedFile(
        $file
    );


    if (!$validation['status']) {

        echo json_encode($validation);

        exit;
    }


    $extension = $validation['extension'];

    $mimeType = $validation['mime_type'];


    /*
     * Original filename from user upload.
     */

    $originalFileName = $file['name'];


    /*
     * Generate UUID.
     */

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


    /*
     * Server-side stored filename.
     */

    $storedFileName =
        $uuid . '.' . $extension;


    /*
     * Physical upload directory.
     */

    $uploadDirectory =
        __DIR__ . '/uploads/file_archive/';


    $filePath =
        $uploadDirectory . $storedFileName;


    /*
     * Path stored in database.
     */

    $databaseFilePath =
        'uploads/file_archive/' . $storedFileName;


    /*
     * Move uploaded file.
     */

    if (!move_uploaded_file(
        $file['tmp_name'],
        $filePath
    )) {

        echo json_encode([
            'status' => false,
            'msg' => 'Unable to save uploaded file.'
        ]);

        exit;
    }


    /*
     * Insert database record.
     */

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


        echo json_encode([
            'status' => false,
            'msg' => 'Unable to save file information.'
        ]);

        exit;
    }


    echo json_encode([
        'status' => true,
        'msg' => 'File uploaded successfully.'
    ]);

    exit;
}


?>