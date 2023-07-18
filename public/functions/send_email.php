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

    $servers            = explode("\n", $formData['servers']);
    $pauseAfterSend     = $formData['pauseAfterSend'];
    $rotationAfter      = $formData['rotationAfter'];
    $BCCnumber          = $formData['BCCnumber'];
    $headers            = explode("\n", $formData['headers']);
    $contentType        = $formData['contentType'];
    $charset            = $formData['charset'];
    $encoding           = $formData['encoding'];
    $priority           = $formData['priority'];
    $fromNameEncoding   = $formData['fromNameEncoding'];
    $fromName           = $formData['fromName'];
    $subjectEncoding    = $formData['subjectEncoding'];
    $subject            = $formData['subject'];
    $fromEmailCheck     = isset($formData['fromEmailCheck']) ? $formData['fromEmailCheck'] : false;
    $fromEmail          = (!empty($formData['fromEmail'])) ? true : false;
    $replyToCheck       = isset($formData['replyToCheck']) ? $formData['replyToCheck'] : false;
    $replyTo            = (!empty($formData['replyTo'])) ? true : false;
    $returnPathCheck    = isset($formData['returnPathCheck']) ? $formData['returnPathCheck'] : false;
    $returnPath         = (!empty($formData['returnPath'])) ? true : false;
    $link               = $formData['link'];
    $attachements       = isset($formData['attachements']) ? $formData['attachements'] : '';
    $creative           = $formData['creative'];
    $recipients         = explode("\n", $formData['recipients']);
    $blacklist          = explode("\n", $formData['blacklist']);

    $emailResponses = [];

    $headerProperties = [];

    // Configure From Name
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
    $subject = replaceTags($subject, '', '', $link);
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

    // Add mail config
    $mail->setFrom(replaceTags($fromEmail, '', '', $link), replaceTags($fromName, '', '', $link));
    $mail->AddReplyTo(replaceTags($replyTo, '', '', $link), replaceTags($fromName, '', '', $link));
    $mail->Sender       = replaceTags($returnPath, '', '', $link);
    $mail->Subject      = $subject;
    $mail->MessageDate  = PHPMailer::rfcDate();
    $mail->Encoding     = $encoding;
    $mail->ContentType  = $contentType;
    $mail->CharSet      = $charset;
    $mail->Priority     = $priority;

    // Split the header properties by ": "
    for ($i = 0; $i < sizeof($headers); $i++) {
        $h = explode(": ", $headers[$i]);
        // $h = explode(": ", htmlentities($headers[$i]));
        array_push($headerProperties, $h[1]);
    }

    list($messageID, $XMailer, $autoSubmit, $XAutoResponse, $XAbuse) = $headerProperties;

    $sendPerRotation = $BCCnumber * count($servers);
    $result = count($recipients) / $sendPerRotation;
    $nbrRotations = ($result != floor($result)) ? ceil($result) : $result;
    // $nbrRotations = count($servers) > 1 ? (($result != floor($result)) ? ceil($result) : $result) : 1;

    $start_index = 0;

    // Perform the rotation
    for ($i = 0; $i < $nbrRotations; $i++) {
        // Loop through the servers
        foreach ($servers as $server) {
            list($host, $port, $smtpSecure, $username, $password) = explode(":", $server);

            // Configure SMTP
            $mail->isSMTP();                                                                                            // Send using SMTP
            $mail->SMTPDebug        = 0;                                                                                // Enable verbose debug output
            $mail->SMTPDebug        = SMTP::DEBUG_SERVER;                                                            // Enable verbose debug output
            $mail->SMTPKeepAlive    = true;                                                                             // Keep the SMTP connection open after each message
            $mail->SMTPAuth         = true;                                                                             // Enable SMTP authentication
            $mail->Host             = $host;                                                                            // Set the SMTP server to send through
            $mail->Port             = $port;                                                                            // TCP port to connect to
            $mail->SMTPSecure       = strtolower($smtpSecure);                                                          // Enable implicit TLS/SSL encryption
            $mail->Username         = $username;                                                                        // SMTP username
            $mail->Password         = $password;                                                                        // SMTP password
            $mail->Priority         = $priority;                                                                        // Email priority
            $mail->MessageID        = replaceTags($messageID, $username, '', $link);                                    // An ID to be used in the Message-ID header
            $mail->XMailer          = replaceTags($XMailer, $username, '', $link);                                      // X-Mailer header
            $mail->addCustomHeader("Auto-Submitted", replaceTags($autoSubmit, $username, '', $link));
            $mail->addCustomHeader("X-Auto-Response-Suppress", replaceTags($XAutoResponse, $username, '', $link));
            $mail->addCustomHeader("X-Abuse", replaceTags($XAbuse, $username, '', $link));

            // Remove empty lines
            $recipients = \array_filter($recipients, static function ($element) {
                return $element !== "";
                //                   ↑
                // Array value which you want to delete
            });

            // Loop through the recipients and send emails
            for ($j = $start_index; $j < ($start_index + $BCCnumber); $j++) {
                if (isset($_POST['cancelled']) && $_POST['cancelled'] === 'true') {
                    // If the "Cancel" button has been clicked, stop the loop
                    break;
                }

                $recipient = $recipients[$j];

                //Recipients
                $mail->addBCC($recipient);                                      //Name is optional

                //Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');                //Add attachments

                //Content
                $mail->msgHTML(replaceTags($creative, $username, $recipient, $link));       // Create a message body from an HTML string



                // Send the email using PHPMailer
                if ($mail->Send()) {
                    $response = 'Mail Sent successfully';
                } else {
                    $response = 'Error: ' . $mail->ErrorInfo;
                }

                $mail->clearAllRecipients();
                // Send the response back to the client-side code using AJAX
                echo "<pre>";
                echo json_encode([
                    'recipient' => $recipient,
                    'response' => $response
                ]);
                echo "</pre>";

                // Flush the output buffer to send the response immediately
                ob_flush();
                flush();

                if (!isset($recipient)) {
                    break;
                }
            }


            // Increase the $start_index by $BCCnumber
            $start_index += $BCCnumber;
            // }
        }
    }
}

// Call the function with the form data sent from AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendEmailsUsingPHPMailer($_POST);
}
