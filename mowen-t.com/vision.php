<?
include_once("./global.php");
ini_set('display_errors', '1');
?>
<!doctype html>
<html>
<head>
<!--[if IE 6]>
<meta http-equiv="refresh" content="0;url=killie6.html">
<![endif]-->
<meta charset="utf-8">
<title>莫问TIME - 摄影</title>
<link href="css/all.css" rel="stylesheet" type="text/css">
<link href="css/vision.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/all.js"></script>
<script type="text/javascript" src="js/vision.js"></script>
<script type="text/javascript" src="js/imgshow.js"></script>
<!--app-->
<script type="text/javascript" src="js/fancybox.js"></script>
<script type="text/javascript">
$(function() {
<?
//查询img_list
$query = $db->select("imglist","id","status='1' AND c_id='1' order by imglist.time DESC,imglist.id DESC");
$query_num = $db->num_rows($query);
while($row = $db->fetch_array($query))
{
	$l_id_s[]=$row['id'];  	
}	
foreach ($l_id_s as $v)
{
?>
$('.fancybox_<? echo $v ?>').fancybox({openEffect:'none',closeEffect:'none',closeBtn:false,helpers:{title:{type:'inside'},buttons:{}},afterLoad : function() {this.title = (this.index + 1) + ' / ' + this.group.length + (this.title ? ' - ' + this.title : '');}});
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
  <div id="bodyloading" class="loading"><a id="loadingtxt" href=""  onclick="window.location.reload()">如页面长时间无响应请点此手动刷新</a></div>
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
    <div id="m3" class="menu m3">Web/IOS</div>
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
			$query_a = $db->select("imgcontent AS c,imglist AS l","*,l.faceid = c.id AS face","l.id = c.l_id AND c.l_id = $v ORDER BY face DESC ,myorder DESC");
			$query_a_unm = $db->num_rows($query_a);
            while($row_a = $db->fetch_array($query_a))
            { 
				if($a==true)
				{
					echo "<a class='fancybox_".$v."' data-fancybox-group='button' href='".$row_a['path']."' title='".$row_a['text']."'></a>";
					$img = GetImageSize($root.$row_a['path']);
					if($img[1]<$img[0]){
						echo "<img src='".$row_a['path']."' height='100%'>";
					}else{
						echo "<img src='".$row_a['path']."' width='100%' style='margin-top:-30px'>";
					}
					$query_a_title = $row_a['title'];
					$query_a_author = $row_a['author'];
					$a = false;	
				}else
				{
					echo "<a class='fancybox_".$v."' data-fancybox-group='button' href='".$row_a['path']."' title='".$row_a['text']."' style='display:none'></a>";
				}
				

				
			}    
			?>                
                <div class="imghover"></div>
                <div class="imghoverIcon"></div>
                <div class="imgtxt">
                	<div class="top"><? echo $query_a_title ?><span class="s2"><? echo $query_a_unm ?></span></div>
                    <div class="bot"><span class="s1"><? echo $query_a_author ?></span></div>
                </div>

            </div>
          </div>
          <!--imgBox e-->
          <?
		}
		?>
        </div>
        <!--dh e--> 
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
</div>
<div style="display:none"><script type="text/javascript" src="/js/stat.js"></script></div>
</body>
</html>
