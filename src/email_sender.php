<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('config.php');
require_once('vendor/PHPMailer/src/Exception.php');
require_once('vendor/PHPMailer/src/PHPMailer.php');
require_once('vendor/PHPMailer/src/SMTP.php');
require_once('translation.php');

class EmailSender{
    private $smtp_host;
    private $smtp_port;
    private $smtp_user;
    private $smtp_pass;
    private $sender_email;
    private $sender_name;
    private $trans;

    function __construct(){
        $this->smtp_host = SMTP_HOST;
        $this->smtp_port = SMTP_PORT;
        $this->smtp_user = SMTP_USER;
        $this->smtp_pass = SMTP_PASS;
        $this->sender_email = SENDER_EMAIL;
        $this->sender_name = SENDER_NAME;
        $this->trans = new Translation($_SESSION['trans']);
    }

    function send_mail($eamil_to, $subject, $body, $cc=false, $bcc=false, $senderName=null){
        $mail = new PHPMailer(true);

        try {
            // Server settings.
            // Since we are using Norwegian characters as æøå, let's use UTF-8
            // encoding. Otherwise we will get transformations like these:
            // Velkommen til NØGD => Velkommen til NÃ˜GD.
            // The reason is that the default charset for PHP mailer is
            // iso-8859-1.
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            // $mail->SMTPDebug = 1;                                       // Enable verbose debug output
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = $this->smtp_host;  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $this->smtp_user;                     // SMTP username
            $mail->Password   = $this->smtp_pass;                               // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = $this->smtp_port;                                    // TCP port to connect to

            //Recipients
            if($senderName){
                $mail->setFrom($this->sender_email, $senderName);
            } else {
                $mail->setFrom($this->sender_email, $this->sender_name);
            }
            $mail->addAddress($eamil_to);               // Name is optional

            if($senderName){
                $mail->addReplyTo($this->sender_email, $senderName);
            } else {
                $mail->addReplyTo($this->sender_email, $this->sender_name);
            }

            $mail->addReplyTo($this->sender_email, $this->sender_name);
            if($cc) $mail->addCC($cc);
            if($bcc) $mail->addBCC($bcc);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $this->trans->phrase('email_sender_phrase1')."{$mail->ErrorInfo}";
        }
    }

    function load_template($template, $attribute=false){
        //loading data from template file
        $data = file_get_contents("email_templates/".$_SESSION['trans']."/".$template.".html");
        $message;
    
        //placing customized messages to template data.
        if($attribute){
            $pattern = array();
            for($x=0; $x < count($attribute); $x++){
                $pattern[$x] = "/{{".$attribute[$x][0]."}}/";
            }
            
            $replace = array();
            for($x=0; $x < count($attribute); $x++){
                $replace[$x] = $attribute[$x][1];
            }
            
            $message = preg_replace($pattern, $replace, $data);
        }
        else{
            $message = $data;
        }
        return $message;
    }
}

?>