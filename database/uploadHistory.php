<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
// header("Content-Type: application/json");

function uploadHistory($history)
{
    // Your database connection configuration here
    $servername = "localhost";
    $username = "stcom";
    $password = "Maruil589";
    $dbname = "stcom";

    // Create connection
    $conn = new mysqli(
        $servername,
        $username,
        $password,
        $dbname
    );

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $offerID            = $history['offerID'];
    $offerName          = $history['offerName'];
    $servers            = $history['servers'];
    $header             = $history['header'];
    $contentType        = $history['contentType'];
    $charset            = $history['charset'];
    $encoding           = $history['encoding'];
    $priority           = $history['priority'];
    $fromName           = $history['fromName'];
    $fromNameEncoding   = $history['fromNameEncoding'];
    $subject            = $history['subject'];
    $subjectEncoding    = $history['subjectEncoding'];
    $fromEmailCheck     = $history['fromEmailCheck'];
    $replyToCheck       = $history['replyToCheck'];
    $returnPathCheck    = $history['returnPathCheck'];
    $link               = $history['link'];
    $creative           = $history['creative'];
    $recipients         = $history['recipients'];
    $blacklist          = $history['blacklist'];
    $mailerID           = $history['mailerID'];
    $countryID          = $history['countryID'];

    $file = '';

    $zip = new ZipArchive();
    //generate a random key for the zip archive name
    $randKey  = uniqid() . rand(0000, 9999);
    //zip file name
    $zip_file_name = "$randKey.zip";
    $zipPath = "/var/www/html/database/tmp_zip_files/$zip_file_name";
    try {
        //create the new zip archive using the $file_name above
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($_FILES["files"]["tmp_name"] as $key => $tmpFile) {
                $originalFilename = $_FILES["files"]["name"][$key];
                $fileData = file_get_contents($_FILES["files"]["tmp_name"][$key]);
                $zip->addFromString($originalFilename, $fileData);
            }
            //close the archive
            $zip->close();

            // Get the binary data of the zip file
            $zipFileData = file_get_contents($zipPath);

            $attachmentsData = base64_encode($zipFileData);

            // echo "<pre>";
            // echo 'data: ' . $zipFileData;
            // echo 'encode: ' . $attachmentsData;
            // echo "</pre>";


            // Prepare the data to be returned as a JSON response
            $response = array(
                "success" => true,
                "message" => "Files uploaded successfully!",
                "file" => $attachmentsData,
                "fileData" => $zipFileData,
            );

            $attachmentsData == "" ? 'NULL' : $attachmentsData;

            $sql = ("INSERT INTO `offer` (`offerID`, `offerName`, `servers`, `header`, `contentType`, `charset`, `encoding`, `priority`, `fromName`, `fromNameEncoding`, `subject`, `subjectEncoding`, `fromEmailCheck`, `replyToCheck`, `returnPathCheck`, `link`, `attachements`, `creative`, `recipients`, `blacklist`, `mailerID`, `countryID`) VALUES ('{$offerID}', '{$offerName}', '{$servers}', '{$header}', '{$contentType}', '{$charset}', '{$encoding}', '{$priority}', '{$fromName}', '{$fromNameEncoding}', '{$subject}', '{$subjectEncoding}', {$fromEmailCheck}, {$replyToCheck}, {$returnPathCheck}, '{$link}', '{$attachmentsData}', '{$creative}', '{$recipients}', '{$blacklist}', '{$mailerID}', '{$countryID}')");
            if ($conn->query($sql) === TRUE) {
                $response = array(
                    "success" => true,
                    "message" => "Files uploaded successfully!",
                    "filesData" => $attachmentsData,
                );
            } else {
                $response = array(
                    "success" => true,
                    "message" => "Files uploaded successfully!",
                    "error" => "Error: " . $sql . "<br>" . $conn->error,
                );

                // echo $conn->error;
            }

            $conn->close();

            // // Set appropriate headers to force the download
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"$zip_file_name\"");
            header("Content-Length: " . filesize($zipPath));

            // Output the zip file content to the browser
            readfile($zipPath);

            // Delete the temporary zip file from the server
            unlink($zipPath);
        }
    } catch (Exception $e) {
        $response = array(
            "success" => false,
            "message" => "Error: {$e}",
        );
    }



    // foreach ($_FILES["attachments"]['tmp_name'] as $tmpFile) {
    //     if ($fileHandle = fopen($tmpFile, 'rb')) {
    //         while (!feof($fileHandle)) {
    //             $file .= fread($fileHandle, 8192); // Read 8KB at a time
    //         }
    //         fclose($fileHandle);
    //     }
    // }

    // $attachmentsData = base64_encode($file);


    // Return the JSON response
    // echo json_encode($response);
}

// Call the function with the form data sent from WebSocket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Call the function to send emails using PHPMailer and get the email responses
    uploadHistory($_POST);
}
