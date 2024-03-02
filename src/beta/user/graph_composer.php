<?php
if(isset($_GET['id'])):
    require_once('../database.php');
    $Database = new Database();
    $ticket = null;
    $question_response = null;
    if(isset($_GET['id'])){
        $ticket = $Database->get_data('ticket_id', $_GET['id'], 'ticket', true);
        if($ticket) $question_response = json_decode($ticket['ticket_response'], true);
    }
    if($ticket && ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')):
        echo "<input type='text' id='ticket_id' value='".$ticket['ticket_id']."' readonly hidden>";
        echo "<input type='text' id='lang_code' value='".$_SESSION['trans']."' readonly hidden>";

        $report = false;
        $report_content = false;
        $reports = $Database->get_multiple_data('ticket_id', $ticket['ticket_id'], 'report');
        if($reports){
            foreach($reports as $single_report){
                if($single_report['lang_code'] == $_SESSION['trans']){
                    $report = $single_report;
                }
            }
        }
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
    <div class="col-12 report-graph-composer">
        <div class="report-composer-widget-title">Graph Composer</div>
        <div class="row user-content-row">
            <div class="col-12">
                <div class="accordion" id="composer_accordion">
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
                                        <button type="button" id="draw_graph_1" class="btn btn-primary btn-sm"><?php echo $trans->phrase('user_composer_phrase27'); ?></button>
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
                                        <button type="button" id="draw_graph_2" class="btn btn-primary btn-sm"><?php echo $trans->phrase('user_composer_phrase27'); ?></button>
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
                    
     
                </div>
                <br>
                <button type="button" id="save_graph_report" class="btn btn-success"><?php echo $trans->phrase('user_composer_phrase24'); ?></button>
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
