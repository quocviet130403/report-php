<?php
    require_once('config.php');
    require_once('database.php');
    require_once('imports.php');

    if(!isset($_SESSION['trans'])){
        $Database = new Database();
        $default_language = $Database->get_data('lang_default', 1, 'language', true);
        if($default_language){
            $_SESSION['trans'] = $default_language['lang_code'];
        }
        else{
            $_SESSION['trans'] = 'en';
        }
    }

    require_once('config.php');
    require_once('imports.php');
    require_once('translation.php');
    require_once('database.php');

    $Database = new Database();

    $trans = new Translation($_SESSION['trans']);

    $redirect = true;
    if(basename($_SERVER['SCRIPT_FILENAME']) == 'tos.php')
        $redirect = false;

    if(isset($_SESSION['account-type']) && isset($_SESSION['account-id']) && $redirect && ($_SESSION['is_verified'])){
        if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')
            header("Location: user/index.php?route=admin_profile");
        if($_SESSION['account-type'] == 'consultant')
            header("Location: user/index.php?route=consultant_profile");
        if($_SESSION['account-type'] == 'company')
            header("Location: user/index.php?route=company_profile");
        if($_SESSION['account-type'] == 'user')
            header("Location: user/index.php?route=profile");
        die();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo NAME; ?> </title>

        <!--Meta Tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">  

        <link rel="icon" href="<?php echo SITE_URL; ?>/favicon.ico">
        <!-- Fonts -->
        <link rel="stylesheet" href="<?php echo FONT_PACIFICO; ?>">

        <!-- Style Sheets -->
        <link rel="stylesheet" href="<?php echo BOOTSTRAP_CSS; ?>">
        <link rel="stylesheet" href="<?php echo FONTAWESOME; ?>">
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/header.css">
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/main.css">
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/custom.css">
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/custom-new.css">
        
    </head>
    <body>
    <div id="translation"
        <?php
            $js_trans = $trans->get_trans();
            foreach($js_trans as $trans_key => $trans_value){
                if(strpos($trans_key, 'main_js_phrase') !== false){
                    echo "data-".$trans_key."=\"".$trans_value."\" ";
                }
            }
        ?>
        >
    </div>
        <div class='container-fluid super-container'>