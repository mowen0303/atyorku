<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_header.html";
$guideModel = new \admin\guide\GuideModel();
$guide_id = BasicTool::get('guide_id');
$guideModel-> increaseCountNumber($guide_id);
$arr = $guideModel ->getRowOfGuideById($guide_id);
?>

<?php
if(BasicTool::get(share) != 1) {
    ?>

    <script>
        $(function () {
            $("title").text("<?php echo $arr['title']?> - AtYorkU");
            $(".clickAuthor").click(function () {
                var url = 'jsbridge://doAction?<?php echo $arr['uid']?>';
                var iframe = document.createElement('iframe');
                iframe.style.width = '1px';
                iframe.style.height = '1px';
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);
                setTimeout(function () {
                    iframe.remove();
                }, 100);
            })

            $("#coverImgBox img").addClass("animate2");

         //----------------fold 折叠------------------------------[start]-------------------
            function fold(obj){
                if(obj.parent(".foldBox").hasClass("foldBoxShow")){
                    obj.parent(".foldBox").removeClass("foldBoxShow");
                }else{
                    obj.parent(".foldBox").addClass('foldBoxShow');
                }
            }

            $(".foldBox").each(function(){
                if($(this).height() >= parseInt($(this).css("max-height"))){
                    var $foldBtn = $('<div class="foldBtn"><div class="show">显示全部<svg viewBox="0 0 10 6""><path d="M8.716.217L5.002 4 1.285.218C.99-.072.514-.072.22.218c-.294.29-.294.76 0 1.052l4.25 4.512c.292.29.77.29 1.063 0L9.78 1.27c.293-.29.293-.76 0-1.052-.295-.29-.77-.29-1.063 0z"></path></svg></div><div class="hide">收起<svg viewBox="0 0 10 6"><path d="M8.716.217L5.002 4 1.285.218C.99-.072.514-.072.22.218c-.294.29-.294.76 0 1.052l4.25 4.512c.292.29.77.29 1.063 0L9.78 1.27c.293-.29.293-.76 0-1.052-.295-.29-.77-.29-1.063 0z"></path></svg></div></div>');
                    $foldBtn.click(function(){fold($foldBtn)});
                    $(this).append($foldBtn);
                }
            })
        //----------------fold 折叠------------------------------[end]-------------------

        })
    </script>
    <?php
}
?>
<article id="container">
    <!-- <div id="coverImgBox"><img style="width: 100%; height: auto" src="<?php echo $arr['cover'] ?>"></div> -->
    <h1><?php echo $arr['title'] ?></h1>
    <hr>
    <div class="authorBox">
    <div id="authorHead" class="authorHead clickAuthor" style="background-image: url(<?php echo $arr['img'] ?>)"></div>
    <address>文章作者：<span class="author clickAuthor"><?php echo $arr['alias']; ?></span><br>发表日期：<data><?php echo BasicTool::translateTime($arr['time'])?></data></address>
    </div>
    <hr>
    <section>
        <?php echo $arr['content'];?>
    </section>
    <p>&nbsp;</p>
    <hr>
    <div class="authorBox">
    <p class="viewBox"><span>浏览量（<?php echo $arr['view_no']; ?>）</span></p>
    </div>
</article>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_footer.html";
?>
