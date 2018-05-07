<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'考试回忆录','getKnowledgeCategories');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>