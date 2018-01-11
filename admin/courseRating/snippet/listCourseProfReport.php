<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listCourseRating">返回</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo $typeStr ?>科目教授报告列表</h2></header>
    <form action="courseRatingController.php?action=deleteCourseProfessorReport" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="60px">ID</th>
                    <th width="100px">科目</th>
                    <th width="100px">教授</th>
                    <th width="80px">内容难度</th>
                    <th width="80px">作业难度</th>
                    <th width="80px">考试难度</th>
                    <th width="80px">综合难度</th>
                    <th width="80px">平均成绩</th>
                    <th width="80px">推荐比例</th>
                    <th width="80px">评论数</th>
                    <th width="80px">问题数</th>
                    <th width="120px">已解决问题数</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $courseRatingModel->getListOfCourseProfessorReports();
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
                        <td><?php echo $row["prof_name"] ?></td>
                        <td><?php echo $row["content_diff"] ?></td>
                        <td><?php echo $row["homework_diff"] ?></td>
                        <td><?php echo $row["test_diff"] ?></td>
                        <td><?php echo $row["overall_diff"] ?></td>
                        <td><?php echo $row["avg_grade"] ?></td>
                        <td><?php echo number_format(100*$row["recommendation_ratio"],2) . '%' ?></td>
                        <td><?php echo $row["rating_count"] ?></td>
                        <td><?php echo $row["count_questions"] ?></td>
                        <td><?php echo $row["count_solved_questions"] ?></td>
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
