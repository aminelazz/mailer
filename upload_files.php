<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["files"])) {
    // Create a new ZipArchive
    $zip = new ZipArchive();
    $zipData = 'archive.zip';

    $zipPath = "/var/www/html/database/tmp_zip_files/$zipData";

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        foreach ($_FILES["files"]["tmp_name"] as $key => $tmpFile) {
            $originalFilename = $_FILES["files"]["name"][$key];
            $zip->addFile($tmpFile, $originalFilename);
        }

        $zip->close();

        // Get the binary data of the zip file
        $zipFileData = file_get_contents($zipPath);

        // Encode the binary data to base64
        $base64EncodedZip = base64_encode($zipFileData);

        // echo "<pre>";
        // echo $base64EncodedZip;
        // echo "</pre>";

        // // Set appropriate headers to force the download
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"$zipData\"");
        header("Content-Length: " . filesize($zipPath));

        // Output the zip file content to the browser
        readfile($zipPath);

        // Delete the temporary zip file from the server
        unlink($zipPath);

        // // Your database connection configuration here
        // $servername = "localhost";
        // $username = "stcom";
        // $password = "Maruil589";
        // $dbname = "stcom";

        // // Create connection
        // $conn = new mysqli(
        //     $servername,
        //     $username,
        //     $password,
        //     $dbname
        // );

        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // }
        // // Insert the base64-encoded zip file data into the database
        // $stmt = $conn->prepare("INSERT INTO files_table (zip_file_data) VALUES (?)");
        // $stmt->bind_param("s", $base64EncodedZip);
        // $stmt->execute();
        // $stmt->close();
        // $conn->close();

        // echo "Files compressed and uploaded successfully!";
    } else {
        echo "Failed to create the zip file.";
    }
} else {
    echo "Please select files to upload.";
}
