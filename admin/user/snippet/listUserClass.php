<?php
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 分类管理</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formUserClass">添加</a>
</nav>
<article class="mainBox">
    <form action="userController.php?action=deleteClassOfUser" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>类别</th>
                    <th>所在组</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $userModel->getListOfUserClass();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="$idOfClassArr[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['id']?></td>
                        <td><a href="index.php?s=formUserClass&id=<?php echo $row['id']?>"><?php echo $row['title']?></a> </td>
                        <td>
                            <?php echo $row['is_admin']==true?'管理员组':'用户组'?>
                        </td>
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
