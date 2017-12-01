<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'广告','getAdCategories');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>