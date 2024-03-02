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


//Account login

if (isset($_POST['sign']) && $_POST['sign'] == 'account_login') {

    $user_email = $_POST['user_email'];

    $user_pass = $_POST['user_password'];

    
    //Admin account
    if ($user_data = $Database->get_data('admin_email', $user_email, 'admin', true)) {

        $password_verify = password_verify($user_pass, $user_data['admin_password']);

        // $password_verify = $user_pass == $user_data['admin_password'];

        if ($password_verify) {
            if ($user_data['tfa_status']) {
                $_SESSION['is_verified'] = false;
                $_SESSION['user_type_logged_in'] = 'admin';
                $_SESSION['user_id_logged_in'] = $user_data['admin_id'];
                $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

                $info = array(array('pin_code', $pin_code));
                $Database->update_data($info, 'admin_id', $user_data['admin_id'], 'admin');

                if ($user_data['tfa_type'] == 'phone') {

                    $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
                    $token = '6842d11eef8b0b84b906cb942a8073bb';
                    $phone = $user_data['tfa_phone'];
                    $client = new Client($sid, $token);

                    $client->messages->create(
                        $phone, array(
                            "from" => "+17473185226",
                            "body" => "Your 2-factor authentication code is: " . $pin_code
                        )
                    );

                } elseif ($user_data['tfa_type'] == 'email') {

                    $email_sender = new EmailSender();
                    $user_email = $user_data['tfa_email'];
                    $body = "Your 2-factor authentication code is: " . $pin_code;

                    $send = $email_sender->send_mail($user_email, 'Two-Factor Authentication', $body);
                }

            } else {
                $_SESSION['is_verified'] = true;
            }
            if ($_SESSION['is_verified']) {
                if ($user_data['admin_role'] == 'super')

                    $_SESSION['account-type'] = 'super_admin';

                else

                    $_SESSION['account-type'] = 'support_admin';


                $_SESSION['account-id'] = $user_data['admin_id'];


                //Track activity

                $tracker_ip = get_client_ip();

                $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

                if ($address_info && $address_info['status'] == 'success')

                    $tracker_location = $address_info['city'] . "/" . $address_info['country'];

                else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign in';

                $info = array(

                    array('user_id', $user_data['admin_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
            } else {
                echo json_encode(array('message' => 'not_verified', 'type' => $user_data['tfa_type']));
            }

        } else {

            echo $trans->phrase("option_server_phrase1");

        }

    } //Company account
    elseif ($user_data = $Database->get_data('company_email', $user_email, 'company', true)) {

        $password_verify = password_verify($user_pass, $user_data['company_password']);

        // $password_verify = $user_pass == $user_data['company_password'];

        if ($password_verify) {
            if ($user_data['tfa_status']) {
                $_SESSION['is_verified'] = false;
                $_SESSION['user_type_logged_in'] = 'company';
                $_SESSION['user_id_logged_in'] = $user_data['company_id'];
                $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

                $info = array(array('pin_code', $pin_code));
                $Database->update_data($info, 'company_id', $user_data['company_id'], 'company');

                if ($user_data['tfa_type'] == 'phone') {

                    $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
                    $token = '6842d11eef8b0b84b906cb942a8073bb';
                    $phone = $user_data['tfa_phone'];
                    $client = new Client($sid, $token);

                    $client->messages->create(
                        $phone, array(
                            "from" => "+17473185226",
                            "body" => "Your 2-factor authentication code is: " . $pin_code
                        )
                    );

                } elseif ($user_data['tfa_type'] == 'email') {

                    $email_sender = new EmailSender();
                    $user_email = $user_data['tfa_email'];
                    $body = "Your 2-factor authentication code is: " . $pin_code;

                    $send = $email_sender->send_mail($user_email, 'Two-Factor Authentication', $body);
                }

            } else {
                $_SESSION['is_verified'] = true;
            }
            if ($_SESSION['is_verified']) {
                $_SESSION['account-type'] = 'company';


                $_SESSION['account-id'] = $user_data['company_id'];


                //Track activity

                $tracker_ip = get_client_ip();

                $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

                if ($address_info && $address_info['status'] == 'success')

                    $tracker_location = $address_info['city'] . "/" . $address_info['country'];

                else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign in';

                $info = array(

                    array('user_id', $user_data['company_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
            } else {
                echo json_encode(array('message' => 'not_verified', 'type' => $user_data['tfa_type']));
            }

        } else {

            echo $trans->phrase("option_server_phrase1");

        }

    } //User Account
    elseif ($user_data = $Database->get_data('user_email', $user_email, 'user', true)) {

        $password_verify = password_verify($user_pass, $user_data['user_password']);

        // $password_verify = $user_pass == $user_data['user_password'];

        if ($password_verify) {
            if ($user_data['tfa_status']) {
                $_SESSION['is_verified'] = false;
                $_SESSION['user_type_logged_in'] = 'user';
                $_SESSION['user_id_logged_in'] = $user_data['user_id'];
                $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

                $info = array(array('pin_code', $pin_code));
                $Database->update_data($info, 'user_id', $user_data['user_id'], 'user');

                if ($user_data['tfa_type'] == 'phone') {

                    $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
                    $token = '6842d11eef8b0b84b906cb942a8073bb';
                    $phone = $user_data['tfa_phone'];
                    $client = new Client($sid, $token);

                    $client->messages->create(
                        $phone, array(
                            "from" => "+17473185226",
                            "body" => "Your 2-factor authentication code is: " . $pin_code
                        )
                    );

                } elseif ($user_data['tfa_type'] == 'email') {

                    $email_sender = new EmailSender();
                    $user_email = $user_data['tfa_email'];
                    $body = "Your 2-factor authentication code is: " . $pin_code;

                    $send = $email_sender->send_mail($user_email, 'Two-Factor Authentication', $body);
                }

            } else {
                $_SESSION['is_verified'] = true;
            }
            if ($_SESSION['is_verified']) {
                $user_company = $Database->get_data('company_id', $user_data['user_company_id'], 'company', true);


                if ($user_company['company_status'] == 'active') {

                    $_SESSION['account-type'] = 'user';


                    $_SESSION['account-id'] = $user_data['user_id'];


                    //Track activity

                    $tracker_ip = get_client_ip();

                    $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

                    if ($address_info && $address_info['status'] == 'success')

                        $tracker_location = $address_info['city'] . "/" . $address_info['country'];

                    else

                        $tracker_location = "Unknown";


                    $tracker_action = 'Sign in';

                    $info = array(

                        array('user_id', $user_data['user_id']),

                        array('user_role', $_SESSION['account-type']),

                        array('user_ip', $tracker_ip),

                        array('user_location', $tracker_location),

                        array('user_action', $tracker_action)

                    );

                    $Database->write_data($info, 'tracker', false);


                    echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
                } else {
                    echo json_encode(array('message' => 'not_verified', 'type' => $user_data['tfa_type']));
                }
            } else {

                echo $trans->phrase("option_server_phrase71");

            }

        } else {

            echo $trans->phrase("option_server_phrase1");

        }

    } //Consultant Account
    elseif ($user_data = $Database->get_data('consultant_email', $user_email, 'consultant', true)) {

        $password_verify = password_verify($user_pass, $user_data['consultant_password']);

        // $password_verify = $user_pass == $user_data['company_password'];

        if ($password_verify) {
            if ($user_data['tfa_status']) {
                $_SESSION['is_verified'] = false;
                $_SESSION['user_type_logged_in'] = 'consultant';
                $_SESSION['user_id_logged_in'] = $user_data['consultant_id'];
                $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

                $info = array(array('pin_code', $pin_code));
                $Database->update_data($info, 'consultant_id', $user_data['consultant_id'], 'consultant');

                if ($user_data['tfa_type'] == 'phone') {

                    $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
                    $token = '6842d11eef8b0b84b906cb942a8073bb';
                    $phone = $user_data['tfa_phone'];
                    $client = new Client($sid, $token);

                    $client->messages->create(
                        $phone, array(
                            "from" => "+17473185226",
                            "body" => "Your 2-factor authentication code is: " . $pin_code
                        )
                    );

                } elseif ($user_data['tfa_type'] == 'email') {

                    $email_sender = new EmailSender();
                    $user_email = $user_data['tfa_email'];
                    $body = "Your 2-factor authentication code is: " . $pin_code;

                    $send = $email_sender->send_mail($user_email, 'Two-Factor Authentication', $body);
                }

            } else {
                $_SESSION['is_verified'] = true;
            }
            if ($_SESSION['is_verified']) {
                $_SESSION['account-type'] = 'consultant';


                $_SESSION['account-id'] = $user_data['consultant_id'];


                //Track activity

                $tracker_ip = get_client_ip();

                $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

                if ($address_info && $address_info['status'] == 'success')

                    $tracker_location = $address_info['city'] . "/" . $address_info['country'];

                else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign in';

                $info = array(

                    array('user_id', $user_data['consultant_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
            } else {
                echo json_encode(array('message' => 'not_verified', 'type' => $user_data['tfa_type']));
            }

        } else {

            echo $trans->phrase("option_server_phrase1");

        }

    } else {
        echo $trans->phrase("option_server_phrase2");

    }

}

// 2-factor authentication

if (isset($_POST['verify']) && $_POST['verify'] == 'code_pin') {
    $pin_code = $_POST['pin_code'];
    $user_type = $_POST['user_type'];
    $user_id = $_POST['user_id'];

    //Admin account
    if ($user_type == 'admin') {

        $user_data = $Database->get_data('admin_id', $user_id, 'admin', true);
        if ($pic_code_verify = $Database->get_data('pin_code', $pin_code, 'admin', true)) {
            $info = array(array('pin_code', ''));
            $Database->update_data($info, 'admin_id', $user_id, 'admin');
            $_SESSION['is_verified'] = true;

            if ($user_data['admin_role'] == 'super')

                $_SESSION['account-type'] = 'super_admin';

            else

                $_SESSION['account-type'] = 'support_admin';


            $_SESSION['account-id'] = $user_data['admin_id'];


            //Track activity

            $tracker_ip = get_client_ip();

            $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

            if ($address_info && $address_info['status'] == 'success')

                $tracker_location = $address_info['city'] . "/" . $address_info['country'];

            else

                $tracker_location = "Unknown";


            $tracker_action = 'Sign in';

            $info = array(

                array('user_id', $user_data['admin_id']),

                array('user_role', $_SESSION['account-type']),

                array('user_ip', $tracker_ip),

                array('user_location', $tracker_location),

                array('user_action', $tracker_action)

            );

            $Database->write_data($info, 'tracker', false);


            echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
        } else {
            $_SESSION['is_verified'] = false;
            echo 'Wrong Verification Code';
        }
    } //Company account
    elseif ($user_type == 'company') {
        $user_data = $Database->get_data('company_id', $user_id, 'company', true);
        if ($pic_code_verify = $Database->get_data('pin_code', $pin_code, 'company', true)) {
            $info = array(array('pin_code', ''));
            $Database->update_data($info, 'company_id', $user_id, 'company');
            $_SESSION['is_verified'] = true;
            $_SESSION['account-type'] = 'company';


            $_SESSION['account-id'] = $user_data['company_id'];


            //Track activity

            $tracker_ip = get_client_ip();

            $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

            if ($address_info && $address_info['status'] == 'success')

                $tracker_location = $address_info['city'] . "/" . $address_info['country'];

            else

                $tracker_location = "Unknown";


            $tracker_action = 'Sign in';

            $info = array(

                array('user_id', $user_data['company_id']),

                array('user_role', $_SESSION['account-type']),

                array('user_ip', $tracker_ip),

                array('user_location', $tracker_location),

                array('user_action', $tracker_action)

            );

            $Database->write_data($info, 'tracker', false);


            echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
        } else {
            $_SESSION['is_verified'] = false;
            echo 'Wrong Verification Code';
        }
    } //User Account
    elseif ($user_type == 'user') {
        $user_data = $Database->get_data('user_id', $user_id, 'user', true);
        if ($pic_code_verify = $Database->get_data('pin_code', $pin_code, 'user', true)) {
            $info = array(array('pin_code', ''));
            $Database->update_data($info, 'user_id', $user_id, 'user');
            $_SESSION['is_verified'] = true;
            $user_company = $Database->get_data('company_id', $user_data['user_company_id'], 'company', true);


            if ($user_company['company_status'] == 'active') {

                $_SESSION['account-type'] = 'user';


                $_SESSION['account-id'] = $user_data['user_id'];


                //Track activity

                $tracker_ip = get_client_ip();

                $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

                if ($address_info && $address_info['status'] == 'success')

                    $tracker_location = $address_info['city'] . "/" . $address_info['country'];

                else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign in';

                $info = array(

                    array('user_id', $user_data['user_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
            } else {
                $_SESSION['is_verified'] = false;
                echo 'Wrong Verification Code';
            }
        }
    } //Company account
    elseif ($user_type == 'consultant') {
        $user_data = $Database->get_data('consultant_id', $user_id, 'consultant', true);
        if ($pic_code_verify = $Database->get_data('pin_code', $pin_code, 'consultant', true)) {
            $info = array(array('pin_code', ''));
            $Database->update_data($info, 'consultant_id', $user_id, 'consultant');
            $_SESSION['is_verified'] = true;
            $_SESSION['account-type'] = 'consultant';


            $_SESSION['account-id'] = $user_data['consultant_id'];


            //Track activity

            $tracker_ip = get_client_ip();

            $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

            if ($address_info && $address_info['status'] == 'success')

                $tracker_location = $address_info['city'] . "/" . $address_info['country'];

            else

                $tracker_location = "Unknown";


            $tracker_action = 'Sign in';

            $info = array(

                array('user_id', $user_data['consultant_id']),

                array('user_role', $_SESSION['account-type']),

                array('user_ip', $tracker_ip),

                array('user_location', $tracker_location),

                array('user_action', $tracker_action)

            );

            $Database->write_data($info, 'tracker', false);


            echo json_encode(array('message' => 'success', 'type' => $_SESSION['account-type']));
        } else {
            $_SESSION['is_verified'] = false;
            echo 'Wrong Verification Code';
        }
    }

}
//Account logout

if (isset($_POST['sign']) && $_POST['sign'] == 'account_logout') {

    $_SESSION['account-type'] = null;

    $_SESSION['account-id'] = null;

    $_SESSION['is_verified'] = false;

    $_SESSION['user_type_logged_in'] = null;

    $_SESSION['user_id_logged_in'] = null;

    echo 'success';

}


//Password reset

if (isset($_POST['sign']) && $_POST['sign'] == 'reset_password') {

    $request_id = $_POST['request_id'];

    $new_pass = $_POST['new_password'];

    $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);


    $updated = false;

    $request_data = $Database->get_data('pass_token', $request_id, 'password_reset', true);

    if ($request_data) {

        if ($request_data['account_type'] == 'user') {

            $info = array(array('user_password', $password_hash));

            $Database->update_data($info, 'user_id', $request_data['user_id'], 'user');

            $updated = true;

        } else if ($request_data['account_type'] == 'company') {

            $info = array(array('company_password', $password_hash));

            $Database->update_data($info, 'company_id', $request_data['user_id'], 'company');

            $updated = true;

        } else if ($request_data['account_type'] == 'consultant') {

            $info = array(array('consultant_password', $password_hash));

            $Database->update_data($info, 'consultant_id', $request_data['user_id'], 'consultant');

            $updated = true;

        } else if ($request_data['account_type'] == 'super' || $request_data['account_type'] == 'support') {

            $info = array(array('admin_password', $password_hash));

            $Database->update_data($info, 'admin_id', $request_data['user_id'], 'admin');

            $updated = true;

        }

    }

    if ($updated) {

        $Database->delete_data('pass_token', $request_id, 'password_reset');

        echo $trans->phrase("option_server_phrase3") . ' <a class="btn btn-dark btn-sm" href="' . SITE_URL . '">' . $trans->phrase('index_phrase5') . '</a>';

    } else {

        echo $trans->phrase("option_server_phrase4");

    }

}


//Forgot password

if (isset($_POST['sign']) && $_POST['sign'] == 'forgot_password') {

    $type = $_POST['type'];
    $companyName = 'Semje Software';
    $sended = '';
    if ($type == 'email') {
        $user_email = $_POST['user_source'];
        if ($user = $Database->get_data('admin_email', $user_email, 'admin', true)) {

            $user_id = $user['admin_id'];

            $user_type = $user['admin_role'];

            $user_name = $user['admin_name'];

            $_SESSION['user_type_logged_in'] = 'admin';
            $_SESSION['user_id_logged_in'] = $user_id;

            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'admin_id', $user_id, 'admin');

            /** custom email template */
            
            $subject = $trans->phrase("index_phrase38");
            $subject = str_replace('{{_AppName_}}', APP_NAME, $subject);

            $emailTemplate = $trans->phrase("index_phrase39");

            $emailContent = str_replace('{{_CompanyName_}}', $companyName, $emailTemplate);
            $emailContent = str_replace('{{_AppName_}}', APP_NAME, $emailContent);
            $emailContent = str_replace('{{_AppLogo_}}', "https://beta.nogd.no/images/responder_logo.png", $emailContent);
            $emailContent = str_replace('{{_OTP_}}', $pin_code, $emailContent);
            $emailContent = str_replace('{{_TELNUM_}}', '+47 950 09 050', $emailContent);

            $email_sender = new EmailSender();
            $send = $email_sender->send_mail($user_email, $subject, $emailContent);

            $sended = 'done';

        } else if ($user = $Database->get_data('company_email', $user_email, 'company', true)) {

            $user_id = $user['company_id'];

            $user_type = 'company';

            $user_name = $user['company_name'];

            $_SESSION['user_type_logged_in'] = 'company';
            $_SESSION['user_id_logged_in'] = $user_id;
            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'company_id', $user_id, 'company');

            /** custom email template */
            
            $subject = $trans->phrase("index_phrase38");
            $subject = str_replace('{{_AppName_}}', APP_NAME, $subject);

            $emailTemplate = $trans->phrase("index_phrase39");

            $emailContent = str_replace('{{_CompanyName_}}', $companyName, $emailTemplate);
            $emailContent = str_replace('{{_AppName_}}', APP_NAME, $emailContent);
            $emailContent = str_replace('{{_AppLogo_}}', "https://beta.nogd.no/images/responder_logo.png", $emailContent);
            $emailContent = str_replace('{{_OTP_}}', $pin_code, $emailContent);
            $emailContent = str_replace('{{_TELNUM_}}', '+47 950 09 050', $emailContent);

            $email_sender = new EmailSender();
            $send = $email_sender->send_mail($user_email, $subject, $emailContent);

            $sended = 'done';

        } else if ($user = $Database->get_data('consultant_email', $user_email, 'consultant', true)) {

            $user_id = $user['consultant_id'];

            $user_type = 'consultant';

            $user_name = $user['consultant_name'];

            $_SESSION['user_type_logged_in'] = 'consultant';
            $_SESSION['user_id_logged_in'] = $user_id;
            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'consultant_id', $user_id, 'consultant');

            /** custom email template */
            
            $subject = $trans->phrase("index_phrase38");
            $subject = str_replace('{{_AppName_}}', APP_NAME, $subject);

            $emailTemplate = $trans->phrase("index_phrase39");

            $emailContent = str_replace('{{_CompanyName_}}', $companyName, $emailTemplate);
            $emailContent = str_replace('{{_AppName_}}', APP_NAME, $emailContent);
            $emailContent = str_replace('{{_AppLogo_}}', "https://beta.nogd.no/images/responder_logo.png", $emailContent);
            $emailContent = str_replace('{{_OTP_}}', $pin_code, $emailContent);
            $emailContent = str_replace('{{_TELNUM_}}', '+47 950 09 050', $emailContent);

            $email_sender = new EmailSender();
            $send = $email_sender->send_mail($user_email, $subject, $emailContent);

            $sended = 'done';

        } else if ($user = $Database->get_data('user_email', $user_email, 'user', true)) {

            $user_id = $user['user_id'];

            $user_type = 'user';

            $user_name = $user['user_name'];

            $_SESSION['user_type_logged_in'] = 'user';
            $_SESSION['user_id_logged_in'] = $user_id;
            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'user_id', $user_id, 'user');

            /** custom email template */
            
            $subject = $trans->phrase("index_phrase38");
            $subject = str_replace('{{_AppName_}}', APP_NAME, $subject);

            $emailTemplate = $trans->phrase("index_phrase39");

            $emailContent = str_replace('{{_CompanyName_}}', $companyName, $emailTemplate);
            $emailContent = str_replace('{{_AppName_}}', APP_NAME, $emailContent);
            $emailContent = str_replace('{{_AppLogo_}}', "https://beta.nogd.no/images/responder_logo.png", $emailContent);
            $emailContent = str_replace('{{_OTP_}}', $pin_code, $emailContent);
            $emailContent = str_replace('{{_TELNUM_}}', '+47 950 09 050', $emailContent);

            $email_sender = new EmailSender();
            $send = $email_sender->send_mail($user_email, $subject, $emailContent);

            $sended = 'done';
        }
    } elseif ($type == 'phone') {
        $user_phone = $_POST['user_source'];
        if ($user = $Database->get_data('tfa_phone', $user_phone, 'admin', true)) {

            $user_id = $user['admin_id'];

            $user_type = $user['admin_role'];

            $user_name = $user['admin_name'];

            $_SESSION['user_type_logged_in'] = 'admin';
            $_SESSION['user_id_logged_in'] = $user_id;

            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'admin_id', $user_id, 'admin');

            $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
            $token = '6842d11eef8b0b84b906cb942a8073bb';
            $phone = $user_phone;
            $client = new Client($sid, $token);

            $client->messages->create(
                $phone, array(
                    "from" => "+17473185226",
                    "body" => "Your Verification code is: " . $pin_code
                )
            );

            $sended = 'done';
        } else if ($user = $Database->get_data('company_phone', $user_phone, 'company', true)) {

            $user_id = $user['company_id'];

            $user_type = 'company';

            $user_name = $user['company_name'];

            $_SESSION['user_type_logged_in'] = 'company';
            $_SESSION['user_id_logged_in'] = $user_id;
            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'company_id', $user_id, 'company');

            $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
            $token = '6842d11eef8b0b84b906cb942a8073bb';
            $phone = $user_phone;
            $client = new Client($sid, $token);

            $client->messages->create(
                $phone, array(
                    "from" => "+17473185226",
                    "body" => "Your Verification code is: " . $pin_code
                )
            );

            $sended = 'done';

        } else if ($user = $Database->get_data('consultant_phone', $user_phone, 'consultant', true)) {

            $user_id = $user['consultant_id'];

            $user_type = 'consultant';

            $user_name = $user['consultant_name'];

            $_SESSION['user_type_logged_in'] = 'consultant';
            $_SESSION['user_id_logged_in'] = $user_id;
            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'consultant_id', $user_id, 'consultant');

            $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
            $token = '6842d11eef8b0b84b906cb942a8073bb';
            $phone = $user_phone;
            $client = new Client($sid, $token);

            $client->messages->create(
                $phone, array(
                    "from" => "+17473185226",
                    "body" => "Your verification code is: " . $pin_code
                )
            );

            $sended = 'done';

        } else if ($user = $Database->get_data('user_phone', $user_phone, 'user', true)) {

            $user_id = $user['user_id'];

            $user_type = 'user';

            $user_name = $user['user_name'];

            $_SESSION['user_type_logged_in'] = 'user';
            $_SESSION['user_id_logged_in'] = $user_id;
            $pin_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

            $info = array(array('pin_code', $pin_code));
            $Database->update_data($info, 'user_id', $user_id, 'user');

            $sid = 'ACa0fdb9ceecd9a0c8d64df4378a42f836';
            $token = '6842d11eef8b0b84b906cb942a8073bb';
            $phone = $user_phone;
            $client = new Client($sid, $token);

            $client->messages->create(
                $phone, array(
                    "from" => "+17473185226",
                    "body" => "Your Verification code is: " . $pin_code
                )
            );

            $sended = 'done';
        }
    }

    if ($sended == 'done') {
        echo json_encode(array('message' => 'not_verified', 'type' => $type));
    } else {
        echo $trans->phrase('option_server_phrase2');
    }
}


//Consultant singup

if (isset($_POST['sign']) && $_POST['sign'] == 'consultant_signup') {

    $consultant_name = $_POST['consultant_name'];

    $consultant_email = $_POST['consultant_email'];

    $consultant_password = $_POST['consultant_password'];

    $consultant_phone = $_POST['consultant_phone'];


    $validation = true;

    if (strlen($consultant_name) < 1 || strlen($consultant_password) < 1 || strlen($consultant_phone) < 1) {

        $validation = $validation & false;

    }

    if (!filter_var($consultant_email, FILTER_VALIDATE_EMAIL)) {

        $validation = $validation & false;

    }


    if ($validation) {

        if ($Database->get_data('consultant_email', $consultant_email, 'consultant')) {

            echo $trans->phrase("option_server_phrase7");

        } else {

            $password_hash = password_hash($consultant_password, PASSWORD_DEFAULT);


            $info = array(

                array("consultant_name", $consultant_name),

                array("consultant_email", $consultant_email),

                array("consultant_password", $password_hash),

                array("consultant_phone", $consultant_phone)

            );


            $write = $Database->write_data($info, 'consultant', false);

            if ($write) {

                $consultant = $Database->get_data('consultant_email', $consultant_email, 'consultant', true);

                $_SESSION['account-type'] = 'consultant';

                $_SESSION['account-id'] = $consultant['consultant_id'];


                //Track activity

                $tracker_ip = get_client_ip();

               // $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

               // if ($address_info && $address_info['status'] == 'success')

               //     $tracker_location = $address_info['city'] . "/" . $address_info['country'];

              //  else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign up';

                $info = array(

                    array('user_id', $consultant['consultant_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                //Sending welcome email

                $attributes = array(

                    array('site_url', SITE_URL),

                    array('site_name', NAME),

                    array('user_name', $consultant_name),

                    array('user_email', $consultant_email),

                    array('user_id', $consultant['consultant_id']),

                    array('site_email', SITE_EMAIL),

                    array('site_contact', SITE_URL . "/contact_us")

                );


                //$email_sender = new EmailSender();

                //$body = $email_sender->load_template('welcome_mail', $attributes);

                //$send = $email_sender->send_mail($consultant_email, $trans->phrase('email_title_phrase8') . ' ' . NAME, $body);


                echo 'success';

            } else echo $trans->phrase("option_server_phrase10");

        }

    }

}

//User singup

if (isset($_POST['sign']) && $_POST['sign'] == 'user_signup') {

    $user_name = $_POST['user_name'];

    $user_email = $_POST['user_email'];

    $user_password = $_POST['user_password'];

    $user_phone = $_POST['user_phone'];

    $user_company_id = $_POST['user_company_id'];


    $validation = true;

    if (strlen($user_name) < 1 || strlen($user_password) < 1 || strlen($user_phone) < 1 || strlen($user_company_id) < 1) {

        $validation = $validation & false;

    }

    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {

        $validation = $validation & false;

    }


    if ($validation) {

        $company = $Database->get_data('company_id', $user_company_id, 'company', true);

        $company_users = $Database->get_multiple_data('user_company_id', $user_company_id, 'user', '=', true, false, false);


        //Company user count

        $user_limit = 0;

        if ($company && $company['company_package_id']) {

            $package_info = $Database->get_data('package_id', $company['company_package_id'], 'package', true);

            $user_limit = $package_info['package_user'];

        }


        if ($Database->get_data('user_email', $user_email, 'user')) {

            echo $trans->phrase("option_server_phrase7");

        } else if (!$company) {

            echo $trans->phrase("option_server_phrase8");

        } else if ($company['company_status'] != 'active') {

            echo $trans->phrase("option_server_phrase9");

        } else if ($company_users && count($company_users) > $user_limit) {

            echo $trans->phrase("option_server_phrase9");

        } else if ($user_limit == 0) {

            echo $trans->phrase("option_server_phrase9");

        } else {

            $password_hash = password_hash($user_password, PASSWORD_DEFAULT);


            $info = array(

                array("user_name", $user_name),

                array("user_email", $user_email),

                array("user_password", $password_hash),

                array("user_phone", $user_phone),

                array("user_company_id", $user_company_id)

            );


            $write = $Database->write_data($info, 'user', false);

            if ($write) {

                $user = $Database->get_data('user_email', $user_email, 'user', true);

                $_SESSION['account-type'] = 'user';

                $_SESSION['account-id'] = $user['user_id'];


                //Track activity

                $tracker_ip = get_client_ip();

              //  $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

              //  if ($address_info && $address_info['status'] == 'success')

             //       $tracker_location = $address_info['city'] . "/" . $address_info['country'];

             //   else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign up';

                $info = array(

                    array('user_id', $user['user_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                //Sending welcome email

                $attributes = array(

                    array('site_url', SITE_URL),

                    array('site_name', NAME),

                    array('user_name', $user_name),

                    array('user_email', $user_email),

                    array('user_id', $user['user_id']),

                    array('site_email', SITE_EMAIL),

                    array('site_contact', SITE_URL . "/contact_us")

                );


              //  $email_sender = new EmailSender();

              //  $body = $email_sender->load_template('welcome_mail', $attributes);

              //  $send = $email_sender->send_mail($user_email, $trans->phrase('email_title_phrase8') . ' ' . NAME, $body);


                echo 'success';

            } else echo $trans->phrase("option_server_phrase10");

        }

    }

}

if (isset($_POST['sign']) && $_POST['sign'] == 'company_user_signup') {

    $user_name = $_POST['user_name'];
    
	$user_email = $_POST['user_email'];

    $approve_per = $_POST['approve_per'];

    $user_password = $_POST['user_password'];

    $user_phone = $_POST['user_phone'];

    $user_company_id = $_POST['user_company_id'];


    $validation = true;

    if (strlen($user_name) < 1 || strlen($user_password) < 1 || strlen($user_phone) < 1 || strlen($user_company_id) < 1) {

        $validation = $validation & false;

    }

    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {

        $validation = $validation & false;

    }


    if ($validation) {

        $company = $Database->get_data('company_id', $user_company_id, 'company', true);

        $company_users = $Database->get_multiple_data('user_company_id', $user_company_id, 'user', '=', true, false, false);


        //Company user count

        $user_limit = 0;

        if ($company && $company['company_package_id']) {

            $package_info = $Database->get_data('package_id', $company['company_package_id'], 'package', true);

            $user_limit = $package_info['package_user'];

        }


        if ($Database->get_data('user_email', $user_email, 'user')) {

            echo $trans->phrase("option_server_phrase7");

        } else if (!$company) {

            echo $trans->phrase("option_server_phrase8");

        } else if ($company['company_status'] != 'active') {

            echo $trans->phrase("option_server_phrase9");

        } else if ($company_users && count($company_users) > $user_limit) {

            echo $trans->phrase("option_server_phrase9");

        } else if ($user_limit == 0) {

            echo $trans->phrase("option_server_phrase9");

        } else {

            $password_hash = password_hash($user_password, PASSWORD_DEFAULT);


            $info = array(

                array("user_name", $user_name),
                
				array("approve_per", $approve_per),

                array("user_email", $user_email),

                array("user_password", $password_hash),

                array("user_phone", $user_phone),

                array("user_company_id", $user_company_id)

            );


            $write = $Database->write_data($info, 'user', false);

            if ($write) {

                $user = $Database->get_data('user_email', $user_email, 'user', true);


                //Track activity

                $tracker_ip = get_client_ip();

                $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

                if ($address_info && $address_info['status'] == 'success')

                    $tracker_location = $address_info['city'] . "/" . $address_info['country'];

                else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign up';

                $info = array(

                    array('user_id', $user['user_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                echo 'success';

            } else echo $trans->phrase("option_server_phrase10");

        }

    }

}


//Company registration

if (isset($_POST['sign']) && $_POST['sign'] == 'company_registration') {

    $company_name = htmlspecialchars($_POST['company_name'], ENT_QUOTES);

    $company_industry_type = $_POST['company_industry_type'];

    $company_email = $_POST['company_email'];

    $company_password = $_POST['company_password'];

    $company_phone = htmlspecialchars($_POST['company_phone'], ENT_QUOTES);


    $validation = true;

    if (strlen($company_name) < 1 || !$company_industry_type || strlen($company_password) < 1 || strlen($company_phone) < 1) {

        $validation = $validation & false;

    }

    if (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {

        $validation = $validation & false;

    }


    if ($validation) {

        if ($Database->get_data('company_email', $company_email, 'company')) {

            echo $trans->phrase("option_server_phrase11");

        } else {

            $password_hash = password_hash($company_password, PASSWORD_DEFAULT);


            $info = array(

                array("company_name", $company_name),

                array("company_industry_type", $company_industry_type),

                array("company_email", $company_email),

                array("company_password", $password_hash),

                array("company_phone", $company_phone),

                array("company_expire", date("Y-m-d", time()))

            );


            $write = $Database->write_data($info, 'company', false);

            if ($write) {

                $company = $Database->get_data('company_email', $company_email, 'company', true);

                $_SESSION['account-type'] = 'company';

                $_SESSION['account-id'] = $company['company_id'];


                //Track activity

               $tracker_ip = get_client_ip();

               // $address_info = @unserialize(file_get_contents('http://ip-api.com/php/' . $tracker_ip));

               // if ($address_info && $address_info['status'] == 'success')

               //     $tracker_location = $address_info['city'] . "/" . $address_info['country'];

              //  else

                    $tracker_location = "Unknown";


                $tracker_action = 'Sign up';

                $info = array(

                    array('user_id', $company['company_id']),

                    array('user_role', $_SESSION['account-type']),

                    array('user_ip', $tracker_ip),

                    array('user_location', $tracker_location),

                    array('user_action', $tracker_action)

                );

                $Database->write_data($info, 'tracker', false);


                //Sending welcome email

                $attributes1 = array(

                    array('site_url', SITE_URL),

                    array('site_name', NAME),

                    array('user_name', $company_name),

                    array('user_email', $company_email),

                    array('user_id', $company['company_id']),

                    array('site_email', SITE_EMAIL)

                );


                $attributes2 = array(

                    array('site_url', SITE_URL),

                    array('site_name', NAME),

                    array('user_name', $company_name),

                    array('user_email', $company_email),

                    array('user_id', $company['company_id']),

                    array('site_email', SITE_EMAIL),

                    array('user_status', 'pending'),

                    array('user_url', SITE_URL . "/user/index.php?route=company_profile&company_id=" . $company['company_id'])

                );


//                $email_sender = new EmailSender();

            //    $body1 = $email_sender->load_template('welcome_mail', $attributes1);

            //    $send1 = $email_sender->send_mail($company_email, $trans->phrase('email_title_phrase8') . ' ' . NAME, $body1);


            //    $body2 = $email_sender->load_template('company_registration_backoffice', $attributes2);

             //   $send2 = $email_sender->send_mail(SITE_EMAIL, $trans->phrase('email_title_phrase9'), $body2);


                echo 'success';

            } else echo $trans->phrase("option_server_phrase12");

        }

    }

}


//Admin actions

//Change admin name

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_admin_name' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $admin_id = $_POST['admin_id'];

    $admin_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);

    $info = array(

        array('admin_name', $admin_name)

    );


    $updated = $Database->update_data($info, 'admin_id', $admin_id, 'admin');

    if ($updated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase13");

}

//Change admin email

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_admin_email' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $admin_id = $_POST['admin_id'];

    $admin_email = $_POST['new_email'];


    $admin_data = $Database->get_data('admin_email', $admin_email, 'admin');

    if (!$admin_data)

        $admin_data = $Database->get_data('company_email', $admin_email, 'company');

    if (!$admin_data)

        $admin_data = $Database->get_data('user_email', $admin_email, 'user');


    if ($admin_data)

        echo $trans->phrase('option_server_phrase76');

    else {

        $info = array(

            array('admin_email', $admin_email)

        );


        $updated = $Database->update_data($info, 'admin_id', $admin_id, 'admin');

        if ($updated) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase13");

    }

}

//Change admin password

if (

    isset($_POST['sign']) && $_POST['sign'] == 'admin_password_change' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $admin_id = $_POST['admin_id'];

    $old_pass = $_POST['old_pass'];

    $new_pass = $_POST['new_pass'];


    $admin_data = $Database->get_data('admin_id', $admin_id, 'admin', true);


    if (password_verify($old_pass, $admin_data['admin_password'])) {

        $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);

        $info = array(

            array('admin_password', $password_hash),

        );


        $updated = $Database->update_data($info, 'admin_id', $admin_id, 'admin');

        if ($updated) {

            echo $trans->phrase("option_server_phrase14");

        } else echo $trans->phrase("option_server_phrase15");

    } else echo $trans->phrase("option_server_phrase16");

}


//Profile actions

//Change user name

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_user_name' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'user')

) {


    $user_id = $_POST['user_id'];

    $user_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);

    $info = array(

        array('user_name', $user_name)

    );


    $updated = $Database->update_data($info, 'user_id', $user_id, 'user');

    if ($updated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase17");

}

//Change user email

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_user_email' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'user')

) {


    $user_id = $_POST['user_id'];

    $user_email = $_POST['new_email'];


    $user_data = $Database->get_data('user_email', $user_email, 'user');

    if (!$user_data)

        $user_data = $Database->get_data('company_email', $user_email, 'company');

    if (!$user_data)

        $user_data = $Database->get_data('admin_email', $user_email, 'admin');


    if ($user_data) {

        echo $trans->phrase('option_server_phrase76');

    } else {

        $info = array(

            array('user_email', $user_email)

        );


        $updated = $Database->update_data($info, 'user_id', $user_id, 'user');

        if ($updated) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase17");

    }

}

//Change user password by admin

if (

    isset($_POST['sign']) && $_POST['sign'] == 'user_password_change_admin' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $user_id = $_POST['user_id'];

    $new_pass = $_POST['new_pass'];


    $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);

    $info = array(

        array('user_password', $password_hash),

    );


    $updated = $Database->update_data($info, 'user_id', $user_id, 'user');

    if ($updated) {

        echo $trans->phrase("option_server_phrase18");

    } else echo $trans->phrase("option_server_phrase19");

}

//Change user password by user

if (isset($_POST['sign']) && $_POST['sign'] == 'user_password_change' && $_SESSION['account-type'] == 'user') {


    $user_id = $_POST['user_id'];

    $old_pass = $_POST['old_pass'];

    $new_pass = $_POST['new_pass'];


    $user_data = $Database->get_data('user_id', $user_id, 'user', true);

    if($old_pass == $new_pass) {

        if (password_verify($old_pass, $user_data['user_password'])) {

            $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);
    
            $info = array(
    
                array('user_password', $password_hash),
    
            );
    
    
            $updated = $Database->update_data($info, 'user_id', $user_id, 'user');
    
            if ($updated) {
    
                echo $trans->phrase("option_server_phrase20");
    
            } else echo $trans->phrase("option_server_phrase21");
    
        } else echo $trans->phrase("option_server_phrase22");
        
    } else {
        $trans->phrase("option_server_phrase96");
    }

}

//Remove calendar access

if (isset($_POST['sign']) && $_POST['sign'] == 'remove_calendar_access' && $_SESSION['account-type'] == 'user') {

    $user_id = $_SESSION['account-id'];


    $info = array(array('google_auth_code', null));

    $Database->update_data($info, 'user_id', $user_id, 'user');

    echo 'success';

}


//Support actions

//Change support email

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_support_email' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $new_email = $_POST['new_email'];


    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {

        echo $trans->phrase('option_server_phrase64');

    } else {

        $info = array(array('option_value', $new_email));

        $updated = $Database->update_data($info, 'option_key', 'support_email', 'options');


        if ($updated)

            echo 'success';

        else

            echo $trans->phrase('option_server_phrase63');

    }

}

//Change support phone

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_support_phone' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $new_phone = htmlspecialchars($_POST['new_phone'], ENT_QUOTES);


    if (strlen($new_phone) < 1) {

        echo $trans->phrase('option_server_phrase64');

    } else {

        $info = array(array('option_value', $new_phone));

        $updated = $Database->update_data($info, 'option_key', 'support_phone', 'options');


        if ($updated)

            echo 'success';

        else

            echo $trans->phrase('option_server_phrase65');

    }

}

//Change support address

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_support_address' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $new_address = htmlspecialchars($_POST['new_address'], ENT_QUOTES);


    if (strlen($new_address) < 1) {

        echo $trans->phrase('option_server_phrase64');

    } else {

        $info = array(array('option_value', $new_address));

        $updated = $Database->update_data($info, 'option_key', 'support_address', 'options');


        if ($updated)

            echo 'success';

        else

            echo $trans->phrase('option_server_phrase66');

    }

}


//Add support request

if (

    isset($_POST['sign']) && $_POST['sign'] == 'add_support_message' &&

    ($_SESSION['account-type'] == 'user' ||

        $_SESSION['account-type'] == 'company')

) {


    $subject = htmlspecialchars($_POST['support_subject'], ENT_QUOTES);

    $message = htmlspecialchars($_POST['support_message'], ENT_QUOTES);

    $user_type = $_SESSION['account-type'];

    $user_id = $_SESSION['account-id'];


    $info = array(

        array('support_user_id', $user_id),

        array('support_user_type', $user_type),

        array('support_subject', $subject),

        array('support_message', $message)

    );


    $added = $Database->write_data($info, 'support', false, true);


    if ($added) {

        //Getting email. Email sent to admin.

        $support_email = SITE_EMAIL;

        $option_email_data = $Database->get_data('option_key', 'support_email', 'options', true);

        if ($option_email_data) $support_email = $option_email_data['option_value'];


        //Send email

        $support_url = SITE_URL . "/user/index.php?route=support&id={$added}";

        $attributes = array(

            array('site_url', SITE_URL),

            array('site_name', NAME),

            array('support_url', $support_url),

            array('site_email', SITE_EMAIL)

        );


       // $email_sender = new EmailSender();

        //$body = $email_sender->load_template('support_new', $attributes);

        //$email_sender->send_mail($support_email, $trans->phrase('email_title_phrase3'), $body);


        echo 'success';

    } else

        echo $trans->phrase('option_server_phrase67');

}

//Add support reply

if (isset($_POST['sign']) && $_POST['sign'] == 'add_support_reply') {


    $message = htmlspecialchars($_POST['support_message'], ENT_QUOTES);

    $parent_id = $_POST['parent_id'];

    $user_type = $_SESSION['account-type'];

    $user_id = $_SESSION['account-id'];


    $support_data = $Database->get_data('support_id', $parent_id, 'support', true);


    $validation = false;

    if ($support_data) {

        if (!$support_data['support_parent'] && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')) {

            $validation = true;

        } else if (

            !$support_data['support_parent'] && $_SESSION['account-type'] == 'company' &&

            $support_data['support_user_type'] == 'company' && $support_data['support_user_id'] == $_SESSION['account-id']

        ) {


            $validation = true;

        } else if (

            !$support_data['support_parent'] && $_SESSION['account-type'] == 'user' &&

            $support_data['support_user_type'] == 'user' && $support_data['support_user_id'] == $_SESSION['account-id']

        ) {


            $validation = true;

        }

    }


    if ($validation) {

        $info = array(

            array('support_user_id', $user_id),

            array('support_user_type', $user_type),

            array('support_parent', $parent_id),

            array('support_message', $message)

        );


        $added = $Database->write_data($info, 'support', false);


        if ($added) {

            //Getting email address. if admin, email sent to user. if user, email sent to admin.

            $support_email = '';

            if ($user_type == 'user' || $user_type == 'company') {

                $support_email = SITE_EMAIL;

                $option_email_data = $Database->get_data('option_key', 'support_email', 'options', true);

                if ($option_email_data) $support_email = $option_email_data['option_value'];

            } else {

                if ($support_data['support_user_type'] == 'company') {

                    $support_company = $Database->get_data('company_id', $support_data['support_user_id'], 'company', true);

                    $support_email = $support_company['company_email'];

                } else {

                    $support_user = $Database->get_data('user_id', $support_data['support_user_id'], 'user', true);

                    $support_email = $support_user['user_email'];

                }

            }


            //Send email

            $support_url = SITE_URL . "/user/index.php?route=support&id={$support_data['support_id']}";

            $attributes = array(

                array('site_url', SITE_URL),

                array('site_name', NAME),

                array('support_id', $support_data['support_id']),

                array('support_url', $support_url),

                array('site_email', SITE_EMAIL)

            );


            $email_sender = new EmailSender();

            $body = $email_sender->load_template('support_update', $attributes);

            $email_sender->send_mail($support_email, $trans->phrase('email_title_phrase4'), $body);


            echo 'success';

        } else

            echo $trans->phrase('option_server_phrase67');

    } else {

        echo $trans->phrase('option_server_phrase68');

    }

}


//Company actions

//Suspend company

if (

    isset($_POST['sign']) && $_POST['sign'] == 'suspend_company' &&

    ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {

    $company_id = $_POST['company_id'];

    $info = array(

        array('company_status', 'suspended')

    );


    $suspended = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($suspended) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase23");

}

//Activate company

if (

    isset($_POST['sign']) && $_POST['sign'] == 'activate_company' &&

    ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {

    $company_id = $_POST['company_id'];

    $info = array(

        array('company_status', 'active')

    );


    $activated = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($activated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase24");

}

//Renew Company

if (

    isset($_POST['sign']) && $_POST['sign'] == 'renew_company' &&

    ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {

    $company_id = $_POST['company_id'];


    $company = $Database->get_data('company_id', $company_id, 'company', true);


    $next_expire = Date("Y-m-d");

    if (strtotime($company['company_expire']) > time()) {

        $next_expire = Date("Y-m-d", strtotime($company['company_expire'] . " + " . $company['company_payment_cycle'] . " months"));

    } else {

        $next_expire = Date("Y-m-d", strtotime(" + " . $company['company_payment_cycle'] . " months"));

    }


    $info = array(

        array('company_expire', $next_expire)

    );


    $renewed = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($renewed) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase72");

}

//Delete Company

if (

    isset($_POST['sign']) && $_POST['sign'] == 'delete_company' &&

    ($_SESSION['account-type'] == 'super_admin'

        || $_SESSION['account-type'] == 'support_admin')

) {

    $company_id = $_POST['company_id'];


    //$deleted = $Database->delete_data('company_id', $company_id, 'company');

    $deleted = $Database->delete_company($company_id);

    if ($deleted) {

        echo 'success';

    } else {

        echo $trans->phrase("option_server_phrase73");

    }

}


//Update admin profile

if (

    isset($_POST['sign']) && $_POST['sign'] == 'update_admin_data' && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {


    $admin_id = $_POST['admin_id'];
    $upload_admin_img = $_POST['upload_admin_img'];

    $admin_email = $_POST['new_email'];
    $old_email = $_POST['old_email'];
    $admin_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);

    if ($old_email != $admin_email) {

        $admin_data = $Database->get_data('admin_email', $admin_email, 'admin');

        if (!$admin_data)

            $admin_data = $Database->get_data('company_email', $admin_email, 'company');

        if (!$admin_data)

            $admin_data = $Database->get_data('user_email', $admin_email, 'user');


        if ($admin_data)

            echo $trans->phrase('option_server_phrase76');

        else {

            $info = array(

                array('admin_email', $admin_email),
                array('upload_admin_img', $upload_admin_img),
                array('admin_name', $admin_name)

            );


            $updated = $Database->update_data($info, 'admin_id', $admin_id, 'admin');

            if ($updated) {

                echo 'success';

            } else echo $trans->phrase("option_server_phrase13");

        }
    } else {
        $info = array(

            array('admin_name', $admin_name),
            array('upload_admin_img', $upload_admin_img)

        );


        $updated = $Database->update_data($info, 'admin_id', $admin_id, 'admin');

        if ($updated) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase13");
    }

}


//Update Consultant profile

if (

    isset($_POST['sign']) && $_POST['sign'] == 'update_consultant_data' && ($_SESSION['account-type'] == 'consultant' || $_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {
    $consultant_id = $_POST['consultant_id'];
    $upload_consultant_img = $_POST['upload_consultant_img'];

    $consultant_email = $_POST['new_email'];
    $old_email = $_POST['old_email'];
    $consultant_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);

    if ($old_email != $consultant_email) {

        $consultant_data = $Database->get_data('admin_email', $consultant_email, 'admin');

        if (!$consultant_data)

            $consultant_data = $Database->get_data('company_email', $consultant_email, 'company');

        if (!$consultant_data)

            $consultant_data = $Database->get_data('user_email', $consultant_email, 'user');

        if (!$consultant_data)

            $consultant_data = $Database->get_data('consultant_email', $consultant_email, 'consultant');


        if ($consultant_data)

            echo $trans->phrase('option_server_phrase76');

        else {

            $info = array(

                array('consultant_email', $consultant_email),
                array('upload_consultant_img', $upload_consultant_img),
                array('consultant_name', $consultant_name)

            );

            $updated = $Database->update_data($info, 'consultant_id', $consultant_id, 'consultant');

            if ($updated) {

                echo 'success';

            } else echo $trans->phrase("option_server_phrase13");

        }
    } else {
        $info = array(

            array('consultant_name', $consultant_name),
            array('upload_consultant_img', $upload_consultant_img),

        );


        $updated = $Database->update_data($info, 'consultant_id', $consultant_id, 'consultant');

        if ($updated) {

            echo 'success';

        } else
            echo $trans->phrase("option_server_phrase13");
    }
    if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') {
        if (isset($_POST['companies']) && isset($_POST['status'])) {
            $companies = $_POST['companies'];
            $status = $_POST['status'];

            $info = array(

                array('consultant_companies', $companies),
                array('consultant_status', $status)

            );
            $updated = $Database->update_data($info, 'consultant_id', $consultant_id, 'consultant');

            if ($updated) {
                if (!empty($companies)) {
                    $consultant_companies = explode(',', $companies);
                    if (is_array($consultant_companies))
                        $ids_str = "'" . implode("','", $consultant_companies) . "'";
                    $Database->update_data_multiple('company_id', $ids_str, 'company');
                } else {
                    $Database->update_data_multiple_2('company');
                }
                echo 'success';
            }
        } else
            echo $trans->phrase("option_server_phrase13");
    }

}

//Update user profile

/*if (

    isset($_POST['sign']) && $_POST['sign'] == 'update_user_data' && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'user')

) {
    
    

    $user_id = $_POST['user_id'];

    $user_email = $_POST['new_email'];
    $old_email = $_POST['old_email'];
    $user_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);


    if ($old_email != $user_email) {
        $user_data = $Database->get_data('user_email', $user_email, 'user');

        if (!$user_data)

            $user_data = $Database->get_data('company_email', $user_email, 'company');

        if (!$user_data)

            $user_data = $Database->get_data('admin_email', $user_email, 'admin');


        if ($user_data) {

            echo $trans->phrase('option_server_phrase76');

        } else {

            $info = array(

                array('user_email', $user_email),
                array('user_name', $user_name)

            );


            $updated = $Database->update_data($info, 'user_id', $user_id, 'user');

            if ($updated) {

                echo 'success';

            } else echo $trans->phrase("option_server_phrase17");

        }
    } else {
        $info = array(

            array('user_name', $user_name)

        );


        $updated = $Database->update_data($info, 'user_id', $user_id, 'user');

        if ($updated) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase17");

    }

}*/


//Company user delete

if (isset($_POST['sign']) && $_POST['sign'] == 'user_delete_profileimg' && $_SESSION['account-type'] == 'user') {

        $user_id = $_POST['user_id'];
        //$sql = "UPDATE `user` SET user_profile = ''  WHERE  user_id='" . $user_id . "'";
        //$delete_data = $Database->get_connection()->prepare($sql);

        //$delete_data->execute();
         $info = array(
             array('user_profile','')
        );
        $updated = $Database->update_data($info, 'user_id', $user_id, 'user');
        echo "success";

}


if (isset($_POST['sign']) && $_POST['sign'] == 'update_user_data' && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'user')) {
   
    $user_id = $_POST['user_id_input'];
    $user_email = $_POST['user_email_editor_input'];
    $old_email = $_POST['user_email_editor_old'];
    $user_name = htmlspecialchars($_POST['user_name_editor_input'], ENT_QUOTES);
    $user_profile = htmlspecialchars($_POST['user_profile'], ENT_QUOTES);
    
    $uploadDir = '/home/alexsol/public_html/nodg/images/profilepic/'; 
 
    // Allowed file types 
    $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg'); 
 
    // Default response 
    $response = array( 
        'status' => 0, 
        'message' => 'Form submission failed, please try again.' 
    ); 
 
    if ($old_email != $user_email) {
        $user_data = $Database->get_data('user_email', $user_email, 'user');
        if (!$user_data)
            $user_data = $Database->get_data('company_email', $user_email, 'company');
        if (!$user_data)
           $user_data = $Database->get_data('admin_email', $user_email, 'admin');
        if ($user_data) {
            $response['message'] =  $trans->phrase('option_server_phrase76');
        } else {
            $response['status'] = 1;     
            $uploadStatus = 1; 
            // Upload file 
            $uploadedFile = ''; 
            if(!empty($_FILES["file"]["name"])){ 
                // File path config 
                $fileName = basename($_FILES["file"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats to upload 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile = $fileName; 
                    }else{ 
                        $uploadStatus = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus = 0; 
                    $response['message'] = 'Sorry, only '.implode('/', $allowTypes).' files are allowed to upload.'; 
                } 
            } 
            $info = array(
                array('user_email', $user_email),
                array('user_name', $user_name),
                array('user_profile',$uploadedFile)
            );
            $updated = $Database->update_data($info, 'user_id', $user_id, 'user');

            if ($updated) {
            $response['message'] =  'Data updated!';
            } else $response['message'] = $trans->phrase("option_server_phrase17");
        }
    } else {
        $response['status'] = 1;     
            $uploadedFile = ''; 
            if(!empty($_FILES["file"]["name"])){ 
                // File path config 
                $fileName = basename($_FILES["file"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats to upload 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile = $fileName; 
                    }else{ 
                        $uploadStatus = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus = 0; 
                    $response['message'] = 'Sorry, only '.implode('/', $allowTypes).' files are allowed to upload.'; 
                } 
            }else{
                $uploadedFile = $user_profile; 
            } 
        $info = array(
             array('user_name', $user_name),
             array('user_email', $user_email),
             array('user_profile',$uploadedFile)
        );
        $updated = $Database->update_data($info, 'user_id', $user_id, 'user');
        if ($updated) {
           $response['message'] = 'Data updated!';
        } else $response['message'] = $trans->phrase("option_server_phrase17");
    }
    
    // Return response 
    echo json_encode($response);
   
}


//update company data


if (isset($_POST['sign']) && $_POST['sign'] == 'update_company_data' && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'support_user' || $_SESSION['account-type'] == 'company')) {


    $company_id = $_POST['company_id'];
    $upload_company_img = $_POST['upload_company_img'];
    // save company name
    $company_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);
    $company_email = $_POST['new_email'];
    $old_email = $_POST['old_email'];

    $industry_type_id = $_POST['industry_id'];
    $company_payment_cycle = $_POST['new_cycle'];


    if ($old_email != $company_email) {


        $company_data = $Database->get_data('company_email', $company_email, 'company');

        if (!$company_data)
            $company_data = $Database->get_data('admin_email', $company_email, 'admin');
        if (!$company_data)
            $company_data = $Database->get_data('user_email', $company_email, 'user');

        if ($company_data)
            echo $trans->phrase('option_server_phrase76');
        else {

            $info = array(

                array('company_name', $company_name),
                array('upload_company_img', $upload_company_img),
                array('company_industry_type', $industry_type_id),
                array('company_payment_cycle', $company_payment_cycle),
                array('company_email', $company_email)

            );


            $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

            if ($updated) {

                echo 'success';

            } else echo $trans->phrase("option_server_phrase25");


            $report = htmlspecialchars($_POST['report_text'], ENT_QUOTES);
            $lang_code = $_POST['lang_code'];
            $updated = false;

            $report_data = null;
            $text_info = $Database->get_multiple_data('text_company', $company_id, 'text');
            if ($text_info) {
                foreach ($text_info as $single_text) {
                    if ($single_text['text_lang'] == $lang_code && $single_text['text_selector'] == 'general_report_text')
                        $report_data = $single_text;
                }
            }
            if (isset($report_data) && $report_data) {
                $info = array(array('text_content', $report));
                $updated = $Database->update_data($info, 'text_id', $report_data['text_id'], 'text');
            } else {
                $info = array(
                    array('text_selector', 'general_report_text'),
                    array('text_content', $report),
                    array('text_company', $company_id),
                    array('text_lang', $lang_code)
                );

                $updated = $Database->write_data($info, 'text');
            }

            if ($updated)
                echo 'success';
            else
                echo $trans->phrase('option_server_phrase60');

        }
    } else {

        $info = array(

            array('company_name', $company_name),
            array('upload_company_img', $upload_company_img),
            array('company_industry_type', $industry_type_id),
            array('company_payment_cycle', $company_payment_cycle),

        );


        $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

        if ($updated) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase25");


        $report = htmlspecialchars($_POST['report_text'], ENT_QUOTES);
        $lang_code = $_POST['lang_code'];
        $updated = false;

        $report_data = null;
        $text_info = $Database->get_multiple_data('text_company', $company_id, 'text');
        if ($text_info) {
            foreach ($text_info as $single_text) {
                if ($single_text['text_lang'] == $lang_code && $single_text['text_selector'] == 'general_report_text')
                    $report_data = $single_text;
            }
        }
        if (isset($report_data) && $report_data) {
            $info = array(array('text_content', $report));
            $updated = $Database->update_data($info, 'text_id', $report_data['text_id'], 'text');
        } else {
            $info = array(
                array('text_selector', 'general_report_text'),
                array('text_content', $report),
                array('text_company', $company_id),
                array('text_lang', $lang_code)
            );

            $updated = $Database->write_data($info, 'text');
        }

        if ($updated)
            echo 'success';
        else
            echo $trans->phrase('option_server_phrase60');

    }


}


//Change company name

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_company_name' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $company_id = $_POST['company_id'];

    $company_name = htmlspecialchars($_POST['new_name'], ENT_QUOTES);

    $info = array(

        array('company_name', $company_name)

    );


    $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($updated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase25");

}

//Change company email

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_company_email' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $company_id = $_POST['company_id'];

    $company_email = $_POST['new_email'];


    $company_data = $Database->get_data('company_email', $company_email, 'company');

    if (!$company_data)

        $company_data = $Database->get_data('admin_email', $company_email, 'admin');

    if (!$company_data)

        $company_data = $Database->get_data('user_email', $company_email, 'user');


    if ($company_data)

        echo $trans->phrase('option_server_phrase76');

    else {

        $info = array(

            array('company_email', $company_email)

        );


        $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

        if ($updated) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase25");

    }

}

//Company get industry types

if (

    isset($_POST['sign']) && $_POST['sign'] == 'get_industry_types' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $industry_types = $Database->get_multiple_data(false, false, 'industry_type', null);

    if ($industry_types) {

        foreach ($industry_types as $industry_type) {

            $sql = "SELECT * FROM industry_content WHERE industry_id='" . $industry_type['industry_id'] . "' AND lang_code='" . $_SESSION['trans'] . "';";

            $industry_type_data = $Database->get_connection()->prepare($sql);

            $industry_type_data->execute();

            if ($industry_type_data->rowCount() < 1) {

                $industry_type_data = $industry_type_data->fetch(PDO::FETCH_ASSOC);

                $industry_type['industry_name'] = $industry_type_data['industry_name'];

                $industry_type['industry_details'] = $industry_type_data['industry_details'];

            }

        }


        echo json_encode($industry_types);

    } else {

        echo "";

    }

}


// Get industry content

//Company get industry types

if (

    isset($_POST['sign']) && $_POST['sign'] == 'get_industry_content' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $industry_type_data = $Database->get_industry_content();

    echo json_encode($industry_type_data);

}


//Change company industry

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_company_industry' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $company_id = $_POST['company_id'];

    $industry_type_id = $_POST['industry_id'];


    $info = array(

        array('company_industry_type', $industry_type_id)

    );


    $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($updated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase84");

}

//Company get package class

if (

    isset($_POST['sign']) && $_POST['sign'] == 'get_package_class' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $packages = $Database->get_multiple_data(false, false, 'package', null, true, 'package_size_min ASC', false);

    if ($packages) {

        $package_classes = array();

        $min_max = array('min' => array(), 'max' => array());

        foreach ($packages as $package) {

            $class_exist = false;

            if (!in_array($package['package_size_min'], $min_max['min'])) {

                array_push($min_max['min'], $package['package_size_min']);

                $class_exist = $class_exist | true;

            }

            if (!in_array($package['package_size_max'], $min_max['max'])) {

                array_push($min_max['max'], $package['package_size_max']);

                $class_exist = $class_exist | true;

            }

            if ($class_exist)

                array_push($package_classes, $package);

        }

        if (count($package_classes) > 0)

            echo json_encode($package_classes);

        else

            echo "";

    }

}

//Company package updater

if (

    isset($_POST['sign']) && $_POST['sign'] == 'package_updater' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {

    $min_max = json_decode($_POST['min_max'], true);


    $packages = $Database->get_multiple_data(false, false, 'package', null, true, 'package_price ASC', false);


    $options = array();


    if ($packages) {

        foreach ($packages as $package) {

            if ($package['package_size_min'] == $min_max['min'] && $package['package_size_max'] == $min_max['max']) {

                $package_name = $package['package_name'];

                $package_details = $package['package_details'];


                $sql = "SELECT * FROM package_content WHERE package_id={$package['package_id']} AND package_lang='{$_SESSION['trans']}'";

                $package_data = $Database->get_connection()->prepare($sql);

                $package_data->execute();

                if ($package_data->rowCount() < 1) $package_data = false;

                else $package_data = $package_data->fetch(PDO::FETCH_ASSOC);


                if ($package_data) {

                    $package_name = $package_data['package_name'];

                    $package_details = $package_data['package_details'];

                }


                $option = array(

                    "package_id" => $package['package_id'],

                    "name" => $package_name,

                    "price" => $package['package_price'],

                    "user" => $package['package_user'],

                    "details" => $package_details

                );


                array_push($options, $option);

            }

        }

    }


    echo json_encode($options);

}

//Update company package

if (

    isset($_POST['sign']) && $_POST['sign'] == 'company_package_update' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $package_id = $_POST['package_id'];

    $company_id = $_POST['company_id'];

    $company_user = false;


    $info = array(array('company_package_id', $package_id));

    if ($_SESSION['account-type'] == 'company') {

        array_push($info, array('company_status', 'pending'));

    }


    $updated = $Database->update_data($info, 'company_id', $company_id, 'company');


    if ($updated) {

        if ($_SESSION['account-type'] == 'company') {

            $company = $Database->get_data('company_id', $company_id, 'company', true);

            $attributes = array(

                array('site_url', SITE_URL),

                array('site_name', NAME),

                array('user_name', $company['company_name']),

                array('user_email', $company['company_email']),

                array('user_id', $company['company_id']),

                array('site_email', SITE_EMAIL),

                array('user_status', 'pending'),

                array('user_url', SITE_URL . "/user/index.php?route=company_profile&company_id=" . $company['company_id'])

            );


            $email_sender = new EmailSender();

            $body = $email_sender->load_template('company_plan_backoffice', $attributes);

            $send = $email_sender->send_mail(SITE_EMAIL, $trans->phrase('email_title_phrase10'), $body);

        }

        echo 'success';

    } else

        echo $trans->phrase('option_server_phrase77');

}

//Change company payment cycle

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_company_payment_cycle' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin' ||

        $_SESSION['account-type'] == 'company')

) {


    $company_id = $_POST['company_id'];

    $company_payment_cycle = $_POST['new_cycle'];

    $info = array(

        array('company_payment_cycle', $company_payment_cycle)

    );


    $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($updated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase26");

}

//Change company ticket view

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_company_ticket_view' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $company_id = $_POST['company_id'];

    $company_ticket_view = $_POST['ticket_view'];

    $info = array(

        array('company_show_tickets', $company_ticket_view)

    );


    $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($updated) {

        echo 'success';

    } else echo $trans->phrase("option_server_phrase62");

}

//Change company password by admin

if (

    isset($_POST['sign']) && $_POST['sign'] == 'change_company_password_admin' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $company_id = $_POST['company_id'];

    $new_pass = $_POST['new_pass'];


    $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);

    $info = array(

        array('company_password', $password_hash),

    );


    $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

    if ($updated) {

        echo $trans->phrase("option_server_phrase28");

    } else echo $trans->phrase("option_server_phrase29");

}

//Change company password by user

if (isset($_POST['sign']) && $_POST['sign'] == 'change_company_password' && $_SESSION['account-type'] == 'company') {


    $company_id = $_POST['company_id'];

    $new_pass = $_POST['new_pass'];

    $old_pass = $_POST['old_pass'];


    $company = $Database->get_data('company_id', $company_id, 'company', true);

    if (password_verify($old_pass, $company['company_password'])) {

        $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);

        $info = array(

            array('company_password', $password_hash),

        );


        $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

        if ($updated) {

            echo $trans->phrase("option_server_phrase30");

        } else echo $trans->phrase("option_server_phrase31");

    } else echo $trans->phrase("option_server_phrase32");

}

//Change company logo

if (isset($_POST['sign']) && $_POST['sign'] == 'update_company_logo' && $_SESSION['account-type'] == 'company') {
   // echo "hello";
   
    $value = array();

    if (isset($_FILES['logo'])) {

        $company_id = $_POST['company_id'];

        $file = $_FILES['logo'];

        $file_name_part = explode('.', $file['name']);

        $file_extension = strtolower(end($file_name_part));

        $file_type = array('image/jpeg', 'image/jpg', 'image/png');

        if ($file['size'] < 2097152 && in_array($file['type'], $file_type)) {

            array_map("unlink", glob('images/company_logo/' . $company_id . '.' . '*'));

            if(move_uploaded_file($file['tmp_name'], 'images/company_logo/' . $company_id . '.' . $file_extension)){

                $info = array(
                    array('upload_company_img', $company_id . '.' . $file_extension),
                );
    
                $updated = $Database->update_data($info, 'company_id', $company_id, 'company');

                //echo 'success';
                $value['success'] = 'success';
                $value['img'] = $company_id . '.' . $file_extension;
                $value['url'] = SITE_URL.'/images/company_logo/'.$company_id . '.' . $file_extension.'?v='.rand();
                
                echo json_encode($value);
            }


        } else {

            echo $trans->phrase("option_server_phrase33");

        }

    }

}

if (isset($_POST['sign']) && $_POST['sign'] == 'update_admin_logo' && $_SESSION['account-type'] == 'super_admin') {
 
   
    $value = array();

    if (isset($_FILES['logo'])) {

        $admin_id = $_POST['admin_id'];

        $file = $_FILES['logo'];

        $file_name_part = explode('.', $file['name']);

        $file_extension = strtolower(end($file_name_part));

        $file_type = array('image/jpeg', 'image/jpg', 'image/png');

        if ($file['size'] < 2097152 && in_array($file['type'], $file_type)) {

            array_map("unlink", glob('images/admin_logo/' . $admin_id . '.' . '*'));

            move_uploaded_file($file['tmp_name'], 'images/admin_logo/' . $admin_id . '.' . $file_extension);

            //echo 'success';
            $value['success'] = 'success';
            $value['img'] = $admin_id . '.' . $file_extension;
            
            echo json_encode($value);

        } else {

            echo $trans->phrase("option_server_phrase33");

        }

    }

}

if (isset($_POST['sign']) && $_POST['sign'] == 'update_report_logo' && $_SESSION['account-type'] == 'super_admin') {
 
   
    $value = array();

    if (isset($_FILES['logo'])) {

        $report_format_id = $_POST['report_format_id'];
        
        $lang_code = $_POST['lang_code'];

        $file = $_FILES['logo'];

        $file_name_part = explode('.', $file['name']);

        $file_extension = strtolower(end($file_name_part));

        $file_type = array('image/jpeg', 'image/jpg', 'image/png');

        if (in_array($file['type'], $file_type)) {

            array_map("unlink", glob('images/report_image/' . $report_format_id .'_' . $lang_code . '.' . '*'));
            array_map("unlink", glob('images/report_image/thumb_' . $report_format_id .'_' . $lang_code . '.' . '*'));

            move_uploaded_file($file['tmp_name'], 'images/report_image/' . $report_format_id .'_' . $lang_code . '.' . $file_extension);

            // Open the image file
            $image = imagecreatefromstring(file_get_contents('images/report_image/' . $report_format_id .'_' . $lang_code . '.' . $file_extension));

            // Resize the image
            $resizedImage = imagescale($image, 250, 350);

            // Save the resized image as a JPEG
            imagejpeg($resizedImage, 'images/report_image/thumb_' . $report_format_id .'_' . $lang_code . '.' . $file_extension, 100);

            // Output the resized image directly to the browser as a PNG
            header('Content-Type: image/jpeg');
            imagejpeg($resizedImage);


            //echo 'success';
            $value['success'] = 'success';
            $value['img'] = $report_format_id .'_' . $lang_code . '.' . $file_extension;
            
            echo json_encode($value);

        } else {

            echo $trans->phrase("option_server_phrase33");

        }

    }

}

if (isset($_POST['sign']) && $_POST['sign'] == 'update_consultant_logo' && $_SESSION['account-type'] == 'consultant') {
 
   
    $value = array();

    if (isset($_FILES['logo'])) {

        $consultant_id = $_POST['consultant_id'];

        $file = $_FILES['logo'];

        $file_name_part = explode('.', $file['name']);

        $file_extension = strtolower(end($file_name_part));

        $file_type = array('image/jpeg', 'image/jpg', 'image/png');

        if ($file['size'] < 2097152 && in_array($file['type'], $file_type)) {

            array_map("unlink", glob('images/consultant_logo/' . $consultant_id . '.' . '*'));

            move_uploaded_file($file['tmp_name'], 'images/consultant_logo/' . $consultant_id . '.' . $file_extension);

            //echo 'success';
            $value['success'] = 'success';
            $value['img'] = $consultant_id . '.' . $file_extension;
            
            echo json_encode($value);

        } else {

            echo $trans->phrase("option_server_phrase33");

        }

    }

}

//Company user delete

if (isset($_POST['sign']) && $_POST['sign'] == 'company_user_delete' && $_SESSION['account-type'] == 'company') {

    $user_id = $_POST['user_id'];


    $deleted = $Database->delete_data('user_id', $user_id, 'user');


    if ($deleted) {

        echo "success";

    } else {

        echo $trans->phrase("option_server_phrase34") . " $user_id";

    }

}


if (isset($_POST['sign']) && $_POST['sign'] == 'company_user_delete' && $_SESSION['account-type'] == 'super_admin') {

    $user_id = $_POST['user_id'];


    $deleted = $Database->delete_data('user_id', $user_id, 'user');


    if ($deleted) {

        echo "success";

    } else {

        echo $trans->phrase("option_server_phrase34") . " $user_id";

    }

}

//Company report text update

if (

    isset($_POST['sign']) && $_POST['sign'] == 'report_text_update' &&

    ($_SESSION['account-type'] == 'support_user' || $_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'company')

) {


    $report = htmlspecialchars($_POST['report_text'], ENT_QUOTES);

    $company_id = $_POST['company_id'];

    $lang_code = $_POST['lang_code'];

    $updated = false;


    $report_data = null;

    $text_info = $Database->get_multiple_data('text_company', $company_id, 'text');

    if ($text_info) {

        foreach ($text_info as $single_text) {

            if ($single_text['text_lang'] == $lang_code && $single_text['text_selector'] == 'general_report_text')

                $report_data = $single_text;

        }

    }

    if (isset($report_data) && $report_data) {

        $info = array(array('text_content', $report));

        $updated = $Database->update_data($info, 'text_id', $report_data['text_id'], 'text');

    } else {

        $info = array(

            array('text_selector', 'general_report_text'),

            array('text_content', $report),

            array('text_company', $company_id),

            array('text_lang', $lang_code)

        );


        $updated = $Database->write_data($info, 'text');

    }


    if ($updated)

        echo 'success';

    else

        echo $trans->phrase('option_server_phrase60');

}


//Language actions

//Add new language

if (isset($_POST['sign']) && $_POST['sign'] == 'add_new_language' && $_SESSION['account-type'] == 'super_admin') {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);

    $code = $_POST['code'];


    $validation = true;

    if (strlen($code) < 1 || strlen($code) > 4) {

        $validation = $validation & false;

    }

    if (strlen($name) < 1) {

        $validation = $validation & false;

    }


    if ($validation) {

        $info = array(

            array('lang_code', $code),

            array('lang_name', $name)

        );


        if ($Database->get_data('lang_code', $code, 'language')) {

            echo $trans->phrase("option_server_phrase35");

        } else {

            $written = $Database->write_data($info, 'language', true);


            if ($written) {

                copy('translation/en.json', 'translation/' . $code . '.json');

                directory_copy('email_templates/en', 'email_templates/' . $code);

                echo "success";

            } else echo $trans->phrase("option_server_phrase36");

        }

    } else {

        echo $trans->phrase("option_server_phrase37");

    }

}

//Delete language

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_language' && $_SESSION['account-type'] == 'super_admin') {

    $code = $_POST['code'];


    if ($Database->get_data('lang_code', $code, 'language')) {

        $deleted = $Database->delete_data('lang_code', $code, 'language');


        if ($deleted) {

            unlink('translation/' . $code . '.json');

            directory_delete('email_templates/' . $code);

            echo "success";

        } else echo $trans->phrase("option_server_phrase38");

    } else {

        echo $trans->phrase("option_server_phrase39");

    }

}

//Activate language

if (isset($_POST['sign']) && $_POST['sign'] == 'activate_language' && $_SESSION['account-type'] == 'super_admin') {

    $code = $_POST['code'];


    if ($Database->get_data('lang_code', $code, 'language')) {

        $info = array(

            array('lang_active', true)

        );

        $updated = $Database->update_data($info, 'lang_code', $code, 'language');


        if ($updated) {

            echo "success";

        } else echo $trans->phrase("option_server_phrase40");

    } else {

        echo $trans->phrase("option_server_phrase41");

    }

}

//Deactivate language

if (isset($_POST['sign']) && $_POST['sign'] == 'deactivate_language' && $_SESSION['account-type'] == 'super_admin') {

    $code = $_POST['code'];


    if ($Database->get_data('lang_code', $code, 'language')) {

        $info = array(

            array('lang_active', false)

        );

        $updated = $Database->update_data($info, 'lang_code', $code, 'language');


        if ($updated) {

            echo "success";

        } else echo $trans->phrase("option_server_phrase42");

    } else {

        echo $trans->phrase("option_server_phrase43");

    }

}

//Default language

if (isset($_POST['sign']) && $_POST['sign'] == 'default_language' && $_SESSION['account-type'] == 'super_admin') {

    $code = $_POST['code'];


    if ($Database->get_data('lang_code', $code, 'language')) {


        $info = array(

            array('lang_default', false)

        );

        $Database->update_data($info, 'lang_code', false, 'language');


        $info = array(

            array('lang_default', true)

        );

        $updated = $Database->update_data($info, 'lang_code', $code, 'language');


        if ($updated) {

            echo "success";

        } else echo $trans->phrase("option_server_phrase44");

    } else {

        echo $trans->phrase("option_server_phrase45");

    }

}

//Update language name

if (isset($_POST['sign']) && $_POST['sign'] == 'update_language_name' && $_SESSION['account-type'] == 'super_admin') {

    $lang_value = $_POST['lang_value'];

    $lang_code = $_POST['lang_code'];


    $translation = $Database->get_data('lang_code', $lang_code, 'language', true)['translations'];


    $trans_data = array();

    if ($translation) {

        $trans_data = json_decode($translation, true);

        $trans_data[$_SESSION['trans']] = $lang_value;

    } else {

        $trans_data = array(

            $_SESSION['trans'] => $lang_value

        );

    }


    //Saving translation data

    $info = array(array('translations', json_encode($trans_data)));

    if ($Database->update_data($info, 'lang_code', $lang_code, 'language')) {

        echo "success";

    } else {

        echo "Language update failed!";

    }

}

// Report Actions

//Add Category

if (isset($_POST['sign']) && $_POST['sign'] == 'add_report' && $_SESSION['account-type'] == 'super_admin') {

    $report_title = htmlspecialchars($_POST['new_report_name'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('status', 1)

    );


    $added = $Database->write_data($info, 'mlreport_format', false, true);


    if ($added !== false) {

        $info = array(

            array('report_format_id', $added),

            array('report_title', $report_title),

            array('report_lang_code', $lang_code)

        );
        
        
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if($languages){
            foreach($languages as $lang){
            $sql = "INSERT INTO mlreport_format_contentd SET report_format_id = '{$added}' , report_lang_code = '{$lang['lang_code']}' ";

            $method_data = $Database->get_connection()->prepare($sql);

            $method_data->execute();
      
        }
        }

        $content_added = $Database->write_data($info, 'mlreport_format_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase85");

    } else echo $trans->phrase("option_server_phrase86");

}

//Category Actions

//Add Category

if (isset($_POST['sign']) && $_POST['sign'] == 'add_category' && $_SESSION['account-type'] == 'super_admin') {

    $category_name = htmlspecialchars($_POST['category_name'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('category_name', $category_name)

    );


    $added = $Database->write_data($info, 'category', false, true);


    if ($added !== false) {

        $info = array(

            array('category_id', $added),

            array('category_name', $category_name),

            array('lang_code', $lang_code)

        );

        $content_added = $Database->write_data($info, 'category_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase85");

    } else echo $trans->phrase("option_server_phrase86");

}

//Delete category

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_category' && $_SESSION['account-type'] == 'super_admin') {

    $category_id = $_POST['category_id'];


    $deleted = $Database->delete_data('category_id', $category_id, 'category');

    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase87");

}

//Update category translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_translation_category' && $_SESSION['account-type'] == 'super_admin') {

    $category_id = $_POST['category_id'];

    $lang_code = $_POST['lang_code'];

    $category_name = htmlspecialchars($_POST['category_name'], ENT_QUOTES);

    $category_details = htmlspecialchars($_POST['category_details'], ENT_QUOTES);


    $info = array(

        array('category_id', $category_id),

        array('category_name', $category_name),

        array('category_details', $category_details),

        array('lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'category_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase88");

}

//Delete category

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_report_format' && $_SESSION['account-type'] == 'super_admin') {

    $report_format_id = $_POST['report_format_id'];


    $deleted = $Database->delete_data('report_format_id', $report_format_id, 'mlreport_format');
    
    $deleted_content = $Database->delete_data('report_format_id', $report_format_id, 'mlreport_format_content');
    
    $deleted_contentd = $Database->delete_data('report_format_id', $report_format_id, 'mlreport_format_contentd');

    if ($deleted && $deleted_content && $deleted_contentd)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase87");

}


//Update Report translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_translation_report' && $_SESSION['account-type'] == 'super_admin') {
    
    $image_name = $_POST['image_name'];

    $report_format_id = $_POST['report_format_id'];

    $lang_code = $_POST['lang_code'];

    $report_title = htmlspecialchars($_POST['report_title'], ENT_QUOTES);

    $report_desc = htmlspecialchars($_POST['report_desc'], ENT_QUOTES);


    $info = array(

        array('report_format_id', $report_format_id),

        array('report_title', $report_title),

        array('report_desc', $report_desc),
        
        array('report_image', $image_name),

        array('report_lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'mlreport_format_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase88");

}

//Update category rank

if (isset($_POST['sign']) && $_POST['sign'] == 'update_category_rank' && $_SESSION['account-type'] == 'super_admin') {

    $categories = json_decode($_POST['categories'], true);

    $updated = true;


    for ($x = 0; $x < count($categories); $x++) {

        $info = array(array('category_rank', $x));

        if ($Database->update_data($info, 'category_id', $categories[$x], 'category')) {

            $updated = $updated & true;

        } else {

            $updated = $updated & false;

        }

    }


    if ($updated) {

        echo "Category rank updated successfully!";

    } else {

        echo "System error while updating category rank.";

    }

}

//Report Format Actions

//Add Report Format

if (isset($_POST['sign']) && $_POST['sign'] == 'add_report_format' && $_SESSION['account-type'] == 'super_admin') {

    $report_format_name = htmlspecialchars($_POST['report_format_name'], ENT_QUOTES);
    $report_format_description = htmlspecialchars($_POST['report_format_description'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('report_format_name', $report_format_name),
        array('report_format_description', $report_format_description),
        array('report_format_image_url', ""),
        array('report_content', ""),
        array('lang_code', $lang_code)

    );


    $added = $Database->write_data($info, 'report_format', false, true);


    if ($added !== false) {

        // $info = array(

        //     array('category_id', $added),

        //     array('category_name', $category_name),

        //     array('lang_code', $lang_code)

        // );

        // $content_added = $Database->write_data($info, 'category_content', true, false);

        // if ($content_added) {

            echo 'success';

        // } else echo $trans->phrase("option_server_phrase85");

    } else echo "false";//$trans->phrase("option_server_phrase86");

}


//Add Report Format

if (isset($_POST['sign']) && $_POST['sign'] == 'add_mreport_format' && $_SESSION['account-type'] == 'super_admin') {
    echo "yes1";
    die();

    $report_format_name = htmlspecialchars($_POST['report_format_name'], ENT_QUOTES);
    $report_format_description = htmlspecialchars($_POST['report_format_description'], ENT_QUOTES);

    $lang_code = $_POST['report_lang'];
    
    $info_mformat = array(
        array('status', 1)
    );
    
    $report_format_id = $Database->write_data($info_mformat, 'mreport_format', false, true);


    $info = array(
        array('report_fromat_id', $report_format_id),
        array('report_title', $report_format_name),
        array('report_desc', $report_format_description),
        array('report_content', ""),
        array('report_lang', $lang_code)

    );


    $added = $Database->write_data($info, 'multi_lang_report_fromat', false, true);


    if ($added !== false) {


    } else echo "false";//$trans->phrase("option_server_phrase86");

}


//Industry Actions

//Add Industry type

if (isset($_POST['sign']) && $_POST['sign'] == 'add_industry' && $_SESSION['account-type'] == 'super_admin') {

    $industry_name = htmlspecialchars($_POST['industry_name'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('industry_name', $industry_name)

    );


    $added = $Database->write_data($info, 'industry_type', false, true);


    if ($added !== false) {

        $info = array(

            array('industry_id', $added),

            array('industry_name', $industry_name),

            array('lang_code', $lang_code)

        );

        $content_added = $Database->write_data($info, 'industry_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase80");

    } else echo $trans->phrase("option_server_phrase81");

}

//Delete industry type

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_industry' && $_SESSION['account-type'] == 'super_admin') {

    $industry_id = $_POST['industry_id'];


    $deleted = $Database->delete_data('industry_id', $industry_id, 'industry_type');

    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase82");

}

//Update industry type translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_translation_industry' && $_SESSION['account-type'] == 'super_admin') {

    $industry_id = $_POST['industry_id'];

    $lang_code = $_POST['lang_code'];

    $industry_name = htmlspecialchars($_POST['industry_name'], ENT_QUOTES);

    $industry_details = htmlspecialchars($_POST['industry_details'], ENT_QUOTES);


    $info = array(

        array('industry_id', $industry_id),

        array('industry_name', $industry_name),

        array('industry_details', $industry_details),

        array('lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'industry_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase83");

}


//Method Actions

//Add method

if (isset($_POST['sign']) && $_POST['sign'] == 'add_method' && $_SESSION['account-type'] == 'super_admin') {

    $method_name = htmlspecialchars($_POST['method_name'], ENT_QUOTES);

    $method_company = $_POST['company_id'];

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('method_restriction', '')

    );

    if ($method_company)

        array_push($info, array('method_company_id', $method_company));


    $added = $Database->write_data($info, 'method', false, true);


    if ($added !== false) {

        $info = array(

            array('method_id', $added),

            array('method_name', $method_name),

            array('lang_code', $lang_code)

        );

        $content_added = $Database->write_data($info, 'method_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase46");

    } else echo $trans->phrase("option_server_phrase47");

}

//Get methods

if (isset($_POST['sign']) && $_POST['sign'] == 'get_methods' && $_SESSION['account-type'] == 'super_admin') {

    $company_id = $_POST['company_id'];

    $result = array();


    $methods = $Database->get_multiple_data(false, false, 'method');

    if ($methods) {

        foreach ($methods as $method) {

            if ($method['method_company_id'] && $method['method_company_id'] != $company_id)

                continue;

            $restriction_array = explode(',', $method['method_restriction']);

            if ($company_id && in_array($company_id, $restriction_array)) continue;


            $method_info;

            $method_data = $Database->get_multiple_data('method_id', $method['method_id'], 'method_content');

            foreach ($method_data as $data) {

                $method_info = $data;

                if ($data['lang_code'] == $_SESSION['trans']) break;

            }

            $method_array = array(

                "method_id" => $method['method_id'],

                "method_name" => $method_info['method_name'],

                "method_company_id" => $method['method_company_id'],

            );

            array_push($result, $method_array);

        }

    }

    echo json_encode($result);

}

//Update basic method info

if (isset($_POST['sign']) && $_POST['sign'] == 'update_basic_method' && $_SESSION['account-type'] == 'super_admin') {

    $method_id = $_POST['method_id'];

    $company_id = $_POST['company_id'];

    $restriction = $_POST['restriction'];

    $method_color = $_POST['method_color'];


    if (strlen($company_id) < 1) $company_id = NULL;

    if (strlen($restriction) < 1) $restriction = NULL;


    $info = array(

        array('method_color', $method_color),

        array('method_restriction', $restriction),

        array('method_company_id', $company_id)

    );


    $updated = $Database->update_data($info, 'method_id', $method_id, 'method');

    if ($updated) echo "success";

    else echo $trans->phrase("option_server_phrase48");

}

//Delete method

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_method' && $_SESSION['account-type'] == 'super_admin') {

    $method_id = $_POST['method_id'];


    $deleted = $Database->delete_data('method_id', $method_id, 'method');

    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase49");

}

//Update basic method translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_translation_method' && $_SESSION['account-type'] == 'super_admin') {

    $method_id = $_POST['method_id'];

    $lang_code = $_POST['lang_code'];

    $method_name = htmlspecialchars($_POST['method_name'], ENT_QUOTES);

    $method_details = htmlspecialchars($_POST['method_details'], ENT_QUOTES);


    $info = array(

        array('method_id', $method_id),

        array('method_name', $method_name),

        array('method_details', $method_details),

        array('lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'method_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase50");

}


//Question Actions

//Add question

if (isset($_POST['sign']) && $_POST['sign'] == 'add_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_name = htmlspecialchars($_POST['question_name'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('question_tip_on_yes', 0),

        array('question_tip_on_no', 0)

    );


    $added = $Database->write_data($info, 'question', false, true);


    if ($added !== false) {

        $info = array(

            array('question_id', $added),

            array('question_name', $question_name),

            array('lang_code', $lang_code)

        );

        $content_added = $Database->write_data($info, 'question_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase51");

    } else echo $trans->phrase("option_server_phrase52");

}

//Update question type

if (isset($_POST['sign']) && $_POST['sign'] == 'update_question_type' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $question_type = $_POST['question_type'];


    $info = array(

        array('question_type', $question_type)

    );


    $updated = $Database->update_data($info, 'question_id', $question_id, 'question');

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase89");

}

//Update question follow-up

if (isset($_POST['sign']) && $_POST['sign'] == 'update_question_follow_up' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $question_follow_up = $_POST['question_follow_up'];


    $info = array(

        array('question_follow_up', $question_follow_up),

        array('category_id', NULL)

    );


    $updated = $Database->update_data($info, 'question_id', $question_id, 'question');

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase89");

}

//Delete question

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];


    $deleted = $Database->delete_data('question_id', $question_id, 'question');

    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase53");

}

//Update basic question info

if (isset($_POST['sign']) && $_POST['sign'] == 'update_question_basic' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $category_id = $_POST['category_id'];

    $yes_response = $_POST['yes_response'];

    $no_response = $_POST['no_response'];

    $question_activate_tip_yes = $_POST['question_activate_tip_yes'];

    $question_activate_tip_no = $_POST['question_activate_tip_no'];

    $question_follow_up_yes = $_POST['question_follow_up_yes'];

    $question_follow_up_no = $_POST['question_follow_up_no'];

    $question_weight_yes = $_POST['question_weight_yes'];

    $question_weight_no = $_POST['question_weight_no'];

    $company_id = $_POST['company_id'];


    if (!$category_id) $category_id = NULL;

    if (!$yes_response) $yes_response = NULL;

    if (!$no_response) $no_response = NULL;

    if (!$question_follow_up_yes) $question_follow_up_yes = NULL;

    if (!$question_follow_up_no) $question_follow_up_no = NULL;


    $info1 = array(

        array('access_question_id', $question_id),

        array('access_yes', $yes_response),

        array('access_no', $no_response)

    );


    $info2 = array(

        array('category_id', $category_id),

        array('question_tip_on_yes', $question_activate_tip_yes),

        array('question_tip_on_no', $question_activate_tip_no),

        array('question_yes_follow_up', $question_follow_up_yes),

        array('question_no_follow_up', $question_follow_up_no),

        array('question_weight_yes', $question_weight_yes),

        array('question_weight_no', $question_weight_no)

    );


    $access_id = null;

    $access_data = $Database->get_multiple_data('access_question_id', $question_id, 'question_method');

    if ($access_data) {

        foreach ($access_data as $data) {

            if (!$data['access_company_id']) {

                $access_id = $data['access_id'];

                break;

            }

        }

    }


    if (strlen($company_id) > 0) {

        array_push($info1, array('access_company_id', $company_id));


        $access_id = null;

        if ($access_data) {

            foreach ($access_data as $data) {

                if ($data['access_company_id'] == $company_id) {

                    $access_id = $data['access_id'];

                    break;

                }

            }

        }

    }


    $write = false;

    if ($access_id) {

        $write = $Database->update_data($info1, 'access_id', $access_id, 'question_method');

    } else {

        $write = $Database->write_data($info1, 'question_method', false);

    }


    $updated = $Database->update_data($info2, 'question_id', $question_id, 'question');

    if ($write && $updated) echo "success";

    else echo $trans->phrase("option_server_phrase54");

}

//Update question translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_translation_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $lang_code = $_POST['lang_code'];

    $question_name = htmlspecialchars($_POST['question_name'], ENT_QUOTES);

    $question_tips_yes = htmlspecialchars($_POST['question_tips_yes'], ENT_QUOTES);

    $question_tips_no = htmlspecialchars($_POST['question_tips_no'], ENT_QUOTES);

    $question_option1 = htmlspecialchars($_POST['question_option1'], ENT_QUOTES);

    $question_option2 = htmlspecialchars($_POST['question_option2'], ENT_QUOTES);

    $question_option3 = htmlspecialchars($_POST['question_option3'], ENT_QUOTES);

    $question_option4 = htmlspecialchars($_POST['question_option4'], ENT_QUOTES);

    $question_option5 = htmlspecialchars($_POST['question_option5'], ENT_QUOTES);
    
    $question_option6 = htmlspecialchars($_POST['question_option6'], ENT_QUOTES);


    $info = array(

        array('question_id', $question_id),

        array('question_name', $question_name),

        array('question_tips_yes', $question_tips_yes),

        array('question_tips_no', $question_tips_no),

        array('question_option1', $question_option1),

        array('question_option2', $question_option2),

        array('question_option3', $question_option3),

        array('question_option4', $question_option4),

        array('question_option5', $question_option5),
        
        array('question_option6', $question_option6),

        array('lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'question_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase55");

}


//Generate dynamic method as user answer the question

if (isset($_POST['sign']) && $_POST['sign'] == 'generate_dynamic_method' && $_SESSION['account-type'] == 'user') {

    $response = $_POST['response'];


    $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);

    $method_selection = $Database->get_multiple_data(false, false, 'question_method');

    $resp_array = json_decode($response, true);


    //Generate methods

    $method_array = array();

    $methods = $Database->get_multiple_data(false, false, 'method');

    if ($methods) {

        foreach ($methods as $method) {

            $method_array[$method['method_id']] = 0;

        }

    }


    foreach ($resp_array as $question_id => $resp) {

        $access = null;

        foreach ($method_selection as $selection) {

            if (

                $selection['access_question_id'] == $question_id &&

                $selection['access_company_id'] == $user_data['user_company_id']

            ) {


                $access = $selection;

                break;

            } else if ($selection['access_question_id'] == $question_id) {

                $access = $selection;

            }

        }


        if (!$access) continue;

        else {

            if ($resp['type'] == 'yes-no') {

                if ($resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            } else if ($resp['type'] == 'mcq') {

                if ($resp['answer'] == 4 || $resp['answer'] == 4) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1 || $resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            }

        }

    }


    arsort($method_array);

    $result = array();


    if ($method_array) {

        $total_selection = 0;

        foreach ($method_array as $method_key => $active_count) {

            $total_selection += $active_count;

        }

        foreach ($method_array as $method_key => $active_count) {

            if ($active_count == 0) continue;


            $sql = "SELECT * FROM method_content WHERE method_id={$method_key} AND lang_code='{$_SESSION['trans']}'";

            $method_data = $Database->get_connection()->prepare($sql);

            $method_data->execute();

            if ($method_data->rowCount() < 1) $method_data = false;

            else $method_data = $method_data->fetch(PDO::FETCH_ASSOC);


            if ($method_data) {

                $decoded_method_details = htmlspecialchars_decode($method_data['method_details'], ENT_QUOTES);

                $single_method = array(

                    "method_name" => $method_data['method_name'],

                    "method_details" => $decoded_method_details,

                    "method_percentage" => (int)(($active_count / $total_selection) * 100)

                );


                array_push($result, $single_method);

            }

        }

    }


    echo json_encode($result);

}

// res1
if (isset($_POST['sign']) && $_POST['sign'] == 'res_form' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {
    $responder_email = htmlspecialchars($_POST['email'], ENT_QUOTES);
    $res_ticket_id = $_POST['res_ticket_id'];
    $companyName = $_POST['companyName'];

    $reportPermission = new ReportPermission($res_ticket_id);
    $canInviteResponder = $reportPermission->canInviteResponder();

    if(!$reportPermission->canChangeReportFormat()){
        
        echo json_encode(['status' => 'error', 'message' => $trans->phrase('option_server_phrase97')]);
        exit;

    }

    if($canInviteResponder['status'] == false){
        
        echo json_encode(['status' => 'error', 'message' => $canInviteResponder['msg']]);
        exit;

    }

    $info = array(

        array('user_id', $_SESSION['account-id']),

        array('ticket_id', $res_ticket_id),

    );


    $tbl_ticket_responder_id = $Database->write_data($info, 'tbl_ticket_responder', false, true);
    
    if ($tbl_ticket_responder_id) {
		
        $subject = $trans->phrase("invite_subject");
        
        $link = SITE_URL.'/custom.php?route=res_question&page=responder&ticket_id='. $res_ticket_id .'&responder_id='.$tbl_ticket_responder_id;

        $emailTemplate = $trans->phrase("invite_message");

        $emailContent = str_replace('{{_CompanyName_}}', $companyName, $emailTemplate);
        $emailContent = str_replace('{{_AppLogo_}}', "https://beta.nogd.no/images/responder_logo.png", $emailContent);
        $emailContent = str_replace('{{_AppName_}}', APP_NAME, $emailContent);
        $emailContent = str_replace('{{_link_}}', $link, $emailContent);

        $email_sender = new EmailSender();

        $send = $email_sender->send_mail($responder_email, $subject, $emailContent);

     if($send);

        $result = array('status' => 'success', 'url' => SITE_URL.'/custom.php?route=res_question&page=responder&ticket_id='. $res_ticket_id .'&responder_id='.$tbl_ticket_responder_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => 'responser not added!');

        echo json_encode($result);

    }    
}


// Invite Bulk responder with excel
if (isset($_POST['sign']) && $_POST['sign'] == 'bulk_res_form' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {

    $res_ticket_id = $_POST['res_ticket_id'];
    $companyName = $_POST['companyName'];

    $reportPermission = new ReportPermission($res_ticket_id);
    $canInviteResponder = $reportPermission->canInviteResponder();

    if(!$reportPermission->canChangeReportFormat()){
        
        echo json_encode(['status' => 'error', 'message' => $trans->phrase('option_server_phrase97')]);
        exit;

    }

    if($canInviteResponder['status'] == false){
        
        echo json_encode(['status' => 'error', 'message' => $canInviteResponder['msg']]);
        exit;

    }

    require 'vendor/autoload.php'; // PhpSpreadsheet and PHPMailer libraries

    // Check if the form has been submitted
    if (isset($_FILES['multiple_responder'])) {

        // Get the uploaded file information
        $fileTmpName = $_FILES['multiple_responder']['tmp_name'];
        $fileName = $_FILES['multiple_responder']['name'];
        $fileSize = $_FILES['multiple_responder']['size'];
        $fileError = $_FILES['multiple_responder']['error'];
        $fileType = $_FILES['multiple_responder']['type'];

        // Check if the file is an Excel file
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('xls', 'xlsx');

        if (in_array($fileActualExt, $allowed)) {

            if ($fileError === 0) {

                if ($fileSize < 1000000) {

                    // Create a new PhpSpreadsheet objReader object based on the file extension
                    if ($fileActualExt == 'xlsx') {
                        $objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    } else {
                        $objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    }

                    // $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($fileTmpName);

                    
                    // Get the active sheet
                    $sheet = $objPHPExcel->getActiveSheet();

                    // Get the highest row number and column letter
                    $highestRow = $sheet->getHighestDataRow();
                    $highestColumn = $sheet->getHighestDataColumn();

                    $sentEmails = [];

                    for ($row = 2; $row <= $highestRow; $row++) {
                        $email = $sheet->getCell('A'.$row)->getValue();

                        // the email address is valid, add it to the email list
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $info = array(

                                array('user_id', $_SESSION['account-id']),
                        
                                array('ticket_id', $res_ticket_id),
                        
                            );
                        
                            $tbl_ticket_responder_id = $Database->write_data($info, 'tbl_ticket_responder', false, true);
                            
                            if ($tbl_ticket_responder_id) {
                                $subject = $trans->phrase("invite_subject");
        
                                $link = SITE_URL.'/custom.php?route=res_question&page=responder&ticket_id='. $res_ticket_id .'&responder_id='.$tbl_ticket_responder_id;
                        
                                $emailTemplate = $trans->phrase("invite_message");
                        
                                $emailContent = str_replace('{{_CompanyName_}}', $companyName, $emailTemplate);
                                $emailContent = str_replace('{{_AppLogo_}}', "https://beta.nogd.no/images/responder_logo.png", $emailContent);
                                $emailContent = str_replace('{{_AppName_}}', APP_NAME, $emailContent);
                                $emailContent = str_replace('{{_link_}}', $link, $emailContent);
                        
                                $email_sender = new EmailSender();
                        
                                $send = $email_sender->send_mail($email, $subject, $emailContent);
    
                                if ($send) {
                                    $sentEmails[] = array('email' => $email, 'status' => 'Sent');
                                } else {
                                    $sentEmails[] = array('email' => $email, 'status' => 'Error: '.$mail->ErrorInfo);
                                }

                            } else {
                                $sentEmails[] = array('email' => $email, 'status' => 'Unable to Process, Try again.');
                            }
                        }
                    }
                    
                    echo json_encode(array('status' => 'success', 'message' => $sentEmails));
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'File size is too large.']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'An error occurred while uploading the file.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only Excel files are allowed.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file was uploaded.']);
        exit;
    }  
}

//Create ticket for user


if (isset($_POST['sign']) && $_POST['sign'] == 'request_ticket_type_relation' && $_SESSION['account-type'] == 'consultant') {

    $request_ticket_relation_type = $_POST['request_ticket_relation_type'];

    $child_categories = array();

    if($request_ticket_relation_type == 'user'){

        $sql = "SELECT * FROM user";

        $child_categories['type'] = 'user';

    } else {

        $sql = "SELECT * FROM company";

        $child_categories['type'] = 'company';
    }

    $result = $Database->get_connection()->prepare($sql);

    $result->execute();

    $data = array(); // Initialize an array to store the retrieved data

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

        if($request_ticket_relation_type == 'user'){
            $data[] = array(
                'id' => $row['user_id'],
                'name' => $row['user_name'],
            );
        } else {
            $data[] = array(
                'id' => $row['company_id'],
                'name' => $row['company_name'],
            );
        }
    }

    $child_categories['data'] = $data;

    echo json_encode($child_categories);
}


if (isset($_POST['sign']) && $_POST['sign'] == 'create_ticket' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {

    $ticket_name = htmlspecialchars($_POST['ticket_name'], ENT_QUOTES);

    $ticket_summary = htmlspecialchars($_POST['ticket_summary'], ENT_QUOTES);

    if(!isset($_POST['_reqId']) || empty($_POST['_reqId'])){
        $result = array('status' => 'error', 'message' => 'Invalid Request');

        echo json_encode($result);
        exit;

    } else if (isset($_POST['_reqId']) || !empty($_POST['_reqId'])) {

        $query = $Database->get_data('report_format_id', $_POST['_reqId'], 'mlreport_format', true);

        if(!$query){
            $result = array('status' => 'error', 'message' => 'Invalid Report Request');
    
            echo json_encode($result);
            exit;
        }

    }

    $response = NULL;

    $generated_method = NULL;

    $infoAddOn = array();

    $notifications = array();

    /** Creating Ticket */
    if($_SESSION['account-type'] == 'consultant'){

        if(!isset($_POST['ticketType'])){
            $result = array('status' => 'error', 'message' => 'Invalid request.');

            echo json_encode($result);
            exit;
        }

        $createdUser = isset($_POST['userTicket']) ? $_POST['userTicket'] : null;

        $createdCompany = isset($_POST['companyTicket']) ? $_POST['companyTicket'] : null;

        if(isset($_POST['userTicket'])) {

            $notifications = array(
                'ref_id' => $_POST['userTicket'],
                'ref_type' => 'user',
                'en' => 'New analysis has been created',
                'nor' => 'Ny analyse er opprettet'
            );

        }

        if(isset($_POST['companyTicket'])) {

            $notifications = array(
                'ref_id' => $_POST['companyTicket'],
                'ref_type' => 'company',
                'en' => 'New analysis has been created',
                'nor' => 'Ny analyse er opprettet'
            );

        }

        $infoAddOn = array(

            array('ticket_consultant_id', $_SESSION['account-id']),

            array('ticket_user_id', $createdUser),

            array('ticket_company_id', $createdCompany),

            array('Created_by', 'consultant'),

            array('Created_to', $_POST['ticketType']),

        );

    } else 
    
    if($_SESSION['account-type'] == 'company'){

        if(isset($_POST['userTicket'])){

            $infoAddOn = array(

                array('ticket_user_id', $_POST['userTicket']),
    
                array('ticket_company_id', $_SESSION['account-id']),
    
                array('Created_by', 'company'),
    
                array('Created_to', 'user'),
    
            );

            
            if(isset($_POST['userTicket'])) {

                $notifications = array(
                    'ref_id' => $_POST['userTicket'],
                    'ref_type' => 'user',
                    'en' => 'New analysis has been created.',
                    'nor' => 'Ny analyse er opprettet'
                );

            }

        } else {

            $infoAddOn = array(
    
                array('ticket_company_id', $_SESSION['account-id']),
    
                array('Created_by', 'company'),
    
                array('Created_to', 'company'),
    
            );

            $notifications = array(
                'ref_id' => $_SESSION['account-id'],
                'ref_type' => 'company',
                'en' => 'New analysis has been created.',
                'nor' => 'Ny analyse er opprettet'
            );

        }

    } else {
        $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);

        $infoAddOn = array(

            array('ticket_user_id', $_SESSION['account-id']),

            array('ticket_company_id', $user_data['user_company_id']),

            array('Created_by', 'user'),

            array('Created_to', 'company'),

        );

        $notifications = array(
            'ref_id' => $_SESSION['account-id'],
            'ref_type' => 'user',
            'en' => 'New analysis has been created.',
            'nor' => 'Ny analyse er opprettet'
        );
    }

    $info = array(
        array('ticket_name', $ticket_name),

        array('ticket_summary', $ticket_summary),

        array('ticket_response', $response),

        array('ticket_methods', $generated_method),

        array('ticket_status', 'process')

    );

    $infoAll = array_merge($info, $infoAddOn);

    $ticket_id = $Database->write_data($infoAll, 'ticket', false, true);

    if ($ticket_id) {

        /** Creating Ticket Report Type */
        if($_SESSION['account-type'] == 'user') {
            $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
            
            $infoReportType = array(

                array('user_id', $user_data['user_id']),
        
                array('report_id', $_POST['_reqId']),
        
                array('request_date_time', date('Y-m-d h:i:s'))
        
            );
        } else

        if($_SESSION['account-type'] == 'company') {
            $company_data = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);
            
            $infoReportType = array(

                array('company_id', $company_data['company_id']),
        
                array('report_id', $_POST['_reqId']),
        
                array('request_date_time', date('Y-m-d h:i:s'))
        
            );
        } else

        if($_SESSION['account-type'] == 'consultant') {
            $consultant_data = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
            
            $infoReportType = array(

                array('consultancy_id', $consultant_data['consultant_id']),
        
                array('report_id', $_POST['_reqId']),
        
                array('request_date_time', date('Y-m-d h:i:s'))
        
            );
        }

        $infoReportType[] = array('ticket_id', $ticket_id);

        $reportType = $Database->write_data($infoReportType, 'tbl_report_request', false, true);

        if($reportType){

            $Database->createNotificationForUser($notifications['ref_id'], $notifications['ref_type'], $notifications);

            $result = array('status' => 'success', 'ticket_id' => $ticket_id);
    
            echo json_encode($result);

        } else {
            $Database->delete_data('ticket_id', $ticket_id,'ticket'); 
            
            $result = array('status' => 'error', 'message' => 'Something went wrong');

            echo json_encode($result);
        }

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

        echo json_encode($result);

    }

}

//request form 

if (isset($_POST['sign']) && $_POST['sign'] == 'request_form' && $_SESSION['account-type'] == 'user') {

    $response = NULL;
    $report_id =  $_POST['request_form_id'];
    $permisson_ticket_title =  $_POST['permisson_ticket_title'];
    $permission_by  =  $_POST['permission_by'];
    $generated_method = NULL;


    $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
  

    $info = array(

        array('user_id', $user_data['user_id']),
        
        array('permisson_ticket_title', $permisson_ticket_title),

        array('company_id', $user_data['user_company_id']),
        array('consultancy_id', $user_data['user_consultancy_id']),

        array('report_id', $report_id),

        array('permission_by', $permission_by),

        array('request_date_time', date('Y-m-d h:i:s')),

        array('status', '0')

    );


    $ticket_id = $Database->write_data($info, 'tbl_report_request', false, true);


    if ($ticket_id) {
       
        $notifications = array(
            'en' => 'User requested ticket a report request!',
            'nor' => 'Bruker sendte en rapportforespørsel!'
        );

        $Database->createNotificationForUser($user_data['user_company_id'], 'company', $notifications);
        
        $result = array('status' => 'success', 'ticket_id' => $ticket_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

        echo json_encode($result);

    }

}


/** Remove 001 this one Start here  */
/*
if (isset($_POST['sign']) && $_POST['sign'] == 'request_na_form' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {


    $report_id =  $_POST['request_form_id'];


    if($_SESSION['account-type'] == 'user') {
        $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
        
        $info = array(

            array('user_id', $user_data['user_id']),
     
            array('report_id', $report_id),
    
            array('request_date_time', date('Y-m-d h:i:s'))
    
        );
    }
  
    if($_SESSION['account-type'] == 'company') {
        $company_data = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);
        
        $info = array(

            array('company_id', $company_data['company_id']),
     
            array('report_id', $report_id),
    
            array('request_date_time', date('Y-m-d h:i:s'))
    
        );
    }
  
    if($_SESSION['account-type'] == 'consultant') {
        $consultant_data = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
        
        $info = array(

            array('consultancy_id', $consultant_data['consultant_id']),
     
            array('report_id', $report_id),
    
            array('request_date_time', date('Y-m-d h:i:s'))
    
        );
    }

    $ticket_id = $Database->write_data($info, 'tbl_report_request', false, true);


    if ($ticket_id) {

        $result = array('status' => 'success', 'req_id' => $ticket_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

        echo json_encode($result);

    }

}
*/
/** Remove 001 this one End here  */


//Update ticket summary

if (

    isset($_POST['sign'])

    && $_POST['sign'] == 'update_ticket_summary'

    && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')

) {

    $ticket_id = htmlspecialchars($_POST['ticket_id'], ENT_QUOTES);

    $ticket_name = htmlspecialchars($_POST['ticket_name'], ENT_QUOTES);

    $ticket_summary = htmlspecialchars($_POST['ticket_summary'], ENT_QUOTES);

    $response = NULL;

    $generated_method = NULL;

    // $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);


    $info = array(

        array('ticket_id', $ticket_id),

        array('ticket_name', $ticket_name),

        array('ticket_summary', $ticket_summary)

    );


    $updated = $Database->update_data($info, 'ticket_id', $ticket_id, 'ticket');


    if ($updated) {

        $result = array('status' => 'success', 'ticket_id' => $ticket_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

        echo json_encode($result);

    }

}


//Save ticket

if (isset($_POST['sign']) && $_POST['sign'] == 'save_ticket' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {

    $ticket_name = htmlspecialchars($_POST['ticket_name'], ENT_QUOTES);

    $response = $_POST['response'];

    $ticket_id = $_POST['ticket_id'];

    $updated = true;


    $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);

    $method_selection = $Database->get_multiple_data(false, false, 'question_method');


    $resp_array = json_decode($response, true);


    //Generate methods

    $method_array = array();

    $methods = $Database->get_multiple_data(false, false, 'method');

    if ($methods) {

        foreach ($methods as $method) {

            $method_array[$method['method_id']] = 0;

        }

    }


    foreach ($resp_array as $question_id => $resp) {

        $access = null;

        foreach ($method_selection as $selection) {

            if ($selection['access_question_id'] == $question_id) {

                $access = $selection;

            }

        }


        if (!$access)

            continue;

        else {

            if ($resp['type'] == 'yes-no') {

                if ($resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            } else if ($resp['type'] == 'mcq') {

                if ($resp['answer'] == 4 || $resp['answer'] == 4) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1 || $resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            }

        }

    }


    arsort($method_array);

    $generated_method = json_encode($method_array);


    $info = array(

        // array('ticket_user_id', $user_data['user_id']),

        // array('ticket_company_id', $user_data['user_company_id']),

        array('ticket_name', $ticket_name),

        array('ticket_response', $response),

        array('ticket_methods', $generated_method),

        array('last_modified_time', date("Y-m-d h:i:s", time()))

        //array('ticket_status', 'process')

    );


    // if ($ticket_id > 0) {

        $updated = $Database->update_data($info, 'ticket_id', $ticket_id, 'ticket');

    // } else {

    //     // $ticket_id = $Database->write_data($info, 'ticket', false, true);
    //     $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

    //     echo json_encode($result);

    // }


    if ($ticket_id && $updated) {

        $result = array('status' => 'success', 'ticket_id' => $ticket_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

        echo json_encode($result);

    }

}


//Submit ticket and generate

if (isset($_POST['sign']) && $_POST['sign'] == 'submit_report' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {

    $ticket_name = htmlspecialchars($_POST['ticket_name'], ENT_QUOTES);

    $response = $_POST['response'];

    $ticket_id = $_POST['ticket_id'];

    $updated = true;


    // $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);

    $method_selection = $Database->get_multiple_data(false, false, 'question_method');


    $resp_array = json_decode($response, true);


    //Check completeness

    $total = 0;

    $answered = 0;

    foreach ($resp_array as $question_id => $resp) {

        if (!$resp['follow-up']) {

            $follow_up_type = false;

            if ((int)$resp['answer'] > 0) {

                $answered++;

            }

            if ($resp['type'] == 'mcq') {

                if ($resp['answer'] == 1 || $resp['answer'] == 2)

                    $follow_up_type = 'no';

                elseif ($resp['answer'] == 4 || $resp['answer'] == 5)

                    $follow_up_type = 'yes';

            }

            if ($resp['type'] == 'yes-no') {

                if ($resp['answer'] == 1)

                    $follow_up_type = 'no';

                elseif ($resp['answer'] == 2)

                    $follow_up_type = 'yes';

            }


            if ($follow_up_type == 'yes') {

                $follow_up_id = (int)$resp['yes-follow-up'];

                if ($follow_up_id) {

                    if ((int)($resp_array[$follow_up_id]['answer']) > 0) {

                        $answered++;

                    }

                    $total++;

                }

            }

            if ($follow_up_type == 'no') {

                $follow_up_id = (int)$resp['no-follow-up'];

                if ($follow_up_id) {

                    if ((int)$resp_array[$follow_up_id]['answer'] > 0) {

                        $answered++;

                    }

                    $total++;

                }

            }


            $total++;

        }

    }


    $completeness = ($answered / $total) * 100;


    //Generate methods

    $method_array = array();

    $methods = $Database->get_multiple_data(false, false, 'method');

    if ($methods) {

        foreach ($methods as $method) {

            $method_array[$method['method_id']] = 0;

        }

    }


    foreach ($resp_array as $question_id => $resp) {

        $access = null;

        foreach ($method_selection as $selection) {
            /*
            if (

                $selection['access_question_id'] == $question_id &&

                $selection['access_company_id'] == $user_data['user_company_id']

            ) {


                $access = $selection;

                break;

            } else if ($selection['access_question_id'] == $question_id) {

                $access = $selection;

            }
            */
            
            if ($selection['access_question_id'] == $question_id) {

                $access = $selection;

            }
        }


        if (!$access)

            continue;

        else {

            if ($resp['type'] == 'yes-no') {

                if ($resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            } else if ($resp['type'] == 'mcq') {

                if ($resp['answer'] == 4 || $resp['answer'] == 4) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1 || $resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            }

        }

    }


    arsort($method_array);

    $generated_method = json_encode($method_array);


    $current_time = time();

    $curent_datetime = date("Y-m-d h:i:s", $current_time);

    $info = array(

        // array('ticket_user_id', $user_data['user_id']),

        // array('ticket_company_id', $user_data['user_company_id']),

        array('ticket_name', $ticket_name),

        array('ticket_response', $response),

        array('ticket_methods', $generated_method),

        array('ticket_status', 'closed'),

        array('ticket_close_time', $curent_datetime)

    );


    if ($completeness >= 100) {

        if ($ticket_id > 0) {

            $updated = $Database->update_data($info, 'ticket_id', $ticket_id, 'ticket');

        // } else {

        //     $ticket_id = $Database->write_data($info, 'ticket', false, true);

        }


        // if ($ticket_id && $updated) {

            $result = array('status' => 'success', 'report_id' => $ticket_id);

            echo json_encode($result);

        // } else {

        //     $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase57"));

        //     echo json_encode($result);

        // }

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase92"));

        echo json_encode($result);

    }

}


//Close ticket

if (

    isset($_POST['sign'])

    && $_POST['sign'] == 'close_ticket'

    && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')

) {


    $ticket_id = $_POST['ticket_id'];

    $updated = false;


    $current_time = time();

    $curent_datetime = date("Y-m-d h:i:s", $current_time);

    $info = array(

        array('ticket_status', 'closed'),

        array('ticket_close_time', $curent_datetime)

    );


    $updated = $Database->update_data($info, 'ticket_id', $ticket_id, 'ticket');


    if ($ticket_id && $updated) {

        $commonMethods = new CommonMethods();

        $isSend = $commonMethods->send_email_to_backoffice($ticket_id);

        $result = array('status' => 'success', 'report_id' => $ticket_id);

        echo json_encode($result);

    } else {

        $result = array(

            'status' => 'error',

            'message' => $trans->phrase("option_server_phrase57")

        );

        echo json_encode($result);

    }

}


//Save ticket review

if (isset($_POST['sign']) && $_POST['sign'] == 'submit_review' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {

    $review_text = htmlspecialchars($_POST['review_text'], ENT_QUOTES);

    $review_status = $_POST['review_status'];

    $ticket_id = $_POST['ticket_id'];

    $ticket_review_status = $_POST['ticket_review_status'];

    $updated = false;


    $data = array('review_status' => $review_status, 'review_text' => $review_text);

    $data = json_encode($data);


    $info = array(

        array('ticket_review', $data),

        array('review_status', $ticket_review_status)

    );


    if (strlen($ticket_id) > 0) {

        $updated = $Database->update_data($info, 'ticket_id', $ticket_id, 'ticket');

    }

    if ($updated) {

        $result = array('status' => 'success');

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase93"));

        echo json_encode($result);

    }

}

//Save ticket rating

if (isset($_POST['sign']) && $_POST['sign'] == 'submit_rating' && ($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company' || $_SESSION['account-type'] == 'consultant')) {

    $rating_text_1 = htmlspecialchars($_POST['rating_text_1'], ENT_QUOTES);

    $rating_text_2 = htmlspecialchars($_POST['rating_text_2'], ENT_QUOTES);

    $rating_check_1 = $_POST['rating_check_1'];

    $rating_check_2 = $_POST['rating_check_2'];

    $rating_check_3 = $_POST['rating_check_3'];

    $rating_check_4 = $_POST['rating_check_4'];

    $ticket_id = $_POST['ticket_id'];

    $ticket_rating_status = $_POST['ticket_rating_status'];

    $updated = false;

    $data = array(

        'rating_check_1' => $rating_check_1,

        'rating_check_2' => $rating_check_2,

        'rating_check_3' => $rating_check_3,

        'rating_check_4' => $rating_check_4,

        'rating_text_1' => $rating_text_1,

        'rating_text_2' => $rating_text_2

    );

    $data = json_encode($data);


    $info = array(

        array('ticket_rating', $data),

        array('rating_status', $ticket_rating_status)

    );


    if (strlen($ticket_id) > 0) {

        $updated = $Database->update_data($info, 'ticket_id', $ticket_id, 'ticket');

    }

    if ($updated) {

        $result = array('status' => 'success');

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase94"));

        echo json_encode($result);

    }

}

//Create common Report

if (isset($_POST['sign']) && $_POST['sign'] == 'create_mlreport_common_format') {

    $ticket_id = $_POST['ticket_id'];

    $content = $_POST['content'];

    $lang_code = $_POST['lang_code'];

    $info = array(

        array('ticket_id', $ticket_id),
        
        array('report_id', $_SESSION['report-format-id']),

        array('report_content', $content),

        array('lang_code', $lang_code)

    );


    $report_created = $Database->write_data($info, 'creport', true);

    echo 'success';



}

//Create Report

if (

    isset($_POST['sign']) && $_POST['sign'] == 'create_report' &&

    ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {

    $ticket_id = $_POST['ticket_id'];

    $content = $_POST['content'];

    $lang_code = $_POST['lang_code'];

    $info = array(

        array('ticket_id', $ticket_id),

        array('report_content', $content),

        array('lang_code', $lang_code)

    );


    $report_created = $Database->write_data($info, 'report', true);

    if ($report_created) {


        $report_gen_time = time();

        $report_gen_datetime = date("Y-m-d h:i:s", $report_gen_time);


        $info_ticket = array(

            array('report_gen_time', $report_gen_datetime)

        );


        $updated = $Database->update_data(

            $info_ticket,

            'ticket_id',

            $ticket_id,

            'ticket'

        );

        echo 'success';

    } else echo $trans->phrase("option_server_phrase95");

}



//Create Report

if (

    isset($_POST['sign']) && $_POST['sign'] == 'create_graph_report' &&

    ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {

    $ticket_id = $_POST['ticket_id'];

    $content = $_POST['content'];

    $lang_code = $_POST['lang_code'];

    $info = array(

        array('ticket_id', $ticket_id),

        array('report_content', $content),

        array('lang_code', $lang_code)

    );


    $report_created = $Database->write_data($info, 'report_graph', true);

  if($report_created){
     echo 'success';
  }



}

//Create ML Report Format Composed

if (

    isset($_POST['sign']) && $_POST['sign'] == 'create_mlreport_format' &&

    ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')

) {

    $report_format_id = $_POST['report_format_id'];

    $content = $_POST['content'];

    $lang_code = $_POST['lang_code'];

    /*$info = array(

        array('report_content', $content),

        array('lang_code', $lang_code)

    );*/

    //$report_format_data = $Database->get_data('report_format_id', $report_format_id, 'report_format', true);
   // $report_updated = $Database->update_data($info, 'report_format_id', $report_format_id, 'report_format');
   
   
    $sql = "SELECT * FROM mlreport_format_content WHERE report_format_id='" . $report_format_id ."' AND report_lang_code='" .$lang_code ."'";

    $report_format_data = $Database->get_connection()->prepare($sql);

    $report_format_data->execute();
    
    $report_format_data = $report_format_data->fetch(PDO::FETCH_ASSOC);
    
    
    /* $sql = "UPDATE mlreport_format_content SET report_content= '" . $content ."'  WHERE report_format_id='" . $report_format_id ."' AND report_lang_code='" .$lang_code ."'";

     $report_updated = $Database->get_connection()->prepare($sql);

     $report_updated->execute();

    if ($report_updated) {
        
        $file_name = 'report_types/ml_pdf_report_' . $report_format_data['report_title'] . '.php';
        
         if((is_file($file_name))&&(file_exists($file_name))){
             echo 'success';
         }else{
           echo 'success';     
           copy('report_types/pdf_report_format_common.php', 'report_types/ml_pdf_report_' . $report_format_data['report_title'] . '.php');
         }

       // echo 'success';

    } else echo $trans->phrase("option_server_phrase95");
*/

 /*$info = array(

        array('report_content', $content)

    );

$report_updated = $Database->update_data($info, 'report_format_id', $report_format_id, 'mlreport_format_content');*/

    $info = array(
        array('report_content', $content)
    );


$report_updated = $Database->update_data_check($info, 'report_format_id', 'report_lang_code', $report_format_id, $lang_code , 'mlreport_format_contentd');

 if ($report_updated) {
        
        $file_name = 'report_types/mlc_pdf_report_' . $report_format_data['report_title'] . '.php';
        
         if((is_file($file_name))&&(file_exists($file_name))){
             echo 'success';
         }else{
                
           copy('report_types/pdf_report_format_common.php', 'report_types/mlc_pdf_report_' . $report_format_data['report_title'] . '.php');
           echo 'success';
         }


} else echo $trans->phrase("option_server_phrase95");

}

//Create Report Format Composed

if (

    isset($_POST['sign']) && $_POST['sign'] == 'create_report_format_composed' ) {

    $report_format_id = $_POST['report_format_id'];

    $content = $_POST['content'];

    $lang_code = $_POST['lang_code'];

    $info = array(

        array('report_content', $content),

        array('lang_code', $lang_code)

    );

    $report_format_data = $Database->get_data('report_format_id', $report_format_id, 'report_format', true);
    $report_updated = $Database->update_data($info, 'report_format_id', $report_format_id, 'report_format');

    if ($report_updated) {
        
        //copy('report_types/pdf_report_main.php', 'report_types/pdf_report_' . $report_format_data['report_format_name'] . '.php');

        echo 'success';

    } else echo $trans->phrase("option_server_phrase95");

}


//Send report email

if (isset($_POST['sign']) && $_POST['sign'] == 'send_report_email' && $_SESSION['account-type'] == 'super_admin') {

    $user_email = $_POST['user_email'];

    $ticket_id = $_POST['ticket_id'];


    //Send email

    $ticket_url = SITE_URL . "/user/index.php?route=ticket&id={$ticket_id}";

    $attributes = array(

        array('site_url', SITE_URL),

        array('site_name', NAME),

        array('ticket_url', $ticket_url),

        array('site_email', SITE_EMAIL)

    );


    $email_sender = new EmailSender();

    $body = $email_sender->load_template('report_notification', $attributes);

    $sent = $email_sender->send_mail($user_email, $trans->phrase('email_title_phrase6'), $body);


    if ($sent)

        echo 'success';

    else

        echo $trans->phrase('option_server_phrase78');

}


//Update Ticket deadline

if (isset($_POST['sign']) && $_POST['sign'] == 'ticket_deadline_update' && $_SESSION['account-type'] == 'super_admin') {


    $ticket_id = $_POST['ticket_id'];

    $deadline_date = date("Y-m-d", strtotime($_POST['deadline_date']));

    $deadline_summary = htmlspecialchars($_POST['deadline_summary'], ENT_QUOTES);

    $deadline_description = htmlspecialchars($_POST['deadline_description'], ENT_QUOTES);


    $info = array(

        array('ticket_id', $ticket_id),

        array('end_date', $deadline_date),

        array('summary', $deadline_summary),

        array('description', $deadline_description)

    );


    $updated = $Database->write_data($info, 'ticket_deadline', true);


    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase58");

}


//Update Question deadline

if (isset($_POST['sign']) && $_POST['sign'] == 'question_deadline_update' && $_SESSION['account-type'] == 'super_admin') {


    $ticket_id = $_POST['ticket_id'];

    $question_id = $_POST['question_id'];

    $deadline_date = date("Y-m-d", strtotime($_POST['deadline_date']));

    $deadline_summary = htmlspecialchars($_POST['deadline_summary'], ENT_QUOTES);

    $deadline_description = htmlspecialchars($_POST['deadline_description'], ENT_QUOTES);


    $info = array(

        array('ticket_id', $ticket_id),

        array('question_id', $question_id),

        array('end_date', $deadline_date),

        array('summary', $deadline_summary),

        array('description', $deadline_description)

    );


    $updated = $Database->write_data($info, 'question_deadline', true);


    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase59");

}

//Create calendar event

if (isset($_POST['sign']) && $_POST['sign'] == 'create_calender_event' && $_SESSION['account-type'] == 'super_admin') {


    $ticket_id = $_POST['ticket_id'];

    $end_date = $_POST['end_date'];

    $summary = $_POST['summary'];

    $description = $_POST['description'];


    $ticket_data = $Database->get_data('ticket_id', $ticket_id, 'ticket', true);

    $ticket_user = $Database->get_data('user_id', $ticket_data['ticket_user_id'], 'user', true);


    $google_auth_code = $ticket_user['google_auth_code'];


    if ($google_auth_code) {

        try {

            $capi = new GoogleCalendarApi();


            // Get the access token

            $access_token = $google_auth_code;


            //Get user timezone

            $user_timezone = $capi->GetUserCalendarTimezone($access_token);


            $calendar_id = 'primary';


            // Full day event

            $full_day_event = 1;

            $event_date = date('Y-m-d', strtotime($end_date));

            $event_time = ['event_date' => $event_date];


            // Create event on primary calendar

            $event_id = $capi->CreateCalendarEvent($calendar_id, $summary, $description, $full_day_event, $event_time, $user_timezone, $access_token);


            echo 'success';

        } catch (Exception $e) {

            echo $trans->phrase('option_server_phrase75');

        }

    } else

        echo $trans->phrase('option_server_phrase74');

}


//Terms and conditions update

if (

    isset($_POST['sign']) && $_POST['sign'] == 'tos_update' &&

    ($_SESSION['account-type'] == 'support_user' || $_SESSION['account-type'] == 'super_admin')

) {


    $tos_content = htmlspecialchars($_POST['tos_content'], ENT_QUOTES);

    $company_id = $_POST['company_id'];

    $lang_code = $_POST['lang_code'];

    $updated = false;

    $isNotify = $_POST['isNotify'];


    if ($company_id) {

        $tos_data = null;

        $tos_info = $Database->get_multiple_data('tos_company_id', $company_id, 'tos');

        if ($tos_info) {

            foreach ($tos_info as $single_tos) {

                if ($single_tos['lang_code'] == $lang_code)

                    $tos_data = $single_tos;

            }

        }

        if (isset($tos_data) && $tos_data) {

            $info = array(array('tos_content', $tos_content));

            $updated = $Database->update_data($info, 'tos_id', $tos_data['tos_id'], 'tos');

        } else {

            $info = array(

                array('tos_content', $tos_content),

                array('tos_company_id', $company_id),

                array('lang_code', $lang_code)

            );


            $updated = $Database->write_data($info, 'tos');

        }


        // create notification for all user of the company

        if ($isNotify == 1) {

            // $Database->createNotificationForUser($company_id, 'TC');

        }

    } else {

        $tos_info = $Database->get_multiple_data(false, false, 'tos');

        if ($tos_info) {

            foreach ($tos_info as $single_tos) {

                if (!$single_tos['tos_company_id'] && $single_tos['lang_code'] == $lang_code) {

                    $tos_data = $single_tos;

                    break;

                }

            }

        }

        if (isset($tos_data) && $tos_data) {

            $info = array(array('tos_content', $tos_content));

            $updated = $Database->update_data($info, 'tos_id', $tos_data['tos_id'], 'tos');

        } else {

            $info = array(

                array('tos_content', $tos_content),

                array('lang_code', $lang_code)

            );


            $updated = $Database->write_data($info, 'tos');

        }


        if ($isNotify == 1) {

            // $Database->createNotificationForUser(0, 'TC');

        }

    }


    if ($updated)

        echo 'success';

    else

        echo $trans->phrase('option_server_phrase60');

}


//Change language

if (isset($_POST['sign']) && $_POST['sign'] == 'language_change') {

    $lang_code = $_POST['lang_code'];


    $lang_data = $Database->get_data('lang_code', $lang_code, 'language', true);


    if ($lang_data && $lang_data['lang_active'] == 1) {

        $_SESSION['trans'] = $lang_code;
        
        $_SESSION['lang_id'] = $lang_data['lang_id'];

        echo 'success';

    } else {

        echo $trans->phrase('option_server_phrase61');

    }

}

// update report status


//request_form_update
if (isset($_POST['sign']) && $_POST['sign'] == 'request_form_update') {

    $id = $_POST['id'];
    $report_status = $_POST['report_status'];
	$date = date('Y-m-d h:i:s');

    $query = "update tbl_report_request set status='$report_status', approval_date_time='$date'" 

        . " where id='$id'";


    $updated = $Database->executeQuery($query);

    if ($updated)

        echo json_encode(array("status"=>'success'));

    else

        echo $trans->phrase('option_server_phrase70');

}

//update notification

if (isset($_POST['sign']) && $_POST['sign'] == 'notification_update') {


    $ref_id = 0;

    $ref_type = '';


    if (isset($_SESSION['account-id'])) {

        $ref_id = $_SESSION['account-id'];

    }

    if (isset($_SESSION['account-type'])) {

        $ref_type = $_SESSION['account-type'];

        if ($ref_type == 'user') {

            $ref_type = 'User';

        } elseif ($ref_type == 'company') {

            $ref_type = 'Company';

        }

    }


    $query = "update notifications set status='Accepted'"

        . " where ref_id='$ref_id'"

        . " and ref_type='$ref_type'"

        . " and status='New'"

        . " and noti_type ='TC'";


    $updated = $Database->executeQuery($query);

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase('option_server_phrase70');

}


//Add package

if (isset($_POST['sign']) && $_POST['sign'] == 'add_package' && $_SESSION['account-type'] == 'super_admin') {

    $package_lang = $_POST['package_lang'];

    $package_name = htmlspecialchars($_POST['package_name'], ENT_QUOTES);

    $package_price = (float)$_POST['package_price'];

    $package_user = (int)$_POST['package_user'];

    $package_size_min = (int)$_POST['package_size_min'];

    $package_size_max = (int)$_POST['package_size_max'];

    $package_details = htmlspecialchars($_POST['package_details'], ENT_QUOTES);

    $package_edit = $_POST['package_edit'];


    $validation = true;

    if (strlen($package_name) < 1) $validation = $validation & false;

    if ($package_price <= 0) $validation = $validation & false;

    if ($package_user <= 0) $validation = $validation & false;

    if ($package_size_min < 0) $validation = $validation & false;

    if ($package_size_max <= 0) $validation = $validation & false;

    if ($package_size_min > $package_size_max) $validation = $validation & false;


    if ($validation) {

        $info = array(

            array('package_name', $package_name),

            array('package_price', $package_price),

            array('package_user', $package_user),

            array('package_size_min', $package_size_min),

            array('package_size_max', $package_size_max),

            array('package_details', $package_details)

        );


        if ($package_edit)

            $updated = $Database->update_data($info, 'package_id', $package_edit, 'package');

        else

            $updated = $Database->write_data($info, 'package', false, true);


        if ($updated) {

            if ($package_edit) $updated = $package_edit;


            $info = array(

                array('package_id', $updated),

                array('package_name', $package_name),

                array('package_details', $package_details),

                array('package_lang', $package_lang)

            );

            $updated = $Database->write_data($info, 'package_content', true);

            if ($updated)

                echo "success";

            else

                echo $trans->phrase('option_server_phrase69');

        } else

            echo $trans->phrase('option_server_phrase69');

    } else {

        echo $trans->phrase('option_server_phrase37');

    }

}

//Delete package

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_package' && $_SESSION['account-type'] == 'super_admin') {

    $package_id = $_POST['package_id'];


    $deleted = $Database->delete_data('package_id', $package_id, 'package');


    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase('option_server_phrase70');

}

//Get package info base on language

if (

    isset($_POST['sign']) && $_POST['sign'] == 'retrieve_package_content' &&

    ($_SESSION['account-type'] == 'super_admin' ||

        $_SESSION['account-type'] == 'support_admin')

) {


    $package_id = $_POST['package_id'];

    $lang_code = $_POST['lang_code'];


    $package = $Database->get_data('package_id', $package_id, 'package', true);


    $sql = "SELECT * FROM package_content WHERE package_id={$package['package_id']} AND package_lang='{$lang_code}'";

    $package_data = $Database->get_connection()->prepare($sql);

    $package_data->execute();

    if ($package_data->rowCount() < 1) $package_data = false;

    else $package_data = $package_data->fetch(PDO::FETCH_ASSOC);


    if ($package_data) {

        $package['package_name'] = $package_data['package_name'];

        $package['package_details'] = $package_data['package_details'];

    }


    echo json_encode($package);

}


//Clear all tracking info

if (isset($_POST['sign']) && $_POST['sign'] == 'clear_tracking' && $_SESSION['account-type'] == 'super_admin') {

    $sql = "TRUNCATE TABLE tracker";

    $clear_tracker = $Database->get_connection()->prepare($sql);

    $clear_tracker->execute();

    echo 'success';

}

//set company user role

if (isset($_POST['sign']) && $_POST['sign'] == 'company_role' && isset($_POST['role']) && $_POST['company_id'] && $_SESSION['account-type'] == 'super_admin') {

    $info = array(array('company_role', $_POST['role']));

    $company_id = $_POST['company_id'];

    $Database->update_data($info, 'company_id', $company_id, 'company');

    echo 'success';

}


//Helper function

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')

{

    $pieces = [];

    $max = mb_strlen($keyspace, '8bit') - 1;

    for ($i = 0; $i < $length; ++$i) {

        $pieces[] = $keyspace[random_int(0, $max)];

    }

    return implode('', $pieces);

}

function directory_copy($src, $dst)

{

    $dir = opendir($src);

    @mkdir($dst);

    while (false !== ($file = readdir($dir))) {

        if (($file != '.') && ($file != '..')) {

            if (is_dir($src . '/' . $file)) {

                recurse_copy($src . '/' . $file, $dst . '/' . $file);

            } else {

                copy($src . '/' . $file, $dst . '/' . $file);

            }

        }

    }

    closedir($dir);

}

function directory_delete($dirPath)

{

    if (!is_dir($dirPath)) {

        return false;

    }

    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {

        $dirPath .= '/';

    }

    $files = glob($dirPath . '*', GLOB_MARK);

    foreach ($files as $file) {

        if (is_dir($file)) {

            deleteDir($file);

        } else {

            unlink($file);

        }

    }

    rmdir($dirPath);

}

//Comparison function for descending usort

function descending_compare($a, $b)

{

    if ($a == $b) {

        return 0;

    }

    return ($a < $b) ? 1 : -1;

}


function get_client_ip()

{

    /*$ipaddress = '';

    if (getenv('HTTP_CLIENT_IP'))

        $ipaddress = getenv('HTTP_CLIENT_IP');

    else if(getenv('HTTP_X_FORWARDED_FOR'))

        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');

    else if(getenv('HTTP_X_FORWARDED'))

        $ipaddress = getenv('HTTP_X_FORWARDED');

    else if(getenv('HTTP_FORWARDED_FOR'))

        $ipaddress = getenv('HTTP_FORWARDED_FOR');

    else if(getenv('HTTP_FORWARDED'))

       $ipaddress = getenv('HTTP_FORWARDED');

    else if(getenv('REMOTE_ADDR'))

        $ipaddress = getenv('REMOTE_ADDR');

    else

        $ipaddress = 'UNKNOWN';

    return $ipaddress;*/


    $id = "";

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

        $ip = $_SERVER['HTTP_CLIENT_IP'];

    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {

        $ip = $_SERVER['HTTP_X_FORWARDED'];

    } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {

        $ip = $_SERVER['HTTP_FORWARDED_FOR'];

    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {

        $ip = $_SERVER['REMOTE_ADDR'];

    } else {

        $ip = 'UNKNOWN';

    }

    return $ip;

}


/* consultant */

//Update Res basic question info

if (isset($_POST['sign']) && $_POST['sign'] == 'update_con_question_basic' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $category_id = $_POST['category_id'];

    $yes_conponse = $_POST['yes_conponse'];

    $no_conponse = $_POST['no_conponse'];

    $question_activate_tip_yes = $_POST['question_activate_tip_yes'];

    $question_activate_tip_no = $_POST['question_activate_tip_no'];

    $question_follow_up_yes = $_POST['question_follow_up_yes'];

    $question_follow_up_no = $_POST['question_follow_up_no'];

    $company_id = $_POST['company_id'];


    if (!$category_id) $category_id = NULL;

    if (!$yes_conponse) $yes_conponse = NULL;

    if (!$no_conponse) $no_conponse = NULL;

    if (!$question_follow_up_yes) $question_follow_up_yes = NULL;

    if (!$question_follow_up_no) $question_follow_up_no = NULL;


    $info1 = array(

        array('access_question_id', $question_id),

        array('access_yes', $yes_conponse),

        array('access_no', $no_conponse)

    );


    $info2 = array(

        array('category_id', $category_id),

        array('question_tip_on_yes', $question_activate_tip_yes),

        array('question_tip_on_no', $question_activate_tip_no),

        array('question_yes_follow_up', $question_follow_up_yes),

        array('question_no_follow_up', $question_follow_up_no)

    );


    $access_id = null;

    $access_data = $Database->get_multiple_data('access_question_id', $question_id, 'question_con_method');

    if ($access_data) {

        foreach ($access_data as $data) {

            if (!$data['access_company_id']) {

                $access_id = $data['access_con_id'];

                break;

            }

        }

    }


    if (strlen($company_id) > 0) {

        array_push($info1, array('access_company_id', $company_id));


        $access_id = null;

        if ($access_data) {

            foreach ($access_data as $data) {

                if ($data['access_company_id'] == $company_id) {

                    $access_id = $data['access_id'];

                    break;

                }

            }

        }

    }


    $write = false;

    if ($access_id) {

        $write = $Database->update_data($info1, 'access_con_id', $access_id, 'question_con_method');

    } else {

        $write = $Database->write_data($info1, 'question_con_method', false);

    }


    $updated = $Database->update_data($info2, 'question_con_id', $question_id, 'question_con');

    if ($write && $updated) echo "success";

    else echo $trans->phrase("option_server_phrase54");

}

//Update res question translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_con_translation_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $lang_code = $_POST['lang_code'];

    $question_name = htmlspecialchars($_POST['question_name'], ENT_QUOTES);

    $question_tips_yes = htmlspecialchars($_POST['question_tips_yes'], ENT_QUOTES);

    $question_tips_no = htmlspecialchars($_POST['question_tips_no'], ENT_QUOTES);

    $question_option1 = htmlspecialchars($_POST['question_option1'], ENT_QUOTES);

    $question_option2 = htmlspecialchars($_POST['question_option2'], ENT_QUOTES);

    $question_option3 = htmlspecialchars($_POST['question_option3'], ENT_QUOTES);

    $question_option4 = htmlspecialchars($_POST['question_option4'], ENT_QUOTES);

    $question_option5 = htmlspecialchars($_POST['question_option5'], ENT_QUOTES);


    $info = array(

        array('question_con_id', $question_id),

        array('question_name', $question_name),

        array('question_tips_yes', $question_tips_yes),

        array('question_tips_no', $question_tips_no),

        array('question_option1', $question_option1),

        array('question_option2', $question_option2),

        array('question_option3', $question_option3),

        array('question_option4', $question_option4),

        array('question_option5', $question_option5),

        array('lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'question_con_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase55");

}

//Delete res question

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_con_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];


    $deleted = $Database->delete_data('question_con_id', $question_id, 'question_con');

    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase53");

}

//Update question follow-up

if (isset($_POST['sign']) && $_POST['sign'] == 'update_con_question_follow_up' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $question_follow_up = $_POST['question_follow_up'];


    $info = array(

        array('question_follow_up', $question_follow_up),

        array('category_id', NULL)

    );


    $updated = $Database->update_data($info, 'question_con_id', $question_id, 'question_con');

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase89");

}

//Update res question type

if (isset($_POST['sign']) && $_POST['sign'] == 'update_con_question_type' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $question_type = $_POST['question_type'];


    $info = array(

        array('question_type', $question_type)

    );


    $updated = $Database->update_data($info, 'question_con_id', $question_id, 'question_con');

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase89");

}

//Add res question 

if (isset($_POST['sign']) && $_POST['sign'] == 'add_con_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_name = htmlspecialchars($_POST['question_name'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('question_tip_on_yes', 0),

        array('question_tip_on_no', 0)

    );


    $added = $Database->write_data($info, 'question_con', false, true);


    if ($added !== false) {

        $info = array(

            array('question_con_id', $added),

            array('question_name', $question_name),

            array('lang_code', $lang_code)

        );

        $content_added = $Database->write_data($info, 'question_con_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase51");

    } else echo $trans->phrase("option_server_phrase52");

}

/* consultant */
/* Responder  */

//Add res question 

if (isset($_POST['sign']) && $_POST['sign'] == 'add_res_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_name = htmlspecialchars($_POST['question_name'], ENT_QUOTES);

    $lang_code = $_SESSION['trans'];


    $info = array(

        array('question_tip_on_yes', 0),

        array('question_tip_on_no', 0)

    );


    $added = $Database->write_data($info, 'question_res', false, true);


    if ($added !== false) {

        $info = array(

            array('question_res_id', $added),

            array('question_name', $question_name),

            array('lang_code', $lang_code)

        );

        $content_added = $Database->write_data($info, 'question_res_content', true, false);

        if ($content_added) {

            echo 'success';

        } else echo $trans->phrase("option_server_phrase51");

    } else echo $trans->phrase("option_server_phrase52");

}

//Update res question type

if (isset($_POST['sign']) && $_POST['sign'] == 'update_res_question_type' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $question_type = $_POST['question_type'];


    $info = array(

        array('question_type', $question_type)

    );


    $updated = $Database->update_data($info, 'question_res_id', $question_id, 'question_res');

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase89");

}
//Update question follow-up

if (isset($_POST['sign']) && $_POST['sign'] == 'update_res_question_follow_up' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $question_follow_up = $_POST['question_follow_up'];


    $info = array(

        array('question_follow_up', $question_follow_up),

        array('category_id', NULL)

    );


    $updated = $Database->update_data($info, 'question_res_id', $question_id, 'question_res');

    if ($updated)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase89");

}


//Delete res question

if (isset($_POST['sign']) && $_POST['sign'] == 'delete_res_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];


    $deleted = $Database->delete_data('question_res_id', $question_id, 'question_res');

    if ($deleted)

        echo 'success';

    else

        echo $trans->phrase("option_server_phrase53");

}

//Update Res basic question info

if (isset($_POST['sign']) && $_POST['sign'] == 'update_res_question_basic' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $category_id = $_POST['category_id'];

    $yes_response = $_POST['yes_response'];

    $no_response = $_POST['no_response'];

    $question_activate_tip_yes = $_POST['question_activate_tip_yes'];

    $question_activate_tip_no = $_POST['question_activate_tip_no'];

    $question_follow_up_yes = $_POST['question_follow_up_yes'];

    $question_follow_up_no = $_POST['question_follow_up_no'];

    $company_id = $_POST['company_id'];


    if (!$category_id) $category_id = NULL;

    if (!$yes_response) $yes_response = NULL;

    if (!$no_response) $no_response = NULL;

    if (!$question_follow_up_yes) $question_follow_up_yes = NULL;

    if (!$question_follow_up_no) $question_follow_up_no = NULL;


    $info1 = array(

        array('access_question_id', $question_id),

        array('access_yes', $yes_response),

        array('access_no', $no_response)

    );


    $info2 = array(

        array('category_id', $category_id),

        array('question_tip_on_yes', $question_activate_tip_yes),

        array('question_tip_on_no', $question_activate_tip_no),

        array('question_yes_follow_up', $question_follow_up_yes),

        array('question_no_follow_up', $question_follow_up_no)

    );


    $access_id = null;

    $access_data = $Database->get_multiple_data('access_question_id', $question_id, 'question_res_method');

    if ($access_data) {

        foreach ($access_data as $data) {

            if (!$data['access_company_id']) {

                $access_id = $data['access_res_id'];

                break;

            }

        }

    }


    if (strlen($company_id) > 0) {

        array_push($info1, array('access_company_id', $company_id));


        $access_id = null;

        if ($access_data) {

            foreach ($access_data as $data) {

                if ($data['access_company_id'] == $company_id) {

                    $access_id = $data['access_id'];

                    break;

                }

            }

        }

    }


    $write = false;

    if ($access_id) {

        $write = $Database->update_data($info1, 'access_res_id', $access_id, 'question_res_method');

    } else {

        $write = $Database->write_data($info1, 'question_res_method', false);

    }


    $updated = $Database->update_data($info2, 'question_res_id', $question_id, 'question_res');

    if ($write && $updated) echo "success";

    else echo $trans->phrase("option_server_phrase54");

}

//Update res question translation

if (isset($_POST['sign']) && $_POST['sign'] == 'update_res_translation_question' && $_SESSION['account-type'] == 'super_admin') {

    $question_id = $_POST['question_id'];

    $lang_code = $_POST['lang_code'];

    $question_name = htmlspecialchars($_POST['question_name'], ENT_QUOTES);

    $question_tips_yes = htmlspecialchars($_POST['question_tips_yes'], ENT_QUOTES);

    $question_tips_no = htmlspecialchars($_POST['question_tips_no'], ENT_QUOTES);

    $question_option1 = htmlspecialchars($_POST['question_option1'], ENT_QUOTES);

    $question_option2 = htmlspecialchars($_POST['question_option2'], ENT_QUOTES);

    $question_option3 = htmlspecialchars($_POST['question_option3'], ENT_QUOTES);

    $question_option4 = htmlspecialchars($_POST['question_option4'], ENT_QUOTES);

    $question_option5 = htmlspecialchars($_POST['question_option5'], ENT_QUOTES);
    
    $question_option6 = htmlspecialchars($_POST['question_option6'], ENT_QUOTES);


    $info = array(

        array('question_res_id', $question_id),

        array('question_name', $question_name),

        array('question_tips_yes', $question_tips_yes),

        array('question_tips_no', $question_tips_no),

        array('question_option1', $question_option1),

        array('question_option2', $question_option2),

        array('question_option3', $question_option3),

        array('question_option4', $question_option4),

        array('question_option5', $question_option5),
        
        array('question_option6', $question_option6),

        array('lang_code', $lang_code)

    );


    $written = $Database->write_data($info, 'question_res_content', true);

    if ($written) echo 'success';

    else echo $trans->phrase("option_server_phrase55");

}

/* Responder  */
//Save Respnder response

if (isset($_POST['sign']) && $_POST['sign'] == 'save_responder_ticket') {

    $response = $_POST['response'];
    
    $responder_id = $_POST['responder_id'];

    $ticket_id = $_POST['ticket_id'];

    $sql = "SELECT * FROM responder_ticket_data WHERE responder_id = ".$responder_id." && ticket_id =".$ticket_id;

    $responder_data = $Database->get_connection()->prepare($sql);

    $responder_data->execute();

    if ($responder_data->rowCount() == 1) {

        $updated = true;
        $recordRes = $responder_data->fetch(PDO::FETCH_ASSOC);

    } else {
        $updated = false;
    }

    

    $method_selection = $Database->get_multiple_data(false, false, 'question_res_method');


    $resp_array = json_decode($response, true);


    //Generate methods

    $method_array = array();

    $methods = $Database->get_multiple_data(false, false, 'method');

    if ($methods) {

        foreach ($methods as $method) {

            $method_array[$method['method_id']] = 0;

        }

    }


    foreach ($resp_array as $question_id => $resp) {

        $access = null;

        foreach ($method_selection as $selection) {

            // if (

            //     $selection['access_question_id'] == $question_id &&

            //     $selection['access_company_id'] == 27

            // ) {


            //     $access = $selection;

            //     break;

            // } else if ($selection['access_question_id'] == $question_id) {

                $access = $selection;

            // }

        }


        if (!$access)

            continue;

        else {

            if ($resp['type'] == 'yes-no') {

                if ($resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            } else if ($resp['type'] == 'mcq') {

                if ($resp['answer'] == 4 || $resp['answer'] == 4) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1 || $resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            }

        }

    }


    arsort($method_array);

    $generated_method = json_encode($method_array);

    if($updated) {

        $info = array(

            array('ticket_response', $response),
    
            array('ticket_methods', $generated_method),
    
            array('added_time', date("Y-m-d h:i:s", time()))
    
            //array('ticket_status', 'process')
    
        );

        $ticket_id = $Database->update_data($info, 'responder_ticket_data_id', $recordRes['responder_ticket_data_id'], 'responder_ticket_data');
    } else {

        $info = array(

            array('ticket_company_id', 27),
           
             array('ticket_id', $ticket_id),
             
             array('responder_id', $responder_id),
    
            array('ticket_response', $response),
    
            array('ticket_methods', $generated_method),
    
            array('added_time', date("Y-m-d h:i:s", time()))
    
            //array('ticket_status', 'process')
    
        );

        $ticket_id = $Database->write_data($info, 'responder_ticket_data', false, true);
    }

    if ($ticket_id) {
		
        $result = array('status' => 'success', 'ticket_id' => $ticket_id, 'url' => SITE_URL.'/custom.php?route=res_question&page=pen_responder&res_id='. $ticket_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));
        echo json_encode($result);

    }

}
//Save Respnder response

//Save Pending Respnder response

if (isset($_POST['sign']) && $_POST['sign'] == 'save_pending_responder_ticket') {

    $response = $_POST['response'];
    
	$res_id = $_POST['res_id'];

    $updated = true;

    $method_selection = $Database->get_multiple_data(false, false, 'question_method');


    $resp_array = json_decode($response, true);


    //Generate methods

    $method_array = array();

    $methods = $Database->get_multiple_data(false, false, 'method');

    if ($methods) {

        foreach ($methods as $method) {

            $method_array[$method['method_id']] = 0;

        }

    }


    foreach ($resp_array as $question_id => $resp) {

        $access = null;

        foreach ($method_selection as $selection) {

            if (

                $selection['access_question_id'] == $question_id &&

                $selection['access_company_id'] == 27

            ) {


                $access = $selection;

                break;

            } else if ($selection['access_question_id'] == $question_id) {

                $access = $selection;

            }

        }


        if (!$access)

            continue;

        else {

            if ($resp['type'] == 'yes-no') {

                if ($resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            } else if ($resp['type'] == 'mcq') {

                if ($resp['answer'] == 4 || $resp['answer'] == 4) {

                    $access_array = explode(",", $access['access_yes']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                } else if ($resp['answer'] == 1 || $resp['answer'] == 2) {

                    $access_array = explode(",", $access['access_no']);

                    if ($access_array) {

                        foreach ($access_array as $method_id) {

                            if ($method_id)

                                $method_array[$method_id]++;

                        }

                    }

                }

            }

        }

    }


    arsort($method_array);

    $generated_method = json_encode($method_array);


    $info = array(

        array('ticket_response', $response),

        array('ticket_methods', $generated_method),

        array('added_time', date("Y-m-d h:i:s", time()))

        //array('ticket_status', 'process')

    );

    $ticket_id = $Database->update_data($info, 'responder_ticket_data_id', $res_id, 'responder_ticket_data');


    if ($ticket_id) {

        $result = array('status' => 'success', 'ticket_id' => $ticket_id);

        echo json_encode($result);

    } else {

        $result = array('status' => 'error', 'message' => $trans->phrase("option_server_phrase56"));

        echo json_encode($result);

    }

}

if (isset($_POST['sign']) && $_POST['sign'] == 'set_session_report_format') {

    $reportPermission = new ReportPermission($_POST['report_ticket_id']);

    if(!$reportPermission->canChangeReportFormat()){
        echo $trans->phrase('option_server_phrase97');
        exit;
    }

    $result = $Database->get_data('report_id', $_POST['report_format_id'], 'tbl_report_request', true);
    
    if($result) {

        $query = "update tbl_report_request set report_id = {$_POST['report_format_id']} where ticket_id={$_POST['report_ticket_id']}";

        $updated = $Database->executeQuery($query);
    
        if ($updated) {
            echo 'success';
        } else {
            echo $trans->phrase('option_server_phrase70');
        }

    } else {
        
        echo 'error';
    }
}


if (isset($_POST['sign']) && $_POST['sign'] == 'get_notifications') {

    $lastTimestamp = isset($_POST['lastTimestamp']) ? $_POST['lastTimestamp'] : date('Y-m-d H:i:s');

    $page = $_POST['page'];
    $limit = 7;
    $lang_code = $_SESSION['trans'];
    $newPage = ($page - 1) * $limit;
    
    $ref_type = null;
    $ref_id = $_SESSION['account-id'];
    
    switch ($_SESSION['account-type']) {
        case 'user':
            $ref_type = 'user';
            break;
        
        case 'company':
            $ref_type = 'company';
            break;
        
        case 'support_admin':
            $ref_type = 'support_admin';
            break;

        case 'super_admin':
            $ref_type = 'super_admin';
            break;
    
        case 'consultant':
            $ref_type = 'consultant';
            break;
        
        default:
            $ref_type = 'user';
            break;
    }
    
    $notifications = array();
    
    if($ref_type == 'super_admin') {

        $sql = "SELECT * FROM notifications WHERE notification_id > '{$_POST['lastNotificationId']}' AND timestamp > '{$lastTimestamp}' ORDER BY timestamp ASC LIMIT {$newPage}, {$limit}";

    } else {

        $sql = "SELECT * FROM notifications WHERE ref_id={$ref_id} AND ref_type = '{$ref_type}' AND notification_id > '{$_POST['lastNotificationId']}' AND timestamp > '{$lastTimestamp}' ORDER BY timestamp ASC LIMIT {$newPage}, {$limit}";

    }
    
    
    $result = $Database->get_connection()->prepare($sql);
    
    $result->execute();
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    
        $notification = array(
            'notification_id' => $row['notification_id'],
            // 'ref_id' => $row['ref_id'],
            // 'ref_type' => $row['ref_type'],
            'timestamp' => $row['timestamp'],
            'is_read' => $row['is_read'],
            'is_admin_read' => $row['is_admin_read'],
        );
    
        if($_SESSION['trans'] == 'en') {
            $notification['message'] = $row['message_en'];
        } else {
            $notification['message'] = $row['message_nor'];
        }
    
        $notifications[] = $notification;
    }
    
    // Return the notifications as JSON
    echo json_encode($notifications);

    // Remove all old notification more then 30 days
    $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

    $sql = "DELETE FROM notifications WHERE timestamp < '{$thirtyDaysAgo}'";

    $result = $Database->get_connection()->prepare($sql);
    
    $result->execute();

}

if (isset($_POST['sign']) && $_POST['sign'] == 'mark_notifications_read') {

    echo $_SESSION['account-type'];
    $ref_type = null;
    $ref_id = $_SESSION['account-id'];
    
    switch ($_SESSION['account-type']) {
        case 'user':
            $ref_type = 'user';
            break;
        
        case 'company':
            $ref_type = 'company';
            break;
    
        case 'support_admin':
            $ref_type = 'support_admin';
            break;

        case 'super_admin':
            $ref_type = 'super_admin';
            break;

        case 'consultant':
            $ref_type = 'consultant';
            break;
        
        default:
            $ref_type = 'user';
            break;
    }
    
    if($ref_type == 'super_admin') {
        $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";

        
        $sql2 = "UPDATE notifications SET is_admin_read = 1 WHERE is_admin_read = 0";
        $stmt2 = $Database->get_connection()->prepare($sql2);
        $stmt2->execute();

    } else {
        $sql = "UPDATE notifications SET is_read = 1 WHERE ref_id={$ref_id} AND ref_type = '{$ref_type}' AND is_read = 0";
    }
    $stmt = $Database->get_connection()->prepare($sql);
    $stmt->execute();

    echo json_encode(['status' => 2]);
}
