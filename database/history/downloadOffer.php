<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header("Content-Type: application/json");

// Function to get offer history data from the database
function downloadOffer($id)
{
    $dbPath = "../db.json";
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
        die("Connection failed: " . $conn->connect_error);
    }

    // $id = $_GET['id'];

    // Perform the SQL query to fetch the offer history data
    $sql = "SELECT id, offerID, offerName, attachements
            FROM offer
            WHERE `id` = $id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $offerID = $row['offerID'];
            $offerName = $row['offerName'];
            $attachements = $row['attachements'];

            // Call the data_json.php using file_get_contents
            $data_json_url = "./database/history/data_json.php?id=$id";
            $json_data = file_get_contents($data_json_url);

            $data = json_decode($json_data, true);

            // Decode the creative
            $data['creative'] = base64_decode($data['creative']);

            $offer = json_encode($data);

            // Decode the base64 encoded file data
            $zipFileData = base64_decode($attachements);

            // Create a temporary file to store the original zip file's content
            $tempZipFile = tempnam(sys_get_temp_dir(), '../tmp_zip_files/temp_zip');
            file_put_contents($tempZipFile, $zipFileData);

            $zip = new ZipArchive();
            //zip file name
            $zip_file_name = $offerID . '_' . $offerName . '.zip';
            $zipPath = "/var/www/html/database/tmp_zip_files/$zip_file_name";

            try {
                //create the new zip archive using the $file_name above
                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    if ($zipFileData != "") {
                        $zip->addFile($tempZipFile, 'attachements.zip');
                    }
                    $zip->addFromString($offerID . '_' . $offerName . '.json', $offer);
                    //close the archive
                    $zip->close();

                    unlink($tempZipFile);

                    // Set appropriate headers to force the browser to download the file
                    header("Content-Type: application/zip");
                    header("Content-Disposition: attachment; filename=$zipPath");
                    header("Content-Length: " . filesize($zipPath));

                    // Output the zip file contents
                    readfile($zipPath);
                    // echo $zip_file_name;

                    // Delete the temporary zip file from the server
                    unlink($zipPath);
                }
            } catch (Exception $e) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error: {$e}",
                ]);
            }
        }
    } else {
        // Invalid or missing parameter, return an error response or handle it as needed
        http_response_code(404); // Not found
        echo json_encode(array('error' => 'No offer has been found.'));
        exit;
    }

    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    downloadOffer($_GET['id']);
} else {
    // Invalid or missing parameter, return an error response or handle it as needed
    http_response_code(400); // Bad Request
    echo json_encode(array('error' => 'Invalid or missing parameter.'));
    exit;
}
