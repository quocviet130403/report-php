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

 $sql = "SELECT * FROM `question_res_content` WHERE question_tips_yes!='' AND lang_code='".$_POST['currLang']."' ";
if ($rss = mysqli_query($conn, $sql)) {

    $response = array();
    while ($row = mysqli_fetch_assoc($rss)) {
        $response[] = $row['question_tips_yes'];  // Append each row to the response array
    }

    // Encode the response array as JSON
    $jsonResponse = json_encode($response);

    // Echo the JSON response back to the AJAX call
    echo $jsonResponse;
}
else{
    echo $conn->error;
}
// Close the connection when you're done
$conn->close();

?>