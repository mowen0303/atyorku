<?php
$locationModel = new \admin\map\LocationModel();
$id = BasicTool::get('id');
$flag = $id ? 'update' : 'add';
if ($flag=='update') {
    $row = $locationModel->getLocationById($id);
    $form_action = "updateLocation";
}
else {
    $row = null;
    $form_action = "addLocation";
}
?>
<script>
    function fullNameDoesExist(str) {
        if (str.length == 0) {
            document.getElementById("fullName").style.background = "#fff";
        } else {
            // TODO
            var url = "www.atyorku.ca/admin/map/locationModel.php?"
        }
    }

    function initDoesExist(str) {
        // TODO
    }
</script>
<header class="topBox">
    <h1><?php echo $pageTitle.' - ';
        echo $flag == 'add' ? '添加大楼' : '修改大楼'; ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/map/">返回列表</a>
</nav>
<article class="mainBox">
    <form action="locationController.php?action=<?php echo $form_action ?>" method="post">
        <section class="formBox">
            <div>
                <label>大楼ID</label>
                <p style="color: black; font-size: 18px"><?php echo $row['id'] ?></p>
                <input hidden class="input" type="text" name="id" value="<?php echo $row['id'] ?>">
            </div>
            <div>
                <label>大楼缩写<i>*</i></label>
                <input class="input" type="text" name="init" id="init" value="<?php echo $row['init'] ?>">
            </div>
            <div>
                <label>大楼全名<i>*</i></label>
                <input class="input" type="text" name="full_name" id="fullName" value="<?php echo $row['full_name'] ?>">
            </div>
            <div>
                <label>纬度<i>*</i></label>
                <input class="input" type="text" name="latitude" value="<?php echo $row['latitude'] ?>">
            </div>
            <div>
                <label >经度<i>*</i></label>
                <input class="input" type="text" name="longitude" value="<?php echo $row['longitude'] ?>">
            </div>
            <div>
                <label>大楼简介</label>
                <textarea class="input input-textarea" name="info"><?php echo $row['info'] ?></textarea>
            </div>
            <div>
                <label>形状坐标</label>
                <textarea class="input input-textarea" name="shape"><?php echo $row['shape'] ?></textarea>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>