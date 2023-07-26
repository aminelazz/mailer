<?php
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["fileID"])) {
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
    $fileID = $_GET["fileID"];
    $sql = "SELECT attachements FROM offer WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fileID);
    $stmt->execute();
    $stmt->bind_result($fileData);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Decode the base64 encoded file data
    $zipFileData = base64_decode($fileData);

    // Set appropriate headers to force the browser to download the file
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=\"attachements.zip\"");
    header("Content-Length: " . strlen($zipFileData));

    // Output the zip file data
    echo $zipFileData;
    exit;
}
