<?php require_once('header.php'); ?>
<?php
$_SESSION['account-type'] = null;

$_SESSION['account-id'] = null;

$_SESSION['is_verified'] = false;

$_SESSION['user_type_logged_in'] = null;

$_SESSION['user_id_logged_in'] = null;

?>
<?php

function get_client_ip()

{

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

require_once ('email_sender.php');

if (isset($_GET['invited']) && !empty($_GET['invited'])) :
    $invitation_id = $_GET['invited'];
    if (isset($_POST['invited_login']) && $_POST['invited_login'] == 'Login') {

        $input_invitation_code = $_POST['invitation_code'];
        $input_password = $_POST['invitation_password'];
        $input_confirm_password = $_POST['invitation_confirm_password'];

        $Database = new Database();
        $invitation_info = $Database->get_data('invitation_id', $invitation_id, 'invitation', true);
        $invite_code = $invitation_info['invitation_code'];

        if ($input_invitation_code == $invite_code) {
            if ($input_password == $input_confirm_password) {
                $invite_company_id = $invitation_info['company_id'];
                $invite_full_name = $invitation_info['full_name'];
                $invite_email = $invitation_info['email'];
                $invite_phone = $invitation_info['phone_number'];

                $company = $Database->get_data('company_id', $invite_company_id, 'company', true);

                $company_users = $Database->get_multiple_data('user_company_id', $invite_company_id, 'user', '=', true, false, false);


                //Company user count

                $user_limit = 0;

                if ($company && $company['company_package_id']) {

                    $package_info = $Database->get_data('package_id', $company['company_package_id'], 'package', true);

                    $user_limit = $package_info['package_user'];

                }


                if ($Database->get_data('user_email', $invite_email, 'user')) {

                    echo $trans->phrase("option_server_phrase7");

                }
                else if (!$company) {

                    echo $trans->phrase("option_server_phrase8");

                } else if ($company['company_status'] != 'active') {

                    echo $trans->phrase("option_server_phrase9");

                } else if ($company_users && count($company_users) > $user_limit) {

                    echo $trans->phrase("option_server_phrase9");

                } else if ($user_limit == 0) {

                    echo $trans->phrase("option_server_phrase9");

                } else {

                    $password_hash = password_hash($input_password, PASSWORD_DEFAULT);


                    $info = array(

                        array("user_name", $invite_full_name),

                        array("user_email", $invite_email),

                        array("user_password", $password_hash),

                        array("user_phone", $invite_phone),

                        array("user_company_id", $invite_company_id)

                    );


                    $write = $Database->write_data($info, 'user', false);

                    if ($write) {

                        $accepted_date = date("Y-m-d H:i:s");
                        $infoAccepted = array(array('status', 1), array('accept_date', $accepted_date));

                        $Database->update_data($infoAccepted, 'invitation_id', $invitation_id, 'invitation');


                        $user = $Database->get_data('user_email', $invite_email, 'user', true);

                        $_SESSION['account-type'] = 'user';

                        $_SESSION['account-id'] = $user['user_id'];

                        $_SESSION['is_verified'] = true;

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


                        //Sending welcome email

                        $attributes = array(

                            array('site_url', SITE_URL),

                            array('site_name', NAME),

                            array('user_name', $invite_full_name),

                            array('user_email', $invite_email),

                            array('user_id', $user['user_id']),

                            array('site_email', SITE_EMAIL),

                            array('site_contact', SITE_URL . "/contact_us")

                        );


                        $email_sender = new EmailSender();

                        $body = $email_sender->load_template('welcome_mail', $attributes);

                        $send = $email_sender->send_mail($invite_email, $trans->phrase('email_title_phrase8') . ' ' . NAME, $body);

                        header("Location: " . SITE_URL . "user/index.php?route=profile");

                    } else echo $trans->phrase("option_server_phrase10");

                }
            }
        }
    }
    ?>
    <style>
        input:focus::placeholder {
            color: transparent;
        }
    </style>
    <div class="row login-parent">
        <div class="col-12">
            <div class="row">
                <div class="col-12 login-logo">
                    <img src="<?php echo SITE_URL; ?>/images/logo-transparent.png" class="img-fluid" title="Main Logo">
                </div>
            </div>
            <div class="row login-ctn-new">
                <div class="col-2"></div>
                <div class="col-8 login-form">
                    <form method="post">
                        <div class="form-group mb-3">
                            <input type="text" class="form-control login-input"
                                   name="invitation_code"
                                   placeholder="Invitation Code">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" class="form-control login-input"
                                   name="invitation_password"
                                   placeholder="Password">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control login-input"
                                   name="invitation_confirm_password"
                                   placeholder="Confirm Password">
                        </div>
                        <div class="login-buttons mt-4" style="text-align:center;">
                            <input type="submit" class="login-button btn btn-default btn-success" name="invited_login"
                                   value="Login">
                        </div>
                    </form>
                </div>
                <div class="col-2"></div>
            </div>
        </div>
    </div>
<?php else:
    header("Location: " . SITE_URL . "index.php");
    ?>
<?php endif ?>
<?php require_once('footer.php'); ?>