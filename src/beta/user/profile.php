<?php
if (($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') && isset($_GET['id'])) :
    require_once('../database.php');
    $Database = new Database();

    $user = $Database->get_data('user_id', $_GET['id'], 'user', true);
    if ($user) :
        $company = $Database->get_data('company_id', $user['user_company_id'], 'company', true);
?>

        <div class="card">
            <div class="card-body p-3">
                <div class="row user-content-row">
                    <div class="col-12 profile">
                        <div class="profile-widget-title">
                            <?php echo $trans->phrase("user_profile_phrase1"); ?>
                        </div>
                        <div class="profile-ctn">
                            <div class="row profile-row">
                                <div class="col-12 profile-name">
                                    <label class="profile-label"><?php echo $trans->phrase("user_profile_phrase2"); ?> </label>
                                    <div id="user_name_editor" data-user="<?php echo $user['user_id']; ?>" class="user-editor">
                                        <?php echo $user['user_name']; ?>
                                        <button id="user_name_editor_button" class="btn btn-light-primary btn-sm">
                                            <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_profile_phrase3"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row profile-row">
                                <div class="col-12 profile-email">
                                    <label class="profile-label"><?php echo $trans->phrase("user_profile_phrase4"); ?> </label>
                                    <div id="user_email_editor" data-user="<?php echo $user['user_id']; ?>" class="user-editor">
                                        <?php echo $user['user_email']; ?>
                                        <button id="user_email_editor_button" class="btn btn-light-primary btn-sm">
                                            <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_profile_phrase3"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row profile-row">
                                <div class="col-12 profile-company">
                                    <label class="profile-label"><?php echo $trans->phrase("user_profile_phrase5"); ?> </label>
                                    <?php echo $company['company_name']; ?>
                                </div>
                            </div>
                            <div class="row profile-row">
                                <div class="col-12 profile-password">
                                    <label class="profile-label"><?php echo $trans->phrase("user_profile_phrase6"); ?> </label>
                                    <div id="user_pass_editor" data-user="<?php echo $user['user_id']; ?>" class="user-editor">
                                        <button id="user_pass_change_admin" class="btn btn-light-primary btn-sm">
                                            <i class="fas fa-key"></i> <?php echo $trans->phrase("user_profile_phrase7"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    else : echo $trans->phrase("user_profile_phrase8");
    endif;
elseif ($_SESSION['account-type'] == 'user') :
    $user = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
    $company = $Database->get_data('company_id', $user['user_company_id'], 'company', true);
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
                                    <div class="">
                                    <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">

                                        <div class="profile-pic">
                                            <label class="-label" for="file">
                                                <span class="glyphicon glyphicon-camera"></span>
                                                <span class="editpic"><i class="fa-solid fa-pencil"></i></span>
                                            </label>
                                           
                                            <input id="file" class="user_logo_input" type="file" name="imgUpload"
                                                   data-admin="<?php echo $user['user_id']; ?>"
                                                   accept=".jpg,.jpeg,.png" />
                                                   <input type="hidden" name="data" value="" id=""/>
                                                   <input type="hidden" name="userID" value="<?php echo $user['user_id']; ?>" id=""/>
                                           
                                            <img src="<?php echo SITE_URL ?>/images/profilepic/<?php echo $user['user_profile']; ?>" id="imgPreview" alt="Company logo">
    </form>
    <script src="<?php echo JQUERY; ?>"></script>
    <script>
     // 

     $(".user_logo_input").change(function() {
        
                var formData = new FormData($("#uploadForm")[0]);
console.log(formData);
var fileInput = $(this)[0];

// Check if a file is selected
if (fileInput.files && fileInput.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
        // Display the selected image as a preview
        $("#imgPreview").attr("src", e.target.result);
    };
    reader.readAsDataURL(fileInput.files[0]);
}
                $.ajax({
                    url: site_url + "/ajax_upload_img.php",
                    type: "POST",
                    data: formData,
                    processData: false,
    contentType: false,
    cache: false,
                    success: function(response) {
                       
                        
                    },
                
                });
            });
      
            // 

            $('#remove_img').click(function(){
                alert("R");
                userID=$('#user_id_input').val();
                $.ajax({
                    url: site_url + "/ajax_remove_img.php",
                    type: "POST",
                    data: {
                        userID:userID
                    },

                    success: function(response) {
                       
                        
                    },
                
                }); 
            });
</script>
                                        </div>
                                    </div>
                                </div>
                                        
                            <div class="col-lg-12">
                                <div class="ab-content">

                                    <!--<form id="myuserprofilecustom" enctype="multipart/form-data">
                                        <div class="row clearfix">
                                            <div class="statusMsg"></div>
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_profile_phrase2"); ?></span>
                                                <input type="text" id="user_name_editor_input" name="user_name_editor_input"
                                                       value="<?php echo $user['user_name']; ?>">
                                            </div>
                                            <div class="clearfix"></div>
                                            <input type="hidden" id="user_email_editor_old"
                                                   value="<?php echo $user['user_email']; ?>" name="user_email_editor_old">

                                            <input type="hidden" id="user_id_input"
                                                   value="<?php echo $user['user_id']; ?>" name="user_id_input">
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_profile_phrase4"); ?></span>
                                                <input type="email" id="user_email_editor_input"
                                                       value="<?php echo $user['user_email']; ?>">
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_profile_phrase5"); ?></span>
                                                <input type="text" id="user_company_name_input"
                                                       value="<?php echo $company['company_name']; ?>" name="user_company_name_input" disabled >
                                            </div>
                                            <input type="hidden" id="update_user_data"
                                                       name="sign" value="update_user_data">
                                            <div class="clearfix"></div>
                                             <div class="col-md-6 col-sm-12 form-group">
                                                    <span>Profile Picture</span>
                                                    <input type="file" class="form-control" name="user_profile" id="user_profile"><br><p style="color:red" id="prouploadtext"></p>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                <input type="submit" name="submit" id="user_update_btn_submit" class="theme-btn btn-style-two view-button" value="Save"/>
                                                <button class="theme-btn btn-style-two view-button" id="user_update_btn_submit"><span class="txt">Save</span></button>
                                            </div>

                                        </div>
                                    </form>-->
                                    <form id="fupForm" enctype="multipart/form-data">
                                        <div class="statusMsg"></div>
                                        <div class="form-group">
                                            <span><?php echo $trans->phrase("user_profile_phrase2"); ?></span>
                                            <input type="text" id="user_name_editor_input" name="user_name_editor_input" value="<?php echo $user['user_name']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <span><?php echo $trans->phrase("user_profile_phrase4"); ?></span>
                                            <input type="email" id="user_email_editor_input" name="user_email_editor_input" value="<?php echo $user['user_email']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <span><?php echo $trans->phrase("user_profile_phrase5"); ?></span>
                                            <input type="text" id="user_company_name_input" value="<?php echo $company['company_name']; ?>" name="user_company_name_input" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="file"><?php echo $trans->phrase("user_sidebar_phrase1"); ?></label>
                                            <input type="file" class="form-control" id="file" name="file" />
                                            <?php if (!empty($user['user_profile'])) { ?>
                                                <div class="col-6" id="preview">
                                                    <img src="<?php echo SITE_URL ?>/images/profilepic/<?php echo $user['user_profile']; ?>"  alt="Company logo" style="width: 100%;">
                                                </div>
                                                <button class='btn btn-danger' id="remove_img"><?php echo $trans->phrase("remove_text"); ?></button>
                                            <?php } else { ?>
                                                <div class="col-6" id="preview">
                                                </div>
                                                <p class="alert alert-danger"><?php echo $trans->phrase("no_profile"); ?></p>
                                            <?php } ?>
                                        </div>
                                        <input type="hidden" id="update_user_data" name="sign" value="update_user_data">
                                        <input type="hidden" id="user_email_editor_old" value="<?php echo $user['user_email']; ?>" name="user_email_editor_old">
                                        <input type="hidden" id="user_id_input" value="<?php echo $user['user_id']; ?>" name="user_id_input">
                                        <input type="hidden" id="user_profile" value="<?php echo $user['user_profile']; ?>" name="user_profile">
                                        <input type="submit" name="submit" class="btn btn-primary submitBtn" value="<?php echo $trans->phrase("user_profile_phrase14"); ?>" />
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
                        <div class="row user-row row-50">
                            <div class="col-12 user-password change-password-container">
                                <span class="user-label pass_label"><?php echo $trans->phrase("user_profile_phrase6"); ?> </span>
                                <div id="user_pass_editor" data-user="<?php echo $user['user_id']; ?>" class="editor">
                                    <form class='form-inline'>
                                        <input type='password' id='user_pass_editor_old' class="col-sm-12 form-fields" placeholder='<?php echo $trans->phrase("user_js_phrase1"); ?>'>
                                        <input type='password' id='user_pass_editor_new' class="col-sm-12 form-fields" placeholder='<?php echo $trans->phrase("user_js_phrase2"); ?>'>
                                        <input type='password' id='user_pass_editor_confirm' class="col-sm-12 form-fields" placeholder='<?php echo $trans->phrase("user_js_phrase3"); ?>'>
                                        <div class="mybutton col-sm-12">
                                            <button id='user_pass_save' data-mismatch-error="<?php echo $trans->phrase("option_server_phrase96"); ?>" class='btn btn-success btn-sm form-button'>
                                                <?php echo $trans->phrase("save_btn"); ?>
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
        if (isset($_POST['tfa']) && $_POST['tfa'] == 'save') {
            $user_id   = $user['user_id'];
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

            $updated = $Database->update_data($info, 'user_id', $user_id, 'user');
        }
    ?>
        <div class="card">
            <div class="card-body p-3">
                <div class="service-area style-two">
                    <div class="container-fluid">
                        <div class="row user-row row-75">
                            <div class="col-12 user-tfa change-tfa-container">
                                <span class="user-label pass_label"><?php echo $trans->phrase("index_phrase29"); ?> </span>
                                <form class='' method="post" action="">
                                    <div class="d-flex mt-4 mb-3">
                                        <div class="me-3">
                                            <?php echo $trans->phrase("auth_email"); ?>
                                        </div>
                                        <div class="">
                                            <input type="radio" name="tfa_status" value="1" id="tfa_enabled" <?= $user['tfa_status'] ? 'checked' : '' ?> oninput="checkTfaStatus()" />
                                            <label for="tfa_enabled">yes</label>
                                            <input type="radio" name="tfa_status" value="0" id="tfa_disabled" <?= !$user['tfa_status'] ? 'checked' : '' ?> oninput="checkTfaStatus()" />
                                            <label for="tfa_disabled">no</label>
                                        </div>
                                    </div>
                                    <div class="inputs-holder d-none" id="inputs_holder">
                                        <div class="d-flex mt-4 mb-3">
                                            <div class="me-3">
                                                <?php echo $trans->phrase("auth_email_factor"); ?>
                                            </div>
                                            <div>
                                                <input type="radio" name="tfa_type" id="tfa_email" value="email" <?= $user['tfa_type'] == 'email' ? 'checked' : '' ?> oninput="checkTfaType()" />
                                                <label for="tfa_email"><?php echo $trans->phrase("email_text"); ?></label>
                                                <input type="radio" name="tfa_type" id="tfa_phone" value="phone" <?= $user['tfa_type'] == 'phone' ? 'checked' : '' ?> oninput="checkTfaType()" />
                                                <label for="tfa_phone"><?php echo $trans->phrase("phone_text"); ?></label>
                                            </div>
                                        </div>
                                        <div class="px-2 px-lg-5 mb-3">
                                            <input type="text" value="<?= empty($user['tfa_email']) ? $user['user_email'] : $user['tfa_email'] ?>" name="tfa_email" placeholder="Enter your email" id="tfa_email_input" class="form-control form-control-sm d-none">
                                            <input type="text" value="<?= empty($user['tfa_phone']) ? $user['user_phone'] : $user['tfa_phone'] ?>" name="tfa_phone" placeholder="Enter your phone" id="tfa_phone_input" class="form-control form-control-sm d-none">
                                        </div>
                                    </div>
                                    <div class="mybutton col-sm-12 text-center">
                                        <input type="submit" name="tfa" class='btn btn-success btn-sm' value="save">
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
                if (tfa_enabled.checked) {
                    inputs_holder.classList.contains('d-none') ? inputs_holder.classList.remove('d-none') : '';
                }
                if (tfa_disabled.checked) {
                    !inputs_holder.classList.contains('d-none') ? inputs_holder.classList.add('d-none') : '';
                }
            }

            function checkTfaType() {
                let tfa_email = document.getElementById('tfa_email');
                let tfa_phone = document.getElementById('tfa_phone');
                let tfa_email_input = document.getElementById('tfa_email_input');
                let tfa_phone_input = document.getElementById('tfa_phone_input');
                if (tfa_email.checked) {
                    tfa_email_input.classList.contains('d-none') ? tfa_email_input.classList.remove('d-none') : '';
                    !tfa_phone_input.classList.contains('d-none') ? tfa_phone_input.classList.add('d-none') : '';
                }
                if (tfa_phone.checked) {
                    !tfa_email_input.classList.contains('d-none') ? tfa_email_input.classList.add('d-none') : '';
                    tfa_phone_input.classList.contains('d-none') ? tfa_phone_input.classList.remove('d-none') : '';
                }
            }
            checkTfaStatus();
            checkTfaType();

           
        </script>

    <?php endif; ?>


<?php
else :
    echo $trans->phrase("user_profile_phrase9");
endif;
?>

