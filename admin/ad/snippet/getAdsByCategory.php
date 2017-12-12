<?php
$adModel = new \admin\ad\AdModel();
$adCategoryModel = new \admin\adCategory\AdCategoryModel();
$userModel = new \admin\user\UserModel();
$ad_category_id = BasicTool::get('ad_category_id');
$ad_category_title = $adCategoryModel->getAdCategory($ad_category_id)["title"];
$imageModel = new\admin\image\ImageModel();
$flag = BasicTool::get("flag");
$arr = $adModel->getAdsByCategory($ad_category_id,$flag);
if($flag == 0){
    $display_option_0 = "style='display:none'";
    $display_option_1 = "";
}
else{
    $display_option_0="";
    $display_option_1 = "style='display:none'";
}
?>
    <header class="topBox">
        <h1><?php echo $pageTitle?>-<?php echo $ad_category_title ?></h1>
    </header>
    <nav class="mainNav">
        <a class="btn" href="/admin/adCategory/index.php?s=getAdCategories">返回</a>
        <a class="btn" <?php echo $display_option_0 ?> href="index.php?s=getAdsByCategory&ad_category_id=<?php echo $ad_category_id ?>&flag=0">未生效或过期的广告</a>
        <a class="btn" <?php echo $display_option_1 ?> href="index.php?s=getAdsByCategory&ad_category_id=<?php echo $ad_category_id ?>&flag=1">生效的广告</a>
        <a class="btn" href="index.php?s=addAd&ad_category_id=<?php echo $ad_category_id ?>&ad_category_title=<?php echo $ad_category_title?>">发布新广告</a>
    </nav>
    <article class="mainBox">
        <header><h2><?php echo BasicTool::get('title')?></h2></header>
        <form action="AdController.php?action=deleteAd" method="post">
            <section>
                <table class="tab">
                    <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>ID</th>
                        <th>图片1</th>
                        <th width="120px">标题</th>
                        <th>简介</th>
                        <th width="80px">广告商</th>
                        <th width="100px">广告链接</th>
                        <th width="40px">浏览量</th>
                        <th width="80px">投放时间</th>
                        <th width="40px">有效至</th>
                        <th width="200px">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                        $display_img_1 = "style=display:none";
                        if($row["img_id_1"]){
                            $img_url_1 = $imageModel->getImageById($row["img_id_1"])["url"];
                            $display_img_1 = "style=display:inline-block";
                        }

                        ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                            <td><?php echo $row['id']?></td>
                            <td><img <?php echo $display_img_1?> width="120" height="60" src="<?php echo $img_url_1?>"></a></td>
                            <td><?php echo $row['title']?></td>
                            <td><?php echo $row['description']?></a></td>
                            <td><?php echo $row['sponsor_name'] ?></td>
                            <td><?php echo $row['ad_url']?></td>
                            <td><?php echo $row['view_count'] ?></td>
                            <td><?php echo date("Y-m-d",($row['publish_time']))?></td>
                            <td><?php echo date("Y-m-d",($row['expiration_time']))?></td>
                            <td><a href="index.php?s=addAd&id=<?php echo $row["id"] ?>&ad_category_id=<?php echo $ad_category_id ?>&ad_category_title=<?php echo $ad_category_title?>">修改</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php echo $adModel->echoPageList()?>
            </section>
            <footer class="buttonBox">
                <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
            </footer>
        </form>
    </article>
<?php
/**
 * Created by PhpStorm.
 * User: XIN
 * Date: 2017/9/5
 * Time: 3:40
 */