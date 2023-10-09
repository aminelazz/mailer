<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

function downloadData($id)
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
        // close with 400 status code and error message
        $response['status'] = 'error';
        $response['message'] = 'Error connecting to database: ' . $e->getMessage() . '';

        http_response_code(500);
        echo json_encode($response);
        die();
    }

    if ($conn->connect_error) {
        $response['status'] = 'error';
        $response['message'] = 'Error connecting to database: ' . $conn->connect_error . '';

        http_response_code(500);
        echo json_encode($response);
        die();
    }

    // Get data from database
    $sql = "SELECT data FROM data WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $response['status'] = 'success';
        $response['data'] = "";

        while ($row = $result->fetch_assoc()) {
            $response['data'] = $row['data'];
        }

        http_response_code(200);
        echo json_encode($response);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No data found';

        http_response_code(404);
        echo json_encode($response);
        die();
    }
}

if (isset($_GET['id'])) {
    downloadData($_GET['id']);
} else {
    $response['status'] = 'error';
    $response['message'] = 'No data ID provided';

    http_response_code(400);
    echo json_encode($response);
    die();
}
