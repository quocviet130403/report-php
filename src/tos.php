
<?php require_once('header.php'); ?>
    

    <div class="main-tos-ctn">       
        <div class="tos-title"><?php echo $trans->phrase('main_tos_phrase1'); ?>
        </div>
        
        <div class="tos-data">       
        <?php
            $tos_data = $Database->get_terms_condition($_SESSION['trans']);
            echo ($tos_data) ? htmlspecialchars_decode( $tos_data['tos_content'], ENT_QUOTES) : '';
        ?>
        </div>
        <div class="w3-button-holder">
            <a href="javascript:void(0)" onclick="history.go(-1);" class="btn btn-info btn-small mt-2 mb-2">
                <?php echo $trans->phrase('main_tos_phrase2'); ?>
            </a>
        </div>
    </div>

<?php //require_once('footer.php'); ?>

</div>
    <script src="<?php echo JQUERY; ?>"></script>
    <script src="<?php echo BOOTSTRAP_JS; ?>"></script>
    <script src="/js/index.js"></script>
</body>
</html>