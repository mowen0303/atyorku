<?php
$courseModel = new \admin\course\CourseModel();
$userModel = new \admin\user\UserModel();
$arr = $courseModel->getListOfFatherClass();
?>

<header class="topBox">
    <h1><?php echo $pageTitle?> - 课程列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/course/index.php?s=formClassFather">添加一个新的课程大类</a>
</nav>

<article class="mainBox">
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th width="20">&nbsp;</th>
                <th width="60">名称</th>
                <th>&nbsp;</th>
                <th class="center" width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $courseModel->getListOfFatherClass();
            foreach ($arr as $row) {
                $argument ="&f_cid={$row['id']}&f_title={$row['title']}";
            ?>
            <tr>
                <td>&nbsp;</td>
                <td><a href="index.php?s=listClassChild<?php echo $argument?>"><?php echo $row['title']?></a></td>
                <td>( <?php echo $row['count_all']?> )</td>
                <td class="center"><a class="btn" href="index.php?s=formClassFather&flag=update<?php echo $argument?>">修改</a> <a class="btn deleteBtn" href="courseController.php?action=deleteFatherClass<?php echo $argument?>">删除</a></td>
            </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <h2>调试数据</h2>
    <section>
        <?php
        echo $argument."<br><br>";
        foreach($arr[0] as $k => $v){
            echo $k." : ".$v."<br>";
        }
        ?>
    </section>
</article>



