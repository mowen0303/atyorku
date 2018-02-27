<?php
$mapModel = new \admin\map\MapModel();
$buildingList = $mapModel->getAllBuildings();
?>
<header class="topBox">
    <h1><?php echo $pageTitle ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=addBuilding">添加新教学楼</a>
</nav>
<article class="mainBox">
    <form action="index.php" method="get">
        <section class="formRow">
            <input type="hidden" name="s" value="listBuilding">
            <input class="input" placeholder="教学楼缩写" type="text" name="keyword">
            <input class="btn btn-center" type="submit" title="查询课评记录" value="搜索教学楼">
        </section>
    </form>
</article>
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
                    <th>全称</th>
                    <th>简介</th>
                    <th>有无形状</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($buildingList as $building) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $building['id'] ?>"></td>
                        <td><?php echo $building['id'] ?></td>
                        <td><?php echo $building['init'] ?></td>
                        <td>
                            <a href="index.php?s=addLocation&id=<?php echo $building['id'] ?>"><?php echo $building['full_name'] ?></a>
                        </td>
                        <td><?php echo $building['info'] ?></td>
                        <?php
                        if($building['shape'] != "") {
                            echo "<td style='color: green'>有</td>";
                        } else {
                            echo "<td style='color: red'>无</td>";
                        }
                        ?>
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
