<?php
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$profModel = new \admin\professor\ProfessorModel();
/*
$course_code_id = BasicTool::get('course_code_id');
$prof_id = BasicTool::get("prof_id");
$flag = BasicTool::get("flag");
*/
$course_code_id = 543;
$prof_id=1;
$flag = 0;
$course_code = $courseCodeModel->getCourseCodeById($course_code_id);
if($flag == 0){
   $display_option_0 = "style='display:none'";
   $display_option_1 = "";
}
else{
    $display_option_0="";
    $display_option_1 = "style='display:none'";
}
if ($prof_id){
    $questions = $questionModel->getQuestionsByCourseCodeIdProfId($course_code_id,$prof_id,$flag);
    $prof = $profModel->getProfessorById($prof_id);
    $p = "&prof_id={$prof_id}";
}
else{
    $questions = $questionModel->getQuestionsByCourseCodeId($course_code_id,$flag);
    $p="";
    $add_button_display = "style='display:none'";
}

?>
    <header class="topBox">
        <h1><?php echo $pageTitle?></h1>
    </header>
    <nav class="mainNav">
        <!-- fix the link-->
        <a class="btn" href="/admin/eventCategory/index.php?s=getEventCategories">返回</a>
        <!-- fix the link-->
        <a class="btn" <?php echo $display_option_0 ?> href="index.php?s=getQuestions&course_code_id=<?php echo $course_code_id ?>&flag=0<?php echo $p?>">未解决的问题</a>
        <a class="btn" <?php echo $display_option_1 ?> href="index.php?s=getQuestions&course_code_id=<?php echo $course_code_id ?>&flag=1<?php echo $p?>">已解决的问题</a>
        <a <?php echo $add_button_display ?>  class="btn" href="index.php?s=addQuestion&course_code_id=<?php echo $course_code_id.$p ?>">发布新提问</a>
    </nav>
    <article class="mainBox">
        <header><h2><?php echo $course_code["full_title"]." - ".$prof["firstname"]." ".$prof["lastname"]?></h2></header>
        <form action="courseQuestionController.php?action=deleteQuestion" method="post">
            <section>
                <table class="tab">
                    <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>ID</th>
                        <th>提问者</th>
                        <th>头像</th>
                        <th>详情</th>
                        <th>图片</th>
                        <th>积分奖励</th>
                        <th>答案量</th>
                        <th>阅读量</th>
                        <th>提问时间</th>
                        <th <?php echo $display_option_0 ?>>解决时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($questions as $question) {
                        $img = $imageModel->getImageById($question["img_id_1"])["url"];
                        $questioner = $userModel->getProfileOfUserById($question["questioner_user_id"]);
                        ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $question['id']?>"></td>
                            <td><?php echo $question['id']?></td>
                            <td><?php echo $questioner['alias']?></td>
                            <td><img width="36" height="36" src="<?php echo $questioner["img"]?>"></td>
                            <td><?php echo $question['description']?></td>
                            <td><a href="/admin/courseSolution/index.php?s=getSolutions&question_id=<?php echo $question["id"]?>"><img width="200" height="100" src="<?php echo $img?>"></a></td>
                            <td><?php echo $question['reward_amount']?></a></td>
                            <td><?php echo $question['count_solutions'] ?></td>
                            <td><?php echo $question['count_views'] ?></td>
                            <td><?php echo BasicTool::translateTime($question['time_posted'])?></td>
                            <td <?php echo $display_option_0 ?>><?php echo BasicTool::translateTime($question['time_solved'])?></td>
                            <td><a href="/admin/courseQuestion/index.php?s=addQuestion&question_id=<?php echo $question['id'] ?>&course_code_id=<?php echo $course_code_id.$p ?>">更改</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php echo $questionModel->echoPageList()?>
            </section>
            <footer class="buttonBox">
                <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
            </footer>
        </form>
    </article>