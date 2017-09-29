<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'地图列表','listLocation');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php";