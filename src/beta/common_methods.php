<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('config.php');
require_once('database.php');
require_once('email_sender.php');
require_once('translation.php');

if (!isset($_SESSION['trans'])) {
    $database = new Database();
    $default_language = $database->get_data('lang_default', 1, 'language', true);
    if ($default_language) {
        $_SESSION['trans'] = $default_language['lang_code'];
        $_SESSION['lang_id'] = $default_language['lang_id'];
    } else {
        $_SESSION['trans'] = 'en';
        $_SESSION['lang_id'] = 1;
    }
}

$database = new Database();
$trans = new Translation($_SESSION['trans']);
$email_sender = new EmailSender();


class CommonMethods
{
    private $database = null;


    function send_email_to_backoffice($ticketId)
    {

        $database = new Database();
        $trans = new Translation($_SESSION['trans']);
        $email_sender = new EmailSender();

        $ticket = $database->get_data('ticket_id', $ticketId, 'ticket', true);
        if ($ticket) {
            $user = $database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);
            $user_company = $database->get_data('company_id', $user['user_company_id'], 'company', true);
            $adminEmail = $database->get_backoffice_email();

            $ticket_close_time = '';

            if (isset($ticket['ticket_close_time'])) {
                $ticket_close_time = date(
                    'Y-M-d h:i:s',
                    strtotime($ticket['ticket_close_time'])
                );
            }

            $attributes = array(
                array('site_url', SITE_URL),
                array('site_name', NAME),
                array('email_to_name', 'Admin'),
                array('ticket_id', $ticketId),
                array('ticket_name', $ticket['ticket_name']),
                array('user_name', $user['user_name']),
                array('user_company', $user_company['company_name']),
                array('ticket_close_time', $ticket_close_time)
            );

            //$email_to = "md.kzaman19@gmail.com";
            $email_to = $adminEmail;
            $body = $email_sender->load_template(
                'ticket_close_info_backoffice',
                $attributes
            );
            $isSendEmail = $email_sender->send_mail(
                $email_to,
                $trans->phrase('email_title_ticket_close'),
                $body
            );
            return $isSendEmail;
        } else {
            return false;
        }
    }


    function send_email_tousers_info_report_gen()
    {

        $database = new Database();
        $trans = new Translation($_SESSION['trans']);
        $email_sender = new EmailSender();

        $isSendEmail = "";

        $query = "SELECT ticket_id, ticket_name, ticket_company_id, ticket_user_id, last_modified_time, report_gen_time FROM ticket where report_gen_time is not null and datediff(CURRENT_TIME, report_gen_time)=14";

        $data_list = $database->get_data_by_query($query);
        if ($data_list) {
            foreach ($data_list as $ticket) {
                // echo $curr_data['ticket_id'];
                $user = $database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);


                $attributes = array(
                    array('site_url', SITE_URL),
                    array('site_name', NAME),
                    array('user_name', $user['user_name']),
                    array('ticket_id', $ticket['ticket_id']),
                    array('ticket_name', $ticket['ticket_name']),
                    array('last_modified_date', date('Y-m-d', strtotime($ticket['last_modified_time'])))
                );

                $email_to = $user['user_email'];
                $body = $email_sender->load_template(
                    'ticket_report_gen_info_user',
                    $attributes
                );
                $isSendEmail = $email_sender->send_mail(
                    $email_to,
                    $trans->phrase('email_title_ticket_report_gen'),
                    $body
                );
            }
        }
        return $isSendEmail;
    }


    function send_email_tousers_incomplete_ticket()
    {

        $database = new Database();
        $trans = new Translation($_SESSION['trans']);
        $email_sender = new EmailSender();

        $isSendEmail = "";

        $query = "SELECT ticket_id, ticket_name, ticket_company_id,
                 ticket_user_id, last_modified_time, report_gen_time 
                 FROM ticket where last_modified_time is not null 
                    and (datediff(CURRENT_TIME, last_modified_time)%7)=0";

        $data_list = $database->get_data_by_query($query);
        if ($data_list) {
            foreach ($data_list as $ticket) {
                // echo $curr_data['ticket_id'];
                $user = $database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);


                $attributes = array(
                    array('site_url', SITE_URL),
                    array('site_name', NAME),
                    array('user_name', $user['user_name']),
                    array('ticket_id', $ticket['ticket_id']),
                    array('ticket_name', $ticket['ticket_name']),
                    array('last_modified_date', date('Y-m-d', strtotime($ticket['last_modified_time'])))
                );

                $email_to = $user['user_email'];
                $body = $email_sender->load_template(
                    'ticket_incomplete_ticket_user',
                    $attributes
                );
                $isSendEmail = $email_sender->send_mail(
                    $email_to,
                    $trans->phrase('email_title_ticket_incomplete'),
                    $body
                );
            }
        }
        return $isSendEmail;
    }

    function send_email_feeback_reminder()
    {

        $database = new Database();
        $trans = new Translation($_SESSION['trans']);
        $email_sender = new EmailSender();

        $isSendEmail = "";

        $query = "SELECT ticket_id, ticket_name, ticket_company_id,
                 ticket_user_id, ticket_close_time, report_gen_time 
                 FROM ticket where report_gen_time is not null 
                    and review_status!=1
                    and datediff(CURRENT_TIME, report_gen_time)=30";

        $data_list = $database->get_data_by_query($query);
        if ($data_list) {
            foreach ($data_list as $ticket) {
                // echo $curr_data['ticket_id'];
                $user = $database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);

                $attributes = array(
                    array('site_url', SITE_URL),
                    array('site_name', NAME),
                    array('user_name', $user['user_name']),
                    array('ticket_id', $ticket['ticket_id']),
                    array('ticket_name', $ticket['ticket_name']),
                    array(
                        'ticket_close_date',
                        date('Y-m-d', strtotime($ticket['ticket_close_time']))
                    )
                );

                $email_to = $user['user_email'];
                $body = $email_sender->load_template(
                    'ticket_feedback_reminder_user',
                    $attributes
                );
                $isSendEmail = $email_sender->send_mail(
                    $email_to,
                    $trans->phrase('email_title_ticket_feedback_closed_ticket'),
                    $body
                );
            }
        }
        return $isSendEmail;
    }

}
