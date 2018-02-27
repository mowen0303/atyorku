<?php
$transactionModel = new \admin\transaction\TransactionModel();
$uid = BasicTool::get("uid") ?: 0;
$transactions = $transactionModel->getTransactions($uid);
if($uid){
    $targetUser = new \admin\user\UserModel($uid);
}
?>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>

<article class="mainBox">
    <header><h2><?php echo $uid?"用户 ".$targetUser->userName." 的交易记录":"交易记录" ?></h2></header>
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
