    <!-- </div>End of main container fluid -->


    <div id="divNotiPopup" class="modal">
    </div>

    <!--Footer-->

    <script src="<?php echo JQUERY; ?>"></script>

    <script src="<?php echo JQUERY_UI; ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="<?php echo TINYMCE; ?>"></script>
    <!--<script src="<?php // echo CHART_JS; 
                        ?>"></script>-->
    <script src="<?php echo CHART_JS ?>"></script>
    <script src="<?php echo CHART_JS_LABEL_PLUGIN ?>"></script>
    <!-- <script type="text/javascript"  src="/vendor/datatable/js/jquery.dataTables.min.js"></script> -->
    <!-- <script type="text/javascript"  src="/vendor/datatable/js/dataTables.rowGroup.min.js"></script> -->
    <script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_URL ?>/js/custom-datatable.js"></script>
    <script type="text/javascript" src="<?php echo SITE_URL; ?>/js/toastr/toastr.min.js"></script>

    <script src="<?php echo SITE_URL ?>/js/user.js?v=<?= time() ?>"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            if ($('#unanseredCatQues').length) {
                $('#unanseredCatQues').hide();
            }

            if ($('#btnNextQgroup').length) {
                $('#btnNextQgroupAlt').hide();
            }

            t = $('#ticket-list').DataTable({
                "bLengthChange": true,
                "ordering": false, // Disable sorting for all columns
    "order": [
        [5, 'desc'], // Column 5 (zero-based index) in descending order
        [6, 'desc'], // Column 6 in descending order
        [2, 'asc']   // Column 2 in ascending order
    ],
    "orderClasses": false,

                "oLanguage": {
                    "sSearch": "<?php echo $trans->phrase("user_tickets_phrase10"); ?>"
                },
                "language": {
                    "info": "<?php echo $trans->phrase("message_no_of_rec"); ?>",
                    "paginate": {
                        "previous": "<?php echo $trans->phrase("text_previous"); ?>",
                        "next": "<?php echo $trans->phrase("text_next"); ?>"
                    }
                }
            });
            q = document.querySelector('[data-kt-customer-table-filter="search"]');
            if (q != null) {
                q.addEventListener("keyup", function(e) {
                    console.log(e.target.value);
                    t.search(e.target.value).draw();
                });
            }
        });
    </script>
    <?php
    if (isset($_SESSION['account-type'])) {
        $isExist = false;
        if ($_SESSION['account-type'] == 'company') {
            // $isExist = $Database->get_notification($_SESSION['account-id'], 'Company', 'TC', false);
        } elseif ($_SESSION['account-type'] == 'user') {
            // $isExist = $Database->get_notification($_SESSION['account-id'], 'User', 'TC', false);
        }
        echo $isExist;
        if ($isExist > 0) {
    ?>
            <script type="text/javascript">
                var htmlText = '<div class="modal-content">';
                htmlText += '<div class="container">';
                htmlText += '<iframe class="odal-iframe" src="<?php echo SITE_URL ?>/user/tosmodal.php"></iframe>';
                htmlText += '<div class="button-holder">';
                htmlText += '<button id="accept-tc" class="btn btn-primary"><?php echo $trans->phrase('text_accept'); ?></button>';
                htmlText +=
                    '&nbsp;<button id="close-tc" class="btn btn-info"><?php echo $trans->phrase('text_close'); ?></button></div>';
                htmlText += '</div></div></div>';
                $('#divNotiPopup').html(htmlText).show();
            </script>
        <?php
        } else {
        ?>
            <script type="text/javascript">
                //document.getElementById('divNotiPopup').style.display='none';
                $('#divNotiPopup').hide();
            </script>
    <?php
        }
    }
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <script>
        $(function() {
            var container = $("#notificationsDropdownMenu");
            var countSpan = $("#notificationsCount");
            var lastTimestamp = 0;
            var page = 1;

            // Initialize lastNotificationId to null at the beginning
            var lastNotificationId = null;

            // Fetch notifications and set the last timestamp
            function fetchNotifications() {
                var data = {
                    sign: 'get_notifications',
                    lastTimestamp: lastTimestamp,
                    page: page,
                    lastNotificationId: lastNotificationId // Pass the lastNotificationId to the server
                };

                $.post("/option_server.php", data, function(data) {
                    var notifications = JSON.parse(data);

                    if (notifications.length > 0) {

                        var hasUnreadNotifications = false;
                        var count = 0;

                        $("#0notification").remove();

                        $.each(notifications, function(index, notification) {

                            var message = notification.message;
                            var timestamp = notification.timestamp;
                            var isRead = notification.is_read;
                            var isAdminRead = notification.is_admin_read;

                            // Create the notification element
                            var element = $('<a href="#" class="dropdown-item"></a>');
                            <?php if ($_SESSION['account-type'] == 'super_admin') { ?>
                                if (isAdminRead === 0) {
                                    element.addClass('font-weight-bold');
                                }
                            <?php } else { ?>
                                if (isRead === 0) {
                                    element.addClass('font-weight-bold');
                                }
                            <?php } ?>

                            element.append('<i class="fas fa-envelope mr-2"></i> ' + message);
                            element.append('<span class="float-right text-muted text-sm">' + timeAgo(timestamp) + '</span>');

                            // Append the notification to the dropdown
                            container.prepend(element);

                            // Update the last timestamp and notification id
                            if (notification.notification_id >= lastNotificationId) {
                                lastTimestamp = timestamp;
                                lastNotificationId = notification.notification_id;
                            }

                            // Update the notifications count
                            <?php if ($_SESSION['account-type'] == 'super_admin') { ?>
                                if (isAdminRead == 0) {
                                    hasUnreadNotifications = true;
                                    count++;
                                }
                            <?php } else { ?>
                                if (isRead == 0) {
                                    hasUnreadNotifications = true;
                                    count++;
                                }
                            <?php } ?>
                        });

                        // Update the notifications count
                        countSpan.text(count);
                        if (hasUnreadNotifications) {
                            countSpan.show();
                        } else {
                            countSpan.hide();
                        }
                    } else {
                        // Create the notification element
                        var element = $('<a id="0notification" href="#" class="dropdown-item"></a>');

                        element.append('<i class="fas fa-envelope mr-2"></i> 0 Notifications');

                        container.html(element);
                    }
                });
            }

            $("#notification").on('click', function(e) {
                e.preventDefault();

                // Send AJAX request to mark all notifications as read
                $.post("/option_server.php", {
                    sign: 'mark_notifications_read'
                }, function(data) {
                    // Update the UI to mark all notifications as read
                    container.find('.notification-dot').remove();
                    container.find('.message').removeClass('font-weight-bold');
                    countSpan.hide();
                });
            });

            // Call fetchNotifications immediately when the page loads
            fetchNotifications();

            // Call fetchNotifications every 30 seconds
            setInterval(fetchNotifications, 30000);

            function timeAgo(timestamp) {
                var seconds = Math.floor((new Date() - new Date(timestamp)) / 1000);

                var interval = Math.floor(seconds / 31536000);

                if (interval >= 1) {
                    return interval + " years ago";
                }
                interval = Math.floor(seconds / 2592000);
                if (interval >= 1) {
                    return interval + " months ago";
                }
                interval = Math.floor(seconds / 86400);
                if (interval >= 1) {
                    return interval + " days ago";
                }
                interval = Math.floor(seconds / 3600);
                if (interval >= 1) {
                    return interval + " hours ago";
                }
                interval = Math.floor(seconds / 60);
                if (interval >= 1) {
                    return interval + " minutes ago";
                }
                return "just now";
            }

        });
    </script>


    </body>

    </html>