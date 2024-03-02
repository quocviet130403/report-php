<?php require_once('header.php'); ?>

<!-- <div class="container mt-5"> -->
    <?php
    //Follow-up notification
    if ($_SESSION['account-type'] == 'user') {
        $sql = "SELECT * FROM ticket WHERE ticket_user_id={$_SESSION['account-id']} AND ticket_status='process'";
        $tickets = $Database->get_connection()->prepare($sql);
        $tickets->execute();
        if ($tickets->rowCount() < 1) $tickets = false;
        else $tickets = $tickets->fetchall(PDO::FETCH_ASSOC);

        $expiring_array = array();
        if ($tickets) {
            foreach ($tickets as $ticket) {
                //Checking ticket deadline
                $sql = "SELECT * FROM ticket_deadline WHERE ticket_id={$ticket['ticket_id']} AND end_date < CURDATE() AND viewed=0";
                $ticket_deadline = $Database->get_connection()->prepare($sql);
                $ticket_deadline->execute();
                if ($ticket_deadline->rowCount() > 0) {
                    $expiring_array[$ticket['ticket_id']] = array();
                }

                //Checking question deadline
                $sql = "SELECT * FROM question_deadline WHERE ticket_id={$ticket['ticket_id']} AND end_date < CURDATE() AND viewed=0";
                $question_deadline = $Database->get_connection()->prepare($sql);
                $question_deadline->execute();
                if ($question_deadline->rowCount() > 0) {
                    $question_deadline = $question_deadline->fetchall(PDO::FETCH_ASSOC);
                    if (!isset($expiring_array[$ticket['ticket_id']])) {
                        $expiring_array[$ticket['ticket_id']] = array();
                    }
                    foreach ($question_deadline as $q_deadline) {
                        array_push($expiring_array[$ticket['ticket_id']], $q_deadline['question_id']);
                    }
                }
            }
        }

        if (count($expiring_array) > 0) :
            $expiring_text = '';
            foreach ($expiring_array as $ticket_key => $question_keys) {
                if (count($question_keys) > 0) {
                    $keys = implode(', ', $question_keys);
                    $expiring_text .= " $ticket_key ($keys),";
                } else
                    $expiring_text .= " $ticket_key,";
            }
    ?>
            <div class="row">
                <div class="col-12 user-warning">
                    <h3 class="warning-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $trans->phrase('user_index_phrase1'); ?>
                    </h3>
                    <p class="warning-message">
                        <?php echo $trans->phrase('user_index_phrase2') . " $expiring_text"; ?>
                    </p>
                </div>
            </div>

    <?php
        endif;
    }
    ?>
    <div class="col-sm-12 col-md-9 col-xl-10 edit-profile">
        <div class="profile style-two">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
    
   <?php  if($_SESSION['account-type'] == 'consultant') { ?>
   <h2><?php echo $trans->phrase("consult_pro_text"); ?></h2>
   <?php }elseif($_SESSION['account-type'] == 'user') { ?>
   <h2><?php echo $trans->phrase("user_pro_text"); ?></h2>
   <?php }elseif($_SESSION['account-type'] == 'company') { ?>
   <h2><?php echo $trans->phrase("company_pro_text"); ?></h2>
   <?php }elseif($_SESSION['account-type'] == 'super_admin') { ?>
   <h2><?php echo $trans->phrase("admin_pro_text"); ?></h2>
   <?php }  ?>     
          
    <!-- <?php if (isset($_GET['route']) && $_GET['route'] == 'dashboard')  echo '<h2>'.$trans->phrase("user_company_profile_phrase1").'</h2>'; ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'ticket')  echo '<h2>'.$trans->phrase("user_sidebar_phrase4").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'ticketsummary')  echo '<h2>'.$trans->phrase("user_sidebar_phrase4").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'tickets')  echo '<h2>'.$trans->phrase("user_sidebar_phrase2").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'profile')  echo '<h2>'.$trans->phrase("user_sidebar_phrase5").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'tos')  echo '<h2>'.$trans->phrase("user_sidebar_phrase11").'</h2>'; ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'companies') echo '<h2>'.$trans->phrase("user_sidebar_phrase7").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'consultants') echo '<h2>'.$trans->phrase("user_sidebar_phrase22").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'company_profile'){

        if(isset($_GET['add']) && $_GET['add'] == 'user'){
                echo '<h2>'.$trans->phrase("user_company_profile_phrase19").'</h2>';
        }elseif(isset($_GET['pass']) && $_GET['pass'] == 'change' ){
            echo '<h2>Change Password</h2>';
        }else{
            echo '<h2>'.$trans->phrase("user_sidebar_phrase3").'</h2>'; 
        }


    } 
    ?>-->
   

    <?php if (isset($_GET['route']) && $_GET['route'] == 'company_module')  echo '<h2>'.$trans->phrase("user_company_profile_phrase1").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'admin_profile')  echo '<h2>'.$trans->phrase("user_sidebar_phrase6").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'languages')  echo '<h2>'.$trans->phrase("user_sidebar_phrase10").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'methods')  echo '<h2>'.$trans->phrase("user_sidebar_phrase8").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'category')  echo '<h2>'.$trans->phrase("user_sidebar_phrase17").'</h2>'; ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'report_format')  echo '<h2>Report Formats</h2>'; ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'questions')  echo '<h2>'.$trans->phrase("user_sidebar_phrase9").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'industry')  echo '<h2>'.$trans->phrase("user_sidebar_phrase16").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'support')  echo '<h2>'.$trans->phrase("user_sidebar_phrase13").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'packages') echo '<h2>'.$trans->phrase("user_sidebar_phrase14").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'tracker')  echo '<h2>'.$trans->phrase("user_sidebar_phrase15").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'report_composer') //echo '<h2>'.$trans->phrase("user_company_profile_phrase1").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'feedback')  echo '<h2>'.$trans->phrase("user_tickets_phrase9").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'users')  echo '<h2>'.$trans->phrase("user_sidebar_phrase18").'</h2>'; ; ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'faqs')  echo '<h2>'.$trans->phrase("text_faq").'</h2>';  ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'ticketsnew')  echo '<h2>'.$trans->phrase("user_sidebar_phrase4").'</h2>';  ?>

      </div>
  <div class="col pppropic">

<?php 
   require_once('../database.php');
    $Database = new Database();
    $company = '';
if (($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') && isset($_GET['company_id'])) {
 $company = $Database->get_data('company_id', $_GET['company_id'], 'company', true);
}
 
   

if ($_SESSION['account-type'] == 'company'){
    $company = $Database->get_data('company_id', $_SESSION['account-id'], 'company', true);

}

    if (!empty($company)){
      $logo_image = '<?php echo SITE_URL ?>/images/default-company.png';
    if (file_exists('..<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.png'))
        $logo_image = '<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.png';
    else if (file_exists('..<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpg'))
        $logo_image = '<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpg';
    else if (file_exists('..<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpeg'))
        $logo_image = '<?php echo SITE_URL ?>/images/company_logo/' . $company['company_id'] . '.jpeg';
               
    $sql = "SELECT * FROM tbl_report_request WHERE company_id= {$_SESSION['account-id']} AND status = '0' ";

    $pedning_ticket_data = $Database->get_connection()->prepare($sql);

    $pedning_ticket_data->execute();
    
    $data_pedning_tickets = $pedning_ticket_data->fetchAll(PDO::FETCH_ASSOC);
    $company_profile = $company['upload_company_img'];

?>
    <div class="propic">
          <span class="notification"><i class="fa-solid fa-bell"></i> <span class="badge badge-warning"><?php echo $pedning_ticket_data->rowCount(); ?></span></span>
           <span class="dropdown">
              <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
              </span>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                   <?php 
             if ($data_pedning_tickets) {
              foreach ($data_pedning_tickets as $data_pedning_ticket) {
                $user_id = $data_pedning_ticket['user_id'];
                $user_data = $Database->get_data('user_id', $user_id, 'user', true);
             ?>
                    <li class="nav-item"><a  href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&status='0'" target="_blank" class="nav-link dropdown-item"> <i class="fas fa-stream"></i> <?php echo $user_data['user_name'] ?> sent a request!</a></li>
             <?php 
              }
             }else {
             ?>
             <li class="nav-item"><?php echo $trans->phrase("no_pending_requests"); ?> </li>
             <?php } ?>
              </ul>
            </span>
           <?php if (!empty($company_profile)){ ?>
           <img src="<?php echo SITE_URL ?>/images/company_logo/<?php echo $company_profile; ?>">
           <?php } else { ?>
           <i class="fas fa-user"></i>
           <?php } ?>
          <span class="dropdown">
              <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
              </span>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile" class="nav-link dropdown-item"> <i class="fas fa-building"></i>  <?php echo $trans->phrase("user_profile_phrase13"); ?></a></li>
                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&pass=change" class="nav-link dropdown-item"> <i class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?></a></li>
                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&add=user" class="nav-link dropdown-item"> <i class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase20"); ?></a></li>
                   <li  class="nav-item navbar_signout"><a href="#" class="nav-link dropdown-item"> <i class="fas fa-sign-out-alt"></i>  <?php echo $trans->phrase("user_sidebar_phrase12"); ?></a></li>
              </ul>
            </span>
  </div>
<div class="propic terms-language">
    <div class="input-group footer-lang">
        <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-language"></i></div>
        </div>
        <select id="footer_language_selector" class="form-control form-control-sm">
        <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if ($languages) {
            foreach ($languages as $lang) {
                $language_name = $lang['lang_name'];
                if ($lang['translations']) {
                    $translation = json_decode($lang['translations'], true);
                    if (isset($translation[$_SESSION['trans']])) {
                        $language_name = $translation[$_SESSION['trans']];
                    }
                }
                if ($lang['lang_code'] == $_SESSION['trans']) {
                    echo '<option value="' . $lang['lang_code'] . '" selected>' . $language_name . '</option>';
                } else {
                    echo '<option value="' . $lang['lang_code'] . '">' . $language_name . '</option>';
                }
            }
        }
        ?>
        </select>
        <!--<button id="footer_button_language_selector"><?php echo $trans->phrase("change_lang"); ?></button>-->
    </div>
  </div>
<div class="propic terms-language">
    <div class="footer-menu">
        <ul>
            <a href="<?php echo SITE_URL ?>/user/index.php?route=tos"><li><?php echo $trans->phrase('footer_phrase1'); ?></li></a>
        </ul>   
    </div>
  </div>

<?php }elseif(($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') ){
     $admin_info = $Database->get_data('admin_id', $_SESSION['account-id'], 'admin', true);
    $admin_profile = $admin_info['upload_admin_img'];
          $logo_image = '<?php echo SITE_URL ?>/images/default-company.png';
          $sql = "SELECT * FROM tbl_report_request WHERE status = '0' ";

          $pedning_ticket_data = $Database->get_connection()->prepare($sql);
        
          $pedning_ticket_data->execute();
            
          $data_pedning_tickets = $pedning_ticket_data->fetchAll(PDO::FETCH_ASSOC);
               
                               

?>
    <div class="propic">
                <span class="notification"><i class="fa-solid fa-bell"></i> <span class="badge badge-warning"><?php echo $pedning_ticket_data->rowCount(); ?></span></span>
           <span class="dropdown">
              <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
              </span>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                   <?php 
             if ($data_pedning_tickets) {
              foreach ($data_pedning_tickets as $data_pedning_ticket) {
                $user_id = $data_pedning_ticket['user_id'];
                $user_data = $Database->get_data('user_id', $user_id, 'user', true);
             ?>
                    <li class="nav-item"><a  href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&status='0'" target="_blank" class="nav-link dropdown-item"> <i class="fas fa-stream"></i> <?php echo $user_data['user_name'] ?> sent a request!</a></li>
             <?php 
              }
             }else {
             ?>
             <li class="nav-item"><?php echo $trans->phrase("no_pending_requests"); ?></li>
             <?php } ?>
              </ul>
            </span>
      <?php if (!empty($admin_profile)){ ?>
           <img src="<?php echo SITE_URL ?>/images/admin_logo/<?php echo $admin_profile; ?>">
           <?php } else { ?>
           <i class="fas fa-user"></i>
           <?php } ?>
      <span class="dropdown">
          <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
          </span>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile" class="nav-link dropdown-item"> <i class="fas fa-building"></i>  <?php echo $trans->phrase("user_profile_phrase13"); ?></a></li>
                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile&pass=change" class="nav-link dropdown-item"> <i class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?></a></li>      
               <li  class="nav-item navbar_signout"><a href="#" class="nav-link dropdown-item"> <i class="fas fa-sign-out-alt"></i>  <?php echo $trans->phrase("user_sidebar_phrase12"); ?></a></li>
          </ul>
      </span>



  </div>
<div class="propic terms-language">
    <div class="input-group footer-lang">
        <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-language"></i></div>
        </div>
        <select id="footer_language_selector" class="form-control form-control-sm">
        <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if ($languages) {
            foreach ($languages as $lang) {
                $language_name = $lang['lang_name'];
                if ($lang['translations']) {
                    $translation = json_decode($lang['translations'], true);
                    if (isset($translation[$_SESSION['trans']])) {
                        $language_name = $translation[$_SESSION['trans']];
                    }
                }
                if ($lang['lang_code'] == $_SESSION['trans']) {
                    echo '<option value="' . $lang['lang_code'] . '" selected>' . $language_name . '</option>';
                } else {
                    echo '<option value="' . $lang['lang_code'] . '">' . $language_name . '</option>';
                }
            }
        }
        ?>
        </select>
    </div>
  </div>
<div class="propic terms-language">
    <div class="footer-menu">
        <ul>
            <a href="<?php echo SITE_URL ?>/user/index.php?route=tos"><li><?php echo $trans->phrase('footer_phrase1'); ?></li></a>
        </ul>   
    </div>
  </div>

<?php 
}else if($_SESSION['account-type'] == 'consultant'){
          $logo_image = '<?php echo SITE_URL ?>/images/default-company.png';
          $consultant_info = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
           $consultant_profile = $consultant_info['upload_consultant_img'];
         //  $consultant = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
            $sql = "SELECT * FROM tbl_report_request WHERE consultancy_id = {$_SESSION['account-id']} AND status = '0' ";

            $pedning_ticket_data = $Database->get_connection()->prepare($sql);
        
            $pedning_ticket_data->execute();
            
            $data_pedning_tickets = $pedning_ticket_data->fetchAll(PDO::FETCH_ASSOC);
               
                               //consultant  //user
?>
    <div class="propic">
                     <span class="notification"><i class="fa-solid fa-bell"></i> <span class="badge badge-warning"><?php echo $pedning_ticket_data->rowCount(); ?></span></span>
           <span class="dropdown">
              <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
              </span>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                   <?php 
             if ($data_pedning_tickets) {
              foreach ($data_pedning_tickets as $data_pedning_ticket) {
                $user_id = $data_pedning_ticket['user_id'];
                $user_data = $Database->get_data('user_id', $user_id, 'user', true);
             ?>
                    <li class="nav-item"><a  href="<?php echo SITE_URL ?>/user/index.php?route=request-reports&status='0'" target="_blank" class="nav-link dropdown-item"> <i class="fas fa-stream"></i> <?php echo $user_data['user_name'] ?> sent a request!</a></li>
             <?php 
              }
             }else {
             ?>
             <li class="nav-item"><?php echo $trans->phrase("no_pending_requests"); ?></li>
             <?php } ?>
              </ul>
            </span>
           <?php if (!empty($consultant_profile)){ ?>
           <img src="<?php echo SITE_URL ?>/images/consultant_logo/<?php echo $consultant_profile; ?>">
           <?php } else { ?>
           <i class="fas fa-user"></i>
           <?php } ?>

      <span class="dropdown">
  <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
  </span>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile" class="nav-link dropdown-item"> <i class="fas fa-building"></i>  <?php echo $trans->phrase("user_profile_phrase13"); ?></a></li>
        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile&pass=change" class="nav-link dropdown-item"> <i class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?></a></li>      
       <li  class="nav-item navbar_signout"><a href="#" class="nav-link dropdown-item"> <i class="fas fa-sign-out-alt"></i>  <?php echo $trans->phrase("user_sidebar_phrase12"); ?></a></li>
  </ul>
</span>



  </div>
<div class="propic terms-language">
    <div class="input-group footer-lang">
        <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-language"></i></div>
        </div>
        <select id="footer_language_selector" class="form-control form-control-sm">
        <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if ($languages) {
            foreach ($languages as $lang) {
                $language_name = $lang['lang_name'];
                if ($lang['translations']) {
                    $translation = json_decode($lang['translations'], true);
                    if (isset($translation[$_SESSION['trans']])) {
                        $language_name = $translation[$_SESSION['trans']];
                    }
                }
                if ($lang['lang_code'] == $_SESSION['trans']) {
                    echo '<option value="' . $lang['lang_code'] . '" selected>' . $language_name . '</option>';
                } else {
                    echo '<option value="' . $lang['lang_code'] . '">' . $language_name . '</option>';
                }
            }
        }
        ?>
        </select>
    </div>
  </div>
<div class="propic terms-language">
    <div class="footer-menu">
        <ul>
            <a href="<?php echo SITE_URL ?>/user/index.php?route=tos"><li><?php echo $trans->phrase('footer_phrase1'); ?></li></a>
        </ul>   
    </div>
  </div>

<?php } else if($_SESSION['account-type'] == 'user') {
    $user_info = $Database->get_data('user_id', $_SESSION['account-id'], 'user', true);
    $user_profile = $user_info['user_profile'];
    
            $sql = "SELECT * FROM tbl_report_request WHERE user_id = {$_SESSION['account-id']} AND status = '1' ";

            $pedning_ticket_data = $Database->get_connection()->prepare($sql);
        
            $pedning_ticket_data->execute();
            
            $data_pedning_tickets = $pedning_ticket_data->fetchAll(PDO::FETCH_ASSOC);
?>
 <div class="propic">
                     <span class="notification"><i class="fa-solid fa-bell"></i> <span class="badge badge-warning"><?php echo $pedning_ticket_data->rowCount(); ?></span></span>
           <span class="dropdown">
              <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
              </span>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                   <?php 
             if ($data_pedning_tickets) {
              foreach ($data_pedning_tickets as $data_pedning_ticket) {
             ?>
                    <li class="nav-item"><a  href="<?php echo SITE_URL ?>/user/index.php?route=ticket&req_id=<?php echo $data_pedning_ticket['id']; ?>&page=summarize" target="_blank" class="nav-link dropdown-item"> <i class="fas fa-stream"></i> <?php echo $data_pedning_ticket['permisson_ticket_title'] ?> Approved! Please start !</a></li>
             <?php 
              }
             }else {
             ?>
             <li class="nav-item"><?php echo $trans->phrase("no_pending_requests"); ?></li>
             <?php } ?>
              </ul>
            </span>
            
            
            <?php if (!empty($user_profile)){ ?>
           <img src="<?php echo SITE_URL ?>/images/profilepic/<?php echo $user_profile; ?>">
           <?php } else { ?>
           <i class="fas fa-user"></i>
           <?php } ?>

      <span class="dropdown">
  <span class="dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
  </span>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile" class="nav-link dropdown-item"> <i class="fas fa-building"></i>  <?php echo $trans->phrase("user_profile_phrase13"); ?></a></li>
        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile&pass=change" class="nav-link dropdown-item"> <i class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?></a></li>      
       <li  class="nav-item navbar_signout"><a href="#" class="nav-link dropdown-item"> <i class="fas fa-sign-out-alt"></i>  <?php echo $trans->phrase("user_sidebar_phrase12"); ?></a></li>
  </ul>
</span>



  </div>
<div class="propic terms-language">
    <div class="input-group footer-lang">
        <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-language"></i></div>
        </div>
        <select id="footer_language_selector" class="form-control form-control-sm">
        <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if ($languages) {
            foreach ($languages as $lang) {
                $language_name = $lang['lang_name'];
                if ($lang['translations']) {
                    $translation = json_decode($lang['translations'], true);
                    if (isset($translation[$_SESSION['trans']])) {
                        $language_name = $translation[$_SESSION['trans']];
                    }
                }
                if ($lang['lang_code'] == $_SESSION['trans']) {
                    echo '<option value="' . $lang['lang_code'] . '" selected>' . $language_name . '</option>';
                } else {
                    echo '<option value="' . $lang['lang_code'] . '">' . $language_name . '</option>';
                }
            }
        }
        ?>
        </select>
    </div>
  </div>
<div class="propic terms-language">
    <div class="footer-menu">
        <ul>
            <a href="<?php echo SITE_URL ?>/user/index.php?route=tos"><li><?php echo $trans->phrase('footer_phrase1'); ?></li></a>
        </ul>   
    </div>
  </div>
<?php } ?>

  </div>
  </div>
    <div class="terms-language-mobile row">
<div class="propic terms-language terms-block">
    <div class="footer-menu">
        <ul>
            <a href="<?php echo SITE_URL ?>/user/index.php?route=tos"><li><?php echo $trans->phrase('footer_phrase1'); ?></li></a>
        </ul>   
    </div>
  </div>
    <div class="propic terms-language lang-block">
    <div class="input-group footer-lang">
        <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-language"></i></div>
        </div>
        <select id="footer_language_selector" class="form-control form-control-sm">
        <?php
        $languages = $Database->get_multiple_data('lang_active', 1, 'language');
        if ($languages) {
            foreach ($languages as $lang) {
                $language_name = $lang['lang_name'];
                if ($lang['translations']) {
                    $translation = json_decode($lang['translations'], true);
                    if (isset($translation[$_SESSION['trans']])) {
                        $language_name = $translation[$_SESSION['trans']];
                    }
                }
                if ($lang['lang_code'] == $_SESSION['trans']) {
                    echo '<option value="' . $lang['lang_code'] . '" selected>' . $language_name . '</option>';
                } else {
                    echo '<option value="' . $lang['lang_code'] . '">' . $language_name . '</option>';
                }
            }
        }
        ?>
        </select>
    </div>
  </div>
</div>
      


  </div>
</div>

<div class="container-fluid">
    <!-- Loading desired pages -->
    <?php if (isset($_GET['route']) && $_GET['route'] == 'dashboard') require_once('dashboard.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'ticket') require_once('ticket.php'); ?>
     <?php if (isset($_GET['route']) && $_GET['route'] == 'request-reports') require_once('request_reports.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'ticketsummary') require_once('ticketsummary.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'tickets') require_once('tickets.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'profile') require_once('profile.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'tos') require_once('tos.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'companies') require_once('companies.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'consultants') require_once('consultants.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'company_profile')  require_once('company_profile.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'company_module') require_once('company_module.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'admin_profile') require_once('admin_profile.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'consultant_profile') require_once('consultant_profile.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'languages') require_once('languages.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'methods') require_once('methods.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'category') require_once('category.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'report_format') require_once('report_format.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'mreport_format') require_once('mreport_format.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'questions') require_once('questions.php'); ?>
    <!-- Lukman code -->
    <?php if (isset($_GET['route']) && $_GET['route'] == 'responder') require_once('responder.php'); ?>
	<?php if (isset($_GET['route']) && $_GET['route'] == 'consultant') require_once('consultant.php'); ?>
	<?php if (isset($_GET['route']) && $_GET['route'] == 'graph_composer') require_once('graph_composer.php'); ?>
	<?php if (isset($_GET['route']) && $_GET['route'] == 'mlreport_composer') require_once('mlreport_composer.php'); ?>
	<!-- Lukman code -->
    <?php if (isset($_GET['route']) && $_GET['route'] == 'industry') require_once('industry.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'support') require_once('support.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'packages') require_once('package.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'tracker') require_once('tracker.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'report_composer') require_once('report_composer.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'mreport_common_composer') require_once('mreport_common_composer.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'report_format_composer') require_once('report_format_composer.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'report_format_dcomposer') require_once('report_format_cdemo.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'feedback') require_once('feedback.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'users') require_once('user.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'faqs') require_once('faq.php'); ?>
    <?php if (isset($_GET['route']) && $_GET['route'] == 'ticketsnew') require_once('ticketsnew.php'); ?> 
</div>
</div>

<!-- </div> -->
<?php require_once('footer.php'); ?>