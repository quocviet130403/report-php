<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    $Database = new Database();
?>

<div class="card">
    <div class="card-body p-3">
        <div class="col-12 category">
            <?php
        if(isset($_GET['id'])):
            $category = $Database->get_data('category_id', $_GET['id'], 'category', true);
            if(!$category): echo $trans->phrase("user_category_phrase7");
            else:
        ?>
            <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if($languages):
            foreach($languages as $lang):
            $sql = "SELECT * FROM category_content WHERE category_id={$category['category_id']} AND lang_code='{$lang['lang_code']}'";
            $category_data = $Database->get_connection()->prepare($sql);
            $category_data->execute();
            if($category_data->rowCount() < 1) $category_data = false;
            else $category_data = $category_data->fetch(PDO::FETCH_ASSOC);

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
                    <div class="category-single-ctn">
                        <div class="category-single-title"><?php echo $language_name; ?>
                            <?php echo $trans->phrase("user_category_phrase8"); ?></div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-gray-700"><?php echo $trans->phrase("user_category_phrase9"); ?>
                                </div>
                            </div>
                            <input type="text" class="form-control category-name"
                                value="<?php echo ($category_data) ? htmlspecialchars_decode( $category_data['category_name'], ENT_QUOTES): ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="category_details_<?php echo $lang['lang_code']; ?>">
                                <?php echo $trans->phrase("user_category_phrase10"); ?> </label>
                            <textarea id="category_details_<?php echo $lang['lang_code']; ?>"
                                class="form-control category-details"><?php echo ($category_data) ? htmlspecialchars_decode( $category_data['category_details'], ENT_QUOTES): ''; ?></textarea>
                        </div>
                        <button class="btn btn-success category-translation-save"
                            data-lang_code="<?php echo $lang['lang_code']; ?>"
                            data-category_id="<?php echo $_GET['id']; ?>">
                            <i class="fas fa-save"></i> <?php echo $trans->phrase("user_category_phrase11"); ?>
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
                <div id="category_card_ctn" class="col-12">
                    <?php
                $categories = $Database->get_multiple_data(false, false, 'category', null, true, 'category_rank ASC');
                if($categories):
                    $category_info;
                    foreach($categories as $category):
                        $category_data = $Database->get_multiple_data('category_id', $category['category_id'], 'category_content');
                        $category_info = $category_data[0];
                        foreach($category_data as $data){
                            if($data['lang_code'] == $_SESSION['trans']){
                                $category_info = $data;
                                break;
                            }
                        }
                ?>
                    <div class="category-card text-gray-700" id="cat_<?php echo $category['category_id']; ?>">
                        <?php echo $category_info['category_name']; ?>
                        <a href="<?php echo SITE_URL ?>/user/index.php?route=category&id=<?php echo $category['category_id']; ?>"
                            class="btn btn-primary btn-sm category-card-btn">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button data-category_id="<?php echo $category['category_id']; ?>"
                            class="category_delete btn btn-danger btn-sm category-card-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <?php 
                    endforeach;
                endif;
                ?>
                </div>
            </div>
            <div class="row user-content-row new-category-ctn">
                <div class="new-category">
                    <form class="form-inline">
                        <div class="input-group mb-1 ml-1">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_category_phrase4"); ?>
                                </div>
                            </div>
                            <input type="text" id="new_category_name" class="form-control">
                        </div>
                        <button type="submit" id="new_category_button" class="btn btn-info mb-1 ml-1">
                            <i class="fas fa-plus"></i> <?php echo $trans->phrase("user_category_phrase5"); ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
else:
    echo $trans->phrase("user_category_phrase6");
endif;
?>
    </div>
</div>