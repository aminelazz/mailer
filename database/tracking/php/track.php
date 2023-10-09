<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
// Set the desired time zone
date_default_timezone_set('Africa/Casablanca');

// set maximum execution time to no limit
set_time_limit(0);
// set memory limit to no limit
ini_set('memory_limit', '-1');

function track()
{

    // $ip = base64_decode($_GET['ip']) == '127.0.0.1' ? 'localhost' : $ip;
    $ip = base64_decode($_GET['ip']);
    $ip = $ip == '127.0.0.1' ? 'localhost' : $ip;
    $UID = $_GET['UID'];
    $email = $_GET['email'];
    $creativeID = $_GET['creativeID'];
    $offer = $_GET['offer'];
    $countryID = $_GET['countryID'];
    $date = $_GET['date'];

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
        http_response_code(500);
        die('Error connecting to database: ' . $e->getMessage() . '');
    }

    // INSERT INTO database values (email, offer, countryID, date) but only if it doesn't exist already

    $sql = "INSERT INTO openers (email, offer, countryID, date)
        SELECT * FROM (SELECT '$email', '$offer', '$countryID', '$date') AS tmp
        WHERE NOT EXISTS (
            SELECT email, offer, countryID, date FROM openers WHERE email = '$email' AND offer = '$offer' AND countryID = '$countryID' AND date = '$date'
        ) LIMIT 1";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200);

        // Check if a row has been inserted
        if ($conn->affected_rows > 0) {
            // A row has been inserted
            sendSubcreatives($UID, $creativeID, $ip, $email);
        } else {
            // No row has been inserted
        }

        $filename = 'blank.png';
        $filepath = '../' . $filename;
        $size = filesize($filepath);

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $size);
        readfile($filepath);
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => 'Error adding track: ' . $conn->error,
        ]);
    }

    $conn->close();
}

function sendSubcreatives($UID, $creativeID, $ip, $email)
{
    // $ip = "localhost"; // "localhost
    $reqURL = "$ip/public/functions/send_email.php?UID=$UID&creativeID=$creativeID&email=$email";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL             => $reqURL,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION  => false,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_NONE,
        CURLOPT_CUSTOMREQUEST   => 'GET',
        CURLOPT_SSL_VERIFYPEER  => false,
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    echo "Response from send_email: $response";
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['email']) || !isset($_GET['offer']) || !isset($_GET['countryID']) || !isset($_GET['date']) || !isset($_GET['UID']) || !isset($_GET['ip']) || !isset($_GET['creativeID'])) {
        http_response_code(400);
        error_log('Track error: Missing parameters');
        die('Track error: Missing parameters');
    }
    track();
} else {
    http_response_code(405);
    die('Method not allowed');
}
