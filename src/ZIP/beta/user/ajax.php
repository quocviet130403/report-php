<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once('../../config.php');

require_once('../../database.php');
$Database = new Database();
// $servername = "nogdno01.mysql.domeneshop.no";
// $username = "nogdno01";
// $password = "maije-ombot-diet-65-Pren";
// $dbname = "nogdno01";


// $connection = new mysqli($servername, $username, $password, $dbname);


// if ($connection->connect_error) {
//     die("Connection failed: " . $connection->connect_error);
// }


if (isset($_POST['sign']) && $_POST['sign'] == 'delete_response') {

    $responder_id = $_POST['responder_id'];

	// $deleted = $Database->delete_data('responder_id', $responder_id, 'tbl_ticket_responder ');

    // $deleted = $Database->delete_data('responder_id', $responder_id, 'responder_ticket_data');

    // $sql1="DELETE FROM `tbl_ticket_responder` WHERE responder_id='".$responder_id."'";
    // $rss1 = $Database->get_connection()->prepare($sql1);
    // $rssprep1->execute();

    // $sql2="DELETE FROM `responder_ticket_data` WHERE responder_id='".$responder_id."'";
    // $rss2 = $Database->get_connection()->prepare($sql2);
    // $rssprep2->execute();


    echo $sql1 = "DELETE FROM tbl_ticket_responder WHERE responder_id = '".$responder_id."'";
    $rss1 = mysqli_query($connection,$sql1);
    if($rss1){
        echo "Deleted";
    }
    else{
        echo $connection->error;
    }

    // $sql2 = "DELETE FROM `responder_ticket_data` WHERE responder_id = '".$responder_id."'";
    // $rss2 = mysqli_query($connection,$sql2);
    
    



}
?>