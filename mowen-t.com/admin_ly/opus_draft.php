<? include_once("./_head_BF.php"); ?>
<?
//0草稿箱，1管理已经发布
$manage  = $_GET['type'];
/*删除草稿箱list*/
if(!empty($_GET['delete']))
{
	$id = $_GET['delete'];                    //要删除的id
	$db->delete("imglist","id='$id'");        //删除imglist中匹配内容
	$db->delete("imgcontent","l_id = '$id'"); //删除imgcontent中匹配的内容
	$db->jump($_SERVER['PHP_SELF']);          //刷新当前页面
}
?>
<? include_once("./_head.php"); ?>
<? include_once("./_main.php"); ?>
  <div class="fmBoxTit">创建作品基本信息</div>
  <div class="fmBoxCon"> 
    <!--草稿箱文件列表 s -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titleList">
      <thead>
        <tr>
          <td width="40%">作品名称</td>
          <td width="20%">所属分类</td>          
          <td width="20%">创建时间</td>
          <td width="10%">作者</td>
          <td width="20%">操作</td>
        </tr>
      </thead>
      <tbody>
      <form method="post">
      <?
	  	$query = $db->select("imglist,imgclass","imglist.*,imgclass.title AS c_title","imglist.c_id=imgclass.id AND status = $manage");
		while($row = $db->fetch_array($query))
		{
	  ?>
        <tr>
          <td><? echo $row['title']?></td>
          <td><? echo $row['c_title']?></td>          
          <td><? echo $row['time']?></td>
          <td><? echo $row['author']?></td>
          <td><a href="#" class="czIcon"><img src="img/edit_icon.png" width="16" height="16"></a><a  onclick="return confirm('你确认要删除吗？');" href="opus_draft.php?delete=<? echo $row['id']?>" class="czIcon"><img src="img/delete_icon.png" width="16" height="16"></a></td>
        </tr>
      <?
		}
	  ?>
      </form>
      </tbody>
    </table>
    <!--草稿箱文件列表 e --> 
  </div>
  <? include_once("./_bottom.php"); ?>
