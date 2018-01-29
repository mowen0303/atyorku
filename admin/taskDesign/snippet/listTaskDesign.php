<?php
$taskDesignModel = new \admin\taskDesign\TaskDesignModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formTaskDesign">添加新成就设计</a>
</nav>
<article class="mainBox">
    <header><h2>成就设计列表</h2></header>
    <form action="taskDesignController.php?action=deleteTaskDesign" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="40px">顺序</th>
                    <th width="60px">图标</th>
                    <th>标题</th>
                    <th width="60px">奖励</th>
                    <th width="80px">学习资料</th>
                    <th width="60px">课评</th>
                    <th width="60px">问答</th>
                    <th width="60px">同学圈</th>
                    <th width="60px">knowledge</th>
                    <th width="60px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $taskDesignModel->getListOfTaskDesigns(40);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><img width="60px" height="auto" src="<?php echo $row['icon_url'] ?>"></td>
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row["bonus"] ?></td>
                        <td><?php echo $row["book"] ?></td>
                        <td><?php echo $row["course_rating"] ?></td>
                        <td><?php echo $row["course_question"] ?></td>
                        <td><?php echo $row["forum"] ?></td>
                        <td><?php echo $row["knowledge"] ?></td>
                        <td><a class="btn" href="index.php?s=formTaskDesign&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $taskDesignModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
