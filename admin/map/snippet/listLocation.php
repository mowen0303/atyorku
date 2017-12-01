<?php
$locationModel = new \admin\map\LocationModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=addLocation">添加大楼位置</a>
</nav>
<article class="mainBox">
    <form action="LocationController.php?action=deleteLocationById" method="post">
        <header>
            <h2>大楼位置</h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>#</th>
                    <th>缩写</th>
                    <th>图片</th>
                    <th>全称</th>
                    <th>简介</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $locationModel->getListOfLocation();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['id']?></td>
                        <td><?php echo $row['init']?></td>
                        <td><img height="36" height="36" src="<?php echo $row['pic']?>"></td>
                        <td><a href="index.php?s=addLocation&id=<?php echo $row['id']?>"><?php echo $row['full_name']?></a></td>
                        <td><?php echo $row['info']?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn">
        </footer>
    </form>
</article>