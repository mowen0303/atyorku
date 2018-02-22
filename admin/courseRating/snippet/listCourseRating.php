<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
$userModel = new \admin\user\UserModel();
$isGod = $userModel->isUserHasAuthority("GOD");
$queryUserName = BasicTool::get("user_name");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <?php
        if($queryUserName){
            echo '<a class="btn" href="javascript:history.go(-1);">返回</a>';
        } else {
            echo '<a class="btn" href="index.php?s=listCourseRatingNotAwarded">未奖励的课评</a>
    <a class="btn" href="index.php?s=listCourseProfReport">科目教授报告表</a>
    <a class="btn" href="index.php?s=listCourseReport">科目报告表</a>
    <a class="btn" href="index.php?s=listProfReport">教授报告表</a>
    <a class="btn" href="index.php?s=formCourseRating&flag=add">添加新课评</a>';
        }
    ?>
</nav>
<?php
if(!$queryUserName){
?>
<article class="mainBox">
    <form action="index.php?s=listCourseRating" method="get">
        <header>
            <h2>查询用户课评记录</h2>
        </header>
        <section class="formRow">
            <input class="input" placeholder="用户名邮箱" type="text" name="user_name" value="">
            <input class="btn btn-center" type="submit" title="查询课评记录" value="查询课评记录">
        </section>
    </form>
</article>
<?php
}
?>
<article class="mainBox">
    <?php
        if($isGod && !$queryUserId){
            echo "<form action=\"courseRatingController.php?action=updateAllReports\" method=\"post\"><footer class=\"buttonBox\"><input type=\"submit\" value=\"更新全部报告\" class=\"btn\" onclick=\"return confirm('确认更新全部报告?')\"></footer></form>";
        }
    ?>


    <header><h2><?php echo $typeStr ?>课评列表<?php if($queryUserName){echo " - {$queryUserName}";} ?></h2></header>
    <form action="courseRatingController.php?action=deleteCourseRating" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="60px">ID</th>
                    <th width="60px">科目</th>
                    <th width="60px">用户</th>
                    <th width="60px">教授</th>
                    <th width="60px">内容</th>
                    <th width="60px">作业</th>
                    <th width="60px">考试</th>
                    <th width="60px">书</th>
                    <th width="60px">成绩</th>
                    <th width="100px">学期</th>
                    <th width="60px">推荐</th>
                    <th width="150px">评论</th>
                    <th width="80px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = null;
                if($queryUserName){
                    $arr = $courseRatingModel->getListOfCourseRatingByUserId($userModel->getUserIdByName($queryUserName), 40);
                } else {
                    $arr = $courseRatingModel->getListOfCourseRating(false,40,"id");
                }
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["course_code_parent_title"] . " " . $row["course_code_child_title"] ?></td>
                        <td><?php
                                if($queryUserId && $queryUserName) {
                                    echo $row["user_name"];
                                } else {
                                    $userId = $row["user_id"];
                                    $username = $row["user_name"];
                                    echo "<a href='index.php?s=listCourseRating&user_id={$userId}&user_name={$username}'>{$username}</a>";
                                }
                            ?>
                        </td>
                        <td><?php echo $row["prof_name"] ?></td>
                        <td><?php echo $row["content_diff"] ?></td>
                        <td><?php echo $row["homework_diff"] ?></td>
                        <td><?php echo $row["test_diff"] ?></td>
                        <td><?php echo $row["has_textbook"] ?></td>
                        <td><?php echo $row["grade"] ?></td>
                        <td><?php echo $row["term"] . " " . $row["year"] ?></td>
                        <td><?php echo $row["recommendation"] ?></td>
                        <td><?php echo $row["comment"] ?></td>
                        <td><a class="btn" href="index.php?s=formCourseRating&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $courseRatingModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
