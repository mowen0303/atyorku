<?php
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$flag = BasicTool::get("flag");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<article class="mainBox">
    <!--评论组件 S-->
    <!--
    data-category 产品数据库表名
    data-production-id 产品ID（文章、二手书、分享、同学圈）
    data-receiver-id 产品作者ID
    -->
    <div id="commentComponent"
         data-category="all"
         data-production-id="0"
         data-receiver-id="1" style="background: transparent">
        <section class="textAreaContainer">
            <textarea name="comment" placeholder="说两句吧..."></textarea>
            <div id="commentButton">评论</div>
        </section>
        <section id="commentListContainer"></section>
        <section id="loadMoreButton">点击加载更多</section>
    </div>
    <!--评论组件 E-->
</article>
