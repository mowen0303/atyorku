<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new \admin\event\EventModel();
$imageModel = new \admin\image\ImageModel();
$userModel = new \admin\user\UserModel();
$event_id = BasicTool::get("event_id");
$event = $eventModel->getEvent($event_id);
$sponsor = $userModel->getProfileOfUserById($event["sponsor_user_id"]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <meta charset="UTF-8"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
    <div>
        <?php
        if ($event["img_id_1"]){
            $img_url_1 = $imageModel->getImageById($event["img_id_1"])["url"];
            echo "<div class='img' style='background-image:url(${img_url_1})'></div>";
        }
            ?>
        </div>
        <div id = "container">
            <h3 id="title"><?php echo $event["title"]?></h3>
            <p style="font-weight:bold;font-size:0.93rem"><?php echo BasicTool::translateTime($event["publish_time"])?>&nbsp;&nbsp;<?php echo $event["count_views"]?>&nbsp;<span style="font-weight:normal">浏览</span>&nbsp;<span style="font-weight:normal">&middot;</span>&nbsp;<?php echo $event["count_comments"]?>&nbsp;<span style="font-weight:normal">回复</span></p>
            <div id="profile_container">
                <div id="profile_picture" style="background-image:<?php echo "url({$sponsor["img"]})"?>"></div>
                <p id="sponsor_info">
                    <span style="margin-right:0.2rem;font-size:1.15rem"><?php echo $sponsor["alias"]?></span>
                    <img height="15" width="15" src="<?php echo "/resource/img/course_icon/{$sponsor["gender"]}g.png"?>"/>
                    <br/>
                    <span style="color:#aaaaaa;font-size:0.90rem;margin-right:0.2rem"><?php echo BasicTool::translateEnrollYear($sponsor["enroll_year"])?></span>
                    <span style="color:#aaaaaa;font-size:0.90rem"><?php echo $sponsor["major"]?></span>
                </p>
            </div>
            <div style="margin-top:0.7rem">
                <span style="font-size:0.7rem;color:#9a9a9a; background-color:white;position:relative;top:0.67rem;left:1.5rem">活动信息</span>
                <div id="event_info_container">
                    <div class="event_info_subcontainer">
                        <span>活动时间&nbsp;<?php echo BasicTool::translateTime($event["event_time"])?></span>
                        <br/>
                        <br/>
                        <span>截止时间&nbsp;<?php echo BasicTool::translateTime($event["expiration_time"])?></span>
                    </div>
                    <div class="event_info_subcontainer">
                        <span>人均费用&nbsp;$<?php echo $event["registration_fee"]?></span>
                        <br/>
                        <br/>
                        <span>报名人数&nbsp;<?php echo "{$event["count_participants"]}/{$event["max_participants"]}"?></span>
                    </div>
                </div>
            </div>
            <div id="event_description_container" style="margin-top:2rem">
                <p style="font-size:1.15rem;margin-bottom:0">
                    <?php echo $event["description"]?>
                </p>
            </div>
        </div>
        <?php
        if ($event["img_id_2"]){
            $img_url_2 = $imageModel->getImageById($event["img_id_2"])["url"];
            echo "<div class='img' style='margin-top:1.75rem;background-image:url(${img_url_2})'></div>";
        }
        ?>
        <?php
        if ($event["img_id_3"]){
            $img_url_3 = $imageModel->getImageById($event["img_id_3"])["url"];
            echo "<div class='img' style='margin-top:1.75rem;background-image:url(${img_url_3})'></div>";
        }
        ?>
    </div>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>