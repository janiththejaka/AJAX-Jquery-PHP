
<?php
session_start();

$_SESSION['page'] = "hel/e/133";
$_SESSION['Module'] = "Help Desk";

?>

<link rel="stylesheet" href="libraries\bootstrap-5.0.2-dist\bootstrap-5.0.2-dist\css\bootstrap.min.css">
<link rel="stylesheet" href="libraries\DataTables\datatables.min.css" >
<style>

    /* Main page */
    .archive-container {
        width: 95%;
        padding: 30px;
        margin: 0 auto;
    }

    /* Common card */
    .archive-card {
        background: #ffffff;
        border: 1px solid #dfe5ec;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }

    /* Card header */
    .archive-card-header {
        background: #0d6efd;
        color: #ffffff;
        padding: 18px 25px;
    }

    .archive-card-header h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .archive-card-header p {
        margin: 4px 0 0;
        font-size: 13px;
        opacity: 0.9;
    }

    /* Card body */
    .archive-card-body {
        padding: 30px;
    }

    /* Form labels */
    .archive-card-body .form-label {
        font-weight: 600;
        color: #344054;
        margin-bottom: 8px;
    }

    /* File name input */
    .file-name-input {
        height: 42px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .file-name-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }

    /* Drop zone */
    .file-drop-zone {
        position: relative;
        border: 2px dashed #9dbce3;
        border-radius: 8px;
        background: #f8fbff;
        padding: 35px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-drop-zone:hover {
        border-color: #0d6efd;
        background: #f1f7ff;
    }

    .file-drop-zone.drag-over {
        border-color: #0d6efd;
        background: #eaf3ff;
    }

    .file-drop-icon {
        font-size: 35px;
        color: #0d6efd;
        margin-bottom: 10px;
    }

    .file-drop-title {
        font-size: 15px;
        font-weight: 600;
        color: #344054;
        margin-bottom: 5px;
    }

    .file-drop-text {
        font-size: 13px;
        color: #667085;
        margin-bottom: 12px;
    }

    .file-input {
        display: none;
    }

    .file-selected-name {
        font-size: 13px;
        color: #0d6efd;
        font-weight: 500;
        margin-top: 10px;
    }

    /* Form buttons */
    .file-upload-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #eaecf0;
    }

    .btn-upload {
        
        min-width: 110px;
        color: #ffffff;
        font-weight: 500;
        background-color: #0d6efd;
        border: 1px solid #0d6efd;
    }

    .btn-upload:hover {
        color: #ffffff;
        background-color: #043e94;
    }

    .btn-clear {
        min-width: 110px;
        font-weight: 500;
        color: #ffffff;
        background-color: #dc3545;
        border: 1px solid #dc3545;
    }

    .btn-clear:hover {
        color: #ffffff;
        background-color: #a20514;
    }

    /* Archive table */
    .archive-table-wrapper {
        width: 100%;
        overflow-x: auto;  
    }

    .archive-table {
        width: 100%;
        margin-bottom: 0;
        vertical-align: middle;
    }

    .archive-table thead th {
        background: #f1f6fc;
        color: #344054;
        font-size: 14px;
        font-weight: 600;
        border-bottom: 2px solid #d6e2f0;
        padding: 13px 15px;
        white-space: nowrap;
    }

    .archive-table tbody td {
        color: #475467;
        font-size: 14px;
        padding: 13px 15px;
        border-bottom: 1px solid #eaecf0;
    }

    .archive-table tbody tr:hover {
        background-color: #f8fbff;
    }

    /* ID column */
    .file-id {
        font-weight: 600;
        color: #0d6efd !important;
    }

    /* File name */
    .file-name {
        font-weight: 500;
        color: #344054 !important;
    }

    /* Action buttons */
    .table-action-btn {
         min-width: 80px;
        font-size: 13px;
        font-weight: 500;
        margin-right: 5px;
        border-radius: 4px;
    }

    .btn-modify {
        color: #0d6efd;
        background-color: #ffffff;
        border: 1px solid #0d6efd;
    }

    .btn-modify:hover {
        color: #ffffff;
        background-color: #0d6efd;
    }

    .btn-delete {
        color: #dc3545;
        background-color: #ffffff;
        border: 1px solid #dc3545;
    }

    .btn-delete:hover {
        color: #ffffff;
        background-color: #dc3545;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: flex-end ;
    }

    /* Responsive */
    @media (max-width: 768px) {

        .archive-container {
            padding: 15px;
        }

        .archive-card-body {
            padding: 20px;
        }

        .file-upload-buttons {
            flex-direction: column;
        }

        .file-upload-buttons button {
            width: 100%;
        }

    }



/*MODIFY MODAL */

.simple-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
}


/* Modal box */
.modify-modal-content {
    width: 500px;
    max-width: 90%;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.update-file-input {
    display: none;
}

/* Header */
.modify-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #0d6efd;
    color: #ffffff;
    padding: 16px 20px;
}

.modify-modal-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

/* Close button */

.modify-close-button {
    border: none;
    background: transparent;
    color: #ffffff;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    padding: 0;
}

.modify-close-button:hover {
    opacity: 0.8;
}


/* Body */
.modify-modal-body {
    padding: 25px;
}

/* Footer */
.modify-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px 25px;
    border-top: 1px solid #eaecf0;
}


/* Modal buttons */

.modify-modal-footer .btn-upload,
.modify-modal-footer .btn-clear {
    min-width: 100px;

    padding: 7px 15px;

    border-radius: 4px;

    font-weight: 500;

    cursor: pointer;
}


/* Modify */

    .modify-modal-footer .btn-upload {
    color: #ffffff;
    background-color: #0d6efd;
    border: 1px solid #0d6efd;
    }

    .modify-modal-footer .btn-upload:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
    }


    /* Cancel */

    .modify-modal-footer .btn-clear {
    color: #dc3545;
    background-color: #ffffff;
    border: 1px solid #dc3545;
    }

    .modify-modal-footer .btn-clear:hover {
    color: #ffffff; 
    background-color: #dc3545;
    border-color: #dc3545;
    }


</style>


<div class="archive-container">

    <!-- FILE UPLOAD SECTION -->

    <div class="archive-card">

        <div class="archive-card-header">

            <h4>File Upload</h4>

            <p>
                Upload a file and provide the required file information.
            </p>

        </div>


        <div class="archive-card-body">

            <form id="fileUploadForm">

                <!-- File Name -->
                <div class="mb-4">

                    <label for="fileName" class="form-label">
                        Remark File Name
                    </label>

                    <input
                        type="text"
                        class="form-control file-name-input"
                        id="fileName"
                        name="fileName"
                        placeholder="Enter file name"
                        required
                    >

                </div>


                <!-- File -->
                <div class="mb-3">

                    <label class="form-label">
                        Select File
                    </label>

                    <div class="file-drop-zone" id="fileDropZone">

                        <div class="file-drop-icon">
                            <i class="glyphicon glyphicon-cloud-upload"></i>
                        </div>

                        <div class="file-drop-title">
                            Drag & Drop your file here
                        </div>

                        <div class="file-drop-text">
                            or click below to browse from your computer
                        </div>

                        <label
                            for="fileInput"
                            class="btn btn-outline-primary btn-sm"
                        >
                            Choose File
                        </label>

                        <input type="file" id="fileInput" name="file" class="file-input">

                        <div  id="selectedFileName" class="file-selected-name"></div>

                    </div>

                </div>


                <!-- Upload Buttons -->
                <div class="file-upload-buttons">

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-clear"
                        id="deleteButton"
                    >
                        Clear
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary btn-upload"
                        id="uploadButton"
                    >
                        Upload
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- ARCHIVE HISTORY SECTION -->

    <div class="archive-card">

        <div class="archive-card-header">

            <h4>User Archive History</h4>

            <p>
                View and manage previously uploaded files.
            </p>

        </div>


        <div class="archive-card-body">
            <div class="archive-table-wrapper">
                <table id="archiveTable" class="table archive-table">

                    <thead>
                        <tr>
                            <th style="width: 35%;"> Original File Name </th>

                            <th style="width: 20%;"> Remark File Name </th>

                            <th style="width: 20%;"> Modified Date </th>

                            <th style="width: 25%;"> Actions </th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                </table>
            </div>
        </div>
    </div>

</div>


<!-- Modify File Modal -->
<div id="modifyModal" class="simple-modal">

    <div class="modify-modal-content">

        <!-- Header -->
        <div class="modify-modal-header">
            <h4>Modify File</h4>
        </div>

        <!-- Body -->
        <div class="modify-modal-body">

            <form id="modifyFileForm">

                <!-- Original File Name -->
                <div class="mb-4">

                    <label
                        for="modifyOriginalFileName"
                        class="form-label">

                        Original File Name

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="modifyOriginalFileName"
                        name="modifyOriginalFileName"
                        readonly>

                </div>


                <!-- Remark File Name -->
                <div class="mb-4">

                    <label
                        for="modifyRemarkName"
                        class="form-label">

                        Update Remark File Name
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        class="form-control file-name-input"
                        id="modifyRemarkName"
                        name="modifyRemarkName"
                        placeholder="Enter remark file name"
                        required>

                </div>

                <!-- Replacement File -->
                <div class="mb-3">

                    <label
                        for="updatefileInput"
                        class="btn btn-outline-primary btn-sm">

                        Update File

                    </label>

                    <input
                        type="file"
                        id="updatefileInput"
                        name="updatefile"
                        class="update-file-input">

                    <div
                        id="modifySelectedFileName"
                        class="file-selected-name">
                    </div>

                </div>

            </form>

        </div>


        <!-- Footer -->
        <div class="modify-modal-footer">

            <button
                type="button"
                class="btn-clear"
                id="modifyCancelButton">

                Cancel

            </button>


            <button
                type="button"
                class="btn-upload"
                id="modifyButton">

                Modify

            </button>

        </div>

    </div>

</div>



<script src="libraries\jquery-4.0.0.min.js"></script>
<script src="libraries\DataTables\datatables.min.js"></script>

<script>

    var archiveTable;
    var modifyUuid = null;
    
    $(document).ready(function (){

         archiveTable = $('#archiveTable').DataTable({
        columns: [

        { data: 'original_file_name'},
        { data: 'remark_name', render: function (data, type, row) {
                return '<span class="file-name">' + data + '</span>';
            } },
        { data: 'updated_at' },
        {data: null,
            render: function (data, type, row) {
                return `
                    <button
                        class="btn btn-sm btn-modify table-action-btn"
                        data-id="${row.uuid}">
                        Modify
                    </button>

                    <button
                        class="btn btn-sm btn-delete table-action-btn"
                        data-id="${row.uuid}">
                        Delete
                    </button>
                `;
            }
        }
    ]
});
        console.log('DataTable initialized and ready.');
        loadArchiveFiles();
        console.log('Archive files loaded and displayed in DataTable.');

        /* Open file browser when the drop zone is clicked.*/
        $('#fileDropZone').on('click', function (event) {

            if (!$(event.target).is('label') &&
                !$(event.target).is('input')) {

                $('#fileInput').click();

            }

        });


        /*Display selected file name.*/
        $('#fileInput').on('change', function () {

            if (this.files.length > 0) {
                $('#selectedFileName').text('Selected file: ' + this.files[0].name);
            } else {
                $('#selectedFileName').text('');
            }

        });


        /* Drag over.*/
        $('#fileDropZone').on('dragover', function (event) {

            event.preventDefault();
            $(this).addClass('drag-over');

        });


        /*Drag leave.*/
        $('#fileDropZone').on('dragleave', function () {

            $(this).removeClass('drag-over');

        });


        /* File dropped.*/
        $('#fileDropZone').on('drop', function (event) {

            event.preventDefault();
            $(this).removeClass('drag-over');
            var files = event.originalEvent.dataTransfer.files;

            if (files.length > 0) {

                $('#fileInput')[0].files = files;
                $('#selectedFileName').text(
                    'Selected file: ' + files[0].name
                );

            }

        });

        /* Upload button click.*/
        $('#uploadButton').on('click', function () {
        uploadFile();
        });


        /*Clear upload form.*/
        $('#deleteButton').on('click', function () {
            $('#fileName').val('');
            $('#fileInput').val('');
            $('#selectedFileName').text('');
            $('#fileDropZone').removeClass('drag-over');

        });


    /* Open modify modal.*/
        $('.archive-table tbody').on('click','.btn-modify', function () {

        modifyUuid = $(this).data('id');
        console.log('Modify UUID:', modifyUuid);

        $.ajax({
            url: 'Sql_file_archive.php',
            type: 'POST',
            data: {
                action: 'get_file',
                uuid: modifyUuid
            },
            success: function (response) {
                console.log('GET FILE RESPONSE:', response);
                var result = JSON.parse(response);
                if (result.status) {

                var file = result.data;
                modifyUuid = file.uuid;
                originalRemarkName = file.remark_name;
                $('#modifyOriginalFileName').val(file.original_file_name);
                $('#modifyRemarkName').val(file.remark_name);
                $('#updatefileInput').val('');
                $('#modifySelectedFileName').text('No replacement file selected.');
                $('#modifyModal').css('display', 'flex');}
            },

            error: function (xhr, status, error) {
                console.log('GET FILE ERROR');
                console.log('Status:',status);
                console.log('Error:', error);
                console.log('Response:',xhr.responseText);
                alert('Unable to load file information.');
            }
        });
    }
);

/*modify update file name in modal */
         $('#updatefileInput').on('change',function () {
             if (this.files.length > 0) {
        $('#modifySelectedFileName').text(this.files[0].name);
        } else {
            $('#modifySelectedFileName').text('No replacement file selected.');
        }
        });

        $('archive-table tbody').on('click', '.btn-delete', function () {
        var row = $(this).closest('tr');
        var fileName = row.find('.file-name').text().trim();
        console.log('Delete button clicked for file: ' + fileName);
        });


    /* Close modify modal.*/
        $('#modifyCancelButton').on('click', function () {
        $('#modifyModal').css('display', 'none');
        });

    /*Modify button.*/
    $('#modifyButton').on('click', function () {

        var newRemarkName = $('#modifyRemarkName').val().trim();
        var fileInput = $('#updatefileInput')[0];

        /* Validate remark.*/
        if (newRemarkName === '') {
            alert('Remark name cannot be empty.');
            return;
        }

        /* Determine whetheranything changed. */

        var remarkChanged =newRemarkName !== originalRemarkName;
        var fileChanged = fileInput.files.length > 0;
        if (!remarkChanged && !fileChanged) {
            alert('No changes were made.');
            return;
        }

        /*Confirmation message. */
        var message ='Are you sure you want to modify this file?\n\n';

        if (remarkChanged) {
            message +='• Remark name will be updated.\n';}
        if (fileChanged) {
            message +='• The existing file will be replaced.\n';
            message +='• The previous physical file will be deleted.\n';
        }
        message +='\nThis action will modify the stored record.';
        message +='\n\nDo you want to continue?';

        /* Browser confirmation.*/

        var confirmed = confirm(message);
        if (!confirmed) {
            console.log('Modify operation cancelled.');
            return;
        }

        /*User confirmed.*/
        modifyFile();

    });

    /* Delete Button Function*/
    $('.archive-table tbody').on('click','.btn-delete',function () {

        var uuid =$(this).data('id');
        var confirmed =
            confirm(
                'WARNING!\n\n' +
                'Are you sure you want to delete this file?\n\n' +
                'The stored physical file will be deleted ' +
                'and the record will be removed from the active archive.\n\n' +
                'This action cannot be undone.\n\n' +
                'Do you want to continue?'
            );

        if (!confirmed) {console.log('Delete operation cancelled.');
            return;
        }
        deleteFile(uuid);
    }
    );


    /*Display selected file.*/
    $('#updatefileInput').on('change', function () {

    console.log('File input changed');

    if (this.files.length > 0) {
        $('#modifySelectedFileName').text(
            'Selected file: ' + this.files[0].name);
    } else {
        $('#modifySelectedFileName').text('');

    }

    });


});



function modifyFile(){
    var remarkName = $('#modifyRemarkName').val().trim();
    var fileInput = $('#updatefileInput')[0];
    var formData = new FormData();
    
    console.log('Preparing to modify file with UUID:', modifyUuid);
    formData.append('action','modify_file');
    formData.append('remarkName',remarkName);
    formData.append('uuid', modifyUuid);

    /* Only append file if user selected one. */

    if (fileInput.files.length > 0) {
        formData.append('file',fileInput.files[0]);
    }
    
    $.ajax({

        url: 'Sql_file_archive.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {
            console.log('MODIFY RESPONSE:',response);

            var result = JSON.parse(response);

            if (result.status) {
                alert(result.msg);
                $('#modifyModal').hide();
                $('#modifyFileForm')[0].reset();
                $('#modifySelectedFileName').text('No replacement file selected.');

                /* Refresh DataTable.*/
                loadArchiveFiles();

            } else {alert(result.msg);}
        },

        error: function (xhr,status, error) {

            console.log('MODIFY AJAX ERROR');
            console.log('Status:',status);
            console.log('Error:',error);
            console.log('Response:',xhr.responseText);

            alert('Unable to modify the file.');
        }
    });
}

/* Delete row function */
function deleteFile(uuid){
    $.ajax({
        url: 'Sql_file_archive.php',
        type: 'POST',
        data: {action: 'delete_file',uuid: uuid},
        success: function (response) {
            console.log('DELETE RESPONSE:',response);

            var result = JSON.parse(response);

            if (result.status) {
                alert(result.msg);
                loadArchiveFiles();
            } else {
                alert(result.msg);
            }
        },
        error: function (xhr,status,error) {

            console.log('DELETE AJAX ERROR');
            console.log('Status:',status);
            console.log('Error:',error);
            console.log('Response:',xhr.responseText);
            alert('Unable to delete the file.');
        }
    });
}

/* jquery functions */

function loadArchiveFiles() {
    $.ajax({
        url: 'Sql_file_archive.php',
        type: 'POST',
        data: {action: 'get_files'},

        success: function (response) {

            var result = JSON.parse(response);

            if (result.status) {
                archiveTable.clear();
                archiveTable.rows.add(result.data);
                archiveTable.draw();
            }
        },

        error: function () {
            console.log('Failed to load archive files.');
        }
    });
}

function uploadFile() {

    var fileName = $('#fileName').val().trim();
    var fileInput = $('#fileInput')[0];

    if (fileName === '') {
        $('#fileName')[0].reportValidity();
        return;
    }
    if (fileInput.files.length === 0) {
        alert('Please select a file.');
        return;
    }

    var formData = new FormData();

    formData.append('action', 'upload_file');
    formData.append('fileName', fileName);
    formData.append('file', fileInput.files[0]);

    $.ajax({
        url: 'Sql_file_archive.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {
            var result = JSON.parse(response);

            if (result.status) {
                $('#fileName').val('');
                $('#fileInput').val('');
                $('#selectedFileName').text('');
        
                loadArchiveFiles();
                alert(result.msg);
            } else {alert(result.msg);}
        },

        error: function () {

            alert('An error occurred while uploading the file.');
        }
    });
}


</script>

