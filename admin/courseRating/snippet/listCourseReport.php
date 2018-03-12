<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
$courseParentTitle = BasicTool::get("course_parent_title") ?: false;
?>
<header class="topBox">
    <h1><?php echo htmlspecialchars($pageTitle)?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listCourseRating">返回</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo htmlspecialchars($typeStr) ?>科目报告列表</h2></header>
    <form action="courseRatingController.php?action=deleteCourseReport" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th width="5%"><input id="cBoxAll" type="checkbox"></th>
                        <th width="5%">ID</th>
                        <th width="15%">科目</th>
                        <th width="9%">内容难度</th>
                        <th width="9%">作业难度</th>
                        <th width="9%">考试难度</th>
                        <th width="10%">综合难度</th>
                        <th width="10%">平均成绩</th>
                        <th width="8%">问题数</th>
                        <th width="10%">已解决问题数</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $arr = $courseRatingModel->getListOfCourseReports(40, $courseParentTitle);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo htmlspecialchars($row['id']) ?>"></td>
                        <td><?php echo htmlspecialchars($row["id"]) ?></td>
                        <td><?php
                            $courseCodeId = $row["course_code_id"];
                            echo ("<a href=\"index.php?s=listCourseRating&course_code_id={$courseCodeId}\">" . htmlspecialchars($row["course_code_parent_title"] . " " . $row["course_code_child_title"]) . " (" . htmlspecialchars($row["rating_count"]) . ")" . "</a>");
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row["content_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["homework_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["test_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["overall_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["avg_grade"]) ?></td>
                        <td><?php echo htmlspecialchars($row["count_questions"]) ?></td>
                        <td><?php echo htmlspecialchars($row["count_solved_questions"]) ?></td>
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
