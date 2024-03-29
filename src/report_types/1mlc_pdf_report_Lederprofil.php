<?php
session_start();

require_once('../vendor/mpdf/vendor/autoload.php');
require_once('../config.php');
require_once('../database.php');
require_once('../vendor/fpdf/fpdf.php');
require_once('../translation.php');
$trans = new Translation($_SESSION['trans']);

$stylesheet = file_get_contents('../css/custompdf.css');
$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];
$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdfConfig = array(
                'mode' => $_SESSION['trans'],
                'default_font_size' => 10,
                'margin_top' => 55,
                'margin_bottom' => 50,
                'margin_left' => 8,
                'margin_right' => 8,
                'margin_footer' => 10,
                'margin_header' => 8,
                'fontDir' => array_merge($fontDirs, [
                         '../fonts',
                    ]),
                'fontdata' => $fontData + [
                    'montserrat' => [
                        'R' => 'Montserrat-Regular.ttf',
                        'I' => 'Montserrat-Italic.ttf',
                        'B' => 'Montserrat-SemiBold.ttf',
                        'BI' => 'Montserrat-SemiBoldItalic.ttf'
                    ]
                ],
                'default_font' => 'montserrat'
            );


$mpdf = new \Mpdf\Mpdf($mpdfConfig);
$mpdf->SetDefaultBodyCSS('line-height', 1.6);

if(!isset($_SESSION['trans'])){
    $Database = new Database();
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if($default_language){
        $_SESSION['trans'] = $default_language['lang_code'];
    }
    else{
        $_SESSION['trans'] = 'en';
    }
}


if(isset($_SESSION['account-type']) && isset($_GET['id']) && $_GET['id'] > 0){
    $Database = new Database();

    $ticket_id = $_GET['id'];
    if(isset($_SESSION['report-format-id'])){
    $report_format_id = $_SESSION['report-format-id'];
    }else{
    $report_format_id = 'Please select format from dropdown';
    die();
    }

    $ticket = $Database->get_data('ticket_id', $ticket_id, 'ticket', true);
    $reports = $Database->get_multiple_data('ticket_id', $ticket_id, 'report');
    
    
    $sql = "SELECT * FROM mlreport_format_contentd WHERE report_format_id='" .$report_format_id . "' AND report_lang_code='" . $_SESSION['trans'] . "';";

    $report_format = $Database->get_connection()->prepare($sql);

    $report_format->execute();

    if ($report_format->rowCount() > 0) {

        $report_format = $report_format->fetch(PDO::FETCH_ASSOC);
    }else{
        $report_format = 'No data Found';
        echo $report_format;
        die();
    }
    
    
      $sqlData = "SELECT * FROM mlreport_format_content WHERE report_format_id = {$report_format_id} AND report_lang_code='{$_SESSION['trans']}'";

        $reportData = $Database->get_connection()->prepare($sqlData);

        $reportData->execute();
        
        if ($reportData->rowCount() < 1) $reportData = false;

        else $reportData = $reportData->fetch(PDO::FETCH_ASSOC);

    /*foreach($reports as $single_report){
        if($single_report['lang_code'] == $_SESSION['trans']){
            $report = $single_report;
        }
    }*/
    
    $report =  json_decode($report_format['report_content'], true);

    function header_footer($report, $user, $company, $trans){
        $html = '';
        $html .= '<htmlpagefooter name="footer">';
        $html .= '<div style="width: 100%; height: 30px;text-align:right;padding-right:30px;padding-bottom:30px;"><span style="font-weight: bold; font-size: 16px; color: black;">{PAGENO}</span>-{nbpg}</div>';
        $html .= '</htmlpagefooter>';
        $html .= '<sethtmlpagefooter name="footer" value="on" />';

        return $html;
    }

    function front_page($report, $user, $company, $ticket, $trans){
        $html = "";
        if($report['front_page']['enabled']){
            $html .="<h2 style='color: black; font-style: italic; top:-200px; float: right;'>KONFIDENSIELT</h2>";
            $html .="<div style='clear: both;'></div>";
            $html .= "<div style='width: 100%; height: 176px;'></div>";
            if($report['front_page']['username']){
                $html .= "<h3 style='text-align: center; color: red;'>".$user['user_name']."</h3>";
            }
            if($report['front_page']['company_name']){
                $company_name = strtoupper($company['company_name']);
                $html .= "<h2 style='width: 100%; height: 10px;'></h2";
                $html .= "<h2 style='text-align: center; color: black; font-style: italic;'>".$company_name."</h2>";
            }

            if($report['front_page']['ticket_id']){
                $html .= "<div style='width: 30%; height: 58%;'></div>";
                $html .= "<div style='align: justify; color: black;margin-left: 35%;'>".$trans->phrase('pdf_text1d').": ".$ticket['ticket_name']."</div>";
            }
            
            if($report['front_page']['page_break1']){
                // $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    // Intro 
    function free_text1($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text1']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text2')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text1']['text'])."</div>";
            if($report['free_text1']['page_break2']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function intro_text($report, $trans, $index){ 
       $color_yellow = '#EBAF33';
        $html = "";
        if($report['intro_text']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text3')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['intro_text']['text'])."</div>";
            if($report['intro_text']['page_break3']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text2($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text2']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text4')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text2']['text'])."</div>";
            if($report['free_text2']['page_break4']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
     function free_text3($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text3']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text5')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text3']['text'])."</div>";
            if($report['free_text3']['page_break5']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text4($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text4']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text6')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text4']['text'])."</div>";
            if($report['free_text4']['page_break6']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text5($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text5']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text7')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text5']['text'])."</div>";
            if($report['free_text5']['page_break7']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text6($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text6']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text8')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text6']['text'])."</div>";
            if($report['free_text6']['page_break8']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text7($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text7']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text9')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text7']['text'])."</div>";
            if($report['free_text7']['page_break9']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text8($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text8']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text10')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text8']['text'])."</div>";
            if($report['free_text8']['page_break10']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text9($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text9']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text11')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text9']['text'])."</div>";
            if($report['free_text9']['page_break11']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text10($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text10']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text11a')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text10']['text'])."</div>";
            if($report['free_text10']['page_break12']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text11($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text11']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text11b')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text11']['text'])."</div>";
            if($report['free_text11']['page_break13']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text12($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text12']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text11c')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text12']['text'])."</div>";
            if($report['free_text12']['page_break14']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text13($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text13']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text11d')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text13']['text'])."</div>";
            if($report['free_text13']['page_break15']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    function free_text14($report, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['free_text14']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_text12')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".htmlspecialchars_decode($report['free_text14']['text'])."</div>";
            if($report['free_text14']['page_break16']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }
    
    
    function radar_graph($report,$trans, $index){
        $color_red = '#DD1D2F';
        $html = "";
        if($report['free_text15']['enabled']){
            $Database = new Database();
            $sql = "SELECT * FROM report_graph WHERE ticket_id='" .$_GET['id'] . "' AND lang_code='" . $_SESSION['trans'] . "';";
            $graph = $Database->get_connection()->prepare($sql);
            $graph->execute();
            
            if ($graph->rowCount() > 0) {
                $graph = $graph->fetch(PDO::FETCH_ASSOC);
                $report_graph =  json_decode($graph['report_content'], true);
                if($report_graph['radar_graph']['graph1']){
                    $html .= "<div style='width: 100%;'>";
                    $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase12')."</h2>";
                    $html .= "</div>";
                    $html .= "<div style=''>".$report['free_text15']['text']."</div>";
                    $html .= "<div style='text-align: center;'><img src='".$report_graph['radar_graph']['graph1']."'></div>";
                }
            }else{
                $graph = false;
            }
            
            if($report['free_text15']['page_break17']){
                $html .= "<pagebreak />";
            }
       
        }
        return $html;
    }
    
    if($report){
        $report_content =  json_decode($report_format['report_content'], true);
        $_GET['id'] = $ticket_id;
        $user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);
        $company = $Database->get_data('company_id', $ticket['ticket_company_id'], 'company', true);
        //$methods = $Database->get_data('method_id', $ticket['ticket_company_id'], 'company', true);
        $index = 1;
        
        $header_footer = header_footer($report_content, $user, $company, $trans);
        $front_page = front_page($report_content, $user, $company, $ticket, $trans);

        $free_text1 = free_text1($report_content, $trans,$index);
        if($free_text1 != "") $index++;
        
        $intro_text = intro_text($report_content, $trans,$index);
        if($intro_text != "") $index++;
        
        $free_text2 = free_text2($report_content, $trans,$index);
        if($free_text2 != "") $index++;
        
        $free_text3 = free_text3($report_content, $trans,$index);
        if($free_text3 != "") $index++;
        
        $free_text4= free_text4($report_content, $trans,$index);
        if($free_text3 != "") $index++;
        
        $free_text5= free_text5($report_content, $trans,$index);
        if($free_text5 != "") $index++;
        
        $free_text6= free_text6($report_content, $trans,$index);
        if($free_text6 != "") $index++;
        
        $free_text7= free_text7($report_content, $trans,$index);
        if($free_text7 != "") $index++;
        
        $free_text8= free_text8($report_content, $trans,$index);
        if($free_text8 != "") $index++;
        
         $free_text9= free_text9($report_content, $trans,$index);
        if($free_text9 != "") $index++;
        
        $free_text10= free_text10($report_content, $trans,$index);
        if($free_text10 != "") $index++;
        
         $free_text11= free_text11($report_content, $trans,$index);
        if($free_text11 != "") $index++;
        
        $free_text12= free_text12($report_content, $trans,$index);
        if($free_text12 != "") $index++;
        
         $free_text13= free_text13($report_content, $trans,$index);
        if($free_text13 != "") $index++;
        
        $free_text14= free_text14($report_content, $trans,$index);
        if($free_text14 != "") $index++;
        
        $radar_graph= radar_graph($report,$trans,$index);
        if($radar_graph != "") $index++;
        
        
        $front_prefix_html = "<html><body style='background-image:url(\"../images/bg-1.png\"); background-image-resize: 6;'>" . $header_footer . "<div>";
        $front_subfix_html = "</div></body></html>";
        
        $content_prefix_html = "<html><body style='background-image:url(\"../images/bakgrunn-mal.png\"); background-image-resize: 6;'>" . $header_footer . "<div>";
        $content_subfix_html = "</div></body></html>";
        
        $normal_prefix_html = "<html><body style='background-image:url(\"../images/bakgrunn-mal.png\"); background-image-resize: 6;'>" . $header_footer . "<div>";
        $normal_subfix_html = "</div></body></html>";
        //Generate PDF
        //file_put_contents('php://stderr', print_r($html, TRUE));
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        
        
        if($report['front_page']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($front_prefix_html.$front_page.$front_subfix_html);
        }
        
        if($report['free_text1']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text1.$normal_subfix_html);
        }
        
        if($report['intro_text']['enabled']){
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
            $mpdf->WriteHTML($normal_prefix_html.$intro_text.$normal_subfix_html);
        }
        
        if($report['free_text2']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text2.$normal_subfix_html);
        }
        
        if($report['free_text3']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text3.$normal_subfix_html);
        }
        
        if($report['free_text4']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text4.$normal_subfix_html);
        }
        
        if($report['free_text5']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text5.$normal_subfix_html);
        }
        
        if($report['free_text6']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text6.$normal_subfix_html);
        }
        
        if($report['free_text7']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text7.$normal_subfix_html);
        }
        
        if($report['free_text8']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text8.$normal_subfix_html);
        }
        
        if($report['free_text9']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text9.$normal_subfix_html);
        }
        
        if($report['free_text10']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text10.$normal_subfix_html);
        }
        
        if($report['free_text11']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text11.$normal_subfix_html);
        }
        
        if($report['free_text12']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text12.$normal_subfix_html);
        }
        
        if($report['free_text13']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text13.$normal_subfix_html);
        }
        
        if($report['free_text14']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$free_text14.$normal_subfix_html);
        }
        
        if($report['free_text15']['enabled']){
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html.$radar_graph.$normal_subfix_html);
        }
        
        $mpdf->Output("Report_".$reportData['report_title']."_".$ticket_id.".pdf", 'D');
        

    }
    else{
        echo $trans->phrase('pdf_report_phrase6');
    }
   
}
else{
    echo $trans->phrase('pdf_report_phrase7');
}
?>
