<?php

ob_start();

session_start();

require_once '../vendor/mpdf/vendor/autoload.php';
require_once '../config.php';
require_once '../database.php';
require_once '../vendor/fpdf/fpdf.php';
require_once '../translation.php';
$trans = new Translation($_SESSION['trans']);

$stylesheet = file_get_contents('../css/custompdf.css');
$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];
$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdfConfig = [
    // 'debug' => true,
    'mode' => $_SESSION['trans'],
    'default_font_size' => 12,
    'margin_top' => 14,
    'margin_bottom' => 20,
    'margin_left' => 6,
    'margin_right' => 10,
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
            'BI' => 'Montserrat-SemiBoldItalic.ttf',
        ],
    ],
    'default_font' => 'montserrat',
];

$mpdf = new \Mpdf\Mpdf($mpdfConfig);
$mpdf->SetDefaultBodyCSS('line-height', 1.6);

if (!isset($_SESSION['trans'])) {
    $Database = new Database();
    $default_language = $Database->get_data('lang_default', 1, 'language', true);
    if ($default_language) {
        $_SESSION['trans'] = $default_language['lang_code'];
    } else {
        $_SESSION['trans'] = 'en';
    }
}

if (isset($_SESSION['account-type']) && ((isset($_GET['id']) && $_GET['id'] > 0) || (isset($_POST['id']) && $_POST['id'] > 0))) {
    $Database = new Database();

    /*$sql = "SELECT * FROM report_graph WHERE ticket_id='".$_GET['id']."' AND lang_code='".$_SESSION['trans']."';";
    $graph = $Database->get_connection()->prepare($sql);
    $graph->execute();

    if ($graph->rowCount() > 0) {*/
    $ticket_id = $_GET['id'];
    if (isset($_SESSION['report-format-id'])) {
        $report_format_id = $_SESSION['report-format-id'];
    } else {
        $report_format_id = 'Please select format from dropdown';
        exit;
    }

    $ticket = $Database->get_data('ticket_id', $ticket_id, 'ticket', true);
    $reports = $Database->get_multiple_data('ticket_id', $ticket_id, 'report');

    /*$sql = "SELECT * FROM creport WHERE ticket_id='" .$ticket_id . "' AND lang_code='" . $_SESSION['trans'] . "';";

    $report_format = $Database->get_connection()->prepare($sql);

    $report_format->execute();

    if ($report_format->rowCount() > 0) {
        $report_format = $report_format->fetch(PDO::FETCH_ASSOC);
    }else{
        $report_format = 'No data Found';
        echo $report_format;
        die();
    }*/

    if ($_GET['com'] == 1) {
        $sql = "SELECT * FROM creport WHERE ticket_id='" . $ticket_id . "' AND lang_code ='" . $_SESSION['trans'] . "';";

        $report_format = $Database->get_connection()->prepare($sql);

        $report_format->execute();

        if ($report_format->rowCount() > 0) {
            $report_format = $report_format->fetch(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT * FROM mlreport_format_contentd WHERE report_format_id='" . $report_format_id . "' AND report_lang_code='" . $_SESSION['trans'] . "';";

            $report_format = $Database->get_connection()->prepare($sql);

            $report_format->execute();

            if ($report_format->rowCount() > 0) {
                $report_format = $report_format->fetch(PDO::FETCH_ASSOC);
            } else {
                $report_format = 'No data Found';
                echo $report_format;
                exit;
            }
        }
    } else {
        $sql = "SELECT * FROM mlreport_format_contentd WHERE report_format_id='" . $report_format_id . "' AND report_lang_code='" . $_SESSION['trans'] . "';";

        $report_format = $Database->get_connection()->prepare($sql);

        $report_format->execute();

        if ($report_format->rowCount() > 0) {
            $report_format = $report_format->fetch(PDO::FETCH_ASSOC);
        } else {
            $report_format = 'No data Found';
            echo $report_format;
            exit;
        }
    }


    $sqlData = "SELECT * FROM mlreport_format_content WHERE report_format_id = '" . $report_format_id . "' AND report_lang_code='{$_SESSION['trans']}'";

    $reportData = $Database->get_connection()->prepare($sqlData);

    $reportData->execute();

    if ($reportData->rowCount() < 1)
        $reportData = false;
    else
        $reportData = $reportData->fetch(PDO::FETCH_ASSOC);

    /*foreach($reports as $single_report){
        if($single_report['lang_code'] == $_SESSION['trans']){
            $report = $single_report;
        }
    }*/

    $report = json_decode($report_format['report_content'], true);


    function page_header($img, $pos)
    {
        if ('NOHEADER' == $img) {
            $html = '';
            $html .= '<htmlpageheader name="temp" style="display:none;">';
            $html .= '<div style="margin-top: 60px">

                    </div>';
            $html .= '</htmlpageheader>';
            $html .= '<sethtmlpageheader name="temp" value="on" show-this-page="1" />';

            return $html;
        } else {
            $html = '';
            $html .= '<htmlpageheader name="firstpage" style="display:none;">';
            $html .= '<div style="margin-top: 60px">
                        <img src="https://beta.nogd.no/images/logo-nogd-rapport.jpg" style="width: 170px; height: 60px; float: ' . $pos . '; margin-' . $pos . ': 50px; margin-bottom:20px;" /><br>
                        <img src="https://beta.nogd.no/images/' . $img . '" style="float: left;"/>
                    </div>';
            $html .= '</htmlpageheader>';

            $html .= '<htmlpageheader name="otherpages" style="display:none;">';
            $html .= '<div style="margin-top: 60px">
                        <img src="https://beta.nogd.no/images/logo-nogd-rapport.jpg" style="width: 170px; height: 60px; float: left; margin-left: 50px;" />
                        <img src="https://beta.nogd.no/images/4.png" />
                    </div>';
            $html .= '</htmlpageheader>';

            $html .= '<sethtmlpageheader name="firstpage" value="on" show-this-page="1" />';
            $html .= '<sethtmlpageheader name="otherpages" value="on" />';

            return $html;
        }
    }

    function page_footer()
    {
        $html = '';
        $html .= '<htmlpagefooter name="footer">';
        $html .= '<div style="width: 100%; height: 30px;text-align:right;padding-right:30px;padding-bottom:30px;"><span style="font-weight: bold; font-size: 16px; color: black;">{PAGENO}</span>-{nbpg}</div>';
        $html .= '</htmlpagefooter>';
        $html .= '<sethtmlpagefooter name="footer" value="on" />';

        return $html;
    }

    function front_page($report, $user, $company, $ticket, $trans)
    {
        $html = '';
        if ($report['front_page']['enabled']) {
            $html .= "<div style='width: 100%; text-align: right; font-size: 16pt; font-weight: normal; letter-spacing: 1px'>KONFIDENSIELT</div>";
            $html .= "<div style='clear: both;'></div>";
            $html .= "<div style='width: 100%; height: 650px;'></div>";

            $html .= "<div style='width: 350px; text-align: center'>";

            // if ($report['front_page']['username']) {
            //    $html .= '<span>'.$user['user_name'].'</span>';
            // }

            $html .= '<p style="font-size: 16pt; font-weight: normal; letter-spacing: 1px; line-height: 1.4">';
            if ($report['front_page']['company_name']) {
                $company_name = strtoupper($company['company_name']);
                $html .= $company_name;
            } elseif ($report['front_page']['ticket_id']) {
                $html .= $trans->phrase('pdf_text1d') . ': ' . $ticket['ticket_name'];
            }

            $html .= '<br />' . date('d.M Y');
            $html .= '</p>';

            if ($report['front_page']['logo']) {
                $html .= "<div style='width: 250px; height: 60px; margin: auto; background: transparent; padding: 10px;'>";
                // $html .= '<img src="https://beta.nogd.no/images/company_logo/27.png" style="width: 200px; height: 60px" />';
                if ($company['upload_company_img']) {
                    $html .= '<img src="https://beta.nogd.no/images/company_logo/' . $company['upload_company_img'] . '" style="height: 60px" />';
                    // $html .= '<img src="https://beta.nogd.no/images/company_logo/'.$company['upload_company_img'].'" style="width: 200px; height: 60px" />';
                } else {
                    $html .= '<img src="' . SITE_URL . '/images/default-company.png" style="width: 200px; height: 60px" />';
                }
                $html .= '</div>';
            }

            $html .= '</div>';

            $html .= "<div style='width: 375px; text-align: left; margin-left: 37px;'>";
            $html .= '<p style="font-size: 14pt; font-weight: normal; line-height: 1.4">';
            $html .= 'Denne rapporten er utarbeidet av:<br />';
            // $html .= 'Konsulentnavn/BHT</p>';
            $html .= $ticket['Created_by'] . '</p>';
            $html .= '</div>';

            if ($report['front_page']['page_break1']) {
                // $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    // Intro
    function free_text1($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text1']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<img src="https://beta.nogd.no/images/logo-b-removebg-preview.png" style="width: 200px; height: 80px; float: right" />';
            $html .= "<div style='width: 100%; height: 97px;'></div>";
            $html .= '<h1 style="font-weight: 600; font-size: 38pt; text-align: center;"><b>' . $trans->phrase('pdf_text2') . '</b></h1>';
            $html .= '</div>';

            $html .= "<div style='font-size: 16pt;' class='ft_1'>" . htmlspecialchars_decode($report['free_text1']['text']) . '</div>';

            if ($report['free_text1']['page_break2']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function intro_text($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['intro_text']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-weight: 600; font-size: 63.5pt;">1</td>
                            <td style="font-weight: 600; font-size: 16pt;"><b>' . $trans->phrase('pdf_text3') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 30px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px; font-size: 12pt">' . htmlspecialchars_decode($report['intro_text']['text']) . '</div>';

            if ($report['intro_text']['page_break3']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text2($report, $trans, $index)
    {
        $check_txt_number = 2;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {
            $n = 2;
        }


        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text2']['enabled']) {
            $html .= '<div style="width: 100%; height: 13px;"></div>';

            $html .= '<div style="width: 100%;">';
            $html .= '<table style="font-size: 30px;">
                        <tr>
                            <td style="width: 400px;"></td>
                            <td style="width: 50px; font-weight: 600; font-size: 63.5pt;">' . $n . '</td>
                            <td style="font-weight: 600; font-size: 16pt;"><b>' . $trans->phrase('pdf_text4') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 80px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px; font-size: 12pt">' . htmlspecialchars_decode($report['free_text2']['text']) . '</div>';

            if ($report['free_text2']['page_break4']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text3($report, $trans, $index)
    {

        $check_txt_number = 3;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {
            $n = 3;
        }


        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text3']['enabled']) {
            $html .= '<div style="width: 100%;">';

            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text5') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px; margin-top: 40px; ">' . htmlspecialchars_decode($report['free_text3']['text']) . '</div>';
            if ($report['free_text3']['page_break5']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text4($report, $trans, $index)
    {
        $check_txt_number = 4;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {
            $n = 4;
        }



        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text4']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text6') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:20px;">' . htmlspecialchars_decode($report['free_text4']['text']) . '</div>';
            if ($report['free_text4']['page_break6']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text5($report, $trans, $index)
    {


        $check_txt_number = 5;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {
            $n = 5;
        }


        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text5']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text7') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';
            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:10px;">' . htmlspecialchars_decode($report['free_text5']['text']) . '</div>';
            if ($report['free_text5']['page_break7']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }


    function set_heading_counter($report, $check_txt_number)
    {
        $n = 0;
        for ($i = 1; $i <= $check_txt_number; $i++) {
            $arr_key = "free_text" . $i;
            if (isset($report[$arr_key])) {
                if ($report[$arr_key]['enabled'] == 1) {
                    $n++;
                }
            }
        }

        return $n;
    }
    function free_text6($report, $trans, $index, $ticketData)
    {
        $check_txt_number = 6;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 6;
        }




        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text6']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text8') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:50px;">' . htmlspecialchars_decode($report['free_text6']['text']) . '</div>';
            if ($report['free_text6']['page_break8']) {
                $html .= '<pagebreak />';
            }
        }



        $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:50px;font-size:16px;">' . $ratingData . '</div>';

        return $html;
    }

    function free_text7($report, $trans, $index)
    {
        $check_txt_number = 7;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 7;
        }


        $color_yellow = '#EBAF33';
        $html = '';


        if ($report['free_text7']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text9') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text7']['text']) . method_list($report, $trans, $index) . '</div>';
            if ($report['free_text7']['page_break9']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function method_list($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';

        foreach ($report['methods'] as $methods) {
            if ($methods['enabled']) {
                // $html .= '<div style="width: 100%;">';
                // $html .= '<table style="width: 400px; font-size: 30px;">
                //         <tr>
                //             <td style="width: 40px;"></td>
                //             <td style="width: 50px; font-size: 55px;">'.$index . "." . $sub_index.'</td>
                //             <td><b>' . $methods['method_name'] . '</b></td>
                //         </tr>
                //     </table>';
                // $html .= '</div>';

                $html .= '<div style="width: 100%; height: 55px;"></div>';
                if (strpos(htmlspecialchars_decode($methods['text']), $methods['method_name']) == false) {
                    $html .= "<h2>{$methods['method_name']}</h2>";
                }
                $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($methods['text']) . '</div>';
            }
        }

        return $html;
    }

    function free_text8($report, $trans, $index)
    {
        $check_txt_number = 8;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 8;
        }



        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text8']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text10') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-bottom:30px;">' . htmlspecialchars_decode($report['free_text8']['text']) . '</div>';
            if ($report['free_text8']['page_break10']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text9($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text9']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<img src="https://beta.nogd.no/images/logo-b-removebg-preview.png" style="width: 200px; height: 80px; float: right" />';
            $html .= "<div style='width: 100%; height: 97px;'></div>";
            $html .= '<h1 style="font-weight: 600; font-size: 38pt; text-align: center;"><b>' . $trans->phrase('pdf_text11') . '</b></h1>';
            $html .= '</div>';
            $html .= "<div style='font-size: 16pt;'>" . htmlspecialchars_decode($report['free_text9']['text']) . '</div>';
            if ($report['free_text9']['page_break11']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text10($report, $trans, $index)
    {

        $check_txt_number = 9;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 9;
        }

        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text10']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text11a') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:100%;">' . htmlspecialchars_decode($report['free_text10']['text']) . '</div>';
            if ($report['free_text10']['page_break12']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text11($report, $trans, $index)
    {

        $check_txt_number = 9;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 9;
        }


        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text11']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text11b') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:20px">' . htmlspecialchars_decode($report['free_text11']['text']) . '</div>';
            if ($report['free_text11']['page_break13']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text12($report, $trans, $index)
    {
        $check_txt_number = 9;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 9;
        }



        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text12']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text11c') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:40px;">' . htmlspecialchars_decode($report['free_text12']['text']) . '</div>';
            if ($report['free_text12']['page_break14']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text13($report, $trans, $index)
    {
        $check_txt_number = 9;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 9;
        }


        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text13']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text11d') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;margin-top:40px;">' . htmlspecialchars_decode($report['free_text13']['text']) . '</div>';
            if ($report['free_text13']['page_break15']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text14($report, $trans, $index)
    {

        $check_txt_number = 9;
        $n = set_heading_counter($report, $check_txt_number);

        if ($n == 0) {

            $n = 9;
        }


        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text14']['enabled']) {
            $html .= '<div style="width: 100%;">';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">' . $n . '</td>
                            <td><b>' . $trans->phrase('pdf_text12') . '</b></td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text14']['text']) . '</div>';
            $html .= '<h2 style="width:100%; margin-top:50px;border-top:4px solid #ebb033;padding-top:18px;padding-bottom:18px; border-bottom:4px solid #ebb033;font-size: 30px; text-align:center;"><b>© 2022 Semje Software. Med enerett.</b></h2>';
            if ($report['free_text14']['page_break16']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function radar_graph($report, $trans, $index)
    {
        $color_red = '#DD1D2F';
        $html = '';
        if ($report['free_text15']['enabled']) {
            $Database = new Database();
            $sql = "SELECT * FROM report_graph WHERE ticket_id='" . $_GET['id'] . "' AND lang_code='" . $_SESSION['trans'] . "';";
            $graph = $Database->get_connection()->prepare($sql);
            $graph->execute();

            if ($graph->rowCount() > 0) {
                $graph = $graph->fetch(PDO::FETCH_ASSOC);
                $report_graph = json_decode($graph['report_content'], true);
                if ($report_graph['radar_graph']['graph1']) {
                    $html .= "<div style='width: 100%;'>";
                    $html .= '<h2>' . $index . ' ' . $trans->phrase('pdf_report_phrase12') . '</h2>';
                    $html .= '</div>';
                    $html .= "<div style=''>" . $report['free_text15']['text'] . '</div>';
                    $html .= "<div style='text-align: center;'><img src='" . $report_graph['radar_graph']['graph1'] . "'></div>";
                }
            } else {
                $graph = false;
            }

            if ($report['free_text15']['page_break17']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    if ($report) {
        $report_content = json_decode($report_format['report_content'], true);

        if (isset($_POST['id'])) {
            $_POST['id'] = $ticket_id;
        } else {
            $_GET['id'] = $ticket_id;
        }

        $user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);
        $company = $Database->get_data('company_id', $ticket['ticket_company_id'], 'company', true);
        // $methods = $Database->get_data('method_id', $ticket['ticket_company_id'], 'company', true);
        $index = 1;

        // $header_footer = header_footer($report_content, $user, $company, $trans);
        $front_page = front_page($report_content, $user, $company, $ticket, $trans);

        $free_text1 = free_text1($report_content, $trans, $index);
        if ($free_text1 != '') {
            ++$index;
        }

        $intro_text = intro_text($report_content, $trans, $index);
        if ($intro_text != '') {
            ++$index;
        }

        $free_text2 = free_text2($report_content, $trans, $index);
        if ($free_text2 != '') {
            ++$index;
        }

        $free_text3 = free_text3($report_content, $trans, $index);
        if ($free_text3 != '') {
            ++$index;
        }

        $free_text4 = free_text4($report_content, $trans, $index);
        if ($free_text3 != '') {
            ++$index;
        }

        $free_text5 = free_text5($report_content, $trans, $index);
        if ($free_text5 != '') {
            ++$index;
        }

        $free_text6 = free_text6($report_content, $trans, $index, $ticket['ticket_rating']);
        if ($free_text6 != '') {
            ++$index;
        }


        $free_text7 = free_text7($report_content, $trans, $index);
        if ($free_text7 != '') {
            ++$index;
        }




        $free_text8 = free_text8($report_content, $trans, $index);
        if ($free_text8 != '') {
            ++$index;
        }

        $free_text9 = free_text9($report_content, $trans, $index);
        if ($free_text9 != '') {
            ++$index;
        }

        $free_text10 = free_text10($report_content, $trans, $index);
        if ($free_text10 != '') {
            ++$index;
        }

        $free_text11 = free_text11($report_content, $trans, $index);
        if ($free_text11 != '') {
            ++$index;
        }

        $free_text12 = free_text12($report_content, $trans, $index);
        if ($free_text12 != '') {
            ++$index;
        }

        $free_text13 = free_text13($report_content, $trans, $index);
        if ($free_text13 != '') {
            ++$index;
        }

        $free_text14 = free_text14($report_content, $trans, $index);
        if ($free_text14 != '') {
            ++$index;
        }


        $prefix_html = "<html><head>
        <link rel='preconnect' href='https://fonts.googleapis.com'>
        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
        <link href='https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap' rel='stylesheet'>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap');
            .ft_1 ul {
                list-style: none !important;
                list-style-type: none !important;
            }
            .ft_1 ul li span {
                font-size: 38pt;
                font-weight: 600;
                margin-right: 25px !important;
            }
            .ft_1 ul li {
                list-style-type: none !important;
                font-size: 16pt;
                font-weight: normal;
                line-height: 70px !important;
            }

            ul.others li {
                font-weight: normal;
                line-height: 20px !important;
            }

            ul.attach {
                list-style: none !important;
                list-style-type: none !important;
            }
            ul.attach li span {
                font-size: 38pt;
                font-weight: 600;
                margin-right: 25px !important;
            }
            ul.attach li {
                list-style-type: none !important;
                font-size: 16pt;
                font-weight: normal;
                line-height: 100px !important;
            }

        </style>
        </head>";

        $page_footer = page_footer();
        $front_prefix_html = "<body style='font-family: \"Montserrat\", sans-serif; background-image:url(\"https://beta.nogd.no/images/bg-1.png\"); background-image-resize: 6;'>";
        $front_subfix_html = '</body></html>';

        $content_prefix_html = "<body style='background-image:url(\"https://beta.nogd.no/images/2.png\"); background-image-resize: 6;'>" . $page_footer . '<div>';
        $content_subfix_html = '</div></body></html>';

        $normal_prefix_html = "<body style='background-color: #d7dbdd; background-image:url(\"https://beta.nogd.no/images/2.png\"); background-image-resize: 6;'><div>";
        $normal_subfix_html = '</div></body></html>';

        $intro_prefix_html = '<html>
        <head>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap" rel="stylesheet">
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap");
            </style>
        </head>';
        $intro_prefix_html .= '<body style="background-color: #fff; background-image:url(\'https://beta.nogd.no/images/white.png\'); background-image-resize: 6;">' . $page_footer . '<div>';
        $intro_subfix_html = '</div></body></html>';

        // Generate PDF
        // file_put_contents('php://stderr', print_r($html, TRUE));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);


        if ($report['front_page']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($prefix_html . $front_prefix_html . $front_page . $front_subfix_html);
        }

        if ($report['free_text1']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 20, 10, 14, 10, 8, 10);
            $mpdf->WriteHTML($prefix_html . $normal_prefix_html . $free_text1 . $normal_subfix_html);
        }

        if ($report['intro_text']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
            $page_header = page_header('intro.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $intro_text . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text2']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
            $page_header = page_header('mpr_5.png', 'right');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text2 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text3']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 52, 10, 0, 0);
            $page_header = page_header('6.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text3 . $page_footer . $intro_subfix_html);
        }


        if ($report['free_text4']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 53, 10, 0, 0);
            $page_header = page_header('lpr_ft_4.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text4 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text5']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
            $page_header = page_header('10.jpeg', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text5 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text6']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
            $page_header = page_header('15.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text6 . $page_footer . $intro_subfix_html);
        }


        if ($report['free_text7']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
            $page_header = page_header('17.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text7 . $page_footer . $intro_subfix_html);
        }



        if ($report['free_text8']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 45, 10, 0, 0);
            $page_header = page_header('28.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text8 . $page_footer . $intro_subfix_html);
        }
        if ($report['free_text9']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 20, 10, 14, 10, 8, 10);
            $mpdf->SetHTMLHeader();
            $page_header = page_header('NOHEADER', 'left');
            $mpdf->WriteHTML($prefix_html . $normal_prefix_html . $page_header . $free_text9 . $normal_subfix_html);
        }

        if ($report['free_text10']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 47, 10, 0, 0);
            $page_header = page_header('31.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text10 . $page_footer . $intro_subfix_html);
        }

        // if ($report['free_text10Content']['enabled']) {
        //     $mpdf->AddPage('P', '', '', '', '', 20, 10, 14, 10, 8, 10);
        //     $mpdf->WriteHTML($prefix_html.$normal_prefix_html.$free_text10Content.$normal_subfix_html);
        // }

        if ($report['free_text11']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 43, 10, 0, 0);
            $page_header = page_header('40.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text11 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text12']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 47, 10, 0, 0);
            $page_header = page_header('41.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text12 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text13']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 47, 10, 0, 0);
            $page_header = page_header('42.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text13 . $page_footer . $intro_subfix_html);
        }

        // if ($report['free_text14']['enabled']) {
        //     $mpdf->AddPage('P', '', '', '', '', 0, 0, 50, 10, 0, 0);
        //     $page_header = page_header('15.png');
        //     $mpdf->WriteHTML($intro_prefix_html.$page_header.$free_text6.$page_footer.$intro_subfix_html);
        // }

        if ($report['free_text14']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 47, 10, 0, 0);
            $page_header = page_header('15.png', 'left');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text14 . $page_footer . $intro_subfix_html);
        }

        // if ($report['free_text15']['enabled']) {
        // $mpdf->AddPage('P', '', '', '', '', 0, 0, 53, 10, 0, 0);
        // $mpdf->WriteHTML($intro_prefix_html.$page_header.$radar_graph.$page_footer.$intro_subfix_html);
        // }




        $mpdf->Output("Report_" . $reportData['report_title'] . "_" . $ticket_id . ".pdf", 'D');
    } else {
        echo $trans->phrase('pdf_report_phrase6');
    }
    // } else {
    // echo 'Please draw graph with proper languange';
    // }
} else {
    echo $trans->phrase('pdf_report_phrase7');
}
