<?php
$adModel = new \admin\ad\AdModel();
$currentUser = new \admin\user\UserModel();
$id = BasicTool::get('id');
$ad_category_id = BasicTool::get('ad_category_id');
$ad_category_title = BasicTool::get("ad_category_title");
$user_id = BasicTool::get('uid');
$flag = $id == null ? 'add' : 'update';
$imageModel = new \admin\image\ImageModel();
if($flag=='add'){
    $row = null;
    $publish_time = time();
    $expiration_time = time();
    $form_action = "/admin/ad/adController.php?action=addAd";

}

else {
    $row = $adModel->getAd($id);
     $img1 = $row["img_id_1"];
     $publish_time = $row["publish_time"];
     $expiration_time = $row["expiration_time"];
     $form_action = "/admin/ad/adController.php?action=updateAd";
}

?>
<script src="/admin/resource/tools/ckeditor/ckeditor.js"></script>
<script src="/admin/resource/tools/ckfinder/ckfinder.js"></script>
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
    $("#publish_time").ready(function(){
        var a = $("#publish_time").val() * 1000;
        var b = new Date(a).toISOString();
        $("#aa").val(b.slice(0,b.indexOf(":",b.indexOf(":")+1)));
    });
    $("#expiration_time").ready(function(){
        var a = $("#expiration_time").val() * 1000;
        var b = new Date(a).toISOString();
        $("#bb").val(b.slice(0,b.indexOf(":",b.indexOf(":")+1)));
    });
</script>

<script>
    function pub(){
        var publish_time = Date.parse(document.getElementById("aa").value) / 1000;
        document.getElementById("publish_time").value = publish_time;
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
        echo $flag=='add'?'发布广告':'修改广告';
        ?></h1>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post" enctype="multipart/form-data" >
        <input name="id" value="<?php echo $id ?>" type="hidden">
        <input name="ad_category_id" value="<?php echo $ad_category_id?>" type="hidden"/>
        <section class="formBox">
            <h4 style="padding-left:5px;color:#555;">广告类别:&nbsp;<?php echo $ad_category_title ?></h4>
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div id="currentImages">
                <label style="margin-top:1.5rem">广告封面: 最多上传1张</label>
                <div id="currentImages">
                    <?php
                    if ($img1) {
                        echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img1)["url"]}' style='width: 100px; height: auto;'><input id='img1' name='img_id_1' value='{$img1}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                    }
                    ?>
                </div>
                <p style="margin-bottom:1rem"><img  id="imgOfUpload" style="width: 100px; height: auto; display: none"></p>
                <input type="file" name="imgFile[]" id="imgFile" multiple/>
            </div>


            <div>
                <label>简介<i>*</i></label>
                <textarea class="input input-textarea" name="description"><?php echo $row['description'] ?></textarea>
            </div>

            <div>
                <label>广告商<i>*</i></label>
                <input class="input input-size30" type="text" name="sponsor_name" value="<?php echo $row['sponsor_name'] ?>">
            </div>
            <div>
                <label>广告链接</label>
                <input class="input input-size30" type="text" name="ad_url" value="<?php echo $row['ad_url'] ?>">
            </div>
        </section>
        <label>广告投放时间<i>*</i></label>
        <input type="datetime-local" onchange="pub()" id="aa" style="margin-right:3rem"/>

        <label>广告有效至<i>*</i></label>
        <input type="datetime-local" onchange="exp()"  id="bb"/>

        <input type="number" name="publish_time" id="publish_time" value="<?php echo $publish_time ?>" hidden/>
        <input type="number" name="expiration_time" id="expiration_time" value="<?php echo $expiration_time ?>" hidden/>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
