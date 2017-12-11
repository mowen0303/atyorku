<?php
$transactionModel = new \admin\transaction\TransactionModel();
$transactions = $transactionModel->getTransactions();
?>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>

<article class="mainBox">
    <header><h2>广告分类</h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>ID</th>
                <th>用户ID</th>
                <th>积分</th>
                <th>详情</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($transactions as $transaction) {
                ?>
                <tr>
                    <td><?php echo $transaction["id"]?></td>
                    <td><?php echo $transaction["user_id"]?></td>
                    <td><?php echo $transaction["amount"]?></td>
                    <td><?php echo $transaction["description"]?></td>
                    <td><?php echo BasicTool::translateTime($transaction["time"]) ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php echo $transactionModel->echoPageList()?>
    </section>

</article>
