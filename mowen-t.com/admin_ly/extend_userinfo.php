<? include_once("./_head_BF.php"); ?>
<? include_once("./_head.php");?>
<? include_once("./_main.php"); ?>
  <div class="fmBoxTit">会员信息</div>
  <div class="fmBoxCon"> 
  <?

/*删除草稿箱list*/

if(!empty($_GET['delete']) && $db->authority('2'))
{
	$id = $_GET['delete'];                    //要删除的id
	$db->delete("extend_userinfo","id = '$id'"); //删除imgcontent中匹配的内容
	$db->echoMsg("删除成功","act0","0");          //刷新当前页面
}
?>
    <!--草稿箱文件列表 s -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titleList">
      <thead>
        <tr>
        	<td width="10%">编号</td>
          <td width="20%">昵称</td>
          <td width="10%">性别</td>          
          <td width="15%">职业</td>
          <td width="20%">电话</td>
          <td>操作</td>
        </tr>
      </thead>
      <tbody>
      <form method="post">
      <?
	  
	  	$query = $db->select("extend_userinfo","*");
		if($db->num_rows($query) == 0)
		{
			echo "<tr><td colspan='6'>本栏目暂无信息<td></tr>";
		}
		else
		{
		while($row = $db->fetch_array($query))
		{
	  ?>
        <tr>
          <td><? echo $row['id']?></td>
          <td><a href="extend_userinfo_peson.php?id=<? echo $row['id']?>" target="_blank"><? echo $row['nickname']?></a></td>
          <td><? echo $row['gender']?></td>          
          <td><? echo $row['work']?></td>
          <td><? echo $row['phone']?></td>
          <td>
          <? if($db->authority("4"))
		  {
		  ?>
          <a href="../register.php?id=<? echo $row['id']?>" target="_blank">[修改]</a>&nbsp;&nbsp;&nbsp;&nbsp;
		  <? 
		  }
		  ?>
          <? if($db->authority("4"))
		  {
		  ?>
          <a href="extend_userinfo_peson.php?id=<? echo $row['id']?>" target="_blank">[查看详情]</a>&nbsp;&nbsp;&nbsp;&nbsp;
		  <? 
		  }
		  ?>
          <? if($db->authority("3"))
		  {
		  ?>
          <a onclick="return confirm('你确认要删除吗？');" href="extend_userinfo.php?delete=<? echo $row['id']?>">[删除]</a>
		  <? 
		  }
		  ?>
          </td>
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
