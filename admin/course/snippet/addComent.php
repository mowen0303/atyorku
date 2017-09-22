<?php
$courseClassId = BasicTool::get('classId');
$courseNumberId = BasicTool::get('c_cid');
$courseModel = new \admin\course\CourseModel();

$arr = $courseModel -> getCourseNum($courseNumberId);
$CourseId = $arr['title'];
$arr = $courseModel -> getCourseNum($courseClassId);
$CourseClassId = $arr['title'];
$currentUser = new \admin\user\UserModel();
$userId = $currentUser->userId;
$userName = $currentUser->userName;
//
//$user10 = new \admin\user\UserModel(10);
//
//$user10->userId;
$t=time();
?>

<nav class="mainNav"></nav>
<article class="mainBox">
    <form action="courseController.php?action=addComment&c_cid=<?php echo $courseNumberId?>&classId=<?php echo $courseClassId?>" method="post">
    <header>
        <h2>添加一个课程</h2>
    </header>
    <section class="formBox">
        <div>
            <label>您好,<?php echo $userName?>,您正在添加<?php echo $CourseClassId.$CourseId ?>的评论 </label>
        </div>



        <div>
            <label>评论:<i>*</i> </label>
            <textarea class="input input-textarea" name="comment" placeholder="来吧,畅快的吐槽吧" value=""></textarea>
        </div>
        <input class="input" type="hidden" name="posttime" value="<?php echo $t?>">
    </section>
    <footer class="submitBox">
        <input type="submit" value="提交" class="btn">
    </footer>
        </form>
</article>