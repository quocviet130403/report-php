<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$method_key=$_POST['method_id'];
echo $sql = "SELECT * FROM method_content WHERE method_id='".$method_key."' AND lang_code='".$_SESSION['trans']."' ";
// ...

if ($rss = mysqli_query($conn, $sql)) {
    // Initialize an empty array to store all records
    $response = array();

    while ($row = mysqli_fetch_assoc($rss)) {
        // Append each row to the response array
        $response[] = $row;
        print_r($row);
        
    }
    echo "<pre>";
print_r($row);
print_r($response);
echo "</pre>";
die("G");
    // Encode the response array as JSON
    $jsonResponse = json_encode($response);

    // Echo the JSON response back to the AJAX call
    echo $jsonResponse;
} else {
    echo $conn->error;
}

// Close the connection when you're done
$conn->close();


?>