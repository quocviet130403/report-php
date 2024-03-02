<?php
if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'):
require_once('../database.php');
$Database = new Database();
?>
<div class="card">
    <div class="card-body p-3">
        <div class="col-12 all-companies">
            <?php
            $consultants = $Database->get_multiple_data(false, false, 'consultant');
            foreach ($consultants as $consultant):
                ?>
                <div class="company-ctn fw-bold text-gray-600">
                    <div class="row company-row">
                        <div class="col-9">
                            <span class="company-label "><?php echo $trans->phrase("user_consultant_phrase1"); ?> </span>
                            <a class="fw-bolder"
                               href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile&consultant_id=<?php echo $consultant['consultant_id']; ?>">
                                <?php echo $consultant['consultant_name']; ?>
                            </a>
                        </div>
                        <div class="col-3">
                            <?php if ($consultant['consultant_status'] == 'pending'): ?>
                                <button
                                        class="btn btn-light-warning btn-sm"><?php echo $trans->phrase("user_consultant_phrase3"); ?></button>
                            <?php elseif ($consultant['consultant_status'] == 'active'): ?>
                                <button
                                        class="btn btn-light-success btn-sm"><?php echo $trans->phrase("user_consultant_phrase4"); ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row company-row">
                        <div class="col-6">
                            <span class="consultant-label"><?php echo $trans->phrase("user_consultant_phrase2"); ?> </span>
                            <a class="btn btn-light-primary btn-sm consultant-id"><?php echo $consultant['consultant_id']; ?></a>
                        </div>
                        <div class="col-6 company-size">
                            <span class="company-label"><?php echo $trans->phrase("user_consultant_phrase5"); ?> </span>
                            <?php
                            if (!empty($consultant['consultant_companies'])) {
                                $consultant_companies = explode(',', $consultant['consultant_companies']);
                                if (is_array($consultant_companies) && !empty($consultant_companies))
                                    $assigned_companies = count($consultant_companies);
                            } else {
                                $assigned_companies = 0;
                            }
                            ?>
                            <?php echo $assigned_companies; ?>
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