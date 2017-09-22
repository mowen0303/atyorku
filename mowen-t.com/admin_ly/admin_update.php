<?  include_once("./_head_BF.php"); ?>
<?
	$name = $_GET['name'];
	$query = $db->select("admin","*","name='$name'");
	$row = $db->fetch_array($query);
	
?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
  <!--创建作品基本信息 S -->
  <form method="post">
    <div class="fmBoxTit">修改管理员级别&amp;密码</div>
    <div class="fm2 imgListFm fmBoxCon">
      <?
     	//点击提交按钮
		if(!empty($_POST['putBtn']))
		{
			$authority = $_POST['authority'];
			$db->update("admin","authority='$authority'","name='$name'");//添加管理员
			$db->echoMsg("管理员<span>".$name."</span>权限已修改！","act0","1","-2");
			
		}
		else
		//没有点击提交按钮
		{
	  ?>
      <dl>
        <dt>管理员 : <? echo $name?></dt>
      </dl>
      <dl>
        <dt>管理员级别</dt>
      </dl>
      <div class="radioBox">
        <input type="radio" <? echo $row['authority']==2 ? "checked":false?> name="authority" value="2" id="rd1">
        <label for="rd1" title="具有[删除]、[修改]权限">一级管理员</label>
        <input type="radio" <? echo $row['authority']==3 ? "checked":false?> name="authority" value="3" id="rd2">
        <label for="rd2" title="具有[修改]权限">二级管理员</label>
        <input type="radio" <? echo $row['authority']==4 ? "checked":false?> name="authority" value="4" id="rd3">
        <label for="rd3" title="只有查看权限">三级管理员</label>
      </div>
      <div class="fmBtn">
        <input name="putBtn" type="submit" class="resBtn" value="修改" />
      </div>
      <?
		}
	  ?>
    </div>
  </form>
  <!--创建作品基本信息 E -->
  <?  include_once("./_bottom.php"); ?>
