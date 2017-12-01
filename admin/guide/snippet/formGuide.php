<?php
$guideModel = new \admin\guide\GuideModel();
$currentUser = new \admin\user\UserModel();
$guide_class_id = BasicTool::get('guide_class_id');
$guide_id = BasicTool::get('guide_id');
$user_id = BasicTool::get('uid');
$flag = $guide_id == null ? 'add' : 'update';

if($flag=='add'){
    $row = null;
    if($_COOKIE['content'] || $_COOKIE['title']){
        $row = [];
        $row['guide_order']=$_COOKIE['guide_order'];
        $row['cover']=$_COOKIE['cover'];
        $row['title']=$_COOKIE['title'];
        $row['introduction']=$_COOKIE['introduction'];
        $row['content']=$_COOKIE['content'];
        $row['user_id']=$_COOKIE['user_id'];
    }

} else {
    $row = $guideModel->getRowOfGuideById($guide_id);
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
                url: 'guideController.php?action=uploadImgWithJson',
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

<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'发布新信息':'修改论坛信息';
        ?></h1>
</header>

<article class="mainBox">
    <form action="guideController.php?action=modifyGuide" method="post">
        <input name="guide_class_old_id" value="<?php echo $row['guide_class_id']?>" type="hidden">

        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="time" value="<?php echo $row['time']?>" type="hidden">
            <input name="guide_id" value="<?php echo $guide_id ?>" type="hidden">
            <input  name="guide_class_id" value="<?php echo $guide_class_id?>" type="hidden">
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div>
                <label>所属分类<i>*</i></label>
                <select class="input input-select input-size50 selectDefault" name="guide_class_id" defvalue="<?php echo $row['guide_class_id']?>">
                    <?php
                        $arrOfClass = $guideModel->getListOfGuideClass(100);
                        foreach($arrOfClass as $rowOfClass){
                            echo "1111";
                            echo '<option value="'.$rowOfClass['id'].'">'.$rowOfClass['title'].'</option>';

                    }
                    ?>
                </select>
            </div>
            <div>
                <label>封面图片: 1000X500</label>

                <input class="input input-size50" type="hidden" name="cover" id="cover" value="<?php echo $row['cover'] ?>">
                <p><img  id="imgOfUpload" src="<?php echo $row['cover'] ?>" style="width: 100px; height: auto; display: none"></p>
                <input type="file" name="imgFile" id="imgFile" /><input type="button" value="上传" id="uploadImg">

            </div>
            <?php $guide_author=$currentUser->userId;
            if($flag != 'add'){
                $guide_author=$row['user_id'];
            }?>

            <div>
                <label>正文</label>
                <textarea class="input input-textarea" name="content"><?php echo $row['content'] ?></textarea>
                <script>
                    CKEDITOR.replace( 'content', {
                        filebrowserBrowseUrl : '/admin/resource/tools/ckfinder/ckfinder.html',
                        filebrowserImageBrowseUrl : '/admin/resource/tools/ckfinder/ckfinder.html?Type=Images',
                        filebrowserFlashBrowseUrl : '/admin/resource/tools/ckfinder/ckfinder.html?Type=Flash',
                        filebrowserUploadUrl : '/admin/resource/tools/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                        filebrowserImageUploadUrl : '/admin/resource/tools/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
                        filebrowserFlashUploadUrl : '/admin/resource/tools/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
                    });
                </script>
                <style>
                    body .cke_inner { margin-bottom: 0}
                </style>
            </div>
            <div>
                <label>简介</label>
                <textarea class="input input-textarea" name="introduction"><?php echo $row['introduction'] ?></textarea>
            </div>
            <div>
                <lable>作者ID</lable>
                <input class="input input-size30" type="text" name="id" value="<?php echo $guide_author ?>">
                <label>当前用户信息:<?php echo "别名: {$currentUser->aliasName}. ID: {$currentUser->userId}" ?> </label>
            </div>
            <div>
                <label>顺序</label>
                <input class="input input-size30" type="text" name="guide_order" value="<?php echo $row['guide_order'] ?>">
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
<article class="mainBox">
<xmp>
文字居中：class="center"
<blockquote>引述</blockquote>
<p class="imgDescribe">图片下方文字解释</p>
<p class="img-10"><img src=""></p> (等比缩放图片尺寸：img-10 ... img-100)
<mark>高亮文字</mark>
-------------------------------
表格的Cass:
1. width-10  ... 100  表格宽度
2. center  表格内的文字居中
-------------------------------
<div class="boxWithTitle">
    <tt class="tit">标题</tt>
    <p>内容</p>
</div>
-------------------------------
折叠文本块：
<div class="foldBox"></div>

</xmp>


</article>
