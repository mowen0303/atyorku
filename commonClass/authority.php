<?php
//用户权限配置
$_AUT = [
    'GOD'=>getAuthorityNum(0),
    'ADMIN'=>getAuthorityNum(1),
    'USER_SHOW'=>getAuthorityNum(2),
    'USER_ADD'=>getAuthorityNum(3),
    'USER_UPDATE'=>getAuthorityNum(4),
    'USER_DELETE'=>getAuthorityNum(5),
    'COURSE_ADD'=>getAuthorityNum(7),
    'COURSE_COMMENT'=>getAuthorityNum(8),
    'COURSE_UPDATE'=>getAuthorityNum(9),
    'COURSE_DELETE'=>getAuthorityNum(10),
    'FORUM_ADD'=>getAuthorityNum(12),
    'FORUM_COMMENT'=>getAuthorityNum(13),
    'FORUM_UPDATE'=>getAuthorityNum(14),
    'FORUM_DELETE'=>getAuthorityNum(15),
    'GUIDE_ADD'=>getAuthorityNum(17),
    'GUIDE_UPDATE'=>getAuthorityNum(19),
    'GUIDE_DELETE'=>getAuthorityNum(20),
    'BOOK'=>getAuthorityNum(6),
    'MAP'=>getAuthorityNum(16),
    'EVENT_ADMIN'=>getAuthorityNum(11),
    'COURSE_QUESTION'=>getAuthorityNum(21),
    'COURSE_RATING'=>getAuthorityNum(22),
    'COURSE_RECALL'=>getAuthorityNum(23),
    'COMMENT'=>getAuthorityNum(24),
    'TASK_DESIGN'=>getAuthorityNum(18),
    'KNOWLEDGE' =>getAuthorityNum(25),
];
function getAuthorityNum($int){return pow(2,$int);}
?>
