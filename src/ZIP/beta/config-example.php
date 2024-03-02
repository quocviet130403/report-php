<?php
    //General settings
    define("NAME", ""); 						//Website Name
    define("SITE_URL", "");						//Website URL
    define("SITE_EMAIL", "");					//Primary Email
    define("SITE_CURRENCY", "kr");				//Main Currency
    define("SITE_CURRENCY_SYMBOL", "");			//Currency Symbol (If any)
    define("COMPANY_RENEW_TIME", '5 days');		//Company renewal notification

    //Database Configuration
    define("DB_NAME", "");						//Database Name
    define("DB_USER", "");						//Database User
    define("DB_PASS", "");						//Database Password
    define("DB_HOST", "");						//Database Host

    //SMTP email configuration
    define("SMTP_HOST", "");					//SMTP Host
    define("SMTP_PORT", "");					//SMTP Port (25, 465, or 587)
    define("SMTP_USER", "");					//SMTP User
    define("SMTP_PASS", "");					//SMTP Password
    define("SENDER_EMAIL", "");					//SMTP Sender Email
    define("SENDER_NAME", "");					//SMTP Sender Name

    //Google authentication credential
    define('GOOGLE_CLIENT_ID', ''); 			//Google App Client Id
    define('GOOGLE_CLIENT_SECRET', ''); 		//Google App Client Secret
	
	
	//**********Do not change this************
    define('GOOGLE_CLIENT_REDIRECT_URL', SITE_URL.'/calendar_redirect.php'); //Google App Redirect Url
?>