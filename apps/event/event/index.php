<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/event/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'活动管理','getEventsByCategory');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>
