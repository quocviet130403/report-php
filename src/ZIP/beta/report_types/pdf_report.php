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

    function page_header($img)
    {
        $html = '';

        $html .= '<htmlpageheader name="firstpage" style="display:none;">';
        $html .= '<div><img src="../images/' . $img . '.png" /></div>';
        $html .= '</htmlpageheader>';

        $html .= '<htmlpageheader name="otherpages" style="display:none;">';
        $html .= '<div style="height: 30px;"></div>';
        $html .= '<div>
                    <img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; float: left" />
                    <img src="../images/4.png" />
                  </div><br /><br />';
        $html .= '</htmlpageheader>';

        $html .= '<sethtmlpageheader name="firstpage" value="on" show-this-page="1" />';
        $html .= '<sethtmlpageheader name="otherpages" value="on" />';

        return $html;
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
    /*
        function page_footer1($report, $user, $company, $trans)
        {
            $html = '';
            $html .= '<htmlpagefooter name="footer">';
            $html .= "<div style='float: right; text-align:right; width; 50px;'><span style='font-weight: bold; font-height: 16px; color: black;'>{PAGENO}</span>-{nbpg}</div>";
            $html .= '</htmlpagefooter>';
            $html .= '<sethtmlpagefooter name="footer" value="on" />';

            return $html;
        }
    */
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

            $html .= '<p style="font-size: 21pt; font-weight: normal; letter-spacing: 1px; line-height: 1.4">';
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
            $html .= '<p style="font-size: 14pt; font-weight: normal; line-height: 1.4">';
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
            $html .= "<div style='width: 100%; height: 97px;'></div>";
            $html .= '<h1 style="font-weight: 600; font-size: 38pt; text-align: center;">' . $trans->phrase('pdf_text2') . '</h1>';
            $html .= '</div>';
            $html .= "<div style='font-size: 21pt;'>" . htmlspecialchars_decode($report['free_text1']['text']) . '</div>';
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

            $html .= '<table>
                        <tr>
                            <td style="width: 30px"></td>
                            <td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td>
                        </tr>
                    </table>';

            $html .= '<div style="width: 100%; height: 50px;"></div>';

            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-weight: 600; font-size: 63.5pt;">1</td>
                            <td style="font-weight: 600; font-size: 21pt;">' . $trans->phrase('pdf_text3') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';

            $html .= '<div style="margin-top: 25px; margin-left: 25px; margin-right: 20px; font-size: 12pt">' . htmlspecialchars_decode($report['intro_text']['text']) . '</div>';

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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">2</td>
                            <td>' . $trans->phrase('pdf_text4') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text2']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">3</td>
                            <td>' . $trans->phrase('pdf_text5') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text3']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">4</td>
                            <td>' . $trans->phrase('pdf_text6') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text4']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">5</td>
                            <td>' . $trans->phrase('pdf_text7') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text5']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">6</td>
                            <td>' . $trans->phrase('pdf_text8') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text6']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">7</td>
                            <td>' . $trans->phrase('pdf_text9') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text7']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">8</td>
                            <td>' . $trans->phrase('pdf_text10') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text8']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">9</td>
                            <td>' . $trans->phrase('pdf_text11') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text9']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">9</td>
                            <td>' . $trans->phrase('pdf_text11a') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text10']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">9</td>
                            <td>' . $trans->phrase('pdf_text11b') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text11']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">9</td>
                            <td>' . $trans->phrase('pdf_text11c') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text12']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">9</td>
                            <td>' . $trans->phrase('pdf_text11d') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text13']['text']) . '</div>';
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
            $html .= '<div style="width: 100%;">';
            $html .= '<table><tr><td style="width: 30px"></td><td><img src="../images/logo-nogd-rapport.jpg" style="width: 200px; height: 80px; margin-left: 20px;" /></td></tr></table>';
            $html .= '<div style="width: 100%; height: 50px;"></div>';
            $html .= '<table style="width: 400px; font-size: 30px;">
                        <tr>
                            <td style="width: 40px;"></td>
                            <td style="width: 50px; font-size: 55px;">9</td>
                            <td>' . $trans->phrase('pdf_text12') . '</td>
                        </tr>
                    </table>';
            $html .= '</div>';
            $html .= '<div style="width: 100%; height: 55px;"></div>';

            $html .= '<div style="margin-left: 25px; margin-right: 20px;">' . htmlspecialchars_decode($report['free_text14']['text']) . '</div>';
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

        $page_header = page_header('intro');
        // $page_footer = page_footer($report_content, $user, $company, $trans);

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

        $prefix_html = "<html><head>
        <link rel='preconnect' href='https://fonts.googleapis.com'>
        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
        <link href='https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap' rel='stylesheet'> 
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap');
        </style>
        </head>";

        $page_footer = page_footer();
        $front_prefix_html = "<body style='font-family: \"Montserrat\", sans-serif; background-image:url(\"../images/bg-1.png\"); background-image-resize: 6;'>" . $page_footer . '<div>';
        $front_subfix_html = '</div></body></html>';

        $content_prefix_html = "<body style='background-image:url(\"../images/2.png\"); background-image-resize: 6;'>" . $page_footer . '<div>';
        $content_subfix_html = '</div></body></html>';

        $normal_prefix_html = "<body style='background-color: #d7dbdd; background-image:url(\"../images/2.png\"); background-image-resize: 6;'>" . $page_footer . '<div>';
        $normal_subfix_html = '</div></body></html>';

        $intro_prefix_html = '<html>
        <head>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap" rel="stylesheet"> 
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap");
                
                @page :first {
                    background: url("http://example.com/first.svg") 50% 0 repeat;
                }
                @page {
                    background: url("http://example.com/page.svg") 50% 0 repeat;
                }
                
            </style>
        </head>';
        $intro_prefix_html .= '<body style="background-color: #fff; background-image:url(\'../images/white.png\'); background-image-resize: 6;"><div>';
        $intro_subfix_html = '</div></body></html>';

        $common_prefix_html = "<body style='background-color: #fff; background-image:url(\"../images/4.png\"); background-image-resize: 6;'>" . $page_footer . '<div>';
        $common_subfix_html = '</div></body></html>';

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
            $mpdf->AddPage('P', '', 1, '', '', 0, 0, 50, 10, 0, 0);
            $page_header = page_header('intro');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $intro_text . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text2']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 10, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text2 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text3']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 10, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text3 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text4']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text4 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text5']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text5 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text6']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text6 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text7']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text7 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text8']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text8 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text9']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text9 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text10']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text10 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text11']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text11 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text12']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text12 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text13']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text13 . $page_footer . $intro_subfix_html);
        }

        if ($report['free_text14']['enabled']) {
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 14, 0, 0, 0);
            $page_header = page_header('ft3');
            $mpdf->WriteHTML($intro_prefix_html . $page_header . $free_text14 . $page_footer . $intro_subfix_html);
        }

        $mpdf->Output('Report_main' . $ticket_id . '.pdf', 'D');
    } else {
        echo $trans->phrase('pdf_report_phrase6');
    }
} else {
    echo $trans->phrase('pdf_report_phrase7');
}
