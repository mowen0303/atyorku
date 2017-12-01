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

?>

<nav class="mainNav"></nav>
<article class="mainBox">
    <form action="courseController.php?action=addRate&c_cid=<?php echo $courseNumberId?>&classId=<?php echo $courseClassId?>" method="post">
        <input type="hidden" name="course_class_id" value="">
        <header>
            <h2>添加评价</h2>
        </header>
        <section class="formBox">
            <div>
                <label>您好,<?php echo $userName?>,您正在添加<?php echo $CourseClassId.$CourseId ?>的评论 </label>
            </div>

            <div>
                <label>你认为这门课的难度是: <i>*</i> </label>
                <select class="input input-select input-size50" name="diff">
                    <option></option>
                    <option value="5">杀手课</option>
                    <option value="4">难</option>
                    <option value="3">一般</option>
                    <option value="2">简单</option>
                    <option value="1">水课</option>
                </select>
            </div>
            <div>
                <label>你的成绩: <i>*</i> </label>
                <select class="input input-select input-size50" name="grade" >
                    <option></option>
                    <option value="9">A+</option>
                    <option value="8">A</option>
                    <option value="7">B+</option>
                    <option value="6">B</option>
                    <option value="5">C+</option>
                    <option value="4">C</option>
                    <option value="3">D+</option>
                    <option value="2">D</option>
                    <option value="1">E</option>
                    <option value="0">F</option>
                </select>
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>