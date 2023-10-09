<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-store");
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (not recommended for production)


$eventsToSend = 5; // Change this to the number of events you want to send

// for ($i = 1; $i <= $eventsToSend; $i++) {
//     echo "data: " . json_encode(["time" => date('h:i:s')]) . "\n\n";
//     flush();
//     sleep(2);
// }

echo "data: " . json_encode(["time" => date('h:i:s')]) . "\n\n";
ob_flush();
flush();
sleep(4);

echo "data: " . json_encode(["time" => date('h:i:s')]) . "\n\n";
ob_flush();
flush();


// Send a "close" event to indicate the end
echo "event: close\n";
echo "data: {}\n\n";
flush();

// Close the PHP script
exit;


if (connection_aborted()) exit();
