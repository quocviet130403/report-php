<?php

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
    'mode' => $_SESSION['trans'],
    'default_font_size' => 10,
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

if (isset($_SESSION['account-type']) && isset($_GET['id']) && $_GET['id'] > 0) {
    $Database = new Database();

    $ticket_id = $_GET['id'];
    // $report_format_id = $_SESSION['report-format-id'];
    $report_format_id = 6;

    $ticket = $Database->get_data('ticket_id', $ticket_id, 'ticket', true);
    $reports = $Database->get_multiple_data('ticket_id', $ticket_id, 'report');

    $report_format = $Database->get_data('report_format_id', $report_format_id, 'report_format', true);

    /*foreach($reports as $single_report){
        if($single_report['lang_code'] == $_SESSION['trans']){
            $report = $single_report;
        }
    }*/

    $report = json_decode($report_format['report_content'], true);

    function header_footer($report, $user, $company, $trans)
    {
        $html = '';
        $html .= '<htmlpagefooter name="footer">';
        $html .= "<div style='float: right; text-align:right; width; 50px;'><span style='font-weight: bold; font-height: 16px; color: black;'>{PAGENO}</span>-{nbpg}</div>";
        $html .= '</htmlpagefooter>';
        $html .= '<sethtmlpagefooter name="footer" value="on" />';

        return $html;
    }

    function front_page($report, $user, $company, $ticket, $trans)
    {
        $html = '';
        if ($report['front_page']['enabled']) {
            $html .= "<div style='width: 100%; text-align: right; font-size: 23px; font-weight: normal; letter-spacing: 1px'>KONFIDENSIELT</div>";
            $html .= "<div style='clear: both;'></div>";
            $html .= "<div style='width: 100%; height: 650px;'></div>";

            $html .= "<div style='width: 350px; text-align: center'>";

            // if ($report['front_page']['username']) {
            //    $html .= '<span>'.$user['user_name'].'</span>';
            // }

            $html .= '<p style="font-size: 22px; font-weight: normal; letter-spacing: 1px">';
            if ($report['front_page']['company_name']) {
                $company_name = strtoupper($company['company_name']);
                $html .= $company_name;
            } elseif ($report['front_page']['ticket_id']) {
                $html .= $trans->phrase('pdf_text1d') . ': ' . $ticket['ticket_name'];
            }

            $html .= '<br />' . date('d.M Y');
            $html .= '</p>';

            if ($report['front_page']['logo']) {
                $html .= "<div style='width: 250px; height: 60px; margin: auto; background: #fff; padding: 10px;'>";
                $html .= '<img src="../images/company_logo/27.png" style="width: 200px; height: 60px" />';
                $html .= '</div>';
            }
            $html .= '</div>';

            $html .= "<div style='width: 375px; text-align: left; margin-left: 37px;'>";
            $html .= '<p style="font-size: 18px; font-weight: normal;">';
            $html .= 'Denne rapporten er utarbeidet av:<br />';
            $html .= 'Konsulentnavn/BHT</p>';
            $html .= '</div>';

            if ($report['front_page']['page_break1']) {
                $html .= '<pagebreak />';
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
            $html .= '<img src="../images/logo-b-removebg-preview.png" style="width: 200px; height: 80px; float: right" />';
            $html .= "<div style='width: 100%; height: 105px;'></div>";
            $html .= '<h1 style="font-size: 45px; text-align: center;">' . $trans->phrase('pdf_text2') . '</h1>';
            $html .= '</div>';
            $html .= "<div style='font-size: 35px;'>" . htmlspecialchars_decode($report['free_text1']['text']) . '</div>';
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
            $html .= "<div style='width: 100%;'>";
            $html .= '<img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px;" />';
            $html .= "<div style='width: 100%; height: 80px;'></div>";
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 50px; font-size: 45px">1</td>
                            <td>' . $trans->phrase('pdf_text3') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= "<div style='width: 100%; height: 55px;'></div>";
            // $html .= "<div style='text-align: justify;'>".substr(htmlspecialchars_decode($report['intro_text']['text']), 0, 35).'</div>';
            // $html .= "<div style='text-align: justify;'>".substr(htmlspecialchars_decode($report['intro_text']['text']), 36, 155).'</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['intro_text']['text']) . '</div>';
            if ($report['intro_text']['page_break3']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text2($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text2']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text4') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text2']['text']) . '</div>';
            if ($report['free_text2']['page_break4']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text3($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text3']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text5') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text3']['text']) . '</div>';
            if ($report['free_text3']['page_break5']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text4($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text4']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text6') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text4']['text']) . '</div>';
            if ($report['free_text4']['page_break6']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text5($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text5']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text7') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text5']['text']) . '</div>';
            if ($report['free_text5']['page_break7']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text6($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text6']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text8') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text6']['text']) . '</div>';
            if ($report['free_text6']['page_break8']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text7($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text7']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text9') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text7']['text']) . '</div>';
            if ($report['free_text7']['page_break9']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text8($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text8']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text10') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text8']['text']) . '</div>';
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
            $html .= '<h3>' . $trans->phrase('pdf_text11') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text9']['text']) . '</div>';
            if ($report['free_text9']['page_break11']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text10($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text10']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text11a') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text10']['text']) . '</div>';
            if ($report['free_text10']['page_break12']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text11($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text11']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text11b') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text11']['text']) . '</div>';
            if ($report['free_text11']['page_break13']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text12($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text12']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text11c') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text12']['text']) . '</div>';
            if ($report['free_text12']['page_break14']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text13($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text13']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text11d') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text13']['text']) . '</div>';
            if ($report['free_text13']['page_break15']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    function free_text14($report, $trans, $index)
    {
        $color_yellow = '#EBAF33';
        $html = '';
        if ($report['free_text14']['enabled']) {
            $html .= "<div style='width: 100%;'>";
            $html .= '<h3>' . $trans->phrase('pdf_text12') . '</h3>';
            $html .= '</div>';
            $html .= "<div style='text-align: justify;'>" . htmlspecialchars_decode($report['free_text14']['text']) . '</div>';
            if ($report['free_text14']['page_break16']) {
                $html .= '<pagebreak />';
            }
        }

        return $html;
    }

    if ($report) {
        $report_content = json_decode($report_format['report_content'], true);
        $_GET['id'] = $ticket_id;
        $user = $Database->get_data('user_id', $ticket['ticket_user_id'], 'user', true);
        $company = $Database->get_data('company_id', $ticket['ticket_company_id'], 'company', true);
        // $methods = $Database->get_data('method_id', $ticket['ticket_company_id'], 'company', true);
        $index = 1;

        $header_footer = header_footer($report_content, $user, $company, $trans);
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

        $free_text6 = free_text6($report_content, $trans, $index);
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

        $front_prefix_html = "<html><head>
        <link rel='preconnect' href='https://fonts.googleapis.com'>
        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
        <link href='https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap' rel='stylesheet'> 
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap');
        </style> 

        </head><body style='font-family: \"Montserrat\", sans-serif; background-image:url(\"../images/bg-1.png\"); background-image-resize: 6;'>" . $header_footer . '<div>';
        $front_subfix_html = '</div></body></html>';

        $content_prefix_html = "<html><body style='background-image:url(\"../images/2.png\"); background-image-resize: 6;'>" . $header_footer . '<div>';
        $content_subfix_html = '</div></body></html>';

        $normal_prefix_html = "<html><body style='background-color: #d7dbdd; background-image:url(\"../images/2.png\"); background-image-resize: 6;'>" . $header_footer . '<div>';
        $normal_subfix_html = '</div></body></html>';

        $intro_prefix_html = "<html><body style='background-color: #fff; background-image:url(\"../images/3.png\"); background-image-resize: 6;'>" . $header_footer . '<div>';
        $intro_subfix_html = '</div></body></html>';

        $common_prefix_html = "<html><body style='background-color: #fff; background-image:url(\"../images/4.png\"); background-image-resize: 6;'>" . $header_footer . '<div>';
        $common_subfix_html = '</div></body></html>';

        // Generate PDF
        // file_put_contents('php://stderr', print_r($html, TRUE));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        if ($report['front_page']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($front_prefix_html . $front_page . $front_subfix_html);
        }

        if ($report['free_text1']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 20, 10, 14, 10, 8, 10);
            $mpdf->WriteHTML($normal_prefix_html . $free_text1 . $normal_subfix_html);
        }

        if ($report['intro_text']['enabled']) {
            // $common_text = $this->generate_common_text($intro_text);
            // $mpdf->WriteHTML($intro_prefix_html.$intro_text.$intro_subfix_html);

            // New Code
            $text_header = "<div style='width: 100%;'>";
            $text_header .= '<img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px;" />';
            $text_header .= "<div style='width: 100%; height: 80px;'></div>";

            for ($i = 0; $i <= strlen($report['intro_text']['text']); $i += 2300) {
                // $html .= "<div style='text-align: justify;'>".substr(htmlspecialchars_decode($report['intro_text']['text']), 0, 35).'</div>';
                // $html .= "<div style='text-align: justify;'>".substr(htmlspecialchars_decode($report['intro_text']['text']), 36, 155).'</div>';

                $mpdf->AddPage('P', '', '', '', '', 10, 10, 14, 10, 8, 10);
                if ($i == 0) {
                    $text_title = '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 50px; font-size: 45px">1</td>
                            <td>' . $trans->phrase('pdf_text3') . '</td>
                        </tr>
                    </table>';
                    $text_title .= '</div>';
                    $text_title .= "<div style='width: 100%; height: 55px;'></div>";
                    $text_body = "<div style='text-align: justify; font-size: 12px !important;'>" . substr(htmlspecialchars_decode($report['intro_text']['text']), $i, 2300) . '</div>';

                    $mpdf->WriteHTML($intro_prefix_html . $text_header . $text_title . $text_body . $intro_subfix_html);
                } else {
                    $text_body = "<div style='text-align: justify; font-size: 12px !important;'>" . substr(htmlspecialchars_decode($report['intro_text']['text']), $i, 2300) . '</div>';
                    $mpdf->WriteHTML($common_prefix_html . $text_header . $text_body . $common_subfix_html);
                }
            }
        }

        if ($report['free_text2']['enabled']) {
            // $mpdf->AddPage();
            // $mpdf->WriteHTML($normal_prefix_html.$free_text2.$normal_subfix_html);

            $text_header = "<div style='width: 100%;'>";
            $text_header .= '<img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px;" />';
            $text_header .= "<div style='width: 100%; height: 80px;'></div>";

            for ($i = 0; $i <= strlen($report['free_text2']['text']); $i += 2300) {
                $mpdf->AddPage('P', '', '', '', '', 10, 10, 14, 10, 8, 10);
                if ($i == 0) {
                    $text_title = '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 50px; font-size: 45px">2</td>
                            <td>' . $trans->phrase('pdf_text4') . '</td>
                        </tr>
                    </table>';
                    $text_title .= '</div>';
                    $text_title .= "<div style='width: 100%; height: 55px;'></div>";
                    $text_body = "<div style='text-align: justify; font-size: 12px !important;'>" . substr(htmlspecialchars_decode($report['free_text2']['text']), $i, 2300) . '</div>';

                    $mpdf->WriteHTML($intro_prefix_html . $text_header . $text_title . $text_body . $intro_subfix_html);
                } else {
                    $text_body = "<div style='text-align: justify; font-size: 12px !important;'>" . substr(htmlspecialchars_decode($report['free_text2']['text']), $i, 2300) . '</div>';
                    $mpdf->WriteHTML($common_prefix_html . $text_header . $text_body . $common_subfix_html);
                }
            }
        }

        if ($report['free_text3']['enabled']) {
            // $mpdf->AddPage();
            // $mpdf->WriteHTML($normal_prefix_html.$free_text3.$normal_subfix_html);

            $text_header = "<div style='width: 100%;'>";
            $text_header .= '<img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px;" />';
            $text_header .= "<div style='width: 100%; height: 80px;'></div>";

            for ($i = 0; $i <= strlen($report['free_text3']['text']); $i += 2300) {
                $mpdf->AddPage('P', '', '', '', '', 10, 10, 14, 10, 8, 10);
                if ($i == 0) {
                    $text_title = '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 50px; font-size: 45px">2</td>
                            <td>' . $trans->phrase('pdf_text5') . '</td>
                        </tr>
                    </table>';
                    $text_title .= '</div>';
                    $text_title .= "<div style='width: 100%; height: 55px;'></div>";
                    $text_body = "<div style='text-align: justify; font-size: 12px !important;'>" . substr(htmlspecialchars_decode($report['free_text3']['text']), $i, 2300) . '</div>';

                    $mpdf->WriteHTML($intro_prefix_html . $text_header . $text_title . $text_body . $intro_subfix_html);
                } else {
                    $text_body = "<div style='text-align: justify; font-size: 12px !important;'>" . substr(htmlspecialchars_decode($report['free_text3']['text']), $i, 2300) . '</div>';
                    $mpdf->WriteHTML($common_prefix_html . $text_header . $text_body . $common_subfix_html);
                }
            }
        }

        if ($report['free_text4']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text4 . $normal_subfix_html);
        }

        if ($report['free_text5']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text5 . $normal_subfix_html);
        }

        if ($report['free_text6']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text6 . $normal_subfix_html);
        }

        if ($report['free_text7']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text7 . $normal_subfix_html);
        }

        if ($report['free_text8']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text8 . $normal_subfix_html);
        }

        if ($report['free_text9']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text9 . $normal_subfix_html);
        }

        if ($report['free_text10']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text10 . $normal_subfix_html);
        }

        if ($report['free_text11']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text11 . $normal_subfix_html);
        }

        if ($report['free_text12']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text12 . $normal_subfix_html);
        }

        if ($report['free_text13']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text13 . $normal_subfix_html);
        }

        if ($report['free_text14']['enabled']) {
            $mpdf->AddPage();
            $mpdf->WriteHTML($normal_prefix_html . $free_text14 . $normal_subfix_html);
        }

        $mpdf->Output('Report_main' . $ticket_id . '.pdf', 'D');
    } else {
        echo $trans->phrase('pdf_report_phrase6');
    }
} else {
    echo $trans->phrase('pdf_report_phrase7');
}
