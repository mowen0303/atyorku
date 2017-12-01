<?php
$professorModel = new \admin\professor\ProfessorModel();
$flag = BasicTool::get("flag");
?>

<header class="topBox">
    <h1><?php echo ($typeStr . $pageTitle);?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listProfessor">返回</a>
</nav>
<article class="mainBox">
    <form action="professorController.php?action=modifyProfessor" method="post">
        <input name="flag" id="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改教授":"添加教授"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label><?php echo $typeStr ?>教授 First Name<i>*</i></label>
                <input class="input" type="text" name="firstname" value="<?php echo BasicTool::get('firstname') ?>">
                <label><?php echo $typeStr ?>教授 Last Name<i>*</i></label>
                <input class="input" type="text" name="lastname" value="<?php echo BasicTool::get('lastname') ?>">
                <label><?php echo $typeStr ?>教授 Middle Name</label>
                <input class="input" type="text" name="middlename" value="<?php echo BasicTool::get('middlename') ?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>
