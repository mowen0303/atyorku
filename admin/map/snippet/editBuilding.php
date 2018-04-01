<?php
$mapModel = new \admin\map\MapModel();
$id = BasicTool::get('id');
$row = $id ? $mapModel->getBuildingByID($id) : [];
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - <?php echo $id ? '添加大楼' : '修改大楼'; ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/map/">返回列表</a>
</nav>
<article class="mainBox">
    <form action="mapController.php?action=editBuilding" method="post">
        <input type="hidden" name="id" value="<?php echo $id?>">
        <section class="formBox">
            <div>
                <label>地图CODE<i>*</i></label>
                <input class="input" type="text" name="code" id="init" value="<?php echo $row['code'] ?>">
            </div>
            <div>
                <label>大楼缩写</label>
                <input class="input" type="text" name="abbreviation" id="init" value="<?php echo $row['abbreviation'] ?>">
            </div>
            <div>
                <label>大楼全名<i>*</i></label>
                <input class="input" type="text" name="full_name" id="fullName" value="<?php echo $row['full_name'] ?>">
            </div>
            <div>
                <label>纬经度坐标 and 形状坐标<i>*</i></label>
                <textarea class="input input-textarea" name="coordinates"><?php echo $row['coordinates'] ?></textarea>
            </div>
            <div>
                <label>大楼简介</label>
                <textarea class="input input-textarea" name="description"><?php echo $row['description'] ?></textarea>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
