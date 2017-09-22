<?  include_once("./_head_BF.php"); ?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
  <!--创建作品基本信息 S -->
  <form method="post">
    <div class="fmBoxTit">添加后台管理员</div>
    <div class="fm2 imgListFm fmBoxCon">
      <?
     	//点击提交按钮
		if(!empty($_POST['putBtn']))
		{
			if(strlen($_POST['new_pw'])>=4)
			{
				$new_pw    =  md5($_POST['new_pw']);                   //新密码
				$is_pw     =  md5($_POST['is_pw']);                    //原确认新密码
				$name      =  $_POST['new_name'];     				   //当前登陆用户的用户名
				$authority =  $_POST['authority'];  				   //用户权限
				$query_pw  =  $db->select("admin","*","name='$name'"); //用户数据库的原密码				
				if($db->num_rows($query_pw)>0)
				{
					$db->echoMsg("用户:<span>".$name."</span>名已存在","act3");               //用户名已存在
				}else
				{
					$db->insert("admin","name,pw,authority","'$name','$new_pw','$authority'");//添加管理员
					$db->echoMsg("新管理员<span>".$name."</span>已添加！","act0");
				}
				
			}else
			{
				$db->echoMsg("请输入4位以上密码","act3");
			}
			
		}
		else
		//没有点击提交按钮
		{
	  ?>
      <dl>
        <dt>用户名</dt>
        <dd>
          <input type="text" class="w1" name="new_name" value="" />
        </dd>
      </dl>
      <dl>
        <dt>密码</dt>
        <dd>
          <input type="password" class="w1" name="new_pw" value="" />
        </dd>
      </dl>
      <dl>
        <dt>确认密码</dt>
        <dd>
          <input type="password" class="w1" name="is_pw" value="" />
        </dd>
      </dl>
      <dl>
        <dt>管理员级别</dt>
      </dl>
      <div class="radioBox">
        <input type="radio" checked name="authority" value="2" id="rd1">
        <label for="rd1" title="具有[删除]、[修改]权限">一级管理员</label>
        <input type="radio" name="authority" value="3" id="rd2">
        <label for="rd2" title="具有[修改]权限">二级管理员</label>
        <input type="radio" name="authority" value="4" id="rd3">
        <label for="rd3" title="只有查看权限">三级管理员</label>
      </div>
      <div class="fmBtn">
        <input name="putBtn" type="submit" class="resBtn" value="添加" />
      </div>
      <?
		}
	  ?>
    </div>
  </form>
  <!--创建作品基本信息 E -->
  <?  include_once("./_bottom.php"); ?>
