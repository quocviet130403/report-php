<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    $Database = new Database();
?>
<div class="card">
    <div class="card-body p-3">
<div class="row user-content-row">
    <div class="col-12 package">
        <div class="package-ctn">
            <div class="package-row">
                <div class="col-12">
                    <?php
                    $packages = $Database->get_multiple_data(false, false, 'package');
                    if($packages):
                    ?>
                    <div class="package-card">
                        <table class="table table-striped table-bordered">
                            <thead class="text-gray-500">
                            <tr>
                                <th scope="col"><?php echo $trans->phrase("user_package_phrase3"); ?></th>
                                <th scope="col"><?php echo $trans->phrase("user_package_phrase4"); ?> (<?php echo SITE_CURRENCY; ?>)</th>
                                <th scope="col"><?php echo $trans->phrase("user_package_phrase12"); ?></th>
                                <th scope="col"><?php echo $trans->phrase("user_package_phrase13"); ?></th>
                                <th scope="col"><?php echo $trans->phrase("user_package_phrase14"); ?></th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-700">
                            <?php
                                foreach($packages as $package):
                                    $package_name = $package['package_name'];
                                    $package_details = $package['package_details'];

                                    $sql = "SELECT * FROM package_content WHERE package_id={$package['package_id']} AND package_lang='{$_SESSION['trans']}'";
                                    $package_data = $Database->get_connection()->prepare($sql);
                                    $package_data->execute();
                                    if($package_data->rowCount() < 1) $package_data = false;
                                    else $package_data = $package_data->fetch(PDO::FETCH_ASSOC);

                                    if($package_data){
                                        $package_name = $package_data['package_name'];
                                        $package_details = $package_data['package_details'];
                                    }
                            ?>
                            <tr>
                                <td class="package-info" hidden="true">
                                <?php
                                    $package_info = array(
                                        "package_name" => $package_name,
                                        "package_price" => $package['package_price'],
                                        "package_user" => $package['package_user'],
                                        "package_size_min" => $package['package_size_min'],
                                        "package_size_max" => $package['package_size_max'],
                                        "package_details" => $package_details
                                    );
                                ?>
                                <input type="text" value='<?php echo json_encode($package_info); ?>'>
                                </td>
                                <td><?php echo $package_name; ?></td>
                                <td><?php echo SITE_CURRENCY_SYMBOL.' '.$package['package_price']; ?></td>
                                <td><?php echo $package['package_user']; ?></td>
                                <td><?php echo $package['package_size_min']." - ".$package['package_size_max']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm package-delete" data-package_id="<?php echo $package['package_id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm package-edit-btn" data-package_id="<?php echo $package['package_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                                endforeach;
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                    <div class="package-editor-ctn">
                        <button class="btn btn-lg btn-info package-add-new"><?php echo $trans->phrase('user_package_phrase16'); ?></button>
                    </div>
                    <div class="package-editor-sample">
                        <div class="package-new-title"><?php echo $trans->phrase('user_package_phrase11'); ?></div>
                        <form class="text-gray-600">
                            <div class="form-group">
                                <label for="package_new_lang"><?php echo $trans->phrase('user_package_phrase15'); ?></label>
                                <select id="package_new_lang" class="form-control">
                                    <?php
                                        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                                        if($languages):
                                            foreach($languages as $language):
                                                $lang_name = $language['lang_name'];
                                                if($language['translations']){
                                                    $translation = json_decode($language['translations'], true);
                                                    if(isset($translation[$_SESSION['trans']])){
                                                        $lang_name = $translation[$_SESSION['trans']];
                                                    }
                                                }
                                    ?>
                                    <option value="<?php echo $language['lang_code']; ?>" <?php echo ($language['lang_code'] == $_SESSION['trans']) ? "selected": ""; ?>><?php echo $lang_name ?></option>
                                    <?php
                                            endforeach;
                                        endif;
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="package_new_name"><?php echo $trans->phrase('user_package_phrase3'); ?></label>
                                <input type="text" id="package_new_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="package_new_price"><?php echo $trans->phrase('user_package_phrase4'); ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <?php echo SITE_CURRENCY_SYMBOL; ?>
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="package_new_price" class="form-control">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <?php echo SITE_CURRENCY; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="package_new_user"><?php echo $trans->phrase('user_package_phrase6'); ?></label>
                                <input type="number" step="1" min="0" id="package_new_user" class="form-control">
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="package_new_size_min"><?php echo $trans->phrase('user_package_phrase7'); ?></label>
                                    <input type="number" step="1" min="0" id="package_new_size_min" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="package_new_size_max"><?php echo $trans->phrase('user_package_phrase8'); ?></label>
                                    <input type="number" step="1" min="0" id="package_new_size_max" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="package_new_details"><?php echo $trans->phrase('user_package_phrase9'); ?></label>
                                <textarea id="package_new_details" class="form-control"></textarea>
                            </div>
                            <button type="submit" id="package_new_submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                <?php echo $trans->phrase('user_package_phrase10'); ?>
                            </button>
                            <button type="button" id="package_new_cancel" class="btn btn-danger">
                                <i class="fas fa-times"></i>
                                <?php echo $trans->phrase('user_package_phrase17'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
else:
    echo $trans->phrase('user_package_phrase2');
endif;
?>
</div>
</div>