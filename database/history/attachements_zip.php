<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    // Your database connection configuration here
    $servername = "localhost";
    $username = "stcom";
    $password = "Maruil589";
    $dbname = "stcom";

    // Create connection
    $conn = new mysqli(
        $servername,
        $username,
        $password,
        $dbname
    );

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the zip file data from the database based on the fileID
    $id = $_GET["id"];

    $sql = "SELECT id, attachements
            FROM offer
            WHERE `id` = $id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $attachements = $row['attachements'];

            // Decode the base64 encoded file data
            $zipFileData = base64_decode($attachements);
            echo $zipFileData;
            if ($zipFileData == null) {
                http_response_code(404); // Not found
                echo json_encode([
                    'error' => 'No attachements has been found',
                ]);
            } else {
                // Set appropriate headers to force the browser to download the file
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=\"attachements.zip\"");
                header("Content-Length: " . strlen($zipFileData));

                // Output the zip file data
                echo $zipFileData;
            }

            exit;
        }
    } else {
        // Invalid or missing parameter, return an error response or handle it as needed
        http_response_code(404); // Not found
        echo json_encode(array('error' => 'No offer has been found.'));
        exit;
    }

    $conn->close();
} else {
    // Invalid or missing parameter, return an error response or handle it as needed
    http_response_code(400); // Bad Request
    echo json_encode(array('error' => 'Invalid or missing parameter.'));
    exit;
}
