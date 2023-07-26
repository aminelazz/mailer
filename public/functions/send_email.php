<?php
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
        list($toName, $toDmain) = explode("@", $recipient);

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
function sendEmailsUsingPHPMailer($formData)
{
    $mail = new PHPMailer;

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
    $fromEmail          = (!empty($formData['fromEmail'])) ? true : false;
    $replyToCheck       = $formData['replyToCheck'];
    $replyTo            = (!empty($formData['replyTo'])) ? true : false;
    $returnPathCheck    = $formData['returnPathCheck'];
    $returnPath         = (!empty($formData['returnPath'])) ? true : false;
    $link               = $formData['link'];
    $creative           = $formData['creative'];
    $recipient          = $formData['recipient'];

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
    if (!$fromEmailCheck) {
        $fromEmail = $username;
    }

    // Configure ReplyTo
    if (!$replyToCheck) {
        $replyTo = $username;
    }

    // Configure Return Path (Confirm Reading) and add it
    if (!$returnPathCheck) {
        $returnPath = $username;
    }

    // Add mail config
    $mail->setFrom(replaceTags($fromEmail, $username, $recipient, $link), replaceTags($fromName, $username, $recipient, $link));
    $mail->AddReplyTo(replaceTags($replyTo, $username, $recipient, $link), replaceTags($fromName, $username, $recipient, $link));
    $mail->ConfirmReadingTo = replaceTags($returnPath, $username, $recipient, $link);
    $mail->Subject          = $subject;
    $mail->MessageDate      = PHPMailer::rfcDate();
    $mail->Encoding         = $encoding;
    $mail->ContentType      = $contentType;
    $mail->CharSet          = $charset;
    $mail->Priority         = $priority;


    // Configure SMTP
    $mail->isSMTP();                                                                                            // Send using SMTP
    $mail->SMTPDebug        = 0;                                                                                // Enable verbose debug output
    // $mail->SMTPDebug        = SMTP::DEBUG_SERVER;                                                               // Enable verbose debug output
    $mail->SMTPKeepAlive    = true;                                                                             // Keep the SMTP connection open after each message
    $mail->SMTPAuth         = true;                                                                             // Enable SMTP authentication
    $mail->Host             = $host;                                                                            // Set the SMTP server to send through
    $mail->Port             = $port;                                                                            // TCP port to connect to
    $mail->SMTPSecure       = strtolower($smtpSecure);                                                          // Enable implicit TLS/SSL encryption
    $mail->Username         = $username;                                                                        // SMTP username
    $mail->Password         = $password;                                                                        // SMTP password
    $mail->Priority         = $priority;                                                                        // Email priority
    $mail->MessageID        = replaceTags($messageID, $username, $recipient, $link);                                    // An ID to be used in the Message-ID header
    $mail->XMailer          = replaceTags($XMailer, $username, $recipient, $link);                                      // X-Mailer header
    $mail->addCustomHeader("Auto-Submitted", replaceTags($autoSubmit, $username, $recipient, $link));
    $mail->addCustomHeader("X-Auto-Response-Suppress", replaceTags($XAutoResponse, $username, $recipient, $link));
    $mail->addCustomHeader("X-Abuse", replaceTags($XAbuse, $username, $recipient, $link));

    //Recipients
    $mail->addBCC($recipient);                                      //Name is optional

    // Attachments
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

    //Content
    $mail->msgHTML(replaceTags($creative, $username, $recipient, $link));       // Create a message body from an HTML string



    // Send the email using PHPMailer
    if ($mail->Send()) {
        $response   = 'Mail Sent successfully';
        $status     = 'success';
    } else {
        $response   = 'Error: ' . $mail->ErrorInfo;
        $status     = 'danger';
    }

    $mail->clearAllRecipients();
    // Send the response back to the client-side code using AJAX
    echo json_encode([
        $recipient => $response,
        'status'   => $status,
    ]) . "||";

    // Flush the output buffer to send the response immediately
    ob_flush();
    flush();
}

// Call the function with the form data sent from WebSocket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Call the function to send emails using PHPMailer and get the email responses
    sendEmailsUsingPHPMailer($_POST);
}
