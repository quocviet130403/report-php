<?php
if(isset($_GET['id'])):
    require_once('../database.php');
    $Database = new Database();

    // if(isset($_GET['id'])){
    //     $ticket = $Database->get_data('ticket_id', $_GET['id'], 'ticket', true);
    //     if($ticket) $question_response = json_decode($ticket['ticket_response'], true);
    // }
    if(isset($_GET['id']) && $_GET['id'] != ""):
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
    <div class="col-12 report-dcomposer">
        <div class="report-composer-widget-title"><?php echo $trans->phrase("user_composer_phrase2"); ?> - <span><?php echo $report['report_format_name']; ?></span> Report</div>
        <div class="row user-content-row">
            <div class="col-12">
                <div class="accordion" id="composer_accordion">
                    <!-- Front page -->
                    <div class="card">
                        <div class="card-header" id="composer_frontpage_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="front_page" id="frontpage_check" <?php echo ($report_content && $report_content['front_page']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#front_page" aria-expanded="true" aria-controls="front_page">
                                    <?php echo $trans->phrase("pdf_text1"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="front_page" class="collapse show" aria-labelledby="composer_frontpage_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="username" id="composer_username" <?php echo ($report_content && $report_content['front_page']['username']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_username">
                                        <?php echo $trans->phrase('pdf_text1a'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="company_name" id="composer_company_name" <?php echo ($report_content && $report_content['front_page']['company_name']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_company_name">
                                        <?php echo $trans->phrase('pdf_text1b'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="company_logo" id="composer_company_logo" <?php echo ($report_content && $report_content['front_page']['logo']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_company_logo">
                                        <?php echo $trans->phrase('pdf_text1c'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="ticket_id" id="composer_ticket_id" <?php echo ($report_content && $report_content['front_page']['ticket_id']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_ticket_id">
                                        <?php echo $trans->phrase('pdf_text1d'); ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="page_break1" id="composer_page_break1" <?php echo ($report_content && !$report_content['front_page']['page_break1']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break1">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
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
                                    <?php echo $trans->phrase("pdf_text2"); ?> 
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
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Start Page Text -->
                    <div class="card">
                        <div class="card-header" id="composer_introtext_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="intro_text" id="intro_text_check" <?php echo ($report_content && $report_content['intro_text']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#intro_text" aria-expanded="true" aria-controls="intro_text">
                                    <?php echo $trans->phrase("pdf_text3"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="intro_text" class="collapse" aria-labelledby="composer_introtext_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <?php $dtcomp2 = $trans->phrase("dt_composer_text_2"); ?>
                                    <textarea id="composer_text_2" class="form-control composer-text"><?php echo ($report_content && $report_content['intro_text']['text']) ? $report_content['intro_text']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break3" id="composer_page_break3" <?php echo ($report_content && !$report_content['intro_text']['page_break3']) ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="composer_page_break3">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Free Text 2 -->
                    
                    <div class="card">
                        <div class="card-header" id="composer_freetext2_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text_2" id="free_text2_check" <?php echo ($report_content && $report_content['free_text2']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_2" aria-expanded="true" aria-controls="free_text_2">
                                    <?php echo $trans->phrase("pdf_text4"); ?> 
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_2" class="collapse" aria-labelledby="composer_freetext2_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_3" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text2']['text']) ? $report_content['free_text2']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break4" id="composer_page_break4" <?php echo ($report_content && $report_content['free_text2']['page_break4']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break2">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
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
                                    <?php echo $trans->phrase("pdf_text5"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_3" class="collapse" aria-labelledby="composer_freetext3_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_4" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text3']['text']) ? $report_content['free_text3']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break5" id="composer_page_break5" <?php echo ($report_content && $report_content['free_text3']['page_break5']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break6">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
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
                                    <?php echo $trans->phrase("pdf_text6"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_4" class="collapse" aria-labelledby="composer_freetext4_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_5" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text4']['text']) ? $report_content['free_text4']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break6" id="composer_page_break6" <?php echo ($report_content && $report_content['free_text4']['page_break6']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break6">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Free Text 5 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext5_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text5" id="free_text5_check" <?php echo ($report_content && $report_content['free_text5']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_5" aria-expanded="true" aria-controls="free_text_5">
                                    <?php echo $trans->phrase("pdf_text7"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_5" class="collapse" aria-labelledby="composer_freetext5_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_6" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text5']['text']) ? $report_content['free_text5']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break7" id="composer_page_break7" <?php echo ($report_content && $report_content['free_text5']['page_break7']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break7">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
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
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_6" aria-expanded="true" aria-controls="free_text_6">
                                    <?php echo $trans->phrase("pdf_text8"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_6" class="collapse" aria-labelledby="composer_freetext6_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_7" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text6']['text']) ? $report_content['free_text6']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break8" id="composer_page_break8" <?php echo ($report_content && $report_content['free_text6']['page_break8']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break8">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
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
                                    <?php echo $trans->phrase("pdf_text9"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_7" class="collapse" aria-labelledby="composer_freetext7_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_8" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text7']['text']) ? $report_content['free_text7']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break9" id="composer_page_break9" <?php echo ($report_content && $report_content['free_text7']['page_break9']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break9">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
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
                                    <?php echo $trans->phrase("pdf_text10"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_8" class="collapse" aria-labelledby="composer_freetext8_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_9" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text8']['text']) ? $report_content['free_text8']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break10" id="composer_page_break10" <?php echo ($report_content && $report_content['free_text8']['page_break10']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break10">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
            
                     <!-- Free Text 9 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext9_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text9" id="free_text9_check" <?php echo ($report_content && $report_content['free_text9']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_9" aria-expanded="true" aria-controls="free_text_9">
                                    <?php echo $trans->phrase("pdf_text11"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_9" class="collapse" aria-labelledby="composer_freetext9_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_10" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text9']['text']) ? $report_content['free_text9']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break11" id="composer_page_break11" <?php echo ($report_content && $report_content['free_text9']['page_break11']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break11">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                     <!-- Free Text 10 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext10_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text10" id="free_text10_check" <?php echo ($report_content && $report_content['free_text10']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_10" aria-expanded="true" aria-controls="free_text_10">
                                    <?php echo $trans->phrase("pdf_text11a"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_10" class="collapse" aria-labelledby="composer_freetext10_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_11" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text10']['text']) ? $report_content['free_text10']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break12" id="composer_page_break12" <?php echo ($report_content && $report_content['free_text10']['page_break12']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break12">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <!-- Free Text 11 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext11_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text11" id="free_text11_check" <?php echo ($report_content && $report_content['free_text11']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_11" aria-expanded="true" aria-controls="free_text_11">
                                    <?php echo $trans->phrase("pdf_text11b"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_11" class="collapse" aria-labelledby="composer_freetext11_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_12" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text11']['text']) ? $report_content['free_text11']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break13" id="composer_page_break13" <?php echo ($report_content && $report_content['free_text11']['page_break13']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break13">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <!-- Free Text 12 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext12_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text12" id="free_text12_check" <?php echo ($report_content && $report_content['free_text12']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_12" aria-expanded="true" aria-controls="free_text_12">
                                    <?php echo $trans->phrase("pdf_text11c"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_12" class="collapse" aria-labelledby="composer_freetext12_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_13" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text12']['text']) ? $report_content['free_text12']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break14" id="composer_page_break14" <?php echo ($report_content && $report_content['free_text12']['page_break14']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break14">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <!-- Free Text 13 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext13_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text13" id="free_text13_check" <?php echo ($report_content && $report_content['free_text13']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_13" aria-expanded="true" aria-controls="free_text_13">
                                    <?php echo $trans->phrase("pdf_text11d"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_13" class="collapse" aria-labelledby="composer_freetext12_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_14" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text13']['text']) ? $report_content['free_text13']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break15" id="composer_page_break15" <?php echo ($report_content && $report_content['free_text13']['page_break15']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break15">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Free Text 14 -->
                    <div class="card">
                        <div class="card-header" id="composer_freetext14_heading">
                            <div style="padding: 30px 0px 30px 15px;">
                                <input class="form-check-input section-checker" type="checkbox" value="free_text14" id="free_text14_check" <?php echo ($report_content && $report_content['free_text14']['enabled']) ? 'checked' : ''; ?>>
                                <h3 class="accordion-heading mb-0" data-toggle="collapse" data-target="#free_text_14" aria-expanded="true" aria-controls="free_text_14">
                                    <?php echo $trans->phrase("pdf_text12"); ?>
                                </h3>
                            </div>
                        </div>

                        <div id="free_text_14" class="collapse" aria-labelledby="composer_freetext14_heading" data-parent="#composer_accordion">
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="composer_text_15" class="form-control composer-text"><?php echo ($report_content && $report_content['free_text14']['text']) ? $report_content['free_text14']['text'] : ""; ?></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="page_break16" id="composer_page_break16" <?php echo ($report_content && $report_content['free_text14']['page_break16']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="composer_page_break16">
                                        <?php echo $trans->phrase('pdf_page_break'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <br>
                <button type="button" id="save_report_format_dcomposed" class="btn btn-success"><?php echo $trans->phrase('user_composer_phrase24'); ?></button>
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
