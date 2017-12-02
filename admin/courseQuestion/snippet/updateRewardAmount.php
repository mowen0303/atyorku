<?php
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$question_id = BasicTool::get("question_id");
$question = $questionModel->getQuestionById($question_id);
?>
<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-'."更改积分"
        ?></h1>
</header>

<article class="mainBox">
    <form action="/admin/courseQuestion/courseQuestionController.php?action=updateRewardAmount" method="post" enctype="multipart/form-data">
        <input name="id" value="<?php echo $question_id ?>" type="hidden">
        <div>
            <h4>提问ID:<?php echo $question_id?></h4>
            <label>积分奖励<i>*</i></label>
            <input type="number" class="input input-size30" name="reward_amount"/>
        </div>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>
