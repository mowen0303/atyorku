<?php
//判断是否有"删除用户分组"权限
$currentUser = new \admin\user\UserModel();
try{$currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("无权操作");}catch(Exception $e){BasicTool::echoMessage($e->getMessage());die();}

$userId = BasicTool::get('uid');
$inquiredUser = new \admin\user\UserModel($userId);
?>

<header class="topBox">
    <h1><?php echo $pageTitle?> - 手动充值 / 手动扣除 点券</h1>
</header>
<article class="mainBox">
    <form action="userController.php?action=modifyCredit" method="post">
        <section>

            <div class="formBox">
                <div>
                    <label>当前点券:<?php echo $inquiredUser->credit?></label>
                </div>
                <div>
                    <label>增加/减少点券数量(例如:100 or -100)</label>
                    <input class="input input-size50" type="number" name="credit" value="">
                    <input type="hidden" name="userId" value="<?php echo $userId?>">
                    <input type="hidden" name="currentCredit" value="<?php echo $inquiredUser->credit?>">
                </div>
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="修改" class="btn" id="btn">
        </footer>
    </form>
</article>
