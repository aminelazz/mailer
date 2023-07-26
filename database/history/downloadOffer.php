<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

// Function to get offer history data from the database
function getOfferHistory()
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

    $offerId = $_GET['offerID'];

    // Perform the SQL query to fetch the offer history data
    $sql = "SELECT id, offerID, offerName, servers, header, contentType, charset, encoding, priority, fromName, fromNameEncoding, subject, subjectEncoding, fromEmailCheck, replyToCheck, returnPathCheck, link, creative, recipients, blacklist, date,
            FROM offer
            WHERE `id` = $offerId";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $servers            = $row['servers'];
            $header             = $row['header'];
            $contentType        = $row['contentType'];
            $charset            = $row['charset'];
            $encoding           = $row['encoding'];
            $priority           = $row['priority'];
            $fromName           = $row['fromName'];
            $fromNameEncoding   = $row['fromNameEncoding'];
            $subject            = $row['subject'];
            $subjectEncoding    = $row['subjectEncoding'];
            $fromEmailCheck     = $row['fromEmailCheck'];
            $replyToCheck       = $row['replyToCheck'];
            $returnPathCheck    = $row['returnPathCheck'];
            $link               = $row['link'];
            $creative           = $row['creative'];
            $recipients         = $row['recipients'];
            $blacklist          = $row['blacklist'];
            $date               = $row['date'];
        }
    }

    // Set appropriate headers to force the browser to download the file
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=\"attachements.zip\"");
    // header("Content-Length: " . strlen($zipFileData));

    // Output the zip file contents
    readfile('path/to/your/generated/zip/file.zip');

    $conn->close();
}
