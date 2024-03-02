<?php 
	
session_start();
require_once('config.php');
require_once('imports.php');
require_once('database.php');
require_once('translation.php');
$Database = new Database();

if(!isset($_SESSION['trans'])){
    
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if($default_language){
        $_SESSION['trans'] = $default_language['lang_code'];
    }
    else{
        $_SESSION['trans'] = 'en';
    }
}

$trans = new Translation($_SESSION['trans']);

$responder_id = null;

if(isset($_GET['ticket_id']) && isset($_GET['page']) && $_GET['page'] == 'responder' && isset($_GET['responder_id'])){

    $ticket_id = $_GET['ticket_id'];
    $responder_id = $_GET['responder_id'];

    $pageNum = isset($_GET['pageNum']) ? $_GET['pageNum'] : 0;

    /** getting reponder details */
    $responderData = $Database->get_data('responder_id', $_GET['responder_id'], 'tbl_ticket_responder', true);


    /** getting responder previous data */
    $sql = "SELECT * FROM responder_ticket_data WHERE responder_id = ".$_GET['responder_id']." && ticket_id =".$ticket_id;

    $responder_data = $Database->get_connection()->prepare($sql);

    $responder_data->execute();

    if ($responder_data->rowCount() == 1) {

        $record = $responder_data->fetch(PDO::FETCH_ASSOC);

        $question_response = json_decode($record['ticket_response'], true);

    } else {
        $question_response = null;
    }

} else {

    /** reditect if responder link is invalid */
    echo '<script> window.location.href = "'.SITE_URL.'"; </script>';
}

	
$user_edit_permission = true;

if(isset($_SESSION['account-type'])){
    if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'user'){
        $user_edit_permission = false;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo NAME; ?></title>

    <!--Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" href="<?php echo SITE_URL ?>/favicon.ico">
    <!-- Fonts -->
    <link rel="stylesheet" href="<?php echo FONT_PACIFICO; ?>">
    <!-- Style Sheets -->
    <link rel="stylesheet" href="<?php echo FONTAWESOME; ?>">
    <link rel="stylesheet" href="<?php echo JQUERY_UI_THEME; ?>">
    <link rel="stylesheet" href="<?php echo BOOTSTRAP_CSS; ?>"> 
    <link rel="stylesheet" href="<?php echo CHART_CSS; ?>">

    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/style.bundle.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/table.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/custom-datatable.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/user_header.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/user.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>/css/custom-new.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"/>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" ></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .table-fixed-header {
            max-height: 100% !important;
        }

        select.form-select {
            margin-bottom: 15px;
        }

        .form-check-input.yes-check, .form-check-input.no-check {
            margin-top: 4px !important;
        }
        
        .form-check-input.mcq-check {
            margin-top: -13px !important;
        }
        

        @media screen and (min-width: 0px) and (max-width: 768px) {
            .answer_Hide { display: none; }
        }
    </style>
</head>

<body data-responder_id="<?php echo $_GET['responder_id']; ?>" data-submit-text="<?php echo $trans->phrase("responder:res_page:submit"); ?>" data-submit-save-text="<?php echo $trans->phrase("responder:res_page:data_saved"); ?>" data-cancel-text="<?php echo $trans->phrase("responder:res_page:cancel"); ?>" data-submit-popup="<?php echo $trans->phrase("responder:res_page:submit:popup"); ?>" data-ticket_id="<?php echo $ticket_id; ?>">
    <div style="margin:10px;width:100%;text-align:right;">
        <a role="button" href="#" style="margin-right:20px;" onclick="unAnseredQuestionFocus()"><?php echo $trans->phrase('text_unansered_qeustion'); ?></a>
    </div>

    <div>
        <?php echo $trans->phrase('pdf_report_phrase2')?>
        <span  id="pageNumber"></span>
        <?php echo $trans->phrase('text_of')?>
        <span id="totalPages"></span>
    </div>

    <div class="propic">
        <div class="input-group footer-lang">
            <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-language"></i></div>
            </div>
        
            <select id="footer_language_selector" class="form-control form-control-sm">
                <?php
                $languages = $Database->get_multiple_data('lang_active', 1, 'language');
                if ($languages) {
                    foreach ($languages as $lang) {
                        $language_name = $lang['lang_name'];
                        if ($lang['translations']) {
                            $translation = json_decode($lang['translations'], true);
                            if (isset($translation[$_SESSION['trans']])) {
                                $language_name = $translation[$_SESSION['trans']];
                            }
                        }
                        if ($lang['lang_code'] == $_SESSION['trans']) {
                            echo '<option value="' . $lang['lang_code'] . '" selected>' . $language_name . '</option>';
                        } else {
                            echo '<option value="' . $lang['lang_code'] . '">' . $language_name . '</option>';
                        }
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div>
        <div class="progress">
	        <div id="progressbar" class="progress-bar bg-success" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%">
		    <span id="label-progressbar"></span>
	    </div>
    </div>

    <input type="hidden" name="pageNum" id="pageNum" value="<?php echo $pageNum; ?>"/>
    
    <div class="row user-content-row">
        <div class="col-12">
            <div class="table-fixed-header">
                <table class="table table-bordered question-table">
                    <thead>
                        <tr>
                            <th class="quesiton_id" width="1%"><?php echo $trans->phrase("user_ticket_phrase10"); ?></th>
                            <th class="quesiton" width="49%"><?php echo $trans->phrase("user_ticket_phrase11"); ?></th>
                            <th class="quesiton answer_Hide" colspan="6" style="text-align:center;"><?php echo $trans->phrase("user_ticket_phrase27"); ?></th>
                        </tr>
                    </thead>
                    
                    <tbody class="question-table-data">
                        <?php
                        //Getting deadline for questions
                        $question_deadline = $Database->get_multiple_data('ticket_id', $ticket_id, 'question_res_deadline');
                        
                        //Getting questions
                        $questions = $Database->get_multiple_data(false, false, 'question_res');
                        
                        if($questions):
                            //Getting category for question pages
                            $available_categories = [];
                            $categories = $Database->get_multiple_data(false, false, 'category', null, true, 'category_rank ASC');
                            $category_info = null;

                            if($categories){
                                foreach($categories as $category){
                                    $category_data = $Database->get_multiple_data('category_id', $category['category_id'], 'category_content');
                                    $category_info = $category_data[0];
                                    foreach($category_data as $data){
                                        if($data['lang_code'] == $_SESSION['trans']){
                                            $category_info = $data;
                                            break;
                                        }
                                    }
                                    $cat_data = array(
                                        'category_id' => $category['category_id'],
                                        'category_rank' => $category['category_rank'],
                                        'category_name' => $category_info['category_name'],
                                        'category_details' => html_entity_decode($category_info['category_details'])
                                    );
                                    array_push($available_categories, $cat_data);
                                }

                                echo '<textarea id="available_categories" class="d-none" name="available_categories" style="display: none !important;" >'.json_encode($available_categories).'</textarea>';
                            }

                            $count = 0;
                            $countNotFollowUpQuestion = 0;
                            $questionIds = "";
                            $answerIds = "";
                            $unAnswerIds = "";

                            foreach($questions as $question):
                                //Skip new question for closed ticket
                                /*if($ticket['ticket_status'] == 'closed'
                                    && !isset($question_response[$question['question_res_id']])){
                                    continue;
                                }*/

                                //Increment question number
                                $count++;
                                if($question['question_follow_up']==0) {
                                    $countNotFollowUpQuestion++;
                                    if($countNotFollowUpQuestion==1) {
                                        $questionIds = $question['question_res_id'];
                                    }
                                    else {
                                        $questionIds = $questionIds.",".$question['question_res_id'];
                                    }
                                }

                                //Getting question data
                                $sql = "SELECT * FROM question_res_content WHERE question_res_id={$question['question_res_id']} AND lang_code='{$_SESSION['trans']}'";
                                $question_data = $Database->get_connection()->prepare($sql);
                                $question_data->execute();
                                if($question_data->rowCount() < 1)
                                    $question_data = false;
                                else
                                    $question_data = $question_data->fetch(PDO::FETCH_ASSOC);

                                //Finding deadline
                                $q_deadline = null;
                                if($question_deadline){
                                    foreach($question_deadline as $deadline){
                                        if($deadline['question_id'] == $question['question_res_id']){
                                            $q_deadline = $deadline;
                                        }
                                    }
                                }

                                //Question response
                                $yes_check = false;
                                $no_check = false;
                                $check_1 = false;
                                $check_2 = false;
                                $check_3 = false;
                                $check_4 = false;
                                $check_5 = false;
                                $check_6 = false;

                                if($question_response != null) {
                                    if(isset($question_response[$question['question_res_id']])){
                                        if($question['question_type'] == 'yes-no'){
                                            if($question_response[$question['question_res_id']]['answer'] == 2) {
                                                $yes_check = true;
                                            }
                                            else if($question_response[$question['question_res_id']]['answer'] == 1) {
                                                $no_check = true;
                                            }
                                        }
                                        if($question['question_type'] == 'mcq'){
                                            if($question_response[$question['question_res_id']]['answer'] == 1) $check_1 = true;
                                            else if($question_response[$question['question_res_id']]['answer'] == 2) $check_2 = true;
                                            else if($question_response[$question['question_res_id']]['answer'] == 3) $check_3 = true;
                                            else if($question_response[$question['question_res_id']]['answer'] == 4) $check_4 = true;
                                            else if($question_response[$question['question_res_id']]['answer'] == 5) $check_5 = true;
                                            else if($question_response[$question['question_res_id']]['answer'] == 6) $check_6 = true;
                                        }
                                    }

                                    if(isset($question_response[$question['question_res_id']]['notes'])) {
                                        $notes = $question_response[$question['question_res_id']]['notes'];
                                    } else {
                                        $notes = "";
                                    }
                                }

                                if($question['question_follow_up']==0){

                                    if($yes_check ||$no_check ||$check_1 ||$check_2 ||$check_3 ||$check_4 ||$check_5 ||$check_6) {    

                                        if(strlen($answerIds)<=0) {
                                            $answerIds = $question['question_res_id'];
                                        } else {
                                            $answerIds = $answerIds.",".$question['question_res_id'];
                                        }

                                    } else {
                                        if(strlen($unAnswerIds)<=0) {
                                            $unAnswerIds = $question['question_res_id'];
                                        } else {
                                            $unAnswerIds = $unAnswerIds.",".$question['question_res_id'];
                                        }
                                    }
                                }
                        ?>
                        
                                <tr
                                id="question-<?php echo $question['question_res_id']; ?>"
                                class="question-row <?php echo ($question['question_follow_up']) ? 'follow-up': ''; ?>"
                                data-question_id="<?php echo $question['question_res_id']; ?>"
                                data-category_id="<?php echo $question['category_id']; ?>"
                                data-question_type="<?php echo $question['question_type']; ?>"
                                data-question_follow_up="<?php echo $question['question_follow_up']; ?>"
                                data-question_yes_follow_up="<?php echo $question['question_yes_follow_up']; ?>"
                                data-question_no_follow_up="<?php echo $question['question_no_follow_up']; ?>">
                                    <td class="question-number"><?php echo $count; ?></td>
                                    <td>
                                        <?php echo ($question_data)? $question_data['question_name'] : ''; ?>
                                        <?php if($question['question_follow_up'] != 0) { ?>
                                            <a href="#" title="<?php echo $trans->phrase('user_ticket_phrase58'); ?>" onclick="showNotes('notes<?php echo $count; ?>')">
                                                <i class="fas fa-comment"></i>
                                            </a>
                                        <?php
                                            }
                                        ?>
                                        <div id="notes<?php echo $count; ?>" style="display:none;">
                                        <input type="text" id="txtnotes<?php echo $question['question_res_id']; ?>"
                                            name="txtnotes<?php echo $question['question_res_id']; ?>"
                                            value="<?php echo $notes; ?>"
                                            style="width: 100%;"
                                            placeholder="<?php echo $trans->phrase('user_ticket_phrase58'); ?>"
                                            />
                                        </div>
                                        <div class="btn-group dropright tb-drop">
                                            <select class="form-select mcq-check" name="check_<?php echo $question['question_res_id']; ?>" 
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onchange="setProgBarSelect('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>>
                                                
                                                <?php if($question['question_type'] == 'mcq'):?>>
                                                                            
                                                <option value="0"><?php echo $trans->phrase('please_select_ticket'); ?></option>
                                                <option value="1" 
                                                class="form-check-input mcq-check check_1" 
                                                data-tip="no" 
                                                data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>" 
                                                <?php if($check_1) echo("selected"); ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> 
                                                ><?php echo $question_data['question_option1']; ?></option>
                                                
                                                <option value="2" 
                                                class="form-check-input mcq-check check_2" 
                                                data-tip="no" 
                                                data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>" 
                                                <?php if($check_2) echo("selected"); ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> 
                                                ><?php echo $question_data['question_option2']; ?></option>

                                                <option value="3" 
                                                class="form-check-input mcq-check check_3" 
                                                data-tip="" 
                                                data-tip_enabled="0" 
                                                <?php echo ($check_3)? 'checked': ''; ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> 
                                                <?php if($check_3) echo("selected"); ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> 
                                                ><?php echo $question_data['question_option3']; ?></option>

                                                <option value="4" 
                                                class="form-check-input mcq-check check_4" 
                                                data-tip="yes" 
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>" 
                                                <?php if($check_4) echo("selected"); ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> 
                                                ><?php echo $question_data['question_option4']; ?></option>

                                                <option value="5" 
                                                class="form-check-input mcq-check check_5" 
                                                data-tip="yes" 
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>" 
                                                <?php if($check_5) echo("selected"); ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>
                                                ><?php echo $question_data['question_option5']; ?></option>

                                                <option value="6" 
                                                class="form-check-input mcq-check check_6" 
                                                data-tip="yes" 
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>" 
                                                <?php if($check_6) echo("selected"); ?> 
                                                <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> 
                                                ><?php echo $question_data['question_option6']; ?></option>
                                            </select>
                                                
                                            <?php elseif($question['question_type']=='yes-no'): ?>
                                            <select class="form-select" name="check_<?php echo $question['question_res_id']; ?>">
                                                <option value="1" class="form-check-input yes-check check_7" data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>" <?php if($yes_check) echo("selected"); ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> ><?php echo $trans->phrase("user_ticket_phrase12"); ?></option>
                                                <option value="2" <?php if($no_check) echo("selected"); ?> 
                                                    data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>" <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> ><?php echo $trans->phrase("user_ticket_phrase13"); ?></option>
                                            </select>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <?php
                                    if($question['question_type'] == 'mcq'):
                                    ?>
                                    <td class="checkbox-td">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input mcq-check check_1"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                value="1"
                                                data-tip="no"
                                                data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>"
                                                <?php echo ($check_1)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                    <?php echo $question_data['question_option1']; ?>
                                            </label>
                                        </div>

                                        <div class="tip-view-ctn">
                                            <div class="tip-view yes-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                            <div class="tip-view no-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="checkbox-td">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input mcq-check check_2"
                                                data-tip="no"
                                                value="2"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>"
                                                <?php echo ($check_2)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $question_data['question_option2']; ?>
                                            </label>
                                        </div>

                                        <div class="tip-view-ctn">
                                            <div class="tip-view yes-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                            <div class="tip-view no-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="checkbox-td">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input mcq-check check_3"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                value="3"
                                                data-tip=""
                                                data-tip_enabled="0" <?php echo ($check_3)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $question_data['question_option3']; ?>
                                            </label>
                                        </div>

                                        <div class="tip-view-ctn">
                                            <div class="tip-view yes-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                            <div class="tip-view no-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="checkbox-td">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input mcq-check check_4"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                value="4"
                                                data-tip="yes"
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                                <?php echo ($check_4)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $question_data['question_option4']; ?>
                                            </label>
                                        </div>

                                        <div class="tip-view-ctn">
                                            <div class="tip-view yes-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                            <div class="tip-view no-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="checkbox-td">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input mcq-check check_5"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                value="5"
                                                data-tip="yes"
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                                <?php echo ($check_5)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $question_data['question_option5']; ?>
                                            </label>
                                        </div>

                                        <div class="tip-view-ctn">
                                            <div class="tip-view yes-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                            <div class="tip-view no-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="checkbox-td">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input mcq-check check_6"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                value="6"
                                                data-tip="yes"
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                                <?php echo ($check_6)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $question_data['question_option6']; ?>
                                            </label>
                                        </div>

                                        <div class="tip-view-ctn">
                                            <div class="tip-view yes-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                            <div class="tip-view no-tip">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <?php
                                    elseif($question['question_type'] == 'yes-no'):
                                    ?>
                                    <td class="checkbox-td" colspan="3" style="text-align:center;">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio"
                                                name="check_<?php echo $question['question_res_id']; ?>"
                                                value="1"
                                                class="form-check-input yes-check"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                                <?php echo ($yes_check)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $trans->phrase("user_ticket_phrase12"); ?>
                                            </label>
                                        </div>
                                        <div class="tip-view-ctn">
                                            <div class="tip-view">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_yes'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="checkbox-td" colspan="3" style="text-align:center;">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input type="radio" name="check_<?php echo $question['question_res_id']; ?>"
                                                class="form-check-input no-check"
                                                value="2"
                                                <?php if($question['question_follow_up']==0) { ?>
                                                    onclick="setProgBar('<?php echo $question['question_res_id']; ?>')"
                                                <?php } ?>
                                                data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>"
                                                <?php echo ($no_check)? 'checked': ''; ?> <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?> >
                                                <?php echo $trans->phrase("user_ticket_phrase13"); ?>
                                            </label>
                                        </div>
                                        <div class="tip-view-ctn">
                                            <div class="tip-view">
                                                <?php echo htmlspecialchars_decode( $question_data['question_tips_no'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>

                <input name="questionIds" id="questionIds" type="hidden" value="<?php echo $questionIds; ?>">
                <input name="answersIds" id="answerIds" type="hidden" value="<?php echo $answerIds; ?>">

                <?php

                    if(count(explode(",", $answerIds)) == count(explode(",", $questionIds))){

                        $url = SITE_URL."/custom.php?route=res_thank_you&page=res_thank_you&ticket_id=".$_GET['ticket_id'];

                        echo "<script>window.location.href = '".$url."'</script>";

                    }
                ?>

                <div class="text-center pb-3 pt-3">
                    <div class="d-inline" style="text-align: center;">
                        <button  onclick="nextCategory()" class="btn btn-info btn-sm table-page-prev" disabled><i class="fas fa-chevron-left"></i></button>
                    </div>

                    <div class="d-inline" style="text-align: center;">
                        <button data-bs-toggle="modal" data-bs-target="#exampleModalPen" class="btn btn-info btn-sm table-page-number" style="width:60%;">1</button>
                    </div>

                    <div class="d-inline" style="text-align: center;">
                        <button id="btnNextQgroup" onclick="nextCategory()" class="btn btn-info btn-sm table-page-next"> <i class="fas fa-chevron-right"></i> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
		
		
    <div id="unanseredCatQues" class="w3-modal"  style="display:none">
        <div class="w3-modal-content">
		    <div class="w3-container">
                <span class="w3-button w3-display-topright"></span>

                <table id="unanswerd-qtable">
			        <tr>
                        <th><?php echo $trans->phrase("user_questions_phrase20"); ?></th>
                        <th><?php echo $trans->phrase("user_sidebar_phrase9"); ?></th>
			        </tr>
			        
                    <?php foreach($available_categories as $catagory) { ?>
				    <tr>
                        <td>
                            <?php echo $catagory['category_name']; ?>
                        </td>
                        <td style="padding:5px;">
                            <?php
                                $unAnswerIdArr = explode(",", $unAnswerIds);
                                $qNoInCat =  0;
                                foreach($questions as $question) {
                                if($question['category_id']==$catagory['category_id']) {
                                    $qNoInCat++;
                                    if(in_array($question['question_res_id'], $unAnswerIdArr)) {
                                        $tableRowId = 'question-'.$question['question_res_id'];
                                        ?>
                                        <span class="unansered-question-no">
                                            <?php echo $qNoInCat; ?>
                                        </span>
                                        <?php
                                    }
                                }
                                }
                            ?>
                        </td>
				    </tr>
			        <?php } ?>
                </table>

                <div class="w3-button-holder">
                    <button id="close-unanseredCatQues" class="btn btn-info"> <?php echo $trans->phrase('text_close');?> </button>
                </div>
            </div>
	    </div>
    </div>
	
    <div style="width:100%;text-align:center;"> 
        
        <?php if($user_edit_permission): ?>
        
            <button id="save_ticket" class="btn btn-success mb-3 ml-3"> <?php echo $trans->phrase("user_ticket_phrase15"); ?></button>

        <?php endif; ?>
    </div>

    <!-- Modal -->
	<div class="modal fade" id="exampleModalPen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h5 class="modal-title text-center" id="exampleModalLabel"><?php echo $trans->phrase("user_questions_phrase26"); ?></h5>
		            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		        </div>
		        <div class="modal-body" id="category_text_custom"></div>
		    </div>
	    </div>
	</div>
	
    <script src="<?php echo SITE_URL ?>/js/responder.js"></script>
</body>		
</html>