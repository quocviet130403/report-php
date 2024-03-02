<?php
header('Content-Type: application/json; charset=utf-8');
//session_start();
require_once('../config.php');
require_once('../database.php');
require_once('../imports.php');
require_once('../translation.php');

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

$Database = new Database();

$trans = new Translation($_SESSION['trans']);

$redirect = true;
if(basename($_SERVER['SCRIPT_FILENAME']) == 'tos.php')
    $redirect = false;

if(isset($_SESSION['account-type']) && isset($_SESSION['account-id']) && $redirect && ($_SESSION['is_verified'])){
    if($_SESSION['account-type'] == 'super_admin' || $_SESSION['account-type'] == 'support_admin')
        header("Location: user/index.php?route=admin_profile");
    if($_SESSION['account-type'] == 'consultant')
        header("Location: user/index.php?route=consultant_profile");
    if($_SESSION['account-type'] == 'company')
        header("Location: user/index.php?route=company_profile");
    if($_SESSION['account-type'] == 'user')
        header("Location: user/index.php?route=profile");
    die();
}

$Database = new Database();

//Getting categories
$categoriesList = $categories = array();
$categories_data = $Database->get_multiple_data('category_type', 'question', 'category');

$sql = "SELECT cat.category_name, cat.category_id as cat_ID, qus.category_id as qus_ID, qus.question_id, quc.question_name FROM category_content cat JOIN question qus ON (cat.category_id = qus.category_id) JOIN question_content quc ON (qus.question_id = quc.question_id) WHERE cat.lang_code='{$_SESSION['trans']}' AND quc.lang_code='{$_SESSION['trans']}'";

$category_content = $Database->get_connection()->prepare($sql);
$category_content->execute();

$row = $category_content->rowCount();

if($category_content->rowCount() < 1) $category_content = false;
else $category_content = $category_content->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

foreach($category_content as $key => $categoryData) {
    $record = array();

    foreach ($categoryData as $questionData) {
        $recordData = array(
                "name" => $questionData['question_id'],
                "value" => $questionData['question_id']
        );
        array_push($record, $recordData);
    }


    $single_category["name"] = $key;
    $single_category["children"] = $record;



    array_push($categoriesList, $single_category);
}

$categories['name'] = 'categories';
$categories['rows'] = $row;
$categories['children'] = $categoriesList;

echo json_encode($categories);