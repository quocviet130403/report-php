<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define("DB_NAME", "nogdno03");
define("DB_USER", "nogdno03");
define("DB_PASS", "maije-ombot-diet-65-Pren");
define("DB_HOST", "nogdno03.mysql.domeneshop.no");

// Create a connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// 
// 
echo $sql = "UPDATE user SET user_profile='' WHERE user_id='".$_POST['userID']."'  ";

if ($rss = mysqli_query($conn, $sql)) {
}
else{
    echo $conn->error;
}
// Close the connection when you're done
$conn->close();

?>