<?php
    //General settings
    define("NAME", "Nogd.no");
    define("APP_NAME", "Nøgd");
    define("SITE_URL", "https://beta.nogd.no");
    define("SITE_EMAIL", "backoffice@nogd.no");
    define("SITE_CURRENCY", "kr");
    define("SITE_CURRENCY_SYMBOL", "Kr");
    define("COMPANY_RENEW_TIME", '5 days');

//    Database Configuration
   define("DB_NAME", "nogdno03");
   define("DB_USER", "nogdno03");
   define("DB_PASS", "Daue-tyte-Terge-1234-skake");
   define("DB_HOST", "nogdno03.mysql.domeneshop.no");


    // SMTP email configuration
   define("SMTP_HOST", "smtp.eu.mailgun.org");
   define("SMTP_PORT", "587");
   define("SMTP_USER", "postmaster@beta.nogd.no");
    define("SMTP_PASS", "178e918baf0bb07c480fd330e9a17b16-ca9eeb88-2d14532f");
   define("SENDER_EMAIL", "postmaster@beta.nogd.no");
   define("SENDER_NAME", "Semje Software AS");

    //Google authentication credential
    define('GOOGLE_CLIENT_ID', '1070937594826-e2kduru2d1cff6972ko343hpm2f1nadk.apps.googleusercontent.com'); //Google App Client Id
    define('GOOGLE_CLIENT_SECRET', 'MhL8bghYmTLTKiXD7cpSBvT7MhL8bghYmTLTKiXD7cpSBvT7'); //Google App Client Secret
    define('GOOGLE_CLIENT_REDIRECT_URL', SITE_URL.'/calendar_redirect.php'); //Google App Redirect Url
    


?>