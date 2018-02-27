<?php
$mapModel = new \admin\map\MapModel();
$id = BasicTool::get('id');
$action = $id ? 'updateBuilding' : 'addBuilding';
?>
<header class="topBox">
    <h1><?php echo $pageTitle.' - ';
        echo $flag == 'add' ? '添加大楼' : '修改大楼'; ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/map/">返回列表</a>
</nav>
<article class="mainBox">
    <form action="locationController.php?action=<?php echo $action ?>" method="post">
        <section class="formBox">
            <div>
                <label>大楼缩写<i>*</i></label>
                <input class="input" type="text" name="init" id="init" value="<?php echo $row['init'] ?>">
            </div>
            <div>
                <label>大楼全名<i>*</i></label>
                <input class="input" type="text" name="full_name" id="fullName" value="<?php echo $row['full_name'] ?>">
            </div>
            <div>
                <label>纬经度坐标<i>*</i></label>
                <input class="input" type="text" name="latitude" value="<?php echo $row['latitude'] ?>">
            </div>
            <div>
                <label>形状坐标<i>*</i></label>
                <textarea class="input input-textarea" name="shape"><?php echo $row['shape'] ?></textarea>
            </div>
            <div>
                <label>大楼简介</label>
                <textarea class="input input-textarea" name="info"><?php echo $row['info'] ?></textarea>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
