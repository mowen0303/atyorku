<?
include_once("./global.php");
ini_set('display_errors', '1');
if(!empty($_GET['class']))
{
	$class = $_GET['class'];
}else
{
	$class = 4 ;
}
?>
<!doctype html>
<html>
<head>
<!--[if IE 6]>
<meta http-equiv="refresh" content="0;url=killie6.html">
<![endif]-->
<meta charset="utf-8">
<title>莫问TIME - 关于</title>
<link href="css/all.css" rel="stylesheet" type="text/css">
<link href="css/about.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/all.js"></script>
<script type="text/javascript" src="js/about.js"></script>
<script type="text/javascript" src="js/timeshow.js"></script>
</head>

<body>
<div id="container"> 
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
    <div id="m2" class="menu m2">摄影</div>
    <div id="m3" class="menu m3">Web/IOS</div>
    <div id="m4" class="menu m4">设计</div>
    <div id="m5" class="menu m5 hover">关于</div>
  </div>
  <!--menu e-->
  <div id="list">
    <div class="listmenu">
      <?
        $query_class = $db->select("newsclass","*","id='4'");	
		while($row_class = $db->fetch_array($query_class))
		{
    ?>
      <div><a href="about.php?class=<? echo $row_class['id'] ?>" class="<? if($row_class['id'] == $class ){echo "hover";} ?>"><? echo $row_class['title']?></a></div>
      <?
    }	
	?>
      <div><a href="http://www.weibo.com/jiyu163/" target="_blank">微博</a></div>
      <div><a href="http://qing.weibo.com/jiyu163" target="_blank">博客</a></div>
    </div>
    <div></div>
  </div>
  <div id="tapsBox">
  	<div class="taps1 hover"></div>
    <div class="taps2"></div>
  </div>
  <!--#aboutBox s-->
  <div id="aboutBox">
    <div id="mainLine_HD">
      <?
		if($class==4)
		{
			$query = $db->select("newslist,newscontent","*","newslist.id=newscontent.l_id AND newslist.c_id = $class order by time");
		}else
		{
			$query = $db->select("newslist,newscontent","*","newslist.id=newscontent.l_id AND newslist.c_id = $class order by time DESC");	
		}
        
		$yaer_next = false;
		$i = 0 ;
    ?>
      <?
    	while($row = $db->fetch_array($query))
		{
			$i++;
			$i%2==1?$a=1:$a=2;
			$year = date("Y",strtotime($row['time']));
			$age =  $year - 1988;
			
			if($yaer_next == 0 || $year != $year_next){
				$yaer_next = true;
				$year_next = $year;			
				echo "<div class='tlist start'>".$year."<br><span style='display:none'>".($year-1987)."岁</span></div>";				
			}	
	?>
      <!--#msgBox_l s-->
      <div class="tlist msgBox <? if($a==1){echo "msgBox_l";}else{echo "msgBox_r";} ?>">
        <div class="msgIn">
          <?
        	if($row['imgpath'] != "")
			{
				echo "<div class='top'><img src='$row[imgpath]'/></div>";
			}
		?>
          <div class="mid"><? echo $row['content']?></div>
          <div class="bot"><? echo date("n月",strtotime($row['time'])) ?></div>
        </div>
      </div>
      <!--#msgBox_l e-->
      <?
		}
	?>
    </div>
    <!--scrollbar s-->
    <div id="scrollbarBox">
      <div id="scrollbar">
        <div></div>
      </div>
    </div>
    <!--scrollbar e--> 
  </div>
  <!--#aboutBox e--> 
</div>
</div>
</div>
<div style="display:none"><script type="text/javascript" src="/js/stat.js"></script></div>
</body>
</html>
