<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
$userModel = new \admin\user\UserModel();
$flag = htmlspecialchars(BasicTool::get("flag"));
$userId = htmlspecialchars($flag=="add" ? $userModel->userId : BasicTool::get("user_id"));
?>

<style>
.row {
    overflow: hidden;
}
.col-2 {
    float:left;
    width: 47%;
}
.col-2:nth-child(1) {
    margin-right: 5%;
}
.col-3 {
    float: left;
    width: 30%;
}
.col-3:nth-last-child(n+2) {
    margin-right: 3%;
}
</style>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listCourseRating">返回</a>
</nav>
<article class="mainBox">
    <form action="courseRatingController.php?action=modifyCourseRating" method="post">
        <input name="flag" id="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo htmlspecialchars(BasicTool::get('id'))?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改课评":"添加课评"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>课评用户ID<i>*</i></label>
                <input class="input" name="user_id" value="<?php echo $userId?>" type="text">
            </div>
            <div id="courseCodeInputComponent" class="row">
                <div class="col-2">
                    <label>课程类别 (例如:ADMS)<i>*</i></label>
                    <input id="parentInput" class="input" type="text" list="parentCodeList" name="course_code_parent_title" value="<?php echo htmlspecialchars(BasicTool::get('course_code_parent_title'))?>">
                    <datalist id="parentCodeList"></datalist>
                </div>
                <div class="col-2">
                    <label>课程代码 (例如:1000)<i>*</i></label>
                    <input id="childInput" class="input" type="text" list="childCodeCodeList" name="course_code_child_title" value="<?php echo htmlspecialchars(BasicTool::get('course_code_child_title'))?>">
                    <datalist id="childCodeCodeList"></datalist>
                </div>
            </div>
            <div id="professorInputComponent">
                <div>
                    <label>教授<i>*</i></label>
                    <input class="input" type="text" list="professorList" name="prof_name" value="<?php echo BasicTool::get('prof_name') ?>" />
                    <datalist id="professorList"></datalist>
                </div>
            </div>
            <div class="row">
                <?php
                    $diffHtml = "<option value=''>请选择难度等级</option>";
                    for($i=1;$i<11;$i++) $diffHtml .= "<option value='{$i}'>{$i}</option>";
                ?>
                <div class="col-3">
                    <label>内容难度<i>*</i></label>
                    <select class="input input-select selectDefault" name="content_diff" defvalue="<?php echo htmlspecialchars(BasicTool::get('content_diff')) ?>">
                        <?php echo $diffHtml; ?>
                    </select>
                </div>
                <div class="col-3">
                    <label>作业难度<i>*</i></label>
                    <select class="input input-select selectDefault" name="homework_diff" defvalue="<?php echo htmlspecialchars(BasicTool::get('homework_diff')) ?>">
                        <?php echo $diffHtml; ?>
                    </select>
                </div>
                <div class="col-3">
                    <label>考试难度<i>*</i></label>
                    <select class="input input-select selectDefault" name="test_diff" defvalue="<?php echo htmlspecialchars(BasicTool::get('test_diff')) ?>">
                        <?php echo $diffHtml; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <label>是否需要教科书<i>*</i></label>
                    <select class="input input-select selectDefault" name="has_textbook" defvalue="<?php echo htmlspecialchars(BasicTool::get('has_textbook')) ?>">
                        <option value="">选择是否需要教科书</option>
                        <option value="0">不需要</option>
                        <option value="1">需要</option>
                    </select>
                </div>
                <div class="col-3">
                    <label>是否推荐此教授<i>*</i></label>
                    <select class="input input-select selectDefault" name="recommendation" defvalue="<?php echo htmlspecialchars(BasicTool::get('recommendation')) ?>">
                        <option value="">选择是否推荐此教授</option>
                        <option value="0">不推荐</option>
                        <option value="1">推荐</option>
                    </select>
                </div>
                <div class="col-3">
                    <label>本门成绩</label>
                    <select class="input input-select selectDefault" name="grade" defvalue="<?php echo htmlspecialchars(BasicTool::get('grade')) ?>">
                        <option value="">选择成绩</option>
                        <option value="A+">A+</option>
                        <option value="A">A</option>
                        <option value="B+">B+</option>
                        <option value="B">B</option>
                        <option value="C+">C+</option>
                        <option value="C">C</option>
                        <option value="D+">D+</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <label>本科所修学年<i>*</i></label>
                    <select class="input input-select selectDefault" name="year" defvalue="<?php echo htmlspecialchars(BasicTool::get('year')) ?>">
                        <?php
                            echo "<option value=''>请选择学年</option>";
                            for($i=date("Y");$i>1959;$i--) echo "<option value='{$i}'>{$i}</option>";
                        ?>
                    </select>
                </div>
                <div class="col-2">
                    <label>本门所修学期<i>*</i></label>
                    <select class="input input-select selectDefault" name="term" defvalue="<?php echo htmlspecialchars(BasicTool::get('term')) ?>">
                        <option value="">选择学期</option>
                        <option value="Fall">Fall</option>
                        <option value="Winter">Winter</option>
                        <option value="Year">Year</option>
                        <option value="Summer">Summer</option>
                        <option value="Summer 1">Summer 1</option>
                        <option value="Summer 2">Summer 2</option>
                    </select>
                </div>
            </div>
            <div>
                <label>评论<i>*</i></label>
                <textarea class="input input-textarea" name="comment" placeholder="说一说对此门课的看法吧。。。"><?php echo htmlspecialchars(BasicTool::get('comment')) ?></textarea>
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>
