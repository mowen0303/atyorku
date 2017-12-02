<?php
$locationModel = new \admin\map\LocationModel();
$userModel = new \admin\user\UserModel();
$keyword = BasicTool::get('keyword');
$flag = $keyword ? 'listResult' : 'listAll';
if ($flag=='listResult') {
    $pageTitle = "包含\"" . $keyword . "\"的结果";
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle ?></h1>
</header>
<nav class="mainNav">
    <?php
    if ($flag == 'listResult') {
        ?>
        <a class="btn" href="index.php">返回</a>
        <?php
    }
    ?>
    <a class="btn" href="index.php?s=addLocation">添加大楼位置</a>
</nav>
<article class="mainBox">
    <form action="index.php?s=listLocation2&" method="get">
        <input class="input" placeholder="通过名称搜索大楼" type="text" name="keyword">
        <input class="btn" type="submit" value="搜索">
    </form>
    <form action="LocationController.php?action=deleteLocationById" method="post">
        <header>
            <h2>大楼位置</h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>#</th>
                    <th>缩写</th>
                    <th>全称</th>
                    <th>简介</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($flag == 'listAll') {
                    $arr = $locationModel->getListOfLocation();
                } elseif ($flag == 'listResult') {
                    $arr = $locationModel->getLocationsByFullNameKeyword($keyword);
                }

                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row['id'] ?></td>
                        <td><?php echo $row['init'] ?></td>
                        <td>
                            <a href="index.php?s=addLocation&id=<?php echo $row['id'] ?>"><?php echo $row['full_name'] ?></a>
                        </td>
                        <td><?php echo $row['info'] ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn">
        </footer>
    </form>
</article>