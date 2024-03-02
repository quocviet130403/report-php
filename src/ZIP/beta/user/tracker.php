<?php
if($_SESSION['account-type'] == 'super_admin'):
    require_once('../database.php');
    $Database = new Database();
?>

<div class="card">
    <div class="card-body p-3">
    <div class="col-12 tracker">
        <div class="tracker-ctn">
        <?php
            $tracker = $Database->get_multiple_data(false, false, 'tracker', null, true, 'access_time DESC', 1000);
            if($tracker):
        ?>
            <table class="table table-striped text-gray-600">
                <thead style="position: sticky; top: 0;">
                    <tr>
                        <th scope="col"><?php echo $trans->phrase('user_tracker_phrase3'); ?></th>
                        <th scope="col"><?php echo $trans->phrase('user_tracker_phrase4'); ?></th>
                        <th scope="col"><?php echo $trans->phrase('user_tracker_phrase5'); ?></th>
                        <th scope="col"><?php echo $trans->phrase('user_tracker_phrase6'); ?></th>
                        <th scope="col"><?php echo $trans->phrase('user_tracker_phrase7'); ?></th>
                        <th scope="col"><?php echo $trans->phrase('user_tracker_phrase8'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($tracker as $activity):
                    ?>
                    <tr>
                        <th scope="row"><?php echo $activity['user_id']; ?></th>
                        <td><?php echo $activity['user_role']; ?></td>
                        <td><?php echo $activity['user_ip']; ?></td>
                        <td><?php echo $activity['user_location']; ?></td>
                        <td><?php echo $activity['access_time']; ?></td>
                        <td><?php echo $activity['user_action']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php if($tracker): ?>
        <button id="clear_tracking" class="btn btn-danger mt-3 mb-3">
            <i class="fas fa-trash"></i> <?php echo $trans->phrase('user_tracker_phrase9'); ?>
        </button>
        <?php endif; ?>
    </div>
<?php
else:
    echo $trans->phrase('user_tracker_phrase2');
endif;
?>
</div>
</div>