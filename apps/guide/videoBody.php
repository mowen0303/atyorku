<article id="container" class="videoContainer">
    <div id="videoBox">
        <div class="aspectration" style="position: relative; height: 0; width: 100%; padding-top: 56.25%;" data-ratio="16:9">
            <?php
            if($arr['video_vendor']=="youtube"){
                ?>
                <iframe style="position: absolute; left:0;top:0;width:100%;height:100%" src="<?php echo $arr["video_source_url"]?>?rel=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media"></iframe>
                <?php
            }else {
                ?>
                <iframe style="position: absolute; left:0;top:0;width:100%;height:100%" src="<?php echo $arr["video_source_url"]?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>
                <?php
            }
            ?>
        </div>
    </div>
    <div id="videoBody">
        <div class="videoTitleBox">
            <div class="videoTitle"><?php echo $arr["title"]?></div>
            <div class="videoSubtitle">
                <span><?php echo BasicTool::translateTime($arr["time"])?></span>
                <span><?php echo $arr["category_title"]?></span>
                <span>
                    <?php
                    if($arr['view_no']>=500){
                        echo '浏览量：'.$arr['view_no'];
                    }
                    ?>
                </span>
            </div>
            <section class="videoContext">
                <?php echo $arr['content']; ?>
            </section>
        </div>

        <!--评论组件 S-->
        <!--
        data-category 产品数据库表名
        data-production-id 产品ID（文章、二手书、分享、同学圈）
        data-receiver-id 产品作者ID
        -->
        <script type="text/javascript" src="/admin/resource/js/component.js"></script>
        <div id="commentComponent"
             style="margin-top: 0"
             data-category="guide"
             data-production-id="<?php echo $arr['id']; ?>"
             data-receiver-id="<?php echo $arr['uid']; ?>">
            <header><span>用户评论（<?php echo $arr['count_comments']; ?>）</span></header>
            <section id="commentListContainer"></section>
            <section id="loadMoreButton">点击加载更多评论</section>
            <section class="textAreaContainer">
                <textarea name="comment" placeholder="说两句吧..."></textarea>
                <div id="commentButton">评论</div>
            </section>
        </div>
        <!--评论组件 E-->
        <div style="padding:2em 0; background: #f2f2f2"><img src="/resource/img/gzhqr.jpg" alt=""></div>
    </div>
</article>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_footer.html";
?>