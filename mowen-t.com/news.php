<? include_once("_head.php");?>
<?
	$query = $db->select("newslist,newscontent","*","newslist.id=newscontent.l_id order by id DESC");
?>
<title>莫问摄影 | MoWen Vision</title>
<link href="css/sed.css" rel="stylesheet" type="text/css">
<? include_once("_body.php");?>
<? include_once("_s_menu.php");?>
  <!--container_news s -->
  <div id="container_second"> 
  	<?
    	while($row = $db->fetch_array($query))
		{	
	?>
    <!--newsCon s -->
    <div class="newsCon">
      <div class="newsCon_tit"><? echo $row['title']?></div>
      <div class="newsCon_txt">
        <div class="txt_l"><? echo $row['content']?></div>
        <div class="txt_r"><img src="<? echo $row['imgpath']?>"/></div>
      </div>
    </div>
    <div class="newsCon_bot"><? echo $row['time']?></div>
    <!--newsCon e --> 
    <?
		}
	?>
  </div>
  <!--container_news e -->
  <?
include_once("_bottom.php");
?>
