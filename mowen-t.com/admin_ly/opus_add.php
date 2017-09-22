<?  include_once("./_head_BF.php"); ?>
<?
$title  = "";                 //作品标题	
$time   = date('Y-m-d');      //作品时间
$author = $_SESSION['uname']; //作品作者

if(!empty($_GET['l_id']))
{
	$l_id       = $_GET['l_id'];
	$btnTxt     = "修改";
	$action     = "opus_upload.php?l_id=$l_id";
	$query_list = $db->select("imglist","*","id=$l_id");
	$row        = $db->fetch_array($query_list);
	$title  = $row['title'];                 //作品标题	
	$time   = $row['time'];      //作品时间
	$author = $row['author']; //作品作者
}else
{
	$btnTxt   = "创建并上传作品";
	$action   = "opus_upload.php";
}
?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
<div><? $db->draft_num()?></div>
	 <!--创建作品基本信息 S -->
    <form method="post" action="<? echo $action?>">
      <div class="fmBoxTit">创建作品基本信息</div>
      <div class="fm2 imgListFm fmBoxCon">      
      <? $db->echoMsg(); //错误信息提示 ?>
      <dl>
          <dt>作品名称</dt>
          <dd>
            <input type="text" name="img_title" value="<? echo $title?>" />
          </dd>
        </dl>
      <dl>
          <dt>作品分类</dt>
          <dd>
          <?		  			  	
			$query = $db->select("imgclass","*");	//查询作品分类表		
			if($db->num_rows($query) == 0)          //获得查询结果条数，如果是0条
			{
				echo "<a href='class.php?class=imgclass'>添加分类</a>";    //显示【添加分类】链接
			}else                                   //如有查询结果，则显示列表
			{
			  echo "<select name='img_class'>";

				while($row = $db->fetch_array($query))    //将查询结果fetch_array
				{
              		echo "<option value='".$row['id']."'";   
					if(!empty($l_id))                      //如果地址栏有l_id参数
					{
						$query_lid = $db->select("imglist","c_id","id='$l_id'"); //查询l_id的分类id :c_id
						$row_lid = $db->fetch_array($query_lid);

						if($row['id'] == $row_lid['c_id'])
						{							
							echo " selected ";
						}else
						{
							echo $l_id;
						}
					}					
					echo ">".$row['title']."</option>";
				}
               echo "</select> <a href='class.php?class=imgclass'>添加新分类</a>";
			}
          ?>            
          </dd>
        </dl>
        <dl>
          <dt>创作时间</dt>
          <dd>
            <input type="text" class="w1" name="img_time" value="<? echo $time?>" />
          </dd>
        </dl>
        <dl>
          <dt>作品作者</dt>
          <dd>
            <input type="text" class="w1" name="img_author" value="<? echo $author?>" />
          </dd>
        </dl>
        
        <div class="fmBtn">
          <input name="imglistEnter" type="submit" class="resBtn" value="<? echo $btnTxt?>" />
        </div>
      </div>
    </form>
 	<!--创建作品基本信息 E -->
<?  include_once("./_bottom.php"); ?>

