<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

// Function to get offer history data from the database
function getOfferHistory()
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

    // Perform the SQL query to fetch the offer history data
    // $sql = "SELECT o.id, o.offerID, o.offerName, o.servers, o.header, o.contentType, o.charset, o.encoding, o.priority, o.fromName, o.fromNameEncoding, o.subject, o.subjectEncoding, o.fromEmailCheck, o.replyToCheck, o.returnPathCheck, o.link, o.attachements, o.creative, o.recipients, o.blacklist, o.date, o.mailerID, o.countryID,
    //                m.firstName AS mailerName,
    //                c.name AS countryName
    //         FROM offer o
    //         JOIN country c ON o.countryID = c.id
    //         JOIN mailer m ON o.mailerID = m.id";

    $sql = "SELECT o.id, o.offerID, o.offerName, o.date, o.mailerID, o.countryID,
                   m.firstName AS mailerName,
                   c.name AS countryName
            FROM offer o
            JOIN country c ON o.countryID = c.id
            JOIN mailer m ON o.mailerID = m.id";

    $result = $conn->query($sql);
    $mailerData = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mailerId = $row['mailerID'];
            $countryId = $row['countryID'];

            // Check if mailer and country already exist in the $mailerData array
            if (!isset($mailerData[$mailerId])) {
                $mailerData[$mailerId] = array(
                    'id'            => $mailerId,
                    'firstName'     => $row['mailerName'],
                    'countries'     => array()
                );
            }
            if (!isset($mailerData[$mailerId]['countries'][$countryId])) {
                $mailerData[$mailerId]['countries'][$countryId] = array(
                    'id'            => $countryId,
                    'name'          => $row['countryName'],
                    'offers'        => array()
                );
            }

            // Add the offer to the country's offers array
            $mailerData[$mailerId]['countries'][$countryId]['offers'][] = array(
                'id'                => $row['id'],
                'offerID'           => $row['offerID'],
                'offerName'         => $row['offerName'],
                // 'servers'           => $row['servers'],
                // 'header'            => $row['header'],
                // 'contentType'       => $row['contentType'],
                // 'charset'           => $row['charset'],
                // 'encoding'          => $row['encoding'],
                // 'priority'          => $row['priority'],
                // 'fromName'          => $row['fromName'],
                // 'fromNameEncoding'  => $row['fromNameEncoding'],
                // 'subject'           => $row['subject'],
                // 'subjectEncoding'   => $row['subjectEncoding'],
                // 'fromEmailCheck'    => $row['fromEmailCheck'],
                // 'replyToCheck'      => $row['replyToCheck'],
                // 'returnPathCheck'   => $row['returnPathCheck'],
                // 'link'              => $row['link'],
                // 'attachements'      => $row['attachements'] != null ? true : false,
                // 'creative'          => $row['creative'],
                // 'recipients'        => $row['recipients'],
                // 'blacklist'         => $row['blacklist'],
                'date'              => $row['date'],
            );
            // $mailerData[$mailerId]['countries'][$countryId] = array_values($mailerData[$mailerId]['countries'][$countryId]);

            // $mailerData[$mailerId]['countries'][$countryId]['offers'][$offerId] = array_values($mailerData[$mailerId]['countries'][$countryId]['offers'][$offerId]);
        }
    }

    // Convert the countries object to an array
    foreach ($mailerData as &$item) {
        if (isset($item['countries'])) {
            $item['countries'] = array_values($item['countries']);
        }
    }


    $conn->close();
    return array_values($mailerData); // Re-index the array keys
}

// Fetch offer history data and send it as JSON response
$offerHistoryData = getOfferHistory();
header('Content-Type: application/json');
echo json_encode($offerHistoryData);
