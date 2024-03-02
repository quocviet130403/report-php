<?php
require_once('../database.php');
require_once("../emojis.php");
$Database = new Database();
$tickets = null;

$rating_value = array_values(RATINGS);
$rating_keys = array_keys(RATINGS);

function getReportName($Database, $ticket_id)
{

    $req_info = $Database->get_data('ticket_id', $ticket_id, 'tbl_report_request', true);

    if ($req_info) {
        $report_format_id = $req_info['report_id'];
    } else {
        $report_format_id = 26;
    }

    $report = $Database->get_data('report_format_id', $report_format_id, 'mlreport_format_content', true);

    return $report['report_title'];
}

if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') {
    //$tickets = $Database->get_multiple_data(false, false, 'ticket');
    $tickets = $Database->get_multiple_data(false, false, 'ticket', null, true, 'ticket_time DESC');
} else if ($_SESSION['account-type'] == 'company') {
    $tickets = $Database->get_multiple_data('ticket_company_id', $_SESSION['account-id'], 'ticket', '=', true, 'ticket_time DESC');
} else if ($_SESSION['account-type'] == 'consultant') {
    $tickets = $Database->get_multiple_data('ticket_consultant_id', $_SESSION['account-id'], 'ticket', '=', true, 'ticket_time DESC');
} else if ($_SESSION['account-type'] == 'user') {
    $user = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
    $user_company = $Database->get_data('company_id', $user['user_company_id'], 'company', true);
    //if($user_company['company_show_tickets']){
    //    $tickets = $Database->get_multiple_data('ticket_company_id', $user_company['company_id'], 'ticket', '=', true, 'ticket_time DESC');
    // }
    // else{
    $tickets = $Database->get_multiple_data('ticket_user_id', $user['user_id'], 'ticket', '=', true, 'ticket_time DESC');
    // }
}
?>
<style>
    .modal .modal-content{

        width:700px;
    }
</style>

<div class="card">
    <div class="tabledata">
        <div class="col-12 r-t-col-12" style="display: flex; justify-content: space-between;">
            <div class="col-6 r-t-col-6">
                <span class="sort-text">
                    <?php echo $trans->phrase("user_tickets_phrase2"); ?>
                </span>
                <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets" class="btn btn-sm btn-light-primary"><?php echo $trans->phrase("user_tickets_phrase3"); ?></a>
                <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets&view=process"
                    class="btn btn-sm btn-light-warning"><?php echo $trans->phrase("user_tickets_phrase4"); ?></a>
                <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets&view=closed"
                    class="btn btn-sm btn-light-success"><?php echo $trans->phrase("user_tickets_phrase5"); ?></a>
            </div>
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1 r-t-my-1">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                            transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path
                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                            fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <input type="text" data-kt-customer-table-filter="search"
                    class="form-control form-control-solid w-250px ps-15"
                    placeholder="<?php echo $trans->phrase("search_ticket_text"); ?>" />
            </div>
            <!--end::Search-->
        </div>
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="ticket-list">
            <thead>
                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                    <th scope="col">
                        <?php echo $trans->phrase("user_tickets_phrase7"); ?>
                    </th>
                    <th scope="col">
                        <?php echo $trans->phrase("user_tickets_phrase6"); ?>
                    </th>
                    <th scope="col">
                        <?php echo $trans->phrase("user_composer_phrase14"); ?>
                    </th>
                    <th scope="col">
                        <?php echo $trans->phrase("user_composer_phrase13"); ?>
                    </th>
                    <th scope="col">
                        <?php echo $trans->phrase("pdf_report_phrase1"); ?>
                    </th>

                    <th scope="col">Date</th>
                    <?php if($_SESSION['account-type'] == 'super_admin') : ?>
                    <th scope="col">Admin Review</th>
                    <?php endif; ?>
                    <th scope="col">
                        <?php echo $trans->phrase("user_language_phrase4"); ?>
                    </th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
            <tbody class="fw-bold text-gray-600">
                <?php
                if ($tickets):
                    //Filter view
                    $view_process = true;
                    $view_closed = true;
                    if (isset($_GET['view']) && $_GET['view'] == 'process') {
                        $view_closed = false;
                    }
                    if (isset($_GET['view']) && $_GET['view'] == 'closed') {
                        $view_process = false;
                    }
                    //End filter
                    foreach ($tickets as $ticket):
                        if (
                            ($ticket['ticket_status'] == 'process' && $view_process) ||
                            ($ticket['ticket_status'] == 'closed' && $view_closed)
                        ):
                            ?>
                            <tr>
                                <td>
                                    <a class="text-gray-600 text-hover-primary mb-1"
                                        href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id']; ?>&page=question">
                                        <?php echo $ticket['ticket_id']; ?>
                                    </a>
                                </td>
                                <td>
                                    <a class="text-gray-800 text-hover-primary mb-1"
                                        href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id']; ?>&page=question">
                                        <?php echo $ticket['ticket_name']; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $cId = isset($ticket['ticket_company_id']) ? $ticket['ticket_company_id'] : 0;
                                    echo $Database->get_name_by_id('company', 'company_name', $cId);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $uId = isset($ticket['ticket_user_id']) ? $ticket['ticket_user_id'] : 0;
                                    echo $Database->get_name_by_id('user', 'user_name', $uId);
                                    ?>
                                </td>

                                <td>
                                    <?php echo getReportName($Database, $ticket['ticket_id']); ?>
                                </td>

                                <td>
                                    <?php

                                    $ticket_rating = json_decode($ticket['ticket_rating']);
                                    //echo date("m-F", strtotime($ticket['ticket_time']));
                                    $dateMName = date("n", strtotime($ticket['ticket_time']));
                                    if ($dateMName == '01') {
                                        $dateMName = ($trans->phrase('text_jan'));
                                    } elseif ($dateMName == '02') {
                                        $dateMName = ($trans->phrase('text_feb'));
                                    } elseif ($dateMName == '03') {
                                        $dateMName = ($trans->phrase('text_mar'));
                                    } elseif ($dateMName == '04') {
                                        $dateMName = ($trans->phrase('text_apr'));
                                    } elseif ($dateMName == '05') {
                                        $dateMName = ($trans->phrase('text_may'));
                                    } elseif ($dateMName == '06') {
                                        $dateMName = ($trans->phrase('text_jun'));
                                    } elseif ($dateMName == '07') {
                                        $dateMName = ($trans->phrase('text_jul'));
                                    } elseif ($dateMName == '08') {
                                        $dateMName = ($trans->phrase('text_aug'));
                                    } elseif ($dateMName == '09') {
                                        $dateMName = ($trans->phrase('text_sep'));
                                    } elseif ($dateMName == '10') {
                                        $dateMName = ($trans->phrase('text_oct'));
                                    } elseif ($dateMName == '11') {
                                        $dateMName = ($trans->phrase('text_nov'));
                                    } elseif ($dateMName == '12') {
                                        $dateMName = ($trans->phrase('text_dec'));
                                    }
                                    $day = date("d", strtotime($ticket['ticket_time']));
                                    if ($_SESSION['trans'] == 'en') {
                                        echo $day . "th " . $dateMName . " " . date("y", strtotime($ticket['ticket_time']));
                                    }
                                    if ($_SESSION['trans'] == 'nor') {
                                        echo $day . ". " . $dateMName . " " . date("y", strtotime($ticket['ticket_time']));
                                    }
                                    ?>
                                </td>
                                <?php if($_SESSION['account-type'] == 'super_admin') : ?>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#adminReviewModal<?= $ticket['ticket_id'] ?>" type="button"><?= $ticket_rating != "" && $ticket_rating->rating_check_1 != 0 && $ticket_rating->rating_check_2 != 0 && $ticket_rating->rating_check_3 != 0 && $ticket_rating->rating_check_4 != 0   && $ticket_rating->rating_text_1 != "" && $ticket_rating->rating_text_2 != ""  ? $trans->phrase("user_methods_phrase12") : $trans->phrase("no_ratings_given") ?></button>
                                </td>
                                <?php endif; ?>
                                <td class="d-flex">
                                    <?php if ($ticket['ticket_status'] == 'process'): ?>
                                        <div class="inline_td">
                                            <span class="badge badge-light-warning">
                                                <?php echo $trans->phrase("user_tickets_phrase4"); ?>
                                            </span><span class="iconify prosess" data-icon="bx:bxs-time"></span>
                                        </div>
                                    <?php elseif ($ticket['ticket_status'] == 'closed'): ?>
                                        <div class="inline_td">
                                            <span class="badge badge-light-success">
                                                <?php echo $trans->phrase("user_tickets_phrase5"); ?>
                                            </span><span class="iconify check" data-icon="akar-icons:circle-check-fill"></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($_SESSION['account-type'] == 'super_admin') { ?>
                                        <a href="#" class="px-1" onclick="delete_ticket(<?=$ticket['ticket_id']?>)">
                                            <i class="fa-regular fa-trash-can text-danger"></i></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php

                                    if ($ticket['report_gen_time']) {
                                        $currentDate = new DateTime('now');
                                        $reportGenDate = new DateTime($ticket['report_gen_time']);

                                        $dateDiff = $currentDate->diff($reportGenDate);
                                        $no_days_report_generated = $dateDiff->format('%a');

                                        if ($no_days_report_generated >= 30) {
                                            if (
                                                ($_SESSION['account-type'] == 'super_admin'
                                                    || $_SESSION['account-type'] == 'support_admin'
                                                    || $_SESSION['account-type'] == 'company')
                                                && $ticket['review_status'] == '1'
                                                && $ticket['ticket_status'] == 'closed'
                                            ) { ?>
                                                <a class="btn btn-primary btn-sm" role="button"
                                                    href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id']; ?>&page=review">
                                                    <?php echo $trans->phrase("user_tickets_phrase9"); ?>
                                                </a>
                                                <?php
                                            } elseif (
                                                $_SESSION['account-type'] == 'user'
                                                && $ticket['ticket_status'] == 'closed'
                                            ) { ?>
                                                <a class="btn btn-primary btn-sm" role="button"
                                                    href="<?php echo SITE_URL ?>/user/index.php?route=ticket&id=<?php echo $ticket['ticket_id']; ?>&page=review">
                                                    <?php echo $trans->phrase("user_tickets_phrase9"); ?>
                                                </a>
                                                <?php
                                            }
                                        }
                                    }

                                    ?>
                                </td>
                            </tr>
                            <?php if ($ticket_rating != '' && $ticket_rating->rating_check_1 != 0 && $ticket_rating->rating_check_2 != 0 && $ticket_rating->rating_check_3 != 0 && $ticket_rating->rating_check_4 != 0  && $ticket_rating->rating_text_1 != "" && $ticket_rating->rating_text_2 != ""): 
                                
                                    
                                ?>
                                <div class="modal fade" id="adminReviewModal<?= $ticket['ticket_id'] ?>" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Admin Review</h1>
                                                <button type="button" class="btn-close" data-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="smiley-check">
                                                    
                                                <?php
                                                
                                                if($ticket_rating->rating_check_1 != "" && $ticket_rating->rating_check_1 != null && $ticket_rating->rating_check_1 != 0  ) : ?>
                                                    <img src="<?= $rating_value[$ticket_rating->rating_check_1] ?>" width="100px"
                                                        height="100px" />
                                                        (<?= $rating_keys[$ticket_rating->rating_check_1]?>)
                                                    <p>The questions contributed to a better understanding of the conflict


                                                    </p>
                                                    <?php endif; ?>
                                                    <?php if($ticket_rating->rating_check_2 != "" && $ticket_rating->rating_check_2 != null && $ticket_rating->rating_check_2 != 0) : 
                                                        
                                                        ?>


                                                    <img src="<?= $rating_value[$ticket_rating->rating_check_2] ?>" width="100px"
                                                        height="100px" />
                                                        (<?= $rating_keys[$ticket_rating->rating_check_2]?>)
                                                    
                                                    <p>The tips were helpful in understanding the conflict

                                                    </p>
                                                    <?php endif; ?>
                                                    <?php if($ticket_rating->rating_check_3 != "" && $ticket_rating->rating_check_3 != null && $ticket_rating->rating_check_3 != 0) : ?>
                                                    <img src="<?= $rating_value[$ticket_rating->rating_check_3] ?>" width="100px"
                                                        height="100px" />
                                                        (<?= $rating_keys[$ticket_rating->rating_check_3]?>)

                                                    <p>The Nøgd system made data collection more efficient

                                                    </p>
                                                    <?php endif; ?>
                                                    <?php if($ticket_rating->rating_check_4 != "" && $ticket_rating->rating_check_4 != null && $ticket_rating->rating_check_4 != 0) : ?>
                                                    <img src="<?= $rating_value[$ticket_rating->rating_check_4] ?>" width="100px"
                                                        height="100px" />
                                                        (<?= $rating_keys[$ticket_rating->rating_check_4]?>)
                                                    
                                                    <p>Receiving a resolution proposal when the case is closed will be helpful

                                                    </p>
                                                    <?php endif; ?>


                                                </div>
                                                <br>
                                                <br>
                                                <?php if ($ticket_rating->rating_text_1 != '') {
                                                    echo "<p>If you think important matters were not covered in this data collection, could you please describe which?</p>";
                                                    echo html_entity_decode($ticket_rating->rating_text_1);


                                                }
                                                if ($ticket_rating->rating_text_2 != '') {
                                                    echo "<p>If there are other general comments you would like to add, you can do so here</p>";

                                                    echo html_entity_decode($ticket_rating->rating_text_2);
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                            endif;
                        endif;

                    endforeach;
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function delete_ticket(ticketID){
        var curr_lang=$('#footer_language_selector').val();
        // alert(curr_lang);
        var msg;
        if(curr_lang=='nor'){
             msg="er du sikker på at du vil slette ?";
        }
        else if(curr_lang=='en'){
             msg="Are you sure you want to delete?";
        }
        var result = confirm(msg);
        if (result) {
            
            // alert(ticketID);

            $.ajax({
                type:'POST',
                url:site_url+'/user/admin_ajax.php?act=delete_ticket',
                data:{   
                    ticketID:ticketID,
                },
                success:function(res){
                    if(res==1){
                        window.location.reload();
                    }
                    
                }
            })
        } 
   
}
</script>
