<import>

  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/bootstrap.min.css">

  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/icons.min.css">

  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/app.min.css">

</import>

<style type="text/css">

  .dropdown-menu.show{
    transform: translate(-156px, 22px) !important;
  }

  .form-control-sm {
    min-height: calc(1.5em + (0.5rem + 2px));
    padding: 0.25rem 0.5rem;
    padding-top: 0.25rem;
    padding-right: 0.5rem;
    padding-bottom: 0.25rem;
    padding-left: 0.5rem;
    font-size: .875rem;
    border-radius: 0.2rem;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    padding-top: 0.375rem;
    padding-right: 0.75rem;
    padding-bottom: 0.375rem;
    padding-left: 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.footer-menu a {
    color: #0d6efd;
    text-decoration: underline !important;
}
</style>

<?php

//if (($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')) :

  require_once('../database.php');

  $Database = new Database();

  $admin = $Database->get_data('admin_id', $_SESSION['account-id'], 'admin', true);

  $user = $Database->get_multiple_data(false, false, 'user');

  $tickets = $Database->get_multiple_data(false, false, 'ticket');

  $supports = $Database->get_multiple_data(false, false, 'support');

  $companies = $Database->get_multiple_data(false, false, 'company');

  $Methods = $Database->get_multiple_data(false, false, 'method');

  $Category = $Database->get_multiple_data(false, false, 'category');

  $Questions = $Database->get_multiple_data(false, false, 'question');

  $Packages = $Database->get_multiple_data(false, false, 'package');

  $tracker = $Database->get_data_by_query('SELECT COUNT(*)  AS count, user_role, MONTH(access_time) AS date FROM tracker WHERE YEAR(access_time) = YEAR(CURDATE()) GROUP BY MONTH (access_time), user_role DESC');

  $citys = $Database->get_data_by_query('SELECT COUNT(*) AS count, user_location FROM tracker WHERE YEAR(access_time) = YEAR(CURDATE()) AND user_location != "Unknown" GROUP BY user_location ORDER BY COUNT DESC;');

//endif;

?>

<style>

  .dashboard-body {

    font-size: .8125rem !important;

    font-weight: 400 !important;

  }



  .avatar-title i {

    color: white;

  }



  .arrow-down {

    display: block !important;

  }



  .arrow-down:after {

    display: none !important;

  }

</style>

<div class="row dashboard-body">

  <div class="col-xl-4">

    <div class="card overflow-hidden">

      <div class="bg-primary bg-soft">

        <div class="row">

          <div class="col-7">

            <div class="text-primary p-3">

              <h5 class="text-primary"><?php echo $trans->phrase("dashboard:welcom_message"); ?></h5>

              <p>Nøgd Dashboard</p>

            </div>

          </div>

          <div class="col-5 align-self-end">

            <img src="<?php echo SITE_URL; ?>/assets/images/profile-img.png" alt="" class="img-fluid">

          </div>

        </div>

      </div>
<?php if (($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')) : ?>
      <div class="card-body pt-0">

        <div class="row">



          <div class="col-sm-12">

            <div class="pt-4">

              <div class="row">

                <div class="col-4">

                  <p class="text-muted mb-0">Name</p>

                </div>

                <div class="col-8">

                  <h5><?php echo $admin['admin_name']; ?></h5>

                </div>

              </div>

              <div class="row">

                <div class="col-4">

                  <p class="text-muted mb-0">Mail</p>

                </div>

                <div class="col-8">

                  <h5><?php echo $admin['admin_email']; ?></h5>

                </div>

              </div>

              <div class="mt-4">

                <a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile" class="btn btn-primary waves-effect waves-light btn-sm"><?php echo $trans->phrase("user_sidebar_phrase6"); ?> <i class="mdi mdi-arrow-right ms-1"></i></a>

              </div>

            </div>

          </div>

        </div>

      </div>
      
      <?php endif; ?>

    </div>

    <!--<div class="card">

      <div class="card-body">

        <h4 class="card-title mb-4">Top Cities of Tracker</h4>



        <div class="text-center">

          <div class="mb-4">

            <i class="bx bx-map-pin text-primary display-4"></i>

          </div>

          <h3 id="top-city-acts"></h3>

          <p id="top-city-name"></p>

        </div>



        <div class="table-responsive mt-4">

          <table class="table align-middle table-nowrap">

            <tbody id="top-city-content">

            </tbody>

          </table>

        </div>

      </div>

    </div>-->

  </div>

  <div class="col-xl-8">

    <div class="row">
	
	
	 <?php if($_SESSION['account-type'] == 'company')  { 
	   $company_tickets = $Database->get_multiple_data('ticket_company_id', $_SESSION['account-id'], 'ticket');

	   $sql = "SELECT * FROM `ticket` WHERE ticket_company_id = '". $_SESSION['account-id']  ."' AND ticket_status = 'process'";
	   $ticket_process_data = $Database->get_connection()->prepare($sql);

       $ticket_process_data->execute();
	   $data_process = $ticket_process_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_support = "SELECT * FROM `support` WHERE support_user_id = '". $_SESSION['account-id']  ."'";
	   $support_process_data = $Database->get_connection()->prepare($sql_support);

       $support_process_data->execute();
	   $support_process = $support_process_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_report_request = "SELECT * FROM `tbl_report_request` WHERE company_id = '". $_SESSION['account-id']  ."' AND status = '0' ";
	   $report_request_data = $Database->get_connection()->prepare($sql_report_request);

       $report_request_data->execute();
	   $report_request_process = $report_request_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_com_user = "SELECT * FROM `user` WHERE user_company_id = '". $_SESSION['account-id']  ."'";
	   $com_user_data = $Database->get_connection()->prepare($sql_com_user);

      $com_user_data->execute();
	   $com_user_process = $com_user_data->fetchAll(PDO::FETCH_NUM);
	 ?>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("user_sidebar_phrase2"); ?></p>

                  <h4 class="mb-0"><?php echo $company_tickets ? count($company_tickets) : ''; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets&view=process">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:analyses_in_progress"); ?></p>

                  <h4 class="mb-0"><?php echo $data_process ? count($data_process) : ''; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=support">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Support</p>

                  <h4 class="mb-0"><?php echo $support_process ? count($support_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:analysis_requests"); ?></p>

                  <h4 class="mb-0"><?php echo $report_request_process ? count($report_request_process) : ''; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=users">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:users"); ?></p>

                  <h4 class="mb-0"><?php echo $com_user_process ? count($com_user_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  
	  
	  <?php } elseif($_SESSION['account-type'] == 'user') { 
	   $company_tickets = $Database->get_multiple_data('ticket_user_id', $_SESSION['account-id'], 'ticket');
	   $sql = "SELECT * FROM `ticket` WHERE ticket_user_id = '". $_SESSION['account-id']  ."' AND ticket_status = 'process'";
	   $ticket_process_data = $Database->get_connection()->prepare($sql);

       $ticket_process_data->execute();
	   $data_process = $ticket_process_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_support = "SELECT * FROM `support` WHERE support_user_id = '". $_SESSION['account-id']  ."'";
	   $support_process_data = $Database->get_connection()->prepare($sql_support);

       $support_process_data->execute();
	   $support_process = $support_process_data->fetchAll(PDO::FETCH_NUM);
	  ?>
	  
      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("user_sidebar_phrase2"); ?></p>

                  <h4 class="mb-0"><?php echo $company_tickets ? count($company_tickets) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets&view=process">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:analyses_in_progress"); ?></p>

                  <h4 class="mb-0"><?php echo $data_process ? count($data_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=support">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Support</p>

                  <h4 class="mb-0"><?php echo $support_process ? count($support_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  <?php } else if($_SESSION['account-type'] == 'consultant') {
		$consultant = $Database->get_data('consultant_id', $_SESSION['account-id'], 'consultant', true);
                $consultant_companies = array();
                if (!empty($consultant['consultant_companies'])) {
                    $consultant_companies = explode(',', $consultant['consultant_companies']);
                    if (is_array($consultant_companies))
                        $ids_str = "'" . implode("','", $consultant_companies) . "'";
                    $companies = $Database->get_data_multiple('company_id', $ids_str, 'company');  
				}
				
				$sql_support = "SELECT * FROM `support` WHERE support_user_id = '". $_SESSION['account-id']  ."'";
	   $support_process_data = $Database->get_connection()->prepare($sql_support);

       $support_process_data->execute();
	   $support_process = $support_process_data->fetchAll(PDO::FETCH_NUM);
	   
	    
	   $sql_report_request = "SELECT * FROM `tbl_report_request` WHERE consultancy_id = '". $_SESSION['account-id']  ."' AND status = '0' ";
	   $report_request_data = $Database->get_connection()->prepare($sql_report_request);

       $report_request_data->execute();
	   $report_request_process = $report_request_data->fetchAll(PDO::FETCH_NUM);
	  ?>
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=companies">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Companies</p>

                  <h4 class="mb-0"><?php echo $companies ? count($companies) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	   <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=support">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Support</p>

                  <h4 class="mb-0"><?php echo $support_process ? count($support_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	    <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:analysis_requests"); ?></p>

                  <h4 class="mb-0"><?php echo $report_request_process ? count($report_request_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  <?php }elseif($_SESSION['account-type'] == 'super_admin') { 
	  
	   $company_tickets = $Database->get_multiple_data(false, false, 'ticket');
	   $sql = "SELECT * FROM `ticket` WHERE ticket_status = 'process'";
	   $ticket_process_data = $Database->get_connection()->prepare($sql);

       $ticket_process_data->execute();
	   $data_process = $ticket_process_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_support = "SELECT * FROM `support`";
	   $support_process_data = $Database->get_connection()->prepare($sql_support);

       $support_process_data->execute();
	   $support_process = $support_process_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_report_request = "SELECT * FROM `tbl_report_request` WHERE status = '0' ";
	   $report_request_data = $Database->get_connection()->prepare($sql_report_request);

       $report_request_data->execute();
	   $report_request_process = $report_request_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_com_user = "SELECT * FROM `user`";
	   $com_user_data = $Database->get_connection()->prepare($sql_com_user);

       $com_user_data->execute();
	   $com_user_process = $com_user_data->fetchAll(PDO::FETCH_NUM);
	   
	   
	   $sql_con = "SELECT * FROM `consultant` WHERE consultant_status = 'active'";
	   $con_data = $Database->get_connection()->prepare($sql_con);

       $con_data->execute();
	   $con_process = $con_data->fetchAll(PDO::FETCH_NUM);
	   
	   $sql_com = "SELECT * FROM `company` WHERE company_status = 'active'";
	   $com_data = $Database->get_connection()->prepare($sql_com);

       $com_data->execute();
	   $com_process = $com_data->fetchAll(PDO::FETCH_NUM);
	 ?>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("user_sidebar_phrase2"); ?></p>

                  <h4 class="mb-0"><?php echo $company_tickets ? count($company_tickets) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=tickets&view=process">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:analyses_in_progress"); ?></p>

                  <h4 class="mb-0"><?php echo $data_process ? count($data_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=support">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Support</p>

                  <h4 class="mb-0"><?php echo $support_process ? count($support_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium"><?php echo $trans->phrase("dashboard:card:analysis_requests"); ?></p>

                  <h4 class="mb-0"><?php echo $report_request_process ? count($report_request_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	   <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=companies">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Companies</p>

                  <h4 class="mb-0"><?php echo $com_process ? count($com_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=consultants">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Consultants</p>

                  <h4 class="mb-0"><?php echo $con_process ? count($con_process) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>
	  
	  
	  <?php } ?>

        <!--  <div class="col-md-4">

        <div class="card mini-stats-wid">

          <div class="card-body">

            <div class="media">

              <div class="media-body">

                <p class="text-muted fw-medium">Users</p>
                <p class="text-muted fw-medium">Analyses in Progress</p>

                <h4 class="mb-0"><?php echo $user ? count($user) : "0"; ?></h4>

              </div>



              <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">

                <span class="avatar-title">

                  <i class="bx bx-user font-size-24"></i>

                </span>

              </div>

            </div>

          </div>

        </div>

      </div>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=companies">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Companies</p>
                  <p class="text-muted fw-medium">Unanswered</p>

                  <h4 class="mb-0"><?php echo $companies ? count($companies) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-building font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=methods">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Methods</p>
                  <p class="text-muted fw-medium">Analysis requestes</p>

                  <h4 class="mb-0"><?php echo $Methods ? count($Methods) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-star-of-david font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=category">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Companies</p>

                  <h4 class="mb-0"><?php echo $Category ? count($Category) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-stream font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>

  <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=questions">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Questions</p>

                  <h4 class="mb-0"><?php echo $Questions ? count($Questions) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-question font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=packages">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Packages</p>

                  <h4 class="mb-0"><?php echo $Packages ? count($Packages) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>

      <div class="col-md-4">

        <a href="<?php echo SITE_URL ?>/user/index.php?route=support">

          <div class="card mini-stats-wid">

            <div class="card-body">

              <div class="media">

                <div class="media-body">

                  <p class="text-muted fw-medium">Support</p>

                  <h4 class="mb-0"><?php echo $supports ? count($supports) : "0"; ?></h4>

                </div>



                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">

                  <span class="avatar-title rounded-circle bg-primary">

                    <i class="fas fa-cubes font-size-24"></i>

                  </span>

                </div>

              </div>

            </div>

          </div>

        </a>

      </div>

    </div>-->

    <!-- end row -->



    <!--<div class="card">

      <div class="card-body">

        <div class="d-sm-flex flex-wrap">

          <h4 class="card-title mb-4">Tracker</h4>

        </div>



        <div id="tracker-chart" class="apex-charts" dir="ltr"></div>

      </div>

    </div>-->

  </div>

</div>





<script src="<?php echo SITE_URL; ?>/assets/libs/jquery/jquery.min.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/libs/metismenu/metisMenu.min.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/libs/simplebar/simplebar.min.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/libs/node-waves/waves.min.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/libs/apexcharts/apexcharts.min.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/js/pages/dashboard.init.js"></script>

<script src="<?php echo SITE_URL; ?>/assets/js/app.js"></script>



<script>

  var trackers = [];

  var citys = <?php echo json_encode($citys) ?>;

  showchart();

  showtopcity();



  function showchart() {

    trackers = <?php echo json_encode($tracker) ?>;

    var admin = [];

    var company = [];

    var user = [];

    for (j = 1; j < 13; j++) {

      var admin_flag = true;

      var company_flag = true;

      var user_flag = true;

      for (i = 0; i < trackers.length; i++) {

        if (trackers[i].date == j) {

          if (trackers[i].user_role == 'company') {

            company.push(trackers[i].count);

            company_flag = false;

          } else if (trackers[i].user_role == 'user') {

            user.push(trackers[i].count);

            user_flag = false;

          } else {

            admin.push(trackers[i].count);

            admin_flag = false;

          }

        }

      }

      if (admin_flag) {

        admin.push(0);

      }

      if (company_flag) {

        company.push(0);

      }

      if (user_flag) {

        user.push(0);

      }

    }





    var options = {

      chart: {

        height: 360,

        type: "bar",

        stacked: !0,

        toolbar: {

          show: !1

        },

        zoom: {

          enabled: !0

        }

      },

      plotOptions: {

        bar: {

          horizontal: !1,

          columnWidth: "15%",

          endingShape: "rounded"

        }

      },

      dataLabels: {

        enabled: !1

      },

      series: [{

        name: "admin",

        data: admin

      }, {

        name: "company",

        data: company

      }, {

        name: "user",

        data: user

      }],

      xaxis: {

        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]

      },

      colors: ["#556ee6", "#f1b44c", "#34c38f"],

      legend: {

        position: "bottom"

      },

      fill: {

        opacity: 1

      }

    };



    var chart = new ApexCharts(document.querySelector("#tracker-chart"), options);

    chart.render();

  }



  function showtopcity() {

    if (citys == null || citys.length == 0) {

      $('#top-city-acts').append(0);

      $('#top-city-name').append('no city');

      return;

    }

    var html = '';

    for (i = 0; i < citys.length; i++) {

      if (i == 5)

        break;



      var btn = 'bg-primary';

      if (i == 0)

        btn = 'bg-primary';

      else if (i == 1)

        btn = 'bg-success';

      else if (i == 2)

        btn = 'bg-warning';

      else if (i == 3)

        btn = 'bg-info';

      else if (i == 4)

        btn = 'bg-danger';



      var ele = `<tr>

                <td style="width: 30%">

                  <p class="mb-0">` + citys[i].user_location + `</p>

                </td>

                <td style="width: 25%">

                  <h5 class="mb-0">` + citys[i].count + `</h5>

                </td>

                <td>

                  <div class="progress bg-transparent progress-sm">

                    <div class="progress-bar ` + btn + ` rounded" role="progressbar" style="width: ` + parseInt(citys[i].count) * 100 / parseInt(citys[0].count) + `%" aria-valuenow="` + citys[i].count / parseInt(citys[0].count) + `" aria-valuemin="0" aria-valuemax="100"></div>

                  </div>

                </td>

              </tr>`;

      html = html + ele;

    }

    $('#top-city-content').append(html);

    $('#top-city-acts').append(citys[0].count);

    $('#top-city-name').append(citys[0].user_location);

  }

</script>