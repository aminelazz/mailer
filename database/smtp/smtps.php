<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain

header("Content-Type: application/json");

// Set the desired time zone
date_default_timezone_set('Africa/Casablanca');

// set maximum execution time to no limit
set_time_limit(0);
// set memory limit to no limit
ini_set('memory_limit', '-1');

/**
 * Get the SMTP servers from the database
 *
 * @return array
 */
function get_smtps()
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
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $e->getMessage(),
        );
        echo json_encode($response);
        exit();
    }

    if ($conn->connect_error) {
        http_response_code(500);
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $conn->connect_error,
        );
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT lower(s.domain) AS domain, lower(s.smtp) AS smtp
            FROM smtps s";

    $result = $conn->query($sql);

    $smtpsByDomain = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $domain = $row['domain'];
            $smtp = $row['smtp'];

            // Check if the domain already exists in the array
            if (array_key_exists($domain, $smtpsByDomain)) {
                // If it exists, add the SMTP value to the existing array
                array_push($smtpsByDomain[$domain]['smtps'], $smtp);
            } else {
                // If it doesn't exist, create a new array for the domain
                $smtpsByDomain[$domain] = array('domain' => $domain, 'smtps' => array($smtp));
            }
        }
    } else {
        http_response_code(404);
        $response = array(
            "status" => 'Error',
            "message" => "No SMTPs found",
        );
        echo json_encode($response);
        exit();
    }

    $conn->close();

    $smtpsByDomain = array_values($smtpsByDomain);
    return $smtpsByDomain;
}

/**
 * Save the SMTP server to the database
 *
 * @param string $domain Domain recieved from POST request
 * @param string $smtp SMTP recieved from POST request
 * @return void
 */
function save_smtp($domain, $smtp)
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
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $e->getMessage(),
        );
        echo json_encode($response);
        exit();
    }

    if ($conn->connect_error) {
        http_response_code(500);
        $response = array(
            "status" => 'Error',
            "message" => "Connection failed: " . $conn->connect_error,
        );
        echo json_encode($response);
        exit();
    }

    $sql = "INSERT INTO smtps (domain, smtp)
            SELECT LOWER('$domain'), LOWER('$smtp')
            WHERE NOT EXISTS (
                SELECT 1 FROM smtps WHERE domain = '$domain' AND smtp = '$smtp'
            )";

    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            http_response_code(200);
            $response = array(
                "status" => 'Success',
                "message" => "SMTP saved successfully",
            );
            echo json_encode($response);
            exit();
        } else {
            http_response_code(409);
            $response = array(
                "status" => 'Duplicate',
                "message" => "SMTP already exists",
            );
            echo json_encode($response);
            exit();
        }
    } else {
        http_response_code(500);
        $response = array(
            "status" => 'Error',
            "message" => "Error: " . $conn->error,
        );
        echo json_encode($response);
        exit();
    }

    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $smtps = get_smtps();
    echo json_encode($smtps);

    http_response_code(200);
    $response = array(
        "status" => 'Success',
        "message" => "SMTPs fetched successfully",
        "data" => $smtps,
    );
    // echo json_encode($response);
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['domain']) && isset($_POST['smtp'])) {
        $domain = $_POST['domain'];
        $smtp = $_POST['smtp'];

        save_smtp($domain, $smtp);
    } else {
        http_response_code(400);
        $response = array(
            "status" => 'Error',
            "message" => "Missing domain or SMTP server",
        );
        echo json_encode($response);
        exit();
    }
} else {
    http_response_code(405);
    $response = array(
        "status" => 'Error',
        "message" => "Method not allowed",
    );
    echo json_encode($response);
    exit();
}
