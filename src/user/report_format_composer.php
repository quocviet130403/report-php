<?php
if(isset($_GET['id'])):
    require_once('../database.php');
    $Database = new Database();

    // if(isset($_GET['id'])){
    //     $ticket = $Database->get_data('ticket_id', $_GET['id'], 'ticket', true);
    //     if($ticket) $question_response = json_decode($ticket['ticket_response'], true);
    // }
    if(isset($_GET['id']) && $_GET['id'] != "" && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')):
        echo "<input type='text' id='report_format_id' value='".$_GET['id']."' readonly hidden>";
        echo "<input type='text' id='lang_code' value='".$_SESSION['trans']."' readonly hidden>";

        $report = false;
        $report_content = false;
        $report = $Database->get_data('report_format_id', $_GET['id'], 'report_format', true);

        // if($report){
        //     foreach($reports as $single_report){
        //         if($single_report['lang_code'] == $_SESSION['trans']){
        //             $report = $single_report;
        //         }
        //     }
        // }
        if($report){
            $report_content = json_decode($report['report_content'], true);
        }
    
?>
<style>
    .pointer-content{
        width: 23%;
        float: left;
    }
    .pointer-arrow{
        width: 20px;
        float: left;
    }

    .pointer0 {
        height: 40px;
        position: relative;
        background: rgb(92, 188,90);
    }
    .pointer-after0 {
        position: relative;
        border-left: 20px solid rgb(92, 188,90);
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }
    .pointer-before0 {
        background: rgb(92, 188,90);
        position: relative;
        border-left: 20px solid white;
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }

    .pointer1 {
        height: 40px;
        position: relative;
        background: rgb(89, 168, 89);
    }
    .pointer-after1 {
        position: relative;
        border-left: 20px solid rgb(89, 168, 89);
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }
    .pointer-before1 {
        background: rgb(89, 168, 89);
        position: relative;
        border-left: 20px solid  white;
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }

    .pointer2 {
        height: 40px;
        position: relative;
        background: rgb(88, 146, 88);
    }
    .pointer-after2 {
        position: relative;
        border-left: 20px solid rgb(88, 146, 88);
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }
    .pointer-before2 {
        background-color: rgb(88, 146, 88);
        position: relative;
        border-left: 20px solid white;
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }

    .pointer3 {
        height: 40px;
        position: relative;
        background: rgb(90, 128, 91);
    }
    .pointer-after3 {
        position: relative;
        border-left: 20px solid rgb(90, 128, 91);
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }
    .pointer-before3 {
        background-color: rgb(90, 128, 91);
        position: relative;
        border-left: 20px solid white;
        border-top: 20px solid transparent;
        border-bottom: 20px solid transparent;
    }
</style>
<div class="row user-content-row">
    <div class="col-12 report-composer">
        <div class="report-composer-widget-title"><?php echo $trans->phrase("user_composer_phrase2"); echo $report['report_format_name']; ?></div>
        <div class="row user-content-row">
            <div class="col-12">
                <div class="accordion" id="composer_accordion">
                    <!-- Front page -->
                    <div class="card">
                        <div class="card-header" id="composer_frontpage_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="front_page" id="frontpage_check" <?php echo ($report_content && $report_content['front_page']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#front_page" aria-expanded="true" aria-controls="front_page">
                                    <?php echo $trans->phrase("user_composer_phrase3"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="front_page" class="collapse show" aria-labelledby="composer_frontpage_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="username" id="composer_username" <?php echo ($report_content && $report_content['front_page']['username']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_username">
                                        <?php echo $trans->phrase('user_composer_phrase13'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="company_name" id="composer_company_name" <?php echo ($report_content && $report_content['front_page']['company_name']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_company_name">
                                        <?php echo $trans->phrase('user_composer_phrase14'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="company_logo" id="composer_company_logo" <?php echo ($report_content && $report_content['front_page']['logo']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_company_logo">
                                        <?php echo $trans->phrase('user_composer_phrase15'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="ticket_id" id="composer_ticket_id" <?php echo ($report_content && $report_content['front_page']['ticket_id']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_ticket_id">
                                        <?php echo $trans->phrase('user_composer_phrase16'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="page_break1" id="composer_page_break1" <?php echo ($report_content && !$report_content['front_page']['page_break1']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break1">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 1 -->
                    
                    <div class="card">
                        <div class="card-header" id="composer_freetext1_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text_1" id="free_text1_check" <?php echo ($report_content && $report_content['free_text1']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_1" aria-expanded="true" aria-controls="free_text_1">
                                    <?php echo $trans->phrase("user_composer_phrase31"); ?> - Introduction 
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_1" class="collapse" aria-labelledby="composer_freetext1_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_1" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text1']['text']) ? $report_content['free_text1']['text'] : $trans->phrase("dt_composer_text_1"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break2" id="composer_page_break2" <?php echo ($report_content && $report_content['free_text1']['page_break2']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break2">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Intro Text -->
                    <div class="card">
                        <div class="card-header" id="composer_introtext_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="intro_text" id="intro_text_check" <?php echo ($report_content && $report_content['intro_text']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#intro_text" aria-expanded="true" aria-controls="intro_text">
                                    <?php echo $trans->phrase("user_composer_phrase5"); ?> - Sammendrag av analysen
                                </h3>
                            </div>
                        </div>

                        <div id="intro_text" class="collapse" aria-labelledby="composer_introtext_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <?php $dtcomp2 = $trans->phrase("dt_composer_text_2"); $dtcomp36 = $trans->phrase("dt_composer_text_36"); $dtcomp37 = $trans->phrase("dt_composer_text_37"); $dtcomp38 = $trans->phrase("dt_composer_text_38"); $dtcomp39 = $trans->phrase("dt_composer_text_39"); ?>
                                    <textarea id="composer_text_2" class="form-control composer-text"><?php echo ($report_content && $report_content['intro_text']['text']) ? $report_content['intro_text']['text'] : "$dtcomp2<br><br>$dtcomp36<br>$dtcomp37<br>$dtcomp38<br>$dtcomp39"; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break3" id="composer_page_break3" <?php echo ($report_content && !$report_content['intro_text']['page_break3']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break3">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 2 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext2_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text2" id="free_text2_check" <?php echo ($report_content && $report_content['free_text2']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_2" aria-expanded="true" aria-controls="free_text_2">
                                    <?php echo $trans->phrase("user_composer_phrase29"); ?> - Bakgrunn for analysen
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_2" class="collapse" aria-labelledby="composer_freetext2_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <?php $dtcomp3 = $trans->phrase("dt_composer_text_3"); $dtcomp31 = $trans->phrase("dt_composer_text_31") ?>
                                    <textarea id="composer_text_3" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text2']['text']) ? $report_content['free_text2']['text'] : "$dtcomp3<br><br>$dtcomp31" ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break4" id="composer_page_break4" <?php echo ($report_content && $report_content['free_text2']['page_break4']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break4">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- What is a conflict -->
                    <div class="card">
                        <div class="card-header" id="composer_conflict_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="conflict_text" id="conflict_text_check" <?php echo ($report_content && $report_content['conflict']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#conflict_text" aria-expanded="true" aria-controls="conflict_text">
                                    <?php echo $trans->phrase("user_composer_phrase6"); ?> - Mandat
                                </h3> 
                            </div>
                        </div>

                        <div id="conflict_text" class="collapse" aria-labelledby="composer_conflict_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_4" class="form-control composer-text"><?php echo ($report_content && $report_content['conflict']['text']) ? $report_content['conflict']['text'] : $trans->phrase("dt_composer_text_4"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break5" id="composer_page_break5" <?php echo ($report_content && !$report_content['conflict']['page_break5']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break5">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 3 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext3_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text3" id="free_text3_check" <?php echo ($report_content && $report_content['free_text3']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_3" aria-expanded="true" aria-controls="free_text_3">
                                    <?php echo $trans->phrase("user_composer_phrase28"); ?> - Profil - oversikt
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_3" class="collapse" aria-labelledby="composer_freetext3_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <?php $dtcomp5 = $trans->phrase("dt_composer_text_5"); $dtcomp32 = $trans->phrase("dt_composer_text_32"); $dtcomp33 = $trans->phrase("dt_composer_text_33"); $dtcomp34 = $trans->phrase("dt_composer_text_34"); $dtcomp35 = $trans->phrase("dt_composer_text_35"); ?>
                                    <textarea id="composer_text_5" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text3']['text']) ? $report_content['free_text3']['text'] : "$dtcomp5<br><br>$dtcomp32<br><br>$dtcomp33<br><br>$dtcomp34<br><br>$dtcomp35" ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break6" id="composer_page_break6" <?php echo ($report_content && $report_content['free_text3']['page_break6']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break6">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Summary -->
                    <div class="card">
                        <div class="card-header" id="composer_summary_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="summary_text" id="summary_text_check" <?php echo ($report_content && $report_content['summary']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#summary_text" aria-expanded="true" aria-controls="summary_text">
                                    <?php echo $trans->phrase("user_composer_phrase7"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="summary_text" class="collapse" aria-labelledby="composer_summary_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div id="composer_summary_text"><?php if(isset($ticket)) echo htmlspecialchars_decode( $ticket['ticket_summary'], ENT_QUOTES); ?></div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break7" id="composer_page_break7" <?php echo ($report_content && !$report_content['summary']['page_break7']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break7">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 4 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext4_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text4" id="free_text4_check" <?php echo ($report_content && $report_content['free_text4']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_4" aria-expanded="true" aria-controls="free_text_4">
                                    <?php echo $trans->phrase("user_composer_phrase30"); ?> -- Profil & analyse
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_4" class="collapse" aria-labelledby="composer_freetext4_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                  <?php $dtcomp6 = $trans->phrase("dt_composer_text_6"); $dtcomp6_1 = $trans->phrase("dt_composer_text_6_1"); $dtcomp6_2 = $trans->phrase("dt_composer_text_6_2"); $dtcomp6_3 = $trans->phrase("dt_composer_text_6_3"); $dtcomp6_4 = $trans->phrase("dt_composer_text_6_4"); $dtcomp6_5 = $trans->phrase("dt_composer_text_6_5");
                                  $dtcomp6_6 = $trans->phrase("dt_composer_text_6_6"); $dtcomp6_7 = $trans->phrase("dt_composer_text_6_7"); ?>
                                  <textarea id="composer_text_6a" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text4']['text']) ? $report_content['free_text4']['text'] : "<b>".$dtcomp6."</b><br>".$dtcomp6_1."<br><br><b>".$dtcomp6_2."</b><br>".$dtcomp6_3."<br><br><b>".$dtcomp6_4."</b><br>"
                                  .$dtcomp6_5."<br><br><b>".$dtcomp6_6."</b><br>".$dtcomp6_7; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <?php $dtcomp15 = $trans->phrase("dt_composer_text_15"); $dtcomp15_1 = $trans->phrase("dt_composer_text_15_1"); $dtcomp15_2 = $trans->phrase("dt_composer_text_15_2"); $dtcomp15_3 = $trans->phrase("dt_composer_text_15_3"); $dtcomp15_4 = $trans->phrase("dt_composer_text_15_4"); $dtcomp15_5 = $trans->phrase("dt_composer_text_15_5");
                                    $dtcomp15_6 = $trans->phrase("dt_composer_text_15_6"); $dtcomp15_7 = $trans->phrase("dt_composer_text_15_7"); $dtcomp15_8 = $trans->phrase("dt_composer_text_15_8"); $dtcomp15_9 = $trans->phrase("dt_composer_text_15_9"); $dtcomp15_10 = $trans->phrase("dt_composer_text_15_10"); $dtcomp15_11 = $trans->phrase("dt_composer_text_15_11");
                                    $dtcomp15_12 = $trans->phrase("dt_composer_text_15_12"); $dtcomp15_13 = $trans->phrase("dt_composer_text_15_13");?>
                                    <textarea id="composer_text_6b" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text4']['text2']) ? $report_content['free_text4']['text2'] : "<b>".$dtcomp15."</b>".$dtcomp15_1."<br><b>".$dtcomp15_2."</b>".$dtcomp15_3."<br><b>".$dtcomp15_4."</b>"
                                    .$dtcomp15_5."<br><b>".$dtcomp15_6."</b>".$dtcomp15_7."<br><b>".$dtcomp15_8."</b>".$dtcomp15_9."<br><b>".$dtcomp15_10."</b>".$dtcomp15_11."<br><br><i>".$dtcomp15_12."<br><br><b>".$dtcomp15_13."</i></b>"; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break8" id="composer_page_break8" <?php echo ($report_content && $report_content['free_text4']['page_break8']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break8">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Radar Graph -->
                    <div class="card">
                        <div class="card-header" id="composer_graph_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="radar_graph" id="radar_graph_check" <?php echo ($report_content && $report_content['radar_graph']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#radar_graph" aria-expanded="true" aria-controls="radar_graph">
                                    <?php echo $trans->phrase("user_composer_phrase8"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="radar_graph" class="collapse" aria-labelledby="composer_graph_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="composer_radar_grahp_header">
                                        <?php echo $trans->phrase('user_composer_phrase18'); ?>
                                    </label>
                                    <input type="text" id="composer_radar_graph_header" class="form-control" value="<?php echo ($report_content && $report_content['radar_graph']['header']) ? $report_content['radar_graph']['header'] : $trans->phrase("dt_composer_text_7"); ?>">
                                </div>

                                <!--Radar Graph-->
                                <?php
                                //Getting categories
                                $categories = array();
                                $categories_data = $Database->get_multiple_data('category_type', 'question', 'category');
                                foreach($categories_data as $category){
                                    $sql = "SELECT * FROM category_content WHERE category_id=".$category['category_id']." AND lang_code='".$_SESSION['trans']."';";
                                    $category_content = $Database->get_connection()->prepare($sql);
                                    $category_content->execute();
                                    if($category_content->rowCount() < 1) $category_content = false;
                                    else $category_content = $category_content->fetch(PDO::FETCH_ASSOC);

                                    if($category_content){
                                        $category['category_name'] = $category_content['category_name'];
                                        $category['category_details'] = $category_content['category_details'];
                                    }

                                    $single_category = array(
                                        "category_id" => $category['category_id'],
                                        "category_name" => $category['category_name'],
                                        "category_details" => $category['category_details']
                                    );
                                    array_push($categories, $single_category);
                                }
                                //Getting questions
                                $questions = $Database->get_multiple_data(false, false, 'question');
                                echo "<input type='text' id='question_input' hidden readonly value='".json_encode($questions)."'>";
                                if(isset($ticket))
                                    echo "<input type='text' id='response_input' hidden readonly value='".$ticket['ticket_response']."'>";
                                ?>
                                <!--Radar Graph 1-->
                                <div class="card text-center mb-2">
                                    <div class="card-header">
                                    <?php echo $trans->phrase('user_composer_phrase25'); ?>
                                    </div>
                                    <div class="card-body radar-graph" id="radar_graph_1">
                                        <?php foreach($categories as $category): ?>
                                        <div class="form-check form-check-inline">
                                            <?php $checked = ($category['category_id'] == 1 || $category['category_id'] == 9 || $category['category_id'] == 11 || $category['category_id'] == 13 || $category['category_id'] == 14) ? "checked": ""; ?>
                                            <input class="form-check-input graph-label" type="checkbox" id="radar1_category_<?php echo $category['category_id']; ?>" data-category="<?php echo $category['category_id']; ?>" value="<?php echo $category['category_name']; ?>" <?php echo $checked; ?>>
                                            <label class="form-check-label" for="radar1_category_<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                        <br><br>
                                        <?php if(isset($ticket)){ ?>
                                        <button type="button" id="draw_graph_1" class="btn btn-primary btn-sm"><?php echo $trans->phrase('user_composer_phrase27'); ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!--Radar Graph 2-->
                                <div class="card text-center mb-2">
                                    <div class="card-header">
                                    <?php echo $trans->phrase('user_composer_phrase26'); ?>
                                    </div>
                                    <div class="card-body radar-graph" id="radar_graph_2">
                                        <?php foreach($categories as $category): ?>
                                        <div class="form-check form-check-inline">
                                            <?php $checked = ($category['category_id'] == 2 || $category['category_id'] == 10 || $category['category_id'] == 12 || $category['category_id'] == 15 || $category['category_id'] == 16) ? "checked": ""; ?>
                                            <input class="form-check-input graph-label" type="checkbox" id="radar2_category_<?php echo $category['category_id']; ?>" data-category="<?php echo $category['category_id']; ?>" value="<?php echo $category['category_name']; ?>" <?php echo $checked; ?>>
                                            <label class="form-check-label" for="radar2_category_<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                        <br><br>
                                        <?php if(isset($ticket)){ ?> 
                                        <button type="button" id="draw_graph_2" class="btn btn-primary btn-sm"><?php echo $trans->phrase('user_composer_phrase27'); ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!-- Free Text 5 -->
                                <div class="form-group">
                                    <textarea id="composer_text_8" class="form-control composer-text"><?php echo ($report_content && $report_content['radar_graph']['text']) ? $report_content['radar_graph']['text'] : $trans->phrase("dt_composer_text_8"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break9" id="composer_page_break9" <?php echo ($report_content && !$report_content['radar_graph']['page_break9']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break9">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 6 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext6_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text6" id="free_text6_check" <?php echo ($report_content && $report_content['free_text6']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0"
                                    data-toggle="collapse"
                                    data-target="#free_text_6"
                                    aria-expanded="true"
                                    aria-controls="free_text_6">
                                    <?php echo $trans->phrase("user_composer_phrase4"); ?> - Motivasjonsprofiler
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_6" class="collapse"
                            aria-labelledby="composer_freetext6_heading"
                            data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_9"
                                        class="form-control composer-text">
                                        <?php echo ($report_content
                                            && $report_content['free_text6']['text']) ? $report_content['free_text6']['text'] : $trans->phrase("dt_composer_text_9"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break10" id="composer_page_break10" <?php echo ($report_content && $report_content['free_text6']['page_break10']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break10">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- review -->
                    <div class="card">
                        <div class="card-header" id="composer_review_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="is_ticket_review" id="chk_review_check" <?php echo ($report_content && $report_content['review']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#cardbody_review" aria-expanded="true" aria-controls="cardbody_review"> 
                                    <?php echo $trans->phrase("text_review"); ?>
                                </h3>
                            </div>
                        </div>
                        <?php
                            if(isset($ticket)){
                                $review = json_decode($ticket['ticket_review'], true);

                                if(isset($review['review_status'])){
                                    $reviewStatus =  $review['review_status'];
                                    $reviewOptions = explode(",",$reviewStatus);
                                }

                                if(isset($review['review_text'])){
                                    $ticketReview = $review['review_text'];
                                }

                                $disabledStatus = 'disabled';
                            }
                        ?>
                        <div id="cardbody_review" class="collapse" aria-labelledby="composer_review_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="row user-content-row">
                                    <div class="col-12" style="margin-left:2%">
                                        <?php if(isset($ticket)){ ?>
                                        <form>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase30"); ?></label><br>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Anger"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Anger",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_1" <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_1">
                                                    <?php echo $trans->phrase("user_ticket_phrase31"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Fear"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Fear",$reviewOptions)==1) {
                                                        echo "checked='checked'"; } ?>
                                                    id="review_check_2"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_2">
                                                    <?php echo $trans->phrase("user_ticket_phrase32"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Anxiety"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Anxiety",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_3"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_3">
                                                    <?php echo $trans->phrase("user_ticket_phrase33"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Loss"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Loss",$reviewOptions)==1) {
                                                        echo "checked='checked'"; } ?>
                                                    id="review_check_4"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_4">
                                                    <?php echo $trans->phrase("user_ticket_phrase34"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Sadness"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Sadness",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_5"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_5">
                                                    <?php echo $trans->phrase("user_ticket_phrase35"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Resignation"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Resignation",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_6"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_6">
                                                    <?php echo $trans->phrase("user_ticket_phrase36"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check" type="checkbox"
                                                value="Guilt"
                                                <?php if(isset($reviewOptions)
                                                        && in_array("Guilt",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                id="review_check_7"
                                                <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_7">
                                                    <?php echo $trans->phrase("user_ticket_phrase37"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Shame"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Shame",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_8"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_8">
                                                    <?php echo $trans->phrase("user_ticket_phrase38"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Jealousy"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Jealousy",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_9"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_9">
                                                    <?php echo $trans->phrase("user_ticket_phrase39"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Enthusiasm"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Enthusiasm",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_10"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_10">
                                                    <?php echo $trans->phrase("user_ticket_phrase40"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox"
                                                    value="Tenderness"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Tenderness",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_11"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_11">
                                                    <?php echo $trans->phrase("user_ticket_phrase41"); ?>
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left:2%">
                                                <input class="form-check-input review-check"
                                                    type="checkbox" value="Hope"
                                                    <?php if(isset($reviewOptions)
                                                        && in_array("Hope",$reviewOptions)==1) {
                                                    echo "checked='checked'"; } ?>
                                                    id="review_check_12"
                                                    <?php echo $disabledStatus; ?>>
                                                <label class="form-check-label" for="review_check_12">
                                                    <?php echo $trans->phrase("user_ticket_phrase42"); ?>
                                                </label>
                                            </div>
                                        </form>
                                        <div class="row user-content-row">
                                            <div class="col-12">
                                                <form>
                                                    <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase43"); ?></label>
                                                    <textarea id="ticket_review">
                                                        <?php if(isset($ticketReview)) { echo $ticketReview; } ?>
                                                    </textarea>
                                                </form>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="page_break_review"
                                                id="composer_page_break_review" <?php echo ($report_content && $report_content['review']['page_break_review']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="composer_page_break_review">
                                                <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="card">
                        <div class="card-header" id="composer_cardbody_rating">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="rating" id="chk_rating_check"<?php echo ($report_content && $report_content['rating']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#cardbody_rating" aria-expanded="true" aria-controls="cardbody_rating"><?php echo $trans->phrase("text_rating"); ?></h3>
                            </div>
                        </div>

                        <?php
                            if (isset($ticket)){    
                                $rating = json_decode($ticket['ticket_rating'], true);

                                $rating_check_1 = 0;
                                $rating_check_2 = 0;
                                $rating_check_3 = 0;
                                $rating_check_4 = 0;

                                $rating_text_1 = '';
                                $rating_text_2 = '';

                                if(isset($rating)) {
                                    $rating_check_1 = $rating['rating_check_1'];
                                    $rating_check_2 = $rating['rating_check_2'];
                                    $rating_check_3 = $rating['rating_check_3'];
                                    $rating_check_4 = $rating['rating_check_4'];

                                    $rating_text_1 = $rating['rating_text_1'];
                                    $rating_text_2 = $rating['rating_text_2'];
                                }

                                $ratingStatus = $ticket['rating_status'];
                            }
                        ?>

                        <div id="cardbody_rating" class="collapse" aria-labelledby="composer_cardbody_rating" data-parent="#composer_accordion">
                            <div class="card-body">
                            <?php if (isset($ticket)){ ?>
                                <div class="row user-content-row">
                                    <input type="hidden" id="rating_status_value" name="rating_status_value" value="<?php echo $ratingStatus; ?>"/>
                                    <div class="col-12">
                                        <form>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase47"); ?></label><br>
                                            <div id="rating_check_1" class="smiley-check">
                                                <i class="far fa-frown <?php if($rating_check_1==1) echo "active" ?>"></i>
                                                <i class="far fa-frown-open <?php if($rating_check_1==2) echo "active" ?>"></i>
                                                <i class="far fa-meh <?php if($rating_check_1==3) echo "active" ?>"></i>
                                                <i class="far fa-smile <?php if($rating_check_1==4) echo "active" ?>"></i>
                                                <i class="far fa-grin <?php if($rating_check_1==5) echo "active" ?>"></i>
                                            </div>
                                            <br>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase48"); ?></label><br>
                                            <div id="rating_check_2" class="smiley-check">
                                                <i class="far fa-frown <?php if($rating_check_2==1) echo "active" ?>"></i>
                                                <i class="far fa-frown-open <?php if($rating_check_2==2) echo "active" ?>"></i>
                                                <i class="far fa-meh <?php if($rating_check_2==3) echo "active" ?>"></i>
                                                <i class="far fa-smile <?php if($rating_check_2==4) echo "active" ?>"></i>
                                                <i class="far fa-grin <?php if($rating_check_2==5) echo "active" ?>"></i>
                                            </div><br>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase49"); ?></label><br>
                                            <div id="rating_check_3" class="smiley-check">
                                                <i class="far fa-frown <?php if($rating_check_3==1) echo "active" ?>"></i>
                                                <i class="far fa-frown-open <?php if($rating_check_3==2) echo "active" ?>"></i>
                                                <i class="far fa-meh <?php if($rating_check_3==3) echo "active" ?>"></i>
                                                <i class="far fa-smile <?php if($rating_check_3==4) echo "active" ?>"></i>
                                                <i class="far fa-grin <?php if($rating_check_3==5) echo "active" ?>"></i>
                                            </div><br>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase50"); ?></label><br>
                                            <div id="rating_check_4" class="smiley-check">
                                                <i class="far fa-frown <?php if($rating_check_4==1) echo "active" ?>"></i>
                                                <i class="far fa-frown-open <?php if($rating_check_4==2) echo "active" ?>"></i>
                                                <i class="far fa-meh <?php if($rating_check_4==3) echo "active" ?>"></i>
                                                <i class="far fa-smile <?php if($rating_check_4==4) echo "active" ?>"></i>
                                                <i class="far fa-grin <?php if($rating_check_4==5) echo "active" ?>"></i>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row user-content-row">
                                    <div class="col-12">
                                        <form>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase51"); ?></label>
                                            <textarea id="rating_text_1">
                                                <?php echo $rating['rating_text_1']; ?>
                                            </textarea>
                                        </form>
                                    </div>
                                </div>
                                <div class="row user-content-row">
                                    <div class="col-12">
                                        <form>
                                            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase52"); ?></label>
                                            <textarea id="rating_text_2">
                                                <?php echo $rating['rating_text_2']; ?>
                                            </textarea>
                                        </form>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break_rating" id="composer_page_break_rating" <?php echo ($report_content && $report_content['rating']['page_break_rating']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break_rating">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- free Text


                        <div id="free_text_6" class="collapse" aria-labelledby="composer_freetext6_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_9" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text6']['text']) ? $report_content['free_text6']['text'] : $trans->phrase("dt_composer_text_9"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break10" id="composer_page_break10" <?php echo ($report_content && $report_content['free_text6']['page_break10']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break10">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Methods -->
                    <div class="card">
                        <div class="card-header" id="composer_methods_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="methods" id="methods_check" <?php echo ($report_content && $report_content['method']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#methods" aria-expanded="true" aria-controls="methods">
                                    <?php echo $trans->phrase("user_composer_phrase9"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="methods" class="collapse" aria-labelledby="composer_methods_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <!-- Getting methods-->
                                <?php
                                if(isset($ticket)){
                                    $methods = $Database->get_multiple_data(false, false, 'method');
                                    $ticket_methods = json_decode($ticket['ticket_methods'], true);
                                    $ranked_methods = array();
                                    $method = null;
                                    $count = 0;

                                    foreach($ticket_methods as $method_id => $selection){
                                        if($selection == 0) continue;
                                        $count++;
                                        foreach($methods as $s_method){
                                            if($s_method['method_id'] == $method_id){
                                                $method = $s_method;
                                                break;
                                            }
                                        }
                                        $method_data = $Database->get_multiple_data('method_id', $method_id, 'method_content');

                                        foreach($method_data as $data){
                                            $method_info = $data;
                                            if($data['lang_code'] == $_SESSION['trans'])
                                                break;
                                        }

                                        $single_method = array(
                                            "method_id" => $method_id,
                                            "rank" => $count,
                                            "method_name" => $method_info['method_name'],
                                            "method_details" => htmlspecialchars_decode($method_info['method_details'], ENT_QUOTES),
                                            "method_color" => $method['method_color'],
                                            "seq_rank" => $method['seq_rank']
                                        );

                                        $ranked_methods[$count] = $single_method;
                                    }
                                    //file_put_contents('php://stderr', print_r($ranked_methods, TRUE));
                                    //Rank for presentation
                                    usort($ranked_methods, function ($item1, $item2) {
                                        if ($item1['seq_rank'] == $item2['seq_rank']) return 0;
                                        return $item1['seq_rank'] < $item2['seq_rank'] ? -1 : 1;
                                    });
                                    echo "<input type='text' id='ranked_methods' value='".json_encode($ranked_methods)."' hidden readonly>";
                                ?>
                                <!--Method Header-->
                                <div class="composer-method-header">
                                    <div class="container" style="display: flex;justify-content: space-between;font-size: 16px;color: wheat;width:90%;height:140px; background-image: linear-gradient(to left,rgba(255,0,0,0.8), green); border-radius: 20px; padding:10px 20px; text-align: center;">
                                    
                                        <?php echo (
                                            "<div style='width: 16%;float:left; margin-left:5px'>
                                                <div style='height:70px;background-color: green;border-radius: 15px;'>
                                                    <div style='height:100%;border-radius: 15px;background-image: linear-gradient(rgb(163,212,133), rgb(80,124,50));'>".$trans->phrase('user_composer_phrase19')."</div>
                                                </div>
                                            </div>"); 
                                        ?>

                                        <?php
                                            $temp_array = array(
                                                "0"=>array(
                                                    "background"=>"cornflowerblue",
                                                    "start"=>"rgb(157,190,222)",
                                                    "end"=>"rgb(47,111,157)",
                                                ),
                                                "1"=>array(
                                                    "background"=>"darkslateblue",
                                                    "start"=>"rgb(141,168,211)",
                                                    "end"=>"rgb(39,80,134)",
                                                ),
                                                "2"=>array(
                                                    "background"=>"yellow",
                                                    "start"=>"rgb(255,212,106)",
                                                    "end"=>"rgb(172,135,0)",
                                                ),
                                                "3"=>array(
                                                    "background"=>"orange",
                                                    "start"=>"rgb(245, 173, 127)",
                                                    "end"=>"rgb(188,84,27)",
                                                ),
                                            ); 
                                            foreach($ranked_methods as $key=>$method): 
                                        ?>
                                        <?php 
                                            echo(
                                                "<div style='width: 16%; float:left;'>
                                                    <div style='height:70px; margin-top:30px;background-color: ".$temp_array[$key]["background"].";border-radius: 15px;'>
                                                        <div style='height:100%;border-radius: 15px;background-image: linear-gradient(".$temp_array[$key]["start"].",".$temp_array[$key]["end"].");'>".$method['method_name']."</div>
                                                    </div>
                                                </div>"
                                            ); 
                                        ?>
                                        <?php 
                                            endforeach; 
                                        ?>
                                        
                                        <?php echo (
                                            "<div style='width: 16%;float:left;'>
                                                <div style='height:70px;background-color: red;border-radius: 15px;'>
                                                    <div style='height:100%;border-radius: 15px;background-image: linear-gradient(rgb(245,85,108), rgb(222,29,48));'>".$trans->phrase('user_composer_phrase20')."</div>
                                                </div>
                                            </div>"); 
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <!--Method header text-->
                                <div class="composer-method-header-text" style="color:white;font-size: 16px;">
                                    <div class="container" style="width:90%;padding-left: 0;margin-top:-10px;display: -webkit-box;">

                                        <?php 
                                            $temp_array = explode("---------",$trans->phrase('user_composer_phrase21')); 
                                            foreach($temp_array as $key=>$element):
                                        ?>
                                            <?php
                                            if($key == 0){
                                                echo("<div class='pointer-arrow pointer-before".$key."' style='z-index:".(3-$key).";'></div>");
                                                echo("<div class='pointer-content pointer".$key."' style='z-index:".(3-$key).";'>".$element."</div>");
                                                echo("<div class='pointer-arrow pointer-after".$key."' style='z-index:".(3-$key).";'></div>");
                                            }else{
                                                echo("<div class='pointer-arrow pointer-before".$key."' style='z-index:".(3-$key).";margin-left: -18px;'></div>");
                                                echo("<div class='pointer-content pointer".$key."' style='z-index:".(3-$key).";'>".$element."</div>");
                                                echo("<div class='pointer-arrow pointer-after".$key."' style='z-index:".(3-$key).";'></div>");
                                            }
                                                
                                            ?>
                                        <?php
                                            endforeach;
                                        ?>
                                    </div>
                                    <div class="container" style="display:flex;justify-content: space-between;width:85%; padding-top:10px">
                                        <?php 
                                            $img_array = array(
                                                "0"=> "green-left.jpg",
                                                "1"=> "red-right.jpg"
                                            );
                                            $temp_array = explode("---------------------------------------------",$trans->phrase('user_composer_phrase22')); 
                                            echo("<div style='width:15%; inline-height:65px;text-indent:15px;text-align:left;padding-left:20px; padding-top:10px; height: 80px;float:left;background-color: #5FC15C;border-top-left-radius: 50%;border-bottom-left-radius: 45%;border-bottom-right-radius: 50%;box-shadow: 3px 3px 3px #5fc15c;'>".$temp_array["0"]."</div>");
                                            echo("<div style='display:flex;justify-content:space-evenly;width:15%; inline-height:65px;text-align:end; padding-left:50px; padding-top:10px;height: 80px;float:right; background-color: #F02739; border-top-right-radius: 45%;border-bottom-right-radius: 45%;border-bottom-left-radius: 50%;box-shadow: 2px 4px 3px #c19a9d;'><div>".$temp_array["1"]."</div></div>");
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <!--Method accordion-->
                                <div class="accordion" id="method-accordion">
                                    <?php foreach($ranked_methods as $method): ?>
                                    <div class="card">
                                        <div class="card-header"  style="padding-left:30px;" id="method-heading-<?php echo $method['rank']; ?>">
                                            <input class="form-check-input" type="checkbox" value="page_break_rating" id="chk_method<?php echo $method['method_id']?>" checked/>

                                            <h4 class="accordion-heading mb-0"
                                                data-toggle="collapse"
                                                data-target="#method-text-<?php echo $method['rank']; ?>" aria-expanded="true" aria-controls="method-text-<?php echo $method['rank']; ?>">
                                               <?php echo $method['method_name']; ?>
                                            </h4>
                                        </div>

                                        <div id="method-text-<?php echo $method['rank']; ?>" class="collapse" aria-labelledby="method-heading-<?php echo $method['rank']; ?>" data-parent="#method-accordion">
                                            <div class="card-body">
                                                <?php echo htmlspecialchars_decode( $method['method_details'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php }
                                   else echo "<input type='text' id='ranked_methods' value='' hidden readonly>";
                                 ?>
                                <br>
                                <!--Page Break-->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break11" id="composer_page_break11" <?php echo ($report_content && !$report_content['method']['page_break11']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break11">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 7 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext7_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text7" id="free_text7_check" <?php echo ($report_content && $report_content['free_text7']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_7" aria-expanded="true" aria-controls="free_text_7">
                                    <?php echo $trans->phrase("user_composer_phrase4"); ?> - Anbefalt metode & fremgangsmåte
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_7" class="collapse" aria-labelledby="composer_freetext7_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_10" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text7']['text']) ? $report_content['free_text7']['text'] : $trans->phrase("dt_composer_text_10"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break12" id="composer_page_break12" <?php echo ($report_content && $report_content['free_text7']['page_break12']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break12">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tips -->
                    <div class="card">
                        <div class="card-header" id="composer_tips_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="tips" id="tips_check" <?php echo ($report_content && $report_content['tip']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#tips" aria-expanded="true" aria-controls="tips"><?php echo $trans->phrase("user_composer_phrase10"); ?></h3>
                            </div>
                        </div>

                        <div id="tips" class="collapse" aria-labelledby="composer_tips_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <!--Getting Questions-->
                                <?php
                                    if(isset($ticket)){
                                        $selected_questions = array();
                                        $responses = json_decode($ticket['ticket_response'], true);
                                        foreach($responses as $question_id => $response){
                                            $question_data = $Database->get_data('question_id', $question_id, 'question', true);
                                            $question_content = null;
                                            $fetched_content = $Database->get_multiple_data('question_id', $question_id, 'question_content');
                                            if($fetched_content) {
                                                foreach($fetched_content as $content){
                                                    $question_content = $content;
                                                    if($content['lang_code'] == $_SESSION['trans'])
                                                        break;
                                                }
                                            }
                                            $tips_type;
                                            $question_answer;
                                            if($response['type'] == 'yes-no' && $response['answer'] == 1){
                                                $tips_type = 'no';
                                                $question_answer = $trans->phrase('user_ticket_phrase13');
                                            }
                                            else if($response['type'] == 'yes-no' && $response['answer'] == 2){
                                                $tips_type = 'yes';
                                                $question_answer = $trans->phrase('user_ticket_phrase12');
                                            }
                                            else if($response['type'] == 'mcq' && (int)$response['answer'] == 1){
                                                $tips_type = 'no';
                                                $question_answer = $question_content['question_option1'];
                                            }
                                            else if($response['type'] == 'mcq' && (int)$response['answer'] == 2){
                                                $tips_type = 'no';
                                                $question_answer = $question_content['question_option2'];
                                            }
                                            else if($response['type'] == 'mcq' && (int)$response['answer'] == 3){
                                                $tips_type = null;
                                                $question_answer = $question_content['question_option3'];
                                            }
                                            else if($response['type'] == 'mcq' && (int)$response['answer'] == 4){
                                                $tips_type = 'yes';
                                                $question_answer = $question_content['question_option4'];
                                            }
                                            else if($response['type'] == 'mcq' && (int)$response['answer'] == 5){
                                                $tips_type = 'yes';
                                                $question_answer = $question_content['question_option5'];
                                            }
                                            else{
                                                $tips_type = null;
                                                $question_answer = 'Skipped';
                                            }

                                            $question_tips = "";
                                            if($question_data['question_tip_on_yes'] && $tips_type == 'yes')
                                                $question_tips = $question_content['question_tips_yes'];
                                            elseif($question_data['question_tip_on_no'] && $tips_type == 'no')
                                                $question_tips = $question_content['question_tips_no'];

                                            $single_question = array(
                                                "question_id" => $question_id,
                                                "question_text" => htmlspecialchars_decode( $question_content['question_name'], ENT_QUOTES),
                                                "question_answer" => $question_answer,
                                                "question_tips" => htmlspecialchars_decode( $question_tips, ENT_QUOTES),
                                            );
                                            array_push($selected_questions, $single_question);
                                        }
                                        echo "<input type='text' id='question_tips' value='".json_encode($selected_questions)."' readonly hidden>";
                                    }
                                    else echo "<input type='text' id='question_tips' value='' readonly hidden>";
                                    
                                ?>
                                <!--Tips accordion-->
                                <?php if(isset($ticket)){ ?>
                                <div class="accordion" id="tips-accordion">
                                    <?php foreach($selected_questions as $question): ?>
                                        <?php if(!$question['question_tips']) continue; ?>
                                    <div class="card">
                                        <div class="card-header" id="tips-heading-<?php echo $question['question_id']; ?>">
                                            <h4 class="accordion-heading mb-0" data-toggle="collapse" data-target="#tips-text-<?php echo $question['question_id']; ?>" aria-expanded="true" aria-controls="tips-text-<?php echo $question['question_id']; ?>">
                                                <?php echo htmlspecialchars_decode( $question['question_text'], ENT_QUOTES); ?><br>
                                                <span style="color: orange"><?php echo $trans->phrase('user_composer_phrase23').$question['question_answer']; ?></span>
                                            </h4>
                                        </div>

                                        <div id="tips-text-<?php echo $question['question_id']; ?>" class="collapse" aria-labelledby="tips-heading-<?php echo $question['question_id']; ?>" data-parent="#tips-accordion">
                                            <div class="card-body">
                                                <?php echo htmlspecialchars_decode( $question['question_tips'], ENT_QUOTES); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <br>
                                <?php } ?>
                                <!--Page Break-->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break13" id="composer_page_break13" <?php echo ($report_content && !$report_content['tip']['page_break13']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break13">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Free Text 8 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext8_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text8" id="free_text8_check" <?php echo ($report_content && $report_content['free_text8']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_8" aria-expanded="true" aria-controls="free_text_8">
                                    <?php echo $trans->phrase("user_composer_phrase4"); ?> -- Risikoanalyse
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_8" class="collapse" aria-labelledby="composer_freetext8_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_11" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text8']['text']) ? $report_content['free_text8']['text'] : $trans->phrase("dt_composer_text_11"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break14" id="composer_page_break14" <?php echo ($report_content && $report_content['free_text8']['page_break14']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break14">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Disclaimer -->
                    <div class="card">
                        <div class="card-header" id="composer_disclaimer_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="disclaimer" id="disclaimer_check" <?php echo ($report_content && $report_content['disclaimer']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#disclaimer" aria-expanded="true" aria-controls="disclaimer">
                                    <?php echo $trans->phrase("user_composer_phrase11"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="disclaimer" class="collapse" aria-labelledby="composer_disclaimer_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_12" class="form-control composer-text"><?php echo ($report_content && $report_content['disclaimer']['disclaimer_text']) ? $report_content['disclaimer']['disclaimer_text'] : $trans->phrase("dt_composer_text_12"); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <textarea id="composer_text_13" class="form-control composer-text"><?php echo ($report_content && $report_content['disclaimer']['free_text']) ? $report_content['disclaimer']['free_text'] : $trans->phrase("dt_composer_text_13"); ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break15" id="composer_page_break15" <?php echo ($report_content && $report_content['disclaimer']['page_break15']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break15">
                                        <?php echo $trans->phrase('user_composer_phrase17'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Risk Assessment -->
                    <div class="card">
                        <div class="card-header" id="composer_assessment_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="assesment" id="assesment_check" <?php echo ($report_content && $report_content['assessment']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#assessment" aria-expanded="true" aria-controls="assessment">
                                    <?php echo $trans->phrase("user_composer_phrase12"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="assessment" class="collapse" aria-labelledby="composer_assessment_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                  <!DOCTYPE html>
                                  <html lang="en" dir="ltr">
                                    <head>
                                      <style>
                                        table, th, td {
                                            border: 1px solid black;
                                            border-collapse: collapse;
                                        }
                                        th, td {
                                            text-align: center;
                                            font-family: 'Montserrat';
                                            font-size: 91%;
                                            font-weight: normal;
                                        }
                                        pre {
                                            font-size: 75%;
                                            text-align: left;
                                            font-family: 'Montserrat';
                                        }
                                      </style>
                                      <meta charset="utf-8">
                                      <title></title>
                                    </head>
                                    <body>
                                       <table style="width:100%;" id="risk_assignment_table">
                                        <tr>
                                          <th colspan="2" style="width:10%;">
                                                <?php echo $trans->phrase("risk_assessment_table1_1"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table1_2"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table1_3"); ?>
                                            </th>
                                            <th colspan="3" style="width:10%;">
                                                <?php echo $trans->phrase("risk_assessment_table2_1"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table2_2"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table2_3"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table2_4"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table2_5"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table2_6"); ?>
                                            </th>
                                            <th colspan="2" style="width:10%;">
                                                <?php echo $trans->phrase("risk_assessment_table3_1"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table3_2"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table3_3"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table3_4"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table3_5"); ?>
                                                <br>
                                                <?php echo $trans->phrase("risk_assessment_table3_6"); ?>
                                            </th>
                                        </tr>
                                        <tr style="background-color:lightgray; font-size:66%;">
                                          <td style="text-align:left"><?php echo $trans->phrase("risk_assessment_table4"); ?></td>
                                          <td class="risk_type"><?php echo $trans->phrase("risk_assessment_table5"); ?></td>
                                          <td class="risk_type"><?php echo $trans->phrase("risk_assessment_table6"); ?></td>
                                          <td class="risk_type"><?php echo $trans->phrase("risk_assessment_table7"); ?></td>
                                          <td class="risk_type"><?php echo $trans->phrase("risk_assessment_table8"); ?></td>
                                          <td class="risk_type"><?php echo $trans->phrase("risk_assessment_table9"); ?></td>
                                          <td class="risk_type"><?php echo $trans->phrase("risk_assessment_table10"); ?></td>
                                        </tr>
                                        <tr>
                                          <td style="width:8rem; text-align:left;"><?php echo $trans->phrase("risk_assessment_table11"); ?></td>
                                            <?php foreach($report_content['assessment']['risks']['rumours'] as $rumours):?>
                                                <td class="risk_type_rumours" contenteditable="true" style="width:7rem; height:2rem; resize:none;"><?php echo $rumours; ?></td>
                                            <?php endforeach?>
                                        </tr>
                                        <tr>
                                          <td style="width:8rem; text-align:left;"><?php echo $trans->phrase("risk_assessment_table12"); ?></td>
                                            <?php foreach($report_content['assessment']['risks']['notEnoughInfo'] as $notEnoughInfo):?>
                                                <td class="risk_type_rumours" contenteditable="true" style="width:7rem; height:2rem; resize:none;"><?php echo $notEnoughInfo; ?></td>
                                            <?php endforeach?>
                                        </tr>
                                        <tr>
                                          <td style="width:8rem; text-align:left;"><?php echo $trans->phrase("risk_assessment_table13"); ?></td>
                                            <?php foreach($report_content['assessment']['risks']['inaccessibleLeader'] as $inaccessibleLeader):?>
                                                <td class="risk_type_rumours" contenteditable="true" style="width:7rem; height:2rem; resize:none;"><?php echo $inaccessibleLeader; ?></td>
                                            <?php endforeach?>
                                        </tr>
                                        <tr>
                                          <td style="width:8rem; text-align:left;"><?php echo $trans->phrase("risk_assessment_table14"); ?></td>
                                            <?php foreach($report_content['assessment']['risks']['sickleave'] as $sickleave):?>
                                                <td class="risk_type_rumours" contenteditable="true" style="width:7rem; height:2rem; resize:none;"><?php echo $sickleave; ?></td>
                                            <?php endforeach?>
                                        </tr>
                                        <tr>
                                          <td style="width:8rem; text-align:left;"><?php echo $trans->phrase("risk_assessment_table15"); ?></td>
                                            <?php foreach($report_content['assessment']['risks']['polarization'] as $polarization):?>
                                                <td class="risk_type_rumours" contenteditable="true" style="width:7rem; height:2rem; resize:none;"><?php echo $polarization; ?></td>
                                            <?php endforeach?>
                                        </tr>
                                      </table>
                                    </body>
                                  </html>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <button type="button" id="save_report_format_composed" class="btn btn-success"><?php echo $trans->phrase('user_composer_phrase24'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php
    else:
        echo $trans->phrase("user_composer_phrase1");
    endif;
else:
    echo $trans->phrase("user_composer_phrase1");
endif;
?>
