<?php

$host = "localhost";
$base = "procorr";
$user = "root";
$pw = "";
$port = "3306";
$salt = "procorr_2017";

$db = new mysqli($host, $user, $pw, $base, $port);
mysqli_set_charset($db,"utf8");

?>