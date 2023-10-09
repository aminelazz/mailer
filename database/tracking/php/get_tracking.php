<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

function getTracking()
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

    // Perform the SQL query to fetch number of openers per offer
    $sql = "SELECT o.offer, COUNT(o.email) AS openers, o.countryID, c.name as country, o.date
            FROM openers o
            JOIN country c ON o.countryID = c.id
            GROUP BY o.offer, o.countryID, o.date";
    // $sql = "SELECT * FROM openers";
    $result = $conn->query($sql);
    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($data, $row);
        }
    }

    $conn->close();

    echo json_encode($data);
}

getTracking();
