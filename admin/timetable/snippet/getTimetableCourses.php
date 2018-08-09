<?php
try{
    $userModel = new \admin\user\UserModel();
    $timetableModel = new \admin\timetable\TimetableModel();
    $userModel->isUserHasAuthority('GOD') or BasicTool::throwException("权限不足");
    $user_id = BasicTool::get("user_id","用户ID不能为空");
    $term_year = BasicTool::get("term_year","请指定学年");
    $arr = $timetableModel->getTimetableCourses($user_id,$term_year,false) or BasicTool::throwException("该用户并未注册任何课程");
}catch(Exception $e){
    BasicTool::echoMessage($e->getMessage());
    die();
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle." - ".$term_year?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="javascript:history.go(-1);">返回</a>
</nav>
<article class="mainBox">
    <section>
        <table class="tab">
            <thead>
                <tr>
                    <th>课码</th>
                    <th>学期</th>
                    <th>Section</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($arr as $row) {
                ?>
                <tr>
                    <td><?php echo $row['course_parent_title']." ".$row['course_child_title']?></td>
                    <td><?php echo $row['term_semester']?></td>
                    <td><?php echo $row['section']?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </section>
</article>
