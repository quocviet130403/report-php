<?php
if(isset($_GET['id'])):
    require_once('../database.php');
    require_once('../database.php');
    $Database = new Database();
    $ticket = null;
    $question_response = null;
    $ticketReview = null;
    $reviewOptions = null;
    if(isset($_GET['id'])){
       
        $ticket = $Database->get_data('ticket_id', $_GET['id'], 'ticket', true);
        if($ticket)  {
            $review = json_decode($ticket['ticket_review'], true);
             
            if(isset($review['review_status'])){
                $reviewStatus =  $review['review_status'];
                $reviewOptions = explode(",",$reviewStatus);
            }
            if(isset($review['review_text'])){
                $ticketReview = $review['review_text'];
            }
        }
    }
?>

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
        <form>
            <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase30"); ?></label><br>
            <div class="form-check">
                <input class="form-check-input review-check" 
                    type="checkbox" value="Anger" disabled="true"
                    <?php if(in_array("Anger",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                    id="review_check_1" >
                <label class="form-check-label" for="review_check_1">
                    <?php echo $trans->phrase("user_ticket_phrase31"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check" 
                    type="checkbox" value="Fear" disabled="true"
                    <?php if(in_array("Fear",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>                    
                    id="review_check_2">
                <label class="form-check-label" for="review_check_2">
                    <?php echo $trans->phrase("user_ticket_phrase32"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                 type="checkbox" value="Anxiety" disabled="true"
                    <?php if(in_array("Anxiety",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                  id="review_check_3">
                <label class="form-check-label" for="review_check_3">
                    <?php echo $trans->phrase("user_ticket_phrase33"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                     type="checkbox" value="Loss"
                     disabled="true"
                    <?php if(in_array("Loss",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                      id="review_check_4">
                <label class="form-check-label" for="review_check_4">
                    <?php echo $trans->phrase("user_ticket_phrase34"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                 type="checkbox" value="Sadness"
                 disabled="true"
                    <?php if(in_array("Sadness",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                     id="review_check_5">
                <label class="form-check-label" for="review_check_5">
                    <?php echo $trans->phrase("user_ticket_phrase35"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check" 
                type="checkbox" value="Resignation"  disabled="true"
                    <?php if(in_array("Resignation",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                id="review_check_6">
                <label class="form-check-label" for="review_check_6">
                    <?php echo $trans->phrase("user_ticket_phrase36"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check" 
                type="checkbox" value="Guilt" 
                disabled="true"
                    <?php if(in_array("Guilt",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                id="review_check_7">
                <label class="form-check-label" for="review_check_7">
                    <?php echo $trans->phrase("user_ticket_phrase37"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                 type="checkbox" value="Shame" 
                 disabled="true"
                    <?php if(in_array("Shame",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                id="review_check_8">
                <label class="form-check-label" for="review_check_8">
                    <?php echo $trans->phrase("user_ticket_phrase38"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                 type="checkbox" value="Jealousy" 
                 disabled="true"
                    <?php if(in_array("Jealousy",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                 id="review_check_9">
                <label class="form-check-label" for="review_check_9">
                    <?php echo $trans->phrase("user_ticket_phrase39"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                 type="checkbox" value="Enthusiasm"
                 disabled="true"
                    <?php if(in_array("Enthusiasm",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                  id="review_check_10">
                <label class="form-check-label" for="review_check_10">
                    <?php echo $trans->phrase("user_ticket_phrase40"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check"
                 type="checkbox" value="Tenderness"
                 disabled="true"
                    <?php if(in_array("Tenderness",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                 id="review_check_11">
                <label class="form-check-label" for="review_check_11">
                    <?php echo $trans->phrase("user_ticket_phrase41"); ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input review-check" 
                type="checkbox" value="Hope"
                disabled="true"
                    <?php if(in_array("Hope",$reviewOptions)==1) { 
                        echo "checked='checked'"; } ?>
                 id="review_check_12">
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
            <textarea readonly="true" rows="20" cols="120" id="ticket_review"><?php echo $ticketReview ?></textarea>
        </form>
    </div>
    <div class="w3-button-holder">
            <a href="#" onclick="history.go(-1);" class="btn btn-info btn-small mt-2 mb-2">
                <?php echo $trans->phrase('main_tos_phrase2'); ?>
            </a>
        </div>     
</div>
</div>

    
<?php
    endif;
?>


