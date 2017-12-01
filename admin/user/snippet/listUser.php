<?php
$userModel = new \admin\user\UserModel();
$isAdmin = BasicTool::get('isAdmin');
$classId = BasicTool::get('classId') ? BasicTool::get('classId') : false;
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 用户列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="userController.php?action=topStudentToHTML">刷新智囊团列表</a>
    <a class="btn" href="index.php?s=listUser&isAdmin=0&classId=6">未激活用户</a>
    <a class="btn" href="index.php?s=listUser&isAdmin=0&classId=7">普通用户</a>
    <a class="btn" href="index.php?s=listUser&isAdmin=0&classId=20">学霸智囊团</a>
    <a class="btn" href="index.php?s=formUser">添加一名新用户</a>
</nav>
<article class="mainBox">
    <form action="userController.php?action=deleteUser" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>头像</th>
                    <th>用户名</th>
                    <th>昵称</th>
                    <th>点券</th>
                    <th>活跃度</th>
                    <th>性别</th>
                    <th>身份</th>
                    <th>状态</th>
                    <th>注册时间</th>
                    <th>设备</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $userModel->getListOfUser($isAdmin,$classId,20,1);
                foreach ($arr as $row) {
                 ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"><input type="hidden" name="isAdmin[]" value="<?php echo $row['is_admin']?>"></td>
                            <td><?php echo $row['id']?></td>
                            <td><img width="36" height="36" src="<?php echo $row['img']?>"></td>
                            <td><a href="index.php?s=formUser&uid=<?php echo  $row['id']?>"><?php echo $row['name'] ?></a></td>
                            <td><?php echo $row['alias']?></td>
                            <td><?php echo $row['credit']?></td>
                            <td><?php echo $row['activist']?></td>
                            <td><?php echo $userModel->translateGender($row['gender'])?></td>
                            <td><?php echo $row['title'] ?></td>
                            <td><?php echo $row['blocktime']-time()>0 ? "禁言中..." : "正常" ?></td>
                            <td><?php echo BasicTool::translateTime($row['registertime']); ?></td>
                            <td><?php echo $row['device'] == false ? "0" : "绑" ?></td>
                        </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php $userModel->echoPageList(); ?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
