<? include_once("./_head_BF.php"); ?>
<?

/*删除草稿箱list*/
if(!empty($_GET['delete']))
{
	$id = $_GET['delete'];                    //要删除的id
	$db->delete("newslist","id='$id'");        //删除imglist中匹配内容
	$db->delete("newscontent","l_id = '$id'"); //删除imgcontent中匹配的内容
	$db->jump($_SERVER['PHP_SELF']);          //刷新当前页面
}
if(!empty($_GET['class']))
{
	$class = $_GET['class'];             
}else
{
	$class = 0;	
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
          <td width="40%">文章名称</td>
          <td width="20%">所属分类</td>          
          <td width="20%">创建时间</td>
          <td width="10%">作者</td>
          <td width="20%">操作</td>
        </tr>
      </thead>
      <tbody>
      <form method="post">
      <?
	  	if($class==0){
			$query = $db->select("newslist,newsclass","newslist.*,newsclass.title AS c_title","newslist.c_id=newsclass.id ORDER BY newslist.time DESC");
		}else
		{
			$query = $db->select("newslist,newsclass","newslist.*,newsclass.title AS c_title","newslist.c_id=newsclass.id AND newslist.c_id=$class ORDER BY newslist.time DESC");
		}	  	
		if($db->num_rows($query) == 0)
		{
			echo "<tr><td colspan='5'>本栏目暂无信息<td></tr>";
		}
		else
		{
		while($row = $db->fetch_array($query))
		{
	  ?>
        <tr>
          <td><a href="/news.php?l_id=<? echo $row['id']?>" target="_blank"><? echo $row['title']?></a></td>
          <td><? echo $row['c_title']?></td>          
          <td><? echo $row['time']?></td>
          <td><? echo $row['author']?></td>
          <td><a href="news_add.php?l_id=<? echo $row['id']?>" class="czIcon"><img src="img/edit_icon.png" width="16" height="16"></a><a  onclick="return confirm('你确认要删除吗？');" href="news_manage.php?delete=<? echo $row['id']?>" class="czIcon"><img src="img/delete_icon.png" width="16" height="16"></a></td>
        </tr>
      <?
		}
		}
	  ?>
      </form>
      </tbody>
    </table>
    <!--草稿箱文件列表 e --> 
  </div>
  <? include_once("./_bottom.php"); ?>
