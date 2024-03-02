<?php

require ('../ReportPermission.php');
session_start();

$reportPermission = new ReportPermission('253','26','0','0');

print_r($reportPermission->getAnalysisResponderData());