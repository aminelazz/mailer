<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
// header("Content-Type: application/json");

// Function to get offer history data from the database
function getOfferHistory()
{
    // Your database connection configuration here
    $servername = "localhost";
    $username = "stcom";
    $password = "Maruil589";
    $dbname = "stcom";

    // // Get db credentials from db.json
    // $dbInfos = file_get_contents("./db.json");
    // $dbInfos = json_decode($dbInfos, true);
    // extract($dbInfos);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Perform the SQL query to fetch the offer history data
    $sql = "SELECT id, username, password, firstName, lastName
            FROM mailer";

    $result = $conn->query($sql);
    $mailer = array();

    while ($row = $result->fetch_assoc()) {
        $mailer[] = $row;
    }

    $conn->close();
    return $mailer;
}

// Fetch offer history data and send it as JSON response
$offerHistoryData = getOfferHistory();
header('Content-Type: application/json');
echo json_encode($offerHistoryData);
