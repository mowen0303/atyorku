<?php
$msgModel = new \admin\msg\MsgModel();
$userModel = new \admin\user\UserModel();
$type = BasicTool::get("type");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php">普通</a><a class="btn" href="index.php?type=alert">通告</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo BasicTool::get('title')?></h2></header>
    <form action="msgController.php?action=deleteMsg" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>发布人</th>
                    <th>内容</th>
                    <th>接受者ID</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if($type == "alert"){
                    $arr = $msgModel->getListOfAlert();
                }else {
                    $arr = $msgModel->getListOfMsg();
                }

                foreach ($arr as $row) {
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['alias']?></td>
                        <td><?php echo $row['content']?></a></td>
                        <td><?php echo $row['receiver_id']?></a></td>
                        <td><?php echo $row['time']?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $msgModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>

<article class="mainBox">
    <h2>调试数据</h2>
    <section>
        <?php
        foreach($arr[0] as $k => $v){
            echo $k." : ".$v."<br>";
        }
        ?>
    </section>
</article>
