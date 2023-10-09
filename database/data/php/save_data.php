<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

function saveData($data)
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

    $dataName = $data['name'];
    $countryID = $data['country'];
    $nbrRecipients = $data['nbrRecipients'];


    // Get file contents
    // $file = file_get_contents($_FILES["data"]["tmp_name"]);

    $file = $data['data'];

    // Upload to database
    $sql = "INSERT INTO data (name, countryID, data, nbrRecipients) VALUES ('$dataName', '$countryID', '$file', '$nbrRecipients')";
    $result = $conn->query($sql);

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Data uploaded successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error uploading data: ' . $conn->error . '';
    }

    echo json_encode($response);
}

// Call the function with the form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Call the function to send emails using PHPMailer and get the email responses
    saveData($_POST);
}
