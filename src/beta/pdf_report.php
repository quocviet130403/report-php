<?php
session_start();

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

require_once('config.php');
require_once('database.php');
require_once('vendor/fpdf/fpdf.php');
require_once('translation.php');
$trans = new Translation($_SESSION['trans']);

class PDF extends FPDF
{
    private $report;
    private $user;
    private $company;
    private $questions;
    private $methods;
    private $trans;
    private $general_text;

    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';

    function __construct($report, $user, $company, $questions, $methods, $trans, $general_text){
        $this->report = $report;
        $this->user = $user;
        $this->company = $company;
        $this->questions = $questions;
        $this->methods = $methods;
        $this->trans = $trans;
        $this->general_text = $general_text;

        parent::__construct();
    }

    function WriteHTML($html)
    {
        // HTML parser
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(7, $e);
                    //$this->MultiCell(0, 5, $e, 0, 'L', true);
            }
            else
            {
                // Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extract attributes
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        // Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        // Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
        // Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

    // Page header
    function Header()
    {
        // Logo
        //$this->Image('logo.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',12);
        // Move to the right
        //$this->Cell(80);
        // Title
        $this->SetTextColor(100);
        $this->Cell(55, 20, $this->company['company_name'], 0, 0, 'L');
        $this->Cell(0, 20, $this->user['user_name'], 0, 0, 'R');
        $this->SetDrawColor(0);
        $this->Line(0,25,210,25);
        // Line break
        $this->Ln(20);
    }
    //Front page
    function FrontPage(){
        $this->AddPage();
        $this->SetFont('Times','B', 46);
        $this->SetTextColor(101, 124, 156);
        $trans_phrase1 = iconv('UTF-8', 'windows-1252', $this->trans->phrase('pdf_report_phrase10'));
        $this->Cell(0, 50, strtoupper($trans_phrase1).' '.$this->report['ticket_id'], 0, 1, 'C');

        $this->SetFont('Arial', 'B', 28);
        $this->SetTextColor(255, 0, 0);
        if($this->report['ticket_status'] == 'process'):
            $trans_phrase2 = iconv('UTF-8', 'windows-1252', $this->trans->phrase('pdf_report_phrase8'));
            $this->Cell(0, 20, ucfirst($trans_phrase2), 0, 1, 'C');
        elseif($this->report['ticket_status'] == 'closed'):
            $trans_phrase3 = iconv('UTF-8', 'windows-1252', $this->trans->phrase('pdf_report_phrase9'));
            $this->Cell(0, 20, ucfirst($trans_phrase3), 0, 1, 'C');
        endif;

        if(file_exists('images/company_logo/'.$this->company['company_id'].'.jpg')){
            $this->Image('images/company_logo/'.$this->company['company_id'].'.jpg', 65, 130, 80, 0);
        }
        else if(file_exists('images/company_logo/'.$this->company['company_id'].'.jpeg')){
            $this->Image('images/company_logo/'.$this->company['company_id'].'.jpeg', 65, 130, 80, 0);
        }
        if(file_exists('images/company_logo/'.$this->company['company_id'].'.png')){
            $this->Image('images/company_logo/'.$this->company['company_id'].'.png', 65, 130, 80, 0);
        }
    }

    //General text
    function GeneralText(){
        if(strlen($this->general_text) > 0){
            $this->AddPage();
            $this->SetTextColor(0);
            $this->SetFont('Arial', '', 14);
            $this->Ln(5);
            $decoded_general_text = htmlspecialchars_decode( $this->general_text, ENT_QUOTES);
            $general_text = iconv('UTF-8', 'windows-1252', html_entity_decode($decoded_general_text));
            $this->WriteHTML($general_text);
        }
    }

    //Add question
    function Tips(){
        $this->AddPage();
        $this->SetFont('Times', 'B', 28);
        $this->SetTextColor(255, 0, 0);
        $trans_phrase3 = iconv('UTF-8', 'windows-1252', $this->trans->phrase('pdf_report_phrase3'));
        $this->Cell(0, 20, $trans_phrase3, 0, 1, 'C');
        foreach($this->questions as $question_count => $question){
            if($question && $question['question_tip']){
                if($this->GetY() > 240){
                    $this->AddPage();
                }
                else{
                    $this->Ln(10);
                }
                $this->SetFont('Arial', '', 10);
                $this->SetTextColor(0);
                $tips = iconv('UTF-8', 'windows-1252', html_entity_decode($question['question_tip']));
                $this->SetFont('Times', 'B', 15);
                $this->Ln(5);
                $tips_title = $this->trans->phrase('pdf_report_phrase3') . " (" . $this->trans->phrase('pdf_report_phrase4') . " " . ($question_count+1) . "): ";
                $tips_title = iconv('UTF-8', 'windows-1252', $tips_title);
                $this->Write(5, "$tips_title ");
                $this->SetFont('Arial', 'I', 12);
                $this->WriteHTML($tips);
                $this->Ln(5);
            }
        }
    }

    //Add methods
    function Methods($count){
        $this->AddPage();
        $this->SetFont('Times', 'B', 28);
        $this->SetTextColor(255, 0, 0);
        $trans_phrase5 = iconv('UTF-8', 'windows-1252', $this->trans->phrase('pdf_report_phrase5'));
        $this->Cell(0, 20, $trans_phrase5, 0, 1, 'C');
        if(count($this->methods) > 0){
            foreach($this->methods as $method_count => $method){
                //Taking only top two.
                if($method_count >= 2) break;

                if($this->GetY() > 240){
                    $this->AddPage();
                }
                else{
                    $this->Ln(10);
                }
                $this->SetDrawColor(100, 100, 100);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255);
                $this->SetFont('Times', 'B', 16);
                $this->Cell(15, 10, ($method_count+1), 0, 0, 'C', true);
                $method_name = iconv('UTF-8', 'windows-1252', $method['method_name']);
                $this->Cell(0, 10, $method_name, 0, 1, 'L', true);
                $this->SetTextColor(0);
                $this->SetFont('Arial', '', 14);
                $this->Ln(5);
                $decoded_method_details = htmlspecialchars_decode( $method['method_details'], ENT_QUOTES);
                $method_details = iconv('UTF-8', 'windows-1252', html_entity_decode($decoded_method_details));
                $this->WriteHTML($method_details);
                $this->Ln(20);
            }
        }
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetTextColor(100);
        $this->SetFont('Arial','I',12);
        // Page number
        $site_name = iconv('UTF-8', 'windows-1252', NAME);
        $this->Cell(0, 10, $site_name, 0, 0, 'L');
        $trans_phrase2 = iconv('UTF-8', 'windows-1252', $this->trans->phrase('pdf_report_phrase2'));
        $this->Cell(0,10,$trans_phrase2.' '.$this->PageNo().'/{nb}',0,0,'R');
    }
}

if(isset($_SESSION['account-type']) && isset($_GET['id']) && $_GET['id'] > 0){
    $Database = new Database();

    $ticket_id = $_GET['id'];

    $report = $Database->get_data('ticket_id', $ticket_id, 'ticket', true);

    //Check answer count is grater than 20
    if($report){
        $answer_count = 0;
        $ticket_response = json_decode($report['ticket_response'], true);
        if($ticket_response){
            foreach($ticket_response as $question_id => $question_answer){
                if($question_answer > 0){
                    $answer_count++;
                }
            }
        }

        //for answer is <= 20; invalidate report.
        if($answer_count <= 20){
            $report = null;
        }
    }

    if($report){
        $user = $Database->get_data('user_id', $report['ticket_user_id'], 'user', true);
        $company = $Database->get_data('company_id', $report['ticket_company_id'], 'company', true);
        $questions = array();
        $methods = array();

        $report_text = null;
        $sql = "SELECT * FROM `text` WHERE text_company={$company['company_id']} AND text_lang='{$_SESSION['trans']}' AND text_selector='general_report_text'";
        $report_content = $Database->get_connection()->prepare($sql);
        $report_content->execute();
        if($report_content->rowCount() < 1) $report_content = false;
        else $report_content = $report_content->fetch(PDO::FETCH_ASSOC);

        if($report_content) $report_text = $report_content['text_content'];

        $report_response = json_decode($report['ticket_response'], true);
        foreach($report_response as $resp_key => $resp_value){
            $sql = "SELECT * FROM question_content WHERE question_id={$resp_key} AND lang_code='{$_SESSION['trans']}'";
            $question_data = $Database->get_connection()->prepare($sql);
            $question_data->execute();

            $follow_up = false;
            $sql = "SELECT * FROM question_deadline WHERE question_id={$resp_key} and ticket_id={$report['ticket_id']}";
            $follow_up_data = $Database->get_connection()->prepare($sql);
            $follow_up_data -> execute();
            if($follow_up_data->rowCount()>0)
                $follow_up = $follow_up_data->fetch(PDO::FETCH_ASSOC);

            if($question_data->rowCount() > 0){
                $question_content = $question_data->fetch(PDO::FETCH_ASSOC);

                $tmp_tip = '';
                if($resp_value == 2) $tmp_tip = htmlspecialchars_decode( $question_content['question_tips_yes'], ENT_QUOTES);
                elseif($resp_value == 1) $tmp_tip = htmlspecialchars_decode( $question_content['question_tips_no'], ENT_QUOTES);

                $temp = array(
                    'question_id' => $resp_key,
                    'question_name' => $question_content['question_name'],
                    'question_response' => $resp_value,
                    'question_tip' => $tmp_tip,
                    'follow_up' => $follow_up
                );
                array_push($questions, $temp);
            }
            else{
                array_push($questions, '');
            }
        }

        $report_method = json_decode($report['ticket_methods'], true);
        foreach($report_method as $method_key => $method_selection){
            if($method_selection == 0) continue;

            $sql = "SELECT * FROM method_content WHERE method_id={$method_key} AND lang_code='{$_SESSION['trans']}'";
            $method_data = $Database->get_connection()->prepare($sql);
            $method_data->execute();

            if($method_data->rowCount() > 0){
                $method_content = $method_data->fetch(PDO::FETCH_ASSOC);
                $decoded_method_details = htmlspecialchars_decode($method_content['method_details'] , ENT_QUOTES);
                $temp = array(
                    'method_id' => $method_key,
                    'method_selection' => $method_selection,
                    'method_name' => $method_content['method_name'],
                    'method_details' => $decoded_method_details
                );
                array_push($methods, $temp);
            }
            else{
                array_push($questions, '');
            }
        }



        // Instanciation of inherited class
        //$config = array('SITE_NAME' => SITE_NAME);
        $pdf = new PDF($report, $user, $company, $questions, $methods, $trans, $report_text);
        $pdf->SetTitle($trans->phrase('pdf_report_phrase1').' - '.$report['ticket_id']);
        $pdf->SetMargins(20, 10);
        $pdf->AliasNbPages();

        $pdf->FrontPage();
        $pdf->GeneralText();
        $pdf->Tips();
        $pdf->Methods(2);
        //$pdf->Output();
        $pdf->Output('D', strtolower($trans->phrase('pdf_report_phrase1').'_'.$report['ticket_id'].'.pdf'));
    }
    else{
        echo $trans->phrase('pdf_report_phrase6');
    }
}
else{
    echo $trans->phrase('pdf_report_phrase7');
}
?>