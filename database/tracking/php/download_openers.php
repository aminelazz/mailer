<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
// header("Content-Type: application/json");

function downloadOpeners($openersArray)
{
    // Initialize $response variable
    $response = array();

    $dbPath = "../../db.json";
    $db = json_decode(file_get_contents($dbPath), true);

    // Your database connection configuration here
    $servername = $db["servername"];
    $username = $db["username"];
    $password = $db["password"];
    $dbname = $db["dbname"];

    try {
        // Create connection
        $conn = new mysqli(
            $servername,
            $username,
            $password,
            $dbname
        );
    } catch (Exception $e) {
        $response = array(
            "status" => 'Error',
            "message" => 'Error connecting to database: ' . $e->getMessage(),
        );
        http_response_code(500);
        echo json_encode($response);
        exit();
    }

    if ($conn->connect_error) {
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $conn->connect_error,
        );
        http_response_code(500);
        echo json_encode($response);
        exit();
    }

    foreach ($openersArray as $opener) {
        $offer = $opener["offer"];
        $countryID = $opener["countryID"];
        $date = $opener["date"];

        // Perform the SQL query to fetch number of openers per offer
        $sql = "SELECT *
                FROM openers
                WHERE offer = '$offer' AND countryID = $countryID AND date = '$date'";

        $result = $conn->query($sql);
        $data = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $email = $row["email"];
                array_push($data, $email);
            }
        }
    }

    $conn->close();

    $data = array_unique($data, SORT_STRING);

    $response = array(
        "status" => 'Success',
        "data" => $data,
    );
    http_response_code(200);
    echo json_encode($response);
}

function test($openersArray)
{
    $i = 1;
    foreach ($openersArray as $opener) {
        echo "Offer $i \n";
        echo "Offer: " . $opener["offer"] . "\n";
        echo "Country: " . $opener["country"] . "\n";
        echo "CountryID: " . $opener["countryID"] . "\n";
        echo "Date: " . $opener["date"] . "\n\n";
        $i++;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data sent from the JavaScript
    $openersArray = json_decode($_POST['openers'], true);

    // test($openersArray);
    downloadOpeners($openersArray);
}
