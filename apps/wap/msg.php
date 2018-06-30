<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_header.html";
?>
<style>
html {background:#fafafa;height:100%}
.subTitle{color:#989898}
</style>
<article id="container">
    <section>
        <p>&nbsp;</p>
        <h1 style="text-align: center"><?php echo $title?:"AtYorkU系统提示" ?></h1>
        <p class="subTitle" align="center"><?php echo $msg?></p>
    </section>
</article>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_footer.html";
?>
