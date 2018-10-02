<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$guideModel = new \admin\guide\GuideModel();
$guide_id = BasicTool::get('guide_id');
$guideModel->increaseCountNumber($guide_id);
$arr = $guideModel->getRowOfGuideById($guide_id);
if ($arr["type"] == "reproduced"){
    header('Location: '.$arr["reproduced_source_url"]);
    die();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_header.html";

if($arr["type"] == "video"){
    require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/videoBody.php";
}else{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/articleBody.php";
}

?>
