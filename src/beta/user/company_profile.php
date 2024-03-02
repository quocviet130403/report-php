<import>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/js/sweetalert2/sweetalert2.min.css">
    <script src="<?php echo SITE_URL; ?>/js/sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/js/toastr/toastr.min.css">
</import>
<style>
    body.swal2-height-auto {
        height: 100% !important;
    }

    .form-row {
        align-items: center;
        margin-top: 10px;
        margin-bottom: 5px;
    }
</style>
<?php
if (($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') && isset($_GET['company_id'])) :
    require_once('../database.php');
    $Database = new Database();

    $company = $Database->get_data('company_id', $_GET['company_id'], 'company', true);
    if ($company) :
        ?>

        <div class="card">
            <div class="card-body p-3">
                <div class="row user-content-row">
                    <div class="col-12 company">
                        <!-- <div class="company-widget-title"><?php echo $trans->phrase("user_company_profile_phrase1"); ?></div> -->
                        <div class="company-ctn">
                            <div id="companyLogo" class="company-logo">
                                <?php
                                if (file_exists(SITE_URL.'/images/company_logo/' . $company['company_id'] . '.png'))
                                    $logo_image = SITE_URL.'/images/company_logo/' . $company['company_id'] . '.png';
                                else if (file_exists(SITE_URL.'/images/company_logo/' . $company['company_id'] . '.jpg'))
                                    $logo_image = SITE_URL.'/images/company_logo/' . $company['company_id'] . '.jpg';
                                else if (file_exists(SITE_URL.'/images/company_logo/' . $company['company_id'] . '.jpeg'))
                                    $logo_image = SITE_URL.'/images/company_logo/' . $company['company_id'] . '.jpeg';
                                ?>
                                <img src="<?php echo $logo_image ?>" alt="Company logo">
                                <?php if ($_SESSION['account-type'] == 'company') : ?>
                                    <button id="company_logo_change" class="btn btn-success btn-sm company-logo-btn"><i
                                                class="fas fa-edit"></i></button>
                                    <input type="file" class="company_logo_input"
                                           data-company="<?php echo $company['company_id']; ?>" accept=".jpg,.jpeg,.png"
                                           hidden>
                                           <input type="hidden" value="" id="upload_company_img"/>
                                <?php endif; ?>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-status">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase2"); ?> </span>
                                    <?php if ($company['company_status'] == 'pending') : ?>
                                        <button class="btn btn-light-warning btn-sm"><?php echo $trans->phrase("user_company_profile_phrase3"); ?></button>
                                    <?php elseif ($company['company_status'] == 'active') : ?>
                                        <button class="btn btn-light-success btn-sm"><?php echo $trans->phrase("user_company_profile_phrase4"); ?></button>
                                    <?php elseif ($company['company_status'] == 'suspended') : ?>
                                        <button class="btn btn-light-secondary btn-sm"><?php echo $trans->phrase("user_company_profile_phrase5"); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row company-row comp-nameimp">
                                <div class="col-12 company-name">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase6"); ?> </span>
                                    <div id="company_name_editor" data-company="<?php echo $company['company_id']; ?>"
                                         class="editor">
                                        <?php echo $company['company_name']; ?>
                                        <button id="company_name_editor_button" class="btn btn-light-primary  btn-sm"><i
                                                    class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase7"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-industry-type">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase37"); ?> </span>
                                    <div id="company_industry_type_editor"
                                         data-company="<?php echo $company['company_id']; ?>" class="editor">
                                        <?php
                                        $industry_type_name = "None";
                                        if ($company['company_industry_type']) {
                                            $industry_data = $Database->get_multiple_data('industry_id', $company['company_industry_type'], 'industry_content');
                                            $industry_type = $industry_data[0];
                                            foreach ($industry_data as $industry) {
                                                if ($industry['lang_code'] == $_SESSION['trans']) {
                                                    $industry_type = $industry;
                                                    break;
                                                }
                                            }
                                            $industry_type_name = $industry_type['industry_name'];
                                        }
                                        ?>
                                        <?php echo $industry_type_name; ?>
                                        <button id="company_industry_type_editor_button"
                                                class="btn btn-light-primary  btn-sm"><i
                                                    class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase7"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-email">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase8"); ?> </span>
                                    <div id="company_email_editor" data-company="<?php echo $company['company_id']; ?>"
                                         class="editor">
                                        <?php echo $company['company_email']; ?>
                                        <button id="company_email_editor_button" class="btn btn-light-primary  btn-sm">
                                            <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase7"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-id">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase9"); ?> </span>
                                    <?php echo $company['company_id']; ?>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-billing">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase12"); ?> </span>
                                    <div id="company_payment_cycle_editor"
                                         data-company="<?php echo $company['company_id']; ?>" class="editor">
                                        <?php
                                        if ($company['company_payment_cycle'] == 3)
                                            echo $trans->phrase("user_company_profile_phrase24");
                                        else if ($company['company_payment_cycle'] == 6)
                                            echo $trans->phrase("user_company_profile_phrase25");
                                        if ($company['company_payment_cycle'] == 12)
                                            echo $trans->phrase("user_company_profile_phrase26");
                                        ?>
                                        <button id="company_payment_cycle_editor_button"
                                                class="btn btn-light-primary  btn-sm"><i
                                                    class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-ticket-view">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase28"); ?> </span>
                                    <div id="company_ticket_view_editor"
                                         data-company="<?php echo $company['company_id']; ?>" class="editor">
                                        <?php
                                        if ($company['company_show_tickets'] == 1)
                                            echo $trans->phrase("user_company_profile_phrase29");
                                        else if ($company['company_show_tickets'] == 0)
                                            echo $trans->phrase("user_company_profile_phrase30");
                                        ?>
                                        <button id="company_ticket_view_editor_button"
                                                class="btn btn-light-primary  btn-sm"><i
                                                    class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-password">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase13"); ?> </span>
                                    <div id="company_password_editor"
                                         data-company="<?php echo $company['company_id']; ?>" class="editor">
                                        <button id="company_password_editor_button_admin"
                                                class="btn btn-light-primary  btn-sm"><i
                                                    class="fas fa-key"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row company-row">
                                <div class="col-12 company-password">
                                    <span>Company Role </span>
                                    <div id="company_role_editor" class="editor">
                                        <select class="form-control company-label" id="company_role_select" data-company="<?php echo $company['company_id']; ?>">
                                            <option value="0" class="company-label" <?php if ($company['company_role'] == 0) echo 'selected'; ?>>10 users</option>
                                            <option value="1" class="company-label" <?php if ($company['company_role'] == 1) echo 'selected'; ?>>30 users</option>
                                            <option value="2" class="company-label" <?php if ($company['company_role'] == 2) echo 'selected'; ?>>unlimited user</option>
                                        </select>
                                    </div>
                                </div>
                            </div> -->
                            <div class="row company-row">
                                <div class="col-12 company-renewal">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase31"); ?> </span>
                                    <?php echo $company['company_expire']; ?>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-size">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase10"); ?> </span>
                                    <div id="company_plan_editor" data-admin="1"
                                         data-site_currency="<?php echo SITE_CURRENCY; ?>"
                                         data-site_currency_symbol="<?php echo SITE_CURRENCY_SYMBOL; ?>"
                                         data-company="<?php echo $company['company_id']; ?>" class="editor">
                                        <?php
                                        if (!$company['company_package_id']) :
                                            echo $trans->phrase('user_company_profile_phrase32');
                                            ?>
                                            <!-- <button id="company_plan_editor_button" class="btn btn-light-primary  btn-sm"><i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?></button> -->
                                            <a href="<?php echo SITE_URL ?>/user/index.php?route=company_module&company_id=<?php echo $company['company_id']; ?>"
                                               class="btn btn-light-primary  btn-sm">
                                                <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?>
                                            </a>
                                        <?php
                                        else :
                                            $package_id = $company['company_package_id'];
                                            $package_data = $Database->get_data('package_id', $package_id, 'package', true);
                                            ?>
                                            <!-- <button id="company_plan_editor_button" class="btn btn-light-primary  btn-sm"><i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?></button> -->
                                            <a href="<?php echo SITE_URL ?>/user/index.php?route=company_module&company_id=<?php echo $company['company_id']; ?>"
                                               class="btn btn-light-primary  btn-sm">
                                                <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?>
                                            </a>
                                            <div class="company-package-ctn">
                                                <div class="company-single-package"
                                                     data-package_id="<?php echo $package_id; ?>">
                                                    <div class="price">
                                                        <br> <span
                                                                class="value"><?php echo SITE_CURRENCY_SYMBOL . $package_data['package_price']; ?></span><br>
                                                        <span class="currency"><?php echo SITE_CURRENCY; ?> / <?php echo $trans->phrase('user_company_profile_phrase34'); ?></span>
                                                    </div>
                                                    <div class="name">
                                                        <label><?php echo $trans->phrase('user_company_profile_phrase6'); ?> </span>
                                                            <span class="value"><?php echo $package_data['package_name']; ?></span>
                                                    </div>
                                                    <div class="user">
                                                        <label><?php echo $trans->phrase('user_company_profile_phrase33'); ?> </span>
                                                            <span class="value"><?php echo $package_data['package_user']; ?></span>
                                                    </div>
                                                    <div class="details">
                                                        <?php echo $package_data['package_details']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-report-text">
                                    <label class="company-label"
                                           style="float: none;"><?php echo $trans->phrase("user_company_profile_phrase35"); ?> </span>
                                        <div id="company_report_text_editor"
                                             data-company_id="<?php echo $company['company_id']; ?>">
                                            <?php
                                            $report_text = null;
                                            $sql = "SELECT * FROM `text` WHERE text_company={$company['company_id']} AND text_lang='{$_SESSION['trans']}' AND text_selector='general_report_text'";
                                            $report_content = $Database->get_connection()->prepare($sql);
                                            $report_content->execute();
                                            if ($report_content->rowCount() < 1) $report_content = false;
                                            else $report_content = $report_content->fetch(PDO::FETCH_ASSOC);

                                            if ($report_content) $report_text = $report_content['text_content'];
                                            ?>
                                            <textarea id="company_report_text_input"
                                                      class="form-control"><?php echo $report_text; ?></textarea>

                                            <button id="company_report_text_save" style="float: left;"
                                                    class="btn btn-success btn-sm mb-1 mt-1 mr-1"><i
                                                        class="fas fa-save"></i> <?php echo $trans->phrase("user_company_profile_phrase36"); ?>
                                            </button>

                                            <select id="company_report_text_lang" class="form-control mt-1"
                                                    style="width: 250px; float: left;">
                                                <?php
                                                $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                                                if ($languages) :
                                                    foreach ($languages as $language) :
                                                        $lang_name = $language['lang_name'];
                                                        if ($language['translations']) {
                                                            $translation = json_decode($language['translations'], true);
                                                            if (isset($translation[$_SESSION['trans']])) {
                                                                $lang_name = $translation[$_SESSION['trans']];
                                                            }
                                                        }
                                                        ?>
                                                        <option value="<?php echo $language['lang_code']; ?>" <?php echo ($language['lang_code'] == $_SESSION['trans']) ? "selected" : ""; ?>><?php echo $lang_name ?></option>
                                                    <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </select>
                                        </div>
                                </div>
                            </div>
                            <div class="row company-row">
                                <div class="col-12 company-action">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase14"); ?> </span>
                                    <?php if ($company['company_status'] == 'pending' || $company['company_status'] == 'suspended') : ?>
                                        <button id="company_activate" class="btn btn-light-success btn-sm"
                                                data-company="<?php echo $company['company_id']; ?>"><i
                                                    class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase15"); ?>
                                        </button>
                                    <?php elseif ($company['company_status'] == 'active') : ?>
                                        <button id="company_suspend" class="btn btn-light-warning btn-sm"
                                                data-company="<?php echo $company['company_id']; ?>"><i
                                                    class="fas fa-pause"></i> <?php echo $trans->phrase("user_company_profile_phrase16"); ?>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (time() > strtotime($company['company_expire'] . " - " . COMPANY_RENEW_TIME)) : ?>
                                        <button id="company_renew" class="btn btn-light-dark btn-sm"
                                                data-company="<?php echo $company['company_id']; ?>"><i
                                                    class="fas fa-redo"></i> <?php echo $trans->phrase("user_company_profile_phrase17"); ?>
                                        </button>
                                    <?php endif; ?>

                                    <?php

                                    $currentDate = new DateTime('now');
                                    $expiryDate = new DateTime($company['company_expire']);
                                    // $interval = date_diff($currentDate, $expiryDate);
                                    $daysExpried = 0;
                                    if ($currentDate > $expiryDate) {
                                        $dateDiff = $currentDate->diff($expiryDate);
                                        $daysExpried = $dateDiff->format('%a');
                                    }
                                    if ($daysExpried > 90) {
                                        ?>
                                        <button id="company_deleteTemp" class="btn btn-danger btn-sm"
                                                onclick="showDeleteCompanyModal(
                                                        '<?php echo $company['company_id']; ?>',
                                                        '<?php echo $trans->phrase('text_close'); ?>',
                                                        '<?php echo $trans->phrase('user_company_profile_phrase18'); ?>'
                                                        )" data-company="<?php echo $company['company_id']; ?>">
                                            <i class="fas fa-trash"></i>
                                            <?php
                                            echo $trans->phrase("user_company_profile_phrase18");
                                            ?>
                                        </button>
                                        <div id="divDeleteCompany" class="w3-modal"></div>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

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
                                                <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase20"); ?> </span>
                                                    <a href="<?php echo SITE_URL ?>/user/index.php?route=profile&id=<?php echo $user['user_id']; ?>">
                                                        <div class="user-card-name"> <?php echo $user['user_name']; ?> </div>
                                                    </a>
                                            </div>
                                            <div class="col-4">
                                                <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase21"); ?> </span>
                                                    <div class="user-card-id"> <?php echo $user['user_id']; ?> </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php
                                            if ($_SESSION['account-type'] == 'super_admin') :
                                                ?>
                                                <div class="col-8">
                                                    <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase22"); ?> </span>
                                                        <div class="user-card-email"> <?php echo $user['user_email']; ?> </div>
                                                </div>
                                                <div class="col-4">
                                                    <button id="company_deleteTemp" class="btn btn-danger btn-sm"
                                                            onclick="showdeleteuserdialog('<?php echo $user['user_id']; ?>')"
                                                            data-company="<?php echo $company['company_id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            <?php
                                            else :
                                                ?>
                                                <div class="col-12">
                                                    <label class="user-card-title"><?php echo $trans->phrase("user_company_profile_phrase22"); ?> </span>
                                                        <div class="user-card-email"> <?php echo $user['user_email']; ?> </div>
                                                </div>
                                            <?php
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            endif;
                            ?>
                            <div class="row company-row" style="margin: 15px 0px;">
                                <div class="col-12 company-action">
                                    <span><?php echo $trans->phrase("user_company_profile_phrase14"); ?> </span>
                                    <button id="company_add_user" onclick="add_company_user()"
                                            class="btn btn-light-primary btn-sm"
                                            data-company="<?php echo $company['company_id']; ?>">
                                        <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_add_test"); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex;">
                            <div class="w3-modal" style="margin: 15px 0px;display: none;" id="delete_user_content">
                                <div class="modal-content" style="padding-bottom: 15px;">
                                    <div class="container">
                                        <div class="form-group form-row">
                                            <!-- <label>Name</span> -->
                                            <input type="text" class="form-control" name="username" id="signup_username"
                                                   placeholder="<?php echo $trans->phrase('index_phrase9'); ?>">
                                        </div>
                                        <div class=" form-group form-row">
                                            <!-- <label>Email address</span> -->
                                            <input type="email" class="form-control" name="email" id="signup_email"
                                                   placeholder="<?php echo $trans->phrase('index_phrase10'); ?>">
                                        </div>
                                        <div class=" form-group form-row">
                                            <!-- <label>Telephone</span> -->
                                            <input type="tel" class="form-control" name="telephone" id="signup_phone"
                                                   placeholder="<?php echo $trans->phrase('index_phrase12'); ?>">
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
                                            <button class="btn btn-primary" id="add_company_user_btn">Add</button>&nbsp;
                                            <button id="btn-close" onclick="add_company_user()" class="btn btn-info">
                                                Close
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
    <?php
    else :
        echo $trans->phrase("user_company_profile_phrase23");
    endif;
elseif ($_SESSION['account-type'] == 'company') :
    $company = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);
    if ($company) :

        if (empty($_GET['add']) && empty($_GET['pass']) && empty($_GET['tfa'])) :
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
                                    <div class="service-shape self-part">
                                        <div id="companyLogo" class="profile-pic">
                                            <label class="-label" for="file">
                                                <span class="glyphicon glyphicon-camera"></span>
                                                <span class="editpic"><i class="fa-solid fa-pencil"></i></span>
                                            </label>
                                            <input id="file" class="company_logo_input" type="file"
                                                   data-company="<?php echo $company['company_id']; ?>"
                                                   accept=".jpg,.jpeg,.png"/>
                                                   <input type="hidden" value="" id="upload_company_img"/>
                                            <!-- <input type="file" id="company_logo_input" data-company="<?php echo $company['company_id']; ?>" accept=".jpg,.jpeg,.png"> -->

                                            <?php
                                            //$logo_image = SITE_URL.'/images/default-company.png';
                                            $logo_image = SITE_URL.'/images/company_logo/' . $company['company_id'] . '.png';
                                            if (file_exists(SITE_URL.'/images/company_logo/' . $company['company_id'] . '.png'))
                                                $logo_image = '/images/company_logo/' . $company['company_id'] . '.png';
                                            else if (file_exists(SITE_URL.'/images/company_logo/' . $company['company_id'] . '.jpg'))
                                                $logo_image = '/images/company_logo/' . $company['company_id'] . '.jpg';
                                            else if (file_exists(SITE_URL.'/images/company_logo/' . $company['company_id'] . '.jpeg'))
                                                $logo_image = '/images/company_logo/' . $company['company_id'] . '.jpeg';
                                            ?>
                                            <img src="<?php echo $logo_image; ?>" alt="Company logo">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="ab-content">

                                        <form method="post" action="" id="mycompanyprofile"
                                              enctype="multipart/form-data">
                                            <div class="row clearfix">
                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase6"); ?></span>
                                                    <input type="text" id="company_name_editor_input"
                                                           value="<?php echo $company['company_name']; ?>">
                                                </div>
                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase8"); ?></span>
                                                    <input type="hidden" id="company_email_editor_old"
                                                           value="<?php echo $company['company_email']; ?>">

                                                    <input type="email" id="company_email_editor_input"
                                                           value="<?php echo $company['company_email']; ?>">

                                                </div>
                                                <div class="col-md-6 col-sm-12  form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase37"); ?></span>
                                                    <?php
                                                    $industry_type_data = $Database->get_industry_content();
                                                    //            echo "<pre>";
                                                    //            print_r( $industry_type_data);
                                                    //            echo "</pre>";
                                                    // echo "<br>".$company['company_industry_type'];
                                                    ?>
                                                    <select id="company_industry_editor_types">

                                                        <option value=''><?php echo $trans->phrase("user_js_phrase37"); ?></option>

                                                        <?php
                                                        // $industry_type_name = "None";

                                                        // if ($company['company_industry_type']) {
                                                        //     $industry_data = $Database->get_multiple_data('industry_id', $company['company_industry_type'], 'industry_content');
                                                        //     $industry_type = $industry_data[0];

                                                        foreach ($industry_type_data as $key => $value) {
                                                            if ($company['company_industry_type'] == $value['industry_id']) {

                                                                echo "<option value='" . $value['industry_id'] . "' selected>" . $value['industry_name'] . "</option>";
                                                            } else {
                                                                echo "<option value='" . $value['industry_id'] . "'>" . $value['industry_name'] . "</option>";

                                                            }

                                                        }
                                                        //     foreach ($industry_data as $industry) {
                                                        //          //echo "<option>".$industry['lang_code']."</option>";
                                                        //         if ($industry['lang_code'] == $_SESSION['trans']) {
                                                        //             $industry_type = $industry;
                                                        //             break;
                                                        //         }
                                                        //     }
                                                        //     $industry_type_name = $industry_type['industry_name'];
                                                        // }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-12  form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase9"); ?></span>
                                                    <input type="text" id="company_id_editor_input"
                                                           value="<?php echo $company['company_id']; ?>" disabled>
                                                </div>
                                                <div class="col-md-6 col-sm-12  form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase12"); ?></span>
                                                    <select id="company_payment_cycle_editor_input">
                                                        <option value='3' <?php echo ($company['company_payment_cycle'] == 3) ? 'selected' : ''; ?>><?php echo $trans->phrase("user_js_phrase4"); ?></option>
                                                        <option value='6' <?php echo ($company['company_payment_cycle'] == 6) ? 'selected' : ''; ?>><?php echo $trans->phrase("user_js_phrase5"); ?></option>
                                                        <option value='12' <?php echo ($company['company_payment_cycle'] == 12) ? 'selected' : ''; ?>><?php echo $trans->phrase("user_js_phrase6"); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase28"); ?></span>
                                                    <?php
                                                    if ($company['company_show_tickets'] == 1)
                                                        echo ' <input type="text" id="company_ticket_view_editor_input" value="' . $trans->phrase("user_company_profile_phrase29") . '" disabled>';
                                                    else if ($company['company_show_tickets'] == 0)

                                                        echo ' <input type="text" id="company_ticket_view_editor_input" value="' . $trans->phrase("user_company_profile_phrase30") . '" disabled>';
                                                    ?>
                                                </div>
                                                <!--  <div class="col-md-12 form-group">
      <span>Password</span>
      <br>
        <input type='password' id='company_pass_editor_old' class='col-md-4 col-sm-12' placeholder='<?php echo $trans->phrase("user_js_phrase1"); ?>' value=''>
        <input type='password' id='company_pass_editor_new' class='col-md-4 col-sm-12' placeholder='<?php echo $trans->phrase("user_js_phrase2"); ?>'>
        <input type='password' id='company_pass_editor_confirm' class='col-md-4 col-sm-12' placeholder='<?php echo $trans->phrase("user_js_phrase3"); ?>'>
    </div> -->
                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase2"); ?></span>
                                                    <?php if ($company['company_status'] == 'pending') : ?>
                                                        <input type="text" id="company_status"
                                                               value="<?php echo $trans->phrase("user_company_profile_phrase3"); ?>"
                                                               disabled>

                                                    <?php elseif ($company['company_status'] == 'active') : ?>
                                                        <input type="text" id="company_status"
                                                               value="<?php echo $trans->phrase("user_company_profile_phrase4"); ?>"
                                                               disabled>

                                                    <?php elseif ($company['company_status'] == 'suspended') : ?>
                                                        <input type="text" id="company_status"
                                                               value="<?php echo $trans->phrase("user_company_profile_phrase5"); ?>"
                                                               disabled>

                                                    <?php endif; ?>

                                                </div>
                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase10"); ?></span>
                                                    <br>
                                                    <div id="company_plan_editor" data-admin="0"
                                                         data-site_currency="<?php echo SITE_CURRENCY; ?>"
                                                         data-site_currency_symbol="<?php echo SITE_CURRENCY_SYMBOL; ?>"
                                                         data-company="<?php echo $company['company_id']; ?>"
                                                         class="editor">
                                                        <?php
                                                        //$company['company_package_id'] = 28;
                                                        if (!$company['company_package_id']) :
                                                            echo ' <input type="text" id="company_package_id" value="' . $trans->phrase("user_company_profile_phrase32") . '" disabled>';

                                                            ?>
                                                            <!-- <button id="company_plan_editor_button" class="btn btn-light-primary  btn-sm"><i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?></button> -->
                                                            <!-- <a href="<?php echo SITE_URL ?>/user/index.php?route=company_module&company_id=<?php echo $company['company_id']; ?>" class="btn btn-light-primary  btn-sm">
                    <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?> </a> -->
                                                        <?php
                                                        else :
                                                            $package_id = $company['company_package_id'];
                                                            $package_data = $Database->get_data('package_id', $package_id, 'package', true);

                                                            $package_name = $package_data['package_name'];
                                                            $package_details = $package_data['package_details'];

                                                            $sql = "SELECT * FROM package_content WHERE package_id={$package_data['package_id']} AND package_lang='{$_SESSION['trans']}'";
                                                            $package_content = $Database->get_connection()->prepare($sql);
                                                            $package_content->execute();
                                                            if ($package_content->rowCount() < 1) $package_content = false;
                                                            else $package_content = $package_content->fetch(PDO::FETCH_ASSOC);

                                                            if ($package_content) {
                                                                $package_name = $package_content['package_name'];
                                                                $package_details = $package_content['package_details'];
                                                            }
                                                            ?>
                                                            <!-- <button id="company_plan_editor_button" class="btn btn-light-primary  btn-sm"><i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?></button> -->
                                                            <!-- <a href="<?php echo SITE_URL ?>/user/index.php?route=company_module&company_id=<?php echo $company['company_id']; ?>" class="btn btn-light-primary  btn-sm">
                    <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_company_profile_phrase11"); ?> </a> -->
                                                            <div class="company-package-ctn">
                                                                <div class="company-single-package"
                                                                     data-package_id="<?php echo $package_id; ?>">
                                                                    <div class="price">
                                                                        <br> <span
                                                                                class="value"><?php echo SITE_CURRENCY_SYMBOL . $package_data['package_price']; ?></span><br>
                                                                        <span class="currency"><?php echo SITE_CURRENCY; ?> / <?php echo $trans->phrase('user_company_profile_phrase34'); ?></span>
                                                                    </div>
                                                                    <div class="name">
                                                                        <label><?php echo $trans->phrase('user_company_profile_phrase6'); ?> </label>
                                                                        <span class="value"><?php echo $package_name ?></span>
                                                                    </div>
                                                                    <div class="user">
                                                                        <label><?php echo $trans->phrase('user_company_profile_phrase33'); ?> </label>
                                                                        <span class="value"><?php echo $package_data['package_user']; ?></span>
                                                                    </div>
                                                                    <div class="details">
                                                                        <?php echo $package_details; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php $packages = $Database->get_multiple_data(false, false, 'package', null, true, 'package_size_min ASC', false);

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
                                                                if (count($package_classes) > 0) {
                                                                    ?>
                                                                    <select id='company_plan_editor_classes'
                                                                            class='form-control form-control-sm ml-2 mt-1 select-module'>
                                                                        <option value=''><?php echo $trans->phrase('user_js_phrase26'); ?></option>
                                                                        <?php
                                                                        foreach ($package_classes as $package) {
                                                                            echo '<option value={"min":"' . $package['package_size_min'] . '","max":"' . $package['package_size_max'] . '"}>
                                    ' . $package['package_size_min'] . ' - ' . $package['package_size_max'] . $trans->phrase('user_js_phrase28') . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <?php
                                                                }
                                                            }


                                                            ?>


                                                        <?php
                                                        endif;
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 form-group">
                                                    <span><?php echo $trans->phrase("user_company_profile_phrase35"); ?></span>
                                                    <div id="company_report_text_editor"
                                                         data-company_id="<?php echo $company['company_id']; ?>">
                                                        <?php
                                                        $report_text = null;
                                                        $sql = "SELECT * FROM `text` WHERE text_company={$company['company_id']} AND text_lang='{$_SESSION['trans']}' AND text_selector='general_report_text'";
                                                        $report_content = $Database->get_connection()->prepare($sql);
                                                        $report_content->execute();
                                                        if ($report_content->rowCount() < 1) $report_content = false;
                                                        else $report_content = $report_content->fetch(PDO::FETCH_ASSOC);

                                                        if ($report_content) $report_text = $report_content['text_content'];
                                                        ?>
                                                        <textarea id="company_report_text_input" class="form-control"
                                                                  rows="8"><?php echo $report_text; ?></textarea>

                                                        <!-- <button id="company_report_text_save" style="float: left;" class="btn btn-success btn-sm mb-1 mt-1 mr-1"><i class="fas fa-save"></i> <?php echo $trans->phrase("user_company_profile_phrase36"); ?></button> -->

                                                        <!--   <select id="company_report_text_lang" class="form-control mt-1" style="width: 250px; float: left;">
            <?php
                                                        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                                                        if ($languages) :
                                                            foreach ($languages as $language) :
                                                                $lang_name = $language['lang_name'];
                                                                if ($language['translations']) {
                                                                    $translation = json_decode($language['translations'], true);
                                                                    if (isset($translation[$_SESSION['trans']])) {
                                                                        $lang_name = $translation[$_SESSION['trans']];
                                                                    }
                                                                }
                                                                ?>
                    <option value="<?php echo $language['lang_code']; ?>" <?php echo ($language['lang_code'] == $_SESSION['trans']) ? "selected" : ""; ?>><?php echo $lang_name ?></option>
            <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
        </select> -->
                                                    </div>
                                                </div>


                                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                    <button class="theme-btn btn-style-two view-button" type="submit"
                                                            name="save"><span class="txt"><?php echo $trans->phrase("save_btn"); ?></span></button>
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
                            <div class="row company-row row-50">
                                <div class="col-12 company-password change-password-container">
                                    <span class="company-label pass_label"><?php echo $trans->phrase("user_company_profile_phrase13"); ?> </span>
                                    <div id="company_password_editor"
                                         data-company="<?php echo $company['company_id']; ?>" class="editor">
                                        <form class='form-inline'>
                                            <input type='password' id='company_pass_editor_old'
                                                   class="col-sm-12 form-fields"
                                                   placeholder='<?php echo $trans->phrase("user_js_phrase1"); ?>'>
                                            <input type='password' id='company_pass_editor_new'
                                                   class="col-sm-12 form-fields"
                                                   placeholder='<?php echo $trans->phrase("user_js_phrase2"); ?>'>
                                            <input type='password' id='company_pass_editor_confirm'
                                                   class="col-sm-12 form-fields"
                                                   placeholder='<?php echo $trans->phrase("user_js_phrase3"); ?>'>
                                            <div class="mybutton col-sm-12">
                                                <button id='company_pass_editor_save'
                                                        class='btn btn-success btn-sm form-button'><?php echo $trans->phrase("save_btn"); ?>
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
                $company_id = $company['company_id'];
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

                $updated = $Database->update_data($info, 'company_id', $company_id, 'company');
            }
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
                                                    <?= $company['tfa_status'] ? 'checked' : '' ?>
                                                       oninput="checkTfaStatus()"/>
                                                <label for="tfa_enabled">yes</label>
                                                <input type="radio" name="tfa_status" value="0" id="tfa_disabled"
                                                    <?= !$company['tfa_status'] ? 'checked' : '' ?>
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
                                                        <?= $company['tfa_type'] == 'email' ? 'checked' : '' ?>
                                                           oninput="checkTfaType()"/>
                                                    <label for="tfa_email"><?php echo $trans->phrase("email_text"); ?></label>
                                                    <input type="radio" name="tfa_type" id="tfa_phone" value="phone"
                                                        <?= $company['tfa_type'] == 'phone' ? 'checked' : '' ?>
                                                           oninput="checkTfaType()"/>
                                                    <label for="tfa_phone"><?php echo $trans->phrase("phone_text"); ?></label>
                                                </div>
                                            </div>
                                            <div class="px-2 px-lg-5 mb-3">
                                                <input type="text"
                                                       value="<?= empty($company['tfa_email']) ? $company['company_email'] : $company['tfa_email'] ?>"
                                                       name="tfa_email" placeholder="Enter your email"
                                                       id="tfa_email_input" class="form-control form-control-sm d-none">
                                                <input type="text"
                                                       value="<?= empty($company['tfa_phone']) ? $company['company_phone'] : $company['tfa_phone'] ?>"
                                                       name="tfa_phone" placeholder="Enter your phone"
                                                       id="tfa_phone_input" class="form-control form-control-sm d-none">
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
        </div>
        </div>

    <?php
    else :
        echo $trans->phrase("user_company_profile_phrase23");
    endif;
else :
    echo $trans->phrase("user_company_profile_phrase27");
endif;
?>