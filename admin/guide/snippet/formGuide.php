<?php
session_start();
$guideModel = new \admin\guide\GuideModel();
$currentUser = new \admin\user\UserModel();
$guide_class_id = BasicTool::get('guide_class_id');
$guide_id = BasicTool::get('guide_id');
$flag = $guide_id == null ? 'add' : 'update';

if ($flag == 'add') {
    $row = null;
    $guide_id = $guideModel->addGuide($guide_class_id, '待编辑草稿...', '', '', $currentUser->userId, '', 0);
} else {
    $row = $guideModel->getRowOfGuideById($guide_id);
}
$_SESSION["ueditor_upload_location"] = "guide2/" . $guide_id;
?>

<script>
    $(function () {

        $("#uploadImg").click(function () {

            //创建FormData对象 - 相当于form表单的功能
            var formData = new FormData();
            //append(name,value) - 相当于 <input name="imgFile" value="">
            formData.append('imgFile', $('#imgFile')[0].files[0]);
            formData.append('oldImg', $('#imgOfUpload').attr("src"));

            $.ajax({
                url: 'guideController.php?action=uploadImgWithJson',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();
                    if (onProgress && xhr.upload) {
                        xhr.upload.addEventListener("progress", onProgress, false);
                        return xhr;
                    }
                },
                dataType: "json"
            }).done(function (data) {

                if (data.code == 1) {
                    $("#cover").val(data.result);
                    $("#imgOfUpload").attr('src', data.result).show();

                } else {
                    alert(data.message);
                }
            }).fail(function (data) {
                alert("上传出错");
            });
        })


        function onProgress(evt) {
            var loaded = evt.loaded;                  //已经上传大小情况
            var tot = evt.total;                      //附件总大小
            var per = Math.floor(100 * loaded / tot);     //已经上传的百分比

            console.log(per);

//            $("#son").html( per +"%" );
//            $("#son").css("width" , per +"%");
        }



        //保存
        $saveBtn = $("#saveBtn");
        $saveBtn.click(function(){
            $.ajax({
                url: 'guideController.php?action=updateGuide',
                type: 'POST',
                contentType:"application/x-www-form-urlencoded",
                data: $("#guideForm").serialize()
            }).done(function (data) {
                $saveBtn.html("成功");
                setTimeout(function(){
                    $saveBtn.html("保存");
                },1000)
            }).fail(function (data) {
                alert("保存失败");
            });
        })

    })

</script>
<style>
    .row {
        overflow: hidden;
    }
    .col-2 {
        float:left;
        width: 47%;
    }
    .col-2:nth-child(1) {
        margin-right: 5%;
    }
</style>
<header class="topBox">
    <h1> <?php
        echo $pageTitle . '-';
        echo $flag == 'add' ? '发布新信息' : '修改论坛信息';
        ?></h1>
</header>

<article class="mainBox">
    <a id="saveBtn" style="position: fixed; top:26px; right: 200px; height: 70px; width: 70px; border-radius: 70px; padding: 0; line-height: 70px" class="btn">保存</a>
    <a style="position: fixed; top:26px; right: 100px; height: 70px; width: 70px; border-radius: 100px; padding: 0; line-height: 70px" class="btn" href="/apps/guide/index.php?guide_id=<?php echo $guide_id?>" target="_blank">预览</a>
    <div style="max-width: 700px">
        <form id="guideForm" action="guideController.php?action=updateGuide" method="post">
            <input name="guide_class_old_id" value="<?php echo $row['guide_class_id'] ?>" type="hidden">

            <section class="formBox">

                <input name="flag" value="<?php echo $flag ?>" type="hidden">
                <input name="time" value="<?php echo $row['time'] ?>" type="hidden">
                <input name="guide_id" value="<?php echo $guide_id ?>" type="hidden">
                <input name="guide_class_id" value="<?php echo $guide_class_id ?>" type="hidden">
                <div>
                    <label>标题<i>*</i></label>
                    <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
                </div>
                <div>
                    <label>是否为转载<i>*</i></label>
                    <select class="input input-select input-size50 selectDefault" name="is_reproduced"
                            defvalue="<?php echo $row['is_reproduced']?:0 ?>">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-2">
                        <label>文章转载来源</label>
                        <input class="input" type="text"  name="source_title" value="<?php echo $row['source_title']?>">
                    </div>
                    <div class="col-2">
                        <label>文章转载地址URL</label>
                        <input class="input" type="text" name="source_url" value="<?php echo $row['source_url']?>">
                    </div>
                </div>
                <div>
                    <label>所属分类<i>*</i></label>
                    <select class="input input-select input-size50 selectDefault" name="guide_class_id"
                            defvalue="<?php echo $row['guide_class_id'] ?>">
                        <?php
                        $arrOfClass = $guideModel->getListOfGuideClass(100);
                        foreach ($arrOfClass as $rowOfClass) {
                            echo "1111";
                            echo '<option value="' . $rowOfClass['id'] . '">' . $rowOfClass['title'] . '</option>';

                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label>封面图片: 640X400</label>
                    <input class="input input-size50" type="hidden" name="cover" id="cover"
                           value="<?php echo $row['cover'] ?>">
                    <p><img id="imgOfUpload" src="<?php echo $row['cover'] ?>"
                            style="width: 100px; height: auto;"></p>
                    <input type="file" name="imgFile" id="imgFile"/><input type="button" value="上传" id="uploadImg">
                    <p>资源上传地址配置：<?php
                        if(explode("/",$_SESSION["ueditor_upload_location"])[1]==$guide_id){
                            echo '<span style="color:green">正常</span> ('.$guide_id.')';
                        }else{
                            echo '<span style="color:#f00">异常<br>'.$_SESSION["ueditor_upload_location"]."<br>".$guide_id."</span>";
                        }
                        ?>
                    </p>
                </div>
                <div>
                    <label>正文</label>
                </div>
            </section>
            <script id='container' name='content' type='text/plain'><?php echo $row['content'] ?></script>
            <p>&nbsp;</p>
            <section class="formBox">
                <div>
                    <label>简介</label>
                    <textarea class="input input-textarea"
                              name="introduction"><?php echo $row['introduction'] ?></textarea>
                </div>
                <div>
                    <label>作者ID</label>
                    <input class="input input-size30" type="text" name="userID" value="<?php echo $row['user_id']?$row['user_id']:$currentUser->userId ?>"> 当前用户<?php echo ": {$currentUser->aliasName} (ID: {$currentUser->userId})" ?>
                    <p>约克头条（2187），GPA+ （2278）</p>
                    <p></p>
                </div>
                <div>
                    <label>总排序顺序 (设置0取消置顶,值越大,排序越靠前))</label>
                    <input class="input input-size30" type="text" name="guide_order"
                           value="<?php echo $row['guide_order'] ?>">
                </div>
                <div>
                    <label>分类内顺序 (设置0取消置顶,值越大,排序越靠前))</label>
                    <input class="input input-size30" type="text" name="guide_class_order"
                           value="<?php echo $row['guide_class_order'] ?>">
                </div>
            </section>


            <footer class="buttonBox">
                <input type="submit" value="完成" class="btn">
            </footer>
        </form>
    </div>
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
        1. width-10 ... 100 表格宽度
        2. center 表格内的文字居中
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

<!-- 配置文件 -->
<script type="text/javascript" src="/admin/resource/tools/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/admin/resource/tools/ueditor/ueditor.all.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('container');
</script>
