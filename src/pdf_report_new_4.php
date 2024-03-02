<?php
session_start();

require_once('vendor/mpdf/vendor/autoload.php');
require_once('config.php');
require_once('database.php');
require_once('vendor/fpdf/fpdf.php');
require_once('translation.php');
$trans = new Translation($_SESSION['trans']);

$stylesheet = file_get_contents('css/custom.css');
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
                        __DIR__ . '/fonts',
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

    $ticket = $Database->get_data('ticket_id', $ticket_id, 'ticket', true);
    $reports = $Database->get_multiple_data('ticket_id', $ticket_id, 'report');

    $report = false;
    foreach($reports as $single_report){
        if($single_report['lang_code'] == $_SESSION['trans']){
            $report = $single_report;
        }
    }

    function header_footer($report, $user, $company, $trans){
        $html = "";
        $html .= '<htmlpagefooter name="footer">';
        $html .= "<div style='float: right; text-align:right; width; 50px;'><span style='font-weight: bold; font-height: 16px; color: black;'>{PAGENO}</span>-{nbpg}</div>";
        $html .= "</htmlpagefooter>";
        $html .= '<sethtmlpagefooter name="footer" value="on" />';
        return $html;
    }

    function front_page($report, $user, $company, $ticket, $trans){
        $html = "";
        $logo_nogd = 'images/logo-nogd-rapport.jpg';
        if($report['front_page']['enabled']){
            // $html .= "<html><body style='background-image:url(\"images/front-background.png\"); background-image-resize: 6;'>";
            $html .= "<div style='width: 100%; height: 176px;'></div>";
            if($report['front_page']['username']){
                $html .= "<h3 style='text-align: center; color: black;'>".$user['user_name']."</h3>";
            }
            if($report['front_page']['company_name']){
                $company_name = strtoupper($company['company_name']);
                //$html .= "<div style='width: 100%; height: 10px;'></div>";
                $html .= "<h2 style='text-align: center; color: black; font-style: italic;'>".$company_name."</h2>";
            }

            if($report['front_page']['ticket_id']){
                $html .= "<div style='width: 100%; height: 58%;'></div>";
                $html .= "<div style='align: justify; color: black;margin-left: 35%;'>".$trans->phrase('pdf_report_phrase10').": ".$ticket['ticket_name']."</div>";
            }
            // $html .= "</body></html>";


        }
        return $html;
    }

    function free_text1($report, $trans, $index){
        $color_red = '#DD1D2F';
        $html = "";
        if($report['free_text1']['enabled']){
            //$html .= "<div style='width: 100%; height: 30px;'></div>";
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase15')."</h2>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['free_text1']['text']."</div>";
            $html .= "<img src='".SITE_URL."/images/zoom.jpg' width='35%' style='float:right;margin-top:-75px;'/>";
        }
        return $html;
    }

    function intro_text($report, $trans, $index){
        $color_red = '#DD1D2F';
        $html = "";
        if($report['intro_text']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase8')."</h2>";
            $html .= "</div>";
            $html .= "<img src='".SITE_URL."/images/schedule.jpg' width='25%' style='float:left;margin-top:45px;'/>";
            $html .= "<div style='text-align: justify;'>".$report['intro_text']['text']."</div>";
            if($report['intro_text']['page_break3']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function free_text2($report, $trans, $index){
        $color_green = '#53B0S1';
        $html = "";
        if($report['free_text2']['enabled']){
            //$html .= "<div style='width: 100%; height: 30px;'></div>";
            $html .= "<div style=' width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase16')."</h2>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['free_text2']['text']."</div>";
            if($report['free_text2']['page_break4']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function conflict($report, $trans ,$index){
        $color_green = '#53B0S1';
        $html = "";
        if($report['conflict']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase9')."</h2>";
            $html .= "</div>";
            $html .= "<div  style='text-align: justify;'>".$report['conflict']['text']."</div>";
            if($report['conflict']['page_break5']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function free_text3($report, $trans, $index){
        $color_red = '#DD1D2F';
        $html = "";
        if($report['free_text3']['enabled']){
            $html .= "<div style='color:".$color_red."; width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase17')."</h2>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['free_text3']['text']."</div>";
            if($report['free_text3']['page_break6']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function summary($report, $trans, $index){
        $color_blue = '#00A1CB';
        $html = "";
        if($report['summary']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase11')."</h2>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['summary']['text']."</div>";
            if($report['summary']['page_break7']){
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
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase18')."</h2>";
            $html .= "</div>";
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_report_phrase19')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['free_text4']['text']."</div>";
            $html .= "<div style='width: 100%;'>";
            $html .= "<h3>".$trans->phrase('pdf_report_phrase20')."</h3>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['free_text4']['text2']."</div>";
            if($report['free_text4']['page_break8']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function radar_graph($report, $trans, $index){
        $color_red = '#DD1D2F';
        $html = "";
        if($report['radar_graph']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase12')."</h2>";
            $html .= "</div>";
            $html .= "<h3 style='text-align: center;'>".$report['radar_graph']['header']."</h3>";
            $html .= "<div style='text-align: center;'><img src='".$report['radar_graph']['graph1']."'></div>";
            $html .= "<div style='text-align: center; background-color: white;'><img src='".$report['radar_graph']['graph2']."'></div>";
            $html .= "<div style=''>".$report['radar_graph']['text']."</div>";
            if($report['radar_graph']['page_break9']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function free_text6($report,$index){
        $html = "";
        if($report['free_text6']['enabled']){
            $html .= "<div style='text-align: justify;'>".$index." ".$report['free_text6']['text']."</div>";
            if($report['free_text6']['page_break10']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function review($report, $ticket, $trans,$index){
        $color_yellow = '#EBAF33';
        $html = "";
        if($report['review']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('text_review')."</h2>";
            $html .= "</div>";

            $html .= "<div style='width: 100%; height: 30px;'></div>";

            $labelEmotions = $trans->phrase("user_ticket_phrase30");
            $html .= "<div style='width: 100%; height: 30px;'>".$labelEmotions."</div>";
            if($ticket) {
                $review = json_decode($ticket['ticket_review'], true);

                if(isset($review['review_status'])){
                    $reviewStatus =  $review['review_status'];
                    $reviewOptions = explode(",",$reviewStatus);
                }

                if(isset($review['review_text'])){
                    $ticketReview = $review['review_text'];
                }

                if(!$ticketReview) {
                    $ticketReview = '';
                }
                else {
                    $ticketReview = htmlspecialchars_decode($ticketReview);
                    //$ticketReview = strip_tags($ticketReview);
                }

                $li = '<ul>';
                foreach($reviewOptions as $review) {
                    $li .= '<li>';
                    $li .= $review;
                    $li .= '</li>';
                }
                $li .= '</ul>';
                $html .= '<div>'.$li.'</div>';
            }

            $html .= '<div>'.$trans->phrase("user_ticket_phrase43").'</div>';
            $html .= '<div>'.$ticketReview.'</div>';

            if($report['review']['page_break_review']){
                $html .= "<pagebreak />";
            }

        }
        return $html;
    }

    function rating($report, $ticket, $trans, $index){
        $color_green = '#53B0S1';
        $html = "";
        if($report['rating']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('text_rating')."</h2>";
            $html .= "</div>";

            $html .= "<div style='width: 100%; height: 30px;'></div>";

            if($ticket) {
                $rating = json_decode($ticket['ticket_rating'], true);

                $rating_check_1 = 0;
                $rating_check_2 = 0;
                $rating_check_3 = 0;
                $rating_check_4 = 0;

                $rating_text_1 = '';
                $rating_text_2 = '';

                if(isset($rating)) {
                    $rating_check_1 = $rating['rating_check_1'];
                    $rating_check_2 = $rating['rating_check_2'];
                    $rating_check_3 = $rating['rating_check_3'];
                    $rating_check_4 = $rating['rating_check_4'];

                    $rating_text_1 = $rating['rating_text_1'];
                    $rating_text_2 = $rating['rating_text_2'];
                }

                $ratingStatus = $ticket['rating_status'];

                $html .= '<div>'.$trans->phrase("user_ticket_phrase47").'&nbsp;'.$rating_check_1.'</div>';
                $html .= '<br/><div>'.$trans->phrase("user_ticket_phrase48").'&nbsp;'.$rating_check_2.'</div>';
                $html .= '<br/><div">'.$trans->phrase("user_ticket_phrase49").'&nbsp;'.$rating_check_3.'</div>';
                $html .= '<br/><div">'.$trans->phrase("user_ticket_phrase50").'&nbsp;'.$rating_check_4.'</div>';
                $html .= '<br/><hr/><br/><div>'.$trans->phrase("user_ticket_phrase50").':<br/>'.$rating_text_2.'</div>';
                $html .= '<br/><div">'.$trans->phrase("user_ticket_phrase50").':<br/>'.$rating_text_2.'</div>';

            }

            if($report['rating']['page_break_rating']){
                $html .= "<pagebreak />";
            }

        }
        return $html;

    }

    function methods($report, $trans, $index){
        $color_blue = '#00A1CB';
        $html = "";
        if($report['method']['enabled']){
            $method_array = json_decode($report['method']['methods'], true);
            $methods = array();
            $selected_method;
            $i = 0;
            foreach($method_array as $single_method){
                if($single_method['rank'] == 1){
                    $selected_method = $single_method;
                }
                $methods[$single_method['method_id']] = array(
                    "method_name" => $single_method['method_name'],
                    "method_color" => $single_method['method_color'],
                    "rank" => $single_method['rank']
                );
                $i++;
            }

            usort($methods, function ($item1, $item2) {
                if ($item1['rank'] == $item2['rank']) return 0;
                return $item1['rank'] < $item2['rank'] ? -1 : 1;
            });

            //file_put_contents('php://stderr', print_r($selected_method["1"], TRUE));
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase5')."</h2>";
            $html .= "</div>";
            //Method header
            $div_width = floor(100 / (count($methods) + 2));
            $count = 0;
            $html .= '<div style="margin-top: 20px;"></div>';

            $count = 0;
            $html .= '<div style="margin-top: 20px;">';
            $html .= '<div style="float: left; align: justify; background-color: #00B8FF; height: 50px; width: '.$div_width.'%; overflow: hidden; border-radius:5px;">'.$trans->phrase('user_composer_phrase19').'</div>';
            foreach($methods as $method):
                $border_top = ($count == 0) ? 25 : 0;
                $html .= '<div style="float: left; align: justify; margin-top: '.$border_top.'px; background-color: '.$method['method_color'].'; height: 50px; width: '.$div_width.'%; overflow: hidden;border-radius:5px;">'.$method['method_name'].'</div>';
                $count++;
            endforeach;
            $html .= '<div style="float: left; align: justify; margin-top: -25px; background-color: #00B8FF; height: 50px; width: '.$div_width.'%; overflow: hidden;border-radius:5px;">'.$trans->phrase('user_composer_phrase20').'</div>';
            $html .= '</div><br>';

            $html .= "<div>".$report['method']['header_text']."</div><br>";
            //Method body
            $html .= '<div style="padding: 5px;"><h3>'.$selected_method['method_name'].'</h3></div>';
            $html .= '<div style="padding: 5px;">'.$selected_method['method_details'].'</div>';
            $html .= "<pagebreak />";

            if($report['method']['page_break11']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function free_text7($report,$index){
        $html = "";
        if($report['free_text7']['enabled']){
            $html .= "<div style='text-align: justify;'>".$index." ".$report['free_text7']['text']."</div>";
            if($report['free_text7']['page_break12']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function tips($report, $trans,$index){
        $color_green = '#53B0S1';
        $html = "";
        if($report['tip']['enabled']){
            $tip_array = json_decode($report['tip']['tips'], true);
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase3')."</h2>";
            $html .= "</div>";
            //Tip body
            $html .= "<div style='margin-top: 20px;'>";
            foreach($tip_array as $tip):
                if(!$tip['question_tips']) continue;
                $html .= '<div style="padding: 5px; background-color: #eeeeee">';
                $html .= '<h3>'.$tip['question_text'].'</h3>';
                $html .= '<h4 style="color: orange; font-style: italic;">'.$trans->phrase('user_composer_phrase23').$tip['question_answer'].'</h4>';
                $html .= '</div>';
                $html .= '<div style="padding: 5px;">'.$tip['question_tips'].'</div>';
            endforeach;
            $html .= "</div>";
            if($report['tip']['page_break13']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function free_text8($report,$index){
        $html = "";
        if($report['free_text8']['enabled']){
            $html .= "<div style='text-align: justify;'>".$index." ".$report['free_text8']['text']."</div>";
            if($report['free_text8']['page_break14']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function disclaimer($report, $trans,$index){
        $color_red = '#DD1D2F';
        $html = "";
        if($report['disclaimer']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase13')."</h2>";
            $html .= "</div>";
            $html .= "<div style='text-align: justify;'>".$report['disclaimer']['disclaimer_text']."</div>";
            $html .= "<div style='text-align: justify;'>".$report['disclaimer']['free_text']."</div>";
            if($report['disclaimer']['page_break15']){
                $html .= "<pagebreak />";
            }
        }
        return $html;
    }

    function assessment($report, $trans,$index){
        $color_green = '#53B0S1';
        $html = "";
        $riskTable = "";
        if($report['assessment']['enabled']){
            $html .= "<div style='width: 100%;'>";
            $html .= "<h2>".$index." ".$trans->phrase('pdf_report_phrase14')."</h2>";
            $html .= "</div>";
            $riskTable = "<table class='riskTable'; style='border-collapse: collapse';>";
            $riskTable .="<tr><th colspan='2'>".$trans->phrase("risk_assessment_table1_1")."<br>".$trans->phrase("risk_assessment_table1_2")."<br>".$trans->phrase("risk_assessment_table1_3")."</th>";
            $riskTable .="<th colspan='3'>".$trans->phrase("risk_assessment_table2_1")."<br>".$trans->phrase("risk_assessment_table2_2")."<br>".$trans->phrase("risk_assessment_table2_3")."<br>".$trans->phrase("risk_assessment_table2_4")."<br>".$trans->phrase("risk_assessment_table2_5")."<br>".$trans->phrase("risk_assessment_table2_6")."</th>";
            $riskTable .="<th colspan='2'>".$trans->phrase("risk_assessment_table3_1")."<br>".$trans->phrase("risk_assessment_table3_2")."<br>".$trans->phrase("risk_assessment_table3_3")."<br>".$trans->phrase("risk_assessment_table3_4")."<br>".$trans->phrase("risk_assessment_table3_5")."<br>".$trans->phrase("risk_assessment_table3_6")."</th></tr>";
            $riskTable .= "<tr><th style='width:9rem; height:2rem; resize:none';>".$trans->phrase('risk_assessment_table4')."</th><th style='width:7rem; height:2rem; resize:none;';>".$trans->phrase('risk_assessment_table5')."</th><th style='width:6rem; height:2rem; resize:none;';>".$trans->phrase('risk_assessment_table6')."</th><th style='width:4rem; height:2rem; resize:none;';>".$trans->phrase('risk_assessment_table7')."</th><th style='width:5rem; height:2rem; resize:none;';>".$trans->phrase('risk_assessment_table8')."</th><th style='width:16rem; height:2rem; resize:none';>".$trans->phrase('risk_assessment_table9')."</th>
            <th style='width:3rem; height:2rem; resize:none';>".$trans->phrase('risk_assessment_table10')."</th>";
            $riskTable .= "</tr><tr><td style='text-align: left;'>".$trans->phrase('risk_assessment_table11')."</td>";
            foreach($report['assessment']['risks']['rumours'] as $val){
                file_put_contents('php://stderr', print_r($val, TRUE));
                $riskTable .= "<td>".$val."</td>";
            }
            $riskTable .= "</tr><tr><td style='text-align: left;'>".$trans->phrase('risk_assessment_table12')."</td>";
            foreach($report['assessment']['risks']['notEnoughInfo'] as $val){
                $riskTable .= "<td>".$val."</td>";
            }
            $riskTable .= "</tr><tr><td style='text-align: left;'>".$trans->phrase('risk_assessment_table13')."</td>";
            foreach($report['assessment']['risks']['inaccessibleLeader'] as $val){
                $riskTable .= "<td>".$val."</td>";
            }
            $riskTable .= "</tr><tr><td style='text-align: left;'>".$trans->phrase('risk_assessment_table14')."</td>";
            foreach($report['assessment']['risks']['sickleave'] as $val){
                $riskTable .= "<td>".$val."</td>";
            }
            $riskTable .= "</tr><tr><td style='text-align: left;'>".$trans->phrase('risk_assessment_table15')."</td>";
            foreach($report['assessment']['risks']['polarization'] as $val){
                $riskTable .= "<td>".$val."</td>";
            }
            $riskTable .= "</tr>";
            $riskTable .= "</table>";

        }
        //file_put_contents('php://stderr', print_r($riskTable, TRUE));
        $html .= $riskTable;
        return $html;
    }
    
    function add_content($report, $trans){
        $html = "";
        $html .=    "<div>
                        <h1 style='text-align: center; color: black;top:10%;'>".$trans->phrase('pdf_generate_content_phrase1')."</h2>
                        <h1 tyle='text-align: left;'color: black;>Sammendrag</h1>
                       
                    </div>";
                    //echo $html;
                    //die();
        return $html;
        
        
    }
    
    if($report){
        $report_content = json_decode($report['report_content'], true);
        $report_content['ticket_id'] = $ticket_id;
        $user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);
        $company = $Database->get_data('company_id', $ticket['ticket_company_id'], 'company', true);
        //$methods = $Database->get_data('method_id', $ticket['ticket_company_id'], 'company', true);
        $index = 1;
        
        $header_footer = header_footer($report_content, $user, $company, $trans);
        $fron_tpage = front_page($report_content, $user, $company, $ticket, $trans);
        $add_content = add_content($report_content,$trans);
        $free_text1 = free_text1($report_content, $trans,$index);
        if($free_text1 != "") $index++;
        $intro_text = intro_text($report_content, $trans,$index);
        if($intro_text != "") $index++;
        $free_text2 = free_text2($report_content, $trans,$index);
        if($free_text2 != "") $index++;
        $conflict = conflict($report_content, $trans,$index);
        if($conflict != "") $index++;
        $free_text3 = free_text3($report_content, $trans,$index);
        if($free_text3 != "") $index++;
        $summary = summary($report_content, $trans,$index);
        if($summary != "") $index++;
        $free_text4 = free_text4($report_content, $trans,$index);
        if($free_text4 != "") $index++;
        $radar_graph = radar_graph($report_content, $trans,$index);
        if($radar_graph != "") $index++;
        $review = review($report_content, $ticket, $trans,$index);
        if($review != "") $index++;
        $rating = rating($report_content, $ticket, $trans,$index);
        if($rating != "") $index++;
        $free_text6 = free_text6($report_content,$index);
        if($free_text6 != "") $index++;
        $methods = methods($report_content, $trans,$index);
        if($methods != "") $index++;
        $free_text7 = free_text7($report_content,$index);
        if($free_text7 != "") $index++;
        $tips = tips($report_content, $trans,$index);
        if($tips != "") $index++;
        $free_text8 = free_text8($report_content,$index);
        if($free_text8 != "") $index++;
        $disclaimer = disclaimer($report_content, $trans,$index);
        if($disclaimer != "") $index++;
        $assessment = assessment($report_content, $trans,$index);
        if($assessment != "") $index++;
        
        
        
        
        $front_prefix_html = "<html><body style='background-image:url(\"images/rapport-forside.jpg\"); background-image-resize: 6;'>" . $header_footer . "<div>";
        $front_subfix_html = "</div></body></html>";
        
        $content_prefix_html = "<html><body style='background-image:url(\"images/bakgrunn-mal.png\"); background-image-resize: 6;'>" . $header_footer . "<div>";
        $content_subfix_html = "</div></body></html>";
        
        $normal_prefix_html = "<html><body style='background-image:url(\"images/bakgrunn-mal.png\"); background-image-resize: 6;'>" . $header_footer . "<div>";
        $normal_subfix_html = "</div></body></html>";
        //Generate PDF
        

        
        //file_put_contents('php://stderr', print_r($html, TRUE));
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($front_prefix_html.$fron_tpage.$front_subfix_html);
        $mpdf->AddPage();
        $mpdf->WriteHTML($content_prefix_html.$add_content.$content_subfix_html);
        $mpdf->AddPage();
        $mpdf->WriteHTML($normal_prefix_html.$free_text1.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->WriteHTML($normal_prefix_html.$intro_text.$normal_subfix_html);
        $mpdf->AddPage();
        /*$mpdf->WriteHTML($normal_prefix_html.$free_text2.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->WriteHTML($normal_prefix_html.$conflict.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->WriteHTML($normal_prefix_html.$free_text3.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$summary.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$free_text4.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(1);
        $mpdf->WriteHTML($normal_prefix_html.$radar_graph.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$review.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(1);
        $mpdf->WriteHTML($normal_prefix_html.$rating.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$free_text6.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(1);
        $mpdf->WriteHTML($normal_prefix_html.$methods.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$free_text7.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$tips.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$free_text8.$normal_subfix_html);
        $mpdf->AddPage();
        $mpdf->SetColumns(2,'J',5);
        $mpdf->WriteHTML($normal_prefix_html.$disclaimer.$normal_subfix_html);
         $mpdf->AddPage();
        $mpdf->SetColumns(1);
        $mpdf->WriteHTML($normal_prefix_html.$assessment.$normal_subfix_html);*/
        
        
        // $mpdf->Output();
        $mpdf->Output("Report_".$ticket_id.".pdf", 'D');

        //echo $review;
        //echo '<br/>';
        //echo $rating;

    }
    else{
        echo $trans->phrase('pdf_report_phrase6');
    }
}
else{
    echo $trans->phrase('pdf_report_phrase7');
}
?>
