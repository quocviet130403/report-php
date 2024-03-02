<?php
session_start();


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


if($_REQUEST['act']=='delete_ticket'){
    $sql1="DELETE FROM ticket WHERE ticket_id='".$_POST['ticketID']."' ";
    if($rss1=mysqli_query($conn,$sql1)){
    echo "1";
    }
}
else{
   
}

// echo "gg";
?>