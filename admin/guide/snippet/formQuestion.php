<?php
$guideModel = new \admin\guide\GuideModel();
$currentUser = new \admin\user\UserModel();
$question_id = BasicTool::get('q_id');
$user_id = BasicTool::get('uid');
$flag = $question_id == null ? 'add' : 'update';
$row = $flag=='add' ? null : $guideModel->getRowOfQuestionById($question_id);

?>
<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'发布问题':'修改问题';
        ?></h1>
</header>

<article class="mainBox">
    <form action="guideController.php?action=modifyQuestion" method="post">
    
        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="q_id" value="<?php echo $question_id ?>" type="hidden">
            <div>
                <label>题目<i>*</i></label>
                <input class="input" type="text" name="question" value="<?php echo $row['question'] ?>">
            </div>
            <div>
                <label>图片</label>
                <input class="input" type="text" name="img" value="<?php echo $row['img'] ?>">
            </div>
            <div>
                <lable>选项</lable>
                <input class="input" type="text" name="options" value="<?php echo $row['options'] ?>">
            </div>
            <div>
                <lable>正确答案</lable>
                <input class="input" type="text" name="solution" value="<?php echo $row['solution'] ?>">
            </div>

        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
