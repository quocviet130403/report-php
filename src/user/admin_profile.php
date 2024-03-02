<?php
if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'):
    require_once('../database.php');
    $Database = new Database();

    $admin = $Database->get_data('admin_id', $_SESSION['account-id'], 'admin', true);

    if (isset($_POST['tfa']) && $_POST['tfa'] == 'save') {
        $admin_id   = $admin['admin_id'];
        $tfa_status = $_POST['tfa_status'];
        $tfa_type   = $_POST['tfa_type'];
        $tfa_email  = $_POST['tfa_email'];
        $tfa_phone  = $_POST['tfa_phone'];

        $info = array(
            array('tfa_status', $tfa_status),
            array('tfa_type', $tfa_type),
            array('tfa_email', $tfa_email),
            array('tfa_phone', $tfa_phone)
        );

        $updated = $Database->update_data($info, 'admin_id', $admin_id, 'admin');
    }

    if (empty($_GET['pass']) && empty($_GET['tfa'])) :
        ?>

        <div class="card">
            <div class="card-body p-3">
                <div class="service-area style-two">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="ab-title text-left">
                                    <h3><?php echo $trans->phrase("profile_edit_profile"); ?></h3>
                                    <h4></h4>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="ab-content">

                                    <form method="post" action="" id="myadminprofile" enctype="multipart/form-data">
                                        <div class="row clearfix">
                                              <div class="col-lg-12">
                                    <div class="">
                                        <div class="profile-pic">
                                            <label class="-label" for="file">
                                                <span class="glyphicon glyphicon-camera"></span>
                                                <span class="editpic"><i class="fa-solid fa-pencil"></i></span>
                                            </label>
                                            <input id="file" class="admin_logo_input" type="file"
                                                   data-admin="<?php echo $admin['admin_id']; ?>"
                                                   accept=".jpg,.jpeg,.png"/>
                                                   <input type="hidden" value="" id="upload_admin_img"/>
                                           
                                            <img src="<?php echo SITE_URL ?>/images/admin_logo/<?php echo $admin['upload_admin_img']; ?>" alt="Company logo">
                                        </div>
                                    </div>
                                </div>
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_admin_profile_phrase2"); ?></span>
                                                <input type="text" id="admin_name_editor_input"
                                                       value="<?php echo $admin['admin_name']; ?>">
                                            </div>
                                            <div class="clearfix"></div>
                                            <input type="hidden" id="admin_email_editor_old"
                                                   value="<?php echo $admin['admin_email']; ?>">

                                            <input type="hidden" id="admin_id_input"
                                                   value="<?php echo $admin['admin_id']; ?>">
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_admin_profile_phrase4"); ?></span>
                                                <input type="email" id="admin_email_editor_input"
                                                       value="<?php echo $admin['admin_email']; ?>">
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_admin_profile_phrase5"); ?></span>
                                                <input type="text" id="admin_role_input"
                                                       value="<?php echo $admin['admin_role']; ?>" disabled>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                <button class="theme-btn btn-style-two view-button" type="submit"
                                                        name="save"><span class="txt">Save</span></button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif;
    if (isset($_GET['pass']) && $_GET['pass'] == 'change') :

        ?>
        <div class="card">
            <div class="card-body p-3">
                <div class="service-area style-two">
                    <div class="container-fluid">
                        <div class="row admin-row row-50">
                            <div class="col-12 admin-password change-password-container">
                                <span class="admin-label pass_label"><?php echo $trans->phrase("user_admin_profile_phrase6"); ?> </span>
                                <div id="admin_pass_editor" data-admin="<?php echo $admin['admin_id']; ?>"
                                     class="editor">
                                    <form class='form-inline'>

                                        <input type='password' id='admin_pass_editor_old' class="col-sm-12 form-fields"
                                               placeholder='<?php echo $trans->phrase("user_js_phrase1"); ?>'>
                                        <input type='password' id='admin_pass_editor_new' class="col-sm-12 form-fields"
                                               placeholder='<?php echo $trans->phrase("user_js_phrase2"); ?>'>
                                        <input type='password' id='admin_pass_editor_confirm'
                                               class="col-sm-12 form-fields"
                                               placeholder='<?php echo $trans->phrase("user_js_phrase3"); ?>'>
                                        <div class="mybutton col-sm-12 ">
                                            <button id='admin_pass_save' class='btn btn-success btn-sm form-button'>
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <?php endif;
    if (isset($_GET['tfa']) && $_GET['tfa'] == 'change') :
        ?>
        <div class="card">
            <div class="card-body p-3">
                <div class="service-area style-two">
                    <div class="container-fluid">
                        <div class="row admin-row row-75">
                            <div class="col-12 admin-tfa change-tfa-container">
                                <span class="admin-label pass_label"><?php echo $trans->phrase("index_phrase29"); ?> </span>
                                <form class='' method="post" action="">
                                    <div class="d-flex mt-4 mb-3">
                                        <div class="me-3">
                                           <?php echo $trans->phrase("auth_email"); ?>
                                        </div>
                                        <div class="">
                                            <input type="radio" name="tfa_status" value="1" id="tfa_enabled"
                                                <?= $admin['tfa_status']? 'checked' : '' ?>
                                                   oninput="checkTfaStatus()"/>
                                            <label for="tfa_enabled"><?php echo $trans->phrase("yes_email"); ?></label>
                                            <input type="radio" name="tfa_status" value="0" id="tfa_disabled"
                                                   <?= !$admin['tfa_status']? 'checked' : '' ?>
                                                   oninput="checkTfaStatus()"/>
                                            <label for="tfa_disabled"><?php echo $trans->phrase("no_email"); ?></label>
                                        </div>
                                    </div>
                                    <div class="inputs-holder d-none" id="inputs_holder">
                                        <div class="d-flex mt-4 mb-3">
                                            <div class="me-3">
                                             <?php echo $trans->phrase("auth_email_factor"); ?>
                                            </div>
                                            <div>
                                                <input type="radio" name="tfa_type" id="tfa_email" value="email"
                                                       <?= $admin['tfa_type'] == 'email' ? 'checked' : '' ?>
                                                       oninput="checkTfaType()"/>
                                                <label for="tfa_email"><?php echo $trans->phrase("email_text"); ?></label>
                                                <input type="radio" name="tfa_type" id="tfa_phone" value="phone"
                                                       <?= $admin['tfa_type'] == 'phone' ? 'checked' : '' ?>
                                                       oninput="checkTfaType()"/>
                                                <label for="tfa_phone"><?php echo $trans->phrase("phone_text"); ?></label>
                                            </div>
                                        </div>
                                        <div class="px-2 px-lg-5 mb-3">
                                            <input type="text" value="<?=  empty($admin['tfa_email']) ? $admin['admin_email'] : $admin['tfa_email'] ?>" name="tfa_email" placeholder="<?php echo $trans->phrase("enter_email_text"); ?>" id="tfa_email_input" class="form-control form-control-sm d-none">
                                            <input type="text" value="<?= !empty($admin['tfa_phone']) ? $admin['tfa_phone']   : '' ?>" name="tfa_phone" placeholder="<?php echo $trans->phrase("enter_phone_text"); ?>" id="tfa_phone_input" class="form-control form-control-sm d-none">
                                        </div>
                                    </div>
                                    <div class="mybutton col-sm-12 text-center">
                                        <input type="submit" name="tfa" class='btn btn-success btn-sm'
                                               value="save">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function checkTfaStatus() {
                let tfa_enabled = document.getElementById('tfa_enabled');
                let tfa_disabled = document.getElementById('tfa_disabled');
                let inputs_holder = document.getElementById('inputs_holder');
                if (tfa_enabled.checked){
                    inputs_holder.classList.contains('d-none') ? inputs_holder.classList.remove('d-none') : '';
                }
                if (tfa_disabled.checked){
                    !inputs_holder.classList.contains('d-none') ? inputs_holder.classList.add('d-none') : '';
                }
            }
            function checkTfaType(){
                let tfa_email = document.getElementById('tfa_email');
                let tfa_phone = document.getElementById('tfa_phone');
                let tfa_email_input = document.getElementById('tfa_email_input');
                let tfa_phone_input = document.getElementById('tfa_phone_input');
                if (tfa_email.checked){
                    tfa_email_input.classList.contains('d-none') ? tfa_email_input.classList.remove('d-none') : '';
                    !tfa_phone_input.classList.contains('d-none') ? tfa_phone_input.classList.add('d-none') : '';
                }
                if (tfa_phone.checked){
                    !tfa_email_input.classList.contains('d-none') ? tfa_email_input.classList.add('d-none') : '';
                    tfa_phone_input.classList.contains('d-none') ? tfa_phone_input.classList.remove('d-none') : '';
                }
            }
            checkTfaStatus();
            checkTfaType();
        </script>

    <?php endif; ?>


<?php
else:
    echo $trans->phrase("user_admin_profile_phrase8");
endif;
?>

