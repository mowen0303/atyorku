<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
BasicTool::loadSnippet(BasicTool::get(s),'地图列表','listLocation');
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php";
//?>
<!--    <header class="topBox">-->
<!--        <h1>--><?php //echo "地图列表"?><!--</h1>-->
<!--        <nav class="mainNav">-->
<!--            <a class="btn" href=href="index.php?action=showFormAdd">添加大楼位置</a>-->
<!--        </nav>-->
<!--    </header>-->
<!--    <div style="width: 50%; height: 100%; float: left">--><?php //BasicTool::loadSnippet(BasicTool::get(s),'','map'); ?><!--</div>-->
<!--    <div style="width: 50%; height: 100%; float: left">--><?php //BasicTool::loadSnippet(BasicTool::get(s),'地图列表','listLocation'); ?><!--</div>-->
<?php
//require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php";