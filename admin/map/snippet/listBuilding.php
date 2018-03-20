<?php
$mapModel = new \admin\map\MapModel();
$buildingList = $mapModel->getAllBuildings();
$version = $mapModel->getMapDataVersion();
?>
<style>
    td {word-break: break-all;}
</style>
<header class="topBox">
    <h1><?php echo $pageTitle ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="http://www.yorku.ca/web/maps/" target="_blank">约克官网地图</a><a class="btn" href="/apps/mapGenerator/index.html" target="_blank">地图标记工具</a><a class="btn" href="index.php?s=editBuilding">添加新教学楼</a>
</nav>
<article class="mainBox">
    <form action="index.php" method="get">
        <p>地图版本号：<?php echo $version->version?></p>
        <p>更新日期：<?php echo $version->time?></p>
    </form>
</article>
<article class="mainBox">
    <form action="mapController.php?action=deleteBuildingByIDs" method="post">
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
                    <th width="35%">坐标</th>
                    <th width="25%">简介</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($buildingList as $building) {
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="ids[]" value="<?php echo $building['id'] ?>"></td>
                        <td><?php echo $building['id'] ?></td>
                        <td><?php echo $building['abbreviation'] ?></td>
                        <td><?php echo $building['full_name'] ?></td>
                        <td><?php echo $building['coordinates'] ?></td>
                        <td><?php echo $building['description'] ?></td>
                        <td><a href="index.php?s=editBuilding&id=<?php echo $building['id'] ?>" class="btn">编辑</a></td>
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
