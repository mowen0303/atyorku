<?  include_once("./_head_BF.php"); ?>
<?
	$l_id = $_GET['l_id'];
	
	/*删除单张作品*/
	if(!empty($_GET['delete']))
	{
		$id = $_GET['delete'];                        //要删除的id
		$query_de = $db->select("imgcontent","*","id='$id'");  // 查询要删除id的路径信息
		$row_de = $db->fetch_array($query_de);        
		$rootpath = $_SERVER['DOCUMENT_ROOT'];        //获取根目录
		$depath_sacl =$rootpath.$row_de['path_sacl']; //缩略图路径
		$depath =$rootpath.$row_de['path'];           //100%图路径
		//删除缩略图
		if(file_exists($depath_sacl)){
		   unlink($depath_sacl);	
		 }
		 //删除原图
		 if(file_exists($depath)){
		   unlink($depath);		
		 }		 
		//删除数据库信息
		$db->delete("imgcontent","id = '$id'"); 		
		//刷新当前页面
		$db->jump($_SERVER['PHP_SELF']."?l_id=$l_id");          
	}
	
	$query = $db->select("imgcontent","*","l_id=$l_id ORDER BY myorder DESC");
	
?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
  <!--添加详细信息 S -->
  <form method="post">
    <div class="fmBoxTit">为作品添加描述</div>
    <div class="fm2 imgListFm fmBoxCon">
      <?

//保存并发布
if(!empty($_POST['imglistEnd']))
{
	//unset($_POST['imglistEnd']);
	//$faceid = $_POST['faceid'];
	//echo $faceid;
	//$db->update("imglist","status='1',faceid='$faceid'","id=$l_id");
	//$db->update("imgcontent","text='$text'","id=$id");
	//print_r($_POST['txt']);
	
	//更新照片注释和排序
	$id = $_POST['id'];
	$txt = $_POST['txt'];
	$myorder = $_POST['myorder'];
	$num = count($txt);
	
	for($i=0;$i<$num;$i++){
		$db->update("imgcontent","text='$txt[$i]',myorder='$myorder[$i]'","id=$id[$i]");
	}
	//设置封面
	if(!empty($_POST['faceid']))
	{
		$faceid = $_POST['faceid'];
	}else
	{
		$faceid = $_POST['id'][0];
	}
	//设置首页
	if(!empty($_POST['indexid']))
	{
		$indexid = $_POST['indexid'];
	}else
	{
		$indexid = $_POST['id'][0];
	}
	//更新imglsit中照片封面和首页id
	$db->update("imglist","status='1',faceid='$faceid',indexid='$indexid'","id=$l_id");
		
?>
      <div>上传成功</div>
      <div><a href="/opus_show.php?l_id=<? echo $l_id?>" target="_blank">查看刚发布的作品</a>| <a href="/index.php" target="_blank">进入首页</a></div>
      <?
} 
else
{	
?>
      <div class="imgConBox">
        <div class="endBtnBox"><a href="opus_upload.php?l_id=<? echo $l_id?>" class="resBtn2">添加作品</a><a href="opus_add.php?l_id=<? echo $l_id?>" class="resBtn2">修改作品信息</a></div>
        <?	
	//imglist被设为封面和首页显示的id
	$query_imglist = $db->select("imglist","*","id='$l_id'");	
	$row_imglist = $db->fetch_array($query_imglist);
			
	while($row=$db->fetch_array($query))
	{	
	//$text = "text".$row['id'];
	//$img_arr[$row['id']]=$row['text'];
?>
        <!--imgBox s -->
        <div class="imgBox">
          <div class="imgBoxBtn">
           <span><label for="phtot<? echo $row['id'] ?>">封面</label>
           <input
		   <?
		   	if($row['id']==$row_imglist['faceid'])
			 	{
				 echo "checked";
			 	}
		   ?> 
           type="radio" name="faceid" value="<? echo $row['id'] ?>" id="phtot<? echo $row['id'] ?>"></span>
           <span><label for="index<? echo $row['id'] ?>">首页</label><input 
           <?
		   	if($row['id']==$row_imglist['indexid'])
			 	{
				 echo "checked";
			 	}
		   ?>  type="radio" name="indexid" value="<? echo $row['id'] ?>" id="index<? echo $row['id'] ?>"></span>
           <a  onclick="return confirm('你确认要删除吗？');" href="opus_end.php?l_id=<? echo $l_id?>&delete=<? echo $row['id']?>" class="czIcon">[删除]</a>
          </div>
          <div class="imgCon"><img src="<? echo $row['path_sacl']?>"></div>
          <div class="imgTxt">
            <div class="imgTxtMs">照片描述：</div>
            <div>
              <input type="hidden" name="id[]" value="<? echo $row['id']?>">
              <textarea name="txt[]"><? echo $row['text']?></textarea><br>
              自定义排序：<input type="text" name="myorder[]" value="<? echo $row['myorder']?>">
            </div>
          </div>
        </div>
        <!--imgBox e -->
        <?
   	 }
  	?>
      </div>
      <div class="fmBtn">
        <input name="imglistEnd" type="submit" class="resBtn" value=" 保存并发布 " />
      </div>
    </div>
  </form>
  <!--添加详细信息 E -->
  <?
}
?>
  <?  include_once("./_bottom.php"); ?>
