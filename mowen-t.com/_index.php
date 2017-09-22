<? include_once("_head.php");?>
<title>莫问摄影 | MoWen Vision</title>
<link href="css/index.css" rel="stylesheet" type="text/css">
<? include_once("_body.php");?>
  <!--图片展示窗 100%~~ s -->
  <div id="showimg_container"> 
    <!--图片展示窗 固定1000 s -->
    <div id="showimg_box">
      <div class="newOpus"></div>
      <!--滑动container S-->
      <div id="animate_contaienr">
        <div id="animate_box" title="可以按键盘左右方向键进行切换">
          <?
        $query = $db->select("imglist,imgcontent","*","imglist.status='1' AND imglist.indexid = imgcontent.id order by imglist.id DESC LIMIT 6");
	while($row = $db->fetch_array($query))
		{
		?>
          <div class="imgBox"><a href="opus_show.php?l_id=<? echo $row['l_id']?>" target="_blank"><img src="<? echo $row['path']?>" width="841" height="419"></a></div>
          <?
		  }
		  ?>
        </div>
      </div>
      <!--滑动contiaenr E--> 
      <!--左右按钮 S -->
      <div id="left" class="imgBtn" title="可按键盘方向键:左"></div>
      <div id="right" class="imgBtn" title="可按键盘方向键:右"></div>
      <!--左右按钮 E --> 
      <!--底部坐标 S -->
      <div class="imgBar"></div>
      <!--底部坐标 E --> 
    </div>
    <!--图片展示窗 固定1000 e --> 
  </div>
  <!--图片展示窗 100%~ e~ -->
  <div id="lay2"> 
    <!--最新状态 S -->
    <div id="newsInfo_container"> 
      <!--信息卡动作区 S -->
      <div id="myinfoAct"></div>
      <!--信息卡动作区 E -->
      <div id="newsInfo">
        <div class="newsInfo_tit">最新动态</div>
        <!--s -->
        <div class="wbInfo">
          <iframe width="100%" height="298" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&width=0&height=298&fansRow=2&ptype=0&speed=0&skin=10&isTitle=0&noborder=0&isWeibo=1&isFans=0&uid=1853836735&verifier=8df3c22e&dpc=1"></iframe>
        </div>
        <!--e --> 
      </div>
    </div>
    <!--最新装填 E --> 
    <!--myinfo S -->
    <div id="myinfo">
      <div class="myinfo_box">
        <div class="myinfo_txt">
          <div class="s1"><span>莫问</span>上海-徐汇</div>
          <div class="s2">承接业务：<span>网站建设</span><span>摄影</span></div>
          <div class="s3">QQ:339788838&nbsp;&nbsp;&nbsp;&nbsp;手机:18621953931</div>
          <div class="s4"><a href="business.php">查看报价</a></div>
        </div>
      </div>
    </div>
    <!--myinfo E --> 
    <!--menu s -->
    <div id="menu">
      <div class="menuTit"><span><a href="http://weibo.com/jiyu163" target="_blank">最新动态</a></span></div>
      <div class="menuTit cur"><span><a href="opus_list.php">我的作品</a></span></div>
      <div class="menuTit"><span><a class="bus" href="business.php">商业服务</a></span></div>
      <div class="menuTit"><span><a href="message.php" target="_blank">预约/留言</a></span></div>
    </div>
    <!--menu e --> 
  </div>
  <?
include_once("_bottom.php");
?>
