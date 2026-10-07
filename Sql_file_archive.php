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
    }
}        
   


function getFiles() {
    global $conn;

    $sql = "SELECT uuid, file_name, updated_at
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


function uploadFiles(){

    global $conn;

    if (!isset($_POST['fileName']) || !isset($_FILES['file'])) {

        echo json_encode(array(
            'status' => false,
            'msg' => 'File name and file are required.'
        ));

        exit;
    }

    $fileName = trim($_POST['fileName']);
    $file = $_FILES['file'];

    if ($fileName == '') {

        echo json_encode(array(
            'status' => false,
            'msg' => 'File name cannot be empty.'
        ));

        exit;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {

        echo json_encode(array(
            'status' => false,
            'msg' => 'File upload failed.'
        ));

        exit;
    }

    /*Generate UUID*/
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

    /*Get original extension */
    $extension = pathinfo(
        $file['name'],
        PATHINFO_EXTENSION
    );

    $extension = strtolower($extension);

    /*Create physical filename*/
    $storedFileName = $uuid;

    if ($extension != '') {
        $storedFileName .= '.' . $extension;
    }

    /* Storage location*/
    $uploadDirectory = __DIR__ . '/uploads/file_archive/';

    $filePath = $uploadDirectory . $storedFileName;

    /*Relative path stored in database*/
    $databaseFilePath =
        'uploads/file_archive/' . $storedFileName;

    /*Move uploaded file*/
    if (!move_uploaded_file(
        $file['tmp_name'],
        $filePath
    )) {

        echo json_encode(array(
            'status' => false,
            'msg' => 'Unable to save uploaded file.'
        ));

        exit;
    }

    /*Insert metadata into database*/
    $sql = "INSERT INTO file_archive (
                uuid,
                file_name,
                stored_file_name,
                file_path,
                file_size,
                file_type
            ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        'ssssds',
        $uuid,
        $fileName,
        $storedFileName,
        $databaseFilePath,
        $file['size'],
        $file['type']
    );

    if (!$stmt->execute()) {

        /*
         * Database failed.
         * Remove the physical file because
         * we don't want an orphan file.
         */
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        echo json_encode(array(
            'status' => false,
            'msg' => 'Unable to save file information.'
        ));

        exit;
    }

    echo json_encode(array(
        'status' => true,
        'msg' => 'File uploaded successfully.'
    ));

    exit;

}


?>