<?

include_once("./global.php");

ini_set('display_errors', '1');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>莫问视觉</title>
<link href="css/all.css" rel="stylesheet" type="text/css">
<link href="css/vision.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="js/all.js"></script>
<script type="text/javascript" src="js/vision.js"></script>
<script type="text/javascript" src="js/imgshow.js"></script>
<!--app-->
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript">
$(function() {
	<?
	//查询img_list
  	$query = $db->select("imglist","id","imglist.status='1' order by id DESC");
	$query_num = $db->num_rows($query);
	while($row = $db->fetch_array($query))
	{
		$l_id_s[]=$row['id'];  	
	}	
	foreach ($l_id_s as $v)
	{
	?>
		$('#vision_list_<? echo $v ?> a').lightBox();
	<?
	}
	?>
	
	
});
</script>
</head>

<body>
<div id="container">
  <div id="say"></div>
  <!--loading s-->
  <div id="bodyloading" class="loading"></div>
  <!--loading e--> 
  <!--bg s-->
  <div id="bgimg_box">
    <div id="bgimg_line"></div>
  </div>
  <!--bg e-->
  <div class="ui_lefttop"> </div>
  <!--menu s-->
  <div id="menuBg"></div>
  <div id="menuBox">
    <div id="menucur">首页</div>
    <div id="m1" class="menu m1">首页</div>
    <div id="m2" class="menu m2 hover">摄影</div>
    <div id="m3" class="menu m3">商业修片</div>
    <div id="m4" class="menu m4">设计</div>
    <div id="m5" class="menu m5">关于</div>
  </div>
  <!--menu e-->
  <div class="secondCon" id="vision">
    <div id="secondTit">
      <div></div>
    </div>
    <div class="imgBoxHdOut">
      <div id="imgleft" class="imglr">
        <div></div>
      </div>
      <div id="imgright" class="imglr">
        <div></div>
      </div>
      <div class="imgBoxHDBox"> 
        
        <!--dh s-->
        <div id="imgBoxHD">
        <?
		foreach ($l_id_s as $v)
		{
		?>
          <!--imgBox s-->
          <div class="imgBoxBg">
            <div class="imgBox" id="vision_list_<? echo $v?>">
            <?
			$a = true;
            $query_a = $db->select("imgcontent,imglist","*","l_id = $v and imglist.id = imgcontent.l_id and imgcontent.id <> imglist.faceid and imgcontent.id <> indexid");
            while($row_a = $db->fetch_array($query_a))
            { 
				if($a==true)
				{
					echo "<a href='".$row_a['path']."'></a>";
					$a = false;	
				}else
				{
					echo "<a href='".$row_a['path']."' style='display:none'></a>";
				}
				
			}    
			?>                
                <div class="imghover"></div>
                <div class="imghoverIcon"></div>
				<?
				$query_img = $db->select("imgcontent,imglist","*","l_id = $v and imglist.id = imgcontent.l_id and imgcontent.id = imglist.faceid");
				$row_img = $db->fetch_array($query_img);
				$img = GetImageSize($root.$row_img['path_sacl']);
				if($img[1]<$img[0]){
					echo "<img src='".$row_img['path_sacl']."' heihgt='100%'>";
				}else{
					echo "<img src='".$row_img['path_sacl']."' width='100%'>";
				}
            
            ?>
            </div>
          </div>
          <!--imgBox e-->
          <?
		}
		?>
        </div>
        <!--dh e--> 
        
      </div>
    </div>
    <!--scrollbar s-->
    <div id="scrollbarBox">
      <div id="scrollbar">
        <div></div>
      </div>
    </div>
    <!--scrollbar e--> 
  </div>
</div>
</div>
</div>
</body>
</html>
