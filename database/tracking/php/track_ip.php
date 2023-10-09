<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
// Set the desired time zone
date_default_timezone_set('Africa/Casablanca');

if (!isset($_GET['email']) || !isset($_GET['offer'])) {
    http_response_code(400);
    error_log('Track error: Missing parameters');
    die('Missing parameters');
    exit;
}

function getIPAddress()
{
    //whether ip is from the share internet  
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from the remote address  
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$ip = getIPAddress();

echo $ip;

$email = $_GET['email'];
$offer = $_GET['offer'];
$date = date("Y-m-d H:i:s");

# get geolocation info
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://freegeoip.live/json/$ip",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$geo = json_decode(curl_exec($curl), true);

curl_close($curl);

if (is_array($geo) && count($geo)) {
    $meta['country-code']   = $geo['country_code'];
    $meta['country-name']   = $geo['country_name'];
    $meta['region-name']    = $geo['region_name'];
    $meta['city-name']      = $geo['city'];
    $meta['latitude']       = $geo['latitude'];
    $meta['longitude']      = $geo['longitude'];
} else {
    $meta['country-code'] = 'US';
    $meta['country-name'] = ucwords(strtolower(self::COUNTRIES[$meta['country-code']]));
    $meta['region-name'] = 'Unknown';
    $meta['city-name'] = 'Unknown';
    $meta['latitude'] = 'Unknown';
    $meta['longitude'] = 'Unknown';
}

$countryName = $meta['country-name'] . ' - ' . $ip;
$countryCode = $meta['country-code'];

echo json_encode($meta);

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

if ($conn->connect_error) {
    http_response_code(500);
    die('Error connecting to database: ' . $conn->connect_error . '');
}

// INSERT INTO database values (email, offer, countryID, date) but only if it doesn't exist already

$sql = "INSERT INTO openers (email, offer, countryName, countryCode, date)
        SELECT * FROM (SELECT '$email', '$offer', '$countryName', '$countryCode', '$date') AS tmp
        WHERE NOT EXISTS (
            SELECT email, offer, countryName, countryCode FROM openers WHERE email = '$email' AND offer = '$offer' AND countryName = '$countryName' AND countryCode = '$countryCode'
        ) LIMIT 1";

if ($conn->query($sql) === TRUE) {
    http_response_code(200);
    echo json_encode([
        'message' => 'Track successfully added',
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error adding track: ' . $conn->error,
    ]);
}

$conn->close();
