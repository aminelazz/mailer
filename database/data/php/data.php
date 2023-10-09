<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Access-Control-Allow-Methods: DELETE"); // Allow the DELETE HTTP method
header("Content-Type: application/json");

// Get all data from database
function getAllData($countryID)
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

    $where = $countryID != null ? "WHERE d.countryID = '$countryID'" : "";

    // Get data from database
    $sql = "SELECT d.id, d.name, d.nbrRecipients, d.countryID, c.name AS countryName
            FROM data d
            JOIN country c ON d.countryID = c.id
            $where";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $response['status'] = 'success';
        $response['data'] = array();

        while ($row = $result->fetch_assoc()) {
            $data = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'countryID' => $row['countryID'],
                'countryName' => $row['countryName'],
                'nbrRecipients' => $row['nbrRecipients'],
            );

            array_push($response['data'], $data);
        }
    } else {
        $response['status'] = 'not found';
        $response['message'] = 'No data found';

        http_response_code(404);
    }

    echo json_encode($response);
}

// Get data from database by ID
function getData($id)
{
    // Initialize $response variable
    $response = array();

    if ($id == null) {
        $response['status'] = 'error';
        $response['message'] = 'Error getting data: No ID found';

        http_response_code(400);
        echo json_encode($response);
        die();
    }

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
    $sql = "SELECT d.id, d.name, d.nbrRecipients, d.countryID, c.name AS countryName
            FROM data d
            JOIN country c ON d.countryID = c.id
            WHERE d.id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $response['status'] = 'success';
        $response['data'] = array();

        while ($row = $result->fetch_assoc()) {
            $data = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'countryID' => $row['countryID'],
                'countryName' => $row['countryName'],
                'nbrRecipients' => $row['nbrRecipients'],
            );

            array_push($response['data'], $data);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No data found';

        http_response_code(404);
    }

    echo json_encode($response);
}

// Delete data from database by ID
function deleteData($id)
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

    // Delete data from database
    $sql = "DELETE FROM data WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Data deleted successfully';
        // $response['query'] = $sql;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error deleting data: ' . $conn->error . '';
        // $response['query'] = $sql;
    }

    echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        getData($_GET['id']);
    } else {
        if (isset($_GET['countryID'])) {
            $countryID = $_GET['countryID'];
        } else {
            $countryID = null;
        }
        getAllData($countryID);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['id'])) {
        deleteData($_GET['id']);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error deleting data: No ID found';

        echo json_encode($response);
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error: Invalid request method';

    echo json_encode($response);
}
