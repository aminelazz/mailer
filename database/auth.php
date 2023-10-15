<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

// Function to get offer history data from the database
function getOfferHistory($credentials)
{
    $dbPath = "./db.json";
    $db = json_decode(file_get_contents($dbPath), true);

    // Your database connection configuration here
    $dbservername = $db["servername"];
    $dbusername = $db["username"];
    $dbpassword = $db["password"];
    $dbname = $db["dbname"];

    // // Get db credentials from db.json
    // $dbInfos = file_get_contents("./db.json");
    // $dbInfos = json_decode($dbInfos, true);
    // extract($dbInfos);

    // Create connection
    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $conn->connect_error,
        );
        http_response_code(500);
        echo json_encode($response);
        exit();
    }

    $username = $credentials['username'];
    $password = $credentials['password'];

    // Perform the SQL query to fetch the offer history data
    $sql = "SELECT id, lower(username) AS username, password, firstName, lastName
            FROM mailer
            WHERE username = '$username' AND password = '$password'";

    $result = $conn->query($sql);
    $mailer = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $mailer = array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password'],
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName']
            );

            $response = array(
                'status' => 'success',
                'message' => 'User found',
                'data' => $mailer
            );
            http_response_code(200);
        }
    } else {
        $response = array(
            'status' => 'not found',
            'message' => 'Username or Password incorrect',
            'data' => $mailer
        );
        http_response_code(404);
    }

    $conn->close();

    echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        http_response_code(400);
        echo json_encode(
            array(
                'status' => 'error',
                'message' => 'Missing username or password'
            )
        );
        exit;
    }

    getOfferHistory($_POST);
} else {
    http_response_code(405);
    echo json_encode(
        array(
            'status' => 'error',
            'message' => 'Invalid request method'
        )
    );
    exit;
}
