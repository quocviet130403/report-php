<?php
session_start();
require_once('config.php');
require_once('database.php');
require_once('email_sender.php');
require_once('translation.php');

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
$email_sender = new EmailSender();


//Checking ticket follow-up and taking specified action
$tickets = $Database->get_multiple_data('ticket_status', "'process'", 'ticket');
if($tickets){
    foreach($tickets as $ticket){
        $deadline = false;

        $ticket_deadline = $Database->get_data('ticket_id', $ticket['ticket_id'], 'ticket_deadline', true);
        if($ticket_deadline && !$ticket_deadline['emailed'] && strtotime($ticket_deadline['end_date']) < time()){
            $deadline = true;
            $info = array(array('emailed', 1));
            $Database->update_data($info, 'ticket_id', $ticket['ticket_id'], 'ticket_deadline');
        }

        $question_deadlines = $Database->get_multiple_data('ticket_id', $ticket['ticket_id'], 'question_deadline');
        if($question_deadlines){
            foreach($question_deadlines as $question_deadline){
                if(!$question_deadline['emailed'] && strtotime($question_deadline['end_date']) < time()){
                    $deadline = true;
                    
                    $sql = "UPDATE question_deadline SET emailed=1 WHERE ticket_id={$ticket['ticket_id']} AND question_id={$question_deadline['question_id']}";
                    $update = $Database->get_connection()->prepare($sql);
                    $update->execute();
                }
            }
        }

        if($deadline){
            $ticket_user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);
            
            //Send Email for this ticket.
            $ticket_url = SITE_URL."/user/index.php?route=ticket&id={$ticket['ticket_id']}";
            $attributes = array(
                array('site_url', SITE_URL),
                array('site_name', NAME),
                array('ticket_url', $ticket_url),
                array('site_email', SITE_EMAIL)
            );
    
            $body = $email_sender->load_template('ticket_follow_up_reminder', $attributes);
            $email_sender->send_mail($ticket_user['ueser_email'], $trans->phrase('email_title_phrase5'), $body);
        }
    }
}

//Checking company and taking specified action.
$companies = $Database->get_multiple_data('company_status', "'active'", 'company');
if($companies){
    foreach($companies as $company){
        if(time() > strtotime($company['company_expire'])){
            //suspend company
            $info = array(array('company_status', 'suspended'));
            $Database->update_data($info, 'company_id', $company['company_id'], 'company');
        
            //send suspend reminder
            $attributes = array(
                array('site_url', SITE_URL),
                array('site_name', NAME),
                array('user_name', $company['company_name']),
                array('user_email', $company['company_email']),
                array('user_id', $company['company_id']),
                array('site_email', SITE_EMAIL),
                array('user_count', $company['company_size'])
            );
  
            $body = $email_sender->load_template('company_expire', $attributes);
            $email_sender->send_mail($company['company_email'], $trans->phrase('email_title_phrase2'), $body);
        }
        else if(time() > strtotime($company['company_expire']." - ".COMPANY_RENEW_TIME)){
            //send reminder
            $attributes = array(
                array('site_url', SITE_URL),
                array('site_name', NAME),
                array('user_name', $company['company_name']),
                array('user_email', $company['company_email']),
                array('user_id', $company['company_id']),
                array('site_email', SITE_EMAIL),
                array('user_count', $company['company_size']),
                array('expire_date', $company['company_expire'])
            );

            $body = $email_sender->load_template('company_expire_reminder', $attributes);
            $email_sender->send_mail($company['company_email'], $trans->phrase('email_title_phrase1'), $body);
        }
    }
}
?>