<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any domain

// set php error reporting to none
error_reporting(0);
// set maximum execution time to no limit
set_time_limit(0);
// set memory limit to no limit
ini_set('memory_limit', '-1');

function get_emails()
{
?>

    <!DOCTYPE html>
    <html lang="en" id="display">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../stylesheets/bootstrap.css" rel="stylesheet" />
        <title>Document</title>
        <script src="script.js"></script>
        <script>
            var html = document.getElementById('display');
        </script>
    </head>

    <body id="body">

        <?php
        if (!isset($_GET['server']) || !isset($_GET['port']) || !isset($_GET['username']) || !isset($_GET['password'])) { ?>
            <div class="d-flex align-items-center justify-content-center" id="readyDiv" style="height: 500px">
                <div class="fs-4 text-secondary">Please provide all the required parameters.</div>
            </div>
        <?php exit();
        }

        $server = $_GET['server'];
        $port = $_GET['port'];
        $username = $_GET['username'];
        $password = $_GET['password'];

        $count = 100;

        if (!function_exists('imap_open')) { ?>
            <div class="d-flex align-items-center justify-content-center" id="readyDiv" style="height: 500px">
                <div class="fs-4">IMAP is not configured.</div>
            </div>
            <?php
            exit();
        } else {

            // Connecting GMX server with IMAP
            // $connection = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', 'jeanmartinklgdgd', 'ufhuoddvedyqqayv') or die('Cannot connect to Gmail: ' . imap_last_error());
            $mailbox = "{{$server}:{$port}/imap/ssl}INBOX";
            $connection = imap_open($mailbox, $username, $password);

            if (!$connection) { ?>
                <div class="d-flex align-items-center justify-content-center" id="readyDiv" style="height: 500px">
                    <div class="fs-4 text-danger">Cannot connect to mailbox: <?php echo imap_errors()[0] ?></div>
                </div>
            <?php
                exit();
            }

            $emailData_1 = [];
            $emailData_2 = [];
            $emailData_3 = [];
            $emailData_4 = [];

            // Search email with multiple criteria
            $emailData_1 = imap_search($connection, 'FROM "GMX Mailer Daemon"', SE_UID);
            $emailData_2 = imap_search($connection, 'FROM "Mail Delivery Subsystem"', SE_UID);
            $emailData_3 = imap_search($connection, 'FROM "Postmaster"', SE_UID);
            $emailData_3 = imap_search($connection, 'FROM "Deliverability Help"', SE_UID);

            $emailData = array_merge($emailData_1, $emailData_2, $emailData_3, $emailData_4);

            if (!empty($emailData)) {
            ?>
                <table id="bouncedTable" class="table table-hover table-bordered text-center m-0" style="overflow: auto;">
                    <thead class="table-success position-sticky" style="top: 0; width: 100%">
                        <tr>
                            <th class="text-start" style="width: 6%">#</th>
                            <th style="width: 8%">ID</th>
                            <!-- <th style="width: 8%">Seq. numb</th> -->
                            <th style="width: 30%">From</th>
                            <th style="width: 30%">Email</th>
                            <th>Date</th>
                            <th style="width: 8%">
                                <input type="checkbox" id="select-all" class="form-check-input" oninput="selectElements(event)" />
                            </th>
                        </tr>
                    </thead>
                    <tbody id="emails_container">
                        <?php
                        $i = 0;
                        foreach ($emailData as $emailIdent) {

                            // if ($i == $count) {
                            //     break;
                            // }

                            // $message_id = imap_uid($connection, $emailIdent);
                            $messageSeqNumber = imap_msgno($connection, $emailIdent);
                            $overview = imap_fetch_overview($connection, $emailIdent, 0);
                            $message = imap_fetchbody($connection, $emailIdent, 1.1);
                            if ($message == '') {
                                $message = (imap_fetchbody($connection, $emailIdent, 1));
                            }

                            if (/*$emailStructure && */$message) {
                                // Parse the email body to extract the email address
                                preg_match('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}/', $message, $matches);

                                if (!empty($matches)) {
                                    $emailAddress = $matches[0];
                                    // $emailAddress = 'Email address found: ' . $emailAddress;
                                } else {
                                    // bypass this iteration
                                    continue;
                                    $emailAddress = 'Email address not found in the body.';
                                }
                            } else {
                                // bypass this iteration
                                continue;
                                $emailAddress = 'Unable to fetch the email or its structure.';
                            }

                            $messageExcerpt = substr($message, 0, 150);
                            $partialMessage = trim(quoted_printable_decode($messageExcerpt));
                            $date = date("d F, Y", strtotime($overview[0]->date));
                        ?>
                            <tr class="emailRow">
                                <th scope="row" class="text-start"><?php echo $i + 1; ?></th>
                                <th class="messageUID"><?php echo $emailIdent; ?></th>
                                <!-- <th><?php echo $messageSeqNumber; ?></th> -->
                                <td><?php echo $username; ?></td>
                                <td class="bouncedEmails"><?php echo $emailAddress; ?></td>
                                <td><?php echo $date; ?></td>
                                <td>
                                    <input type="checkbox" class="form-check-input select-email" oninput="updateCheckboxs()" />
                                </td>
                            </tr>
                            <script>
                                // bouncedEmailsArray.push("<?php echo $emailAddress; ?>");

                                html.scrollTo({
                                    top: html.scrollHeight,
                                    left: 0,
                                    behavior: "instant",
                                })
                            </script>
                        <?php

                            // Flush the output buffer to send the data immediately
                            ob_flush();
                            flush();

                            $i++;
                        } // End foreach
                        ?>
                    </tbody>
                </table>
            <?php
            } else { ?>
                <div class="d-flex align-items-center justify-content-center" id="readyDiv" style="height: 500px">
                    <div class="fs-4">No emails found.</div>
                </div> <?php
                    } ?>
    </body>

    </html> <?php

            imap_close($connection);
        } ?>


<?php
}

function delete_emails($data)
{
    // set php error reporting to normal
    // error_reporting(E_ALL);
    // Set the JSON header
    header("Content-type: application/json");

    if (!isset($data['server']) || !isset($data['port']) || !isset($data['username']) || !isset($data['password']) || !isset($data['messageUIDs'])) {
        $response['status'] = 'Error';
        $response['message'] = 'Please provide all the required parameters.';
        echo json_encode($response);
        exit();
    }

    $response = array();

    $server = $data['server'];
    $port = $data['port'];
    $username = $data['username'];
    $password = $data['password'];
    $messageUIDs = $data['messageUIDs'];

    $messageCount = count(explode(',', $messageUIDs));

    if (!function_exists('imap_open')) {
        $response['status'] = 'Error';
        $response['message'] = 'IMAP is not configured.';
        echo json_encode($response);
        exit();
    } else {

        // Connecting GMX server with IMAP
        // $connection = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', 'jeanmartinklgdgd', 'ufhuoddvedyqqayv') or die('Cannot connect to Gmail: ' . imap_last_error());
        $mailbox = "{{$server}:{$port}/imap/ssl}INBOX";
        $connection = imap_open($mailbox, $username, $password);

        if (!$connection) {
            $response['status'] = 'Error';
            $response['message'] = 'Cannot connect to mailbox: ' . imap_errors()[0];
            echo json_encode($response);
            exit();
        }

        imap_delete($connection, $messageUIDs, FT_UID);
        imap_close($connection, CL_EXPUNGE);

        $response['status'] = 'Success';
        $response['message'] = "{$messageCount} emails deleted successfully.";
        echo json_encode($response);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    get_emails($_GET);
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    delete_emails($_POST);
}
?>