<?php
$taskTransactionModel = new \admin\taskTransaction\TaskTransactionModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formTaskTransaction">添加新成就交易</a>
</nav>
<article class="mainBox">
    <header><h2>成就交易列表</h2></header>
    <form action="taskTransactionController.php?action=deleteTaskTransaction" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="40px">顺序</th>
                    <th width="60px">类别</th>
                    <th width="120px">用户</th>
                    <th width="60px">产品ID</th>
                    <th width="80px">操作类别</th>
                    <th width="80px">发布时间</th>
                    <th width="80px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $taskTransactionModel->getListOfTaskTransactions(false,40);
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["task_type"] ?></td>
                        <td><?php echo $row["user_name"] ?></td>
                        <td><?php echo $row['item_id'] ?></td>
                        <td><?php echo $row['op'] ?></td>
                        <td><?php echo $row['time'] ?></td>
                        <td><a class="btn" href="index.php?s=summaryTaskTransaction&user_id=<?php echo $row['user_id']?>">查看总结</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $taskTransactionModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
