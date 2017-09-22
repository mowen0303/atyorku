<?  include_once("./_head_BF.php"); ?>
<?
$title  = "";                 //作品标题	
$time   = date('Y-m-d');      //作品时间
$author = $_SESSION['uname']; //作品作者
$news_con = false;            //初始化文章内容

//如果是修改文章，则重新定义以下变量
if(!empty($_GET['l_id']))
{
	$l_id           = $_GET['l_id'];
	$action       = "news_add.php?l_id=$l_id";
	$query_list  = $db->select("newslist","*","id=$l_id");
	$btn           = "";
	$row           = $db->fetch_array($query_list);
	$title           = $row['title'];                 //作品标题	
	$time          = $row['time'];      //作品时间
	$author      = $row['author']; //作品作者
	$imgpath      = $row['imgpath']; //作品作者
	$query_content = $db->select("newscontent","*","l_id=$l_id");
	$row_con    = $db->fetch_array($query_content);
	$news_con   = $row_con['content'];

}else
//如果不是修改文章
{
	$action   = "news_add.php";

}
?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
  <!--创建作品基本信息 S -->
  <form method="post" action="<? echo $action?>">
    <div class="fmBoxTit"><? echo !empty($_GET['l_id'])==true?"编辑文章":"发表新文章"?></div>
    <div class="fm2 imgListFm fmBoxCon">
      <?
     	//点击提交按钮
		if(!empty($_POST['putBtn']))
		{
			$c_id    = $_POST['news_class'];  //所属分类
			$title   = $_POST['news_title'];	//文章标题
			$time    = $_POST['news_time'];   //发布时间
			$author  = $_POST['news_author']; //文章作者
			$imgpath = $_POST['news_img'];    //缩略图路径
			$con     = $_POST['news_con'];    //文章内容
			
			//修改文章
			if(!empty($_GET['l_id']))
			{
				$db->update("newslist","c_id='$c_id',title='$title',time='$time',author='$author',imgpath='$imgpath'","id=$l_id");
				$db->update("newscontent","content='$con'","l_id=$l_id");	
				echo "修改成功";
			}
			else
			//添加新闻文章
			{
				$db->insert("newslist","id,c_id,title,time,author,imgpath","NULL,'$c_id','$title','$time','$author','$imgpath'"); //将，分类、标题、时间 插入表newslist
				$l_id  = $db->insert_id();      //获得插入的id
				$db->insert("newscontent","l_id,content","'$l_id','$con'");
				echo "发布成功 <a href='/news.php?l_id=".$l_id."' target='_blank'>查看刚发布文章</a>";
			}
		}
		else
		//没有点击提交按钮
		{
	 ?>
      <? $db->echoMsg(); //错误信息提示 ?>
      <dl>
        <dt>文章标题</dt>
        <dd>
          <input type="text" name="news_title" value="<? echo $title?>" />
        </dd>
      </dl>
      <dl>
        <dt>所属分类</dt>
        <dd>
          <?		  			  	
			$query = $db->select("newsclass","*");	//查询作品分类表		
			if($db->num_rows($query) == 0)          //获得查询结果条数，如果是0条
			{
				echo "<a href='class.php?class=newsclass'>添加分类</a>";    //显示【添加分类】链接
			}else                                   //如有查询结果，则显示列表
			{
			  echo "<select name='news_class'>";

				while($row = $db->fetch_array($query))    //将查询结果fetch_array
				{
              		echo "<option value='".$row['id']."'";   
					if(!empty($l_id))                      //如果地址栏有l_id参数
					{
						$query_lid = $db->select("newslist","*","id='$l_id'"); //查询l_id的分类id :c_id
						$row_lid = $db->fetch_array($query_lid);

						if($row['id'] == $row_lid['c_id'])
						{							
							echo " selected='selected' ";
						}
					}					
					echo ">".$row['title']."</option>";
				}
               echo "</select> <a href='class.php?class=newsclass'>添加新分类</a>";
			}
          ?>
        </dd>
      </dl>
      <dl>
        <dt>发布时间</dt>
        <dd>
          <input type="text" class="w1" name="news_time" value="<? echo $time?>" />
        </dd>
      </dl>
      <dl>
        <dt>文章作者</dt>
        <dd>
          <input type="text" class="w1" name="news_author" value="<? echo $author?>" />
        </dd>
      </dl>
      <dl>
        <dt>缩略图</dt>
        <dd>
        <script type="text/javascript">
			function op(){				
				window.open('upfile.php','MyName','width=500,height=100,top=300,left=500');	
				//alert("1");
			}
		</script>
          <input type="text" id="imgpath" class="w1" name="news_img" value="<? echo $imgpath?>" /><a id="upfileid" onClick="op()">上传</a>
        </dd>
      </dl>
      <dl>
        <dt>文章内容</dt>
      </dl>
      <div>
        <? $ed->editor("news_con",$news_con)?>
      </div>
      <div class="fmBtn">
        <input name="putBtn" type="submit" class="resBtn" value="发表" />
      </div>
      <?
		}
		?>
    </div>
  </form>
  <!--创建作品基本信息 E -->
  <?  include_once("./_bottom.php"); ?>
