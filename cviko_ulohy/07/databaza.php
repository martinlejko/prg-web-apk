<?php
$host = "localhost";
$username = "56704379";
$password = "i3AWxLj8";
$dbname = "stud_56704379";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$mysqli->close();
?>
