<?php
if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'consultant'):
require_once('../database.php');
$Database = new Database();
?>


<div class="card">
    <div class="card-body p-3">
        <div class="row user-content-row"
        <div class="col-12 question">
            <?php
            //If question id is set in url, this will show question editing options.
            if (isset($_GET['id'])):
                $question = $Database->get_data('question_con_id', $_GET['id'], 'question_con', true);
                if (!$question): echo "Invalid consultant question ID!";
                else:
                    $question_access = $Database->get_data('access_question_id', $question['question_con_id'], 'question_con_method', true);
                    $yes_access = null;
                    $no_access = null;
                    $company = null;

                    if (isset($_GET['company']) && strlen($_GET['company']) > 0) {
                        $company = $Database->get_data('company_id', $_GET['company'], 'company', true);
                        if ($company) {
                            $sql = "SELECT * FROM question_con_method WHERE access_question_id={$question['question_con_id']} AND access_company_id='{$company['company_id']}'";
                            $access_data = $Database->get_connection()->prepare($sql);
                            $access_data->execute();
                            if ($access_data->rowCount() < 1) $access_data = false;
                            else $question_access = $access_data->fetch(PDO::FETCH_ASSOC);
                        }
                    }
                    if ($question_access) {
                        $yes_access = ($question_access['access_yes']) ? explode(',', $question_access['access_yes']) : null;
                        $no_access = ($question_access['access_no']) ? explode(',', $question_access['access_no']) : null;
                    }
                    ?>
                    <div class="row user-content-row">
                        <div class="col-12">
                            <div class="question-single-ctn">
                                <div class="question-single-title"> <?php echo $trans->phrase("user_questions_phrase22"); ?> </div>
                                <form>
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <label for="question_con_type" class="question-title">Type: </label>
                                            <select class="form-control form-control-sm" id="question_con_type"
                                                    data-question_id="<?php echo $question['question_con_id']; ?>">
                                                <option value="yes-no" <?php echo ($question['question_type'] == 'yes-no') ? "selected" : ""; ?>>
                                                    <?php echo $trans->phrase("user_yes_no"); ?>
                                                </option>
                                                <option value="mcq" <?php echo ($question['question_type'] == 'mcq') ? "selected" : ""; ?>>
                                                   <?php echo $trans->phrase("user_mcq"); ?>
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="question_type" class="question-title">Is follow-up: </label>
                                            <select class="form-control form-control-sm" id="question_con_follow_up"
                                                    data-question_id="<?php echo $question['question_con_id']; ?>">
                                                <option value="0" <?php echo (!$question['question_follow_up']) ? "selected" : ""; ?>>
                                                    <?php echo $trans->phrase('user_ticket_phrase13'); ?></option>
                                                <option value="1" <?php echo ($question['question_follow_up']) ? "selected" : ""; ?>>
                                                    <?php echo $trans->phrase('user_ticket_phrase12'); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row user-content-row">
                        <div class="col-12">
                            <div class="question-single-ctn">
                                <div class="question-single-title"> <?php echo $trans->phrase("user_questions_phrase2"); ?> </div>
                                <form>
                                    <!-- Question category -->
                                    <?php if (!$question['question_follow_up']): ?>
                                        <div class="form-group mt-1 ml-1">
                                            <label class="question-title"> <?php echo $trans->phrase("user_questions_phrase20"); ?> </label>
                                            <select id="question_category" class="form-control form-control-sm">
                                                <option value=""><?php echo $trans->phrase("user_questions_phrase21"); ?></option>
                                                <?php
                                                $categories = $Database->get_multiple_data('category_type', 'question', 'category');
                                                if ($categories) {
                                                    foreach ($categories as $category) {
                                                        $sql = "SELECT * FROM category_content WHERE category_id='" . $category['category_id'] . "' AND lang_code='" . $_SESSION['trans'] . "';";
                                                        $category_data = $Database->get_connection()->prepare($sql);
                                                        $category_data->execute();
                                                        if ($category_data->rowCount() > 0) {
                                                            $category_data = $category_data->fetch(PDO::FETCH_ASSOC);
                                                            $category['category_name'] = $category_data['category_name'];
                                                            $category['category_details'] = $category_data['category_details'];

                                                            $selected = ($question['category_id'] == $category['category_id']) ? "selected" : "";
                                                            echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' . $category['category_name'] . '</option>';

                                                        }
                                                    }

                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Methods assignment for yes respons -->
                                    <div class="form-group mt-1 ml-1">
                                        <label class="question-title"> <?php echo $trans->phrase("user_questions_phrase3"); ?> </label>
                                        <select id="question_methods_yes" class="form-control form-control-sm" multiple>
                                            <?php
                                            $methods = $Database->get_multiple_data(false, false, 'method');
                                            if ($methods):
                                                $method_info;
                                                foreach ($methods as $method):
                                                    if ($method['method_company_id'] && !($company && $company['company_id'] == $method['method_company_id']))
                                                        continue;
                                                    $method_data = $Database->get_multiple_data('method_id', $method['method_id'], 'method_content');
                                                    foreach ($method_data as $data) {
                                                        $method_info = $data;
                                                        if ($data['lang_code'] == $_SESSION['trans']) {
                                                            $option = "<option value='{$method_info['method_id']}'";
                                                            $option .= ($yes_access && in_array($method_info['method_id'], $yes_access)) ? "selected" : "";
                                                            $option .= ">Method {$method['method_id']}: {$method_info['method_name']}</option>";
                                                            echo $option;
                                                            break;
                                                        }
                                                    }
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <!--Methods assignment for no response-->
                                    <div class="form-group mt-1 ml-1">
                                        <label class="question-title"> <?php echo $trans->phrase("user_questions_phrase4"); ?> </label>
                                        <select id="question_methods_no" class="form-control form-control-sm" multiple>
                                            <?php
                                            $methods = $Database->get_multiple_data(false, false, 'method');
                                            if ($methods):
                                                $method_info;
                                                foreach ($methods as $method):
                                                    if ($method['method_company_id'] && !($company && $company['company_id'] == $method['method_company_id']))
                                                        continue;
                                                    $method_data = $Database->get_multiple_data('method_id', $method['method_id'], 'method_content');
                                                    foreach ($method_data as $data) {
                                                        $method_info = $data;
                                                        if ($data['lang_code'] == $_SESSION['trans']) {
                                                            $option = "<option value='{$method_info['method_id']}'";
                                                            $option .= ($no_access && in_array($method_info['method_id'], $no_access)) ? "selected" : "";
                                                            $option .= ">Method {$method['method_id']}: {$method_info['method_name']}</option>";
                                                            echo $option;
                                                            break;
                                                        }
                                                    }

                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <!--Tipbox option-->
                                    <div class="form-row">
                                        <div class="col-9">
                                            <div class="col-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                           id="question_activate_tip_yes" <?php echo ($question['question_tip_on_yes']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="question_activate_tip_yes">
                                                        <?php echo $trans->phrase("user_questions_phrase5"); ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                           id="question_activate_tip_no" <?php echo ($question['question_tip_on_no']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="question_activate_tip_no">
                                                        <?php echo $trans->phrase("user_questions_phrase6"); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Follow-up question -->
                                    <?php if (!$question['question_follow_up']): ?>
                                        <?php
                                        $follow_up_questions = $Database->get_multiple_data('question_follow_up', '1', 'question_con');
                                        $f_questions = array(array('question_con_id' => '', 'question_name' => $trans->phrase("user_questions_phrase25")));
                                        if ($follow_up_questions) {
                                            foreach ($follow_up_questions as $follow_up_question) {
                                                $fq_id = $follow_up_question['question_con_id'];
                                                $fq_name = "";
                                                $sql = "SELECT * FROM question_con_content WHERE question_con_id='" . $follow_up_question['question_con_id'] . "' AND lang_code='" . $_SESSION['trans'] . "';";
                                                $fq_data = $Database->get_connection()->prepare($sql);
                                                $fq_data->execute();
                                                if ($fq_data->rowCount() > 0) {
                                                    $fq_data = $fq_data->fetch(PDO::FETCH_ASSOC);
                                                    $fq_name = $fq_data['question_name'];
                                                } else {
                                                    $sql = "SELECT * FROM question_con_content WHERE question_con_id='" . $follow_up_question['question_con_id'] . "' LIMIT 1;";
                                                    $fq_data = $Database->get_connection()->prepare($sql);
                                                    $fq_data->execute();
                                                    if ($fq_data->rowCount() > 0) {
                                                        $fq_data = $fq_data->fetch(PDO::FETCH_ASSOC);
                                                        $fq_name = $fq_data['question_name'];
                                                    }
                                                }
                                                array_push($f_questions, array('question_con_id' => $fq_id, 'question_name' => $fq_name));
                                            }
                                        }
                                        ?>
                                        <div class="form-row">
                                            <div class="col-auto"
                                                 style="margin-top: 12px; display: grid; grid-template-columns: 1fr 3fr">
                                                <label class="question-title"> <?php echo $trans->phrase("user_questions_phrase23"); ?> </label>
                                                <select id="question_follow_up_yes"
                                                        class="form-control form-control-sm">
                                                    <?php
                                                    foreach ($f_questions as $follow_up_question) {
                                                        $selected = ($question['question_yes_follow_up'] == $follow_up_question['question_con_id']) ? "selected" : "";
                                                        echo '<option value="' . $follow_up_question['question_con_id'] . '" ' . $selected . '>' . $follow_up_question['question_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-auto"
                                                 style="margin-top: 12px; display: grid; grid-template-columns: 1fr 3fr">
                                                <label class="question-title"> <?php echo $trans->phrase("user_questions_phrase24"); ?> </label>
                                                <select id="question_follow_up_no" class="form-control form-control-sm">
                                                    <?php
                                                    foreach ($f_questions as $follow_up_question) {
                                                        $selected = ($question['question_no_follow_up'] == $follow_up_question['question_con_id']) ? "selected" : "";
                                                        echo '<option value="' . $follow_up_question['question_con_id'] . '" ' . $selected . '>' . $follow_up_question['question_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-auto"
                                                 style="margin-top: 12px;margin-bottom: 12px; display: grid; grid-template-columns: 3fr 1fr 3fr 3fr 1fr 4fr">
                                                <label class="question-title"> <?php echo $trans->phrase("user_questions_titleJA"); ?> </label>
                                                <div><input style="width: 24px;" type="text"
                                                            value="<?php echo $question['question_weight_yes']; ?>"
                                                            disabled></div>
                                                <div></div>
                                                <label class="question-title"> <?php echo $trans->phrase("user_questions_titleNEI"); ?> </label>
                                                <div><input style="width: 24px;" type="text"
                                                            value="<?php echo $question['question_weight_no']; ?>"
                                                            disabled></div>
                                                <div></div>
                                            </div>

                                        </div>
                                    <?php endif; ?>
                                    <!--Question basic information save -->
                                    <button type="submit" id="question_con_basic" class="btn btn-success btn-sm ml-1 mt-1"
                                            data-question_id="<?php echo $_GET['id']; ?>"
                                            data-company_id="<?php echo ($company) ? $company['company_id'] : ''; ?>">
                                        <i class="fas fa-save"></i> <?php echo $trans->phrase("user_questions_phrase7"); ?>
                                    </button>
                                    <button id="question_con_delete" class="btn btn-danger btn-sm ml-1 mt-1"
                                            data-question_id="<?php echo $_GET['id']; ?>">
                                        <i class="fas fa-trash"></i> <?php echo $trans->phrase("user_questions_phrase8"); ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                    //Translation options
                    $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                    if ($languages):
                        foreach ($languages as $lang):
                            $sql = "SELECT * FROM question_con_content WHERE question_con_id={$question['question_con_id']} AND lang_code='{$lang['lang_code']}'";
                            $question_data = $Database->get_connection()->prepare($sql);
                            $question_data->execute();
                            if ($question_data->rowCount() < 1) $question_data = false;
                            else $question_data = $question_data->fetch(PDO::FETCH_ASSOC);

                            $language_name = $lang['lang_name'];
                            if ($lang['translations']) {
                                $translation = json_decode($lang['translations'], true);
                                if (isset($translation[$_SESSION['trans']])) {
                                    $language_name = $translation[$_SESSION['trans']];
                                }
                            }
                            ?>
                            <div class="row user-content-row">
                                <div class="col-12">
                                    <div class="question-single-ctn">
                                        <div class="question-single-title"><?php echo $language_name; ?><?php echo $trans->phrase("user_questions_phrase9"); ?></div>
                                        <div class="input-group mb-1">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_questions_phrase10"); ?></div>
                                            </div>
                                            <input type="text" class="form-control question-name"
                                                   value="<?php echo ($question_data) ? $question_data['question_name'] : ''; ?>">
                                        </div>
                                        <?php if ($question['question_type'] == 'mcq'): ?>
                                            <div class="form-row mb-3">
                                                <div class="col-auto">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_option1"); ?></div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control form-control-sm question_option1"
                                                               value="<?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_option1'], ENT_QUOTES) : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_option2"); ?></div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control form-control-sm question_option2"
                                                               value="<?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_option2'], ENT_QUOTES) : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_option3"); ?></div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control form-control-sm question_option3"
                                                               value="<?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_option3'], ENT_QUOTES) : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_option4"); ?></div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control form-control-sm question_option4"
                                                               value="<?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_option4'], ENT_QUOTES) : ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_option5"); ?></div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control form-control-sm question_option5"
                                                               value="<?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_option5'], ENT_QUOTES) : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-group">
                                            <label for="question_tip_yes_<?php echo $lang['lang_code']; ?>"> <?php echo $trans->phrase("user_questions_phrase11"); ?> </label>
                                            <textarea id="question_tip_yes_<?php echo $lang['lang_code']; ?>"
                                                      class="form-control question-tips-yes"><?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_tips_yes'], ENT_QUOTES) : ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="question_tip_no_<?php echo $lang['lang_code']; ?>"> <?php echo $trans->phrase("user_questions_phrase12"); ?> </label>
                                            <textarea id="question_tip_no_<?php echo $lang['lang_code']; ?>"
                                                      class="form-control question-tips-no"><?php echo ($question_data) ? htmlspecialchars_decode($question_data['question_tips_no'], ENT_QUOTES) : ''; ?></textarea>
                                        </div>
                                        <button class="btn btn-success question-translation-con-save"
                                                data-lang_code="<?php echo $lang['lang_code']; ?>"
                                                data-question_id="<?php echo $_GET['id']; ?>">
                                            <i class="fas fa-save"></i> <?php echo $trans->phrase("user_questions_phrase13"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach; //Language iteration end
                    endif; //Language check end
                endif;
            else:
                //if question id is not set, it will show question list page.
                ?>
                <?php if ($_SESSION['account-type'] == 'super_admin'): ?>
                <div class="row user-content-row">
                    <div class="col-12">
                        <form class="form-inline">
                            <label class="question-title"> <?php echo $trans->phrase("user_questions_phrase14"); ?> </label>
                            <select id="question_con_view" class="form-control form-control-sm">
                                <option value=""
                                        selected><?php echo $trans->phrase("user_questions_phrase15"); ?></option>
                                <?php
                                $companies = $Database->get_multiple_data(false, false, 'company');
                                if ($companies):
                                    foreach ($companies as $company):
                                        if (isset($_GET['company']) && $_GET['company'] == $company['company_id']) {
                                            echo "<option value='{$company['company_id']}' selected>{$company['company_name']}</option>";
                                        } else {
                                            echo "<option value='{$company['company_id']}'>{$company['company_name']}</option>";
                                        }
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
                <div class="row user-content-row">
                    <div id="question_card_ctn" class="col-12">
                        <?php
                        $questions = $Database->get_multiple_data(false, false, 'question_con');
                        if ($questions):
                            $question_info;
                            $count = 0;
                            foreach ($questions as $question):
                                $count++;
                                $question_data = $Database->get_multiple_data('question_con_id', $question['question_con_id'], 'question_con_content');
                                foreach ($question_data as $data) {
                                    $question_info = $data;
                                    if ($data['lang_code'] == $_SESSION['trans'])
                                        break;
                                }
                                ?>
                                <div class="question-card text-gray-700">
                                    <label class="question-title text-gray-600"><?php echo $trans->phrase("user_questions_phrase16"); ?> <?php echo $count; ?>
                                        : </label>
                                    <?php echo $question_info['question_name']; ?>

                                    <?php if ($_SESSION['account-type'] == 'super_admin'): ?>

                                        <?php if (isset($_GET['company']) && strlen($_GET['company']) > 0): ?>
                                            <a href="/nodg/user/index.php?route=consultant&id=<?php echo $question['question_con_id']; ?>&company=<?php echo $_GET['company']; ?>"
                                               class="btn btn-primary btn-sm question-card-btn">
                                                <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_questions_phrase17"); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="/nodg/user/index.php?route=consultant&id=<?php echo $question['question_con_id']; ?>"
                                               class="btn btn-primary btn-sm question-card-btn">
                                                <i class="fas fa-edit"></i> <?php echo $trans->phrase("user_questions_phrase17"); ?>
                                            </a>
                                        <?php endif; ?>

                                    <?php endif; ?>

                                </div>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
                <?php if ($_SESSION['account-type'] == 'super_admin'): ?>
                <div class="row user-content-row new-question-ctn">
                    <div class="new-question">
                        <form class="form-inline">
                            <div class="input-group mb-1 ml-1">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-gray-600"><?php echo $trans->phrase("user_questions_phrase16"); ?></div>
                                </div>
                                <input type="text" id="new_question_name" class="form-control">
                            </div>
                            <button type="submit" id="new_con_question_button" class="btn btn-info mb-1 ml-1">
                                <i class="fas fa-plus"></i> <?php echo $trans->phrase("user_questions_phrase18"); ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    else:
        echo $trans->phrase("user_questions_phrase19");
    endif;
    ?>
</div>
</div>