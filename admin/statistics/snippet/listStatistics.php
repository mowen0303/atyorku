<?php
$statisticsModel = new \admin\statistics\StatisticsModel();
$currentUser = new \admin\user\UserModel();
$currentUser->isUserHasAuthority('GOD') or BasicTool::echoMessage("NONE");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listForumClass">返回论坛</a>
</nav>
<article class="mainBox">
    <header><h2>浏览量</h2></header>
    <section>
        <p>有效用户:<? echo $currentUser->getCountOfUserForValid() ?></p>
        <p>设备注册量:<? echo $currentUser->getCountOfDevice() ?></p>
    </section>
</article>
<article class="mainBox">
    <header><h2>浏览量</h2></header>
    <form action="forumController.php?action=deleteForum" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th>日期</th>
                    <th>类型</th>
                    <th>浏览量</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $arr = $statisticsModel->getListOfStatic();
                foreach ($arr as $row) {
                ?>
                    <tr>
                        <td><?php echo $row['date'] ?></td>
                        <td>
                            <?php

                            switch($row['type']){
                                case 1:
                                    echo "新鲜事";
                                    break;
                                case 2:
                                    echo "资讯";
                                    break;
                                case 3:
                                    echo "课评";
                                    break;
                                case 4:
                                    echo "今日用户数量";
                                    break;

                            }


                            ?>
                        </td>
                        <td><?php echo $row['amount_view'] ?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $statisticsModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
