<?php
    require_once('../database.php');
    $Database = new Database();
    $tickets = null;
    $status_condtion= "'0','1','2'";
    if(isset($_GET['status']))
    {
        $status_condtion= $_GET['status'];
    }
    
    if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'){
        //$tickets = $Database->get_multiple_data(false, false, 'ticket');
        //$tickets = $Database->get_multiple_data(false, false, 'ticket', null, true, 'ticket_time DESC');
        $tickets =$Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE 1=1 AND status IN({$status_condtion})");
     

    }
    else if($_SESSION['account-type'] == 'company'){
        $user = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);
       
        $record_id =  $user['company_id'];
        $tickets =$Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE company_id={$record_id} AND status IN({$status_condtion})");
        //print_r($tickets); exit;
    }
    else if ($_SESSION['account-type'] == 'consultant' ) {
        $user = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
        $record_id =  $user['consultant_id'];
        $tickets =$Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE permission_by='1' AND consultancy_id={$record_id} AND status IN({$status_condtion})");
    }
    else if($_SESSION['account-type'] == 'user'){
        echo '<h1>Invaild url</h1>';
        exit;
    }
?>

<!-- Page: Question -->
<?php
    if(isset($_GET['page']) && $_GET['page'] == 'edit'){
    $id=$_GET['id'];
    
    $permission_by="'0','1'";
    if($_SESSION['account-type'] == 'consultant' )
    {
         $permission_by = '1' ;
    }
    $reports =$Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE permission_by IN ({$permission_by}) AND id={$id}");
   
    $ticket = array();
    if(count($reports)>0)
    {
        $ticket =  $reports[0];
        
    }

?>

<form>
<div class="card">
    <div class="card-body p-3">
    <div class="row user-content-row">
        <div class="col-12">
            
                <input type="hidden" id="request_id" name="request_id" value="<?php echo $id; ?>"
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <?php echo 'User Name' ?>
                        </div>
                         <?php $uId = isset($ticket['user_id'])? $ticket['user_id']:0;
                        $userName = $Database->get_name_by_id('user', 'user_name', $uId); ?>
                         <input type="text" id="ticket_name" value="<?php echo $userName; ?>" class="form-control" disabled>

                    </div>
                    
                </div>
        </div>
    </div>

    <div class="row user-content-row">
        <div class="col-12">
            <div class="ticket-information">
                <div class="row">
                    <div class="col-6">
                        <div class="ticket-information-group">
                            <label class="ticket-information-title"><?php echo 'Report Id' ?> </label>
                            <?php echo $ticket['report_id']; ?>
                        </div>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title"><?php echo 'Status' ?> </label>
                            <?php if($ticket['status'] == '2'): ?>
                                <button class="btn btn-secondary btn-sm">
                                    <?php echo 'Close' ?>
                                </button>
                            <?php elseif($ticket['status'] == '0' || $ticket['status'] == '1'): ?>
                                <select id="report_status" name="report_status">
                                    <option value="0" <?php  if ($ticket['status'] =='0') { echo "selected='selected'"; } ?> > Pending</option>
                                    <option value="1" <?php  if ($ticket['status'] =='1') { echo "selected='selected'"; } ?> > Approval</option>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="ticket-information-group">
                            <label class="ticket-information-title">
                                <?php echo 'Date' ?> :
                            </label>
                            <?php echo date("Y-m-d", strtotime($ticket['request_date_time'])); ?>
                        </div>
                      
                     

                        <div class="ticket-information-group">
                    <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports" style="background-color: grey;"
                                                    class="btn btn-success btn-sm"> Back</a>
                            <?php if($ticket['status'] != '2'){ ?>
                            <span id="updateRequestForm"  class="btn btn-success btn-sm"><?php echo 'Update' ?></span>
                            <div style="display:none;" id="alertSectionDiv"><span id="alertSection" style="margin-left: 10px;color: green;">Status changed successfully.</span></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
            </form>
 <script src="<?php echo JQUERY; ?>"></script>

 <script type="text/javascript">
        $(document).ready(function() {
               
                
    //Create ticket
    $('#updateRequestForm').click(function (event) {
        event.preventDefault();

        let request_id = $('#request_id').val();
        let report_status = $("select#report_status").val();
 
            $.ajax({
                url: '<?php echo SITE_URL ?>/option_server.php',
                type: 'POST',
                data: {'sign': 'request_form_update', 'id': request_id, 'report_status': report_status}
            }).done(function (data) {
                data = JSON.parse(data);
                if (data['status'] == 'success') {
                    $('#alertSectionDiv').show();
                      setTimeout(function() {$("#alertSectionDiv").hide();}, 3000);
                } else {
                    alert(data['message']);
                }
            })
        
    });
        });
    
</script>
    
<?php exit; } ?>    

<div class="card">
<div class="tabledata">
    <div class="col-12 r-t-col-12" style="display: flex; justify-content: space-between;">
        <div class="col-6 r-t-col-6">
        <span class="sort-text"><?php echo $trans->phrase("user_tickets_phrase2"); ?></span>
        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports" class="btn btn-sm btn-light-primary"><?php echo $trans->phrase("user_tickets_phrase3"); ?></a>
        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&status='0'" class="btn btn-sm btn-light-warning"><?php echo 'Pending' ?></a>
        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&status='1'" class="btn btn-sm btn-light-warning"><?php echo 'Approval' ?></a>
        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&status='2'" class="btn btn-sm btn-light-success"><?php echo $trans->phrase("user_tickets_phrase5"); ?></a>
</div>
       
    </div>
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="ticket-list">
        <thead>
            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                <th scope="col" >Report Id</th>
                <th scope="col" >Permission By</th>
                <th scope="col" ><?php echo $trans->phrase("user_composer_phrase14"); ?></th>                
                <th scope="col" ><?php echo $trans->phrase("user_composer_phrase13"); ?></th>
                <th scope="col" >Date</th>
                <th scope="col" ><?php echo $trans->phrase("user_language_phrase4"); ?></th>
            </tr>
        </thead>
        <tbody class="fw-bold text-gray-600">
        <?php
        echo '<pre>';
        if($tickets):
            //Filter view
            $view_process = true; 
            $view_closed = true;
            if(isset($_GET['status']) && $_GET['status'] == '0'){
                $view_closed = false;
            }
            if(isset($_GET['status']) && $_GET['status'] == '2'){
                $view_process = false;
            }
            //End filter
            foreach($tickets as $ticket):
                
        ?>
            <tr>                
                <td>
                    
                    <?php if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin'){ ?>
                                <?php echo $ticket['id']; ?>
                        <?php } else {                         
                        
                           if ($_SESSION['account-type'] == 'company' && $ticket['permission_by']=='1') { ?>
                           
                                <?php echo $ticket['id']; ?>
                            
                            <?php } else{ ?>
                                <a class="text-gray-600 text-hover-primary mb-1" href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&id=<?php echo $ticket['id']; ?>&page=edit">
                                    <?php echo $ticket['id']; ?>
                                </a>
                            <?php } ?>
                    <?php } ?>
                </td>
                <td>
                    
                        <?php 
                          $cId = $ticket['company_id'];
                          $company_name =$Database->get_name_by_id('company', 'company_name', $cId); 
                        
                        if($ticket['permission_by']=='0'){
                             echo '<b>By Company </b>'.$company_name;
                        }else{
                             $consultancy_id = $ticket['consultancy_id'];
                             $name =$Database->get_name_by_id('consultant', 'consultant_name', $consultancy_id); 
                            echo '<b>By Consultancy </b>'.$name;
                        }
                        ?>
                   
                </td>
             
                <td>
                     <?php 
                        echo $company_name; 
                    ?>
                </td>
                <td>
                    <?php
                        $uId = isset($ticket['user_id'])? $ticket['user_id']:0;
                        echo $Database->get_name_by_id('user', 'user_name', $uId);
                    ?>
                </td>
               
                <td>
                    <?php 
                        
                        echo $ticket['request_date_time'];
                     ?>
                </td>
                <td>
                    <?php if($ticket['status'] == '0'): ?>
                        <div class="inline_td">
                        <span class="badge badge-light-warning"><?php echo 'Pending'; ?></span>
                        </div>
                    <?php elseif($ticket['status'] == '1'): ?>
                        <div class="inline_td">
                        <span class="badge badge-light-warning"><?php echo 'Approval' ?></span>
                        </div>    
                    <?php elseif($ticket['status'] == '2'): ?>
                        <div class="inline_td">
                        <span class="badge badge-light-warning"><?php echo 'Closed' ?></span>
                        </div>
                    <?php endif; ?> 
                </td>
               
            </tr>
            <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>
</div>
   
