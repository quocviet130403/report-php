<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    $Database = new Database();
    
    $languages = $Database->get_multiple_data(false, false, 'language');
?>


<div class="card">
    <div class="card-body p-3">
        <div class="row user-content-row">
            <div class="col-12 language">
                <div class="row user-content-row">
                    <table class="table language-table text-gray-700">
                        <thead>
                            <tr>
                                <th style="min-width: 100px" scope="col">
                                    <?php echo $trans->phrase("user_language_phrase2"); ?></th>
                                <th style="min-width: 300px" scope="col">
                                    <?php echo $trans->phrase("user_language_phrase3"); ?></th>
                                <th style="min-width: 200px" scope="col">
                                    <?php echo $trans->phrase("user_language_phrase4"); ?></th>
                                <th scope="col"><?php echo $trans->phrase("user_language_phrase5"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                foreach($languages as $lang):
            ?>
                            <tr>
                                <td>
                                    <button class="btn btn-info btn-sm"><?php echo $lang['lang_code']; ?></button>
                                </td>
                                <td scope="row">
                                    <div class="language-name">
                                        <div class="language-name-value">
                                            <?php
                                $language_name = $lang['lang_name'];
                                if($lang['translations']){
                                    $translation = json_decode($lang['translations'], true);
                                    if(isset($translation[$_SESSION['trans']])){
                                        $language_name = $translation[$_SESSION['trans']];
                                    }
                                }
                            ?>
                                            <?php echo $language_name; ?>
                                        </div>
                                        <button data-lang_code="<?php echo $lang['lang_code']; ?>"
                                            class="btn btn-light edit-language-name">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <?php echo ($lang['lang_active']) ? 
                            '<span class="language-status">'.$trans->phrase("user_language_phrase6").'</span>': 
                            '<span class="language-status">'.$trans->phrase("user_language_phrase7").'</span>'; ?>
                                    <?php echo ($lang['lang_default']) ? '<span class="language-status">'.$trans->phrase("user_language_phrase8").'</span>': ''; ?>
                                </td>
                                <td>
                                    <?php if($lang['lang_active']): ?>
                                    <?php if(!$lang['lang_default']): ?>
                                    <button class="btn btn-info btn-sm mb-1 ml-1 default_language"
                                        data-code="<?php echo $lang['lang_code']; ?>">
                                        <i class="fas fa-check-square"></i>
                                        <?php echo $trans->phrase("user_language_phrase9"); ?>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-warning btn-sm mb-1 ml-1 deactivate_language"
                                        data-code="<?php echo $lang['lang_code']; ?>"
                                        <?php echo ($lang['lang_default']) ? 'disabled': ''; ?>>
                                        <i class="fas fa-pause"></i>
                                        <?php echo $trans->phrase("user_language_phrase10"); ?>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-success btn-sm mb-1 ml-1 activate_language"
                                        data-code="<?php echo $lang['lang_code']; ?>">
                                        <i class="fas fa-check"></i>
                                        <?php echo $trans->phrase("user_language_phrase11"); ?>
                                    </button>
                                    <?php endif; ?>
                                    <?php if($lang['lang_code'] != 'en'): ?>
                                    <button class="btn btn-danger btn-sm mb-1 ml-1 delete_language"
                                        data-code="<?php echo $lang['lang_code']; ?>"
                                        <?php echo ($lang['lang_default']) ? 'disabled': ''; ?>>
                                        <i class="fas fa-trash"></i>
                                        <?php echo $trans->phrase("user_language_phrase12"); ?>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                endforeach;
            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row user-content-row new-language-ctn">
                    <div class="new-language">
                        <form class="form-inline">
                            <div class="input-group input-group-sm mb-1 ml-1">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><?php echo $trans->phrase("user_language_phrase2"); ?>
                                    </div>
                                </div>
                                <input type="text" id="new_language_code" class="form-control">
                            </div>
                            <div class="input-group input-group-sm mb-1 ml-1">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><?php echo $trans->phrase("user_language_phrase3"); ?>
                                    </div>
                                </div>
                                <input type="text" id="new_language_name" class="form-control">
                            </div>
                            <button type="submit" id="new_language_button" class="btn btn-info btn-sm mb-1 ml-1"><i
                                    class="fas fa-plus"></i>
                                <?php echo $trans->phrase("user_language_phrase13"); ?></button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
else:
    echo $trans->phrase("user_language_phrase13");
endif;
?>
        </div>
    </div>