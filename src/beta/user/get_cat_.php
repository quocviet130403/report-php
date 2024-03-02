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



// Initialize an array to store the results
$response = [];

// Query to fetch category_name
$sql = "SELECT category_name FROM category WHERE category_id='" . $_GET['cat_id'] . "' ";
if ($result = $conn->query($sql)) {
    $category = $result->fetch_assoc();
    $response['category'] = $category['category_name'];
    $result->close();
}



// Close the connection
$conn->close();

// Encode the response array as JSON and send it
header('Content-Type: application/json');
echo json_encode($response);

?>