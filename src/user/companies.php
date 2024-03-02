<?php
if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'consultant'):
require_once('../database.php');
$Database = new Database();
?>

<div class="card">
    <div class="card-body p-3">
        <div class="col-12 all-companies">
            <?php
            $companies = array();
            $consultant_companies = '';
            $ids_str = '';
            if ($_SESSION['account-type'] == 'consultant') {
                $consultant = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
                $consultant_companies = array();
                if (!empty($consultant['consultant_companies'])) {
                    $consultant_companies = explode(',', $consultant['consultant_companies']);
                    if (is_array($consultant_companies))
                        $ids_str = "'" . implode("','", $consultant_companies) . "'";
                    $companies = $Database->get_data_multiple('company_id', $ids_str, 'company');
                } else {
                    echo $trans->phrase("user_companies_phrase9");
                }
            } else {
                $companies = $Database->get_multiple_data(false, false, 'company');
            }

            foreach ($companies as $company):


                ?>
                <div class="company-ctn fw-bold text-gray-600">
                    <div class="company-logo">
                        <?php
                        $logo_image = '<?php echo SITE_URL ?>/images/default-company.png';
                        if (file_exists('..<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.png'))
                            $logo_image = '<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.png';
                        else if (file_exists('..<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpg'))
                            $logo_image = '<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpg';
                        else if (file_exists('..<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpeg'))
                            $logo_image = '<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpeg';
                        ?>
                        <img src="<?php echo SITE_URL ?>/images/company_logo/<?php echo $company['upload_company_img']; ?>" alt="Company logo">
                    </div>
                    <div class="row company-row">
                        <div class="col-9">
                            <span class="company-label "><?php echo $trans->phrase("user_companies_phrase6"); ?> </span>
                            <?php if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'): ?>
                                <a class="fw-bolder"
                                   href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&company_id=<?php echo $company['company_id']; ?>">
                                    <?php echo $company['company_name']; ?>
                                </a>
                            <?php elseif ($_SESSION['account-type'] == 'consultant'): ?>
                                <a class="fw-bolder"
                                   href="<?php echo SITE_URL ?>/user/index.php?route=tickets&company_id=<?php echo $company['company_id']; ?>">
                                    <?php echo $company['company_name']; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-3">
                            <?php if ($company['company_status'] == 'pending'): ?>
                                <button
                                        class="btn btn-light-warning btn-sm"><?php echo $trans->phrase("user_companies_phrase3"); ?></button>
                            <?php elseif ($company['company_status'] == 'active'): ?>
                                <button
                                        class="btn btn-light-success btn-sm"><?php echo $trans->phrase("user_companies_phrase5"); ?></button>
                            <?php elseif ($company['company_status'] == 'suspended'): ?>
                                <button
                                        class="btn btn-light-secondary btn-sm"><?php echo $trans->phrase("user_companies_phrase4"); ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row company-row">
                        <div class="col-6">
                            <span class="company-label"><?php echo $trans->phrase("user_companies_phrase7"); ?> </span>
                            <a class="btn btn-light-primary btn-sm company-id"><?php echo $company['company_id']; ?></a>
                        </div>
                        <div class="col-6 company-size">
                            <span class="company-label"><?php echo $trans->phrase("user_companies_phrase8"); ?> </span>
                            <?php echo $company['company_size']; ?>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
        <?php
        else:
            echo $trans->phrase("user_companies_phrase9");
        endif;
        ?>
    </div>
</div>