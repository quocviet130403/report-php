<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    $Database = new Database();
?>


<div class="card">
    <div class="card-body p-3">
    <div class="col-12 industry">
        <?php
        if(isset($_GET['id'])):
            $industry = $Database->get_data('industry_id', $_GET['id'], 'industry_type', true);
            if(!$industry): echo $trans->phrase("user_industry_phrase6");
            else:
        ?>
        <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if($languages):
            foreach($languages as $lang):
            $sql = "SELECT * FROM industry_content WHERE industry_id={$industry['industry_id']} AND lang_code='{$lang['lang_code']}'";
            $industry_data = $Database->get_connection()->prepare($sql);
            $industry_data->execute();
            if($industry_data->rowCount() < 1) $industry_data = false;
            else $industry_data = $industry_data->fetch(PDO::FETCH_ASSOC);

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
                <div class="industry-single-ctn">
                    <div class="industry-single-title"><?php echo $language_name; ?> <?php echo $trans->phrase("user_industry_phrase7"); ?></div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_industry_phrase8"); ?></div>
                        </div>
                        <input type="text" class="form-control industry-name" value="<?php echo ($industry_data) ? htmlspecialchars_decode( $industry_data['industry_name'], ENT_QUOTES): ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="industry_details_<?php echo $lang['lang_code']; ?>"> <?php echo $trans->phrase("user_industry_phrase9"); ?> </label>
                        <textarea id="industry_details_<?php echo $lang['lang_code']; ?>" class="form-control industry-details"><?php echo ($industry_data) ? htmlspecialchars_decode( $industry_data['industry_details'], ENT_QUOTES): ''; ?></textarea>
                    </div>
                    <button class="btn btn-success industry-translation-save" data-lang_code="<?php echo $lang['lang_code']; ?>" data-industry_id="<?php echo $_GET['id']; ?>">
                        <i class="fas fa-save"></i> <?php echo $trans->phrase("user_industry_phrase10"); ?>
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
        <div class="row user-content-row text-gray-700">
            <div id="industry_card_ctn" class="col-12">
                <?php
                $industries = $Database->get_multiple_data(false, false, 'industry_type');
                if($industries):
                    $industry_info;
                    foreach($industries as $industry):
                        $industry_data = $Database->get_multiple_data('industry_id', $industry['industry_id'], 'industry_content');
                        foreach($industry_data as $data){
                            $industry_info = $data;
                            if($data['lang_code'] == $_SESSION['trans'])
                                break;
                        }
                ?>
                <div class="industry-card">
                    <?php echo $industry_info['industry_name']; ?>
                    <a href="<?php echo SITE_URL ?>/user/index.php?route=industry&id=<?php echo $industry['industry_id']; ?>" class="btn btn-primary btn-sm industry-card-btn">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button data-industry_id="<?php echo $industry['industry_id']; ?>" class="industry_delete btn btn-danger btn-sm industry-card-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>
        <div class="row user-content-row new-industry-ctn">
            <div class="new-industry">
                <form class="form-inline">
                    <div class="input-group mb-1 ml-1">
                        <div class="input-group-prepend">
                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_industry_phrase3"); ?></div>
                        </div>
                        <input type="text" id="new_industry_name" class="form-control">
                    </div>
                    <button type="submit" id="new_industry_button" class="btn btn-info mb-1 ml-1">
                        <i class="fas fa-plus"></i> <?php echo $trans->phrase("user_industry_phrase4"); ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
<?php
else:
    echo $trans->phrase("user_industry_phrase5");
endif;
?>
</div>
</div>