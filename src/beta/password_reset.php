<?php require_once('header.php'); ?>
<div class="row login-parent">
    <div class="col-12">
        <div class="row login-ctn" style="color: white">
            <?php
                require_once('database.php');
                if(isset($_GET['id']) && strlen($_GET['id']) > 8):
                    $request_id = $_GET['id'];
                    $Database = new Database();
                    $reset_data = $Database->get_data('pass_token', $request_id, 'password_reset', true);
                    if(!$reset_data && ((int) $reset_data['pass_expire']) < time()):
                        echo $trans->phrase('password_reset_phrase1');
                    else:
            ?>
            <div class="col-12 col-sm-7 pass-reset-form" id="pass_reset_form">
                <form>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-key"></i></div>
                        </div>
                        <input type="password" id="new_pass" class="form-control pass-reset-input" placeholder="<?php echo $trans->phrase('password_reset_phrase2'); ?>">
                    </div>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-key"></i></div>
                        </div>
                        <input type="password" id="confirm_pass" class="form-control pass-reset-input" placeholder="<?php echo $trans->phrase('password_reset_phrase3'); ?>">
                    </div>
                    <div class="pass-reset-buttons">
                        <button type="submit" id="pass_reset_button" data-request-id="<?php echo $_GET['id']; ?>" class="pass-reset-button btn btn-dark btn-sm"><?php echo $trans->phrase('password_reset_phrase4'); ?></button>
                    </div>
                </form>
                <div id="pass_reset_status" class="status"></div>
            </div>
            <div class="col-12 col-sm-5 login-logo">
                <img src="/images/logo-transparent.png" class="img-fluid" title="Main Logo">
            </div>
            <?php
                    endif;
                else: echo $trans->phrase('password_reset_phrase1');
                endif;
            ?>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>