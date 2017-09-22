<?php
$courseModel = new \admin\course\CourseModel();
$userModel = new \admin\user\UserModel();
$argument ="&f_cid=".BasicTool::get('f_cid')."&f_title=".BasicTool::get('f_title');

?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - <?php echo $courseTitle?>课程列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formClassChild<?php echo $argument?>">在此类下添加一门课程</a> <a class="btn" href="/admin/course/">返回主列表</a>
</nav>
<article class="mainBox">
    <h2><?php echo BasicTool::get('f_title')?></h2>
    <section>
        <table class="tab center">
            <thead>
            <tr style="text-align:center">
                <th class="center" width="140">课程代码</th>
                <th>状态</th>
                <th width="50">学分</th>
                <th width="50">难度</th>
                <th width="50">通过率</th>
                <th width="50">投票数</th>
                <th width="50">阅读量</th>
                <th width="50">评论量</th>
                <th width="200" class="center">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $courseModel -> getListOfChildClassByParentId(BasicTool::get('f_cid'));
            foreach ($arr as $row){
                $argument ="&f_cid={$row['parent_id']}&f_title={$row['mtitle']}&c_cid={$row['id']}&c_title={$row['stitle']}";
            ?>
            <tr>
                <td class="center"><a href="index.php?s=listDescription<?php echo $argument?>"> <?php echo $row['mtitle']." ".$row['stitle'];?></a> &nbsp;&nbsp;&nbsp;&nbsp;( <?php echo $row['count_all']?> )</td>
                <td><?php echo $row['course_description_id']==0?"<span style='color:#ff0000'>未绑定</span>":"<span style='color:#4d9200'>已绑定</span>"?></td>
                <td><?php echo $row['credits']?></td>
                <td><?php echo $row['diff']?></td>
                <td><?php echo $row['pass_rate']?></td>
                <td><?php echo $row['average_count']?></td>
                <td><?php echo $row['readcounter']?></td>
                <td><?php echo $row['comment_num']?></td>
                <td class="center"><a class="btn" href="index.php?s=formClassChild&flag=update<?php echo $argument?>">修改</a><a class="btn deleteBtn" href="courseController.php?action=deleteChildClass<?php echo $argument?>">删除</a><a class="btn" target="_blank" href="index.php?s=preview&c_cid=<?php echo $row['id']?>">预览</a></td>

            </tr>
            <?php }?>
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


