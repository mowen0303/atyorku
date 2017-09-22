<?
header('Content-Type: text/html; charset=utf-8');
include_once("common/upload_class.php");
$uf = new upclass;
if(!empty($_POST['filepost']))
{
	$path = $uf->fn_up('upfile');
	echo "<script type='text/javascript'>
		window.opener.document.getElementById('imgpath').value='$path';
		this.window.opener=null;
		window.close();
		</script>";
}
?>



<html>
<body style="padding:30px">
<form enctype="multipart/form-data" method="post">
  文件:
  <input type="file" name="upfile" />
  <input name="filepost" type="submit" value="上传" />
</form>
</body>
</html>
