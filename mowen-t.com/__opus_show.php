<? include_once("_head.php");?>
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
    <div class="opus_tit">标题</div>
    <!--list s -->
    <div id="gallery">
          <ul>
        <li> <a href="upfile/opus/image1.jpg" title="111"> <img src="upfile/opus/thumb_image1.jpg" width="72" height="72" alt="" /> </a> </li>
        <li> <a href="upfile/opus/image2.jpg" title="Utilize a flexibilidade dos seletores da jQuery e crie um grupo de imagens como desejar. $('#gallery a').lightBox();"> <img src="upfile/opus/thumb_image2.jpg" width="72" height="72" alt="" /> </a> </li>
        <li> <a href="upfile/opus/image3.jpg" title="Utilize a flexibilidade dos seletores da jQuery e crie um grupo de imagens como desejar. $('#gallery a').lightBox();"> <img src="upfile/opus/thumb_image3.jpg" width="72" height="72" alt="" /> </a> </li>
        <li> <a href="upfile/opus/image4.jpg" title="Utilize a flexibilidade dos seletores da jQuery e crie um grupo de imagens como desejar. $('#gallery a').lightBox();"> <img src="upfile/opus/thumb_image4.jpg" width="72" height="72" alt="" /> </a> </li>
        <li> <a href="upfile/opus/image5.jpg" title="Utilize a flexibilidade dos seletores da jQuery e crie um grupo de imagens como desejar. $('#gallery a').lightBox();"> <img src="upfile/opus/thumb_image5.jpg" width="72" height="72" alt="" /> </a> </li>
      </ul>
        </div>
    <!--list e -->
    <div class="opus_info"> <span>时间：2011-1-1</span> <span>地点：公园</span> <span>拍摄：流云</span> </div>
  </div>
      <!--container_news e -->
      <?
include_once("_bottom.php");
?>
