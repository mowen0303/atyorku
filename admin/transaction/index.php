<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'交易记录','getTransactions');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>