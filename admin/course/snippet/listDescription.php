<?php
$currentUser = new \admin\user\UserModel();
$courseModel = new \admin\course\CourseModel();
$arr = $courseModel->getListOfCourseDescriptionByChildClassId(BasicTool::get('c_cid'));
$argument ="&f_cid=".BasicTool::get('f_cid')."&f_title=".BasicTool::get('f_title')."&c_cid=".BasicTool::get('c_cid')."&c_title=".BasicTool::get('c_title');
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 课评列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formDescription<?php echo $argument?>">添加一个课评</a>
    <a class="btn" href="index.php?s=listClassChild<?php echo $argument?>">返上级列表</a>
</nav>
<article class="mainBox">
    <header>
        <h2><?php echo BasicTool::get('f_title').BasicTool::get('c_title');?></h2>
    </header>
    <section>
        <?php
        foreach ($arr as $row) {
            $argument ="&f_cid=".BasicTool::get('f_cid')."&f_title=".BasicTool::get('f_title')."&c_cid=".BasicTool::get('c_cid')."&c_title=".BasicTool::get('c_title')."&id=".$row['id'];
        ?>

        <table class="tab">
            <thead>
            <tr>
                <th width="50">作者</th>
                <th>&nbsp;</th>
                <th>最后修改时间</th>
                <th>状态</th>
                <th class="center" width="270">操作</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td><em style=" display:inline-block;width: 50px; height: 50px;background-image: url(<?php echo $row['img']?>); background-size: 100% auto; background-position: center; border-radius: 50px"></em></td>
                    <td><?php echo $row['alias']?> &nbsp;&nbsp;&nbsp;&nbsp;(UID: <?php echo $row['user_id']?> )</td>
                    <td>
                        <?php
                        echo BasicTool::translateTime($row['time']);
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $row['course_description_id']==$row['id']?"<b style='color:#dd4444'>使用中</b>":"未使用";
                        ?>
                    </td>
                    <td class="center"><a class="btn" href="index.php?s=formDescription&flag=update&id=<?php echo $row['id']?><?php echo $argument?>">修改</a> <a href="courseController.php?action=setValidOfDescription&id=<?php echo $row['id']?><?php echo $argument?>" class="btn <?php echo $row['course_description_id']==$row['id']?"hidden":"";?>">启用</a> <a href="courseController.php?action=deleteDescription&bindId=<?php echo $row['course_description_id']?>&id=<?php echo $row['id']?><?php echo $argument?>" class="btn deleteBtn">删除</a> </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="5">
                        课程总结:
                        <pre><?php echo $row['summary']?></pre>
                        <hr>
                        分数构成:
                        <pre><?php echo $row['structure']?></pre>
                        <hr>
                        教授推荐:
                        <pre><?php echo $row['wisechooes']?></pre>
                        <hr>
                        高分攻略:
                        <pre><?php echo $row['strategy']?></pre>
                    </td>
                </tr>

            </tbody>
        </table>
        <?php
        }
        ?>
        <?php $courseModel->echoPageList(); ?>
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
