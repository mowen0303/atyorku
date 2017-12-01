<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
    BasicTool::loadSnippet(BasicTool::get(s),'用户','listUser');  //-- 注意 --//
    require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php"
?>
