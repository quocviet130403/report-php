<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    
    $Database = new Database();
    
?>

<div class="card">
    <div class="card-body p-3">
        <div class="col-12 method">
            <?php
        if(isset($_GET['id'])):
            $method = $Database->get_data('method_id', $_GET['id'], 'method', true);
            if(!$method): echo $trans->phrase("user_methods_phrase18");
            else:
        ?>
            <div class="row user-content-row">
                <div class="col-12">
                    <div class="method-single-ctn">
                        <div class="method-single-title"> <?php echo $trans->phrase("user_methods_phrase1"); ?> </div>
                        <form>
                            <div class="form-group form-group-sm mt-1 ml-1">
                                <label class="method-title"> <?php echo $trans->phrase("user_methods_phrase19"); ?>
                                </label>
                                <input type="color" name="method_color" id="method_color"
                                    value="<?php echo $method['method_color']; ?>">
                            </div>
                            <div class="form-group form-group-sm mt-1 ml-1">
                                <label class="method-title"> <?php echo $trans->phrase("user_methods_phrase2"); ?>
                                </label>
                                <select id="method_for" class="form-control form-control-sm">
                                    <option value="" <?php echo (!$method['method_id'])? 'selected' : ''; ?>>
                                        <?php echo $trans->phrase("user_methods_phrase3"); ?></option>
                                    <?php
                                $companies = $Database->get_multiple_data(false, false, 'company');
                                if($companies):
                                    foreach($companies as $company):
                                        $option = "<option value='{$company['company_id']}'";
                                        $option .= ($method['method_company_id'] == $company['company_id']) ? "selected" : "";
                                        $option .= ">{$company['company_name']}</option>";
                                        echo $option;
                                    endforeach;
                                endif;
                                ?>
                                </select>
                            </div>
                            <div class="form-group form-group-sm ml-1 mt-1">
                                <label for="company_restriction"
                                    class="method-title"><?php echo $trans->phrase("user_methods_phrase4"); ?> </label>
                                <input type="text" id="company_restriction" class="form-control form-control-sm"
                                    value="<?php echo $method['method_restriction']; ?>"
                                    placeholder="<?php echo $trans->phrase("user_methods_phrase5"); ?>">
                            </div>
                            <button type="submit" id="method_basic" class="btn btn-success btn-sm ml-1 mt-1"
                                data-method_id="<?php echo $_GET['id']; ?>"><i class="fas fa-save">
                                </i> <?php echo $trans->phrase("user_methods_phrase6"); ?>
                            </button>
                            <button id="method_delete" class="btn btn-danger btn-sm ml-1 mt-1"
                                data-method_id="<?php echo $_GET['id']; ?>">
                                <i class="fas fa-trash"></i> <?php echo $trans->phrase("user_methods_phrase7"); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if($languages):
            foreach($languages as $lang):
            $sql = "SELECT * FROM method_content WHERE method_id={$method['method_id']} AND lang_code='{$lang['lang_code']}'";
            $method_data = $Database->get_connection()->prepare($sql);
            $method_data->execute();
            if($method_data->rowCount() < 1) $method_data = false;
            else $method_data = $method_data->fetch(PDO::FETCH_ASSOC);

            $language_name = $lang['lang_name'];
            if($lang['translations']){
                $translation = json_decode($lang['translations'], true);
                if(isset($translation[$_SESSION['trans']])){
                    $language_name = $translation[$_SESSION['trans']];
                }
            }
        ?>
            <div class="row user-content-row">
                <div class="col-12">
                    <div class="method-single-ctn">
                        <div class="method-single-title"><?php echo $language_name; ?>
                            <?php echo $trans->phrase("user_methods_phrase8"); ?></div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_methods_phrase9"); ?>
                                </div>
                            </div>
                            <input type="text" class="form-control method-name text-gray-700"
                                value="<?php echo ($method_data) ? $method_data['method_name']: ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="method_details_<?php echo $lang['lang_code']; ?>">
                                <?php echo $trans->phrase("user_methods_phrase10"); ?> </label>
                            <textarea id="method_details_<?php echo $lang['lang_code']; ?>"
                                class="form-control method-details"><?php echo ($method_data) ? htmlspecialchars_decode( $method_data['method_details'], ENT_QUOTES): ''; ?></textarea>
                        </div>
                        <button class="btn btn-success method-translation-save"
                            data-lang_code="<?php echo $lang['lang_code']; ?>"
                            data-method_id="<?php echo $_GET['id']; ?>">
                            <i class="fas fa-save"></i> <?php echo $trans->phrase("user_methods_phrase11"); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php
            endforeach; //Language iteration end
            endif; //Language check end
            endif;
        else:
        ?>
            <div class="row user-content-row">
                <div class="col-12">
                    <form class="form-inline">
                        <label class="method-title"><?php echo $trans->phrase("user_methods_phrase12"); ?> </label>
                        <select id="method_view" class="form-control form-control-sm">
                            <option value="" selected><?php echo $trans->phrase("user_methods_phrase3"); ?></option>
                            <?php
                        $companies = $Database->get_multiple_data(false, false, 'company');
                        if($companies):
                            foreach($companies as $company):
                                echo "<option value='{$company['company_id']}'>{$company['company_name']}</option>";
                            endforeach;
                        endif;
                        ?>
                        </select>
                    </form>
                </div>
            </div>
            <div class="row user-content-row">
                <div id="method_card_ctn" class="col-12">
                    <?php
                $methods = $Database->get_multiple_data(false, false, 'method');
                if($methods):
                    $method_info;
                    foreach($methods as $method):
                        if($method['method_company_id']) continue;
                        $method_data = $Database->get_multiple_data('method_id', $method['method_id'], 'method_content');
                        foreach($method_data as $data){
                            $method_info = $data;
                            if($data['lang_code'] == $_SESSION['trans'])
                                break;
                        }
                ?>
                    <div class="method-card text-gray-700">
                        <label class="method-title text-gray-500"><?php echo $trans->phrase("user_methods_phrase13"); ?>
                            <?php echo $method['method_id']; ?> : </label>
                        <?php echo $method_info['method_name']; ?>
                        <a href="<?php echo SITE_URL ?>/user/index.php?route=methods&id=<?php echo $method['method_id']; ?>"
                            class="btn btn-primary btn-sm method-card-btn">
                            <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_methods_phrase14"); ?>
                        </a>
                    </div>
                    <?php 
                    endforeach;
                endif;
                ?>
                </div>
            </div>
            <div class="row user-content-row new-method-ctn">
                <div class="new-method">
                    <form class="form-inline">
                        <div class="input-group mb-1 ml-1">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_methods_phrase13"); ?>
                                </div>
                            </div>
                            <input type="text" id="new_method_name" class="form-control">
                        </div>
                        <button type="submit" id="new_method_button" class="btn btn-info mb-1 ml-1">
                            <i class="fas fa-plus"></i> <?php echo $trans->phrase("user_methods_phrase15"); ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
else:
    echo $trans->phrase("user_methods_phrase16");
endif;
?>
    </div>
</div>