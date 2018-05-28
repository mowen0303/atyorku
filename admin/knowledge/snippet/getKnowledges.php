<?php
$knowledgeModel = new \admin\knowledge\KnowledgeModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();

$knowledge_category_id = BasicTool::get('knowledge_category_id');
$knowledge_category_name = BasicTool::get("knowledge_category_name");
$arr = $knowledgeModel->getKnowledgeByCourseCodeIdProfIdCMS(1,true,"","",0,0,"",$knowledge_category_id);

?>
<header class="topBox">
    <h1>考试回忆录</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/knowledgeCategory/index.php">分类管理</a>
    <a class="btn" href="index.php?s=addKnowledgeText">添加新回忆录(文字)</a>
    <a class="btn" href="index.php?s=addKnowledgeImage">添加新回忆录(图片)</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo $knowledge_category_name?$knowledge_category_name:"最新发布"?></h2></header>
    <form action="knowledgeController.php?action=deleteKnowledge" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>科目</th>
                    <th>图片</th>
                    <th>卖家留言</th>
                    <th>文字</th>
                    <th>卖家</th>
                    <th>考点量</th>
                    <th>发布时间</th>
                    <th>售价</th>
                    <th>已售</th>
                    <th>评论量</th>
                    <th>阅读量</th>
                    <th>顺序</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"/></td>
                        <td><?php echo $row['id']?></td>
                        <td><?php echo "{$row['course_code_parent_title']} {$row['course_code_child_title']}"?></td>
                        <td style="max-height:85px;max-width:125px">
                            <?php
                            if ($row['img_id']){
                                $url = $imageModel->getImageById($row['img_id'])['url'];
                                echo "<img width='120' height='80' src='{$url}'>";
                                }
                                else
                                    echo "N/A"
                            ?>
                        </td>
                        <td style="max-width:170px"><?php echo $row['description'] ?></td>
                        <td style="max-width:320px">
                            <?php
                            if (!$row['img_id']){
                                $text="";
                                $i = 1;
                                foreach($row['knowledge_points'] as $knowledge_point){
                                    $text .= "{$i}. {$knowledge_point['description']}. ";
                                    $i++;
                                    }
                                    echo $text;
                                }
                                else
                                    echo 'N/A'
                            ?>
                        </td>
                        <td><?php echo $row['alias']?></td>
                        <td><?php echo $row['count_knowledge_points'] ?></td>
                        <td><?php echo BasicTool::translateTime($row['publish_time'])?></td>
                        <td><?php echo $row['price']?></td>
                        <td><?php echo $row['count_sold']?></td>
                        <td><?php echo $row['count_comments']?></td>
                        <td><?php echo $row["count_views"]?></td>
                        <td><?php echo $row["sort"]?></td>
                        <td><a href="index.php?s=<?php $url = $row['img_id']?"addKnowledgeImage&knowledge_id={$row['id']}":"addKnowledgeText&knowledge_id={$row['id']}"; echo $url?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $knowledgeModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
