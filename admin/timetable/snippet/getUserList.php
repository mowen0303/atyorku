<?php
$userModel = new \admin\user\UserModel();
$timeTableModel = new \admin\timetable\TimetableModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 用户列表</h1>
</header>
<nav class="mainNav">>
</nav>
<article class="mainBox">
    <form class="" action="index.php" method="get">
        <section class="formRow">
            <input type="hidden" name="s" value="getTerms">
            <input class="input" placeholder="用户名邮箱" type="text" name="username" value="">
            <input class="btn btn-center" type="submit" title="查询课评记录" value="查询用户">
        </section>
    </form>
</article>

<article class="mainBox">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>头像</th>
                    <th>用户名</th>
                    <th>昵称</th>
                    <th>点券</th>
                    <th>活跃度</th>
                    <th>性别</th>
                    <th>状态</th>
                    <th>注册时间</th>
                    <th>设备</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $timeTableModel->getUserList();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']?></td>
                        <td><img width="36" height="36" src="<?php echo $row['img']?>"></td>
                        <td><a href="index.php?s=getTerms&user_id=<?php echo  $row['id']?>"><?php echo $row['name'] ?></a></td>
                        <td><?php echo $row['alias']?></td>
                        <td><?php echo $row['credit']?></td>
                        <td><?php echo $row['activist']?></td>
                        <td><?php echo $userModel->translateGender($row['gender'])?></td>
                        <td><?php echo $row['blocktime']-time()>0 ? "禁言中..." : "正常" ?></td>
                        <td><?php echo BasicTool::translateTime($row['registertime']); ?></td>
                        <td><?php echo $row['device'] == false ? "0" : "绑" ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php $timeTableModel->echoPageList(); ?>
        </section>
</article>
