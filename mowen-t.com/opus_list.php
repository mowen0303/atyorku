<? include_once("_head.php");?>
<title>莫问 | Mowen Vision</title>
<link href="css/sed.css" rel="stylesheet" type="text/css">
<? include_once("_body.php");?>
  <? include_once("_s_menu.php");?>
  <!--container_news s -->
  <div id="container_opus">
  <?
  	$query = $db->select("imglist,imgcontent","*,imglist.id AS l_id","imglist.status='1' AND imglist.faceid = imgcontent.id order by imglist.id DESC");
	while($row = $db->fetch_array($query))
	{
		
  ?>     
    <!--opusCon s -->
    <div class="opusCon">
      <div class="opusImgBox"><a target="_blank" href="opus_show.php?l_id=<? echo $row['l_id']?>"><img src="<? echo $row['path_sacl']?>" /></a></div>
      <div class="opusBot"><a target="_blank" href="opus_show.php?l_id=<? echo $row['l_id']?>"><? echo $row['title']?></a><span><? echo $row['time']?></span></div>
    </div>
    <!--opusCon e -->
    <?
	}
	?>
  </div>
  <!--container_news e -->
  <?
include_once("_bottom.php");
?>
