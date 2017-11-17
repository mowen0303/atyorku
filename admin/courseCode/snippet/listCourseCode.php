<?php
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$userModel = new \admin\user\UserModel();
$parentId = BasicTool::get("parent_id");
$typeStr = $parentId > 0 ? "子类" : "父类";
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <?php if($parentId>0) echo '<a class="btn" href="index.php?s=listCourseCode&parent_id=0">返回父类科目</a>';?>
    <a class="btn" href="index.php?s=formCourseCode&flag=add&parent_id=<?php echo $parentId ?>">添加新<?php echo $typeStr ?>科目</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo $typeStr ?>科目列表</h2></header>
    <form action="courseCodeController.php?action=deleteCourseCode" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="60px">ID</th>
                    <th><?php echo $typeStr ?>科目名称</th>
                    <th width="80px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!$parentId){$parentId=0;}
                $arr = $courseCodeModel->getListOfCourseCodeByParentId($parentId);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php
                            $id = $row["id"];
                            $title = $row["title"];
                            echo ($parentId == 0 ? "<a href=\"index.php?s=listCourseCode&parent_id={$id}\">{$title}</a>" : "{$title}");
                            ?>
                        </td>
                        <td><a class="btn" href="index.php?s=formCourseCode&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
