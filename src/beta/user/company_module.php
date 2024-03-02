<?php
if (($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'company') && isset($_GET['company_id'])) :
  require_once('../database.php');
  $Database = new Database();

  $company = $Database->get_data('company_id', $_GET['company_id'], 'company', true);

  if ($company) :
    $packages = $Database->get_data('package_id', $company['company_package_id'], 'package', true);
  endif;

endif;
?>
<style>
  .company_module {
    flex-direction: column;
  }
</style>
<div class="card">
  <div class="card-body p-3">
    <div class="row user-content-row">
      <div class="col-12 company">
        <div class="company-widget-title"><?php echo $trans->phrase("user_company_profile_phrase10"); ?></div>
        <div class="company-ctn">
          <div class="row company-row">
            <div class="col-12 company-size">
              <label class="company-label"><?php echo $trans->phrase("user_company_profile_phrase10"); ?> </label>
              <div id="company_plan_editor1" data-admin="1" data-site_currency="<?php echo SITE_CURRENCY; ?>" data-site_currency_symbol="<?php echo SITE_CURRENCY_SYMBOL; ?>" data-company="<?php echo $company['company_id']; ?>" class="editor">
                <form class='form-inline'>
                  <select id='company_plan_editor_classes' class='form-control form-control-sm ml-2 mt-1'>
                    <option value=''><?php echo $trans->phrase("user_js_phrase26"); ?></option>
                  </select>
                  <button id='company_plan_editor_save' class='btn btn-success btn-sm ml-2 mt-1' <?php if ($_SESSION['account-type'] == 'company') echo "disabled" ?>><i class='fas fa-save'></i></button>
                </form>
              </div>
            </div>
            <div class="col-12 company-size">
              <div></div>
              <div id="company_plan_editor" data-admin="1" data-site_currency="<?php echo SITE_CURRENCY; ?>" data-site_currency_symbol="<?php echo SITE_CURRENCY_SYMBOL; ?>" data-company="<?php echo $company['company_id']; ?>"></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>