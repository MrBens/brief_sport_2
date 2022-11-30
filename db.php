<?php
$hostname = "localhost";
$username = "root";
$password = "";
$db = "blog";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$sqli = mysqli_connect($hostname, $username, $password, $db);
mysqli_set_charset($sqli, 'utf8mb4');
