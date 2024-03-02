<?php
require_once('../database.php');
$Database = new Database();

$users = null;
$company_id = 0;
if ($_SESSION['account-type'] == 'company') {
    $company_id = $_SESSION['account-id'];
    $users = $Database->get_multiple_data('user_company_id', $company_id, 'user', '=', true, 'user_name ASC');
    $invited_users = $Database->get_multiple_data('company_id', $company_id, 'invitation', '=', true, 'invitation_id DESC');
    $company = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);
} else {
    $company_id = $_SESSION['account-id'];
    $users = $Database->get_multiple_data('user_company_id', $company_id, 'user', '=', true, 'user_name ASC');
}

?>
<?php if (empty($_GET['add']) && empty($_GET['invite'])) :
    ?>
    <div class="card">
        <div class="card-body p-3">
            <table class="table table-striped">
                <thead class="bg-primary">
                <tr>
                    <th scope="col">
                        <h4 style="color:white; margin-left: 20px;"><?php echo $trans->phrase("user_sidebar_phrase18"); ?> </h4>

                    </th>
                    <th scope="col" colspan="3" style="text-align:right;">
                        <h5 style="color:white; margin-right: 20px">
                            <?php
                            echo $trans->phrase('index_phrase17') . " : ";
                            echo $Database->get_name_by_id('company', 'company_name', $company_id);
                            ?>
                        </h5>
                    </th>
                </tr>
                </thead>
                <thead class="bg-secondary">
                <tr style="margin-top:10px !important;">
                    <th scope="col">&nbsp;</th>
                    <th scope="col"><?php echo $trans->phrase('user_composer_phrase13'); ?></th>
                    <th scope="col"><?php echo $trans->phrase('index_phrase1'); ?></th>
                    <th scope="col"><?php echo $trans->phrase('index_phrase12'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($users):
                    $count = 1;
                    foreach ($users as $user): ?>
                        <tr>
                            <th class="pull-right"><?php echo $count++ ?></th>
                            <td><?php echo $user['user_name']; ?></td>
                            <td><?php echo $user['user_name']; ?></td>
                            <td><?php echo $user['user_phone']; ?></td>
                        </tr>
                    <?php
                    endforeach;
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif;

if (isset($_GET['add']) && $_GET['add'] == 'user') :
    ?>
    <div class="card">
        <div class="card-body p-3">
            <div class="container-fluid">
                <div class="row user-content-row">
                    <div class="col-12 company">
                        <div class="company-widget-title"><?php echo $trans->phrase("user_company_profile_phrase19"); ?></div>
                        <div class="company-user-ctn">
                            <?php
                            $company_users = $Database->get_multiple_data('user_company_id', $company['company_id'], 'user', '=', true, false, false);
                            if ($company_users) :
                                foreach ($company_users as $user) :
                                    ?>
                                    <div class="user-card">
                                        <div class="row">
                                            <div class="col-8">
                                                <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase20"); ?> </label>
                                                <div class="user-card-name"><?php echo $user['user_name']; ?> </div>
                                            </div>
                                            <div class="col-4">
                                                <button class="btn btn-danger btn-sm user-card-btn user-card-delete"
                                                        data-user-id="<?php echo $user['user_id']; ?>"><i
                                                            class="fas fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase22"); ?> </label>
                                                <div class="user-card-email"> <?php echo $user['user_email']; ?> </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase21"); ?> </label>
                                                <div class="user-card-id"> <?php echo $user['user_id']; ?> </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            endif;
                            ?>
                            <div class="row company-row" style="margin: 15px 0px;">
                                <!--  <div class="col-12 company-action">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase14"); ?> </span>
                                    <button id="company_add_user" onclick="add_company_user()" class="btn btn-light-primary btn-sm" data-company="<?php echo $company['company_id']; ?>">
                                        <i class="fas fa-edit"></i> Add User</button>
                                </div> -->
                            </div>
                            <div style="display: flex;">
                                <div class="w3-modal" style="margin: 15px 0px;" id="delete_user_content">
                                    <div class="modal-content" style="padding-bottom: 15px;">
                                        <div class="container">
                                            <div class="form-group form-row">
                                                <!-- <label>Name</span> -->
                                                <input type="text" class="form-control" name="username"
                                                       id="signup_username"
                                                       placeholder="<?php echo $trans->phrase('index_phrase9'); ?>">
                                            </div>
                                            <div class=" form-group form-row">
                                                <!-- <label>Email address</span> -->
                                                <input type="email" class="form-control" name="email"
                                                       id="signup_email"
                                                       placeholder="<?php echo $trans->phrase('index_phrase10'); ?>">
                                            </div>
                                            <div class=" form-group form-row">
                                                <!-- <label>Telephone</span> -->
                                                <input type="tel" class="form-control" name="telephone"
                                                       id="signup_phone"
                                                       placeholder="<?php echo $trans->phrase('index_phrase12'); ?>">
                                            </div>
											 <div class=" form-group form-row">
											   <label class="radio-inline">Need approval?</label>
											   <label class="radio-inline"><input type="radio" name="approve_per" value="1">Yes</label>
                                               <label class="radio-inline"><input type="radio" name="approve_per" value="0">No</label>
                                            </div>
											<div class=" form-group form-row"> 
                                                <!-- <label>Password</span> -->
                                                <input type="password" class="form-control" name="password"
                                                       id="signup_password"
                                                       placeholder="<?php echo $trans->phrase('index_phrase11'); ?>">
                                            </div>
                                            <div class=" form-group form-row">
                                                <!-- <label>Cirform Password</span> -->
                                                <input type="password" class="form-control" name="cirpassword"
                                                       id="signup_confirm_password"
                                                       placeholder="<?php echo $trans->phrase('index_phrase19'); ?>">
                                            </div>
                                            <input type="hidden" value="<?php echo $company['company_id']; ?>"
                                                   name="company_id" id="signup_company">
                                            <div class="button-holder">
                                                <button class="btn btn-primary" id="add_company_user_btn">Add
                                                </button>&nbsp;
                                                <button id="btn-close" onclick="add_company_user()"
                                                        class="btn btn-info">Close
                                                </button>
                                                <div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif;

if (isset($_GET['invite']) && $_GET['invite'] == 'user') :
    function generate_invite_code($length)
    {
        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $text = "";

        for ($x = 0; $x < $length; $x++) {
            $random = rand(0, 61);
            $text .= $array[$random];
        }
        return $text;
    }
    if (isset($_POST['invite']) && $_POST['invite'] == 'invite') {
        require_once('../email_sender.php');
        $invite_company_id = $company['company_id'];
        $invite_full_name = $_POST['full_name'];
        $invite_email = $_POST['email'];
        $invite_phone = $_POST['phone'];
        $invite_date = date("Y-m-d H:i:s");
        $invite_code = generate_invite_code(16);

        $info = array(
            array('company_id', $invite_company_id),
            array('full_name', $invite_full_name),
            array('email', $invite_email),
            array('phone_number', $invite_phone),
            array('invitation_date', $invite_date),
            array('invitation_code', $invite_code)
        );

        $write = $Database->write_data($info, 'invitation', false);
        $invitation_info = $Database->get_data('email', $invite_email, 'invitation', true);
        $invite_link = SITE_URL . 'invited.php?invited='.$invitation_info['invitation_id'];

        $email_sender = new EmailSender();

        $body = '<pre>
                 Subject: Nøgd undersøkelse

                    Hei.
                    
                    Du har nå fått invitasjon til å besvare spørsmål om hvordan du opplever ditt arbeidsmiljø i relasjon til de samarbeidsutfordringer som har oppstått. Din arbeidsgiver bør ha informert deg om denne.
                    
                    Kartleggingen er konfidensiell. Kartleggingen etterspør ikke person sensitive data.
                    
                    Om du ved en feil har mottatt denne epost, vennligst gi beskjed til oss ved å besvare denne epost så vil vi slette deg fra denne kartleggingen.
                    
                    Vennlig hilsen, Semje Software AS.
                    
                    Klikk på linken under for å starte din besvarelse: 
                    <h5>'. $invite_link .'</h5>
                    invitasjonskode: 
                    <h5>'. $invite_code .'</h5>
                </pre>';

        $send = $email_sender->send_mail($invite_email, 'Satisfied examination', $body);
    }
    ?>
    <div class="card">
        <div class="card-body p-3" id="invite_form_holder" style="display: none">
            <div class="service-area style-two">
                <div class="container-fluid">
                    <div class="row company-row row-50">
                        <div class="col-12 company-password change-password-container">
                            <span class="company-label pass_label">Invite User</span>
                            <form class='form-inline' method="post">
                                <input type='text' id='full_name_input_holder'
                                       name="full_name"
                                       class="col-sm-12 form-fields"
                                       placeholder='Full name'>
                                <input type='text' id='email_input_holder'
                                       name="email"
                                       class="col-sm-12 form-fields"
                                       placeholder='Email'>
                                <input type='text' id='phone_input_holder'
                                       name="phone"
                                       class="col-sm-12 form-fields"
                                       placeholder='Phone number'>
                                <div class="mybutton col-sm-12 d-flex">
                                    <input class='btn btn-success btn-sm form-button'
                                           id='invite_user_button' type="submit" value="invite" name="invite">
                                    <span onclick="add_show_invite()"
                                            class='btn btn-danger btn-sm form-button ms-3'>Cancel
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="card">
        <div class="fs-5 btn btn-primary px-4 ms-4 mt-3" style="width: fit-content" onclick="add_show_invite()">Add new Invitation</div>
        <div class="card-body p-3">
            <table class="table table-striped">
                <thead class="bg-primary">
                <tr>
                    <th scope="col">
                        <h4 style="color:white; margin-left: 20px;"><?php echo $trans->phrase("user_sidebar_phrase18"); ?> </h4>

                    </th>
                    <th scope="col" colspan="4" style="text-align:right;">
                        <h5 style="color:white; margin-right: 20px">
                            <?php
                            echo $trans->phrase('index_phrase17') . " : ";
                            echo $Database->get_name_by_id('company', 'company_name', $company_id);
                            ?>
                        </h5>
                    </th>
                </tr>
                </thead>
                <thead class="bg-secondary">
                <tr style="margin-top:10px !important;">
                    <th scope="col">&nbsp; Invitation ID</th>
                    <th scope="col"><?php echo $trans->phrase('user_composer_phrase13'); ?></th>
                    <th scope="col"><?php echo $trans->phrase('index_phrase1'); ?></th>
                    <th scope="col"><?php echo $trans->phrase('index_phrase12'); ?></th>
                    <th scope="col">Invitation Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($invited_users):
                    foreach ($invited_users as $invited_user): ?>
                        <tr>
                            <th class="pull-right"><?php echo $invited_user['invitation_id']; ?></th>
                            <td><?php echo $invited_user['full_name']; ?></td>
                            <td><?php echo $invited_user['email']; ?></td>
                            <td><?php echo $invited_user['phone_number']; ?></td>
                            <td><?php echo ($invited_user['status']) ? 'Accepted' : 'Not Accepted'?></td>
                        </tr>
                    <?php
                    endforeach;
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function add_show_invite() {
            $('#invite_form_holder').toggle();
        }
    </script>

<?php endif;
