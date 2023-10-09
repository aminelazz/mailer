<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

// Function to get offer history data from the database
function data_json($data)
{
    $dbPath = "../db.json";
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
        die("Connection failed: " . $conn->connect_error);
    }

    $id = $data['id'];

    // Perform the SQL query to fetch the offer history data
    $sql = "SELECT o.id, o.offerID, o.offerName, o.servers, o.header, o.contentType, o.charset, o.encoding, o.priority, o.fromName, o.fromNameEncoding, o.subject, o.subjectEncoding, o.fromEmailCheck, o.fromEmail, o.replyToCheck, o.replyTo, o.returnPathCheck, o.returnPath, o.link, o.creative,/* o.recipients, o.blacklist, */o.date, o.countryID, o.mailerID,
            m.firstName AS mailerName,
            c.name AS countryName
            FROM offer o
            JOIN country c ON o.countryID = c.id
            JOIN mailer m ON o.mailerID = m.id
            WHERE o.id = $id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mailerName         = $row['mailerName'];
            $servers            = $row['servers'];
            $header             = $row['header'];
            $contentType        = $row['contentType'];
            $charset            = $row['charset'];
            $encoding           = $row['encoding'];
            $priority           = $row['priority'];
            $offerID            = $row['offerID'];
            $offerName          = $row['offerName'];
            $countryID          = $row['countryID'];
            $countryName        = $row['countryName'];
            $fromNameEncoding   = $row['fromNameEncoding'];
            $fromName           = $row['fromName'];
            $subjectEncoding    = $row['subjectEncoding'];
            $subject            = $row['subject'];
            $fromEmailCheck     = $row['fromEmailCheck'];
            $fromEmail          = $row['fromEmail']     == null ? '' : $row['fromEmail'];
            $replyToCheck       = $row['replyToCheck'];
            $replyTo            = $row['replyTo']       == null ? '' : $row['replyTo'];
            $returnPathCheck    = $row['returnPathCheck'];
            $returnPath         = $row['returnPath']    == null ? '' : $row['returnPath'];
            $link               = $row['link'];
            $creative           = $row['creative'];
            // $recipients         = $row['recipients'];
            // $blacklist          = $row['blacklist'];
            $date               = $row['date'];

            // $creative = stripslashes($creative);

            // str_replace("\\r\\n", "&#10;", $creative);
            // str_replace("\\\"", '"', $creative);

            echo json_encode([
                'mailerName'        => $mailerName,
                'servers'           => $servers,
                'header'            => $header,
                'contentType'       => $contentType,
                'charset'           => $charset,
                'encoding'          => $encoding,
                'priority'          => $priority,
                'offerID'           => $offerID,
                'offerName'         => $offerName,
                'countryID'         => $countryID,
                'countryName'       => $countryName,
                'fromName'          => $fromName,
                'fromNameEncoding'  => $fromNameEncoding,
                'subject'           => $subject,
                'subjectEncoding'   => $subjectEncoding,
                'fromEmailCheck'    => $fromEmailCheck,
                'fromEmail'         => $fromEmail,
                'replyToCheck'      => $replyToCheck,
                'replyTo'           => $replyTo,
                'returnPathCheck'   => $returnPathCheck,
                'returnPath'        => $returnPath,
                'link'              => $link,
                'creative'          => $creative,
                // 'recipients'        => $recipients,
                // 'blacklist'         => $blacklist,
                'date'              => $date,
            ]);
        }
    } else {
        // Invalid or missing parameter, return an error response or handle it as needed
        http_response_code(404); // Not found
        echo json_encode(array('error' => 'No offer has been found.'));
        exit;
    }

    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    data_json($_GET);
} else {
    // Invalid or missing parameter, return an error response or handle it as needed
    http_response_code(400); // Bad Request
    echo json_encode(array('error' => 'Invalid or missing parameter.'));
    exit;
}
