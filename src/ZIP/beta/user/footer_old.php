    <!-- </div>End of main container fluid -->


    <div id="divNotiPopup" class="modal">
    </div>

    <!--Footer-->

    <script src="<?php echo JQUERY; ?>"></script>
    <script src="<?php echo JQUERY_UI; ?>"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo BOOTSTRAP_JS; ?>"></script>
    <script src="<?php echo TINYMCE; ?>"></script>
    <script src="<?php echo CHART_JS; ?>"></script>

    <!-- <script type="text/javascript"  src="/vendor/datatable/js/jquery.dataTables.min.js"></script> -->
    <!-- <script type="text/javascript"  src="/vendor/datatable/js/dataTables.rowGroup.min.js"></script> -->
    <script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_URL ?>/js/custom-datatable.js"></script>
    <script type="text/javascript" src="<?php echo SITE_URL; ?>/js/toastr/toastr.min.js"></script>

    <script src="<?php echo SITE_URL ?>/js/user.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            if ($('#unanseredCatQues').length) {
                $('#unanseredCatQues').hide();
            }

            if ($('#btnNextQgroup').length) {
                $('#btnNextQgroupAlt').hide();
            }

            t = $('#ticket-list').DataTable({
                "bLengthChange": false,
                order: [
                    [5, 'dec'],
                    [6, 'desc'],
                    [2, 'asc']
                ],
                "orderClasses": false,
                rowGroup: {
                    dataSrc: [5, 6]
                },
                columnDefs: [{
                    targets: [5, 6],
                    visible: false
                }],
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
            $isExist = $Database->get_notification($_SESSION['account-id'], 'Company', 'TC', false);
        } elseif ($_SESSION['account-type'] == 'user') {
            $isExist = $Database->get_notification($_SESSION['account-id'], 'User', 'TC', false);
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
    </body>

    </html>