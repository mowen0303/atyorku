<?php
$courseModel = new \admin\course\CourseModel();
$currentUser = new \admin\user\UserModel();
$flag = BasicTool::get('flag');
$argument ="&f_cid=".BasicTool::get('f_cid')."&f_title=".BasicTool::get('f_title')."&c_cid=".BasicTool::get('c_cid')."&c_title=".BasicTool::get('c_title');
$row = $flag=="update"?$courseModel->getRowOfEasyCourseDescriptionById(BasicTool::get('id')):null;
$uid = $flag == "update"?$row['user_id']:$currentUser->userId;
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 编辑课评</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listDescription<?php echo $argument?>">返回主列表</a>
</nav>
<article class="mainBox">
    <form action="courseController.php?action=addDescription" method="post">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="c_cid" value="<?php echo BasicTool::get('c_cid')?>" type="hidden">
        <input name="argument" value="<?php echo $argument?>" type="hidden">
        <header>
            <h2><?php echo BasicTool::get('f_title').BasicTool::get('c_title');?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>• 课程内容总结: (这个课主要是讲什么) </label>
                <textarea class="input input-textarea" name="summary" placeholder="" value=""><?php echo $row['summary']?></textarea>
            </div>
            <div>
                <label>分数构成:  例如: 1个Assignment: 占10%(题型)</label>
                <textarea class="input input-textarea" name="structure" placeholder="" value=""><?php echo $row['structure']?></textarea>
            </div>
            <div>
                <label>选课意见:</label>
                <textarea class="input input-textarea" name="wisechooes" placeholder="" value=""><?php echo $row['wisechooes']?></textarea>
            </div>
            <div>
                <label>高分攻略: (求学霸推荐一下拿A+的学习方法)</label>
                <textarea class="input input-textarea" name="strategy" placeholder="" value=""><?php echo $row['strategy']?></textarea>
            </div>
            <div>
                <label>作者ID<i>*</i>&nbsp;&nbsp;&nbsp;&nbsp;(你的ID: <?php echo $currentUser->userId ?> )</label>
                <input class="input input-size30" type="text" name="user_id" placeholder="" value="<?php echo $uid?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>

<article class="mainBox">
    <h2>调试数据</h2>
    <section>
        <?php
        echo $argument."<br><br>";
        foreach($row as $k => $v){
            echo $k." : ".$v."<br>";
        }
        ?>
    </section>
</article>