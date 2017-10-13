<?php
$adModel = new \admin\ad\AdModel();
$currentUser = new \admin\user\UserModel();
$id = BasicTool::get('id');
$ad_category_id = BasicTool::get('ad_category_id');
$ad_category_title = BasicTool::get("ad_category_title");
$user_id = BasicTool::get('uid');
$flag = $id == null ? 'add' : 'update';

if($flag=='add'){
    $row = null;
    $form_action = "/admin/event/eventController.php?action=addAd";
}

 else {
    $row = $eventModel->getEvent($id);
     $form_action = "/admin/event/eventController.php?action=updateAd";
}

?>
<script src="/admin/resource/tools/ckeditor/ckeditor.js"></script>
<script src="/admin/resource/tools/ckfinder/ckfinder.js"></script>
<script>
    $(function() {

        $("#uploadImg").click(function() {

            //创建FormData对象 - 相当于form表单的功能
            var formData = new FormData();
            //append(name,value) - 相当于 <input name="imgFile" value="">
            formData.append('imgFile', $('#imgFile')[0].files[0]);

            $.ajax({
                url: 'adController.php?action=uploadImgWithJson',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function(){
                    var xhr = $.ajaxSettings.xhr();
                    if(onProgress && xhr.upload) {
                        xhr.upload.addEventListener("progress" , onProgress, false);
                        return xhr;
                    }
                },
                dataType:"json"
            }).done(function(data) {

                if (data.code == 1) {
                    $("#cover").val(data.result);
                    $("#imgOfUpload").attr('src', data.result).show();

                } else {
                    alert(data.message);
                }
            }).fail(function(data) {
                alert("上传出错");
            });
        })



        function onProgress(evt){
            var loaded = evt.loaded;                  //已经上传大小情况
            var tot = evt.total;                      //附件总大小
            var per = Math.floor(100*loaded/tot);     //已经上传的百分比

            console.log(per);

//            $("#son").html( per +"%" );
//            $("#son").css("width" , per +"%");
        }


    })

</script>
<script>
    function eve(){
        var publish_time = Date.parse(document.getElementById("aa").value) / 1000;
        document.getElementById("event_time").value = publish_time;
    }
    function exp(){
        var expiration_time = Date.parse(document.getElementById("bb").value)/ 1000;
        document.getElementById("expiration_time").value = expiration_time;
    }

</script>
<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'发布活动':'修改活动';
        ?></h1>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post" >
        <input name="id" value="<?php echo $id ?>" type="hidden">
        <input name="event_category_id" value="<?php echo $event_category_id?>" type="hidden"/>
        <section class="formBox">
            <h4 style="padding-left:5px;color:#555;">活动类别:&nbsp;<?php echo $event_category_title ?></h4>
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div>
                <label>封面图片: 1000X500</label>

                <input class="input input-size50" type="hidden" name="banner_url" id="cover" value="<?php echo $row['banner_url'] ?>">
                <p><img  id="imgOfUpload" src="<?php echo $row['poster_url'] ?>" style="width: 100px; height: auto; display: none"></p>
                <input type="file" name="imgFile" id="imgFile" /><input type="button" value="上传" id="uploadImg">

            </div>

            <div>
                <label>活动金额</label>
                <input type="number" class="input input-size30" name="registration_fee"><?php echo $row['registration_fee'] ?></input>
            </div>
            <div>
                <label>活动名额</label>
                <input type="number" class="input input-size30" name="max_participants"><?php echo $row['max_participants'] ?></input>
            </div>

            <div>
                <label>活动发起人</label>
                <input  type="text" class="input input-size30" name="sponsor_user_id"><?php echo $row['sponsor_user_id'] ?></input>
            </div>

            <div>
                <label>发起人ID</label>
                <input type="number" class="input input-size30" type="text" name="sponsor_name" value="<?php echo $row['sponsor_name'] ?>">
            </div>
            <div>
                <label>联系电话</label>
                <input type="text" class="input input-size30" type="text" name="sponsor_telephone" value="<?php echo $row['sponsor_telephone'] ?>">
            </div>
            <div>
                <label>微信</label>
                <input type="text" class="input input-size30" type="text" name="sponsor_wechat" value="<?php echo $row['sponsor_wechat'] ?>">
            </div>
            <div>
                <label>邮箱</label>
                <input type="text" class="input input-size30" type="text" name="sponsor_email" value="<?php echo $row['sponsor_email'] ?>">
            </div>


        </section>
        <label>活动时间</label>
        <input type="datetime-local" onchange="eve()" id="aa" style="margin-right:3rem"/>
        <label>活动有效至</label>
        <input type="datetime-local" onchange="exp()" id="bb" style="margin-right:3rem"/>

        <input type="number" name="event_time" id="event_time" hidden/>
        <input type="number" name="expiration_time" id="expiration_time" hidden/>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
