<?php
//Update Ticket Summary
if(isset($_GET['id'])) {

    require_once('../database.php');
    $Database = new Database();
    $ticket = null;
    $ticket = $Database->get_data('ticket_id', $_GET['id'], 'ticket', true);   
       
if($ticket) {
    if($_SESSION['account-type'] == 'user') {
?>
    <div class="row user-content-row">
        <div class="col-12 all-tickets">
            <div class="all-tickets-widget-title"><?php echo $trans->phrase("user_ticket_phrase56"); ?></div>
        <div class="row user-content-row">
            <div class="col-12">
                <form>
                    <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase57"); ?></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><?php echo $trans->phrase("user_ticket_phrase1"); ?></div>
                        </div>
                        <input type="text" id="ticket_name" class="form-control" 
                            value="<?php echo $ticket['ticket_name']; ?>">
                    </div>
                </form>
            </div>
        </div>
        <div class="row user-content-row">
            <div class="col-12">
                <form>
                <label class="ticket-label"><?php echo $trans->phrase("user_ticket_phrase25"); ?></label>
                    <textarea id="ticket_summary"><?php echo $ticket['ticket_summary']; ?></textarea>
                </form>
            </div>
        </div>
        <div class="row user-content-row">
            <button id="update_ticket_summary" class="btn btn-success mb-3 ml-3" 
                data-ticket_id="<?php echo $ticket['ticket_id']?>">
                <?php echo $trans->phrase("text_save_and_continue"); ?>
            </button>
        </div>
    </div>
<?php
  } // end check condition user type
 }// end ticket\
}// end isset['id']
?>