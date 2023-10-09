<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

error_reporting(E_ERROR | E_PARSE);

function uploadHistory($history)
{
    // Initialize $response variable
    $response = array();

    $dbPath = "./db.json";
    $db = json_decode(file_get_contents($dbPath), true);

    // Your database connection configuration here
    $servername = $db["servername"];
    $username = $db["username"];
    $password = $db["password"];
    $dbname = $db["dbname"];

    // Create connection
    $conn = new mysqli(
        $servername,
        $username,
        $password,
        $dbname
    );

    if ($conn->connect_error) {
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $conn->connect_error,
        );
        echo json_encode($response);
        exit();
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
    $fromEmail          = $history['fromEmail'];
    $replyToCheck       = $history['replyToCheck'];
    $replyTo            = $history['replyTo'];
    $returnPathCheck    = $history['returnPathCheck'];
    $returnPath         = $history['returnPath'];
    $link               = $history['link'];
    $creative           = base64_encode($history['creative']);
    $recipients         = $history['recipients'];
    $blacklist          = $history['blacklist'];
    $mailerID           = $history['mailerID'];
    $countryID          = $history['countryID'];

    // Set the desired time zone
    date_default_timezone_set('Africa/Casablanca');

    // Get the current date and time
    $currentDateTime = date("Y-m-d H:i:s");

    $zip = new ZipArchive();
    //generate a random key for the zip archive name
    $randKey  = uniqid() . rand(0000, 9999);
    //zip file name
    $zip_file_name = "$randKey.zip";
    $zipPath = "./tmp_zip_files/$zip_file_name";

    try {
        //create the new zip archive using the $file_name above
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($_FILES["attachements"]["tmp_name"] as $key => $tmpFile) {
                $originalFilename = $_FILES["attachements"]["name"][$key];
                $zip->addFile($tmpFile, $originalFilename);
            }
            //close the archive
            $zip->close();

            // Get the binary data of the zip file
            $zipFileData = file_get_contents($zipPath);
            $attachmentsData = base64_encode($zipFileData);

            // $attachmentsData == "" ? 'NULL' : $attachmentsData;

            $sql = ("INSERT INTO `offer` (`offerID`, `offerName`, `servers`, `header`, `contentType`, `charset`, `encoding`, `priority`, `fromName`, `fromNameEncoding`, `subject`, `subjectEncoding`, `fromEmailCheck`, `fromEmail`, `replyToCheck`, `replyTo`, `returnPathCheck`, `returnPath`, `link`, `attachements`, `creative`, /*`recipients`, `blacklist`, */`date`, `mailerID`, `countryID`) VALUES ('{$offerID}', '{$offerName}', '{$servers}', '{$header}', '{$contentType}', '{$charset}', '{$encoding}', '{$priority}', '{$fromName}', '{$fromNameEncoding}', '{$subject}', '{$subjectEncoding}', {$fromEmailCheck}, '{$fromEmail}', {$replyToCheck}, '{$replyTo}', {$returnPathCheck}, '{$returnPath}', '{$link}', '{$attachmentsData}', '{$creative}',/* '{$recipients}', '{$blacklist}',*/ '{$currentDateTime}', '{$mailerID}', '{$countryID}')");

            if ($conn->query($sql) === TRUE) {
                $response = array(
                    "status" => 'success',
                    "message" => "History saved successfully!",
                );
            } else {
                $response = array(
                    "status" => 'error',
                    "message" => "Error: " . $sql . "<br>" . $conn->error,
                );
            }

            $conn->close();

            // Delete the temporary zip file from the server
            unlink($zipPath);
        }
    } catch (Exception $e) {
        $response = array(
            "status" => 'error',
            "message" => "Error: {$e}",
        );
    }

    // Return the JSON response
    echo json_encode($response);
}

// Call the function with the form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Call the function to send emails using PHPMailer and get the email responses
    uploadHistory($_POST);
}
