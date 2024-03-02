<?php
//Start of single support request
if(isset($_GET['id']) && strlen($_GET['id']) > 0):
    //Check support content exist and has permission to view
    $support_data = $Database->get_data('support_id', $_GET['id'], 'support', true);
    
    $has_access = false;
    if($support_data){
        if(!$support_data['support_parent'] && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')){
            
            $has_access = true;
        }
        else if(!$support_data['support_parent'] && $_SESSION['account-type'] == 'company' && 
            $support_data['support_user_type'] == 'company' && $support_data['support_user_id'] == $_SESSION['account-id']){
            
            $has_access = true;
        }
        else if(!$support_data['support_parent'] && $_SESSION['account-type'] == 'user' && 
            $support_data['support_user_type'] == 'user' && $support_data['support_user_id'] == $_SESSION['account-id']){
            
            $has_access = true;
        }
    }

    if($has_access):
?>

<div class="card">
    <div class="card-body p-3">
        <div class="row user-content-row">
            <div class="col-12 support">
                <div class="support-widget-title">
                    <?php echo $trans->phrase("user_support_phrase13")." ".$support_data['support_id']; ?>
                </div>
                <div class="single-support">
                    <div class="row">
                        <div class="col-12 single-support-subject">
                            <b><?php echo $trans->phrase('user_support_phrase11'); ?></b>
                            <?php echo $support_data['support_subject']; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 single-support-content">
                            <?php echo htmlspecialchars_decode( $support_data['support_message'], ENT_QUOTES); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="support-section-title"><?php echo $trans->phrase('user_support_phrase15'); ?>
                            </div>
                            <?php
                    $replies = $Database->get_multiple_data('support_parent', $support_data['support_id'], 'support', '=', true, 'support_time ASC', false);
                    if($replies):
                        foreach($replies as $reply):
                            $user_name = '';
                            $admin = false;
                            if($reply['support_user_type'] == 'super_admin' || $reply['support_user_type'] == 'support_admin'){
                                $user_data = $Database->get_data('admin_id', $reply['support_user_id'], 'admin', true);
                                $user_name = $user_data['admin_name'];    
                                $admin = true;
                            }
                            elseif($reply['support_user_type'] == 'company'){
                                $user_data = $Database->get_data('company_id', $reply['support_user_id'], 'company', true);
                                $user_name = $user_data['company_name'];
                            }
                            else{
                                $user_data = $Database->get_data('user_id', $reply['support_user_id'], 'user', true);
                                $user_name = $user_data['user_name'];
                            }
                    ?>
                            <div class="support-reply-card <?php echo ($admin) ? 'support-reply-card-admin' : ''; ?>">
                                <div class="row">
                                    <div class="col-12 support-reply-card-text">
                                        <?php echo htmlspecialchars_decode( $reply['support_message'], ENT_QUOTES); ?>
                                    </div>
                                </div>
                                <div class="row support-reply-card-info">
                                    <div class="col-5">
                                        <i class="fas fa-user"></i>
                                        <?php echo $user_name; ?>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date("d-m-Y", strtotime($reply['support_time'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endforeach;
                    endif;
                    ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="single-support-reply">
                                <div class="support-reply-title"><?php echo $trans->phrase('user_support_phrase14'); ?>
                                </div>
                                <form>
                                    <div class="form-group">
                                        <label
                                            for="support_reply_text"><?php echo $trans->phrase('user_support_phrase8'); ?></label>
                                        <textarea id="support_reply_text" class="form-control"></textarea>
                                    </div>
                                    <button type="submit" id="support_reply_submit" class="btn btn-success"
                                        data-support_id="<?php echo $support_data['support_id']; ?>">
                                        <i class="fas fa-reply"></i>
                                        <?php echo $trans->phrase('user_support_phrase14');?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    else:
        echo $trans->phrase('user_support_phrase12');
    endif;

//Start of main support page.
else:
$support_info = array();
$options = $Database->get_multiple_data(false, false, 'options');
if($options){
    foreach($options as $option){
        if(strpos($option['option_key'], 'support') !== false){
            $support_info[$option['option_key']] = $option['option_value'];
        }
    }
}
?>
<div class="card">
    <div class="card-body p-3">
        <div class="row user-content-row">
            <div class="col-12 support">
                <div class="support-widget-title">
                    <?php echo $trans->phrase("user_support_phrase1"); ?>
                </div>
                <div class="support-info-ctn">
                    <div class="row support-row">
                        <div class="col-12 support-email">
                            <label class="support-label"><?php echo $trans->phrase("user_support_phrase2"); ?> </label>
                            <div id="support_email_editor" class="support-editor">
                                <?php echo ($support_info['support_email']) ? $support_info['support_email']: ''; ?>
                                <?php if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'): ?>
                                <button id="support_email_editor_button" class="btn btn-light-primary btn-sm">
                                    <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_support_phrase3"); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row support-row">
                        <div class="col-12 support-phone">
                            <label class="support-label"><?php echo $trans->phrase("user_support_phrase4"); ?> </label>
                            <div id="support_phone_editor" class="support-editor">
                                <?php echo ($support_info['support_phone']) ? $support_info['support_phone']: ''; ?>
                                <?php if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'): ?>
                                <button id="support_phone_editor_button" class="btn btn-light-primary btn-sm">
                                    <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_support_phrase3"); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row support-row">
                        <div class="col-12 profile-address">
                            <label class="support-label"><?php echo $trans->phrase("user_support_phrase5"); ?> </label>
                            <div id="support_address_editor" class="support-editor">
                                <?php echo ($support_info['support_address']) ? $support_info['support_address']: ''; ?>
                                <?php if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'): ?>
                                <button id="support_address_editor_button" class="btn btn-light-primary btn-sm">
                                    <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_support_phrase3"); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'company'): ?>
                    <div class="row support-row">
                        <div class="col-12 support-message">
                            <div class="support-message-title"><?php echo $trans->phrase('user_support_phrase6'); ?>
                            </div>
                            <form>
                                <div class="form-group">
                                    <label
                                        for="support_message_subject"><?php echo $trans->phrase('user_support_phrase7'); ?></label>
                                    <input type="text" id="support_message_subject" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label
                                        class="support_message_content"><?php echo $trans->phrase('user_support_phrase8'); ?></label>
                                    <textarea id="support_message_content" class="form-control"></textarea>
                                </div>
                                <button type="submit" id="support_message_submit" class="btn btn-info">
                                    <i class="fas fa-paper-plane"></i>
                                    <?php echo $trans->phrase('user_support_phrase9'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row user-content-row">
            <div class="col-12 support">
                <div class="support-widget-title">
                    <?php echo $trans->phrase("user_support_phrase10"); ?>
                </div>
                <?php
        $support_request = null;
        if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'){
            $support_request = $Database->get_multiple_data('support_parent', NULL, 'support', '<=>', true, 'support_time DESC', false);
        }
        else{
            $request_data = $Database->get_multiple_data('support_user_id', $_SESSION['account-id'], 'support', '=', true, 'support_time DESC', false);
            if($request_data && $_SESSION['account-type'] == 'company'){
                $support_request = array();
                foreach($request_data as $request){
                    if($request['support_user_type'] == 'company' && !$request['support_parent']){
                        array_push($support_request, $request);
                    }
                }
            }
            if($request_data && $_SESSION['account-type'] == 'user'){
                $support_request = array();
                foreach($request_data as $request){
                    if($request['support_user_type'] == 'user' && !$request['support_parent']){
                        array_push($support_request, $request);
                    }
                }
            }
        }
        if(isset($support_request) && count($support_request) > 0):
            foreach($support_request as $support):
                //Getting user data for support request
                $user_name = '';
                if($support['support_user_type'] == 'super_admin' || $support['support_user_type'] == 'support_admin'){
                    $user_data = $Database->get_data('admin_id', $support['support_user_id'], 'admin', true);
                    $user_name = $user_data ? $user_data['admin_name'] : null;    
                }
                elseif($support['support_user_type'] == 'company'){
                    $user_data = $Database->get_data('company_id', $support['support_user_id'], 'company', true);
                    $user_name = $user_data ? $user_data['company_name'] : null;
                }
                else{
                    $user_data = $Database->get_data('user_id', $support['support_user_id'], 'user', true);
                    $user_name = $user_data ? $user_data['user_name'] : null;
                }
        ?>
                <div class="support-card">
                    <div class="row">
                        <div class="col-12 support-card-subject">
                            <label class="support-card-title"><?php echo $trans->phrase('user_support_phrase11'); ?>
                            </label>
                            <a href="/user/index.php?route=support&id=<?php echo $support['support_id']; ?>">
                                <?php echo $support['support_subject']; ?>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 support-card-text">
                            <?php
                    $support_content = '';
                    $decode_support_message = htmlspecialchars_decode( $support['support_message'], ENT_QUOTES);
                    if(strlen($decode_support_message) > 100){
                        $support_content = substr(strip_tags($decode_support_message), 0, 100)."...";
                    }
                    else{
                        $support_content = strip_tags($decode_support_message);
                    }
                    echo $support_content;
                ?>
                        </div>
                    </div>
                    <div class="row support-card-info">
                        <div class="col-5">
                            <i class="fas fa-user"></i>
                            <?php echo $user_name; ?>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-clock"></i>
                            <?php echo date("d-m-Y", strtotime($support['support_time'])); ?>
                        </div>
                        <div class="col-3">
                            <i class="fas fa-hashtag"></i>
                            <?php echo $support['support_id']; ?>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
        endif;
        ?>
            </div>
        </div>
    </div>
</div>
<?php endif; //End of main support page ?>