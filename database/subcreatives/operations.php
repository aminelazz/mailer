<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header('Content-Type: application/json');

// set maximum execution time to no limit
set_time_limit(0);
// set memory limit to no limit
ini_set('memory_limit', '-1');

error_reporting(E_ERROR | E_PARSE);

function saveToDB($formData)
{
    $dbPath = "../db.json";
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
        $response['status'] = 'error';
        $response['message'] = 'Error connecting to database: ' . $conn->connect_error . '';

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

    $UID = $formData['UID'];
    $creativeID = $formData['creativeID'];
    $subcreative = $formData['subcreative'];
    $subData = $formData['subData'];

    $subcreative = base64_encode($subcreative);
    $subData = base64_encode($subData);

    if (isset($_FILES['subAttachement']) && !empty($_FILES['subAttachement'])) {
        $file = $_FILES['subAttachement'];

        $subAttachementExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $subAttachementContent = file_get_contents($file['tmp_name']);
        $subAttachementContent = base64_encode($subAttachementContent);

        $subAttachement = array(
            'extension' => $subAttachementExtension,
            'content' => $subAttachementContent,
        );
        $subAttachement = base64_encode(json_encode($subAttachement));
    } else {
        $subAttachement = NULL;
    }

    $sql = "INSERT INTO subcreatives (UID, creativeID, subcreative, subAttachement, subData) VALUES ('$UID', '$creativeID', '$subcreative', '$subAttachement', '$subData')";

    $result = $conn->query($sql);

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Subcreative uploaded successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error uploading subcreative: ' . $conn->error . '';
    }

    // $response = array(
    //     "status" => "success",
    //     "formData" => $formData
    // );


    echo json_encode($response);
}

function getSubcreatives($UID, $creativeID)
{
    $dbPath = "../db.json";
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
        http_response_code(500);
        die('Error connecting to database: ' . $e->getMessage() . '');
    }

    $sql = "SELECT * FROM subcreatives WHERE UID = '$UID' AND creativeID = $creativeID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $subcreatives = array();
        while ($row = $result->fetch_assoc()) {
            $subcreative = array(
                'subcreative' => base64_decode($row['subcreative']),
                'subAttachement' => base64_decode($row['subAttachement']),
                'subData' => base64_decode($row['subData']),
            );
            array_push($subcreatives, $subcreative);
        }
        $response = array(
            'status' => 'success',
            'subcreatives' => $subcreatives,
        );
        echo json_encode($response);
    } else {
        http_response_code(404);
        $response = array(
            'status' => 'error',
            'message' => 'No subcreatives found',
        );
        echo json_encode($response);
    }

    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;
    saveToDB($formData);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['UID']) || !isset($_GET['creativeID'])) {
        http_response_code(400);
        die('Error: Missing parameters.');
    }
    $UID = $_GET['UID'];
    $creativeID = $_GET['creativeID'];
    getSubcreatives($UID, $creativeID);
    // error_log("Accessed ffrom : " . $_SERVER['REMOTE_ADDR']);
} else {
    http_response_code(400);
    die('Error: Method not allowed.');
}
