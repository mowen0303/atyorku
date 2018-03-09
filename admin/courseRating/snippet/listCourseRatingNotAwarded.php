<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
?>
<header class="topBox">
    <h1><?php echo htmlspecialchars($pageTitle)?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listCourseRating">返回</a>
</nav>

<article class="mainBox">
    <header><h2><?php echo htmlspecialchars($typeStr) ?>未奖励的课评列表</h2></header>
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
                    <th width="120px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $courseRatingModel->getListOfunawardedCourseRating();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr id="courseRating<? echo htmlspecialchars($row['id']) ?>">
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo htmlspecialchars($row['id']) ?>"></td>
                        <td><?php echo htmlspecialchars($row["id"]) ?></td>
                        <td><?php echo htmlspecialchars($row["course_code_parent_title"] . " " . $row["course_code_child_title"]) ?></td>
                        <td><?php echo htmlspecialchars($row["user_name"]) ?></td>
                        <td><?php echo htmlspecialchars($row["prof_name"]) ?></td>
                        <td><?php echo htmlspecialchars($row["content_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["homework_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["test_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["has_textbook"]) ?></td>
                        <td><?php echo htmlspecialchars($row["grade"]) ?></td>
                        <td><?php echo htmlspecialchars($row["term"] . " " . $row["year"]) ?></td>
                        <td><?php echo htmlspecialchars($row["recommendation"]) ?></td>
                        <td><?php echo htmlspecialchars($row["comment"]) ?></td>
                        <td>
                            <a class="btn obtainBtn" href="#" data-id="<?php echo htmlspecialchars($row['id'])?>" data-credit="0">奖励0积分</a>
                            <a class="btn obtainBtn" href="#" data-id="<?php echo htmlspecialchars($row['id'])?>" data-credit="3">奖励3积分</a>
                            <a class="btn obtainBtn" href="#" data-id="<?php echo htmlspecialchars($row['id'])?>" data-credit="5">奖励5积分</a>
                        </td>
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

<script>
    $(document).ready(function() {
        // 注册奖励按钮
        $(".obtainBtn").click(function (e) {
            var that = this;
            $(this).addClass('isDisabled');
            var courseRatingId = e.target.dataset.id;
            var credit = e.target.dataset.credit;

            if (courseRatingId && credit) {
                $.ajax({
                    url: "/admin/courseRating/courseRatingController.php?action=awardCourseRatingWithJson",
                    type: "POST",
                    contentType: "application/x-www-form-urlencoded",
                    data: {"id":courseRatingId,"credit":credit},
                    dataType: "json",
                }).done(function (json) {
                    console.log(json);
                    if (json.code === 1) {
                        alert("奖励成功");
                        var td = "#courseRating" + courseRatingId;
                        $(td).remove();
                    }else{
                        alert("奖励失败");
                        $(that).removeClass('isDisabled');
                    }
                }).fail(function (data) {
                    console.log(data);
                    alert("奖励失败");
                    $(that).removeClass('isDisabled');
                });
            } else {
                alert("缺失课评ID或奖励积分");
                $(that).removeClass('isDisabled');
            }
        });
    });
</script>
