<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain
header('Content-Type: application/json');

date_default_timezone_set('Africa/Casablanca'); // Set the desired time zone

set_time_limit(0); // set maximum execution time to no limit
ini_set('memory_limit', '-1'); // set memory limit to no limit

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../functions/src/SMTP.php';
require '../../functions/src/PHPMailer.php';
require '../../functions/src/Exception.php';

function smtpCheck($smtpServer, $debug)
{
    list($host, $port, $encryption, $username, $password) = explode(":", $smtpServer);

    $mail = new PHPMailer();

    // Set SMTP server settings
    $mail->IsSMTP();
    $mail->SMTPDebug = $debug; // Enable verbose debugging (you can adjust the level as needed)
    $mail->Timeout = 120; // Set the timeout value for SMTP connections to 2 minutes
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = $encryption;
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->Username = $username;
    $mail->Password = $password;

    $mail->SetFrom($username, 'SMTP Check');
    $mail->AddAddress($username);
    $mail->Subject = 'SMTP Check';
    $mail->Body = 'This is a test email sent from the SMTP Check tool.';
    $mail->IsHTML(false);

    try {
        // Try to connect to the SMTP server
        $mail->SmtpConnect();
        // echo "Connected to $host:$port\n";

        // Send the test email
        if (!$mail->Send()) {
            http_response_code(401);
            $response = array(
                "status"    => "error",
                "message"   => $mail->ErrorInfo,
                "server"    => $smtpServer
            );
        } else {
            http_response_code(200);
            $response = array(
                "status"    => "success",
                "message"   => "SMTP server is valid!",
                "server"    => $smtpServer
            );
        }

        // Close the SMTP connection
        $mail->SmtpClose();
        // echo "Connection closed\n";
    } catch (Exception $e) {
        http_response_code(401);
        $response = array(
            "status"    => "error",
            "message"   => $e->getMessage(),
            "server"    => $smtpServer
        );
    }

    echo json_encode($response);

    // Flush the output buffer to send the data immediately
    ob_flush();
    flush();
}

if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
    if (isset($_POST['smtpServer']) && isset($_POST['debug'])) {
        smtpCheck($_POST['smtpServer'], $_POST['debug']);
    } else {
        $response = array(
            "status" => "error",
            "message" => "Missing SMTP server or debug level",
        );
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => "error",
        "message" => "Please use POST method & provide an SMTP server",
    );
    echo json_encode($response);
}
