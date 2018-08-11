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
                <input class="input input-size30" type="text" name="code" value="<?php echo $row['code'] ?>">
            </div>
            <div>
                <label>大楼缩写</label>
                <input class="input input-size30" type="text" name="abbreviation" value="<?php echo $row['abbreviation'] ?>">
            </div>
            <div>
                <label>大楼全名<i>*</i></label>
                <input class="input input-size30" type="text" name="full_name" value="<?php echo $row['full_name'] ?>">
            </div>
            <div>
                <label>类型<i>*</i></label>
                <select class="input input-select input-size30 selectDefault" name="type" defvalue="<?php echo $row['type'] ?>">
                    <option value='0'>无类型</option>
                    <option value='办公室'>办公室</option>
                    <option value='图书馆'>图书馆</option>
                    <option value='学院楼'>学院楼</option>
                    <option value='宿舍'>宿舍</option>
                    <option value='公寓'>公寓</option>
                    <option value='公共停车场'>公共停车场</option>
                    <option value='包月停车场'>包月停车场</option>
                    <option value='学院楼'>学院楼</option>
                    <option value='表演设施'>表演设施</option>
                    <option value='运动设施'>运动设施</option>
                    <option value='历史建筑'>历史建筑</option>
                    <option value='非约克建筑'>非约克建筑</option>
                    <option value='旅游景点'>旅游景点</option>
                </select>
            </div>
            <div>
                <label>价格</label>
                <input class="input input-size30" type="text" name="price" value="<?php echo $row['price'] ?>">
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
