<?php
$eventModel = new \admin\event\EventModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$eventCategoryModel = new \admin\eventCategory\EventCategoryModel();

$user_id = $currentUser->userId;
$event_category_id = BasicTool::get('event_category_id',"event_category_id missing");
$event_category_title = $eventCategoryModel->getEventCategory($event_category_id)["title"];

$id = BasicTool::get('id');
$flag = $id == null ? 'add' : 'update';

if($flag=='add'){
    $row = null;
    $form_action = "/admin/event/eventController.php?action=addEvent";
    $event_time = time();
    $expiration_time = time();
}

 else {
    $row = $eventModel->getEvent($id);
    $img1 = $row["img_id_1"];
    $img2 = $row["img_id_2"];
    $img3 = $row["img_id_3"];
    $event_time = $row["event_time"];
    $expiration_time = $row["expiration_time"];
    $form_action = "/admin/event/eventController.php?action=updateEvent";
}

?>
<script>
    //删除已上传图片 (点击更新后生效)
    function removeImg(i) {
        var v = $('#img'+i).val();
        if (v) {
            $('#img'+i).attr('value', '');
            $('#pic'+i).attr('src', '').show();
            $('#imgbtn'+i).hide();
        }
    }

    $(function() {

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imgOfUpload').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgFile").change(function(){
            readURL(this);
        });
    })
</script>
<script>
    function eve(){
        var event_time = Date.parse(document.getElementById("aa").value) / 1000;
        document.getElementById("event_time").value = event_time;
    }

    function exp(){
        var expiration_time = Date.parse(document.getElementById("bb").value)/ 1000;
        document.getElementById("expiration_time").value = expiration_time;
    }

    function fileChangeListener() {
        var file = document.getElementById("imgFile").files[0];
        var reader = new FileReader();
        var img = document.getElementById("imgOfUpload");
        reader.onload = function () {
            img.src = reader.result;
        }
        reader.readAsDataURL(file);
    }
    $("#event_time").ready(function(){
        var a = $("#event_time").val() * 1000;
        var b = new Date(a).toISOString();
        $("#aa").val(b.slice(0,b.indexOf(":",b.indexOf(":")+1)));
    });
    $("#expiration_time").ready(function(){
        var a = $("#expiration_time").val() * 1000;
        var b = new Date(a).toISOString();
        $("#bb").val(b.slice(0,b.indexOf(":",b.indexOf(":")+1)));
    });



</script>

<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'发布活动':'修改活动';
        ?></h1>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post" enctype="multipart/form-data">
        <input name="id" value="<?php echo $id ?>" type="hidden">
        <input name="event_category_id" value="<?php echo $event_category_id?>" type="hidden"/>
        <input name="sponsor_user_id" value="<?php echo $user_id ?>" type="hidden"/>
        <section class="formBox">
            <h4 style="padding-left:5px;color:#555;">活动类别:&nbsp;<?php echo $event_category_title ?></h4>
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
        </section>
        <div>
            <label>活动详情<i>*</i></label>
            <textarea class="input input-textarea" name="description"><?php echo $row["description"]?></textarea>
        </div>
        <section class="formBox">
            <div>
                <div id="currentImages">
                    <label style="margin-top:1.5rem">活动图片: 最多上传3张</label>
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
                    <p style="margin-bottom:1rem"><img  id="imgOfUpload" style="width: 100px; height: auto; display: none"></p>
                    <input type="file" name="imgFile[]" id="imgFile" multiple/>
                </div>

                <div>
                    <label>活动金额<i>*</i></label>
                    <input type="number" class="input input-size30" name="registration_fee" value="<?php echo $row['registration_fee'] ?>"/>
                </div>
                <div>
                    <label>活动名额<i>*</i></label>
                    <input type="number" class="input input-size30" name="max_participants" value="<?php echo $row['max_participants'] ?>"/>
                </div>
                <div>
                    <label>活动地点</label>
                    <input type="text" class="input input-size30" name="location" value="<?php echo $row['location'] ?>"/>
                </div>
                <div>
                    <label>活动地点连接</label>
                    <input type="text" class="input input-size30" name="location_link" value="<?php echo $row['location_link'] ?>"/>
                </div>
                <div>
                    <label>活动发起人姓名</label>
                    <input  type="text" class="input input-size30" name="sponsor_name" value="<?php echo $row['sponsor_user_id'] ?>"/>
                </div>

                <div>
                    <label>联系电话</label>
                    <input type="tel" class="input input-size30" name="sponsor_telephone" value="<?php echo $row['sponsor_telephone'] ?>">
                </div>
                <div>
                    <label>微信</label>
                    <input type="text" class="input input-size30" type="text" name="sponsor_wechat" value="<?php echo $row['sponsor_wechat'] ?>">
                </div>
                <div>
                    <label>邮箱</label>
                    <input type="email" name="sponsor_email" class="input input-size30" value="<?php echo $row['sponsor_email'] ?>">
                </div>
                <div>
                    <label>顺序</label>
                    <input class="input input-size30" type="number" name="sort" value="<?php echo $row['sort'] ?>">
                </div>
            </div>

        </section>
        <label>活动时间<i>*</i></label>
        <input type="datetime-local" onchange="eve()" id="aa" style="margin-right:3rem"/>
        <label>活动有效至<i>*</i></label>
        <input type="datetime-local" onchange="exp()" id="bb"  style="margin-right:3rem" />

        <input type="number" name="event_time" id="event_time" value="<?php echo $event_time ?>" hidden/>
        <input type="number" name="expiration_time" id="expiration_time" value="<?php echo $expiration_time ?>" hidden/>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
        <div id="cc">

        </div>
    </form>
</article>