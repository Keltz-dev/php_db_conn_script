<?php

$dbServername = "localhost";
$dbUsername = "db_user";
$dbPassword = "password";
$dbName = "imaginary_db";

// $dbServername = "localhost";
// $dbUsername = readline("What is your username?  ");
// $dbPassword = readline("What is the corresponding password?  ");
// $dbName = readline("What is the name of your database?  ");

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
echo mysqli_connect_error();
