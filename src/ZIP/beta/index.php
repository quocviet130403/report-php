<?php
session_start();
?>
<?php require_once('header.php'); ?>
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
			<div id="message_success">
			
			</div>
            <div class="row login-ctn-new">
                <div class="col-2"></div>
                <div class="col-8 login-form" id="login_form">
                    <form>
                        <div class="form-group mb-3">
                            <input type="email" id="login_email" class="form-control login-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase1'); ?>">
                        </div>
                        <div class="form-group">
                            <input type="password" id="login_password" class="form-control login-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase2'); ?>">
                        </div>
                        <div class="login-buttons" style="text-align:center;">
                            <a href="javascript:void(0)" class="login-rel-btn"
                               id="signup_consultant_now"><?php echo $trans->phrase('index_phrase31'); ?></a> | <a
                                    href="javascript:void(0)" class="login-rel-btn"
                                    id="signup_now"><?php echo $trans->phrase('index_phrase4'); ?></a>
                            <div>
                                <a href="javascript:void(0)" class="login-rel-btn"
                                   id="forgot_password"><?php echo $trans->phrase('index_phrase3'); ?></a>
                            </div>
                            <input type="submit" id="login_button" class="login-button btn btn-default btn-success"
                                   value="<?php echo $trans->phrase('index_phrase5'); ?>">
                            <a href="javascript:void(0)" id="pricing_list" class="login-rel-btn"
                               onclick="showPricing('<?php echo $trans->phrase('text_close'); ?>')">
                                <?php echo $trans->phrase('text_pricing'); ?>
                            </a>
                        </div>
                        <p class="support-text">Ring support på <a href="tel:950 09 050">950 09 050</a> om du har
                            spørsmål
                            eller trenger hjelp.</p>
                    </form>
                    <div id="login_status" class="status"></div>
                </div>

                <div class="col-8 forgot-pass-form" id="forgot_pass_form">
                    <form>
                        <div class="">
                            <div class="text-white text-center d-flex align-items-center">
                                <p><?php echo $trans->phrase('index_phrase36'); ?></p>
                            </div>
                            <div class="text-white d-flex align-items-center">
                                <div class="d-flex align-items-center mr-2 mr-md-4 mr-lg-5">
                                    <input class="mr-1 mr-md-2" type="radio" name="tfa_type" id="tfa_email" value="email" checked
                                           oninput="checkTfaType()"/>
                                    <label for="tfa_email">Email</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="mr-1 mr-md-2" type="radio" name="tfa_type" id="tfa_phone" value="phone"
                                           oninput="checkTfaType()"/>
                                    <label for="tfa_phone">Phone</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" id="forgot_pass_email" class="form-control forgot-pass-input d-none"
                                   placeholder="<?php echo $trans->phrase('index_phrase6'); ?>">
                            <input type="tel" id="forgot_pass_phone" class="form-control forgot-pass-input d-none"
                                   placeholder="<?php echo $trans->phrase('index_phrase32'); ?>">
                        </div>
                        <div class="forgot-pass-buttons">
                            <a href="javascript:void(0)"
                               id="forgot_pass_login"><?php echo $trans->phrase('index_phrase7'); ?></a>
                            <input type="submit" id="forgot_pass_button"
                                   class="forgot-pass-button btn btn-default btn-danger">
                        </div>
                    </form>
                    <div id="forgot_pass_status" class="status"></div>
                </div>

                <div class="col-12 col-sm-7 signup-form" id="signup_form">
                    <form>
                        <div class="form-group mb-3">
                            <input type="text" id="signup_username" class="form-control signup-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase9'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" id="signup_email" class="form-control signup-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase10'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" id="signup_phone" class="form-control signup-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase12'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" id="signup_password" class="form-control signup-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase11'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" id="signup_confirm_password" class="form-control signup-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase19'); ?>">
                        </div>

                        <div class="form-group">
                            <input type="text" id="signup_company" class="form-control signup-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase13'); ?>">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="signup_terms">
                            <label class="form-check-label signup-terms-label"
                                   for="signup_terms"><?php echo $trans->phrase('index_phrase25'); ?> <a
                                        href="tos.php"><?php echo $trans->phrase('index_phrase26'); ?></a>.</label>
                        </div>
                        <div class="signup-buttons">
                            <a href="javascript:void(0)"
                               id="login_to_account"><?php echo $trans->phrase('index_phrase14'); ?></a> | <a
                                    href="javascript:void(0)"
                                    id="register_company"><?php echo $trans->phrase('index_phrase15'); ?></a>
                            <input type="submit" id="signup_button" class="signup-button btn btn-success"></input>
                        </div>
                    </form>
                    <div id="signup_status" class="status"></div>
                </div>

                <div class="col-12 col-sm-7 consultant-form" id="signup_consultant_form">
                    <form>
                        <div class="form-group mb-3">
                            <input type="text" id="consultant_username" class="form-control consultant-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase9'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" id="consultant_email" class="form-control consultant-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase10'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" id="consultant_phone" class="form-control consultant-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase12'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" id="consultant_password" class="form-control consultant-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase11'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" id="consultant_confirm_password"
                                   class="form-control consultant-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase19'); ?>">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="consultant_terms">
                            <label class="form-check-label signup-terms-label"
                                   for="consultant_terms"><?php echo $trans->phrase('index_phrase25'); ?> <a
                                        href="tos.php"><?php echo $trans->phrase('index_phrase26'); ?></a>.</label>
                        </div>
                        <div class="signup-buttons">
                            <a href="javascript:void(0)"
                               id="consultant_login_to_account"><?php echo $trans->phrase('index_phrase14'); ?></a> | <a
                                    href="javascript:void(0)"
                                    id="consultant_register_company"><?php echo $trans->phrase('index_phrase15'); ?></a>
                            <input type="submit" id="consultant_signup_button"
                                   class="signup-button btn btn-success"></input>
                        </div>
                    </form>
                    <div id="consultant_status" class="status"></div>
                </div>

                <div class="col-12 col-sm-7 register-company-form" id="register_company_form">
                    <form>
                        <div class="form-group mb-3">
                            <input type="text" id="register_company_name" class="form-control register-company-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase17'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <select id="register_company_industry_type" class="form-control register-company-input">
                                <option value=""><?php echo $trans->phrase('index_phrase28'); ?></option>
                                <?php
                                $industry_types = $Database->get_multiple_data(false, false, 'industry_type', null);
                                if ($industry_types) {
                                    foreach ($industry_types as $industry_type) {
                                        $sql = "SELECT * FROM industry_content WHERE industry_id='" . $industry_type['industry_id'] . "' AND lang_code='" . $_SESSION['trans'] . "';";
                                        $industry_type_data = $Database->get_connection()->prepare($sql);
                                        $industry_type_data->execute();
                                        if ($industry_type_data->rowCount() > 0) {
                                            $industry_type_data = $industry_type_data->fetch(PDO::FETCH_ASSOC);
                                            $industry_type['industry_name'] = $industry_type_data['industry_name'];
                                            $industry_type['industry_details'] = $industry_type_data['industry_details'];
                                        }
                                    }
                                    foreach ($industry_types as $industry_type) {
                                        echo '<option value="' . $industry_type['industry_id'] . '">' . $industry_type['industry_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" id="register_company_email" class="form-control register-company-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase18'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" id="register_company_phone" class="form-control register-company-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase20'); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" id="register_company_password"
                                   class="form-control register-company-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase11'); ?>">
                        </div>
                        <div class="form-group">
                            <input type="password" id="register_company_confirm_password"
                                   class="form-control register-company-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase19'); ?>">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="register_company_terms">
                            <label class="form-check-label register-company-terms-label"
                                   for="register_company_terms"><?php echo $trans->phrase('index_phrase25'); ?> <a
                                        href="tos.php"><?php echo $trans->phrase('index_phrase26'); ?></a>.</label>
                        </div>
                        <div class="register-company-buttons">
                            <a href="javascript:void(0)"
                               id="company_login_to_account"><?php echo $trans->phrase('index_phrase22'); ?></a> | <a
                                    href="javascript:void(0)"
                                    id="company_signup_now"><?php echo $trans->phrase('index_phrase23'); ?></a>
                            <input type="submit" id="register_company_button"
                                   class="signup-button btn btn-success"></input>
                        </div>
                    </form>
                    <div id="register_company_status" class="status"></div>
                </div>

                <div class="col-8 pin-code-form" id="pin_code_form">
                    <form>
                        <div class="text-white text-center d-flex align-items-center">
                            <p><?php echo $trans->phrase('index_phrase37'); ?></p>
                        </div>
                        <div class="form-group">
                            <input type="hidden" id="user_type_logged_in"
                                   value="<?= $_SESSION['user_type_logged_in'] ?>">
                            <input type="hidden" id="user_id_logged_in" value="<?= $_SESSION['user_id_logged_in'] ?>">
                            <input type="text" id="pin_code" class="form-control pin-code-input"
                                   placeholder="<?php echo $trans->phrase('index_phrase30'); ?>">
                        </div>
                        <div class="pin-code-buttons">
                            <a href="javascript:void(0)"
                               id="forgot_pass_login"><?php echo $trans->phrase('index_phrase7'); ?></a>
                            <input type="submit" id="submit_pin_code_button" class="pin-code-button btn btn-success">
                        </div>
                    </form>
                    <div id="code_pin_status" class="status"></div>
                </div>

                <div class="col-2"></div>
            </div>
        </div>
    </div>


    <div id="divPricing" class="w3-modal">
    </div>

    <div id="cookieConsent">
        <div id="closeCookieConsent">
            <span style="margin-right:10px"><i class="fa fa-times"></i></span>
        </div>
        <div>&nbsp;</div>
        <div>
            <?php echo $trans->phrase('index_phrase33'); ?>
        </div>
        <div>&nbsp;</div>
        <div>
            <a href="javascript:void(0)" id="cookieAccept" class="btn btn-primary"><?php echo $trans->phrase('index_phrase34'); ?></a>
            <a href="javascript:void(0)" id="noThanks" class="btn btn-danger"><?php echo $trans->phrase('index_phrase35'); ?></a>
        </div>


    </div>
    <script src="<?php echo JQUERY; ?>"></script>
<script>
    function checkTfaType(){
        let tfa_email = document.getElementById('tfa_email');
        let tfa_phone = document.getElementById('tfa_phone');
        let tfa_email_input = document.getElementById('forgot_pass_email');
        let tfa_phone_input = document.getElementById('forgot_pass_phone');
        if (tfa_email.checked){
            tfa_email_input.classList.contains('d-none') ? tfa_email_input.classList.remove('d-none') : '';
            !tfa_phone_input.classList.contains('d-none') ? tfa_phone_input.classList.add('d-none') : '';
        }
        if (tfa_phone.checked){
            !tfa_email_input.classList.contains('d-none') ? tfa_email_input.classList.add('d-none') : '';
            tfa_phone_input.classList.contains('d-none') ? tfa_phone_input.classList.remove('d-none') : '';
        }
    }
    checkTfaType();
    $('#forgot_pass_button').click(function(){
        var userEMAIL=$('#forgot_pass_email').val();
        var userPHONE=$('#forgot_pass_phone').val();
        var currLANG=$('#footer_language_selector').val();

        if(userEMAIL!=''){
            check_user=userEMAIL;
        }
        else if(userPHONE!=''){
            check_user=userPHONE;
        }
        $.ajax({
            url: site_url + "/ajax_validate_user.php",
            type: 'POST',
            dataType: 'JSON',
            data: {
              check_user: check_user,
            },
            success: function (res) {
              if(res.status=='not found'){
                if(currLANG=='nor'){
                    alert("Ugyldig e-post eller telefon");
                    $('#forgot_pass_form').show();
                }
                else if(currLANG=='en'){
                    alert("Invalid Email Or Phone");
                    $('#forgot_pass_form').show();
                }
              }
              else{
                $('#user_type_logged_in').val(res.type);
                $('#user_id_logged_in').val(res.id[0]);
              }
              
            },
          });
        });
        
</script>
<?php require_once('footer.php'); ?>