<?php
try{
    $employeeKPIModel = new \admin\employeeKPI\EmployeeKPIModel();
    $currentUser = new \admin\user\UserModel();
    $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足");
    $id = BasicTool::get("id");
    $flag = !$id ? "add" : "update";
    $form_action = "employeeKPIController.php?action=insertOrUpdateEmployeeKPIProfile";
    if ($flag == "add")
        $row=null;
    else
        $row = $employeeKPIModel->getEmployeeKPIProfiles($id)[0] or BasicTool::throwException("");
}catch(Exception $e){
    BasicTool::echoMessage($e->getMessage());
    die();
}

?>
<script>
    var nextInputId = 1;
    function addTextInput (){
        let textInput = `<div id="inputWrapper${nextInputId}"><input placeholder="用户名(邮箱)" class="input" style="width:90%" type="text" name="username[]" required/><button type="button" onclick="removeTextInput(${nextInputId})">&times;</button></div>`;
        $('#textInputsBox')[0].insertAdjacentHTML('beforeend',textInput);
        nextInputId++;
    }
    function removeTextInput(i){
        $('#inputWrapper'+i).remove();
    }
</script>

<header class="topBox">
    <h1> <?php
        echo "KPI统计" . '-';
        echo $flag == 'add' ? '添加': '更改';
        ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=getEmployeeKPIProfiles">返回</a>
</nav>
<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post">
        <input type="text" name="flag" value="<?php echo $flag?>" hidden/>
        <input type="text" name="id" value="<?php echo $id?>" hidden/>
        <section class="formBox">
            <div>
                <label>管理员用户ID</label>
                <input required class="input input-size30" type="text" name="main_user_id" value="<?php echo $row['main_user_id']?:$currentUser->userId ?>"> 当前用户<?php echo ": {$currentUser->aliasName} (ID: {$currentUser->userId})" ?>
            </div>
        </section>
        <section class="formBox" id = 'textInputsBox' style="clear:left">
            <label>小号</label>
            <?php
            if ($row){
                foreach ($row["user_ids"] as $i => $uid){
                    $username = $currentUser->getProfileOfUserById($uid)["name"];
                    $output = "<div id='inputWrapper{$i}'>
                <input class='input' placeholder='用户名(邮箱)' type='text' style='width:90%' name='username[]' value='{$username}' required/>
                <button type='button' onclick='removeTextInput({$i})'>&times;</button>
                </div>";
                    echo $output;
                }
                $nextInputId = count($row["user_ids"]);
                echo "<script type='text/javascript'>nextInputId={$nextInputId}</script>";
            }
            else{
                $output="<div id='inputWrapper0'>
                <input class='input' placeholder='用户名(邮箱)' type='text' style='width:90%' name='username[]' required/>
                <button type='button' onclick='removeTextInput(0)'>&times;</button>
            </div>";
                echo $output;
            }
            ?>
        </section>
        <button type='button' style="background-color:#222222;border-radius: 5px;padding:8px 24px;color:white" onclick="addTextInput()">添加</button>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>