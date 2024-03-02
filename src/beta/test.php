<?php

session_start();

require_once('config.php');

require_once('database.php');

require_once('email_sender.php');

require_once('translation.php');

require_once('google_calendar_api.php');

require_once('common_methods.php');

require_once "vendor/autoload.php";

require ('report_permissions.php');

use Twilio\Rest\Client;

$trans = new Translation($_SESSION['trans']);

$Database = new Database();

// var_dump($_SESSION['report-format-id']);die;
// $sql = "SELECT * FROM mlreport_format_contentd WHERE report_format_id=" .$_SESSION['report-format-id'];
// $report_format = $Database->get_connection()->prepare($sql);
// $report_format->execute();
// $report_format = $report_format->fetch(PDO::FETCH_ASSOC);
// var_dump($report_format);die;

    

// $ticket_id = $Database->write_data($infoAll, 'ticket', false, true);
$user_data = $Database->get_data('ticket_id', 405, 'ticket', true);
var_dump($user_data);die;

$sql = "SELECT * FROM creport WHERE ticket_id=" . 405;

$report_format = $Database->get_connection()->prepare($sql);

$report_format->execute();
$report_format = $report_format->fetch(PDO::FETCH_ASSOC);
$report = json_decode($report_format['report_content'], true);
var_dump($report_format);die;
var_dump($report['free_text2']);die;
            