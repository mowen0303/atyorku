<?php
$courseModel = new \admin\course\CourseModel();
$row = $courseModel->getRowOfCourseDescriptionByChildClassId(BasicTool::get('c_cid'));
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 课评列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listComment&f_title=<?php echo $row['mtitle']?>&c_cid=<?php echo $row['course_class_id']?>&c_title=<?php echo $row['title']?>">留言</a>
    <a class="btn" href="index.php?s=formDescription">评级</a>
</nav>
<article class="mainBox">
        <header>
            <h2><?php echo $row['mtitle'].$row['title'];?></h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="50">作者</th>
                    <th>&nbsp;</th>
                    <th width="60" class="center">学分</th>
                    <th width="60" class="center">通过率</th>
                    <th width="60" class="center">平均分</th>
                    <th width="60" class="center">难度</th>
                    <th width="60" class="center">浏览量</th>
                    <th width="60" class="center">投票总数</th>
                    <th width="60"  class="center">评论量</th>
                    <th width="100" class="center">最后修改时间</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><em style=" display:inline-block;width: 50px; height: 50px;background-image: url(<?php echo $row['img']?>); background-size: 100% auto; background-position: center; border-radius: 50px"></em></td>
                    <td><?php echo $row['alias']?> &nbsp;&nbsp;&nbsp;&nbsp;(UID: <?php echo $row['user_id']?> )</td>
                    <td class="center"><?php echo $row['credits']?></td>
                    <td class="center"><?php echo $row['pass_rate']?></td>
                    <td class="center"><?php echo $row['average']?></td>
                    <td class="center"><?php echo $row['diff']?></td>
                    <td class="center"><?php echo $row['readcounter']?></td>
                    <td class="center"><?php echo $row['average_count']?></td>
                    <td class="center"><?php echo $row['comment_num']?></td>
                    <td class="center">
                        <?php
                        echo BasicTool::translateTime($row['time']);
                        ?>
                    </td>

                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="9">
                        描述:
                        <pre><?php echo $row['descript']?></pre>
                        <hr>
                        学分排除:
                        <pre><?php echo $row['credit_ex']?></pre>
                        <hr>
                        前提条件:
                        <pre><?php echo $row['prerequest']?></pre>
                        <hr>
                        课本:
                        <pre><?php echo $row['textbook']?></pre>
                        <hr>
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
        </section>
</article>
<article class="mainBox">
    <h2>调试数据</h2>
    <section>
        <?php
        echo $argument."<br><br>";
        foreach($row as $k => $v){
            echo $k." : ".$v."<br>";
        }
        ?>
    </section>

</article>
