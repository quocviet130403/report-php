<?php
class ReportPermission{

    private $analysisId;
    private $isAnalysisSubmitted;
    private $totalReponder;
    private $closedReponder;


    public function __construct($analysisID) {

        $this->analysisId   = $analysisID;

    }

    public function getAnalysis(){

        require_once('database.php');
        $Database = new Database();

        return $Database->get_data('ticket_id', $this->analysisId, 'ticket', true);

    }

    public function getReportType(){

        require_once('database.php');
        
        $Database = new Database();
        
        $req_info = $Database->get_data('ticket_id', $this->analysisId, 'tbl_report_request', true);

        return $req_info ? $req_info['report_id'] : 26;

    }

    public function getAnalysisSubmission(){

        $ticket = $this->getAnalysis();

        require_once('database.php');
        $Database = new Database();
        
        //Getting questions
        $questions = $Database->get_multiple_data(false, false, 'question');

        $question_response = json_decode($ticket['ticket_response'], true);
        
        if ($questions) {

            $countNotFollowUpQuestion = 0;
            $questionIds = "";
            $answerIds = "";
            $unAnswerIds = "";

            foreach ($questions as $question) {

                //Increment question number
                if ($question['question_follow_up'] == 0) {
                    $countNotFollowUpQuestion++;
                    if ($countNotFollowUpQuestion == 1) {
                        $questionIds = $question['question_id'];
                    } else {
                        $questionIds = $questionIds . "," . $question['question_id'];
                    }
                }


                //Question response
                $yes_check = false;
                $no_check = false;
                $check_1 = false;
                $check_2 = false;
                $check_3 = false;
                $check_4 = false;
                $check_5 = false;
                $check_6 = false;

                if (isset($question_response[$question['question_id']])) {
                    if ($question['question_type'] == 'yes-no') {
                        if ($question_response[$question['question_id']]['answer'] == 2) {
                            $yes_check = true;
                        } else if ($question_response[$question['question_id']]['answer'] == 1) {
                            $no_check = true;
                        }
                    }

                    if ($question['question_type'] == 'mcq') {
                        if ($question_response[$question['question_id']]['answer'] == 1) $check_1 = true;
                        else if ($question_response[$question['question_id']]['answer'] == 2) $check_2 = true;
                        else if ($question_response[$question['question_id']]['answer'] == 3) $check_3 = true;
                        else if ($question_response[$question['question_id']]['answer'] == 4) $check_4 = true;
                        else if ($question_response[$question['question_id']]['answer'] == 5) $check_5 = true;
                        else if ($question_response[$question['question_id']]['answer'] == 6) $check_6 = true;
                    }
                }

                if ($question['question_follow_up'] == 0) {
                    if (
                        $yes_check
                        || $no_check
                        || $check_1
                        || $check_2
                        || $check_3
                        || $check_4
                        || $check_5
                        || $check_6
                    ) {
                        if (strlen($answerIds) <= 0) {
                            $answerIds = $question['question_id'];
                        } else {
                            $answerIds = $answerIds . "," . $question['question_id'];
                        }
                    } else {
                        if (strlen($unAnswerIds) <= 0) {
                            $unAnswerIds = $question['question_id'];
                        } else {
                            $unAnswerIds = $unAnswerIds . "," . $question['question_id'];
                        }
                    }
                }
            }
            
            return $unAnswerIds === "" ? true : false;
        }

        return false;

    }

    public function getAnalysisResponderData(){

        $ticket = $this->getAnalysis();

        require_once('database.php');
        $Database = new Database();

        $allResponders = $Database->get_multiple_data('ticket_id', $ticket['ticket_id'], 'tbl_ticket_responder', '=', true, 'responder_id ASC', false);

        $totalResponder = $pendingResponder = $closedResponder = [];

        if($allResponders) {
            foreach ($allResponders as $responder) {
                
                $totalResponder[] = 1;
    
                $responderDetails = $Database->get_data('responder_id', $responder['responder_id'], 'responder_ticket_data', true);
    
                if($responderDetails){
    
                    $responderResponses = json_decode($responderDetails['ticket_response'], true);
    
                    $responderQuestions = $Database->get_multiple_data(false, false, 'question_res');
    
                    $countNotFollowUpQuestion = 0;
                    $questionIds_test = "";
                    $answerIds_test = "";
                    $unAnswerIds = "";
                                    
                    foreach ($responderQuestions as $question) {
    
                        if ($question['question_follow_up'] == 0) {
                            $countNotFollowUpQuestion++;
                            if ($countNotFollowUpQuestion == 1) {
                                $questionIds_test = $question['question_res_id'];
                            } else {
                                $questionIds_test = $questionIds_test . "," . $question['question_res_id'];
                            }
                        }
    
                        //Question response
                        $yes_check = false;
                        $no_check = false;
                        $check_1 = false;
                        $check_2 = false;
                        $check_3 = false;
                        $check_4 = false;
                        $check_5 = false;
                        $check_6 = false;
    
                        if (isset($responderResponses[$question['question_res_id']])) {
                            if ($question['question_type'] == 'yes-no') {
                                if ($responderResponses[$question['question_res_id']]['answer'] == 2) {
                                    $yes_check = true;
                                } else if ($responderResponses[$question['question_res_id']]['answer'] == 1) {
                                    $no_check = true;
                                }
                            }
                            if ($question['question_type'] == 'mcq') {
                                if ($responderResponses[$question['question_res_id']]['answer'] == 1) $check_1 = true;
                                else if ($responderResponses[$question['question_res_id']]['answer'] == 2) $check_2 = true;
                                else if ($responderResponses[$question['question_res_id']]['answer'] == 3) $check_3 = true;
                                else if ($responderResponses[$question['question_res_id']]['answer'] == 4) $check_4 = true;
                                else if ($responderResponses[$question['question_res_id']]['answer'] == 5) $check_5 = true;
                                else if ($responderResponses[$question['question_res_id']]['answer'] == 6) $check_6 = true;
                            }
                        }
    
                        if ($question['question_follow_up'] == 0) {
                            if (
                                $yes_check
                                || $no_check
                                || $check_1
                                || $check_2
                                || $check_3
                                || $check_4
                                || $check_5
                                || $check_6
                            ) {
                                if (strlen($answerIds_test) <= 0) {
                                    $answerIds_test = $question['question_res_id'];
                                } else {
                                    $answerIds_test = $answerIds_test . "," . $question['question_res_id'];
                                }
                            } else {
                                if (strlen($unAnswerIds) <= 0) {
                                    $unAnswerIds = $question['question_res_id'];
                                } else {
                                    $unAnswerIds = $unAnswerIds . "," . $question['question_res_id'];
                                }
                            }
                        }
                    }
    
                    if(count(explode(',', $answerIds_test)) === count(explode(',',$questionIds_test))){
                        $closedResponder[] = 1;
                    } else {
                        $pendingResponder[] = 1;
                    }
    
                } else {
                    $pendingResponder[] = 1;
                }
    
            }            

            $this->totalReponder = count($totalResponder);
            $this->closedReponder = count($closedResponder);

        } else {
            $this->totalReponder = 0;
            $this->closedReponder = 0;

            return ['totalResponder' => 0, 'pendingResponder' => 0, 'closedResponder' => 0];
        }
    }

    public function canChangeReportFormat()
    {
        return $this->getAnalysis()['ticket_status'] === 'closed' ? false : true;
    }

    public function canCloseAnalysis()
    {
        $canSubmitAnalysis = $this->reportPermissions();
        
        $this->getAnalysisResponderData();

        $getAnalysisSubmission = $this->getAnalysisSubmission();

        require_once('translation.php');

        $trans = new Translation($_SESSION['trans']);

        if($canSubmitAnalysis['reportType'] == '26') {

            if($this->totalReponder < 2){

                return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase67")];
            } else if($this->closedReponder < 2){

                return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase69")];
            } else if(!$getAnalysisSubmission){

                return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase66")];
            }
            //  else if(!$this->isAnalysisSubmitted){
            //     return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder, 'msg'=>$trans->phrase("user_ticket_phrase66")];

            // }
             else  {
                return ['status'=>true];
            }

        } else
            if($canSubmitAnalysis['reportType'] == '27') {

                if(!$getAnalysisSubmission){
                    return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase66")];
                }
                //  else if(!$this->isAnalysisSubmitted){
                //     return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase66")];
                // }
                 else {
                    return ['status'=>true];
                }

            } else
                if($canSubmitAnalysis['reportType'] == '30') {

                    if($this->totalReponder < 2){
                        return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase67")];
                    } else if($this->closedReponder < 2){

                        return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase69")];
                    }  else {
                        return ['status'=>true];
                    }

                } else
                    if($canSubmitAnalysis['reportType'] == '31') {

                        if($this->totalReponder < 1){
                            return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase68")];
                        } else if($this->closedReponder < 1){

                            return ['status'=>false, 'totalReponder' => $this->totalReponder, 'closedReponder' => $this->closedReponder,'msg'=>$trans->phrase("user_ticket_phrase70")];
                        }  else {
                            return ['status'=>true];
                        }
                    } else {
                        return ['status'=>false];
                    }
    }

    public function reportPermissions() {
        
        if ($this->getReportType() == 26) {

            return ['reportType' => $this->getReportType(), 'canAnalysis' => true, 'canResponder' => true, 'responder_limit' => 999];

        } elseif ($this->getReportType() == 27) {

            return ['reportType' => $this->getReportType(), 'canAnalysis' => true, 'canResponder' => true, 'responder_limit' => 1];

        } elseif ($this->getReportType() == 30) {

            return ['reportType' => $this->getReportType(), 'canAnalysis' => false, 'canResponder' => true, 'responder_limit' => 2];

        } elseif ($this->getReportType() == 31) {

            return ['reportType' => $this->getReportType(), 'canAnalysis' => false, 'canResponder' => true, 'responder_limit' => 1];

        } else {
            return false;
        }
    }

    public function canInviteResponder() {

        $permission = $this->reportPermissions();

        require_once('translation.php');
        $trans = new Translation($_SESSION['trans']);

        $this->getAnalysisResponderData();

        if(!$permission) {
            return 'Invalid Request.';
        }
        
        if ($this->getReportType() == 26 && $permission['responder_limit'] <= $this->totalReponder) {

            return ['status'=>false,'msg' => $trans->phrase("user_ticket_phrase71").$permission['responder_limit']];

        } elseif ($this->getReportType() == 27 && !$permission['canResponder']) {

            return ['status'=>false,'msg' => $trans->phrase("user_ticket_phrase72")];

        } elseif ($this->getReportType() == 30 && $permission['responder_limit'] <= $this->totalReponder) {

            return ['status'=>false,'msg' => $trans->phrase("user_ticket_phrase74").$permission['responder_limit']];

        } elseif ($this->getReportType() == 31 && $permission['responder_limit'] <= $this->totalReponder) {

            return ['status'=>false,'msg' => $trans->phrase("user_ticket_phrase73").$permission['responder_limit']];

        } else {
            return ['status'=>true,'msg' => 'success'];
        }
    }

}