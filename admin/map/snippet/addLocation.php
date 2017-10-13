<?php
$locationModel = new \admin\map\LocationModel();
$id = BasicTool::get('id');
$flag = $id ? 'update' : 'add';
if ($flag=='update') {
    $row = $locationModel->getLocationById($id);
    $form_action = "/admin/map/LocationController.php?action=updateLocation";
}
else {
    $row = null;
    $form_action = "/admin/map/LocationController.php?action=addLocation";
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle.' - ';
        echo $flag == 'add' ? '添加大楼' : '修改大楼'; ?></h1>
</header>
<article class="mainBox">
    <form action="locationController.php?action=addLocation" method="post">
        <section class="formBox">
            <div>
                <label>大楼ID</label>
                <input class="input" type="text" name="id" value="<?php echo $row['id'] ?>">
            </div>
            <div>
                <label>大楼缩写<i>*</i></label>
                <input class="input" type="text" name="init" value="<?php echo $row['init'] ?>">
            </div>
            <div>
                <label>大楼全名<i>*</i></label>
                <input class="input" type="text" name="full_name" value="<?php echo $row['full_name'] ?>">
            </div>
            <div>
                <label>纬度<i>*</i></label>
                <input class="input" type="text" name="lat" value="<?php echo $row['lat'] ?>">
            </div>
            <div>
                <label >经度<i>*</i></label>
                <input class="input" type="text" name="lng" value="<?php echo $row['lng'] ?>">
            </div>
            <div>
                <label>大楼简介</label>
                <textarea class="input input-textarea" name="info" value="<?php echo $row['info'] ?>"></textarea>
            </div>
            <div>
                <label>形状坐标</label>
                <input class="input" type="text" name="shape" value="<?php echo $row['shape'] ?>">
            </div>
            <div>
                <label>上传图片</label>

            </div>
            <!-- TODO - Add visibility in the future, for now it's useless -->
<!--            <div>-->
<!--                --><?php //if ($row['visible']==0) {?>
<!--                    <input type="radio" checked ="true" class="cBox" name="visible" value="0">显示-->
<!--                    <input type="radio" class="cBox" name="visible" value="1">隐藏-->
<!--                    --><?php
//                }
//                elseif ($row['visible']==1) {?>
<!--                    <input type="radio" class="cBox" name="visible" value="0">显示-->
<!--                    <input type="radio" checked ="true" class="cBox" name="visible" value="1">隐藏-->
<!--                    --><?php
//                }?>
<!--            </div>-->
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>