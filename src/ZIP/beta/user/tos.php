<div class="card">
    <div class="card-body p-3">
        <div class="row user-content-row">
            <div class="col-12 tos">
                <div class="tos-ctn">
                    <?php
            //Company information from url parameter
            $tos_company = null;
            if(isset($_GET['company'])){
                $tos_company = $Database->get_data('company_id', $_GET['company'], 'company', true);
            }
            //For admins
            if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'):
            ?>
                    <form>
                        <div class="form-group">
                            <label for="tos_selection" class="tos-section-title">
                                <?php echo $trans->phrase('user_tos_phrase3'); ?>
                            </Label>
                            <select id="tos_selection" class="form-control">
                                <option value=""><?php echo $trans->phrase('user_tos_phrase4'); ?></option>
                                <?php
                            $companies = $Database->get_multiple_data(false, false, 'company');
                            if($companies){
                                foreach($companies as $company){
                                    if($tos_company && $tos_company['company_id'] == $company['company_id'])
                                        echo "<option value=\"{$company['company_id']}\" selected>{$company['company_name']}</option>";
                                    else
                                    echo "<option value=\"{$company['company_id']}\">{$company['company_name']}</option>";
                                }
                            }
                        ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tos_selection" class="form-control">
                                <?php echo $trans->phrase('text_notify_user_to_accept'); ?> :
                                <input type="checkbox" checked="checked" value="1" id="chkNotifyUser"
                                    name="chkNotifyUser" />
                            </Label>
                        </div>
                        <?php
                //Getting terms and conditions for company or general.
                $tos_data_array = array();
                if($tos_company){
                    $tos_info = $Database->get_multiple_data('tos_company_id', $tos_company['company_id'], 'tos');
                    if($tos_info){
                        foreach($tos_info as $single_tos){
                            $tos_data_array[$single_tos['lang_code']] = $single_tos;
                        }
                    }
                }
                else{
                    $tos_info = $Database->get_multiple_data(false, false, 'tos');
                    if($tos_info){
                        foreach($tos_info as $single_tos){
                            if(!$single_tos['tos_company_id']){
                                $tos_data_array[$single_tos['lang_code']] = $single_tos;
                            }
                        }
                    }
                }
                //Iteration for translation.
                $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                if($languages):
                    foreach($languages as $lang):
                        $tos_data = null;
                        if(isset($tos_data_array[$lang['lang_code']]))
                           $tos_data = $tos_data_array[$lang['lang_code']];
                ?>
                        <div class="tos-editor-ctn">
                            <div class="tos-editor-title"><?php echo $lang['lang_name']; ?></div>
                            <textarea id="tos_editor_<?php echo $lang['lang_code']; ?>" class="tos-editor">
                        <?php echo ($tos_data) ? htmlspecialchars_decode( $tos_data['tos_content'], ENT_QUOTES) : ''; ?>
                    </textarea>
                            <button class="tos_save btn btn-primary btn-sm mb-2 mt-2"
                                data-company_id="<?php echo ($tos_company)? $tos_company['company_id']: ''; ?>"
                                data-lang_code="<?php echo $lang['lang_code']; ?>">
                                <i class="fas fa-save"></i> <?php echo $trans->phrase('user_tos_phrase2'); ?>
                            </button>
                        </div>
                    </form>
                    <?php
                    endforeach;
                endif;
            //For user and company
            else:
                //Getting terms and conditions for company or general.
                $tos_data = null;
                $user_tos = null;
                if($_SESSION['account-type'] == 'company'){
                    $company_id = $_SESSION['account-id'];
                    $user_tos = $Database->get_multiple_data('tos_company_id', $company_id, 'tos');
                }
                else if($_SESSION['account-type'] == 'user'){
                    $user_data = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
                    $company_id = $user_data['user_company_id'];
                    $user_tos = $Database->get_multiple_data('tos_company_id', $company_id, 'tos');
                }
                if(isset($user_tos) && $user_tos){
                    foreach($user_tos as $single_tos){
                        if($single_tos['lang_code'] == $_SESSION['trans'])
                            $tos_data = $single_tos;
                    }
                }
                else{
                    $tos_info = $Database->get_multiple_data(false, false, 'tos');
                    if($tos_info){
                        foreach($tos_info as $single_tos){
                            if(!$single_tos['tos_company_id'] && $single_tos['lang_code'] == $_SESSION['trans']){
                                $tos_data = $single_tos;
                                break;
                            }
                        }
                    }
                }
                echo ($tos_data) ? htmlspecialchars_decode( $tos_data['tos_content'], ENT_QUOTES) : '';
            endif;
            ?>
                </div>
            </div>
        </div>
    </div>
</div>