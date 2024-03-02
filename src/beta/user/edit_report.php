<?php
    require_once('../database.php');
    $Database = new Database();
    
    $tickets = null;
    $status_condtion= '0,1,2';
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
        $reports =$Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE company_id={$record_id} AND status IN({$status_condtion})");

    }
    else if ($_SESSION['account-type'] == 'consultant' && isset($_GET['company_id']) && !empty($_GET['company_id']) ) {
        $record_id = $_GET['company_id'];
        $reports =$Database->get_data_by_query("SELECT * FROM tbl_report_request WHERE company_id={$record_id} AND status IN({$status_condtion})");
    }
    else if($_SESSION['account-type'] == 'user'){
        echo '<h1>Invaild url</h1>';
        exit;
    }
?>

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
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 r-t-my-1">
            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Search-->
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
                    <a class="text-gray-600 text-hover-primary mb-1" href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&id=<?php echo $ticket['user_id']; ?>&page=edit">
                        <?php echo $ticket['user_id']; ?>
                    </a>
                </td>
                <td>
                    
                        <?php 
                          $cId = $ticket['company_id'];
                          $company_name =$Database->get_name_by_id('company', 'company_name', $cId); 
                        
                        if($ticket['permission_by']=='0'){
                             echo 'By Company <b>'.$company_name.'</b>';
                        }else{
                             $consultancy_id = $ticket['consultancy_id'];
                             $name =$Database->get_name_by_id('consultant', 'consultant_name', $consultancy_id); 
                            echo 'By Consultancy <b>'.$name.'</b>';
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
                        // //echo date("m-F", strtotime($ticket['ticket_time']));
                        // $dateMName = date("n", strtotime($ticket['request_date_time']));
                        // if($dateMName=='01') {
                        //     $dateMName = ($trans->phrase('text_jan'));
                        // }
                        // elseif($dateMName=='02') {
                        //     $dateMName = ($trans->phrase('text_feb'));
                        // }
                        // elseif($dateMName=='03') {
                        //     $dateMName = ($trans->phrase('text_mar'));
                        // }
                        // elseif($dateMName=='04') {
                        //     $dateMName = ($trans->phrase('text_apr'));
                        // }
                        // elseif($dateMName=='05') {
                        //     $dateMName = ($trans->phrase('text_may'));
                        // }
                        // elseif($dateMName=='06') {
                        //     $dateMName = ($trans->phrase('text_jun'));
                        // }
                        // elseif($dateMName=='07') {
                        //     $dateMName = ($trans->phrase('text_jul'));
                        // }
                        // elseif($dateMName=='08') {
                        //     $dateMName = ($trans->phrase('text_aug'));
                        // }
                        // elseif($dateMName=='09') {
                        //     $dateMName = ($trans->phrase('text_sep'));
                        // }
                        // elseif($dateMName=='10') {
                        //     $dateMName = ($trans->phrase('text_oct'));
                        // }
                        // elseif($dateMName=='11') {
                        //     $dateMName = ($trans->phrase('text_nov'));
                        // }
                        // elseif($dateMName=='12') {
                        //     $dateMName = ($trans->phrase('text_dec'));
                        // }
                        // $day = date("y", strtotime($ticket['request_date_time']));
                        // echo $day."/".$dateMName."/".date("y", strtotime($ticket['request_date_time']));
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