<? 
include_once("_head.php");
$l_id = $_GET['l_id'];

?>
<title>
<?
    	$query_tit = $db->select("imglist","*","id=$l_id");
		$row_tit   = $db->fetch_array($query_tit);
		echo $row_tit['title']; 
	?>
-莫问摄影 | MoWen Vision</title>
<link href="css/sed.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.lightbox.js"></script>
<link href="css/show.css" rel="stylesheet" type="text/css">
<!-- Ativando o jQuery lightBox plugin -->
<script type="text/javascript">
    $(function() {
        $('#gallery a').lightBox();
    });
    </script>
<? include_once("_body.php");?>
  <? include_once("_s_menu.php");?>
  <!--container_news s -->
  <div id="container_opus">
    <div class="opus_tit">
      <?
		echo $row_tit['title']; 
	?>
    </div>
    <!--list s -->
    <div id="gallery">
      <?
			$query = $db->select("imgcontent,imglist","*","l_id = $l_id and imglist.id = imgcontent.l_id and imgcontent.id <> imglist.faceid");
			while($row=$db->fetch_array($query))
			{
		  ?>
      <a href="<? echo $row['path']?>" title="<? echo $row['text']?>"> <img src="<? echo $row['path_sacl']?>"/> </a>
      <?
			}
		?>
    </div>
    <!--list e -->
    <div class="opus_info"> <span><script type="text/javascript" charset="utf-8">
(function(){
  var _w = 24 , _h = 24;
  var param = {
    url:location.href,
    type:'2',
    count:'', /**是否显示分享数，1显示(可选)*/
    appkey:'4141180378', /**您申请的应用appkey,显示分享来源(可选)*/
    title:'分享我的#流云摄影#作品《<? echo $row_tit['title'];  ?>》', /**分享的文字内容(可选，默认为所在页面的title)*/
    pic:'', /**分享图片的路径(可选)*/
    ralateUid:'1853836735', /**关联用户的UID，分享微博会@该用户(可选)*/
    rnd:new Date().valueOf()
  }
  var temp = [];
  for( var p in param ){
    temp.push(p + '=' + encodeURIComponent( param[p] || '' ) )
  }
  document.write('<iframe allowTransparency="true" frameborder="0" scrolling="no" src="http://hits.sinajs.cn/A1/weiboshare.html?' + temp.join('&') + '" width="'+ _w+'" height="'+_h+'"></iframe>')
})()
</script></span><span>时间：<? echo $row_tit['time'];?></span> <span>地点：<? echo $row_tit['title'];?></span> <span>拍摄：<? echo $row_tit['author'];?></span> </div>
  </div>
  <!--container_news e -->
  <?
include_once("_bottom.php");
?>
