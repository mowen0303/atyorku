<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/event/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'活动','getEventCategories');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>