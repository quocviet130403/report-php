<?php 
session_start();
require_once('config.php');
require_once('imports.php');
require_once('database.php');
require_once('translation.php');
$Database = new Database();

if(!isset($_SESSION['trans'])){
    
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if($default_language){
        $_SESSION['trans'] = $default_language['lang_code'];
    }
    else{
        $_SESSION['trans'] = 'en';
    }
}

  $trans = new Translation($_SESSION['trans']);

  if(isset($_GET['ticket_id'])){
    
      $ticket_id = $_GET['ticket_id'];

      $ticket = $Database->get_data('ticket_id', $_GET['ticket_id'], 'ticket', true);

      if(!$ticket) {
        header('location: '.SITE_URL);
      }
      
      $ticket_company = $Database->get_data('company_id', $ticket['ticket_company_id'], 'company', true);

      
      $logo_image = SITE_URL.'/images/default-company.png';
      if(file_exists('images/company_logo/'.$ticket_company['company_id'].'.png')):
          $logo_image = '/images/company_logo/'.$ticket_company['company_id'].'.png';
      elseif(file_exists('images/company_logo/'.$ticket_company['company_id'].'.jpg')):
          $logo_image = '/images/company_logo/'.$ticket_company['company_id'].'.jpg';
      elseif(file_exists('images/company_logo/'.$ticket_company['company_id'].'.jpeg')):
          $logo_image = '/images/company_logo/'.$ticket_company['company_id'].'.jpeg';
      endif;

  }else{
      header('location: '.SITE_URL);
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    
    <title><?php echo $trans->phrase('responder:thanks_page:headline'); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
</head>
  <body class="d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <!-- <img src="/images/responder_logo.png" alt="Thank You" width="250" style="margin-bottom: 30px;" /> -->
          <img src="<?php echo $logo_image; ?>" alt="<?php echo $trans->phrase('responder:thanks_page:headline'); ?>" width="150" style="margin-bottom: 30px;" />
          <h1 class="display-4"><?php echo $trans->phrase('responder:thanks_page:headline'); ?></h1>
          <p class="lead"><?php echo $trans->phrase('responder:thanks_page:peragraph')?></p>
        </div>
      </div>
    </div>
  </body>
</html>
