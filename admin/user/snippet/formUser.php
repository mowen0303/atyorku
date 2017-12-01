<?php
$currentUser = new \admin\user\UserModel();
$userId = BasicTool::get('uid');
try {$currentUser->isAuthorityToManageUserByTargetUserId($userId) or BasicTool::throwException("无权管理其他管理员");}catch(Exception $e){BasicTool::echoMessage($e->getMessage(),-1);die();}

$flag = $userId == null ? 'add' : 'update';
$inquiredUser = $flag=='add' ? false : new \admin\user\UserModel($userId);

$headDefault = '/resource/img/head-default.png';

?>
<script>
    $(function(){

        $("#blockBtn").click(function(){
           $("#blockBox").slideToggle();
        })

        $blockBox1 = $("#blockBox1");
        $blockBox2 = $("#blockBox2");
        $blockBox1.change(function(){

            if($blockBox1.val()>0)
            {
                $blockBox2.show();
            }else{
                $blockBox2.hide();
            }
        })

    })
</script>
<header class="topBox">
    <h1>
        <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'添加新用户':'修改用户信息';
        ?>
    </h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formPwd&uid=<?php echo $userId?>">修改密码</a>
    <a class="btn" href="index.php?s=formCredit&uid=<?php echo $userId?>">修改用户点券</a>
</nav>
<article class="mainBox">
    <form action="userController.php?action=modify" method="post">
        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="uid" value="<?php echo $userId?>" type="hidden">
            <div>
                <label>用户名邮箱<i>*</i></label>
                <?php
                    if($flag=='add'){
                        echo '<input class="input" type="text" name="name" value="">';
                    }else{
                        echo "<label>{$inquiredUser->userName}</label>";
                    }
                ?>
            </div>
            <div>
                <label>点券:<?php echo $inquiredUser->credit?></label>
            </div>
            <div>
                <label>活跃度:<?php echo $inquiredUser->activist?></label>
            </div>
            <?php if($flag=='add'){ ?>
                <div>
                    <label>密码</label>
                    <input class="input" type="password" name="pwd" value="">
                </div>
            <?php } ?>
            <div>
                <label>头像</label>
                <img src="<?php echo $flag=='add'?$headDefault:$inquiredUser->userHeadImg?>" height="80" width="80">
                <input class="input" type="hidden" name="img" value="<?php echo $flag=='add'?$headDefault:$inquiredUser->userHeadImg?>">
            </div>
            <div>
                <label>名字</label>
                <input class="input" type="text" name="alias" value="<?php echo $inquiredUser->aliasName?>">
            </div>
            <div>
                <label>微信号</label>
                <input class="input input-size50" type="text" name="wechat" value="<?php echo $inquiredUser->wechat?>">
            </div>
            <div>
                <?php
                if($currentUser->userId==$userId)
                {
                    echo '<input class="input" type="hidden" name="user_class_id" value="'.$inquiredUser->userClassId.'">';
                }
                else
                {
                ?>
                <label>级别</label>
                <select class="input input-select input-size50 selectDefault" name="user_class_id" defvalue="<?echo $inquiredUser->userClassId?>">
                    <?php  $currentUser->echoUserClassOption() ?>
                </select>
                <?php }?>
            </div>
            <div>
                <label>性别</label>
                <select class="input input-select input-size50 selectDefault" name="gender" defvalue="<?php echo $inquiredUser->gender?>">
                    <option value="2">保密</option>
                    <option value="1">男</option>
                    <option value="0">女</option>
                </select>
            </div>
            <div>
                <label>专业</label>
                <input class="input" type="text" name="major" value="<?php echo $inquiredUser->major?>">
            </div>
            <div>
                <label>入学年份</label>
                <input class="input" type="date" name="enroll_year" value="<?php echo date("Y-m-d",$inquiredUser->enrollYear) ?>">
            </div>
            <div>
                <label>个性签名</label>
                <textarea class="input input-textarea" placeholder="" name="description"><?php echo $inquiredUser->description?></textarea>
            </div>
            <div>
                <label>账号状态: <?php
                    if($inquiredUser){
                        $inquiredUser->isUserHasAuthority()?"正常":$inquiredUser->errorMsg;
                    }

                    ?> <input id="blockBtn" type="button" class="btn" value="禁言设置"> </label>
            </div>
            <div id="blockBox" style="display: none">

                <select id="blockBox1" class="input input-select input-size30 selectDefault" name="setblocktime">
                    <option value="-1">设置禁言状态</option>
                    <option value="0">解除禁言</option>
                    <option value="7">禁言7天</option>
                    <option value="14">禁言14天</option>
                    <option value="30">禁言30天</option>
                    <option value="3000">永久禁言</option>
                </select>
                <select  id="blockBox2" class="input input-select input-size50 selectDefault" name="blockreason" style="display: none">
                    <option value="无">设置禁言原因</option>
                    <option value="恶意刷广告">恶意广告</option>
                    <option value="恶意刷广告">发表不当内容</option>
                    <option value="辱骂或攻击他人">辱骂或攻击他人</option>
                    <option value="恶意灌水">恶意灌水</option>
                </select>
            </div>

            <div>
                <input class="btn btn-center" type="submit" title="提交" value="提交">
            </div>
        </section>
    </form>
</article>
