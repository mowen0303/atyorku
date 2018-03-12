<?php
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    echo '<a class="btn" href="javascript:history.go(-1);">返回</a>';
</nav>
<article class="mainBox">
    <header><h2>父类科目列表</h2></header>
    <form action="" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="60px">ID</th>
                    <th width="100px">科目简称</th>
                    <th>科目全称</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $courseCodeModel->getListOfCourseCodeByParentId(0);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php
                            $title = $row["title"];
                            echo ("<a href=\"index.php?s=listCourseReport&course_parent_title={$title}\">{$title}</a>");
                            ?>
                        </td>
                        <td><?php echo $row["full_title"]; ?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </section>
    </form>
</article>
