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
if ($approved_solution){
    $display = "style='display:none'";
    $approved_solution_answerer = $userModel->getProfileOfUserById($approved_solution["answerer_user_id"]);
    $approved_solution_img = $imageModel->getImageById($approved_solution["img_id_1"])["url"];
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
                            <td><?php echo $question['time_posted']?></td>
                            <td><?php echo $question['time_solved']?></td>
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
    if($question["img_id_1"]){
        $img_link_1 = $imageModel->getImageById($question["img_id_1"])["url"];
        echo "<img src='{$img_link_1}' height='260' width='390'/>"
        ?>
    <?php }?>
    <?php
    if($question["img_id_2"]){
        $img_link_2 = $imageModel->getImageById($question["img_id_2"])["url"];
        echo "<img src='{$img_link_2}' height='260' width='390'/>"
        ?>
    <?php }?>
    <?php
    if($question["img_id_3"]){
        $img_link_3 = $imageModel->getImageById($question["img_id_3"])["url"];
        echo "<img src='{$img_link_3}' height='260' width='390'/>"
        ?>
    <?php }?>
</article>
    <article class="mainBox">
    <header><h2>答案</h2></header>
    <form action="courseSolutionController.php?action=deleteSolution" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>用户</th>
                    <th>头像</th>
                    <th>详情</th>
                    <th>图片</th>
                    <th>阅读量</th>
                    <th>提问时间</th>
                    <th>解决时间</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td><?php echo $approved_solution['id']?></td>
                        <td><?php echo $approved_solution_answerer['alias']?></td>
                        <td><img width="36" height="36" src="<?php echo $approved_solution_answerer["img"]?>"></td>
                        <td><?php echo $approved_solution['description']?></td>
                        <td><img width="200" height="100" src="<?php echo $approved_solution_img?>"></td>
                        <td><?php echo $approved_solution['count_views'] ?></td>
                        <td><?php echo $approved_solution['time_posted']?></td>
                        <td><?php echo $approved_solution["time_approved"]?></td>
                        <td></td>
                    </tr>
                <?php
                foreach ($solutions as $solution) {
                    $img = $imageModel->getImageById($solution["img_id_1"])["url"];
                    $answerer = $userModel->getProfileOfUserById($solution["answerer_user_id"]);
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $solution['id']?>"></td>
                        <td><?php echo $solution['id']?></td>
                        <td><?php echo $answerer['alias']?></td>
                        <td><img width="36" height="36" src="<?php echo $answerer["img"]?>"></td>
                        <td><?php echo $solution['description']?></td>
                        <td><img width="200" height="100" src="<?php echo $img?>"></td>
                        <td><?php echo $solution['count_views'] ?></td>
                        <td><?php echo $solution['time_posted']?></td>
                        <td><?php echo $solution["time_approved"]?></td>
                        <td <?php echo $display?>><a href="/admin/courseSolution/index.php?s=getSolutions&question_id=<?php echo $solution['id'] ?>">采纳</a></td>
                        <td><a href="/admin/courseSolution/index.php?s=addSolution&question_id=<?php echo $question_id?>&solution_id=<?echo $solution["id"]?>">修改</a></td>
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
