<?
include_once("../global.php");


?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>无标题文档</title>
</head>

<body>
<?
echo $db->query("CREATE TABLE IF NOT EXISTS `admin` (
  `name` varchar(25) NOT NULL COMMENT '用户名',
  `pw` varchar(80) NOT NULL COMMENT '密码',
  `authority` tinyint(3) NOT NULL COMMENT '用户权限,1超级管理员,2一级,3二级,4三级',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;") ? "插入表admin<br>":false;

echo $db->query("INSERT INTO `admin` (`name`, `pw`, `authority`) VALUES
('jiyu', '02b1be0d48924c327124732726097157', 1);") ? "插入表数据<br>":false;

echo $db->query("CREATE TABLE IF NOT EXISTS `adminauthority` (
  `id` tinyint(3) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;") ? "插入表adminauthority<br>":false;

echo $db->query("INSERT INTO `adminauthority` (`id`, `name`) VALUES
(1, '超级管理员'),
(2, '一级管理员'),
(3, '二级管理员'),
(4, '三级管理员');") ? "插入表adminauthority的数据<br>":false;

echo $db->query("CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(20) NOT NULL,
  `values` varchar(100) NOT NULL,
  `remark` tinytext NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;") ? "插入表config<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `extend_userinfo` (
  `id` int(11) NOT NULL auto_increment,
  `nickname` varchar(60) NOT NULL COMMENT '昵称',
  `gender` varchar(20) NOT NULL COMMENT '性别',
  `work` varchar(20) NOT NULL COMMENT '职业',
  `name` varchar(40) NOT NULL COMMENT '真实姓名',
  `phone` varchar(20) NOT NULL COMMENT '手机',
  `call` varchar(20) NOT NULL COMMENT '固话',
  `qq` varchar(20) NOT NULL COMMENT 'QQ',
  `date` varchar(30) NOT NULL COMMENT '生日',
  `dress` varchar(120) NOT NULL COMMENT '住址',
  `page` varchar(120) NOT NULL COMMENT '网站',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") ? "插入表extend_userinfo<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `imgclass` (
  `id` int(11) NOT NULL auto_increment COMMENT '当前分类ID',
  `title` varchar(60) NOT NULL COMMENT '分类名称',
  `f_id` int(11) NOT NULL COMMENT '所属父分类的ID',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") ? "插入表imgclass<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `imgcontent` (
  `id` int(11) NOT NULL auto_increment COMMENT '图片ID',
  `path_sacl` varchar(120) NOT NULL COMMENT '缩略图路径',
  `path` varchar(120) NOT NULL COMMENT '图片存放路径',
  `text` tinytext NOT NULL COMMENT '图片说明',
  `l_id` int(11) NOT NULL COMMENT '所属作品list ID',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") ? "插入表imgcontent<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `imglist` (
  `id` int(11) NOT NULL auto_increment COMMENT '作品list ID',
  `title` varchar(120) NOT NULL COMMENT '作品你名称',
  `time` date NOT NULL COMMENT '创建时间',
  `author` varchar(40) NOT NULL COMMENT '作者',
  `c_id` int(11) NOT NULL default '0' COMMENT '所属分类',
  `status` tinyint(2) NOT NULL default '0' COMMENT '发布状态',
  `faceid` varchar(11) NOT NULL default '0' COMMENT '作品封面图片id',
  `indexid` varchar(11) NOT NULL default '0' COMMENT '首页显示图片id',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") ? "插入表imglist<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `newsclass` (
  `id` int(11) NOT NULL auto_increment COMMENT '分类ID',
  `title` varchar(25) NOT NULL COMMENT '分类名称',
  `f_id` int(11) NOT NULL COMMENT '父分类ID',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
") ? "插入表newsclass<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `newscontent` (
  `l_id` int(11) NOT NULL COMMENT '关联newslist ID',
  `content` text NOT NULL,
  PRIMARY KEY  (`l_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;") ? "插入表newscontent<br>":false;



echo $db->query("CREATE TABLE IF NOT EXISTS `newslist` (
  `id` int(11) NOT NULL auto_increment COMMENT '新闻ID',
  `c_id` int(11) NOT NULL COMMENT '所属分类ID',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `time` date NOT NULL COMMENT '时间',
  `author` varchar(30) NOT NULL COMMENT '作者',
  `imgpath` varchar(120) NOT NULL COMMENT '图片地址路径',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") ? "插入表newslist<br>":false;






?>
</body>
</html>
