<?php

$dbServername = "localhost";
$dbName = readline("What is the name of your database?  ");
$dbUsername = readline("What is your username?  ");
$dbPassword = readline("What is your password?  ");

// $dbUsername = "db_user";
// $dbPassword = "password";
// $dbName = "imaginary_db";

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
echo mysqli_connect_error();
