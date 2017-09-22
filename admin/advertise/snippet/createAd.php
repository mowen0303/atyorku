<?php
$adModel = new \admin\advertise\adModel();
$userModel = new \admin\user\UserModel();
$aid = BasicTool::get('aid');
$flag = $aid == null ? 'add' : 'update';
$row = $flag=='add' ? null : $adModel->getListByAdId($aid);
?>
<link rel="stylesheet" href="../../resource/css/HHuploadify.css">
<script src="../../resource/js/HHuploadify.js"></script>
<script src="https://jonthornton.github.io/jquery-timepicker/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="https://jonthornton.github.io/jquery-timepicker/jquery.timepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.standalone.css" />
<script src="dist/datepair.js"></script>
<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'发布新广告':'修改广告信息';
        ?></h1>
</header>

<?php if ($aid != null)
    $arr = $adModel->getListByAdId($aid);
?>

<article class="mainBox" data-gr-c-s-loaded="true">
    <form action="adController.php?action=addAd&aid=<?php echo $aid?>" method="post">
        <section class="formBox">
            <input name="flag" value="<?php echo $flag ?>" type="hidden">
            <input name="user_id" value="<?php echo $user_id ?>" type="hidden">
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $arr["title"]; ?>">
                <label>内容</label>
            <textarea name="content" id="content" rows="10" cols="80"><?php echo $arr['content'];?></textarea>
                <div>
                    <label>图片</label>

                        <a href="#" id="urlcommit" class="button big" <?php echo $aid==null ? '' : "style='display: none'" ?>>输入URL</a>

                    <a href="#" id = "uploadifyimg" class="button big">手工上传</a>
                    <input class="input" type="text" id= "boxuploadurl" <?php echo $aid==null ? "style='display: none'" : "" ?> name="img" value="<?php echo $arr['img']?>">
                    <div id="upload" style="display: none"></div>

                <script>
                document.getElementById("urlcommit").onclick = function(){
                    document.getElementById("urlcommit").style = "display: none";
                    document.getElementById("boxuploadurl").style="";
                    document.getElementById("uploadifyimg").style = "display: none";
                }
                document.getElementById("uploadifyimg").onclick = function(){
                    document.getElementById("urlcommit").style = "display: none";
                    document.getElementById("uploadifyimg").style = "display: none";
                    document.getElementById("boxuploadurl").style = "display: none";
                    document.getElementById("upload").style = "";

                }

                </script>
                    <script>
                        $('#upload').HHuploadify({
                            auto:true,
                            fileTypeExts:'*.jpg;*.png;*.gif',
                            multi:true,
                            formData:{key:123456,key2:'vvvv'},
                            fileSizeLimit:1024,
                            uploader:'upload.php',
                            onUploadStart:function(file){
                                console.log(file.name+'开始上传');
                            },
                            onInit:function(obj){
                                console.log('初始化');
                                console.log(obj);
                            },
                            onUploadComplete:function(file,data){
                                console.log(file.name+'上传完成');
                                alert(data);
                                var temp = JSON.parse(data);
                                var url = temp.url;
                                document.getElementById("boxuploadurl").value = ""+url;

                            },
                            onCancel:function(file){
                                console.log(file.name+'删除成功');
                                document.getElementById("boxuploadurl").value = "";
                            },
                            onClearQueue:function(queueItemCount){
                                console.log('有'+queueItemCount+'个文件被删除了');
                            },
                            onDestroy:function(){
                                console.log('destroyed!');
                            },
                            onSelect:function(file){
                                console.log(file.name+'加入上传队列');
                            },
                            onQueueComplete:function(queueData){
                                console.log('队列中的文件全部上传完成',queueData);
                            }

                        });</script>
                    <br>

                </div>

                <div id="dennisdatepickerbaseonjohnsontimepair">
                    <label>开始日期</label>
                    <input class="date start input" type="text" name="startdate" id="dateinput1"
                           value="<?php  if ($arr['startdate']!=null)
                               echo date("Y-m-d", $arr['startdate']);
                           else
                                echo date("Y-m-d", time());?>">
                    <label>到期日期</label>
                    <input class="date end input" type="text" name="datetotime" id="dateinput"
                           value="<?php  if ($arr['expiredate']!=null)
                               echo date("Y-m-d", $arr['expiredate']);
                           else
                               echo date("Y-m-d", time());?>">
                </div>
                <div>
                    <label>广告URL</label>
                    <input class="input" type="text" name="url"
                           value="<?php echo   $arr['url']; ?>">
                </div>
                <div>
                    <label>分类</label>
                    <select class="input input-select input-size50 selectDefault" name="category" required="required"  defvalue="<? echo $arr["class_id"] ?>" >
                        <option value="1">课评</option>
                        <option value="2">论坛</option>
                        <option value="3">指南</option>
                        <option value="4">其他</option>

                    </select>
                </div>

        </section>

        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
<script>
    $('#dennisdatepickerbaseonjohnsontimepair .date').datepicker({
        'format': 'yyyy-m-d',
        'autoclose': true
    });

    var dennistimepicker = document.getElementById('dennisdatepickerbaseonjohnsontimepair');
    var dateOnlyDatepair = new Datepair(dennistimepicker);
</script>
</article>
