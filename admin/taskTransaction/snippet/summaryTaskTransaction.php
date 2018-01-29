<?php
$taskTransactionModel = new \admin\taskTransaction\TaskTransactionModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1>成就总结</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listTaskTransaction">返回</a>
</nav>
<article class="mainBox">
    <header><h2>成就交易总结</h2></header>
    <table class="tab">
        <thead>
        <th width="80px">类别</th>
        <th width="80px">添加数</th>
        <th width="80px">删除数</th>
        <th width="80px">合计</th>
        </thead>
        <tbody>
        <?php
        $arr = $taskTransactionModel->getSummaryOfTaskTransactionsByUserId(BasicTool::get('user_id'));
        foreach($arr as $k=>$v){
            ?>
            <tr>
                <td><?php echo $k ?></td>
                <td><?php echo $v['add'] ?></td>
                <td><?php echo $v['delete'] ?></td>
                <td><?php echo $v['total'] ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</article>
