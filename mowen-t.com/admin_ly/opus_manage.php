<? include_once("./_head_BF.php"); ?>
<?
//0草稿箱，1管理已经发布
if(isset($_GET['type']))
{
	$type  = $_GET['type'];
}
if(isset($_GET['opusclass']))
{
	$opusclass  = $_GET['opusclass'];
}else
{
	$opusclass = 0;
}
//删除作品以及所有文件
if(!empty($_GET['delete']))
{
	$id = $_GET['delete'];                        //要删除的id
	$rootpath = $_SERVER['DOCUMENT_ROOT'];        //获取根目录
	$query_de = $db->select("imgcontent","*","l_id='$id'");  // 查询要删除id的路径信息
	while($row_de = $db->fetch_array($query_de)){	       
		
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
		$db->delete("imgcontent","id = '$row_de[id]'"); 		
	}
	$db->delete("imglist","id = '$id'"); 			
	//刷新当前页面
	$db->jump($_SERVER['PHP_SELF']."?type=$type&opusclass=$opusclass");          
}


/*删除草稿箱list*/
if(!empty($_GET['delete']))
{
	$id = $_GET['delete'];                    //要删除的id
	$db->delete("imglist","id='$id'");        //删除imglist中匹配内容
	$db->delete("imgcontent","l_id = '$id'"); //删除imgcontent中匹配的内容
	$db->jump($_SERVER['PHP_SELF']."??type=$type&opusclass=$opusclass");          //刷新当前页面
}
?>
<? include_once("./_head.php"); ?>
<? include_once("./_main.php"); ?>
<?
	if($opusclass==0)
	{
		$query = $db->select("imglist,imgclass","imglist.*,imgclass.title AS c_title","imglist.c_id=imgclass.id AND status = $type ORDER BY imglist.time DESC,imglist.id DESC");
	}else
	{
		$query = $db->select("imglist,imgclass","imglist.*,imgclass.title AS c_title","imglist.c_id=imgclass.id AND status = $type AND imgclass.id = $opusclass ORDER BY imglist.time DESC,imglist.id DESC");
	}
?>
  <div class="fmBoxTit">作品管理 &nbsp;（<? echo $num_rows = $db->num_rows($query) ?>）</div>
  <div class="fmBoxCon">  
    <!--草稿箱文件列表 s -->
      <form method="post">
    <?
		if($num_rows > 0)
		{
		while($row = $db->fetch_array($query))
		{
			$l_id = $row['id'];
			$query_img = $db->select("imgcontent,imglist","*,imglist.faceid=imgcontent.id as face","imglist.id = imgcontent.l_id AND imgcontent.l_id=$l_id ORDER BY face DESC");
			$num_img   = $db->num_rows($query_img);
			$row_img = $db->fetch_array($query_img);
	  ?>
      <!--imgBox s -->
      <div class="imgBox maImgBox">
        <div class="imgCon">
        	<?
            	if($num_img == 0)
				{
					echo "<img src='img/zw.png'>";
				}else
				{
					echo "<a href='/opus_show.php?l_id=".$row['id']."' target='_blank'><img title='".$row['title']."' src='".$row_img['path']."'></a>";	
				}
			?>        
        </div>
        <div class="mgTit">
          <p><a class="tit" href="/opus_show.php?l_id=<? echo $row['id']?>" target="_blank"><? echo $row['title']?></a></p>
          <p><? echo $num_img?>张照片 |  <a href="opus_end.php?l_id=<? echo $row['id']?>" class="czIcon">编辑</a><a  onclick="return confirm('你确认要删除吗？');" href="opus_manage.php?type=<? echo $type?>&delete=<? echo $row['id']?>" class="czIcon">删除</a></p>
          <p>创建日期：<? echo $row['time']?></p>
        </div>
      </div>
      <!--imgBox e -->
      <?
		}
		}else
		{
			echo "本栏目暂无信息";	
		}
	  ?>
      </form>
    <!--草稿箱文件列表 e --> 
  </div>
  <? include_once("./_bottom.php"); ?>
