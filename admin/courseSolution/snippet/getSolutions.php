<?php
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$question_id = BasicTool::get("question_id");
$question = $questionModel->getQuestionById($question_id);
$questioner = $userModel->getProfileOfUserById($question["questioner_user_id"]);
$approved_solution = $solutionModel->getApprovedSolutionByQuestionId($question_id);
$solutions = $solutionModel->getSolutionsByQuestionId($question_id);

$approved_solution_display = "style='display:none'";
if ($approved_solution){
    $display_img_1 = "style='display:none'";
    $display_img_2 = "style='display:none'";
    $display_img_3 = "style='display:none'";
    $approved_solution_answerer = $userModel->getProfileOfUserById($approved_solution["answerer_user_id"]);
    if ($approved_solution["img_id_1"]) {
        $approved_solution_img_url_1 = $imageModel->getImageById($approved_solution["img_id_1"])["url"];
        $display_img_1 = "style='display:inline-block'";
    }
    if ($approved_solution["img_id_2"]){
        $approved_solution_img_url_2 = $imageModel->getImageById($approved_solution["img_id_2"])["url"];
        $display_img_2 = "style='display:inline-block'";
    }
    if ($approved_solution["img_id_3"]){
        $approved_solution_img_url_3 = $imageModel->getImageById($approved_solution["img_id_3"])["url"];
        $display_img_3 = "style='display:inline-block'";
    }
    $approved_solution_display = "style='display:table-row'";
    $display="style='none'";

}

?>
    <header class="topBox">
        <h1><?php echo $pageTitle?></h1>
    </header>
    <nav class="mainNav">
        <!-- fix the link-->
        <a class="btn" href="/admin/eventCategory/index.php?s=getEventCategories">返回</a>
        <!-- fix the link-->
        <a class="btn" href="index.php?s=addSolution&question_id=<?php echo $question_id ?>">添加新答案</a>
    </nav>
    <article class="mainBox">
        <header><h2>问题</h2></header>
            <section>
                <table class="tab">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>提问者</th>
                        <th>头像</th>
                        <th>详情</th>
                        <th>积分奖励</th>
                        <th>阅读量</th>
                        <th>答案量</th>
                        <th>提问时间</th>
                        <th>解决时间</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $question['id']?></td>
                            <td><?php echo $questioner['alias']?></td>
                            <td><img width="36" height="36" src="<?php echo $questioner["img"]?>"></td>
                            <td><?php echo $question['description']?></td>
                            <td><?php echo $question['reward_amount']?></a></td>
                            <td><?php echo $question['count_solutions'] ?></td>
                            <td><?php echo $question['count_views'] ?></td>
                            <td><?php echo BasicTool::translateTime($question['time_posted'])?></td>
                            <td><?php echo ($question["time_solved"] == 0) ? "未解决": BasicTool::translateTime($question["time_solved"]) ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
    </article>
    <article class="mainBox">
    <header>
        <h2>图片</h2>
    </header>
    <?php
        if ($question["img_id_1"])
        {
            $img_url_1 = $imageModel->getImageById($question["img_id_1"])["url"];
            echo "<img width='390' height='260' style='margin-right:2rem' src='{$img_url_1}'/>";
        }
        if ($question["img_id_2"])
        {
            $img_url_2 = $imageModel->getImageById($question["img_id_2"])["url"];
            echo "<img width='390' height='260' style='margin-right:2rem' src='{$img_url_2}'/>";
        }
        if ($question["img_id_3"]){
            $img_url_3 = $imageModel->getImageById($question["img_id_3"])["url"];
            echo "<img width='390' height='260' src='{$img_url_3}'/>";
        }
    ?>
</article>
<article class="mainBox">
    <header><h2>答案</h2></header>
    <form action="/admin/courseSolution/courseSolutionController.php?action=deleteSolutionById" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>用户</th>
                    <th>头像</th>
                    <th>详情</th>
                    <th>图片1</th>
                    <th>图片2</th>
                    <th>图片3</th>
                    <th>阅读量</th>
                    <th>提问时间</th>
                    <th>采纳时间</th>
                    <th <?php echo $display?>></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr <?php echo $approved_solution_display ?>>
                        <td></td>
                        <td><?php echo $approved_solution['id']?></td>
                        <td><?php echo $approved_solution_answerer['alias']?></td>
                        <td><img width="36" height="36" src="<?php echo $approved_solution_answerer["img"]?>"></td>
                        <td><?php echo $approved_solution['description']?></td>
                        <td><img  width="100" height="70" src="<?php echo $approved_solution_img_url_1?>" <?php echo $display_img_1 ?>"></td>
                        <td><img  width="100" height="70" src="<?php echo $approved_solution_img_url_2?>" <?php echo $display_img_2 ?>"></td>
                        <td><img  width="100" height="70" src="<?php echo $approved_solution_img_url_3?>" <?php echo $display_img_3 ?>"></td>
                        <td><?php echo $approved_solution['count_views'] ?></td>
                        <td><?php echo BasicTool::translateTime($approved_solution['time_posted'])?></td>
                        <td><?php echo BasicTool::translateTime($approved_solution["time_approved"])?></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php
                foreach ($solutions as $solution) {
                    $answerer=$userModel->getProfileOfUserById($solution["answerer_user_id"]);
                    $display_img_1 = "style='display:none'";
                    $display_img_2 = "style='display:none'";
                    $display_img_3 = "style='display:none'";
                    if ($solution["img_id_1"]){
                        $img_url_1 = $imageModel->getImageById($solution["img_id_1"])["url"];
                        $display_img_1 = "style='display:inline-block'";
                    }
                    if ($solution["img_id_2"]){
                        $img_url_2 = $imageModel->getImageById($solution["img_id_2"])["url"];
                        $display_img_2 = "style='display:inline-block'";
                    }
                    if ($solution["img_id_3"]){
                        $img_url_3 = $imageModel->getImageById($solution["img_id_3"])["url"];
                        $display_img_3 = "style='display:inline-block'";
                    }
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $solution['id']?>"></td>
                        <td><?php echo $solution['id']?></td>
                        <td><?php echo $answerer['alias']?></td>
                        <td><img width="36" height="36" src="<?php echo $answerer["img"]?>"></td>
                        <td><?php echo $solution['description']?></td>
                        <td><img <?php echo $display_img_1?> width="100" height="70" src="<?php echo $img_url_1?>"></td>
                        <td><img <?php echo $display_img_2?> width="100" height="70" src="<?php echo $img_url_2?>"></td>
                        <td><img <?php echo $display_img_3?> width="100" height="70" src="<?php echo $img_url_3?>"></td>
                        <td><?php echo $solution['count_views'] ?></td>
                        <td><?php echo BasicTool::translateTime($solution['time_posted'])?></td>
                        <td></td>
                        <td><a href="/admin/courseQuestion/courseQuestionController.php?action=approveSolution&question_id=<?php echo $question_id?>&solution_id=<?php echo $solution['id']?>">采纳</a></td>
                        <td><a href="/admin/courseSolution/index.php?s=addSolution&question_id=<?php echo $question_id?>&solution_id=<?php echo $solution['id']?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $solutionModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
