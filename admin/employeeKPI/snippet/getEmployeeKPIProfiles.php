<?php
$userModel = new \admin\user\UserModel();
$employeeKPIModel = new \admin\employeeKPI\EmployeeKPIModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 管理员列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=addEmployeeKPIProfile">添加</a>
</nav>
<article class="mainBox">
    <form class="" action="index.php" method="get">
        <section class="formRow">
            <input type="hidden" name="s" value="getEmployeeKPIDetail">
            <input class="input" placeholder="用户名邮箱" type="text" name="username" value="">
            <input class="btn btn-center" type="submit" value="查询用户">
        </section>
    </form>
</article>

<article class="mainBox">
    <form action="employeeKPIController.php?action=deleteEmployeeKPIProfileById" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>头像</th>
                    <th>用户名</th>
                    <th>昵称</th>
                    <th>账号数</th>
                    <th>性别</th>
                    <th>创建时间</th>
                    <th>上次更改时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $employeeKPIModel->getEmployeeKPIProfiles();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['id']?></td>
                        <td><img width="36" height="36" src="<?php echo $row['img']?>"></td>
                        <td><a href="index.php?s=getEmployeeKPIDetail&id=<?php echo $row['id']?>"><?php echo $row['name'] ?></a></td>
                        <td><?php echo $row['alias']?></td>
                        <td><?php echo $row['count_accounts']?></td>
                        <td><?php echo $userModel->translateGender($row['gender'])?></td>
                        <td><?php echo BasicTool::translateTime($row['created_time'])?></td>
                        <td><?php echo BasicTool::translateTime($row['last_updated_time'])?></td>
                        <td><a href="index.php?s=addEmployeeKPIProfile&id=<?php echo $row['id']?>">更改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php $employeeKPIModel->echoPageList(); ?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>

