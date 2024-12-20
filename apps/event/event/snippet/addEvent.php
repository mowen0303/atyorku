<?php
date_default_timezone_set("America/Toronto");
$eventModel = new \apps\event\event\EventModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$eventCategoryModel = new \apps\event\eventCategory\EventCategoryModel();
$eventCategories = $eventCategoryModel->getEventCategories();

$id = BasicTool::get('id');
$flag = $id == null ? 'add' : 'update';

if ($flag == 'add') {
    $row = null;
    $form_action = "./eventController.php?action=addEvent";
    $event_time = time();
    $expiration_time = time();
} else {
    $row = $eventModel->getEvent($id);
    $img1 = $row["img_id_1"];
    $img2 = $row["img_id_2"];
    $img3 = $row["img_id_3"];
    $event_time = $row["event_time"];
    $expiration_time = $row["expiration_time"];
    $form_action = "./eventController.php?action=updateEvent";
}

?>
<script>
    //删除已上传图片 (点击更新后生效)

    function removeImg(i) {
        var v = $('#img' + i).val();
        if (v) {
            $('#img' + i).attr('value', '');
            $('#pic' + i).attr('src', '').show();
            $('#imgbtn' + i).hide();
        }
    }

    $(function () {

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imgOfUpload').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgFile").change(function () {
            readURL(this);
        });
    })
</script>
<script>
    function fileChangeListener() {
        var file = document.getElementById("imgFile").files[0];
        var reader = new FileReader();
        var img = document.getElementById("imgOfUpload");
        reader.onload = function () {
            img.src = reader.result;
        }
        reader.readAsDataURL(file);
    }

</script>

<header class="topBox">
    <h1> <?php
        echo $pageTitle . '-';
        echo $flag == 'add' ? '发布活动' : '修改活动';
        ?></h1>
    <nav class="mainNav">
        <a class="btn" href="index.php?s=getEventsByCategory">返回</a>
    </nav>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post" enctype="multipart/form-data">
        <input name="id" value="<?php echo $id ?>" type="hidden"/>
        <section class="formBox">
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <?php
            $userId = $row['sponsor_user_id']?:$currentUser->userId;
            $html = "<div><label>用户id</label><input class='input input-size30' type='text' name='sponsor_user_id' value='{$userId}'/>当前用户: {$currentUser->aliasName} (ID: {$currentUser->userId})</div>";
            if ($currentUser->isAdmin)
                echo $html;
            ?>
            <div>
                <label>活动类别<i>*</i></label>
                <select class = 'input input-select input-size30' name="event_category_id">
                    <?php
                    $placeholderSelected=$flag=='add'?'selected':'';
                    $html = "<option value='' disabled {$placeholderSelected}>请选择分类...</option>";
                    echo $html;
                    foreach ($eventCategories as $category){
                        if ($flag == 'update' && $row['event_category_id'] == $category['id'])
                            $html = "<option selected value='{$category["id"]}'>{$category['title']}</option>";
                        else
                            $html = "<option value='{$category["id"]}'>{$category['title']}</option>";
                        echo $html;
                    }
                    ?>
                </select>
            </div>
            <div style="display:flex;flex-direction:row">
                <div>
                    <label>活动开始时间<i>*</i></label>
                    <input id="i1" class="input" type="datetime-local" name="event_time" value="<?php echo date("Y-m-d",$event_time)."T".date("H:i",$event_time)?>"  id="aa" style="margin-right:3rem"/>
                </div>
                <div style="margin-left: 20px">
                    <label>结束时间<i>*</i></label>
                    <input class="input" type="datetime-local" name="expiration_time" value="<?php echo date("Y-m-d",$expiration_time)."T".date("H:i",$expiration_time)?>" id="bb" style="margin-right:3rem"/>
                </div>
            </div>

            <div style="display:flex;flex-direction:row">
                <div style="flex:1;margin-right:20px">
                    <label>活动地点<i>*</i></label>
                    <input type="text" class="input" name="location" value="<?php echo $row['location'] ?>"/>
                </div>
                <div style="flex:1">
                    <label>定位连接 (Google Map / 百度地图)</label>
                    <input type="text" class="input" name="location_link" value="<?php echo $row['location_link'] ?>"/>
                </div>
            </div>

             <div style="display:flex;flex-direction:row">
                 <div style="flex:1;margin-right:20px">
                     <label>报名方式<i>*</i>（drop-in / 在线报名）</label>
                     <input type="text" class="input" name="registration_way" value="<?php echo $row['registration_way'] ?>"/>
                 </div>
                 <div style="flex:1;margin-right:20px">
                    <label>活动金额（不填代表免费）</label>
                    <input type="text" class="input" name="registration_fee" value="<?php echo $row['registration_fee'] ?>"/>
                </div>
                 <div style="flex:1;margin-right:20px">
                     <label>报名连接（非在线报名可不填）</label>
                     <input type="text" class="input" name="registration_link" value="<?php echo $row['registration_link'] ?>"/>
                 </div>
                <div style="flex:1;margin-right:20px">
                    <label>名额限制</label>
                    <input type="number" class="input" name="max_participants" value="<?php echo $row['max_participants'] ?>"/>
                </div>
            </div>

            <div style="display:flex;flex-direction:row">
                <div style="flex:1;margin-right:20px">
                    <label>活动联系人<i>*</i></label>
                    <input type="text" class="input" name="sponsor_name" value="<?php echo $row['sponsor_name'] ?>"/>
                </div>
                <div style="flex:1;margin-right:20px">
                    <label>联系电话</label>
                    <input type="tel" class="input" name="sponsor_telephone" value="<?php echo $row['sponsor_telephone'] ?>">
                </div>
                <div style="flex:1;margin-right:20px">
                    <label>微信</label>
                    <input type="text" class="input" type="text" name="sponsor_wechat" value="<?php echo $row['sponsor_wechat'] ?>">
                </div>
                <div style="flex:1">
                    <label>邮箱</label>
                    <input type="email" name="sponsor_email" class="input" value="<?php echo $row['sponsor_email'] ?>">
                </div>
            </div>
            <div>
                <label>活动详情<i>*</i></label>
                <textarea class="input input-textarea" name="description"><?php echo $row["description"] ?></textarea>
            </div>
            <div style="width:50%">
                <label>活动详情链接</label>
                <input type="text" class="input" name="detail_url" value="<?php echo $row['detail_url'] ?>"/>
            </div>
            <div>
                <div id="currentImages">
                    <label>活动图片: 最多3张 (第1张,封面,尺寸:800*400) (第2,3张,自动嵌入活动介绍,尺寸不限制)</label>
                    <input type="file" name="imgFile[]" id="imgFile" multiple/>
                    <div id="currentImages">
                        <?php
                        if ($img1) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img1)["url"]}' style='width: 100px; height: auto;'><input id='img1' name='img_id_1' value='{$img1}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                        }
                        if ($img2) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic2' src='{$imageModel->getImageById($img2)["url"]}' style='width: 100px; height: auto;'><input id='img2' name='img_id_2' value='{$img2}' style='display: none'></div><div><input type='button' id='imgbtn2' value='删除' onclick='removeImg(2);'></div></div>";
                        }
                        if ($img3) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic3' src='{$imageModel->getImageById($img3)["url"]}' style='width: 100px; height: auto;'><input id='img3' name='img_id_3' value='{$img3}' style='display: none'></div><div><input type='button' id='imgbtn3' value='删除' onclick='removeImg(3);'></div></div>";
                        }
                        ?>
                    </div>
                    <p style="margin-bottom:1rem"><img id="imgOfUpload" style="width: 100px; height: auto; display: none"></p>
                </div>
            </div>
            <?php
            if ($currentUser->isUserHasAuthority('ADMIN'))
                echo "<div>
                    <label>设置排序 (仅管理员可见)</label>
                    <input class='input input-size30' type='number' name='sort' value='{$row["sort"]}'>
                    </div>";
            ?>
        </section>

        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
        <div id="cc">

        </div>
    </form>
</article>
