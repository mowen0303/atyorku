<?php
$courseRatingModel = new \admin\courseRating\CourseRatingModel();
$userModel = new \admin\user\UserModel();
$isGod = $userModel->isUserHasAuthority("GOD");
$queryUserName = htmlspecialchars(BasicTool::get("user_name"));
$queryCourseCode = BasicTool::get("course_code_id");
?>
<header class="topBox">
    <h1><?php echo htmlspecialchars($pageTitle)?></h1>
</header>
<nav class="mainNav">
    <?php
        if($queryUserName || $queryCourseCode){
            echo '<a class="btn" href="javascript:history.go(-1);">返回</a>';
        } else {
            echo '<a class="btn" href="index.php?s=listCourseCode">大类索引</a>
    <a class="btn" href="index.php?s=listCourseRatingNotAwarded">未奖励的课评</a>
    <a class="btn" href="index.php?s=listCourseProfReport">科目教授报告表</a>
    <a class="btn" href="index.php?s=listCourseReport">科目报告表</a>
    <a class="btn" href="index.php?s=listProfReport">教授报告表</a>
    <a class="btn" href="index.php?s=formCourseRating&flag=add">添加新课评</a>';
        }
    ?>
</nav>
<?php
if(!$queryUserName && !$queryCourseCode){
?>
<script>
    function parseCourseCodeFromString(str){
        var arr = str.split(/(^[a-zA-Z]+)/).slice(1);
        var parent=arr[0]?arr[0].trim():"";
        var child = "";
        if(parent.length===0 && str.length>0){
            child = str;
        }else if(arr[1]){
            child=arr[1].trim();
        }
        return [parent,child];
    }

    function handleQuery(e){
        var e = document.getElementById("querySelect");
        if(e.options[e.selectedIndex].value==="course_code"){
            var str = $("input[name=search_value]").val().trim();
            if(str.length===0){return;}
            var arr = parseCourseCodeFromString(str);
            $.get("/admin/courseCode/courseCodeController.php?action=getCourseCodeByStringWithJson&parent="+arr[0]+"&child="+arr[1],function(data){
                data = JSON.parse(data);
                if(data.result != null && data.result['course_child_id'] != null){
                    $("input[name=user_name]").val(null);
                    $("input[name=course_code_id]").val(data.result['course_child_id']);
                    document.getElementById("queryForm").submit();
                }else{
                    alert("未找到该科目");
                }
            });
        } else {
            $("input[name=course_code_id]").val(null);
            $("input[name=user_name]").val($("input[name=search_value]").val());
            document.getElementById("queryForm").submit();
        }

    }
</script>
<article class="mainBox">
    <form id="queryForm" action="index.php?s=listCourseRating" method="get">
        <header>
            <h2>查询用户课评记录</h2>
        </header>
        <section>
            <table width="100%">
                <tbody>
                <tr>
                    <td width="180px">
                        <select id="querySelect" class="input input-select input-50 selectDefault" name="search_type" defvalue="<?php echo htmlspecialchars(BasicTool::get("search_type")) ?>">
                            <option value="course_code">科目名称</option>
                            <option value="username">用户名邮箱</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="user_name" style="display:none;">
                        <input type="text" name="course_code_id" style="display:none;">
                        <input class="input" type="text" name="search_value" placeholder="输入对应搜索信息" style="margin-left:16px;" />
                    </td>
                    <td width="150px">
                        <a><input type="submit" value="搜索" class="btn" style="width:110px; float:right;" onclick="handleQuery(); return false;"></a>
                    </td>
                </tr>
                </tbody>
            </table>
<!--            <input class="input" placeholder="用户名邮箱" type="text" name="user_name" value="">-->
<!--            <input class="btn btn-center" type="submit" title="查询课评记录" value="查询课评记录">-->
        </section>
    </form>
</article>
<?php
}
?>
<article class="mainBox">
    <div style="display: flex; justify-content: flex-end">
        <a class="btn" href="courseRatingController.php?action=updateAllReports" onclick="return confirm('确认更新全部报告?')">更新全部报告</a>
        <a class="btn" href="courseRatingController.php?action=cleanCourseProfReport" onclick="return confirm('确认清除无效数据?')">清除无效'教授&课程'数据</a>
    </div>
    <header><h2><?php echo htmlspecialchars($typeStr) ?>课评列表<?php if($queryUserName){echo " - {$queryUserName}";} ?></h2></header>
    <form action="courseRatingController.php?action=deleteCourseRating" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="5%"><input id="cBoxAll" type="checkbox"></th>
                    <th width="5%">ID</th>
                    <th width="8%">科目</th>
                    <th width="8%">用户</th>
                    <th width="8%">教授</th>
                    <th width="3%">内容</th>
                    <th width="3%">作业</th>
                    <th width="3%">考试</th>
                    <th width="3%">成绩</th>
                    <th width="6%">学期</th>
                    <th width="6%">赞/踩</th>
                    <th width="20%">评论</th>
                    <th width="18%">课程总结</th>
                    <th width="10%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = null;
                if($queryUserName){
                    $arr = $courseRatingModel->getListOfCourseRatingByUserId($userModel->getUserIdByName($queryUserName), 40);
                } else if($queryCourseCode) {
                    $arr = $courseRatingModel->getListOfCourseRatingByCourseId($queryCourseCode,'essence' ,40);
                } else {
                    $arr = $courseRatingModel->getListOfCourseRating(false,40,"id");
                }
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo htmlspecialchars($row["id"]) ?></td>
                        <td><?php echo htmlspecialchars($row["course_code_parent_title"] . " " . $row["course_code_child_title"]) ?></td>
                        <td><?php
                                if($queryUserId && $queryUserName) {
                                    echo htmlspecialchars($row["user_name"]);
                                } else {
                                    $userId = htmlspecialchars($row["user_id"]);
                                    $username = htmlspecialchars($row["user_name"]);
                                    echo "<a href='index.php?s=listCourseRating&user_id={$userId}&user_name={$username}'>{$username}</a>";
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row["prof_name"]) ?></td>
                        <td><?php echo htmlspecialchars($row["content_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["homework_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["test_diff"]) ?></td>
                        <td><?php echo htmlspecialchars($row["grade"]) ?></td>
                        <td><?php echo htmlspecialchars($row["term"] . " " . $row["year"]) ?></td>
                        <td><?php echo $row["count_like"] ?>/<?php echo $row["count_dislike"] ?></td>
                        <td><?php echo htmlspecialchars($row["comment"]) ?></td>
                        <td><?php echo htmlspecialchars($row["content_summary"]) ?></td>
                        <td>
                            <a class="btn" href="index.php?s=formCourseRating&flag=update<?php echo htmlspecialchars($argument)?>">修改</a>
                            <?php
                                if($row['essence']){
                                    echo '<a class="btn" href="courseRatingController.php?action=deleteEssenceWithJson&id='.$row['id'].'">精品</a>';
                                }else{
                                    echo '<a class="btn" style="background-color:#ccc" href="courseRatingController.php?action=addEssenceWithJson&id='.$row['id'].'">精品</a>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $courseRatingModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
