<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
    BasicTool::loadSnippet(BasicTool::get(s),'指南','listGuideClass');  //-- 注意 --//
    require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>
