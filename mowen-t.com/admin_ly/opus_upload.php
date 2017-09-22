<?  include_once("./_head_BF.php"); ?>
<?
//如果是返回继续上传，则直接得到l_id
if(!empty($_GET['l_id']))
{
	$l_id = $_GET['l_id'];	
}
//添加新作品基本信息
if(!empty($_POST['imglistEnter']))
{
	$img_class  = $_POST['img_class'];   //作品-分类ID
	$img_title  = $_POST['img_title'];   //作品-标题
	$img_time   = $_POST['img_time'];    //作品-发布时间
	$img_author = $_POST['img_author'];  //作品-作者 	
	
	//判断是添加还是修改
	if(!empty($_GET['l_id']))
	{
		$db->update("imglist","title='$img_title',time='$img_time',author='$img_author',c_id='$img_class'","id='$l_id'");
		$db->jump("opus_end.php?l_id=$l_id");	
	}else
	{
		//将以上信息插入到数据库表imglist中
		$db->insert("imglist","id,title,time,author,c_id","	NULL,'$img_title','$img_time','$img_author','$img_class'");
		//获得表imglist新作品的ID
		$l_id = $db->insert_id();
	}
}
?>
<?  include_once("./_head.php"); ?>
	<link href="upload/css/upload.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="upload/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="upload/js/swfupload.queue.js"></script>
	<script type="text/javascript" src="upload/js/fileprogress.js"></script>
	<script type="text/javascript" src="upload/js/handlers.js"></script>
	<script type="text/javascript">
		var swfu;

		window.onload = function() {
			var settings = {
				flash_url : "upload/swfupload/swfupload.swf",
				upload_url: "/admin_ly/upload/upload.php?l_id=<? echo $l_id;?>",	
				post_params: {"PHPSESSID" : "<? echo session_id()?>"},
				file_size_limit : "100 MB",
				file_types : "*.jpg;*.png;*.gif",
				file_types_description : "All Files",
				file_upload_limit : 50,  //配置上传个数
				file_queue_limit : 0,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button settings
				button_image_url: "upload/images/btn.png",
				button_width: "120",
				button_height: "30",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: '<span class="theFont"><font color="#ffffff">添加作品图片</font></span>', //上传按钮文字
				button_text_style: ".theFont { font-size: 13; }",
				button_text_left_padding: 20,
				button_text_top_padding: 6,
				
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete	
			};

			swfu = new SWFUpload(settings);
	     };
	</script>
<?  include_once("./_main.php"); ?>
<form id="form1"  method="post" enctype="multipart/form-data" action="opus_end.php?l_id=<? echo $l_id ?>">
    <div class="fmBoxTit">上传作品</div>
    <div class="fm imgListFm fmBoxCon">
          <div id="content">
        <div class="fieldset flash" id="fsUploadProgress"> <span class="legend">快速上传</span> </div>
        <div id="divStatus"></div>
        <div class="btnBox"> <span id="spanButtonPlaceHolder"></span><span class="btnEsc">
          <input id="btnCancel" type="button" value="取消所有上传" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
          </span> </div>        
      </div>
      <div class="fmBtn">
          <input id="imgUpload" name="imgUpload" type="submit" class="resBtn" value=" 下一步 " />
        </div>
        </div>
  </form>
<?  include_once("./_bottom.php"); ?>