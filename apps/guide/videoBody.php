<article id="container">
    <div>
        <!-- <div id="coverImgBox"><img style="width: 100%; height: auto" src="<?php echo $arr['cover'] ?>"></div> -->
        <div class="wrapper">
            <div class="videoBox">
                <!--放视频 S-->
                <!--
                放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频
                放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频
                放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频
                放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频 放视频
                -->
            </div>
            <div class="infoBox">
                <h6 class="title"><?php echo $arr["title"]?></h6>
                <?php
                if ($arr["introduction"])
                    echo "<p class='intro'>{$arr['introduction']}</p>";
                ?>
                <div>
                    <span class="subtitle"><?php echo BasicTool::translateTime($arr["time"])?></span>
                    <span class="subtitle"><?php echo $arr["category_title"]?></span>
                </div>
            </div>
        </div>

        <section class="context">
            <p style="margin-top:2em"><img src="/resource/img/gzhqr.jpg" alt=""><p><p class="copyDoc"></p>
        </section>
        <?php
        if($arr['view_no']>=500){
            echo '<div class="readCount"><em></em><span>浏览量：'.$arr['view_no'].'</span><em></em></div>';
        }
        ?>

    </div>
    <!--评论组件 S-->
    <!--
    data-category 产品数据库表名
    data-production-id 产品ID（文章、二手书、分享、同学圈）
    data-receiver-id 产品作者ID
    -->
    <script type="text/javascript" src="/admin/resource/js/component.js"></script>
    <div id="commentComponent"
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
</article>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_footer.html";
?>