

<div class="faqs">

<?php
  
  if($_SESSION['trans'] == 'en') {
    require_once('faqs/faqen.php');
  }
  elseif($_SESSION['trans'] == 'dan') {
    require_once('faqs/faqdan.php');
  }
  elseif($_SESSION['trans'] == 'nor') {
    require_once('faqs/faqnor.php');
  }
  elseif($_SESSION['trans'] == 'span') {
    require_once('faqs/faqspan.php');
  }
  elseif($_SESSION['trans'] == 'gen') {
    require_once('faqs/faqgen.php');
  }
  elseif($_SESSION['trans'] == 'swe') {
    require_once('faqs/faqswe.php');
  }

?>
</div>
