<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

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
    $zipPath = "/var/www/html/database/tmp_zip_files/$zip_file_name";
    try {
        if (!empty($_FILES['attachements']['tmp_name'])) {
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

                $attachmentsData == "" ? 'NULL' : $attachmentsData;

                $sql = ("INSERT INTO `offer` (`offerID`, `offerName`, `servers`, `header`, `contentType`, `charset`, `encoding`, `priority`, `fromName`, `fromNameEncoding`, `subject`, `subjectEncoding`, `fromEmailCheck`, `fromEmail`, `replyToCheck`, `replyTo`, `returnPathCheck`, `returnPath`, `link`, `attachements`, `creative`, `recipients`, `blacklist`, `date`, `mailerID`, `countryID`) VALUES ('{$offerID}', '{$offerName}', '{$servers}', '{$header}', '{$contentType}', '{$charset}', '{$encoding}', '{$priority}', '{$fromName}', '{$fromNameEncoding}', '{$subject}', '{$subjectEncoding}', {$fromEmailCheck}, '{$fromEmail}', {$replyToCheck}, '{$replyTo}', {$returnPathCheck}, '{$returnPath}', '{$link}', '{$attachmentsData}', '{$creative}', '{$recipients}', '{$blacklist}', '{$currentDateTime}', '{$mailerID}', '{$countryID}')");

                if ($conn->query($sql) === TRUE) {
                    $response = array(
                        "success" => true,
                        "message" => "History saved successfully!",
                        "creative" => $creative,
                    );
                } else {
                    $response = array(
                        "success" => false,
                        "message" => "Error: " . $sql . "<br>" . $conn->error,
                        "creative" => $creative,
                    );
                }

                $conn->close();

                // Delete the temporary zip file from the server
                unlink($zipPath);
            }
        } else {
            $sql = ("INSERT INTO `offer` (`offerID`, `offerName`, `servers`, `header`, `contentType`, `charset`, `encoding`, `priority`, `fromName`, `fromNameEncoding`, `subject`, `subjectEncoding`, `fromEmailCheck`, `fromEmail`, `replyToCheck`, `replyTo`, `returnPathCheck`, `returnPath`, `link`, `attachements`, `creative`, `recipients`, `blacklist`, `date`, `mailerID`, `countryID`) VALUES ('{$offerID}', '{$offerName}', '{$servers}', '{$header}', '{$contentType}', '{$charset}', '{$encoding}', '{$priority}', '{$fromName}', '{$fromNameEncoding}', '{$subject}', '{$subjectEncoding}', {$fromEmailCheck}, '{$fromEmail}', {$replyToCheck}, '{$replyTo}', {$returnPathCheck}, '{$returnPath}', '{$link}', '', '{$creative}', '{$recipients}', '{$blacklist}', '{$currentDateTime}', '{$mailerID}', '{$countryID}')");
            if ($conn->query($sql) === TRUE) {
                $response = array(
                    "success" => true,
                    "message" => "History saved successfully!",
                    "creative" => $creative,
                );
            } else {
                $response = array(
                    "success" => false,
                    "message" => "Error: " . $sql . "<br>" . $conn->error,
                    "creative" => $creative,
                );
            }
        }

        // // Step 1: Retrieve the existing mailerIDs for the specific country
        // $selectQuery = "SELECT mailerIDs FROM country WHERE id = ?";
        // $selectStmt = $conn->prepare($selectQuery);
        // $selectStmt->bind_param("s", $countryID);
        // $selectStmt->execute();

        // $result = $selectStmt->get_result();
        // $row = $result->fetch_assoc();
        // $existingMailerIDs = $row['mailerIDs'];

        // // Step 2: Check if the mailer's ID is already present in the mailerIDs
        // $mailerIDsArray = explode(",", $existingMailerIDs);
        // if (!in_array($mailerID, $mailerIDsArray)) {
        //     // Step 3: Add the mailer's ID to the existing IDs and update the mailerIDs field
        //     $mailerIDsArray[] = $mailerID;
        //     $newMailerIDs = implode(",", $mailerIDsArray);

        //     $updateQuery = "UPDATE country SET mailerIDs = ? WHERE id = ?";
        //     $updateStmt = $conn->prepare($updateQuery);
        //     $updateStmt->bind_param("ss", $newMailerIDs, $countryID);
        //     if ($updateStmt->execute()) {
        //         $response = array(
        //             "success" => true,
        //             "message" => "ID added successfully",
        //         );
        //     } else {
        //         $response = array(
        //             "success" => true,
        //             "message" => "Error: " . $sql . "<br>" . $conn->error,
        //         );
        //     }
        // }

        $conn->close();
    } catch (Exception $e) {
        $response = array(
            "success" => false,
            "message" => "Error: {$e}",
        );
    }

    // Return the JSON response
    echo json_encode($response);
}

// Call the function with the form data sent from WebSocket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Call the function to send emails using PHPMailer and get the email responses
    uploadHistory($_POST);
}
