<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    $Database = new Database();
?>

<div class="card">
    <div class="card-body p-3">
        <div class="col-12 category">
            <?php
        if(isset($_GET['report_format_id'])):
            $category = $Database->get_data('report_format_id', $_GET['report_format_id'], 'mlreport_format', true);
            if(!$category): echo $trans->phrase("user_category_phrase7");
            else:
        ?>
            <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if($languages):
            foreach($languages as $lang):
            $sql = "SELECT * FROM mlreport_format_content WHERE report_format_id={$category['report_format_id']} AND report_lang_code='{$lang['lang_code']}'";
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
                        <form method="post" action="" id="myconsultantprofile" enctype="multipart/form-data">
                        <div class="category-single-title"><?php echo $language_name; ?>
                            <?php echo $trans->phrase("user_category_phrase8"); ?></div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-gray-700"> <?php echo $trans->phrase("report_format_title"); ?>
                                </div>
                            </div>
                            <input type="text" class="form-control report-name"
                                value="<?php echo ($category_data) ? htmlspecialchars_decode( $category_data['report_title'], ENT_QUOTES): ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="category_details_<?php echo $lang['lang_code']; ?>">
                                 <?php echo $trans->phrase("report_format_desc"); ?> </label>
                            <textarea id="category_details_<?php echo $lang['lang_code']; ?>"
                                class="form-control category-details"><?php echo ($category_data) ? htmlspecialchars_decode( $category_data['report_desc'], ENT_QUOTES): ''; ?></textarea>
                        </div>
                         <div class="form-group">
                                            <label for="file"> <?php echo $trans->phrase("report_format_image"); ?>:</label>
                                             <?php 
                             if(isset($category_data['report_image']) && $category_data['report_image'] != ''){
                             $image_value = $category_data['report_image'];
                             }else {
                             $image_value = ';';
					         } ?>
                                            <input type="file" data-lang_code="<?php echo $lang['lang_code']; ?>" data-report_format_id="<?php echo $_GET['report_format_id']; ?>" class="form-control report_image_upload" id="file" name="file" />
                         <input type="hidden" data-image_code="<?php echo $lang['lang_code']; ?>" value = "<?php echo $image_value; ?>"  class="uploaded_image" id="upload_image_<?php echo $lang['lang_code']; ?>"/>
                        </div>
                        <button class="btn btn-success reportformat-translation-save"
                            data-lang_code="<?php echo $lang['lang_code']; ?>"
                            data-report_format_id="<?php echo $_GET['report_format_id']; ?>">
                            <i class="fas fa-save"></i> <?php echo $trans->phrase("user_category_phrase11"); ?>
                        </button>
                        </form>
                          <div class="col-6" id="preview">
                             <?php 
                             if(isset($category_data['report_image']) && $category_data['report_image'] != ''){
                             $path_info = pathinfo($category_data['report_image']);
                             ?>
                           <img src="<?php echo SITE_URL ?>/images/report_image/<?php echo $_GET['report_format_id'] .'_' .  $lang['lang_code'] .'.' . $path_info['extension'] ; ?>" alt="Company logo" style="width: 250px;height: 229px;">
					       <?php } ?>
					    </div>
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
                $categories = $Database->get_multiple_data(false, false, 'mlreport_format', null, true, 'report_format_id ASC');
                if($categories):
                    $category_info;
                    foreach($categories as $category):
                        $category_data = $Database->get_multiple_data('report_format_id', $category['report_format_id'], 'mlreport_format_content');
                        $category_info = $category_data[0];
                        foreach($category_data as $data){
                            if($data['report_lang_code'] == $_SESSION['trans']){
                                $category_info = $data;
                                break;
                            }
                        }
                ?>
                    <div class="category-card text-gray-700" id="cat_<?php echo $category['report_format_id']; ?>">
                        <?php echo $category_info['report_title']; ?>
                         <a href="<?php echo SITE_URL ?>/user/index.php?route=mlreport_composer&report_format_id=<?php echo $category['report_format_id']; ?>"
                            class="btn btn-primary btn-sm report_format_composer-card-btn">
                            <i class="fas fa-stream"></i>
                        </a>
                        <a href="<?php echo SITE_URL ?>/user/index.php?route=mreport_format&report_format_id=<?php echo $category['report_format_id']; ?>"
                            class="btn btn-primary btn-sm category-card-btn">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <button data-report_format_id="<?php echo $category['report_format_id']; ?>"
                            class="report_delete btn btn-danger btn-sm category-card-btn">
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
                                <div class="input-group-text text-gray-600"><?php echo $trans->phrase("report_new"); ?>
                                </div>
                            </div>
                            <input type="text" id="new_report_name" class="form-control">
                        </div>
                        <button type="submit" id="new_report_button" class="btn btn-info mb-1 ml-1">
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