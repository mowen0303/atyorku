<?  include_once("./_head_BF.php"); ?>
<?
$classTab    =   $_GET['class'];
$query        =   $db->select("$classTab","*");
$class_num  =  $db->num_rows($query);
$this_url      =  "class.php?class=$classTab";
//$this_url      =  "class.php?class=imgclass";
// 添加新分类
if(!empty($_POST['insertBtn']))
{
	$title = $_POST['title'];
	$db->insert("$classTab","title,f_id","'$title','0'");
	$db->jump($this_url);
}

// 删除分类
if(!empty($_POST['deleteBtn']))
{
	$c_id = $_POST['c_id'];
	$db->delete("$classTab","id='$c_id'");
	$db->jump($this_url);	
}

//修改分类
if(!empty($_POST['updateBtn']))
{
	$c_id = $_POST['c_id'];
	$title = $_POST['title'];
	$db->update("$classTab","title='$title'","id='$c_id'");
	$db->jump($this_url);		
}
?>
<?  include_once("./_head.php"); ?>
<?  include_once("./_main.php"); ?>
	  
      <div class="fmBoxTit">作品分类管理</div>
      <div class="fm classFm fmBoxCon">      
      <? $db->echoMsg(); //错误信息提示 ?>
    <!--显示分类 s -->
    <div class="addClass showClass">
    
    <?
		while($row = $db->fetch_array($query))
		{
	?>
    	<form method="post">
            <dl>
              <dd><input type="text" name="title" value="<? echo $row['title']?>" /><input type="hidden" name="c_id" value="<? echo $row['id']?>" /></dd>
              <dt><input name="updateBtn" type="submit" class="resBtn" value="修改" /><input name="deleteBtn" type="submit" class="resBtn" value="删除" /></dt>
            </dl>
        </form>
    <?    			
		}
	?>    
    </div>
    <!--显示分类 e -->
    <!--添加分类 S -->   
      <div class="addClass">
      <form method="post">
      	<dl>
          <dt>添加分类</dt>
          <dd><input type="text" name="title" /></dd>
          <dt><input name="insertBtn" type="submit" class="resBtn" value="创建新分类" /></dt>
        </dl>
        </form>
        </div>         
 	<!--添加分类 E -->
    </div> 
<?  include_once("./_bottom.php"); ?>

