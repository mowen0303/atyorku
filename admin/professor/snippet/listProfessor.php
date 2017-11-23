<?php
$professorModel = new \admin\professor\ProfessorModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formProfessor&flag=add">添加新教授</a>
</nav>
<article class="mainBox">
    <header><h2>教授列表</h2></header>
    <form action="professorController.php?action=deleteProfessor" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="60px">ID</th>
                    <th>教授名称</th>
                    <th width="60px">热度</th>
                    <th width="160px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $professorModel->getListOfProfessor("",20);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["name"] ?></td>
                        <td class="viewCount" data-id="<?php echo $row["id"]?>"><?php echo $row["view_count"] ?></td>
                        <td><a class="btn" href="index.php?s=formProfessor&flag=update<?php echo $argument?>">修改</a>
                        <a class="btn incrementBtn" data-id="<?php echo $row["id"]?>" href="javascript:void(0)">加热度</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $professorModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
        <script>
        $('.incrementBtn').each((i)=>{
            let id = $(`.incrementBtn:eq(${i})`).attr("data-id");
            $(`.incrementBtn:eq(${i})`).click(()=>{
                $.ajax({
                    url: "/admin/professor/ProfessorController.php?action=incrementProfessorViewCountByIdWithJson&id="+id,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    dataType:"json",
                    success:(json)=>{
                        if (json.code === 1) {
                            $.ajax({
                                url: "/admin/professor/ProfessorController.php?action=getProfessorByIdWithJson&id="+id,
                                type: "POST",
                                processData: false,
                                contentType: false,
                                dataType:"json",
                                success:(json2)=>{
                                    if (json2.code === 1) {
                                        $(`.viewCount:eq(${i})`).text(json2.result["view_count"]);
                                    } else {
                                        alert("刷新失败");
                                    }
                                }
                            })
                        } else {
                            alert("热度+1失败");
                        }
                    }
                })
            })
        });
        </script>
    </form>
</article>
