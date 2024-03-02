<!--End of main container fluid-->
<div class="container-fluid footer-container">
    <div class="row footer-row">
        <div class="col-6">
            <div class="footer-menu">
                <ul>
                    <a href="/tos.php"><li><?php echo $trans->phrase('footer_phrase1'); ?></li></a>
                </ul>   
            </div>
        </div>
        <div class="col-6">
            <div class="input-group footer-lang">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-language"></i></div>
                </div>
                <select id="footer_language_selector" class="form-control form-control-sm">
                <?php
                $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                if($languages){
                    foreach($languages as $lang){
                        $language_name = $lang['lang_name'];
                        if($lang['translations']){
                            $translation = json_decode($lang['translations'], true);
                            if(isset($translation[$_SESSION['trans']])){
                                $language_name = $translation[$_SESSION['trans']];
                            }
                        }
                        if($lang['lang_code'] == $_SESSION['trans']){
                            echo '<option value="'.$lang['lang_code'].'" selected>'.$language_name.'</option>';
                        }
                        else{
                            echo '<option value="'.$lang['lang_code'].'">'.$language_name.'</option>';
                        }
                    }
                }
                ?>
                </select>
            </div>
        </div>
    </div>
</div>
    <script src="<?php echo JQUERY; ?>"></script>
    <script src="<?php echo BOOTSTRAP_JS; ?>"></script>
    <script src="<?php echo SITE_URL; ?>/js/index.js?v="<?php echo time();?>></script>
</body>
</html>