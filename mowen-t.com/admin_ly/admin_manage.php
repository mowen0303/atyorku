<?  include_once("./_head_BF.php"); ?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
  <!--创建作品基本信息 S -->
    <div class="fmBoxTit">管理后台管理员</div>
    <div class="fm2 imgListFm fmBoxCon">
    <!-- s -->
    <?
    	if(!empty($_GET['delete']) && $db->authority("2"))
		{
			$name = $_GET['delete'];
			$db->delete("admin","name='$name'");
			$db->echoMsg("删除成功!","act0","0");	
		}
	?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titleList">
      <thead>
        <tr>
          <td width="20%">管理员ID</td>
          <td width="20%">级别</td>   
          <td width="20%">所具有权限</td>        
          <td>操作</td>
        </tr>
      </thead>
      <tbody>
      <form method="post">
      <?
	  	$query = $db->select("admin,adminauthority","admin.*,adminauthority.name AS aname","admin.authority = adminauthority.id");
		if($db->num_rows($query) == 0)
		{
			echo "<tr><td colspan='4'>没有设置其他管理员<td></tr>";
		}
		else
		{
		while($row = $db->fetch_array($query))
		{
	  ?>
        <tr>
          <td><? echo $row['name']?></td>
          <td><? echo $row['aname']?></td>       
          <td>
		  <?
          switch($row['authority'])
		  {
			case 1:
				echo "超级权限";
				break;
			case 2:
				echo "查看、删除、修改";
				break;
			case 3:
				echo "查看、修改";
				break;
			case 4:
				echo "查看";
				break;
			default:
				echo "无以上权限,还未定义";
		  }
		  ?>
          </td>   
          <td>
          <? if($db->authority("3"))
		  {
		  ?>
          <a href="admin_update.php?name=<? echo $row['name']?>" class="czIcon"><img src="img/edit_icon.png" width="16" height="16"></a>
		  <? 
		  }
		  ?>
		  <? 
		  if($db->authority("2"))
		  {
		  ?>
          <a  onclick="return confirm('你确认要删除吗？');" href="admin_manage.php?delete=<? echo $row['name']?>" class="czIcon"><img src="img/delete_icon.png" width="16" height="16"></a>
		  <? 
		  }
		  ?></td>
        </tr>
      <?
		}
		}
	  ?>
      </form>
      </tbody>
    </table>
    <!-- e --> 
    </div>
  <!--创建作品基本信息 E -->
  <?  include_once("./_bottom.php"); ?>
