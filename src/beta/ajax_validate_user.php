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

$check_user=$_POST['check_user'];
$response = array();
// Define the queries for each table
$query_user = "SELECT user_id FROM user WHERE user_email = ? OR user_phone = ?";
$query_company = "SELECT company_id FROM company WHERE company_email = ? OR company_phone = ?";
$query_consultant = "SELECT consultant_id FROM consultant WHERE consultant_email = ? OR consultant_phone = ?";

// Create a function to fetch a record from a table
function fetchRecord($conn, $query, $check_user) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $check_user, $check_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_array();
    } else {
        return null;
    }
}

// Check each table and build the response array, prioritizing the user table
if ($user_record = fetchRecord($conn, $query_user, $check_user)) {
    $response['status'] = 'success';
    $response['type'] = 'user';
    $response['id'] = $user_record;
} elseif ($company_record = fetchRecord($conn, $query_company, $check_user)) {
    $response['status'] = 'success';
    $response['type'] = 'company';
    $response['id'] = $company_record;
} elseif ($consultant_record = fetchRecord($conn, $query_consultant, $check_user)) {
    $response['status'] = 'success';
    $response['type'] = 'consultant';
    $response['id'] = $consultant_record;
} else {
    $response['status'] = 'not found';
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);


?>