<?  include_once("./_head_BF.php"); ?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
  <!--创建作品基本信息 S -->
  <form method="post">
    <div class="fmBoxTit">修改登陆密码</div>
    <div class="fm2 imgListFm fmBoxCon">
      <?
     	//点击提交按钮
		if(!empty($_POST['putBtn']))
		{
			if(strlen($_POST['new_pw'])>=4)
			{
				$old_pw  = md5($_POST['old_pw']);  //原密码
				$new_pw  = md5($_POST['new_pw']);  //新密码
				$is_pw   = md5($_POST['is_pw']);   //原确认新密码
				$name    = $_SESSION['uname'];//当前登陆用户的用户名
				$query_pw = $db->select("admin","*","name='$name'"); //查询用户名所对应的密码
				$row = $db->fetch_array($query_pw);
				
				if($old_pw == $row['pw'])
				{
					if($new_pw == $is_pw)
					{					  
						$db->update("admin","pw='$new_pw'","name='$name'");
						$db->echoMsg("密码修改成功","act0");
						  
					}else
					{
						$db->echoMsg("两次输入的新密码不一样","act3");
					}				
				}else
				{
					$db->echoMsg("原密码不正确","act3");
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
      <? $db->echoMsg(); //错误信息提示 ?>
      <dl>
        <dt>原密码</dt>
        <dd>
          <input type="password" class="w1" name="old_pw" value="" />
        </dd>
      </dl>
      <dl>
        <dt>新密码</dt>
        <dd>
          <input type="password" class="w1" name="new_pw" value="" />
        </dd>
      </dl>
      <dl>
        <dt>确认新密码</dt>
        <dd>
          <input type="password" class="w1" name="is_pw" value="" />
        </dd>
      </dl>
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
