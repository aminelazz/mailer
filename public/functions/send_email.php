<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain

date_default_timezone_set('Africa/Casablanca'); // Set the desired time zone

set_time_limit(0); // set maximum execution time to no limit
ini_set('memory_limit', '-1'); // set memory limit to no limit

// Include the necessary PHPMailer and other configurations here
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

// Generate random string
function generateRandomString($length, $characters)
{
    $randomString = '';
    $charactersLength = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

// Replace tags
function replaceTags($var, $username, $recipient, $link)
{

    $var = str_replace('[mail_date]', date('d/m/Y'), $var);
    $var = str_replace('[time]', date('H:i:s'), $var);

    if (isset($recipient) && $recipient != "") {
        list($toName, $toDomain) = explode("@", $recipient);

        $var = str_replace('[email]', $recipient, $var);
        $var = str_replace('[first_name]', $toName, $var);
    }

    if (isset($username) && $username != "") {
        list($fromName, $fromDomain) = explode("@", $username);

        $var = str_replace('[domain]', $fromDomain, $var);
    }




    $pattern = '/\[(a|A|n|al|au|an|anl|anu)_(\d+)\]/';

    $var = preg_replace_callback($pattern, function ($matches) {
        $expression = $matches[1];
        $length = intval($matches[2]);

        switch ($expression) {
            case 'a':
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'n':
                $characters = '0123456789';
                break;
            case 'al':
                $characters = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 'au':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'an':
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 'anl':
                $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'anu':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            default:
                return $matches[0]; // Return the original expression if no match is found
        }

        return generateRandomString($length, $characters);
    }, $var);

    return $var;
}

// Define the function that sends emails using PHPMailer
function sendEmailsUsingPHPMailer($formData, $subSend = false)
{
    $mail = new PHPMailer;

    $ip                 = base64_encode($_SERVER['REMOTE_ADDR']);
    $UID                = $formData['UID'];
    $offerName          = $formData['offerName'];
    $serverIndex             = $formData['serverIndex'];
    $server             = $formData['server'];
    $headers            = explode("\n", $formData['headers']);
    $contentType        = $formData['contentType'];
    $charset            = $formData['charset'];
    $encoding           = $formData['encoding'];
    $priority           = $formData['priority'];
    $fromNameEncoding   = $formData['fromNameEncoding'];
    $fromName           = $formData['fromName'];
    $subjectEncoding    = $formData['subjectEncoding'];
    $subject            = $formData['subject'];
    $fromEmailCheck     = $formData['fromEmailCheck'];
    $fromEmail          = $formData['fromEmail'];
    $replyToCheck       = $formData['replyToCheck'];
    $replyTo            = $formData['replyTo'];
    $returnPathCheck    = $formData['returnPathCheck'];
    $returnPath         = $formData['returnPath'];
    $tracking           = $formData['tracking'];
    $link               = $formData['link'];
    $creative           = $formData['creative'];
    $creativeID         = $formData['creativeID'];
    $recipient          = $formData['recipient'];
    $countryID          = $formData['countryID'];
    $date               = $formData['date'];

    $trackImg = "<img src='https://45.145.6.18/database/tracking/php/track.php?email=[email]&offer=$offerName&countryID=$countryID&date=$date&ip=$ip&UID=$UID&creativeID=$creativeID' border='0' alt='' />";

    if ($tracking == "false" || $tracking == false) {
        $trackImg = "";
    }

    if ($subSend) {
        $index      = $formData['index'] + 1;
        $creativeID = $formData['creativeID'] + 1;
        // $trackImg   = "<img src='http://45.145.6.18/database/tracking/php/track.php?email=[email]&offer={$offerName}_{$creativeID}_sub{$index}&countryID=$countryID&date=$date&ip=$ip&UID=$UID&creativeID=$creativeID' border='0' alt='' />";
        $trackImg   = "";
    }

    $trackImg = replaceTags($trackImg, '', $recipient, '');

    // append the tracking image to the creative
    $creative = $creative . $trackImg;

    $headerProperties = [];

    // Split the header properties by ": "
    for ($i = 0; $i < sizeof($headers); $i++) {
        $h = explode(": ", $headers[$i]);
        array_push($headerProperties, $h[1]);
    }

    list($messageID, $XMailer, $autoSubmit, $XAutoResponse, $XAbuse) = $headerProperties;

    list($host, $port, $smtpSecure, $username, $password) = explode(":", $server);

    // Configure From Name
    $fromName = replaceTags($fromName, $username, $recipient, $link);
    switch ($fromNameEncoding) {
        case '7bit':
            $fromName = iconv(mb_detect_encoding($fromName, 'auto'), 'UTF-8', $fromName);
            break;
        case 'base64':
            $fromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
            break;
        case 'binary':
            $fromName = imap_base64(imap_binary($fromName));
            break;
        case 'quoted-printable':
            $fromName = '=?UTF-8?Q?' . quoted_printable_encode($fromName) . '?=';
            break;
        default:
            break;
    }

    // Configure Subject
    $subject = replaceTags($subject, $username, $recipient, $link);
    switch ($subjectEncoding) {
        case '7bit':
            $subject = iconv(mb_detect_encoding($subject, 'auto'), 'UTF-8', $subject);
            break;
        case 'base64':
            $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            break;
        case 'binary':
            $subject = imap_base64(imap_binary($subject));
            break;
        case 'quoted-printable':
            $subject = '=?UTF-8?Q?' . quoted_printable_encode($subject) . '?=';
            break;
        default:
            break;
    }

    // Configure From Email
    if ($fromEmailCheck == "false" || $fromEmailCheck == false) {
        $fromEmail = $username;
    }

    // Configure ReplyTo
    if ($replyToCheck == "false" || $replyToCheck == false) {
        $replyTo = $username;
    }

    // Configure Return Path (Confirm Reading) and add it
    if ($returnPathCheck == "false" || $returnPathCheck == false) {
        $returnPath = $username;
    }

    // Add mail config
    $mail->setFrom(replaceTags($fromEmail, $username, $recipient, $link), replaceTags($fromName, $username, $recipient, $link));
    $mail->AddReplyTo(replaceTags($replyTo, $username, $recipient, $link), replaceTags($fromName, $username, $recipient, $link));
    // $mail->Sender           = replaceTags($returnPath, $username, $recipient, $link);
    $mail->addCustomHeader("Return-Path", replaceTags($returnPath, $username, $recipient, $link));
    $mail->Subject          = $subject;
    $mail->MessageDate      = PHPMailer::rfcDate();
    $mail->Encoding         = $encoding;
    $mail->ContentType      = $contentType;
    $mail->CharSet          = $charset;
    $mail->Priority         = $priority;


    // Configure SMTP
    $mail->isSMTP();                                                                                                    // Send using SMTP
    $mail->SMTPDebug        = 0;                                                                                        // Disable verbose debug output
    // $mail->SMTPDebug        = SMTP::DEBUG_SERVER;                                                                    // Enable verbose debug output
    $mail->SMTPKeepAlive    = true;                                                                                     // Keep the SMTP connection open after each message
    $mail->SMTPAuth         = true;                                                                                     // Enable SMTP authentication
    $mail->Host             = $host;                                                                                    // Set the SMTP server to send through
    $mail->Port             = $port;                                                                                    // TCP port to connect to
    $mail->SMTPSecure       = strtolower($smtpSecure);                                                                  // Enable implicit TLS/SSL encryption
    $mail->Username         = $username;                                                                                // SMTP username
    $mail->Password         = $password;                                                                                // SMTP password
    $mail->Priority         = $priority;                                                                                // Email priority
    $mail->MessageID        = replaceTags($messageID, $username, $recipient, $link);                                    // An ID to be used in the Message-ID header
    $mail->XMailer          = replaceTags($XMailer, $username, $recipient, $link);                                      // X-Mailer header
    $mail->addCustomHeader("Auto-Submitted", replaceTags($autoSubmit, $username, $recipient, $link));
    $mail->addCustomHeader("X-Auto-Response-Suppress", replaceTags($XAutoResponse, $username, $recipient, $link));
    $mail->addCustomHeader("X-Abuse", replaceTags($XAbuse, $username, $recipient, $link));

    //Recipients
    $mail->addBCC($recipient);                                      //Name is optional

    // Check if main send or sub send
    if ($subSend) {
        $attachement = $formData['attachement'];
        if ($attachement != null) {
            $attachement            = (array)$attachement;
            $attachementExtension   = $attachement['extension'];
            $attachementContent     = base64_decode($attachement['content']);

            $fileName = "attachement.$attachementExtension";
            // $file = file_put_contents($fileName, $attachementContent);

            // $mail->addAttachment($file, $fileName);

            $mail->addStringAttachment($attachementContent, $fileName, 'base64');

            // unlink($fileName);
        }
    } else {
        // Attach files from the form
        if (!empty($_FILES['attachements']['tmp_name'])) {
            // Check if multiple files were uploaded
            if (is_array($_FILES['attachements']['tmp_name'])) {
                // Loop through each uploaded file
                foreach ($_FILES['attachements']['tmp_name'] as $index => $tmp_name) {
                    // Get the filename
                    $filename = $_FILES['attachements']['name'][$index];
                    // Add the attachment to the email
                    $mail->addAttachment($tmp_name, $filename);
                }
            } else {
                // Single file uploaded
                $tmp_name = $_FILES['attachements']['tmp_name'];
                $filename = $_FILES['attachements']['name'];
                // Add the attachment to the email
                $mail->addAttachment($tmp_name, $filename);
            }
        }
    }

    //Content
    $mail->msgHTML(replaceTags($creative, $username, $recipient, $link));       // Create a message body from an HTML string

    // Send the email using PHPMailer
    if ($mail->Send()) {
        $response   = array(
            'status'        => 'success',
            'message'       => 'Mail Sent successfully',
            'email'         => $recipient,
            'serverIndex'   => $serverIndex,
            'server'        => $server,
        );
    } else {
        $response   = array(
            'status'        => 'danger',
            'message'       => $mail->ErrorInfo,
            'email'         => $recipient,
            'serverIndex'   => $serverIndex,
            'server'        => $server,
        );
    }

    $mail->clearAllRecipients();

    header("content-type: application/json");
    // Send the response back to the client-side code using AJAX
    echo json_encode($response);

    // Flush the output buffer to send the response immediately
    ob_flush();
    flush();
}

function sendSubcreatives($UID, $creativeID, $email)
{

    $reqURL = "https://45.145.6.18/database/subcreatives/operations.php?UID=$UID&creativeID=$creativeID";
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

    $response = json_decode($response, true);
    // echo $response;

    if ($response['status'] == 'error') {
        http_response_code(500);
        echo json_encode($response);
        die();
    }

    $subcreatives = $response['subcreatives'];

    // echo json_encode($subcreatives);

    $subData = $subcreatives[0]["subData"];
    $subData = json_decode($subData, true);

    foreach ($subcreatives as $index => $subcreative) {
        // Choose a random server from the list
        $servers    = $subData['servers'];
        $server     = $servers[array_rand($servers, 1)];

        // Choose a random from name from the list
        $fromNames  = $subData['fromNames'];
        $fromName   = $fromNames[array_rand($fromNames, 1)];

        // Choose a random subject from the list
        $subjects   = $subData['subjects'];
        $subject    = $subjects[array_rand($subjects, 1)];

        $formData = array(
            'index'             => $index,
            'UID'               => $UID,
            'offerName'         => $subData['offerName'],
            'server'            => $server,
            'headers'           => $subData['headers'],
            'contentType'       => $subData['contentType'],
            'charset'           => $subData['charset'],
            'encoding'          => $subData['encoding'],
            'priority'          => $subData['priority'],
            'fromNameEncoding'  => $subData['fromNameEncoding'],
            'fromName'          => $fromName,
            'subjectEncoding'   => $subData['subjectEncoding'],
            'subject'           => $subject,
            'fromEmailCheck'    => $subData['fromEmailCheck'],
            'fromEmail'         => $subData['fromEmail'],
            'replyToCheck'      => $subData['replyToCheck'],
            'replyTo'           => $subData['replyTo'],
            'returnPathCheck'   => $subData['returnPathCheck'],
            'returnPath'        => $subData['returnPath'],
            'tracking'          => $subData['tracking'],
            'link'              => $subData['link'],
            'creative'          => $subcreative['subcreative'],
            'creativeID'        => $creativeID,
            'attachement'       => json_decode($subcreative['subAttachement']),
            'recipient'         => $email,
            'countryID'         => $subData['countryID'],
            'date'              => $subData['date'],
        );


        // Sleep for 10 seconds
        sendEmailsUsingPHPMailer($formData, true);
        sleep(10);
    }

    // echo json_encode($formData);



    // echo $properties = json_encode([
    //     'fromName' => $fromName,
    //     'subject' => $subject,
    // ]);
}

// Call the function with the form data sent from AJAX using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Call the function to send emails using PHPMailer and get the email responses
    sendEmailsUsingPHPMailer($_POST);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header("content-type: application/json");

    if (!isset($_GET['UID']) || !isset($_GET['creativeID']) || !isset($_GET['email'])) {
        http_response_code(400);
        $response = array(
            'status' => 'error',
            'message' => 'UID, creativeID and email are required',
        );

        echo json_encode($response);
        die();
    }
    // Call the function to send subcreatives
    sendSubcreatives($_GET['UID'], $_GET['creativeID'], $_GET['email']);
}
