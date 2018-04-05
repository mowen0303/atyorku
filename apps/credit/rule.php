<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2018-02-17
 * Time: 2:24 PM
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/commonClass/config.php";
use \Credit as Credit;
?>

<!DOCTYPE html>
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta charset="UTF-8">
    <link href="css.css?9" rel="stylesheet" type="text/css">
    <title>我的积分</title>
</head>
<body>
<div class="creditRecord">
    <h2>获取积分途径</h2>
    <?php
    $dailyDescription = credit::$dailyCredit;
    foreach ($dailyDescription as $dailyRow) {
        ?>
        <div class="desRow">
            <span><?php echo $dailyRow['description']; ?></span><i>积分<?php echo $dailyRow['credit']?></i>
        </div>
        <?php
    }
    ?>
    <div class="desRow"><span>在问答系统中发布提问</span><i>积分<?php echo credit::$addCourseQuestion['credit'] ?></i></div>
    <div class="desRow"><span>在问答系统中删除提问</span><i>积分<?php echo credit::$deleteCourseQuestion['credit'] ?></i></div>
    <div class="desRow"><span>在问答中发布答案</span><i>积分<?php echo credit::$addCourseSolution['credit'] ?></i></div>
    <div class="desRow"><span>在资料市场中发布资料</span><i>积分<?php echo credit::$addBook['credit'] ?></i></div>
    <div class="desRow"><span>课评被评为: 有用课评</span><i>积分<?php echo credit::$addCourseRating[3]['credit'] ?></i></div>
    <div class="desRow"><span>课评被评为: 优秀课评</span><i>积分<?php echo credit::$addCourseRating[5]['credit'] ?></i></div>

    <h2>兑换政策</h2>
    <p>每1积=1元 (人民币)</p>
    <p>兑换用户必须是约克大学 (加拿大) 的学生,将会通过学生号进行认证。</p>
    <p>必须满100分才能进行兑换,每笔兑换中系统将收取10%的手续费。</p>
    <p>联系人工客服,微信号 atyorku666 进行兑换</p>
</div>

</div>
</body>
</html>
