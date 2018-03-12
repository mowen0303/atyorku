<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
$userModel = new \admin\user\UserModel();
$isGod = $userModel->isUserHasAuthority("GOD");
$queryUserName = htmlspecialchars(BasicTool::get("user_name"));
$queryCourseCode = BasicTool::get("course_code_id");
?>
<header class="topBox">
    <h1><?php echo htmlspecialchars($pageTitle)?></h1>
</header>
<nav class="mainNav">
    <?php
        if($queryUserName || $queryCourseCode){
            echo '<a class="btn" href="javascript:history.go(-1);">返回</a>';
        } else {
            echo '<a class="btn" href="index.php?s=listCourseCode">科目搜索</a>
    <a class="btn" href="index.php?s=listCourseRatingNotAwarded">未奖励的课评</a>
    <a class="btn" href="index.php?s=listCourseProfReport">科目教授报告表</a>
    <a class="btn" href="index.php?s=listCourseReport">科目报告表</a>
    <a class="btn" href="index.php?s=listProfReport">教授报告表</a>
    <a class="btn" href="index.php?s=formCourseRating&flag=add">添加新课评</a>';
        }
    ?>
</nav>
<?php
if(!$queryUserName && !$queryCourseCode){
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


    <header><h2><?php echo htmlspecialchars($typeStr) ?>课评列表<?php if($queryUserName){echo " - {$queryUserName}";} ?></h2></header>
    <form action="courseRatingController.php?action=deleteCourseRating" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="5%"><input id="cBoxAll" type="checkbox"></th>
                    <th width="5%">ID</th>
                    <th width="8%">科目</th>
                    <th width="8%">用户</th>
                    <th width="8%">教授</th>
                    <th width="3%">内容</th>
                    <th width="3%">作业</th>
                    <th width="3%">考试</th>
                    <th width="3%">成绩</th>
                    <th width="6%">学期</th>
                    <th width="20%">评论</th>
                    <th width="18%">课程总结</th>
                    <th width="10%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = null;
                if($queryUserName){
                    $arr = $courseRatingModel->getListOfCourseRatingByUserId($userModel->getUserIdByName($queryUserName), 40);
                } else if($queryCourseCode) {
                    $arr = $courseRatingModel->getListOfCourseRatingByCourseId($queryCourseCode, 40);
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
                        <td><?php echo htmlspecialchars($row["id"]) ?></td>
                        <td><?php echo htmlspecialchars($row["course_code_parent_title"] . " " . $row["course_code_child_title"]) ?></td>
                        <td><?php
                                if($queryUserId && $queryUserName) {
                                    echo htmlspecialchars($row["user_name"]);
                                } else {
                                    $userId = htmlspecialchars($row["user_id"]);
                                    $username = htmlspecialchars($row["user_name"]);
                                    echo "<a href='index.php?s=listCourseRating&user_id={$userId}&user_name={$username}'>{$username}</a>";
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row["prof_name"]) ?></td>
                        <td><?php echo htmlspecialchars($row["content_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["homework_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["test_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["grade"]) ?></td>
                        <td><?php echo htmlspecialchars($row["term"] . " " . $row["year"]) ?></td>
                        <td><?php echo htmlspecialchars($row["comment"]) ?></td>
                        <td><?php echo htmlspecialchars($row["content_summary"]) ?></td>
                        <td><a class="btn" href="index.php?s=formCourseRating&flag=update<?php echo htmlspecialchars($argument)?>">修改</a></td>
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
