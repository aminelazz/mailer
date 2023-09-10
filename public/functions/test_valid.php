<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'src/VerifyEmail.php';

$ve = new hbattat\VerifyEmail('aminelazzouzi00@gmail.com', 'rish.hinwar@gmx.de');

var_dump($ve->verify());

echo '<pre>';
print_r($ve->get_debug());

// verifyEmail("simo.20052011@outlook.com", "rish.hinwar@gmx.de");
