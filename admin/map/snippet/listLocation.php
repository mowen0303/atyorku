<?php
$locationModel = new \admin\map\LocationModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formGuide&guide_class_id=<?php echo BasicTool::get('guide_class_id') ?>">添加大楼位置</a>
</nav>
<article class="mainBox">
    <form action="" method="post">
        <header>
            <h2>大楼位置</h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>缩写</th>
                    <th>全称</th>
                    <th>简介</th>
                    <th>纬度</th>
                    <th>经度</th>
                    <th>形状坐标</th>
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
                    <td><?php echo $row['full_name']?></td>
                    <td><?php echo $row['info']?></td>
                    <td><?php echo $row['lat']?></td>
                    <td><?php echo $row['lng']?></td>
                    <td><?php echo $row['shape']?></td>
                </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn"><a class="btn" href="index.php?action=showFormAdd">添加</a>
        </footer>
    </form>
</article>