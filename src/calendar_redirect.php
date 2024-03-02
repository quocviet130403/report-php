<?php
session_start();

require_once('config.php');
require_once('database.php');
require_once('translation.php');
require_once('google_calendar_api.php');

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

$Database = new Database();

$trans = new Translation($_SESSION['trans']);

if(isset($_SESSION['account-type']) && $_SESSION['account-type'] == 'user'){
    $user_id = $_SESSION['account-id'];

    if(isset($_GET['code'])){
        $auth_code = $_GET['code'];

        try {
            $capi = new GoogleCalendarApi();
            
            // Get the access token 
            $data = $capi->GetAccessToken(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_REDIRECT_URL, GOOGLE_CLIENT_SECRET, $auth_code);
            $access_token = $data['access_token'];
    
            //Storing auth code to database
            $info = array(array('google_auth_code', $access_token));

            $Database->update_data($info, 'user_id', $_SESSION['account-id'], 'user');
            echo $trans->phrase('calendar_redirect_phrase4');
        }
        catch(Exception $e) {
            echo $trans->phrase('calendar_redirect_phrase3');
        }
    }
    else{
        echo $trans->phrase('calendar_redirect_phrase2');
    }
}
else{
    echo $trans->phrase('calendar_redirect_phrase1');
}
?>
<script>
    setTimeout(function(){
        window.location.href="/user/index.php?route=profile";
    }, 1000);
</script>