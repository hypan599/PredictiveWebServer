<?php
session_start();

$message = '';
if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload')
{
    if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK)
    {
        // Obtain details of uploaded file
        $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
        $fileName = $_FILES['uploadedFile']['name'];
        $fileSize = $_FILES['uploadedFile']['size'];
        $fileType = $_FILES['uploadedFile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Account for spaces & special characters in filename
        	
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        
        // Run check file Python script to read file and confirm fastq format
        $output = passthru('python filecheck.py $fileTmpPath/$newFileName');
        if ($output == 'False')
        {
            exit('Error: Upload failed! Incorrect file type/format.');
        }

        // ADD boolean to PASS or FAIL file check

        // Check file extenstions
        $allowedfileExtensions = array('fq', 'fastq');

        if (in_array($fileExtension, $allowedfileExtensions))
        {
            // Directory to move uploaded file to
            $uploadFileDir = './uploaded_files/';
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path))
            {
                $message ='File is successfully uploaded!';
            }
            else
            {
                $message = 'File was NOT uploaded successfully';
            }
        }
        else
        {
            $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }
    else
    {
        $message = 'There is some error in the file upload. Please check the following error.<br>';
        $message = 'Error: ' . $_FILES['uploadedFile']['error'];
    }
}
$_SESSION['message'] = $message;
header("Location: index.php");