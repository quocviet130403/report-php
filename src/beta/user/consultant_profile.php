<?php
if ($_SESSION['account-type'] == 'consultant' || $_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'):
    require_once('../database.php');
    $Database = new Database();

    if (isset($_SESSION['account-id']) && $_SESSION['account-type'] == 'consultant')
        $consultant_ID = $_SESSION['account-id'];
    elseif (isset($_GET['consultant_id']))
        $consultant_ID = $_GET['consultant_id'];

    $consultant = $Database->get_data('consultant_id', $consultant_ID, 'consultant', true);

    if (isset($_POST['tfa']) && $_POST['tfa'] == 'save') {
        $consultant_id = $consultant_ID;
        $tfa_status = $_POST['tfa_status'];
        $tfa_type = $_POST['tfa_type'];
        $tfa_email = $_POST['tfa_email'];
        $tfa_phone = $_POST['tfa_phone'];

        $info = array(
            array('tfa_status', $tfa_status),
            array('tfa_type', $tfa_type),
            array('tfa_email', $tfa_email),
            array('tfa_phone', $tfa_phone)
        );

        $updated = $Database->update_data($info, 'consultant_id', $consultant_id, 'consultant');
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

                                    <form method="post" action="" id="myconsultantprofile"
                                          enctype="multipart/form-data">
                                        <div class="row clearfix">
                                                                            <div class="col-lg-12">
                                    <div class="">
                                        <div class="profile-pic">
                                            <label class="-label" for="file">
                                                <span class="glyphicon glyphicon-camera"></span>
                                                <span class="editpic"><i class="fa-solid fa-pencil"></i></span>
                                            </label>
                                            <input id="file" class="consultant_logo_input" type="file"
                                                   data-consultant="<?php echo $consultant_ID; ?>"
                                                   accept=".jpg,.jpeg,.png"/>
                                                   <input type="hidden" value="" id="upload_consultant_img"/>
                                            <!-- <input type="file" id="company_logo_input" data-company="<?php echo $company['company_id']; ?>" accept=".jpg,.jpeg,.png"> -->

                                           
                                            <img src="<?php echo SITE_URL ?>/images/consultant_logo/<?php echo $consultant['upload_consultant_img']; ?>" alt="Company logo">
                                        </div>
                                    </div>
                                </div>
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_consultant_profile_phrase2"); ?></span>
                                                <input type="text" id="consultant_name_editor_input"
                                                       value="<?php echo $consultant['consultant_name']; ?>">
                                            </div>
                                            <div class="clearfix"></div>
                                            <input type="hidden" id="consultant_email_editor_old"
                                                   value="<?php echo $consultant['consultant_email']; ?>">

                                            <input type="hidden" id="consultant_id_input"
                                                   value="<?php echo $consultant_ID; ?>">
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <span><?php echo $trans->phrase("user_consultant_profile_phrase4"); ?></span>
                                                <input type="email" id="consultant_email_editor_input"
                                                       value="<?php echo $consultant['consultant_email']; ?>">
                                            </div>
                                            <?php if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'):
                                                $companies = $Database->get_multiple_data(false, false, 'company');
                                                $consultant_companies = array();
                                                if (!empty($consultant['consultant_companies']))
                                                    $consultant_companies = explode(',', $consultant['consultant_companies']);
                                                if (!empty($companies)) :?>
                                                    <span><?php echo $trans->phrase("user_consultant_profile_phrase9"); ?></span>
                                                    <?php foreach ($companies as $company): ?>
                                                        <?php if (is_array($consultant_companies) && !empty($consultant_companies)): ?>
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <input id="<?= $company['company_name'] ?>"
                                                                           type="checkbox"
                                                                        <?= in_array($company['company_id'], $consultant_companies) ? 'checked' : '' ?>
                                                                        <?= (!in_array($company['company_id'], $consultant_companies) && ($company['assigned'] == 1)) ? 'disabled' : '' ?>
                                                                           class="form-check mb-0 consultant-company"
                                                                           name="consultant_companies"
                                                                           value="<?= $company['company_id'] ?>">
                                                                </div>
                                                                <label class="ms-3 mb-0 mt-2"
                                                                       for="<?= $company['company_name'] ?>"><?= $company['company_name'] ?>
                                                                    <span class="ms-2 small"><?= (!in_array($company['company_id'], $consultant_companies) && ($company['assigned'] == 1)) ? '(Already Assigned)' : '' ?></span></label>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="d-flex align-items-center">
                                                                <div>

                                                                    <input id="<?= $company['company_name'] ?>"
                                                                           type="checkbox"
                                                                        <?= (!in_array($company['company_id'], $consultant_companies) && ($company['assigned'] == 1)) ? 'disabled' : '' ?>
                                                                           class="form-check mb-0 consultant-company"
                                                                           name="consultant_companies"
                                                                           value="<?= $company['company_id'] ?>">
                                                                </div>
                                                                <label class="ms-3 mb-0 mt-2"
                                                                       for="<?= $company['company_name'] ?>"><?= $company['company_name'] ?>
                                                                    <span class="ms-2 small"><?= (!in_array($company['company_id'], $consultant_companies) && ($company['assigned'] == 1)) ? '(Already Assigned)' : '' ?></span></label>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <div class="mt-5">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase2"); ?></span>
                                                    <select id="consultant_status" class="form-select w-50"
                                                            name="consultant_status">
                                                        <?php if ($consultant['consultant_status'] == 'active'): ?>
                                                            <option value="pending"><?php echo $trans->phrase("user_consultant_phrase3"); ?></option>
                                                            <option value="active"
                                                                    selected><?php echo $trans->phrase("user_consultant_phrase4"); ?></option>
                                                        <?php else : ?>
                                                            <option value="pending"
                                                                    selected><?php echo $trans->phrase("user_consultant_phrase3"); ?></option>
                                                            <option value="active"><?php echo $trans->phrase("user_consultant_phrase4"); ?></option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>
                                            <div class="clearfix"></div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 form-group mt-4">
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
                        <div class="row consultant-row row-50">
                            <div class="col-12 consultant-password change-password-container">
                                <span class="consultant-label pass_label"><?php echo $trans->phrase("user_consultant_profile_phrase6"); ?> </span>
                                <div id="consultant_pass_editor"
                                     data-consultant="<?php echo $consultant_ID; ?>"
                                     class="editor">
                                    <form class='form-inline'>

                                        <input type='password' id='consultant_pass_editor_old'
                                               class="col-sm-12 form-fields"
                                               placeholder='<?php echo $trans->phrase("user_js_phrase1"); ?>'>
                                        <input type='password' id='consultant_pass_editor_new'
                                               class="col-sm-12 form-fields"
                                               placeholder='<?php echo $trans->phrase("user_js_phrase2"); ?>'>
                                        <input type='password' id='consultant_pass_editor_confirm'
                                               class="col-sm-12 form-fields"
                                               placeholder='<?php echo $trans->phrase("user_js_phrase3"); ?>'>
                                        <div class="mybutton col-sm-12 ">
                                            <button id='consultant_pass_save'
                                                    class='btn btn-success btn-sm form-button'>
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
                        <div class="row consultant-row row-75">
                            <div class="col-12 consultant-tfa change-tfa-container">
                                <span class="consultant-label pass_label"><?php echo $trans->phrase("index_phrase29"); ?> </span>
                                <form class='' method="post" action="">
                                    <div class="d-flex mt-4 mb-3">
                                        <div class="me-3">
                                             <?php echo $trans->phrase("auth_email"); ?>
                                        </div>
                                        <div class="">
                                            <input type="radio" name="tfa_status" value="1" id="tfa_enabled"
                                                <?= $consultant['tfa_status'] ? 'checked' : '' ?>
                                                   oninput="checkTfaStatus()"/>
                                            <label for="tfa_enabled">yes</label>
                                            <input type="radio" name="tfa_status" value="0" id="tfa_disabled"
                                                <?= !$consultant['tfa_status'] ? 'checked' : '' ?>
                                                   oninput="checkTfaStatus()"/>
                                            <label for="tfa_disabled">no</label>
                                        </div>
                                    </div>
                                    <div class="inputs-holder d-none" id="inputs_holder">
                                        <div class="d-flex mt-4 mb-3">
                                            <div class="me-3">
                                                <?php echo $trans->phrase("auth_email_factor"); ?>
                                            </div>
                                            <div>
                                                <input type="radio" name="tfa_type" id="tfa_email" value="email"
                                                    <?= $consultant['tfa_type'] == 'email' ? 'checked' : '' ?>
                                                       oninput="checkTfaType()"/>
                                                <label for="tfa_email"><?php echo $trans->phrase("email_text"); ?></label>
                                                <input type="radio" name="tfa_type" id="tfa_phone" value="phone"
                                                    <?= $consultant['tfa_type'] == 'phone' ? 'checked' : '' ?>
                                                       oninput="checkTfaType()"/>
                                                <label for="tfa_phone"><?php echo $trans->phrase("phone_text"); ?></label>
                                            </div>
                                        </div>
                                        <div class="px-2 px-lg-5 mb-3">
                                            <input type="text"
                                                   value="<?= empty($consultant['tfa_email']) ? $consultant['consultant_email'] : $consultant['tfa_email'] ?>"
                                                   name="tfa_email" placeholder="Enter your email"
                                                   id="tfa_email_input"
                                                   class="form-control form-control-sm d-none">
                                            <input type="text"
                                                   value="<?= empty($consultant['tfa_phone']) ? $consultant['consultant_phone'] : $consultant['tfa_phone'] ?>"
                                                   name="tfa_phone" placeholder="Enter your phone"
                                                   id="tfa_phone_input"
                                                   class="form-control form-control-sm d-none">
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
else:
    echo $trans->phrase("user_consultant_profile_phrase8");
endif;
?>