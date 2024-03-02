<style>
    .select_report {
            border: 3px solid green;
    }
    
</style>
 <script src="<?php echo JQUERY; ?>"></script>

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
    if($ticket):
        //Update ticket views from follow-ups deadline
        //For ticket
        $sql = "UPDATE ticket_deadline SET viewed=1, emailed=1 WHERE ticket_id={$ticket['ticket_id']} AND end_date < CURDATE()";
        $update = $Database->get_connection()->prepare($sql);
        $update->execute();

        //For questions
        $sql = "UPDATE question_deadline SET viewed=1, emailed=1 WHERE ticket_id={$ticket['ticket_id']} AND end_date < CURDATE()";
        $update = $Database->get_connection()->prepare($sql);
        $update->execute();

        //Ticket company
        $ticket_company = $Database->get_data('company_id', $ticket['ticket_company_id'], 'company', true);
        //Ticket user
        $ticket_user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);

        //Getting user and setting up permission
        $user_permission = false; $company_permission = false; $admin_permission = false;
        $user_edit_permission = false;
        $user = null;
        if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'){
            $user = $Database->get_data('admin_id', $_SESSION['account-id'], 'admin', true);
            $admin_permission = true;
        }
        else if($_SESSION['account-type'] == 'company'){
            $user = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);
            if($ticket['ticket_company_id'] == $user['company_id'])
                $company_permission = true;
        }
        else if($_SESSION['account-type'] == 'consultant'){
            $user = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
            $consultant_permission = true;
        }
        elseif($_SESSION['account-type'] == 'user'){
            $user = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
            $user_company = $Database->get_data('company_id', $user['user_company_id'], 'company', true);
            if($user_company['company_show_tickets'] && $ticket['ticket_company_id'] == $user_company['company_id']){
                $user_permission = true;

            if($ticket['ticket_status'] == 'process' && $ticket['ticket_user_id'] == $user['user_id'])
                $user_edit_permission = true;
            }
            else if($ticket['ticket_user_id'] == $user['user_id']){
                $user_permission = true;
                if($ticket['ticket_status'] == 'process')
                    $user_edit_permission = true;
            }
        }


if($user_permission || $company_permission || $admin_permission || $consultant_permission):
?>
<!-- Page: Intro -->
<?php
    if(isset($_GET['page']) && $_GET['page'] == 'intro'):
?>


<div class="card">
    <div class="card-body p-3">
    <div class="row user-content-row">
        <div class="col-12">
            <form>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><?php echo $trans->phrase("user_ticket_phrase2"); ?></div>
                    </div>
                    <input type="text" id="ticket_name" value="<?php echo ($ticket) ? $ticket['ticket_name']: ''; ?>" class="form-control" disabled>
                </div>
            </form>
        </div>
    </div>
    <div class="row user-content-row">
        <div class="col-12">
        <?php echo $trans->phrase("user_ticket_phrase28"); ?>
        </div>
    </div>
    <div class="row user-content-row">
        <div class="col-12">
            <a href="<?php echo SITE_URL?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id']; ?>&page=question">
                <button class="btn btn-success btn-lg mb-3">
                    <?php echo $trans->phrase("user_ticket_phrase26"); ?>
                </button>
            </a>
        </div>
    </div>
    </div>
    </div>

<!-- Page: Question -->
<?php
    elseif(isset($_GET['page']) && $_GET['page'] == 'question'):
        if(isset($_GET['pageNum'])) {
            $pageNum = $_GET['pageNum'];
        }
        else {
            $pageNum = 0;
        }
        if(isset($_SESSION['ticket_request_id'])){
        $ticket_request_id =  $_SESSION['ticket_request_id'];
        $info = array(array('ticket_id', $_GET['id']));
        $Database->update_data($info, 'id', $ticket_request_id, 'tbl_report_request');
		
		

        }
		/*if(isset($_SESSION['ticket_narequest_id'])){
		$ticket_narequest_id =  $_SESSION['ticket_narequest_id'];	
		$info = array(array('ticket_id', $_GET['id']));
		$Database->update_data($info, 'id', $ticket_narequest_id, 'tbl_report_narequest');	
		}*/
		
?>


<div class="card">
    <div class="card-body p-3">
   <input type="hidden" name="pageNum" id="pageNum" value="<?php echo $pageNum; ?>"/>
    <div class="row user-content-row">
        <div class="col-12">
            <form>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <?php echo $trans->phrase("user_ticket_phrase1"); ?>
                        </div>
                    </div>
                    <input type="text" style="height: 44px;" id="ticket_name"
                        value="<?php echo ($ticket) ? $ticket['ticket_name']: ''; ?>"
                            class="form-control" <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                            
                            <?php
                 if($user_permission
                    && isset($ticket)
                    && $ticket['ticket_status']!='closed') {
                ?>
             
                <a class="btn btn-success" href="<?php echo SITE_URL ?>/user/index.php?route=ticketsummary&id=<?php echo $ticket['ticket_id']; ?>">
                    <i class="fas fa-edit"></i
                    ><?php echo $trans->phrase("user_js_phrase10"); ?>
                </a>
             
                <?php
                  }
                ?>
                </div>
                
            </form>
        </div>
    </div>

    <div class="row user-content-row">
        <div class="col-12">
           <?php  if($_SESSION['account-type'] == 'user'){ ?>
            <div class="ticket-information">
                <div class="row user-content-row">
                    <div class="col-12">
                        <form>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><?php echo $trans->phrase("user_ticket_invite_responder"); ?></div>
                                </div>
                                <input type="text" id="res" style="height: 44px;" placeholder = "<?php echo $trans->phrase("user_ticket_responder_placeholder"); ?>" class="form-control" <?php if(!$user_edit_permission) echo ('disabled') ?>>
                                <input type="hidden" id="res_ticket_id" value="<?php echo $_GET['id'] ?>" class="form-control" >
                                <div class="input-group-prepend">
                                   <button id="btn_res1" class="btn btn-success" <?php if(!$user_edit_permission) echo ('disabled') ?>>
                                         <?php echo $trans->phrase("user_ticket_invite_text"); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div style="display:none;" id="alertSectionsuccess1"><span id="alertSection" style="color: green;"><?php echo $trans->phrase("user_ticket_email_sucess"); ?></span></div>
                        <div style="display:none;text-align: center;font-size: 17px;" id="alertSectionsuccess2"></div>
                        <div style="display:none;" id="alertSectionempty1"><span id="alertSection" style="color: red;"><?php echo $trans->phrase("user_ticket_add_email"); ?></span></div>
                    </div>
                </div>
				       
                  <script type="text/javascript">
                        $(document).ready(function() {
                                //Create res1
                                let res_ticket_id = $('#res_ticket_id').val();
                                $('#btn_res1').click(function (event) {
                                    event.preventDefault();
                                       let email = $('#res').val();
                                       
                                       if(email != ''){
                                                $.ajax({
                                                    url: '<?php echo SITE_URL ?>/option_server.php',
                                                    type: 'POST',
                                                    data: {'sign': 'res_form', 'email': email , 'res_ticket_id': res_ticket_id}
                                                }).done(function (data) {
                                                    data = JSON.parse(data);
                                                    if (data['status'] == 'success') {
                                                        $('#alertSectionsuccess1').show();
                                                       // setTimeout(function() {$("#alertSectionsuccess1").hide();}, 3000);
                                                        
                                                        //$('#alertSectionsuccess2').html('<span id="alertSection" style="color: red;"> Copy this url to process : '+ data['url']  +'</span>');
                                                        //$('#alertSectionsuccess2').show();
                                                        //setTimeout(function() {$("#alertSectionsuccess1").hide();}, 3000);
                                                        
                                                    } else {
                                                    }
                                                })
                                       }else{
                                                $('#alertSectionempty1').show();
                                                setTimeout(function() {$("#alertSectionempty1").hide();}, 3000);
                                       }
                                });
                        });
                </script>
            </div>
            <?php } ?>
			
			
			 <?php 
       if($_SESSION['account-type'] == 'user' || $_SESSION['account-type'] == 'super_admin'){   
        $res_ticket_1id = $_GET['id'];
        $respond_tickets = $Database->get_multiple_data('ticket_id',$res_ticket_1id,'tbl_ticket_responder', '=', true, 'responder_id ASC', false);
        if ($respond_tickets) { ?>
        <div> Responders </div>
        <?php 
        $respond_count = 1;    
        foreach ($respond_tickets as $respond_ticket) {
            
        $respond_ticket_data = $Database->get_data('responder_id', $respond_ticket['responder_id'], 'responder_ticket_data', true);
        ?>
        <div>
            <div> <?php echo "Responder " . $respond_count; ?></div>
			<!--<a class="btn btn-info btn-sm" target="_blank" href="<?php echo SITE_URL ?>/custom.php?route=res_question&page=pen_responder&res_id=<?php echo $respond_ticket_data['responder_ticket_data_id']; ?>"> Go to view </a>-->
        </div>
		
		<?php 

							$question_response_test = json_decode($respond_ticket_data['ticket_response'], true);

                            $questions = $Database->get_multiple_data(false, false, 'question_res');

                            $count = 0;
                            $countNotFollowUpQuestion = 0;
                            $questionIds_test = "";
                            $answerIds_test = "";
                            $unAnswerIds = "";
                            foreach($questions as $question):
                                $count++;
                                if($question['question_follow_up']==0) {
                                    $countNotFollowUpQuestion++;
                                    if($countNotFollowUpQuestion==1) {
                                        $questionIds_test = $question['question_res_id'];
                                    }
                                    else {
                                        $questionIds_test = $questionIds_test.",".$question['question_res_id'];
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

                                //Question response
                                $yes_check = false;
                                $no_check = false;
                                $check_1 = false;
                                $check_2 = false;
                                $check_3 = false;
                                $check_4 = false;
                                $check_5 = false;
                                $check_6 = false;

                                if(isset($question_response_test[$question['question_res_id']])){
                                    if($question['question_type'] == 'yes-no'){
                                        if($question_response_test[$question['question_res_id']]['answer'] == 2) {
                                            $yes_check = true;
                                        }
                                        else if($question_response_test[$question['question_res_id']]['answer'] == 1) {
                                            $no_check = true;
                                        }
                                    }
                                    if($question['question_type'] == 'mcq'){
                                        if($question_response_test[$question['question_res_id']]['answer'] == 1) $check_1 = true;
                                        else if($question_response_test[$question['question_res_id']]['answer'] == 2) $check_2 = true;
                                        else if($question_response_test[$question['question_res_id']]['answer'] == 3) $check_3 = true;
                                        else if($question_response_test[$question['question_res_id']]['answer'] == 4) $check_4 = true;
                                        else if($question_response_test[$question['question_res_id']]['answer'] == 5) $check_5 = true;
                                        else if($question_response_test[$question['question_res_id']]['answer'] == 6) $check_6 = true;
                                    }
                                }

                                if($question['question_follow_up']==0)
                                {
                                    if($yes_check
                                        ||$no_check
                                        ||$check_1
                                        ||$check_2
                                        ||$check_3
                                        ||$check_4
                                        ||$check_5
                                        ||$check_6) {    
                                        if(strlen($answerIds_test)<=0) {
                                            $answerIds_test = $question['question_res_id'];
                                        }
                                        else {
                                            $answerIds_test = $answerIds_test.",".$question['question_res_id'];
                                        }
                                    }
                                    else {
                                        if(strlen($unAnswerIds)<=0) {
                                            $unAnswerIds = $question['question_res_id'];
                                        }
                                        else {
                                            $unAnswerIds = $unAnswerIds.",".$question['question_res_id'];
                                        }
                                    }
                                }						   
                            endforeach;
		?>		
		 <div>
            <div> <?php echo $trans->phrase("user_ticket_phrase55"); ?></div>
            <div class="progress" id="progres_check_<?php echo $respond_count; ?>">
                <div id="progressbar_<?php echo $respond_count; ?>" class="progress-bar bg-success"
                    role="progressbar"
                    aria-valuenow="70"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width:0%">
                    <span id="label-progressbar_<?php echo $respond_count; ?>"></span>
                </div>
            </div>
        </div>
		
	   <input name="" id="questionIds_<?php echo $respond_count; ?>" type="hidden"
					value="<?php echo $questionIds_test; ?>">
	   <input name="" id="answerIds_<?php echo $respond_count; ?>" type="hidden"
					value="<?php echo $answerIds_test; ?>">
					
		<script>
    let progressbar_test_<?php echo $respond_count; ?> = $('#progressbar_<?php echo $respond_count; ?>');
    let questionIds_test_<?php echo $respond_count; ?> = $('#questionIds_<?php echo $respond_count; ?>').val();
    let answerIds_test_<?php echo $respond_count; ?> = $('#answerIds_<?php echo $respond_count; ?>').val();
    let questionNo_test_<?php echo $respond_count; ?> = <?php echo $respond_ticket_data['responder_ticket_data_id']; ?>;

    let numberOfQuestion_test_<?php echo $respond_count; ?> = 0;
    let numberOfAnswer_test_<?php echo $respond_count; ?> = 0;
    let percent_test_<?php echo $respond_count; ?> = 0;

    if (questionIds_test_<?php echo $respond_count; ?>.length >= 1) {
        var questionIdArr_test_<?php echo $respond_count; ?> = questionIds_test_<?php echo $respond_count; ?>.split(',');
        numberOfQuestion_test_<?php echo $respond_count; ?> = questionIdArr_test_<?php echo $respond_count; ?>.length;
    }

    if (answerIds_test_<?php echo $respond_count; ?>.length >= 1) {
        var totalAnswerIdsArr_test_<?php echo $respond_count; ?> = answerIds_test_<?php echo $respond_count; ?>.split(',');
        numberOfAnswer_test_<?php echo $respond_count; ?> = totalAnswerIdsArr_test_<?php echo $respond_count; ?>.length;

        if (questionNo_test_<?php echo $respond_count; ?> != 0) {
            if (!totalAnswerIdsArr_test_<?php echo $respond_count; ?>.includes(questionNo_test_<?php echo $respond_count; ?>)) {
                numberOfAnswer_test_<?php echo $respond_count; ?> = numberOfAnswer_test_<?php echo $respond_count; ?>;
                answerIds_test_<?php echo $respond_count; ?> = answerIds_test_<?php echo $respond_count; ?> + "," + questionNo_test_<?php echo $respond_count; ?>;
                $('#answerIds_<?php echo $respond_count; ?>').val(answerIds_test_<?php echo $respond_count; ?>);
            }
        }
    } else {
        if (questionNo_test_<?php echo $respond_count; ?> != 0) {
            $('#answerIds_<?php echo $respond_count; ?>').val(questionNo_test_<?php echo $respond_count; ?>);
            numberOfAnswer_test_<?php echo $respond_count; ?> = numberOfAnswer_test_<?php echo $respond_count; ?>;
        }
    }

    percent_test_<?php echo $respond_count; ?> = Math.floor((numberOfAnswer_test_<?php echo $respond_count; ?> * 100) / numberOfQuestion_test_<?php echo $respond_count; ?>);
    progressbar_test_<?php echo $respond_count; ?>.css({
        "width": percent_test_<?php echo $respond_count; ?> + "%"
    });

    $('#label-progressbar_<?php echo $respond_count; ?>').html(percent_test_<?php echo $respond_count; ?> + '% (' + numberOfAnswer_test_<?php echo $respond_count; ?> + '/' + numberOfQuestion_test_<?php echo $respond_count; ?> + ')');
	    </script>		
        <?php 
        $respond_count++;
        }
        }
        }
		?>
			
            <div class="ticket-information">
                <div class="row">
                    <div class="col-6">
                        <div class="ticket-information-group">
                            <label class="ticket-information-title"><?php echo $trans->phrase("user_ticket_phrase2"); ?> </label>
                            <?php echo $ticket['ticket_id']; ?>
                        </div>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title"><?php echo $trans->phrase("user_ticket_phrase3"); ?> </label>
                            <?php if($ticket['ticket_status'] == 'process'): ?>
                                <button class="btn btn-warning btn-sm">
                                    <?php echo $trans->phrase("user_ticket_phrase4"); ?>
                                </button>
                            <?php elseif($ticket['ticket_status'] == 'closed'): ?>
                                <button class="btn btn-secondary btn-sm">
                                    <?php echo $trans->phrase("user_ticket_phrase5"); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title">
                                <?php echo $trans->phrase("text_open_date"); ?> :
                            </label>
                            <?php echo date("Y-m-d", strtotime($ticket['ticket_time'])); ?>
                        </div>
                        <?php
                          if($ticket['ticket_status']=='closed') {
                        ?>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title">
                                <?php echo $trans->phrase("text_close_date"); ?> :
                            </label>
                            <?php
                               if(isset($ticket['ticket_close_time'])) {
                                    echo date("Y-m-d", strtotime($ticket['ticket_close_time']));
                               }
                             ?>
                        </div>

                        <?php
                            }
                            //Restricting report generate action to admins only. Changed applied to pdf_report.php also for security reason.
                            //Report is activated based request.
                            if($_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'super_admin'):
                        ?>
                        <div class="ticket-information-group">

                            <?php
                            $report = false;
                            $reports = $Database->get_multiple_data('ticket_id', $ticket['ticket_id'], 'report');
                            if($reports){
                                foreach($reports as $single_report){
                                    if($single_report['lang_code'] == $_SESSION['trans']){
                                        $report = $single_report;
                                        break;
                                    }
                                }
                            }
                            if(!$report
                                && ($_SESSION['account-type'] == 'support_admin'
                                        || $_SESSION['account-type'] == 'super_admin')
                                && $ticket['ticket_status'] == 'closed') {
                            ?>
                                <label class="ticket-information-title">
                                    <?php echo $trans->phrase("user_ticket_phrase21"); ?>
                                </label>
                            <?php
                            }
                        
                            if(isset($_SESSION['report-format-id'])){    
                            $report_format_id =  $_SESSION['report-format-id'];
                            }else{
                            $req_info = $Database->get_data('ticket_id', $_GET['id'], 'tbl_report_request', true);
                            if($req_info) {
                                $report_id = $req_info['report_id'];
                                $report_format_id = $report_id;
                                $_SESSION['report-format-id'] = $report_format_id;
                            }else{
                                $report_format_id = 0;
                            }
                            }
                            $report_formats = $Database->get_multiple_data('report_lang_code', $_SESSION['trans'], 'mlreport_format_content');
                            
                            $sql = "SELECT * FROM mlreport_format_content WHERE report_format_id={$report_format_id} AND report_lang_code='{$_SESSION['trans']}'";

                            $report = $Database->get_connection()->prepare($sql);
                    
                            $report->execute();
                            
                            if ($report->rowCount() < 1) $report = false;
                    
                            else $report = $report->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <label>Select report type</label>
                            <select class="form-control" id="report_format">
                                <option value="0">Please select</option>
                                <?php foreach($report_formats as $report_format){ ?>
                                  <?php if($report_format['report_format_id'] == $report_format_id ) { ?>
                                  <option value="<?php echo $report_format['report_format_id']; ?>" selected="selected"><?php echo $report_format['report_title']; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $report_format['report_format_id']; ?>"><?php echo $report_format['report_title']; ?></option>
                                  <?php } ?>
                                <?php } ?>
                            </select>
                            <br>
                            <input type="button" value="Update report format" class="btn btn-info"  id="add_report_format"/><br><br>
                            <input type="hidden" value="<?php echo $_GET['id']; ?>" id="report_ticket_id"/>
                            

                        
                            <label class="ticket-information-title"><?php echo $trans->phrase("user_ticket_phrase21"); ?> </label>
                            <?php if($report_format_id == $report['report_format_id']) {  ?>
                            <a href="<?php echo SITE_URL ?>/report_types/mlc_pdf_report_<?php echo str_replace(' ', '%20', $report['report_title']); ?>.php?id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-download"></i>
                                <?php echo $trans->phrase('user_ticket_phrase22'); ?>
                            </a>
                            <?php } else  { ?>
							
                             <!--<a href="<?php echo SITE_URL ?>/pdf_report_new.php?id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-download"></i>
                                <?php echo $trans->phrase('user_ticket_phrase22'); ?>
                            </a>-->
                            <?php } ?>
							
                             <!--<a href="<?php echo SITE_URL ?>/pdf_report_new.php?id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-download"></i>
                                Orignal download file
                            </a>-->
                            
                            <!-- Report composer, only for admins -->
                            <?php if($_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'super_admin'): ?>
                            <!--<a href="<?php echo SITE_URL ?>/user/index.php?route=graph_composer&id=<?php echo $ticket['ticket_id']; ?>" target="_blank" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-pen-alt"></i>
                                Graph Composer
                            </a>-->
                            

						
						
						<!--<button data-bs-toggle="modal" onclick="return false;" data-bs-target="#newgraphModal" class="btn btn-primary">New graph </button>
    
                        <div class="modal fade" id="newgraphModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    	  <div class="modal-dialog modal-xl">
                    		<div class="modal-content">
                    		  <div class="modal-header">
                    			<h5 class="modal-title text-center" id="exampleModalLabel"><?php echo $trans->phrase('graph_text'); ?></h5>
                    			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    		  </div>
                    		  <div class="modal-body" id="">
                                               <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        In Highcharts, pies can also be hollow, in which case they are commonly
        referred to as donut charts. This pie also has an inner chart, resulting
        in a hierarchical type of visualization. 
    </p>
</figure>

<script>
let questionIdData = JSON.parse($('#questionIdData').val());

var colors = Highcharts.getOptions().colors,
    categories = [
        'Chrome'
    ],
    data = [
        {
            y: 61.04,
            color: colors[2],
            drilldown: {
                name: 'Chrome',
                categories: [
                    'Chrome v97.0',
                    'Chrome v96.0',
                    'Chrome v95.0',
                    'Chrome v94.0',
                    'Chrome v93.0',
                    'Chrome v92.0',
                    'Chrome v91.0',
                    'Chrome v90.0',
                    'Chrome v89.0',
                    'Chrome v88.0',
                    'Chrome v87.0',
                    'Chrome v86.0',
                    'Chrome v85.0',
                    'Chrome v84.0',
                    'Chrome v83.0',
                    'Chrome v81.0',
                    'Chrome v89.0',
                    'Chrome v79.0',
                    'Chrome v78.0',
                    'Chrome v76.0',
                    'Chrome v75.0',
                    'Chrome v72.0',
                    'Chrome v70.0',
                    'Chrome v69.0',
                    'Chrome v56.0',
                    'Chrome v49.0'
                ],
                data: [
                    36.89,
                    18.16,
                    0.54,
                    0.7,
                    0.8,
                    0.41,
                    0.31,
                    0.13,
                    0.14,
                    0.1,
                    0.35,
                    0.17,
                    0.18,
                    0.17,
                    0.21,
                    0.1,
                    0.16,
                    0.43,
                    0.11,
                    0.16,
                    0.15,
                    0.14,
                    0.11,
                    0.13,
                    0.12
                ]
            }
        }
    ],
    browserData = [],
    versionsData = [],
    i,
    j,
    dataLen = data.length,
    drillDataLen,
    brightness;


// Build the data arrays
for (i = 0; i < dataLen; i += 1) {

    // add browser data
    browserData.push({
        name: categories[i],
        y: data[i].y,
        color: data[i].color
    });

    // add version data
    drillDataLen = data[i].drilldown.data.length;
    for (j = 0; j < drillDataLen; j += 1) {
        brightness = 0.2 - (j / drillDataLen) / 5;
        versionsData.push({
            name: data[i].drilldown.categories[j],
            y: data[i].drilldown.data[j],
            color: Highcharts.color(data[i].color).brighten(brightness).get()
        });
    }
}

// Create the chart
Highcharts.chart('container', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Browser market share, January, 2022',
        align: 'left'
    },
    subtitle: {
        text: 'Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>',
        align: 'left'
    },
    plotOptions: {
        pie: {
            shadow: false,
            center: ['50%', '50%']
        }
    },
    tooltip: {
        valueSuffix: '%'
    },
    series: [{
        name: 'Browsers',
        data: browserData,
        size: '60%',
        dataLabels: {
            formatter: function () {
                return this.y > 5 ? this.point.name : null;
            },
            color: '#ffffff',
            distance: -30
        }
    }, {
        name: 'Versions',
        data: versionsData,
        size: '80%',
        innerSize: '60%',
        dataLabels: {
            formatter: function () {
                // display only if larger than 1
                return this.y > 1 ? '<b>' + this.point.name + ':</b> ' +
                    this.y + '%' : null;
            }
        },
        id: 'versions'
    }],
    responsive: {
        rules: [{
            condition: {
                maxWidth: 400
            },
            chartOptions: {
                series: [{
                }, {
                    id: 'versions',
                    dataLabels: {
                        enabled: false
                    }
                }]
            }
        }]
    }
});

</script>
                    		  </div>
                    		</div>
                    	  </div>
                    	</div>-->
                            
                            <?php endif; ?>
                            <?php if(($_SESSION['account-type'] == 'support_admin' || $_SESSION['account-type'] == 'super_admin') && $ticket['ticket_status'] == 'closed'): ?>
                            <!--<a href="<?php echo SITE_URL ?>/user/index.php?route=report_composer&id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-pen-alt"></i>
                                <?php echo $trans->phrase('user_ticket_phrase54'); ?>
                            </a>-->
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
						
						<a href="<?php echo SITE_URL ?>/report_types/pdf_report_main.php?id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-download"></i>
                               Main report download
                            </a>
							
							<a href="<?php echo SITE_URL ?>/user/index.php?route=mreport_common_composer&id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-pen-alt"></i>
                                Common report composer - Testing on
                            </a>
							
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
							
							
							                        <button data-bs-toggle="modal" onclick="return false;" data-bs-target="#graphModal" class="btn btn-primary"><?php echo $trans->phrase('add_graph'); ?> </button>
    
                        <div class="modal fade" id="graphModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    	  <div class="modal-dialog modal-xl">
                    		<div class="modal-content">
                    		  <div class="modal-header">
                    			<h5 class="modal-title text-center" id="exampleModalLabel"><?php echo $trans->phrase('graph_text'); ?></h5>
                    			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    		  </div>
                    		  <div class="modal-body" id="">
                    		      <div class="card text-center mb-2">
                                    <div class="card-body radar-graph" id="radar_graph_1">
                                        <h4 class="text-center" id="success_message"></h4><br>    
                                        <?php foreach($categories as $category): ?>
                                        <div class="form-check form-check-inline">
                                            <?php //$checked = ($category['category_id'] == 1 || $category['category_id'] == 9 || $category['category_id'] == 11 || $category['category_id'] == 13 || $category['category_id'] == 14) ? "checked": ""; ?>
                                            <input class="form-check-input graph-label" type="checkbox" id="radar1_category_<?php echo $category['category_id']; ?>" data-category="<?php echo $category['category_id']; ?>" value="<?php echo $category['category_name']; ?>" checked>
                                            <label class="form-check-label" for="radar1_category_<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                        <br><br>
                                        <?php 
                                        if(!isset($_SESSION['trans'])){
                                            $Database = new Database();
                                            $default_language = $Database->get_data('lang_default', 1, 'language', true);
                                            if($default_language){
                                                $_SESSION['trans'] = $default_language['lang_code'];
                                            }
                                            else{
                                                $_SESSION['trans'] = 'en';
                                            }
                                        }
                                        ?>
                                        <input type="hidden" value="<?php echo $_GET['id'] ?>" id="ticket_id_data" />
                                        <input type="hidden" value="<?php echo $_SESSION['trans'] ?>" id="lang_code" />
                                        <button type="button" id="draw_graph_1" class="btn btn-primary btn-sm"><?php echo $trans->phrase('user_composer_phrase27'); ?></button>
                                        <button type="button" id="save_graph_report" class="btn btn-primary btn-sm">Save graph</button>
                                    </div>
                                </div>
                    		  </div>
                    		</div>
                    	  </div>
                    	</div>

                        <div class="ticket-information-group">
                            <label class="ticket-information-title"><?php echo $trans->phrase("user_ticket_phrase7"); ?> </label>
                            <?php
                            $ticket_deadline = $Database->get_data('ticket_id', $ticket['ticket_id'], 'ticket_deadline', true);
                            if($ticket_deadline):
                                echo $ticket_deadline['end_date'];
                            else:
                                echo $trans->phrase('user_questions_phrase25');
                            endif;
                            if(($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'consultant') &&
                                $ticket['ticket_status'] == 'process'):
                            ?>
                                <button id="ticket_deadline_update" class="btn btn-success btn-sm"
                                    data-ticket_id="<?php echo $ticket['ticket_id']; ?>"
                                    data-end_date="<?php echo $ticket_deadline['end_date']; ?>"
                                    data-summary="<?php echo $ticket_deadline['summary']; ?>"
                                    data-description="<?php echo $ticket_deadline['description']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php
                                //Show calender event reminder if user authenticated with calendar
                                if($ticket_deadline && $ticket_user['google_auth_code']):
                                ?>
                                <button class="btn btn-info btn-sm calendar_event_reminder"
                                    title="<?php echo $trans->phrase('user_ticket_phrase18'); ?>"
                                    data-ticket_id="<?php echo $ticket['ticket_id']; ?>"
                                    data-end_date="<?php echo $ticket_deadline['end_date']; ?>"
                                    data-summary="<?php echo $ticket_deadline['summary']; ?>"
                                    data-description="<?php echo $ticket_deadline['description']; ?>">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                        if($_SESSION['account-type'] == 'super_admin' && $ticket['ticket_status'] == 'closed'):
                        ?>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title"><?php echo $trans->phrase("user_ticket_phrase19"); ?> </label>
                            <button type="button" id="send_report_email" data-user_email="<?php echo $ticket_user['user_email']; ?>" data-ticket_id="<?php echo $ticket['ticket_id']; ?>" class="btn btn-success btn-sm"><?php echo $trans->phrase('user_ticket_phrase20'); ?></button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <div class="ticket-information-group">
                            <div class="ticket-information-logo">
                                <?php
                                $logo_image = SITE_URL.'/images/default-company.png';
                                if(file_exists('../images/company_logo/'.$ticket_company['company_id'].'.png')):
                                    $logo_image = '/images/company_logo/'.$ticket_company['company_id'].'.png';
                                elseif(file_exists('../images/company_logo/'.$ticket_company['company_id'].'.jpg')):
                                    $logo_image = '/images/company_logo/'.$ticket_company['company_id'].'.jpg';
                                elseif(file_exists('../images/company_logo/'.$ticket_company['company_id'].'.jpeg')):
                                    $logo_image = '/images/company_logo/'.$ticket_company['company_id'].'.jpeg';
                                endif;
                                ?>
                                <img src="<?php echo $logo_image; ?>" alt="Company logo">
                            </div>
                        </div>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title" style="width:100px"><?php echo $trans->phrase("user_ticket_phrase8"); ?> </label>
                            <span><?php echo $ticket_company['company_name'];?></span>
                        </div>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title" style="width:100px"><?php echo $trans->phrase("user_ticket_phrase9"); ?> </label>
                            <?php $user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true); ?>
                            <span><?php echo $user['user_name']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if($_GET['page'] == 'question') { ?>
        <div>
            <div> <?php echo $trans->phrase("user_ticket_phrase55"); ?></div>
            <div class="progress">
                <div id="progressbar" class="progress-bar bg-success"
                    role="progressbar"
                    aria-valuenow="70"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width:0%">
                    <span id="label-progressbar"></span>
                </div>
            </div>
        </div>
        
	<?php } ?>

    <div style="margin:10px;width:100%;text-align:right;">
        <?php
            if($user_permission
                && (isset($ticket) && $ticket['ticket_status']!='closed')) {
            ?>
            <a role="button" href="#" style="margin-right:20px;"
                onclick="unAnseredQuestionFocus()">
                <?php echo $trans->phrase('text_unansered_qeustion'); ?>
            </a>
        <?php
            }
        ?>
    </div>

    <div>
        <?php echo $trans->phrase('pdf_report_phrase2')?>
        <span  id="pageNumber"></span>
        <?php echo $trans->phrase('text_of')?>
        <span id="totalPages"></span>
    </div>

    <div class="row user-content-row">
        <div class="col-12">
            <div class="table-fixed-header">
                <table class="table table-bordered question-table">
                    <thead>
                        <tr>
                            <th  class="quesiton_id" width="1%"><?php echo $trans->phrase("user_ticket_phrase10"); ?></th>
                            <th  class="quesiton" width="60%"><?php echo $trans->phrase("user_ticket_phrase11"); ?></th>
                            <th  class="answer" colspan="5" style="text-align:center;"><?php echo $trans->phrase("user_ticket_phrase27"); ?></th>

                        </tr>
                    </thead>
                    <tbody class="question-table-data">
                        <?php
                        //Getting deadline for questions
                        $question_deadline = $Database->get_multiple_data('ticket_id', $ticket['ticket_id'], 'question_deadline');
                        //Getting questions
                        $questions = $Database->get_multiple_data(false, false, 'question');
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

                                
                                echo '<script>console.log('.json_encode($available_categories).')</script>';

                                echo "<textarea type='text' id='available_categories' value='".json_encode($available_categories)."' hidden />";
                            }

                            $count = 0;
                            $countNotFollowUpQuestion = 0;
                            $questionIds = "";
                            $answerIds = "";
                            $unAnswerIds = "";
                            foreach($questions as $question):
                                //Skip new question for closed ticket
                                if($ticket['ticket_status'] == 'closed'
                                    && !isset($question_response[$question['question_id']])){
                                    continue;
                                }

                                //Increment question number
                                $count++;
                                if($question['question_follow_up']==0) {
                                    $countNotFollowUpQuestion++;
                                    if($countNotFollowUpQuestion==1) {
                                        $questionIds = $question['question_id'];
                                    }
                                    else {
                                        $questionIds = $questionIds.",".$question['question_id'];
                                    }
                                }

                                //Getting question data
                                $sql = "SELECT * FROM question_content WHERE question_id={$question['question_id']} AND lang_code='{$_SESSION['trans']}'";
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
                                        if($deadline['question_id'] == $question['question_id']){
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

                                if(isset($question_response[$question['question_id']])){
                                    if($question['question_type'] == 'yes-no'){
                                        if($question_response[$question['question_id']]['answer'] == 2) {
                                            $yes_check = true;
                                        }
                                        else if($question_response[$question['question_id']]['answer'] == 1) {
                                            $no_check = true;
                                        }
                                    }
                                    if($question['question_type'] == 'mcq'){
                                        if($question_response[$question['question_id']]['answer'] == 1) $check_1 = true;
                                        else if($question_response[$question['question_id']]['answer'] == 2) $check_2 = true;
                                        else if($question_response[$question['question_id']]['answer'] == 3) $check_3 = true;
                                        else if($question_response[$question['question_id']]['answer'] == 4) $check_4 = true;
                                        else if($question_response[$question['question_id']]['answer'] == 5) $check_5 = true;
                                         else if($question_response[$question['question_id']]['answer'] == 6) $check_6 = true;
                                    }
                                }

                                if($question['question_follow_up']==0)
                                {
                                    if($yes_check
                                        ||$no_check
                                        ||$check_1
                                        ||$check_2
                                        ||$check_3
                                        ||$check_4
                                        ||$check_5 
                                        ||$check_6) {
                                        if(strlen($answerIds)<=0) {
                                            $answerIds = $question['question_id'];
                                        }
                                        else {
                                            $answerIds = $answerIds.",".$question['question_id'];
                                        }
                                    }
                                    else {
                                        if(strlen($unAnswerIds)<=0) {
                                            $unAnswerIds = $question['question_id'];
                                        }
                                        else {
                                            $unAnswerIds = $unAnswerIds.",".$question['question_id'];
                                        }
                                    }
                                }

                         if(isset($question_response[$question['question_id']]['notes']))
                         {
                            $notes = $question_response[$question['question_id']]['notes'];
                         }
                         else {
                            $notes = "";
                         }
                        ?>
                        <tr
                        id="question-<?php echo $question['question_id']; ?>"
                        class="question-row <?php echo ($question['question_follow_up']) ? 'follow-up': ''; ?>"
                        data-question_id="<?php echo $question['question_id']; ?>"
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
                                  <input type="text" id="txtnotes<?php echo $question['question_id']; ?>"
                                    name="txtnotes<?php echo $question['question_id']; ?>"
                                    value="<?php echo $notes; ?>"
                                    style="width: 100%;"
                                    placeholder="<?php echo $trans->phrase('user_ticket_phrase58'); ?>"
                                    />
                                 </div>
                                <div class="btn-group dropright tb-drop">
                                    <select class="form-select" <?php if($user_edit_permission) echo ('onchange="setProgBar('.$question['question_id'].');"'); ?> >
                                        <?php if($question['question_type'] == 'mcq'):?>
                                            <option value="0"><?php echo $trans->phrase('please_select_ticket'); ?></option>
											<option value="1" <?php if($check_1) echo("selected"); ?> <?php if(!$user_edit_permission) echo ('disabled') ?> ><?php echo $question_data['question_option1']; ?></option>
                                            <option value="2" <?php if($check_2) echo("selected"); ?> <?php if(!$user_edit_permissionn) echo ('disabled') ?> ><?php echo $question_data['question_option2']; ?></option>
                                            <option value="3" <?php if($check_3) echo("selected"); ?> <?php if(!$user_edit_permission) echo ('disabled') ?> ><?php echo $question_data['question_option3']; ?></option>
                                            <option value="4" <?php if($check_4) echo("selected"); ?> <?php if(!$user_edit_permission) echo ('disabled') ?> ><?php echo $question_data['question_option4']; ?></option>
                                            <option value="5" <?php if($check_5) echo("selected"); ?> <?php if(!$user_permission) echo ('disabled') ?> ><?php echo $question_data['question_option5']; ?></option>
                                            <option value="6" <?php if($check_6) echo("selected"); ?> <?php if(!$user_permission) echo ('disabled') ?> ><?php echo $question_data['question_option6']; ?></option>
                                        <?php elseif($question['question_type']=='yes-no'): ?>
                                            <option value="0"><?php echo $trans->phrase('please_select_ticket'); ?></option>
											<option value="1" <?php if($yes_check) echo("selected"); ?> <?php if(!$user_edit_permission) echo ('disabled') ?> ><?php echo $trans->phrase("user_ticket_phrase12"); ?></option>
                                            <option value="1" <?php if($no_check) echo("selected"); ?> <?php if(!$user_edit_permission) echo ('disabled') ?> ><?php echo $trans->phrase("user_ticket_phrase13"); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </td>
                            <?php
                            if($question['question_type'] == 'mcq'):
                            ?>
                             <td class="checkbox-td">
                                <div class="form-check">
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input mcq-check check_1"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip="no"
                                        data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>"
                                        <?php echo ($check_1)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>
                                        >
                                    <label class="form-check-label">
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
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input mcq-check check_2"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip="no"
                                        data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>"
                                        <?php echo ($check_2)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input mcq-check check_3"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip=""
                                        data-tip_enabled="0" <?php echo ($check_3)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input mcq-check check_4"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip="yes"
                                        data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                        <?php echo ($check_4)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input mcq-check check_5"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip="yes"
                                        data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                        <?php echo ($check_5)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input mcq-check check_6"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip="yes"
                                        data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                        <?php echo ($check_6)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                            <td class="checkbox-td" colspan="2" style="text-align:center;">
                                <div class="form-check">
                                    <input type="radio"
                                        name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input yes-check"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip_enabled="<?php echo $question['question_tip_on_yes']; ?>"
                                        <?php echo ($yes_check)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                                    <input type="radio" name="check_<?php echo $question['question_id']; ?>"
                                        class="form-check-input no-check"
                                        <?php if($question['question_follow_up']==0) { ?>
                                            onclick="setProgBar('<?php echo $question['question_id']; ?>')"
                                        <?php } ?>
                                        data-tip_enabled="<?php echo $question['question_tip_on_no']; ?>"
                                        <?php echo ($no_check)? 'checked': ''; ?>
                                        <?php echo (!$user_edit_permission) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label">
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
                            <!--<td>
                                <?php
                               /* if($q_deadline):
                                    echo $q_deadline['end_date'];
                                else:
                                    echo $trans->phrase('user_questions_phrase25');
                                endif;
                                if(($_SESSION['account-type'] == 'super_admin') &&
                                    $ticket['ticket_status'] == 'process'):    */
                                ?>
                                    <button class="btn btn-success btn-sm question-deadline-update"
                                        data-ticket_id="<?php //echo $ticket['ticket_id']; ?>"
                                        data-question_id="<?php //echo $question['question_id']; ?>"
                                        data-end_date="<?php //echo $q_deadline['end_date']; ?>"
                                        data-summary="<?php //echo $q_deadline['summary']; ?>"
                                        data-description="<?php //echo $q_deadline['description']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php
                                    //Show calender event reminder if user authenticated with calendar
                                   // if($q_deadline && $ticket_user['google_auth_code']):
                                    ?>
                                    <button class="btn btn-info btn-sm calendar_event_reminder"
                                        title="<?php// echo $trans->phrase('user_ticket_phrase18'); ?>"
                                        data-ticket_id="<?php //echo $ticket['ticket_id']; ?>"
                                        data-end_date="<?php// echo $q_deadline['end_date']; ?>"
                                        data-summary="<?php //echo $q_deadline['summary']; ?>"
                                        data-description="<?php //echo $q_deadline['description']; ?>">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                    <?php //endif; ?>
                                <?php //endif; ?>
                            </td> -->
                        </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
                <input name="questionIds" id="questionIds" type="hidden"
                        value="<?php echo $questionIds; ?>">
				<input name="questionIdData" id="questionIdData" type="hidden"
                        value="<?php echo json_encode($questionIds); ?>">
                <input name="answersIds" id="answerIds" type="hidden"
                        value="<?php echo $answerIds; ?>">

                <div class="text-center pb-3 pt-3">
                    <div class="d-inline" style="text-align: center;">
                        <button  onclick="nextCategory()" class="btn btn-info btn-sm table-page-prev" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                    <div class="d-inline" style="text-align: center;">
                        <!--<button class="btn btn-info btn-sm table-page-number" 
                                style="pointer-events: none;width:60%;">1</button>-->
                                 <button data-bs-toggle="modal" data-bs-target="#exampleModalPen" class="btn btn-info btn-sm table-page-number" 
                                style="width:60%;">1</button>
                    </div>
                    <div class="d-inline" style="text-align: center;">
                        <button id="btnNextQgroup" onclick="nextCategory()" class="btn btn-info btn-sm table-page-next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $response = $ticket['ticket_response'];
    $answer_count = 0;
    if($response) {
        $response_array = json_decode($response, true);
        foreach($response_array as $q_id => $resp) {
            if(isset($resp['answer'])
                && $resp['answer']
                && !$resp['follow-up']) {
                $answer_count++;
            }
        }
    }

    ?>
	<div class="modal fade" id="exampleModalPen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title text-center" id="exampleModalLabel">Category details</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body" id="category_text_custom">
			
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
			<?php
			foreach($available_categories as $catagory) { ?>
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
								if(in_array($question['question_id'], $unAnswerIdArr)) {
									$tableRowId = 'question-'.$question['question_id'];
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
			<?php
			}
		?>
    </table>

        <div class="w3-button-holder">
            <button id="close-unanseredCatQues" class="btn btn-info">
                <?php echo $trans->phrase('text_close');?>
            </button>
        </div>
        </div>
	</div>
 </div>

    <div style="width:100%;text-align:center;">
        <?php if($user_edit_permission): ?>
        <button id="save_ticket"
            class="btn btn-success mb-3 ml-3"
            data-ticket_id="<?php echo ($ticket) ? $ticket['ticket_id']: ''; ?>">
            <?php echo $trans->phrase("user_ticket_phrase15"); ?>
        </button>
        <?php
            //hide close button until all question not completed.
            if($answer_count >= $countNotFollowUpQuestion) {
        ?>
        <button id="submit_ticket" class="btn btn-info  mb-3 ml-3"
            data-ticket_id="<?php echo ($ticket) ? $ticket['ticket_id']: ''; ?>">
            <?php echo $trans->phrase("user_ticket_phrase16"); ?>
        </button>
        <?php
          }
        ?>
        <?php endif; ?>
    </div>
    <?php
    if($answer_count >= $countNotFollowUpQuestion && $admin_permission) {
    ?>
    <div class="row user-content-row">
        <div class="col-12">
            <div class="ticket-method-ctn">
                <label class="ticket-method-title">
                        <?php echo $trans->phrase('user_ticket_phrase17'); ?>
                </label>
                <?php
                   if($answer_count >= $countNotFollowUpQuestion):
                        $methods = $Database->get_multiple_data(false, false, 'method');
                        $active_methods = null;
                        if(strlen($ticket['ticket_methods'])>0){
                            $active_methods = json_decode($ticket['ticket_methods'], true);
                        }
                        if($active_methods):
                            //Total method selection
                            $total_selection = 0;
                            foreach($active_methods as $method_key => $method_priority){
                                $total_selection += $method_priority;
                            }

                            $count = 0;
                            foreach($active_methods as $method_key => $method_priority):
                                if($method_priority > 0):
                                    //Incrementing method number
                                    $count++;
                                    $sql = "SELECT * FROM method_content WHERE method_id={$method_key} AND lang_code='{$_SESSION['trans']}'";
                                    $method_data = $Database->get_connection()->prepare($sql);
                                    $method_data->execute();
                                    if($method_data->rowCount() < 1)
                                        $method_data = false;
                                    else
                                        $method_data = $method_data->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="ticket-method-card">
                    <div class="row">
                        <div class="col-2 method-card-number">
                            <?php echo $count; ?>
                        </div>
                        <div class="col-7 method-card-title">
                            <?php echo $method_data['method_name'] ?>
                        </div>
                        <div class="col-3 method-card-readmore">
                            <button class="btn btn-light btn-sm method-card-btn ticket-method-card-readmore"><i class="fas fa-chevron-down"></i></button>
                            <button class="btn btn-dark btn-sm method-card-btn method-percent-btn mr-1">
                            <?php echo ((int)(($method_priority / $total_selection) * 100))."%"; ?>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 method-card-details ticket-method-card-details">
                        <?php echo htmlspecialchars_decode( $method_data['method_details'], ENT_QUOTES); ?>
                        </div>
                    </div>
                </div>
                <?php
                                endif;
                            endforeach;
                        endif;
                    else:
                        echo $trans->phrase("user_ticket_phrase29");
                    endif;
                }
                ?>
            </div>
        </div>
    </div>
    </div>
    </div>
<!-- Page: Review -->
<?php
    elseif(isset($_GET['page']) && $_GET['page'] == 'review'):
 ?>
 
<div class="card">
    <div class="card-body p-3">
    <div class="row user-content-row">
        <div class="col-12">
            <form>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><?php echo $trans->phrase("user_ticket_phrase2"); ?></div>
                    </div>
                    <input type="text" id="ticket_name" value="<?php echo ($ticket) ? $ticket['ticket_name']: ''; ?>" class="form-control" disabled>
                </div>
            </form>
        </div>
    </div>
    <?php

    $review = json_decode($ticket['ticket_review'], true);

    if(isset($review['review_status'])){
        $reviewStatus =  $review['review_status'];
        $reviewOptions = explode(",",$reviewStatus);
    }
    if(isset($review['review_text'])){
        $ticketReview = $review['review_text'];
    }

    $disabledStatus = '';

    if($ticket['review_status']=='1') {
        $disabledStatus = 'disabled';
    }

    if(!$user_permission) {
        $disabledStatus = 'disabled';
    }

    ?>
    <div class="row user-content-row">
        <div class="col-12">
            <form class="r-f-form">
                <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase30"); ?></label><br>
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
                <div class="form-check">
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
        </div>
    </div>
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

    <div class="row col-12">
        <div class="col-3">
        </div>
        <div class="row col-6">
            <div class="col-3 d-flex flex-row-reverse">
                <a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id'] ?>&page=question&pageNum=7"
                        role="button"
                        class="btn btn-info btn-sm ml-1 mr-1 table-page-prev">
                    &nbsp;&nbsp;&nbsp;<i class="fas fa-chevron-left"></i>&nbsp;&nbsp;&nbsp;
                </a>
            </div>
            <div class="col-6">
                <button  class="btn btn-info btn-sm table-page-number" style="width:100%">
                <span><?php echo $trans->phrase('text_review');?></span>
                </button>
            </div>
            <div class="col-3">
                    <a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id'] ?>&page=rating"
                        role="button"
                        class="btn btn-info btn-sm ml-1 mr-1 table-page-next">
                        &nbsp;&nbsp;&nbsp;<i class="fas fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;
                </a>
            </div>
        </div>
        <div class="col-3">
        </div>
    </div>

    <div  class="row user-content-row" style="margin-top:1%">
        <?php
        if($ticket['review_status']!='1'
                && $user_permission) {
            if($ticket['ticket_status']=='closed') {
        ?>
            <button id="review_submit" class="btn btn-success"
                data-ticket_id="<?php echo $_GET['id']; ?>"
                data-ticket_review_status="1">
                <?php echo $trans->phrase("user_ticket_phrase44"); ?>
            </button>
        <?php
            }
            else {
                ?>
                  <button id="review_submit"
                  class="btn btn-success"
                  data-ticket_id="<?php echo $_GET['id']; ?>"
                  data-ticket_review_status="0">
                    <?php echo $trans->phrase("user_ticket_phrase15"); ?>
                  </button>
                <?php
            }
        }
        ?>
    </div>

    </div>
    </div>
    </div>

<!-- Page: ratting -->
<?php
    elseif(isset($_GET['page'])
        && $_GET['page'] == 'rating'):
?>

<div class="card">
    <div class="card-body p-3">
<div class="row user-content-row">
    <div class="col-12">
        <form>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                            <?php echo $trans->phrase("user_ticket_phrase1"); ?>
                    </div>
                </div>
                <input type="text" id="ticket_name"
                    value="<?php echo ($ticket) ? $ticket['ticket_name']: ''; ?>"
                    class="form-control" disabled>
            </div>
        </form>
    </div>
</div>

    <?php

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

        $ratingStatus = '0';
        if(!$user_permission) {
            $ratingStatus = '1';
        }
        else {
            $ratingStatus = $ticket['rating_status'];
        }

    ?>
    <div class="row user-content-row">
        <input type="hidden" id="rating_status_value" name="rating_status_value"
            value="<?php echo $ratingStatus; ?>"/>
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
            </textarea>
        </form>
    </div>
</div>
<div class="row user-content-row">
    <div class="col-12">
        <form>
        <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase52"); ?></label>
            <textarea id="rating_text_2">
            </textarea>
        </form>
    </div>
</div>

<div class="row col-12">
        <div class="col-3">
        </div>
        <div class="row col-6">
            <div class="col-3 d-flex flex-row-reverse">
                <a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id'] ?>&page=review"
                        role="button"
                        class="btn btn-info btn-sm ml-1 mr-1 table-page-next">
                        &nbsp;&nbsp;&nbsp;<i class="fas fa-chevron-left"></i>&nbsp;&nbsp;&nbsp;
                </a>
            </div>
            <div class="col-6">
                <button class="btn btn-info btn-sm table-page-number" style="width:100%">
                    <span><?php echo $trans->phrase('text_rating');?></span>
                </button>
            </div>
        </div>
        <div class="col-3">
        </div>
    </div>
    </div>
    </div>

    <?php

    $answerCount = 0;
    $countNotFollowUpQuestion = 0;

    $questionsList = $Database->get_multiple_data(false, false, 'question');
    if($questionsList) {
        foreach($questionsList as $questionData) {
            if($questionData['question_follow_up']==0) {
                $countNotFollowUpQuestion++;
            }
        }
    }

    $response = $ticket['ticket_response'];
    if($response) {
        $response_array = json_decode($response, true);
        foreach($response_array as $q_id => $resp) {
            if(isset($resp['answer'])
                && $resp['answer']
                && !$resp['follow-up']) {
                $answerCount++;
            }
        }
    }

    ?>
    
<div class="card">
    <div class="card-body p-3">

    <div  class="row user-content-row" style="justify-content:center;">
        <?php
        if($ticket['rating_status']!='1'
                        && $user_permission) {
            if($ticket['ticket_status']=='closed') {
        ?>
            <button id="rating_submit" class="btn btn-success mb-3 ml-3"
                style = "width:150px;"
                data-ticket_id="<?php echo $_GET['id']; ?>"
                data-ticket_rating_status="1">
                <?php echo $trans->phrase("user_ticket_phrase44"); ?>
            </button>
            <?php
            }
            else {
                ?>
                  <button id="rating_submit"
                    class="btn btn-success  mb-3 ml-3"
                    style = "width:150px;"
                    data-ticket_id="<?php echo $_GET['id']; ?>"
                    data-ticket_rating_status="0">
                    <?php echo $trans->phrase("user_ticket_phrase15"); ?>
                  </button>
                <?php
            }

            if($answerCount>=$countNotFollowUpQuestion
                && $ticket['ticket_status']!='closed') {
            ?>
                   <button id="close_ticket"
                           class="btn btn-info  mb-3 ml-3"
                           style = "width:150px;"
                           data-ticket_id="<?php echo ($ticket) ? $ticket['ticket_id']: ''; ?>">
                        <?php echo $trans->phrase("user_ticket_phrase16"); ?>
                    </button>
                <?php

                }
        }
        ?>
    </div>
    </div>
    </div>

<?php endif; ?> <!-- Pages -->
<?php
        else:
            echo "You are not authorized to view this report!";
        endif; //User permission endif
    else:
        echo "Content not found!";
    endif; //Ticket exist endif

//New ticket
elseif($_SESSION['account-type'] == 'user'):
    
      if(isset($_GET['page']) && $_GET['page'] == 'reporttype'):
          ?>
          <div class="container">
          <div class="row">
  <div class="col-3"><a href="?route=ticket&page=requestreport&type=mainreport"><img src="<?php echo SITE_URL ?>/images/generic-image-placeholder.png"/><span style="margin-left:35%;">Main Report </span></a></div>
  <div class="col-3"><a href="?route=ticket&page=requestreport&type=duoreport"><img src="<?php echo SITE_URL ?>/images/generic-image-placeholder.png"/><span style="margin-left:35%;">Duo Report </span></a></div>
  <div class="col-3"><a href="?route=ticket&page=requestreport&type=leaderprofile"><img src="<?php echo SITE_URL ?>/images/generic-image-placeholder.png"/><span style="margin-left:35%;">Leader Profile </span></a></div>
   <div class="col-3"><a href="?route=ticket&page=requestreport&type=employeeprofile"><img src="<?php echo SITE_URL ?>/images/generic-image-placeholder.png"/><span style="margin-left:35%;">Employee Profile </span></a></div>
</div>
</div>
<?php 
          
          
          endif;
          
           if(isset($_GET['page']) && $_GET['page'] == 'requestreport'):
          ?>
           
          <div class="container">
          <div class="row">
             
<div class = "radio">
     <label for = "name">Submit Report To :</label></div>
      <div class = "radio">
        
           <input class="form-check-input--" type="radio" name="submitto"  value="company" checked/>  <label>Company
         </label>
      </div>
      <div class = "radio">
         <label>
          <input class="form-check-input--" type="radio" name="submitto"  value="consultant"  />
              <span>Consultant</span>
         </label>
      </div>
  </div>
  </div>

  
  
 <?php 
 
 
$user_data= $Database->get_data_new('user_id', $_SESSION['account-id'], 'user', true);

 $company_id= $user_data['user_company_id'];
//echo $company_email= $user_data['user_7email'];

$company_data= $Database->get_data_new('company_id', $company_id, 'company', true);
 $company_data['company_email'];

$coonsultant_data= $Database->get_data_new('consultant_companies', $company_id, 'consultant', true);
 $coonsultant_data['tfa_email'];


?>
<input type="hidden" namne="reporttype" id="reporttype" value="<?php echo $_GET['type'] ;?>">

<input type="hidden" namne="company_email" id="company_email" value="<?php echo $company_data['company_email'] ;?>">
<input type="hidden" namne="company_id" id="company_id" value="<?php echo $company_data['company_id'] ;?>">

<input type="hidden" namne="consultant_id" id="consultant_id" value="<?php echo $company_data['consultant_id'] ;?>">


<input type="hidden" namne="consultant_email" id="consultant_email" value="<?php echo $coonsultant_data['consultant_email'] ;?>">
<input type="hidden" namne="user_id" id="user_id" value="<?php echo $_SESSION['account-id'] ;?>">

 <button id="submitreportrequest" class="btn btn-success">
               Send Request
            </button>


<?php 
          
          
          endif;
          

if(isset($_GET['page']) && $_GET['page'] == 'summarize'):
if(isset($_GET['req_id'])){
$_SESSION['ticket_request_id'] = $_GET['req_id'];
}
/*if(isset($_GET['nareq_id'])){
$_SESSION['ticket_narequest_id'] = $_GET['nareq_id'];
}*/
?>
<?php 
  $user_id = $_SESSION['account-id'];
                  
?>

<div class="card">
    <div class="card-body p-3">
<div class="row user-content-row">
    <div class="col-12">
        <form>
            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase23"); ?></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><?php echo $trans->phrase("user_ticket_phrase1"); ?></div>
                </div>
                <input type="text" id="ticket_name" class="form-control">
            </div>
        </form>
    </div>
</div>
<div class="row user-content-row">
    <div class="col-12">
        <form>
        <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase25"); ?></label>
            <textarea id="ticket_summary"></textarea>
        </form>
    </div>
</div>
<div class="row user-content-row">
    <button id="create_ticket" class="btn btn-success mb-3 ml-3" data-ticket_id="">
        <?php echo $trans->phrase("user_ticket_phrase24"); ?>
    </button>
</div>
    </div>
    </div>

<?php 
 endif;
if(isset($_GET['page']) && $_GET['page'] == 'summarize1'): ?>

<?php 
  $user_id = $_SESSION['account-id'];


  $is_report = count($Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE status='0' AND user_id={$user_id}"));
 $flag=false;
 if($is_report > 0)
 { 
     $flag=true;
 ?>
    <div class="card">
        <div class="card-body p-3">
            <div class="row user-content-row request_form">
                <div class="col-12" style="text-align: center;color: green;"> 
                    <h2 style="color: green;">You have submitted Your report for permission. Please wait sometime for approvel.</h>
                </div>
            </div>
        </div>    
 <?php 

 }


 $is_report = count($Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE status IN ('0','1') AND user_id={$user_id}"));
 if($flag==false && $is_report == 0)
 { 
 $flag=true;
 ?>
<div class="card">
    <div class="card-body p-3">
<div class="row user-content-row request_form">
    <div class="col-12">
        <form>
            <input type="hidden" name="request_form_id" id="request_form_id" value="1">
            <div class="input-group">
                <div style="display: contents;">
                    <!-- section 1 start -->
                    <div class="col-xs-3 click_select_report" attr-val="1" style="cursor: pointer;">
                        <div class="data-section col-xs-12">
                            <div class="image-section col-xs-12" style="padding: 10px;padding-bottom: 1px;">
                                <img src="https://alexsol.tk<?php echo SITE_URL ?>/images/report_image1.png">
                            </div>
                            <div class="text-section col-xs-12" style="text-align: center;font-weight:bold;border: 1px solid;height: 40px;
    margin: 0px 11px;">
                                Image 1
                            </div> 
                        </div>
                    </div>
                    <!-- section 1 end -->
                     <!-- section 2 start -->
                    <div class="col-xs-3 click_select_report" attr-val="2" style="cursor: pointer;">
                        <div class="data-section col-xs-12">
                            <div class="image-section col-xs-12" style="padding: 10px;padding-bottom: 1px;">
                                <img src="https://alexsol.tk<?php echo SITE_URL ?>/images/report_image1.png">
                            </div>
                            <div class="text-section col-xs-12" style="text-align: center;font-weight:bold;border: 1px solid;height: 40px;
    margin: 0px 11px;">
                                Image 2
                            </div> 
                        </div>
                    </div>
                    <!-- section 2 end -->
                     <!-- section 3 start -->
                    <div class="col-xs-3 click_select_report" attr-val="3" style="cursor: pointer;">
                        <div class="data-section col-xs-12">
                            <div class="image-section col-xs-12" style="padding: 10px;padding-bottom: 1px;">
                                <img src="https://alexsol.tk<?php echo SITE_URL ?>/images/report_image1.png">
                            </div>
                            <div class="text-section col-xs-12" style="text-align: center;font-weight:bold;border: 1px solid;height: 40px;
    margin: 0px 11px;">
                                Image 3
                            </div> 
                        </div>
                    </div>
                    <!-- section 3 end -->
                     <!-- section 4 start -->
                    <div class="col-xs-3 click_select_report" attr-val="4" style="cursor: pointer;">
                        <div class="data-section col-xs-12">
                            <div class="image-section col-xs-12" style="padding: 10px;padding-bottom: 1px;">
                                <img src="https://alexsol.tk<?php echo SITE_URL ?>/images/report_image1.png">
                            </div>
                            <div class="text-section col-xs-12" style="text-align: center;font-weight:bold;border: 1px solid;height: 40px;
    margin: 0px 11px;">
                                Image 4
                            </div> 
                        </div>
                    </div>
                    <!-- section 4 end -->
                    
            
                </div>
            </div>
        </form>
    </div>
</div>
<div class="col-xs-6">
    <div class="col-12">
        <label class="ticket-label">Permission by:</label>
        <input type="radio" checked="checked" name="permission_by" id="permission_by" value="0"> Company
        <input type="radio" name="permission_by" id="permission_by" value="1"> Consultancy
    </div>
</div>
<div class="col-xs-6">
    <button id="request_from_ticket" class="btn btn-success mb-3 ml-3" data-ticket_id="">
        <?php echo $trans->phrase("user_ticket_phrase59"); ?>
    </button>
</div>

    </div>
    </div>

  <script type="text/javascript">
        $(document).ready(function() {
                $('.click_select_report').click(function(){
                    $('.select_report').removeClass();
                    $('#request_form_id').val($(this).attr('attr-val'));
                    $(this).addClass('select_report');
                });
                
    //Create ticket
    $('#request_from_ticket').click(function (event) {
        event.preventDefault();

        let request_form_id = $('#request_form_id').val();
        let permission_by = $("input[name='permission_by']:checked").val();

       

            $.ajax({
                url: '<?php echo SITE_URL ?>/option_server.php',
                type: 'POST',
                data: {'sign': 'request_form', 'request_form_id': request_form_id, 'permission_by': permission_by}
            }).done(function (data) {
                data = JSON.parse(data);
                if (data['status'] == 'success') {
                    window.location.href = "<?php echo SITE_URL ?>/user/index.php?route=ticket&page=summarize1";
                } else {
                    alert(data['message']);
                }
            })
        
    });
        });
    
</script>

<?php  }
 if($flag==false) header("Location: index.php?route=ticket&page=summarize");
    
    endif;
else:
    echo "Content not found!";
endif;  //ID set endif
?>
<?php if(isset($_GET['page']) && $_GET['page'] == 'rtickets') {
$user_id = $_SESSION['account-id'];
$requests = $Database->get_multiple_data('user_id',$user_id,'tbl_report_request', '=', true, 'id DESC', false);
?>
<div class="card">
    <div class="card-body p-3">
<div class="table-resposive">
 <table class="table align-middle table-row-dashed fs-6 gy-5" id="ticket-list">
        <thead>
            <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                <th scope="col" ><?php echo $trans->phrase("per_title") ?></th>
                <th scope="col" ><?php echo $trans->phrase("report_id") ?></th>
				<th scope="col" ><?php echo $trans->phrase("date_request") ?></th>
                <th scope="col" ><?php echo $trans->phrase("report_status") ?></th>
                <th scope="col" ><?php echo $trans->phrase("report_per_by") ?> </th>
				<th scope="col" ><?php echo $trans->phrase("date_approval") ?></th>
                <th scope="col" ><?php echo $trans->phrase("report_action") ?></th>    
            </tr>
        </thead>
        <tbody class="fw-bold text-gray-600">
             <?php 
             if ($requests) {
              foreach ($requests as $request) {
			  if($request['permisson_ticket_title'] != "") {	  
             ?>
             <tr class="text-start fw-bolder fs-7 gs-0">
                 <td><?php echo $request['permisson_ticket_title']; ?></td>
                        <td>
                            <?php if($request['report_id'] == 26) { ?>
                            <?php echo $trans->phrase("main_report") ?>
                            <?php } elseif($request['report_id'] == 27) { ?>
                            <?php echo $trans->phrase("duo_report") ?>
                            <?php } elseif($request['report_id'] == 28) { ?>
                            <?php echo $trans->phrase("leader_report") ?>
                            <?php } elseif($request['report_id'] == 29) { ?>
                            <?php echo $trans->phrase("employee_report") ?>
                            <?php } ?>
                        </td>
						<td>
						<?php echo $request['request_date_time']; ?>
						</td>
                        <td>
                            <?php if($request['status'] == 0) { ?>
                            <?php echo $trans->phrase("report_pen") ?>
                            <?php } else { ?>
                            <?php echo $trans->phrase("report_approve") ?>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($request['permission_by'] == 0) { ?>
                             <?php echo $trans->phrase("company_report_text") ?>
                            <?php } else { ?>
                            <?php echo $trans->phrase("consultancy_report_text") ?>
                            <?php } ?>
                        </td>
						<td>
						<?php echo $request['approval_date_time']; ?>
						</td>
                        <td>
                            <?php if($request['status'] == 0) { ?>
                                 <a class="btn btn-primary btn-sm" style="pointer-events: none;" role="button"
                                    href="<?php echo SITE_URL ?>/user/index.php?route=ticket&req_id=<?php echo $request['id']; ?>&page=summarize">
                                     <?php echo $trans->phrase("start_ticket_text") ?>
                                </a>
                             <?php } else { ?>
                             <a class="btn btn-primary btn-sm" role="button"
                                    href="<?php echo SITE_URL ?>/user/index.php?route=ticket&req_id=<?php echo $request['id']; ?>&page=summarize">
                                     <?php echo $trans->phrase("start_ticket_text") ?>
                                </a>
                             <?php } ?>
                        </td>
            </tr>
            <?php } ?>
			<?php } ?>
            <?php } ?>
        </tbody>
</table>
</div>
</div>
</div>
<?php } ?>
<?php if(isset($_GET['page']) && $_GET['page'] == 'nticket') { 


if(!isset($_SESSION['trans'])){
    $Database = new Database();
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if($default_language){
        $_SESSION['trans'] = $default_language['lang_code'];
    }
    else{
        $_SESSION['trans'] = 'en';
    }
}

$report_data = $Database->get_multiple_data('report_lang_code', $_SESSION['trans'], 'mlreport_format_content');
?>



<div class="card">
    <div class="card-body p-3">
<div class="row user-content-row request_form">
    <div class="col-12">
        <form>
            <input type="hidden" name="request_form_id" id="request_form_id" value="1">
            <div class="input-group">
                <div style="display: contents;">
                  <?php 
                  if ($report_data) {
                  $i = 1;       
                  foreach ($report_data as $report_format) {
                  ?>  
                    <!-- section 1 start -->
                    <div class="col-xs-3 click_select_report <?php if($i == 1)  { ?>select_report<?php } ?>" attr-val="<?php echo $report_format['report_format_id'];  ?>" style="cursor: pointer;">
                         <?php if($i == 1)  { ?>
                        <script>
                            $('#request_form_id').val(<?php echo $report_format['report_format_id'];  ?>);
                        </script>
                         <?php } ?>
                        <div class="data-section col-xs-12">
                            <div class="image-section col-xs-12" style="padding: 10px;padding-bottom: 1px;">
                                <img src="<?php echo SITE_URL ?>/images/report_image/<?php echo $report_format['report_image']  ?>" style="height: 317px;width: 230px;">
                            </div>
                            <div class="text-section col-xs-12" style="font-size: 23px;text-align: center;font-weight:bold;border: 1px solid;height: 40px;
    margin: 0px 11px;">
                                <?php echo $report_format['report_title']  ?> <button data-bs-toggle="modal" onclick="return false;" data-bs-target="#exampleModalPen_<?php echo $report_format['report_format_id']; ?>" class="btn"><i class="fas fa-info-circle"></i></button>
                            </div> 
                        </div>
                    </div>
                    
    <div class="modal fade" id="exampleModalPen_<?php echo $report_format['report_format_id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title text-center" style="margin: 0px auto;" id="exampleModalLabel"><?php echo $report_format['report_title']  ?> details</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body" id="">
		            <?php echo htmlspecialchars_decode($report_format['report_desc']); ?>
		  </div>
		</div>
	  </div>
	</div>
	
                   <?php
                   
                       $i++;
                  }
                   } ?> 
                    
                </div>
            </div>
        </form>
    </div>
</div>
<div class="col-xs-6">
    <div class="col-12">
        <form>
            <label class="ticket-label"><?php echo $trans->phrase("create_ticket_personal"); ?> :</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><?php echo $trans->phrase("permisson_ticket_name"); ?></div>
                </div>
                <input type="text" id="permisson_ticket_title" class="form-control">
            </div>
        </form>
    </div>
</div>
<br>
<div class="col-xs-6">
    <div class="col-12">
        <label class="ticket-label"><?php echo $trans->phrase("permission_by"); ?> :</label>
        <input type="radio" name="permission_by" id="permission_by_company" value="0"><?php echo $trans->phrase("company_text"); ?>
        <input type="radio" name="permission_by" id="permission_by_consultancy" value="1"><?php echo $trans->phrase("consultancy_text"); ?>
    </div>
</div>
<div class="col-xs-6">
    <button id="request_from_ticket" class="btn btn-success mb-3 ml-3" data-ticket_id="">
        <?php echo $trans->phrase("user_ticket_phrase59"); ?>
    </button>
</div>
<div style="display:none;" id="alertSectionemptyptitle"><span id="alertSection" style="color: red;"><?php echo $trans->phrase("permisson_tite_text"); ?></span></div>
    </div>
    </div>


<?php } ?>
<?php if(isset($_GET['page']) && $_GET['page'] == 'naticket') { 
if(!isset($_SESSION['trans'])){
    $Database = new Database();
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if($default_language){
        $_SESSION['trans'] = $default_language['lang_code'];
    }
    else{
        $_SESSION['trans'] = 'en';
    }
}

$report_data = $Database->get_multiple_data('report_lang_code', $_SESSION['trans'], 'mlreport_format_content');
?>

<div class="card">
    <div class="card-body p-3">
<div class="row user-content-row request_form">
    <div class="col-12">
        <form>
            <input type="hidden" name="request_form_id" id="request_form_id" value="1">
            <div class="input-group">
                <div style="display: contents;">
                  <?php 
                  if ($report_data) {
                  $i = 1;       
                  foreach ($report_data as $report_format) {
                  ?>  
                    <!-- section 1 start -->
                    <div class="col-xs-3 click_select_report <?php if($i == 1)  { ?>select_report<?php } ?>" attr-val="<?php echo $report_format['report_format_id'];  ?>" style="cursor: pointer;">
                         <?php if($i == 1)  { ?>
                        <script>
                            $('#request_form_id').val(<?php echo $report_format['report_format_id'];  ?>);
                        </script>
                         <?php } ?>
                        <div class="data-section col-xs-12">
                            <div class="image-section col-xs-12" style="padding: 10px;padding-bottom: 1px;">
                                <img src="<?php echo SITE_URL ?>/images/report_image/<?php echo $report_format['report_image']  ?>" style="height: 317px;width: 230px;">
                            </div>
                            <div class="text-section col-xs-12" style="font-size: 23px;text-align: center;font-weight:bold;border: 1px solid;height: 40px;
    margin: 0px 11px;">
                                <?php echo $report_format['report_title']  ?> <button data-bs-toggle="modal" onclick="return false;" data-bs-target="#exampleModalPen_<?php echo $report_format['report_format_id']; ?>" class="btn"><i class="fas fa-info-circle"></i></button>
                            </div> 
                        </div>
                    </div>
                    
    <div class="modal fade" id="exampleModalPen_<?php echo $report_format['report_format_id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title text-center" style="margin: 0px auto;" id="exampleModalLabel"><?php echo $trans->phrase("report_type_name_text"); ?> <?php echo $report_format['report_title']  ?></h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body" id="">
		            <?php echo htmlspecialchars_decode($report_format['report_desc']); ?>
		  </div>
		</div>
	  </div>
	</div>
	
                   <?php
                   
                       $i++;
                  }
                   } ?> 
                    
                </div>
            </div>
        </form>
    </div>
</div>

<div class="col-xs-6">
    <button id="request_na_ticket" class="btn btn-success mb-3 ml-3" data-ticket_id="">
        <?php echo $trans->phrase("start_ticket_text"); ?>
    </button>
</div>

</div>
</div>

<?php } ?>
<?php if(isset($_GET['page']) && $_GET['page'] == 'nticket_success') { ?>
<div class="card">
        <div class="card-body p-3">
            <div class="row user-content-row request_form">
                <div class="col-12" style="text-align: center;color: green;"> 
                    <h2 style="color: green;">You have submitted Your report for permission. Please wait sometime for approvel.</h>
                </div>
            </div>
        </div> 
<?php } ?>
<script>
let res_ticket_id = $('#res_ticket_id').val();
     let progressbar = $("#progressbar");
    let questionIds = $('#questionIds').val();
    let answerIds = $('#answerIds').val();
    let pageNumber = $('#pageNumber').val();
    let questionNo = res_ticket_id;

    let numberOfQuestion = 0;
    let numberOfAnswer = 0;
    let percent = 0;

    if (questionIds.length >= 1) {
        var questionIdArr = questionIds.split(',');
        numberOfQuestion = questionIdArr.length;
    }

    if (answerIds.length >= 1) {
        var totalAnswerIdsArr = answerIds.split(',');
        numberOfAnswer = totalAnswerIdsArr.length;

        if (questionNo != 0) {
            if (!totalAnswerIdsArr.includes(questionNo)) {
                numberOfAnswer = numberOfAnswer;
                answerIds = answerIds + "," + questionNo;
                $('#answerIds').val(answerIds);
            }
        }
    } else {
        if (questionNo != 0) {
            $('#answerIds').val(questionNo);
            numberOfAnswer = numberOfAnswer;
        }
    }

    percent = Math.floor((numberOfAnswer * 100) / numberOfQuestion);

    progressbar.css({
        "width": percent + "%"
    });

    $('#label-progressbar').html(percent + '% (' + numberOfAnswer + '/' + numberOfQuestion + ')');
</script>