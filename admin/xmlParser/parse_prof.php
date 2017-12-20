<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$parser = new \admin\xmlParser\XMLParser();
$first_name = BasicTool::get("first_name");
$first_name = $first_name?$first_name:"";
$last_name = BasicTool::get("last_name");
$last_name = $last_name?$last_name:"";
$middle_name = BasicTool::get("middle_name");
$middle_name = $middle_name?$middle_name:"";
$arr=[];
$arr["middlename"]=$middle_name;
$arr["firstname"] = $first_name;
$arr["lastname"] = $last_name;
$arr["view_count"] = 0;
$parser->insert("professors",$arr);
