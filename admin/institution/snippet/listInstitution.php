<?php
$institutionModel = new \admin\institution\InstitutionModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/institution/index.php?listInstitution">返回</a>
    <a class="btn" href="index.php?s=formInstitution">添加机构</a>
</nav>
<article class="mainBox">
    <header><h2>机构列表</h2></header>
    <form action="institutionController.php?action=deleteInstitution" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="80px">顺序</th>
                    <th>机构名称</th>
                    <th>类别</th>
                    <th width="100px">坐标</th>
                    <th width="150px">学期开始日期</th>
                    <th width="150px">学期截止日期</th>
                    <th width="100px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $institutionModel->getListOfInstitution();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row["type"] ?></td>
                        <td><?php echo $row['coordinate'] ?></td>
                        <td><?php echo $row["term_start_times"] ?></td>
                        <td><?php echo $row['term_end_times'] ?></td>
                        <td><a class="btn" href="index.php?s=formInstitution&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $institutionModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
