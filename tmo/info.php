<?php

$servername = "localhost";
$username = "root";
$password = "";
$db = "xdjb8jcr_jcr_new";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT user_id, status FROM `takemeout`";
//var_dump($sql);
$result = $conn->query($sql);
$user = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "0 results";
}

echo json_encode($users);