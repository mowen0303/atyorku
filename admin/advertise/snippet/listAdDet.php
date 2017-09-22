<?php
$adModel = new \admin\advertise\adModel();
$userModel = new \admin\user\UserModel();

?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=createAd">发布</a>
</nav>
<article class="mainBox">
    <header><h2>未过期广告</h2></header>
    <form action="adController.php?action=deleteAd" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>标题</th>
                    <th>板块</th>
                    <th>发布用户</th>
                    <th>发布时间</th>
                    <th>开始时间</th>
                    <th>到期时间</th>
                    <th>浏览数</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $arr = $adModel->getListWithUser();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><a href="index.php?s=viewAd&aid=<?php echo $row['id']?>"><?php echo $row['id'] ?></a></td>
                        <td><a href="index.php?s=viewAd&aid=<?php echo $row['id']?> "><?php echo $row['title']?></a></td>
                        <td><?php  if($row['class_id']==1){
                                echo '课评';
                            }else if ($row['class_id']==2){
                                echo '论坛';
                            }
                            else if ($row['class_id']==3){
                                echo '指南';
                            }
                            else {echo '其他';}
                            ?>

                            </td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo  BasicTool::translateTime($row['datetopost']) ?></td>
                        <td><?php echo  date("Y-m-d h:i", $row['startdate']) ?></td>
                        <td><?php echo  date("Y-m-d h:i", $row['expiredate']) ?></td>
                        <td><?php echo $row['count'] ?></td>
                        <td><a href="index.php?s=createAd&aid=<?php echo $row['id']?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $adModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
<article class="mainBox">
    <header><h2>已过期广告</h2></header>
    <form action="adController.php?action=deleteAd" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>标题</th>
                    <th>板块</th>
                    <th>发布用户</th>
                    <th>发布时间</th>
                    <th>开始时间</th>
                    <th>到期时间</th>
                    <th>浏览数</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $arr = $adModel->getListWithUserExp();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><a href="index.php?s=viewAd&aid=<?php echo $row['id']?>"><?php echo $row['id'] ?></a></td>
                        <td><a href="index.php?s=viewAd&aid=<?php echo $row['id']?> "><?php echo $row['title']?></a></td>
                        <td><?php  if($row['class_id']==1){
                                echo '课评';
                            }else if ($row['class_id']==2){
                                echo '论坛';
                            }
                            else if ($row['class_id']==3){
                                echo '指南';
                            }
                            else {echo '其他';}
                            ?>

                        </td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo  BasicTool::translateTime($row['datetopost']) ?></td>
                        <td><?php echo  date("Y-m-d h:i", $row['startdate']) ?></td>
                        <td><?php echo  date("Y-m-d h:i", $row['expiredate']) ?></td>
                        <td><?php echo $row['count'] ?></td>
                        <td><a href="index.php?s=createAd&aid=<?php echo $row['id']?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $adModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
<article class="mainBox">
    <header><h2>已删除广告</h2></header>
    <form action="adController.php?action=realDeleteAd" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>标题</th>
                    <th>板块</th>
                    <th>发布用户</th>
                    <th>发布时间</th>
                    <th>开始时间</th>
                    <th>到期时间</th>
                    <th>浏览数</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $arr = $adModel->getListWithUserDel();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><a href="index.php?s=viewAd&aid=<?php echo $row['id']?>"><?php echo $row['id'] ?></a></td>
                        <td><a href="index.php?s=viewAd&aid=<?php echo $row['id']?> "><?php echo $row['title']?></a></td>
                        <td><?php  if($row['class_id']==1){
                                echo '课评';
                            }else if ($row['class_id']==2){
                                echo '论坛';
                            }
                            else if ($row['class_id']==3){
                                echo '指南';
                            }
                            else {echo '其他';}
                            ?>

                        </td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo  BasicTool::translateTime($row['datetopost']) ?></td>
                        <td><?php echo  date("Y-m-d h:i", $row['startdate']) ?></td>
                        <td><?php echo  date("Y-m-d h:i", $row['expiredate']) ?></td>
                        <td><?php echo $row['count'] ?></td>
                        <td><a href="index.php?s=createAd&aid=<?php echo $row['id']?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $adModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="彻底删除" class="btn" onclick="return confirm('确认彻底删除吗?')">
        </footer>
    </form>
</article>