<?php
ob_start();
session_start();
require_once('../config.php');
require_once('../imports.php');
require_once('../translation.php');
require_once('../database.php');

$Database = new Database();

if (!isset($_SESSION['trans'])) {
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if ($default_language) {
        $_SESSION['trans'] = $default_language['lang_code'];
    } else {
        $_SESSION['trans'] = 'en';
    }
}

$trans = new Translation($_SESSION['trans']);

     //echo $_SESSION['account-type'];
              // echo $_SESSION['account-id'];exit;

if (!isset($_SESSION['account-type']) || !isset($_SESSION['account-id'])) {
    header("Location: ../index.php");
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo NAME; ?></title>

    <!--Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" href="<?php echo SITE_URL ?>/favicon.ico">
    <!-- Fonts -->
    <link rel="stylesheet" href="<?php echo FONT_PACIFICO; ?>">
    <!-- Style Sheets -->
    <link rel="stylesheet" href="<?php echo FONTAWESOME; ?>">
    <link rel="stylesheet" href="<?php echo JQUERY_UI_THEME; ?>">
   <link rel="stylesheet" href="<?php echo BOOTSTRAP_CSS; ?>"> 
    <link rel="stylesheet" href="<?php echo CHART_CSS; ?>">


    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/style.bundle.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/table.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/custom-datatable.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/user_header.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/user.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/custom-new.css">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" ></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

<style>
    .navbar-nav {
        flex-direction: row;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }

    .navbar-nav .nav-item {
        margin: 0;
    }

    .navbar-nav .nav-link {
        height: 2.5rem;
        position: relative;
        display: block;
        padding: 0.5rem 1rem !important;
    }
    
    .navbar-badge {
        font-size: .6rem !important;
        font-weight: 500 !important;
        padding: 2px 4px !important;
        position: absolute;
        right: 5px;
        top: 5px;
    }

    .user-image {
        border-radius: 50%;
        float: left;
        height: 2.1rem;
        margin-right: 10px;
        margin-top: -2px;
        width: 2.1rem;
        padding: 0 !important;
        margin: 0 !important;
        margin-top: -7px !important;
        box-shadow: 0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23)!important;
    }
/*
    .custom-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 35px !important;
    }

    .notification-dropdown-menu {
        position: absolute;
        top: 100%;
        left: -105px !important;
    }
    */

    .btn i {
        padding-right: 0 !important;
    }
</style>
</head>

<body>
    <div id="translation" <?php
                            $js_trans = $trans->get_trans();
                            foreach ($js_trans as $trans_key => $trans_value) {
                                if (strpos($trans_key, 'user_js_phrase') !== false) {
                                    echo "data-" . $trans_key . "=\"" . $trans_value . "\" ";
                                }
                            }
                            ?>>
     <!--    <div id="user-full-screen">
            <button id="user-full-screen-close" class="btn btn-outline-danger btn-sm screen-close"><i class="fas fa-times"></i></button>
            <div id="user-full-screen-loader" class="screen-loader"><i class="fas fa-spinner fa-spin"></i></div>
            <div id="user-full-screen-content"></div>
        </div> -->

    <div class="header_logo" >
 <a href="javascipt:void(0);" class="logo">
    <img src="<?php echo SITE_URL ?>/images/logo-transparent.png" alt="Logo">
</a>
</div> 
  <div class="main-body">
  <div class="container-fluid">

<div class="row no-gutters">

<div class="col-sm-4 col-md-3 col-xl-2 mp">          

<header class="main-header my-header test">

<div class="top-content">

<?php require_once('menu.php'); ?>


</div>
</header>

</div>





