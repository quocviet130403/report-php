<nav class="navbar navbar-dark side-profile m-menu">

    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar"
                aria-controls="offcanvasDarkNavbar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="offcanvas offcanvas-start text-bg-dark my-sidebar" tabindex="-1" id="offcanvasDarkNavbar"
             aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <span class="proavtpic">
                 <a href="https://beta.nogd.no/user/index.php?route=dashboard" class="logo">
                    <img src="<?php echo SITE_URL ?>/images/logo-transparent.png" alt="Logo">
                </a>
                </span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <div class="d-flex flex-column flex-shrink-0 text-white">

                    <ul class="nav nav-pills flex-column mb-auto">
                         <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=dashboard"
                                                    class="nav-link  <?php if ($_GET['route'] == 'dashboard') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("dashboard_text"); ?></a></li>
                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tickets"
                                                class="nav-link  <?php if ($_GET['route'] == 'tickets') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-ticket-alt"></i> <?php echo $trans->phrase("user_sidebar_phrase2"); ?>
                            </a>
                        </li>
                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports"
                                                    class="nav-link  <?php if ($_GET['route'] == 'request-reports') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("request_report_all"); ?></a></li>
                        <!-- Special Company menu -->
                        <?php if ($_SESSION['account-type'] == 'company') : ?>

                            <li class="nav-item"><a href="#" class="nav-link" aria-current="page"> <i
                                            class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase3"); ?>
                                </a>
                                <ul class="text-small shadow">
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile"
                                                            class="nav-link  <?php if ($_GET['route'] == 'dashboard' && !isset($_GET['pass']) && empty($_GET['pass']) && !isset($_GET['add']) && empty($_GET['add'])) echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                        </a></li>
                                    <li class="nav-item"><a
                                                href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&pass=change"
                                                class="nav-link  <?php if ($_GET['route'] == 'dashboard' && !isset($_GET['add']) && empty($_GET['add']) && isset($_GET['pass']) && $_GET['pass'] == 'change') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                        </a></li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&add=user"
                                                            class="nav-link  <?php if ($_GET['route'] == 'dashboard' && isset($_GET['add']) && $_GET['add'] == 'user' && !isset($_GET['pass']) && empty($_GET['pass'])) echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase20"); ?>
                                        </a></li>
                                    </li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&add=user"
                                                            class="nav-link  <?php if ($_GET['route'] == 'dashboard' && isset($_GET['add']) && $_GET['add'] == 'user' && !isset($_GET['pass']) && empty($_GET['pass'])) echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase23"); ?>
                                        </a></li>    
                                    <li class="nav-item"><a
                                                href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&tfa=change"
                                                class="nav-link  <?php if ($_GET['route'] == 'dashboard' && empty($_GET['add']) && $_GET['tfa'] == 'change') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                        </a></li>

                                </ul>
                            </li>
                            

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=users"
                                                    class="nav-link  <?php if ($_GET['route'] == 'users') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-users"></i> <?php echo $trans->phrase("user_sidebar_phrase18"); ?>
                                </a></li>

                        <?php endif; ?>

                        <!-- Special User menu -->

                        <?php if ($_SESSION['account-type'] == 'user') : ?>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=summarize1"
                                                    class="nav-link  <?php if ($_GET['route'] == 'summarize') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-plus-square"></i> <?php echo $trans->phrase("user_sidebar_phrase4"); ?>
                                </a></li>

                            <li class="nav-item"><a href="#" class="nav-link" aria-current="page"> <i
                                            class="fas fa-user"></i> <?php echo $trans->phrase("user_sidebar_phrase5"); ?>
                                </a>
                                <ul class="text-small shadow">
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile"
                                                            class="nav-link 22 <?php if ($_GET['route'] == 'profile' && empty($_GET['pass'])) echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                        </a></li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile&pass=change"
                                                            class="nav-link  <?php if ($_GET['route'] == 'profile' && $_GET['pass'] == 'change') echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                        </a></li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile&tfa=change"
                                                            class="nav-link  <?php if ($_GET['route'] == 'profile' && $_GET['tfa'] == 'change') echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                        </a></li>
                                </ul>
                            </li>

                        <?php endif; ?>

                        <!-- General Admin menu -->

                        <?php if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') : ?>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports"
                                                    class="nav-link  <?php if ($_GET['route'] == 'request-reports') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("request_report_all"); ?></a></li>
                                                    
                            <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=dashboard"
                                                    class="nav-link  <?php if ($_GET['route'] == 'dashboard') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> Dashboard</a></li>-->

                            <li class="nav-item"><a href="#" class="nav-link" aria-current="page"> <i
                                            class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase6"); ?>
                                </a>
                                <ul class="text-small shadow">
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile"
                                                            class="nav-link  <?php if ($_GET['route'] == 'admin_profile' && empty($_GET['pass'])) echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile&pass=change"
                                                            class="nav-link  <?php if ($_GET['route'] == 'admin_profile' && $_GET['pass'] == 'change') echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile&tfa=change"
                                                            class="nav-link  <?php if ($_GET['route'] == 'admin_profile' && $_GET['tfa'] == 'change') echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=companies"
                                                    class="nav-link  <?php if ($_GET['route'] == 'companies') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase7"); ?>
                            </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultants"
                                                    class="nav-link  <?php if ($_GET['route'] == 'consultants') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase22"); ?>
                            </a></li>

                        <?php endif; ?>

                        <!-- Special Super Admin menu -->

                        <?php if ($_SESSION['account-type'] == 'super_admin') : ?>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=methods"
                                                    class="nav-link  <?php if ($_GET['route'] == 'methods') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-star-of-david"></i> <?php echo $trans->phrase("user_sidebar_phrase8"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=category"
                                                    class="nav-link  <?php if ($_GET['route'] == 'category') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> <?php echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li>
                            <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=report_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'report_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> Main Report<?php //echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li> -->
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=mreport_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'mreport_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> <?php echo $trans->phrase("multi_lang_report"); ?>
                                </a></li>

                           <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                    class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                                </a></li>-->
                                
                                   <!-- Lukman code -->
                             <li class="nav-item"><a href="#" class="nav-link <?php if ($_GET['route'] == 'questions' || $_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a>
                            <ul class="submenu text-small shadow">
                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a></li>
							
							 <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=responder"
                                                    class="nav-link  <?php if ($_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> <?php echo $trans->phrase("responder_text"); ?>
                                </a></li>
                            </ul>
                        </li>
								
							<!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant"
                                                    class="nav-link  <?php if ($_GET['route'] == 'consultant') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> Consultant
                                </a></li>-->  
                                <!-- Lukman code -->

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=industry"
                                                    class="nav-link  <?php if ($_GET['route'] == 'industry') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-industry"></i> <?php echo $trans->phrase("user_sidebar_phrase16"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=languages"
                                                    class="nav-link  <?php if ($_GET['route'] == 'languages') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-language"></i> <?php echo $trans->phrase("user_sidebar_phrase10"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=packages"
                                                    class="nav-link  <?php if ($_GET['route'] == 'packages') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-cubes"></i> <?php echo $trans->phrase("user_sidebar_phrase14"); ?>
                                </a></li>

                           <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tracker"
                                                    class="nav-link  <?php if ($_GET['route'] == 'tracker') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-chart-line"></i> <?php echo $trans->phrase("user_sidebar_phrase15"); ?>
                                </a></li>-->

                        <?php endif; ?>

                        <!-- Consultant menu -->

                        <?php if ($_SESSION['account-type'] == 'consultant') : ?>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports"
                                                    class="nav-link  <?php if ($_GET['route'] == 'request-reports') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("request_report_all"); ?></a></li>
                            <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=dashboard"
                                                    class="nav-link  <?php if ($_GET['route'] == 'dashboard') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> Dashboard</a></li>-->
                                <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=report_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'report_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> Main Report<?php //echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li> -->
                            <li class="nav-item"><a href="#" class="nav-link" aria-current="page"> <i
                                            class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase21"); ?>
                                </a>
                                <ul class="text-small shadow">
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile"
                                                            class="nav-link  <?php if ($_GET['route'] == 'consultant_profile' && empty($_GET['pass'])) echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile&pass=change"
                                                            class="nav-link  <?php if ($_GET['route'] == 'consultant_profile' && $_GET['pass'] == 'change') echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                        </a>
                                    </li>
									
                                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile&tfa=change"
                                                            class="nav-link  <?php if ($_GET['route'] == 'consultant_profile' && $_GET['tfa'] == 'change') echo 'active'; else echo ''; ?>"
                                                            aria-current="page"> <i
                                                    class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=companies"
                                                    class="nav-link  <?php if ($_GET['route'] == 'companies') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase7"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=methods"
                                                    class="nav-link  <?php if ($_GET['route'] == 'methods') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-star-of-david"></i> <?php echo $trans->phrase("user_sidebar_phrase8"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=category"
                                                    class="nav-link  <?php if ($_GET['route'] == 'category') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> <?php echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li>
         
                           <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                    class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                                </a></li>-->
                                <!-- Lukman code -->
                             <li class="nav-item"><a href="#" class="nav-link <?php if ($_GET['route'] == 'questions' || $_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a>
                            <ul class="submenu text-small shadow">
                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a></li>
							
							 <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=responder"
                                                    class="nav-link  <?php if ($_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> <?php echo $trans->phrase("responder_text"); ?>
                                </a></li>
                            </ul>
                        </li>
								
							<!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant"
                                                    class="nav-link  <?php if ($_GET['route'] == 'consultant') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> Consultant
                                </a></li>-->  
                                <!-- Lukman code -->

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=industry"
                                                    class="nav-link  <?php if ($_GET['route'] == 'industry') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-industry"></i> <?php echo $trans->phrase("user_sidebar_phrase16"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=languages"
                                                    class="nav-link  <?php if ($_GET['route'] == 'languages') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-language"></i> <?php echo $trans->phrase("user_sidebar_phrase10"); ?>
                                </a></li>

                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=packages"
                                                    class="nav-link  <?php if ($_GET['route'] == 'packages') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-cubes"></i> <?php echo $trans->phrase("user_sidebar_phrase14"); ?>
                                </a></li>

                            <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tracker"
                                                    class="nav-link  <?php if ($_GET['route'] == 'tracker') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-chart-line"></i> <?php echo $trans->phrase("user_sidebar_phrase15"); ?>
                                </a></li>-->

                        <?php endif; ?>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tos"
                                                class="nav-link  <?php if ($_GET['route'] == 'tos') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-info-circle"></i> <?php echo $trans->phrase("user_sidebar_phrase11"); ?>
                            </a></li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=support"
                                                class="nav-link  <?php if ($_GET['route'] == 'support') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-headset"></i> <?php echo $trans->phrase("user_sidebar_phrase13"); ?>
                            </a></li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=faqs"
                                                class="nav-link  <?php if ($_GET['route'] == 'faqs') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-hands-helping"></i> <?php echo $trans->phrase("text_faq"); ?></a>
                        </li>

                        <li class="nav-item navbar_signout"><a href="javascript:void(0);" class="nav-link"
                                                               aria-current="page"> <i
                                        class="fas fa-sign-out-alt"></i> <?php echo $trans->phrase("user_sidebar_phrase12"); ?>
                            </a></li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</nav>


<nav class="side-profile h-sticky">

    <div class="my-sidebar">
        <div class="offcanvas-header">
<span class="proavtpic">
 <a href="https://beta.nogd.no/user/index.php?route=dashboard" class="logo">
    <img src="<?php echo SITE_URL ?>/images/logo-transparent.png" alt="Logo">
</a>
</span>
        </div>

        <div class="offcanvas-body">

            <div class="d-flex flex-column flex-shrink-0 text-white">

                <ul class="nav nav-pills flex-column mb-auto">
                     <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=dashboard"
                                                    class="nav-link  <?php if ($_GET['route'] == 'dashboard') { echo 'active'; } else { echo '';} ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("dashboard_text"); ?></a></li>
                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tickets"
                                            class="nav-link  <?php if ($_GET['route'] == 'tickets') echo 'active'; else echo ''; ?>"
                                            aria-current="page"> <i
                                    class="fas fa-ticket-alt"></i> <?php echo $trans->phrase("user_sidebar_phrase2"); ?>
                        </a></li>


                    <!-- Special Company menu -->

                    <?php if ($_SESSION['account-type'] == 'company') : ?>

                            <li class="nav-item">
                                <a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=naticket" class="nav-link  <?php if ($_GET['route'] == 'ticket' && $_GET['page'] == 'naticket') echo 'active'; else echo ''; ?>" aria-current="page">
                                    <i class="fas fa-plus-square"></i> <?php echo $trans->phrase("company_add_ticket_text"); ?>
                                </a>
                            </li>
							 <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=report_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'report_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> Main Report<?php //echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li> -->
													<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports"
                                                    class="nav-link  <?php if ($_GET['route'] == 'request-reports') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("request_report_all"); ?></a></li>
                                                    <?php if($_GET['route'] == 'company_profile') { ?>
                        <li class="nav-item">
                            <a href="#"
                                                class="nav-link  <?php if ($_GET['route'] == 'company_profile') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase3"); ?>
                            </a>
                            <ul class="submenu shadow">
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile"
                                                        class="nav-link " aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&pass=change"
                                                        class="nav-link " aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=company_profile&tfa=change"
                                                        class="nav-link " aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                    </a></li>

                            </ul>
                        </li>
                        <?php } ?>

                        <li class="nav-item"><a href="#"
                                                class="nav-link  <?php if ($_GET['route'] == 'users') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-users"></i> <?php echo $trans->phrase("user_sidebar_phrase18"); ?>
                            </a>
                            <ul class="submenu shadow">
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=users"
                                                        class="nav-link " aria-current="page"> <i
                                                class="fas fa-users"></i><?php echo $trans->phrase("all_user_text"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=users&add=user"
                                                        class="nav-link" aria-current="page"> <i
                                                class="fas fa-users"></i><?php echo $trans->phrase("user_sidebar_phrase20"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=users&invite=user"
                                                        class="nav-link" aria-current="page"> <i
                                                class="fas fa-users"></i> <?php echo $trans->phrase("user_invite"); ?>
                                    </a></li>

                            </ul>
                        </li>

                    <?php endif; ?>


                    <!-- Special User menu -->

                    <?php if ($_SESSION['account-type'] == 'user') : ?>

                           <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=summarize1"
                                                class="nav-link  <?php if ($_GET['route'] == 'ticket') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-plus-square"></i> <?php echo $trans->phrase("user_sidebar_phrase4"); ?>
                            </a></li>-->
							<?php $user_id = $_SESSION['account-id']; 
							$user_info = $Database->get_data('user_id', $user_id, 'user', true);
							if($user_info['approve_per'] == 1){
							?>
                             <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=nticket"
                                                class="nav-link  <?php if ($_GET['route'] == 'ticket' && $_GET['page'] == 'nticket') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-plus-square"></i> <?php echo $trans->phrase("user_sidebar_phrase4"); ?>
                            </a></li>
							<?php } else { ?>

							<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=naticket"
                                                class="nav-link  <?php if ($_GET['route'] == 'ticket' && $_GET['page'] == 'naticket') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-plus-square"></i> <?php echo $trans->phrase("user_sidebar_phrase4"); ?>
                            </a></li>
							<?php } ?>
                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=rtickets"
                                                    class="nav-link  <?php if ($_GET['route'] == 'ticket' && $_GET['page'] == 'rtickets') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-ticket-alt"></i> <?php echo $trans->phrase("ticket_request"); ?>
                            </a></li>
							 <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=report_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'report_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> Main Report<?php //echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li> -->

                        <li class="nav-item"><a href="#"
                                                class="nav-link "
                                                aria-current="page"> <i
                                        class="fas fa-user"></i> <?php echo $trans->phrase("user_sidebar_phrase5"); ?>
                            </a>
                            <ul class="submenu text-small shadow <?php if ($_GET['route'] == 'profile') echo 'myown'; else echo ''; ?>">
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile" class="nav-link <?php if ($_GET['route'] == 'profile' && !isset($_GET['pass']) && !isset($_GET['tfa']) ) echo 'active'; else echo ''; ?>" 
                                                        aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile&pass=change"
                                                        class="nav-link <?php if ($_GET['route'] == 'profile' && $_GET['pass'] == 'change') echo 'active'; else echo ''; ?>" aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=profile&tfa=change"
                                                        class="nav-link <?php if ($_GET['route'] == 'profile' && $_GET['tfa'] == 'change') echo 'active'; else echo ''; ?>"  aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                    </a></li>
                            </ul>
                        </li>

<style>
    .myown {
        display: block !important;
        opacity: 1 !important;
        z-index: 1!important;
        transform: translateY(0%)!important;
    }
</style>
                    <?php endif; ?>


                    <!-- General Admin menu -->

                    <?php if ($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin') : ?>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports"
                                                    class="nav-link  <?php if ($_GET['route'] == 'request-reports') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("request_report_all"); ?>
                        <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=dashboard"
                                                class="nav-link <?php if ($_GET['route'] == 'dashboard') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i class="fas fa-home"></i> Dashboard</a></li>-->

                        <li class="nav-item"><a href="#"
                                                class="nav-link <?php if ($_GET['route'] == 'admin_profile') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase6"); ?>
                            </a>
                            <ul class="submenu text-small shadow">
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile" class="nav-link"
                                                        aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile&pass=change"
                                                        class="nav-link" aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=admin_profile&tfa=change"
                                                        class="nav-link" aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                    </a></li>
                            </ul>
                        </li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=companies"
                                                class="nav-link <?php if ($_GET['route'] == 'companies') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase7"); ?>
                        </a></li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultants"
                                                class="nav-link <?php if ($_GET['route'] == 'consultants') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase22"); ?>
                        </a></li>

                    <?php endif; ?>


                    <!-- Special Super Admin menu -->

                    <?php if ($_SESSION['account-type'] == 'super_admin') : ?>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=methods"
                                                class="nav-link  <?php if ($_GET['route'] == 'methods') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-star-of-david"></i> <?php echo $trans->phrase("user_sidebar_phrase8"); ?>
                            </a></li>
                          

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=category"
                                                class="nav-link  <?php if ($_GET['route'] == 'category') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-stream"></i> <?php echo $trans->phrase("user_sidebar_phrase17"); ?>
                            </a></li>
                           <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=report_format"
                                                class="nav-link  <?php if ($_GET['route'] == 'report_format') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-stream"></i>Main report
                            </a></li> -->
                             <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=mreport_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'mreport_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> <?php echo $trans->phrase("multi_lang_report"); ?>
                                </a></li>

                        <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a></li>-->
                            
                               <!-- Lukman code -->
                              <li class="nav-item"><a href="#" class="nav-link <?php if ($_GET['route'] == 'questions' || $_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a>
                            <ul class="submenu text-small shadow">
                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a></li>
							
							 <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=responder"
                                                    class="nav-link  <?php if ($_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> <?php echo $trans->phrase("responder_text"); ?>
                                </a></li>
                            </ul>
                        </li>
								
							<!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant"
                                                    class="nav-link  <?php if ($_GET['route'] == 'consultant') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> Consultant
                                </a></li>-->   
                                <!-- Lukman code -->

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=industry"
                                                class="nav-link  <?php if ($_GET['route'] == 'industry') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-industry"></i> <?php echo $trans->phrase("user_sidebar_phrase16"); ?>
                            </a></li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=languages"
                                                class="nav-link  <?php if ($_GET['route'] == 'languages') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-language"></i> <?php echo $trans->phrase("user_sidebar_phrase10"); ?>
                            </a></li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=packages"
                                                class="nav-link  <?php if ($_GET['route'] == 'packages') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-cubes"></i> <?php echo $trans->phrase("user_sidebar_phrase14"); ?>
                            </a></li>

                       <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tracker"
                                                class="nav-link  <?php if ($_GET['route'] == 'tracker') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-chart-line"></i> <?php echo $trans->phrase("user_sidebar_phrase15"); ?>
                            </a></li>-->

                    <?php endif; ?>

                    <!-- Consultant menu -->

                    <?php if ($_SESSION['account-type'] == 'consultant') : ?>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL ?>/user/index.php?route=ticket&page=naticket" class="nav-link  <?php if ($_GET['route'] == 'ticket' && $_GET['page'] == 'naticket') echo 'active'; else echo ''; ?>" aria-current="page">
                                <i class="fas fa-plus-square"></i> <?php echo $trans->phrase("company_add_ticket_text"); ?>
                            </a>
                        </li>
                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=request-reports"
                                                    class="nav-link  <?php if ($_GET['route'] == 'request-reports') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i class="fas fa-home"></i> <?php echo $trans->phrase("request_report_all"); ?></a></li>
                                <!-- <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=report_format"
                                                    class="nav-link  <?php if ($_GET['route'] == 'report_format') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-stream"></i> Main Report<?php //echo $trans->phrase("user_sidebar_phrase17"); ?>
                                </a></li> -->
                        <li class="nav-item"><a href="#"
                                                class="nav-link <?php if ($_GET['route'] == 'consultant_profile') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase21"); ?>
                            </a>
                            <ul class="submenu text-small shadow">
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile" class="nav-link"
                                                        aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_profile_phrase13"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile&pass=change"
                                                        class="nav-link" aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("user_sidebar_phrase19"); ?>
                                    </a></li>
                                <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant_profile&tfa=change"
                                                        class="nav-link" aria-current="page"> <i
                                                class="fas fa-building"></i><?php echo $trans->phrase("index_phrase29"); ?>
                                    </a></li>
                            </ul>
                        </li>

                        <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=companies"
                                                class="nav-link  <?php if ($_GET['route'] == 'companies') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-building"></i> <?php echo $trans->phrase("user_sidebar_phrase7"); ?>
                            </a></li>
                        <!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a></li>-->
                            
                               <!-- Lukman code -->
                             <li class="nav-item"><a href="#" class="nav-link <?php if ($_GET['route'] == 'questions' || $_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-user-shield"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a>
                            <ul class="submenu text-small shadow">
                            <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=questions"
                                                class="nav-link  <?php if ($_GET['route'] == 'questions') echo 'active'; else echo ''; ?>"
                                                aria-current="page"> <i
                                        class="fas fa-question"></i> <?php echo $trans->phrase("user_sidebar_phrase9"); ?>
                            </a></li>
							
							 <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=responder"
                                                    class="nav-link  <?php if ($_GET['route'] == 'responder') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> <?php echo $trans->phrase("responder_text"); ?>
                                </a></li>
                            </ul>
                        </li>
								
								<!--<li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=consultant"
                                                    class="nav-link  <?php if ($_GET['route'] == 'consultant') echo 'active'; else echo ''; ?>"
                                                    aria-current="page"> <i
                                            class="fas fa-question"></i> Consultant
                                </a></li>-->    
                                <!-- Lukman code -->

                    <?php endif; ?>

                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=tos"
                                            class="nav-link  <?php if ($_GET['route'] == 'tos') echo 'active'; else echo ''; ?>"
                                            aria-current="page"> <i
                                    class="fas fa-info-circle"></i> <?php echo $trans->phrase("user_sidebar_phrase11"); ?>
                        </a></li>

                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=support"
                                            class="nav-link  <?php if ($_GET['route'] == 'support') echo 'active'; else echo ''; ?>"
                                            aria-current="page"> <i
                                    class="fas fa-headset"></i> <?php echo $trans->phrase("user_sidebar_phrase13"); ?>
                        </a></li>

                    <li class="nav-item"><a href="<?php echo SITE_URL ?>/user/index.php?route=faqs"
                                            class="nav-link <?php if ($_GET['route'] == 'faqs') echo 'active'; else echo ''; ?>"
                                            aria-current="page"> <i
                                    class="fas fa-hands-helping"></i> <?php echo $trans->phrase("text_faq"); ?></a></li>

                    <li class="nav-item navbar_signout"><a href="#" class="nav-link" aria-current="page"> <i
                                    class="fas fa-sign-out-alt"></i> <?php echo $trans->phrase("user_sidebar_phrase12"); ?>
                        </a></li>
                </ul>

            </div>


        </div>
    </div>
</nav>
