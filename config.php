<?php
$dbUser = "root";
$dbPass = "";
$dbHost = "localhost";
$dbDatabase = "foodecomm";

$dbConn = mysqli_connect($dbHost,$dbUser,$dbPass,$dbDatabase);

if(!$dbConn) {
    die ("Database not connected");
}
