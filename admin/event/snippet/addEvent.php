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
    $img_url_1 = null;
    $img_url_2 = null;
    $img_url_3 = null;
    $text_editor_initation = "<script id='container' name='description' type='text/plain'></script>";
    $form_action = "/admin/event/eventController.php?action=addEvent";
}

 else {
    $row = $eventModel->getEvent($id);
    $img_url_1 = $imageModel->getImageById($row["img_id_1"])["url"];
    $img_url_2 = $imageModel->getImageById($row["img_id_2"])["url"];
    $img_url_3 = $imageModel->getImageById($row["img_id_3"])["url"];
    $event_time = $row["event_time"];
    $text_editor_initation = "<script id='container' name='description' type='text/plain'>{$row['description']}</script>";
    $expiration_time = $row["expiration_time"];
    $form_action = "/admin/event/eventController.php?action=updateEvent";
     session_start();
     $_SESSION["ueditor_upload_location"] = "event/".$id;
}

?>

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
            <div>
                <label>活动详情</label>
                <!-- 加载编辑器的容器 -->
                <?php echo $text_editor_initation ?>
            </div>
            <div>
                <label>活动图片:</label>
                <div id="currentImages">

                    <p><img  id="imgOfUpload" src="<?php echo $img_url_1 ?>" style="width: 400px; height: auto; "></p>
                    <input type="file" onchange = "fileChangeListener()" name="imgFile[]" id="imgFile" multiple/>
                </div>

                <div>
                    <label>活动金额</label>
                    <input type="number" class="input input-size30" name="registration_fee" value="<?php echo $row['registration_fee'] ?>"/>
                </div>
                <div>
                    <label>活动名额</label>
                    <input type="number" class="input input-size30" name="max_participants" value="<?php echo $row['max_participants'] ?>"/>
                </div>
                <div>
                    <label>活动地点</label>
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


        </section>
        <label>活动时间</label>
        <input type="datetime-local" onchange="eve()" id="aa" style="margin-right:3rem" value="<?php echo $event_time*1000 ?>"/>
        <label>活动有效至</label>
        <input type="datetime-local" onchange="exp()" id="bb" style="margin-right:3rem" value="<?php echo $expiration_time*1000 ?>"/>

        <input type="number" name="event_time" id="event_time" value="<?php echo $event_time ?>" hidden/>
        <input type="number" name="expiration_time" id="expiration_time" value="<?php echo $expiration_time ?>" hidden/>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
        <!-- 配置文件 -->
        <script type="text/javascript" src="/admin/resource/tools/ueditor/ueditor.config.js"></script>
        <!-- 编辑器源码文件 -->
        <script type="text/javascript" src="/admin/resource/tools/ueditor/ueditor.all.js"></script>
        <!-- 实例化编辑器 -->
        <script type="text/javascript">
            var ue = UE.getEditor('container');
        </script>
    </form>

</article>
