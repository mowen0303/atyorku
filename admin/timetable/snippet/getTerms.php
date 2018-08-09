<?php
try{
    $userModel = new \admin\user\UserModel();
    $timetableModel = new \admin\timetable\TimetableModel();
    if (BasicTool::get("user_id")){
        $user_id = BasicTool::get("user_id");
        $terms = $timetableModel->getTermYears($user_id) or BasicTool::throwException("该用户没有注册任何课程");
    }else{
        $searchedUsername = BasicTool::get("username","用户名邮箱不能为空");
        $user_id = $userModel->getUserIdByName($searchedUsername) or BasicTool::throwException("用户不存在");
        $terms = $timetableModel->getTermYears($user_id) or BasicTool::throwException("该用户没有注册任何课程");
    }
}catch(Exception $e){
    BasicTool::echoMessage($e->getMessage());
    die();
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 学期</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="javascript:history.go(-1);">返回</a>
</nav>
<article class="mainBox">
    <form action="timetableController.php?action=deleteTimetable" method="post">
        <input type="text" name="user_id" value="<?php echo $user_id ?>" hidden/>
        <section>
            <table class="tab">
                <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>学年</th>
                        <th>节课</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($terms as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="term_year[]" value="<?php echo $row['term_year']?>"></td>
                        <td><?php echo $row['term_year'] ?></td>
                        <td><?php echo $row['count'] ?></td>
                        <td><a href="index.php?s=getTimetableCourses&term_year=<?php echo $row['term_year']?>&user_id=<?php echo $user_id?>">查看</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
