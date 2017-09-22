-- phpMyAdmin SQL Dump
-- version 2.6.4-pl4
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 25, 2015 at 11:52 PM
-- Server version: 6.0.2
-- PHP Version: 5.2.4
-- 
-- Database: `liuyun`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `admin`
-- 

CREATE TABLE `admin` (
  `name` varchar(25) NOT NULL COMMENT '用户名',
  `pw` varchar(80) NOT NULL COMMENT '密码',
  `authority` tinyint(3) NOT NULL COMMENT '用户权限,1超级管理员,2一级,3二级,4三级',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `admin`
-- 

INSERT INTO `admin` VALUES ('jiyu', '69aa37f2a305f152c4f2a95d5add5b13', 1);
INSERT INTO `admin` VALUES ('xiaofen', '69aa37f2a305f152c4f2a95d5add5b13', 2);
INSERT INTO `admin` VALUES ('xiaoxi', '44607d70e6bf1c40701f73104cad2d7c', 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `adminauthority`
-- 

CREATE TABLE `adminauthority` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `adminauthority`
-- 

INSERT INTO `adminauthority` VALUES (1, '超级管理员');
INSERT INTO `adminauthority` VALUES (2, '一级管理员');
INSERT INTO `adminauthority` VALUES (3, '二级管理员');
INSERT INTO `adminauthority` VALUES (4, '三级管理员');

-- --------------------------------------------------------

-- 
-- Table structure for table `config`
-- 

CREATE TABLE `config` (
  `name` varchar(20) NOT NULL,
  `values` varchar(100) NOT NULL,
  `remark` tinytext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `config`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `extend_userinfo`
-- 

CREATE TABLE `extend_userinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `extend_userinfo`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `imgclass`
-- 

CREATE TABLE `imgclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '当前分类ID',
  `title` varchar(60) NOT NULL COMMENT '分类名称',
  `f_id` int(11) NOT NULL COMMENT '所属父分类的ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `imgclass`
-- 

INSERT INTO `imgclass` VALUES (1, '摄影', 0);
INSERT INTO `imgclass` VALUES (2, '商业修片', 0);
INSERT INTO `imgclass` VALUES (3, '设计', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `imgcontent`
-- 

CREATE TABLE `imgcontent` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `path_sacl` varchar(120) NOT NULL COMMENT '缩略图路径',
  `path` varchar(120) NOT NULL COMMENT '图片存放路径',
  `text` tinytext NOT NULL COMMENT '图片说明',
  `l_id` int(11) NOT NULL COMMENT '所属作品list ID',
  `myorder` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=496 DEFAULT CHARSET=utf8 AUTO_INCREMENT=496 ;

-- 
-- Dumping data for table `imgcontent`
-- 

INSERT INTO `imgcontent` VALUES (55, '/upfile/opus/1(2)_1319649978_small.jpg', '/upfile/opus/1(2)_1319649978.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (56, '/upfile/opus/1(3)_1319649981_small.jpg', '/upfile/opus/1(3)_1319649981.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (57, '/upfile/opus/1(4)_1319649983_small.jpg', '/upfile/opus/1(4)_1319649983.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (60, '/upfile/opus/1(7)_1319649991_small.jpg', '/upfile/opus/1(7)_1319649991.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (62, '/upfile/opus/1(9)_1319649996_small.jpg', '/upfile/opus/1(9)_1319649996.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (64, '/upfile/opus/1(11)_1319650001_small.jpg', '/upfile/opus/1(11)_1319650001.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (65, '/upfile/opus/1(12)_1319650003_small.jpg', '/upfile/opus/1(12)_1319650003.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (66, '/upfile/opus/1(13)_1319650006_small.jpg', '/upfile/opus/1(13)_1319650006.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (68, '/upfile/opus/1(15)_1319650010_small.jpg', '/upfile/opus/1(15)_1319650010.jpg', '', 2, 0);
INSERT INTO `imgcontent` VALUES (69, '/upfile/opus/2(1)_1319718667_small.jpg', '/upfile/opus/2(1)_1319718667.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (70, '/upfile/opus/2(2)_1319718669_small.jpg', '/upfile/opus/2(2)_1319718669.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (71, '/upfile/opus/2(3)_1319718670_small.jpg', '/upfile/opus/2(3)_1319718670.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (72, '/upfile/opus/2(4)_1319718672_small.jpg', '/upfile/opus/2(4)_1319718672.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (73, '/upfile/opus/2(5)_1319718674_small.jpg', '/upfile/opus/2(5)_1319718674.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (76, '/upfile/opus/2(8)_1319718678_small.jpg', '/upfile/opus/2(8)_1319718678.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (77, '/upfile/opus/2(9)_1319718680_small.jpg', '/upfile/opus/2(9)_1319718680.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (78, '/upfile/opus/2(10)_1319718683_small.jpg', '/upfile/opus/2(10)_1319718683.jpg', '', 3, 0);
INSERT INTO `imgcontent` VALUES (95, '/upfile/opus/1_1321609309_small.jpg', '/upfile/opus/1_1321609309.jpg', '', 5, 0);
INSERT INTO `imgcontent` VALUES (96, '/upfile/opus/2_1321609313_small.jpg', '/upfile/opus/2_1321609313.jpg', '', 5, 0);
INSERT INTO `imgcontent` VALUES (97, '/upfile/opus/3_1321609316_small.jpg', '/upfile/opus/3_1321609316.jpg', '', 5, 0);
INSERT INTO `imgcontent` VALUES (191, '/upfile/opus/1(1)__1326339915_small.jpg', '/upfile/opus/1(1)__1326339915.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (192, '/upfile/opus/1(2)__1326339915_small.jpg', '/upfile/opus/1(2)__1326339915.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (194, '/upfile/opus/1(4)__1326339916_small.jpg', '/upfile/opus/1(4)__1326339916.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (195, '/upfile/opus/1(5)_1326339917_small.jpg', '/upfile/opus/1(5)_1326339917.jpg', '', 10, 99);
INSERT INTO `imgcontent` VALUES (196, '/upfile/opus/1(6)__1326339918_small.jpg', '/upfile/opus/1(6)__1326339918.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (198, '/upfile/opus/1(8)_1326339919_small.jpg', '/upfile/opus/1(8)_1326339919.jpg', '', 10, 98);
INSERT INTO `imgcontent` VALUES (203, '/upfile/opus/1(13)_1326339922_small.jpg', '/upfile/opus/1(13)_1326339922.jpg', '', 10, 97);
INSERT INTO `imgcontent` VALUES (206, '/upfile/opus/1(16)__1326339924_small.jpg', '/upfile/opus/1(16)__1326339924.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (207, '/upfile/opus/1(17)__1326339925_small.jpg', '/upfile/opus/1(17)__1326339925.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (210, '/upfile/opus/1(20)__1326339926_small.jpg', '/upfile/opus/1(20)__1326339926.jpg', '', 10, 0);
INSERT INTO `imgcontent` VALUES (212, '/upfile/opus/ontheway(35)__1334739697_small.jpg', '/upfile/opus/ontheway(35)__1334739697.jpg', '', 11, 0);
INSERT INTO `imgcontent` VALUES (214, '/upfile/opus/ontheway(38)__1334739699_small.jpg', '/upfile/opus/ontheway(38)__1334739699.jpg', '', 11, 0);
INSERT INTO `imgcontent` VALUES (215, '/upfile/opus/ontheway(42)__1334739701_small.jpg', '/upfile/opus/ontheway(42)__1334739701.jpg', '', 11, 0);
INSERT INTO `imgcontent` VALUES (218, '/upfile/opus/ontheway(5)__1334739705_small.jpg', '/upfile/opus/ontheway(5)__1334739705.jpg', '', 11, 0);
INSERT INTO `imgcontent` VALUES (255, '/upfile/opus/6e7f4dbfjw1dtk8ucp0lnj-001_1347873081_small.jpg', '/upfile/opus/6e7f4dbfjw1dtk8ucp0lnj-001_1347873081.jpg', '', 14, 99);
INSERT INTO `imgcontent` VALUES (256, '/upfile/opus/6e7f4dbfjw1dtk6y6tg7mj-001_1347873083_small.jpg', '/upfile/opus/6e7f4dbfjw1dtk6y6tg7mj-001_1347873083.jpg', '', 14, 98);
INSERT INTO `imgcontent` VALUES (257, '/upfile/opus/6e7f4dbfjw1dtk6xxhmzhj-001_1347873085_small.jpg', '/upfile/opus/6e7f4dbfjw1dtk6xxhmzhj-001_1347873085.jpg', '', 14, 97);
INSERT INTO `imgcontent` VALUES (258, '/upfile/opus/6e7f4dbfjw1dtk6xrixrcj-001_1347873087_small.jpg', '/upfile/opus/6e7f4dbfjw1dtk6xrixrcj-001_1347873087.jpg', '', 14, 96);
INSERT INTO `imgcontent` VALUES (260, '/upfile/opus/1(1)_1347873389_small.jpg', '/upfile/opus/1(1)_1347873389.jpg', '', 15, 99);
INSERT INTO `imgcontent` VALUES (261, '/upfile/opus/1(2)_1347873391_small.jpg', '/upfile/opus/1(2)_1347873391.jpg', '', 15, 100);
INSERT INTO `imgcontent` VALUES (262, '/upfile/opus/1(6)_1347873394_small.jpg', '/upfile/opus/1(6)_1347873394.jpg', '', 15, 98);
INSERT INTO `imgcontent` VALUES (263, '/upfile/opus/1(5)_1347873396_small.jpg', '/upfile/opus/1(5)_1347873396.jpg', '', 15, 97);
INSERT INTO `imgcontent` VALUES (264, '/upfile/opus/1(3)_1347873398_small.jpg', '/upfile/opus/1(3)_1347873398.jpg', '', 15, 101);
INSERT INTO `imgcontent` VALUES (265, '/upfile/opus/1(4)_1347873400_small.jpg', '/upfile/opus/1(4)_1347873400.jpg', '', 15, 95);
INSERT INTO `imgcontent` VALUES (268, '/upfile/opus/1(7)_1347873764_small.jpg', '/upfile/opus/1(7)_1347873764.jpg', '', 15, 96);
INSERT INTO `imgcontent` VALUES (283, '/upfile/opus/(23)_1347891118_small.jpg', '/upfile/opus/(23)_1347891118.jpg', '', 14, 96);
INSERT INTO `imgcontent` VALUES (284, '/upfile/opus/(24)_1347891122_small.jpg', '/upfile/opus/(24)_1347891122.jpg', '', 14, 95);
INSERT INTO `imgcontent` VALUES (287, '/upfile/opus/(33)_1347891133_small.jpg', '/upfile/opus/(33)_1347891133.jpg', '', 14, 92);
INSERT INTO `imgcontent` VALUES (289, '/upfile/opus/1_1347934563_small.jpg', '/upfile/opus/1_1347934563.jpg', '', 16, 0);
INSERT INTO `imgcontent` VALUES (290, '/upfile/opus/2_1347934565_small.jpg', '/upfile/opus/2_1347934565.jpg', '', 16, 0);
INSERT INTO `imgcontent` VALUES (291, '/upfile/opus/3_1347934569_small.jpg', '/upfile/opus/3_1347934569.jpg', '', 16, 0);
INSERT INTO `imgcontent` VALUES (292, '/upfile/opus/1_1347934867_small.jpg', '/upfile/opus/1_1347934867.jpg', '', 17, 0);
INSERT INTO `imgcontent` VALUES (293, '/upfile/opus/2_1347934870_small.jpg', '/upfile/opus/2_1347934870.jpg', '', 17, 0);
INSERT INTO `imgcontent` VALUES (294, '/upfile/opus/3_1347934873_small.jpg', '/upfile/opus/3_1347934873.jpg', '', 17, 0);
INSERT INTO `imgcontent` VALUES (296, '/upfile/opus/2_1347944159_small.jpg', '/upfile/opus/2_1347944159.jpg', '', 21, 0);
INSERT INTO `imgcontent` VALUES (297, '/upfile/opus/1_1347944161_small.jpg', '/upfile/opus/1_1347944161.jpg', '', 21, 0);
INSERT INTO `imgcontent` VALUES (298, '/upfile/opus/3_1347944164_small.jpg', '/upfile/opus/3_1347944164.jpg', '', 21, 0);
INSERT INTO `imgcontent` VALUES (299, '/upfile/opus/2_1347968442_small.jpg', '/upfile/opus/2_1347968442.jpg', '', 22, 0);
INSERT INTO `imgcontent` VALUES (300, '/upfile/opus/1_1347968443_small.jpg', '/upfile/opus/1_1347968443.jpg', '', 22, 0);
INSERT INTO `imgcontent` VALUES (301, '/upfile/opus/3_1347968444_small.jpg', '/upfile/opus/3_1347968444.jpg', '', 22, 0);
INSERT INTO `imgcontent` VALUES (302, '/upfile/opus/1_1347968687_small.jpg', '/upfile/opus/1_1347968687.jpg', '', 23, 0);
INSERT INTO `imgcontent` VALUES (304, '/upfile/opus/3_1347968691_small.jpg', '/upfile/opus/3_1347968691.jpg', '', 23, 0);
INSERT INTO `imgcontent` VALUES (305, '/upfile/opus/2_1347968739_small.jpg', '/upfile/opus/2_1347968739.jpg', '', 23, 9);
INSERT INTO `imgcontent` VALUES (306, '/upfile/opus/1_1347968994_small.jpg', '/upfile/opus/1_1347968994.jpg', '', 24, 0);
INSERT INTO `imgcontent` VALUES (307, '/upfile/opus/2_1347968995_small.jpg', '/upfile/opus/2_1347968995.jpg', '', 24, 0);
INSERT INTO `imgcontent` VALUES (308, '/upfile/opus/3_1347968995_small.jpg', '/upfile/opus/3_1347968995.jpg', '', 24, 0);
INSERT INTO `imgcontent` VALUES (312, '/upfile/opus/1_1347969574_small.jpg', '/upfile/opus/1_1347969574.jpg', '', 26, 0);
INSERT INTO `imgcontent` VALUES (313, '/upfile/opus/2_1347969574_small.jpg', '/upfile/opus/2_1347969574.jpg', '', 26, 0);
INSERT INTO `imgcontent` VALUES (314, '/upfile/opus/3_1347969575_small.jpg', '/upfile/opus/3_1347969575.jpg', '', 26, 0);
INSERT INTO `imgcontent` VALUES (315, '/upfile/opus/1_1347970360_small.jpg', '/upfile/opus/1_1347970360.jpg', '', 27, 0);
INSERT INTO `imgcontent` VALUES (316, '/upfile/opus/2_1347970362_small.jpg', '/upfile/opus/2_1347970362.jpg', '', 27, 0);
INSERT INTO `imgcontent` VALUES (317, '/upfile/opus/3_1347970366_small.jpg', '/upfile/opus/3_1347970366.jpg', '', 27, 0);
INSERT INTO `imgcontent` VALUES (318, '/upfile/opus/1_1347971640_small.jpg', '/upfile/opus/1_1347971640.jpg', '', 19, 0);
INSERT INTO `imgcontent` VALUES (319, '/upfile/opus/IMG_1567-_1347971855_small.jpg', '/upfile/opus/IMG_1567-_1347971855.jpg', '', 19, 0);
INSERT INTO `imgcontent` VALUES (321, '/upfile/opus/1_1348027665_small.jpg', '/upfile/opus/1_1348027665.jpg', '', 28, 0);
INSERT INTO `imgcontent` VALUES (322, '/upfile/opus/2_1348027712_small.jpg', '/upfile/opus/2_1348027712.jpg', '', 28, 0);
INSERT INTO `imgcontent` VALUES (323, '/upfile/opus/3_1348027733_small.jpg', '/upfile/opus/3_1348027733.jpg', '', 28, 0);
INSERT INTO `imgcontent` VALUES (324, '/upfile/opus/3_1348027954_small.jpg', '/upfile/opus/3_1348027954.jpg', '', 29, 0);
INSERT INTO `imgcontent` VALUES (325, '/upfile/opus/2_1348027957_small.jpg', '/upfile/opus/2_1348027957.jpg', '', 29, 0);
INSERT INTO `imgcontent` VALUES (326, '/upfile/opus/1_1348027964_small.jpg', '/upfile/opus/1_1348027964.jpg', '', 29, 0);
INSERT INTO `imgcontent` VALUES (327, '/upfile/opus/4_1348028129_small.jpg', '/upfile/opus/4_1348028129.jpg', '', 30, 9);
INSERT INTO `imgcontent` VALUES (328, '/upfile/opus/3_1348028131_small.jpg', '/upfile/opus/3_1348028131.jpg', '', 30, 8);
INSERT INTO `imgcontent` VALUES (329, '/upfile/opus/2_1348028132_small.jpg', '/upfile/opus/2_1348028132.jpg', '', 30, 0);
INSERT INTO `imgcontent` VALUES (330, '/upfile/opus/1_1348028135_small.jpg', '/upfile/opus/1_1348028135.jpg', '', 30, 6);
INSERT INTO `imgcontent` VALUES (331, '/upfile/opus/5_1348028137_small.jpg', '/upfile/opus/5_1348028137.jpg', '', 30, 7);
INSERT INTO `imgcontent` VALUES (332, '/upfile/opus/2_1348032316_small.jpg', '/upfile/opus/2_1348032316.jpg', '', 18, 0);
INSERT INTO `imgcontent` VALUES (333, '/upfile/opus/3_1348032320_small.jpg', '/upfile/opus/3_1348032320.jpg', '', 18, 0);
INSERT INTO `imgcontent` VALUES (334, '/upfile/opus/4_1348032322_small.jpg', '/upfile/opus/4_1348032322.jpg', '', 18, 0);
INSERT INTO `imgcontent` VALUES (335, '/upfile/opus/5_1348032325_small.jpg', '/upfile/opus/5_1348032325.jpg', '', 18, 0);
INSERT INTO `imgcontent` VALUES (336, '/upfile/opus/7_1348032381_small.jpg', '/upfile/opus/7_1348032381.jpg', '', 31, 0);
INSERT INTO `imgcontent` VALUES (337, '/upfile/opus/9_1348032424_small.jpg', '/upfile/opus/9_1348032424.jpg', '', 31, 0);
INSERT INTO `imgcontent` VALUES (338, '/upfile/opus/10_1348032427_small.jpg', '/upfile/opus/10_1348032427.jpg', '', 31, 0);
INSERT INTO `imgcontent` VALUES (339, '/upfile/opus/8_1348032430_small.jpg', '/upfile/opus/8_1348032430.jpg', '', 31, 0);
INSERT INTO `imgcontent` VALUES (340, '/upfile/opus/6_1348032521_small.jpg', '/upfile/opus/6_1348032521.jpg', '', 32, 0);
INSERT INTO `imgcontent` VALUES (361, '/upfile/opus/1_1348062934_small.jpg', '/upfile/opus/1_1348062934.jpg', '', 35, 0);
INSERT INTO `imgcontent` VALUES (362, '/upfile/opus/2_1348062939_small.jpg', '/upfile/opus/2_1348062939.jpg', '', 35, 0);
INSERT INTO `imgcontent` VALUES (365, '/upfile/opus/1_1348063571_small.jpg', '/upfile/opus/1_1348063571.jpg', '', 37, 0);
INSERT INTO `imgcontent` VALUES (368, '/upfile/opus/2_1348064548_small.jpg', '/upfile/opus/2_1348064548.jpg', '', 39, 0);
INSERT INTO `imgcontent` VALUES (369, '/upfile/opus/1_1348064557_small.jpg', '/upfile/opus/1_1348064557.jpg', '', 39, 0);
INSERT INTO `imgcontent` VALUES (371, '/upfile/opus/1_1348144440_small.jpg', '/upfile/opus/1_1348144440.jpg', '', 1, 0);
INSERT INTO `imgcontent` VALUES (372, '/upfile/opus/3_1348144444_small.jpg', '/upfile/opus/3_1348144444.jpg', '', 1, 0);
INSERT INTO `imgcontent` VALUES (373, '/upfile/opus/6_1348144451_small.jpg', '/upfile/opus/6_1348144451.jpg', '', 1, 0);
INSERT INTO `imgcontent` VALUES (374, '/upfile/opus/7_1348144456_small.jpg', '/upfile/opus/7_1348144456.jpg', '', 1, 0);
INSERT INTO `imgcontent` VALUES (376, '/upfile/opus/9-001_1348144607_small.jpg', '/upfile/opus/9-001_1348144607.jpg', '', 1, 0);
INSERT INTO `imgcontent` VALUES (377, '/upfile/opus/IMG_2392_1348144922_small.jpg', '/upfile/opus/IMG_2392_1348144922.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (378, '/upfile/opus/IMG_2433_1348144931_small.jpg', '/upfile/opus/IMG_2433_1348144931.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (379, '/upfile/opus/IMG_2451_1348144934_small.jpg', '/upfile/opus/IMG_2451_1348144934.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (380, '/upfile/opus/IMG_2470_1348144939_small.jpg', '/upfile/opus/IMG_2470_1348144939.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (381, '/upfile/opus/IMG_2489_1348144943_small.jpg', '/upfile/opus/IMG_2489_1348144943.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (382, '/upfile/opus/IMG_2490_1348144947_small.jpg', '/upfile/opus/IMG_2490_1348144947.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (383, '/upfile/opus/IMG_2512_1348144953_small.jpg', '/upfile/opus/IMG_2512_1348144953.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (384, '/upfile/opus/IMG_2532_1348144963_small.jpg', '/upfile/opus/IMG_2532_1348144963.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (385, '/upfile/opus/IMG_2564_1348144969_small.jpg', '/upfile/opus/IMG_2564_1348144969.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (386, '/upfile/opus/IMG_2569_1348144975_small.jpg', '/upfile/opus/IMG_2569_1348144975.jpg', '', 9, 0);
INSERT INTO `imgcontent` VALUES (388, '/upfile/opus/IMG_0002_1348146094_small.jpg', '/upfile/opus/IMG_0002_1348146094.jpg', '', 13, 0);
INSERT INTO `imgcontent` VALUES (390, '/upfile/opus/IMG_0010_1348146099_small.jpg', '/upfile/opus/IMG_0010_1348146099.jpg', '', 13, 0);
INSERT INTO `imgcontent` VALUES (391, '/upfile/opus/IMG_0013_1348146102_small.jpg', '/upfile/opus/IMG_0013_1348146102.jpg', '', 13, 0);
INSERT INTO `imgcontent` VALUES (392, '/upfile/opus/IMG_0023_1348146107_small.jpg', '/upfile/opus/IMG_0023_1348146107.jpg', '', 13, -2);
INSERT INTO `imgcontent` VALUES (393, '/upfile/opus/IMG_0094_1348146112_small.jpg', '/upfile/opus/IMG_0094_1348146112.jpg', '', 13, -1);
INSERT INTO `imgcontent` VALUES (394, '/upfile/opus/IMG_9936_1348146116_small.jpg', '/upfile/opus/IMG_9936_1348146116.jpg', '', 13, 0);
INSERT INTO `imgcontent` VALUES (397, '/upfile/opus/IMG_9976_1348146124_small.jpg', '/upfile/opus/IMG_9976_1348146124.jpg', '', 13, -3);
INSERT INTO `imgcontent` VALUES (399, '/upfile/opus/IMG_7241_1348147955_small.jpg', '/upfile/opus/IMG_7241_1348147955.jpg', '', 34, 9);
INSERT INTO `imgcontent` VALUES (400, '/upfile/opus/IMG_7019_1348147960_small.jpg', '/upfile/opus/IMG_7019_1348147960.jpg', '', 34, 0);
INSERT INTO `imgcontent` VALUES (401, '/upfile/opus/IMG_7091_1348147965_small.jpg', '/upfile/opus/IMG_7091_1348147965.jpg', '', 34, 0);
INSERT INTO `imgcontent` VALUES (402, '/upfile/opus/IMG_7105_1348147970_small.jpg', '/upfile/opus/IMG_7105_1348147970.jpg', '', 34, 4);
INSERT INTO `imgcontent` VALUES (403, '/upfile/opus/IMG_7126_1348147974_small.jpg', '/upfile/opus/IMG_7126_1348147974.jpg', '', 34, 8);
INSERT INTO `imgcontent` VALUES (404, '/upfile/opus/IMG_7185_1348147979_small.jpg', '/upfile/opus/IMG_7185_1348147979.jpg', '', 34, 0);
INSERT INTO `imgcontent` VALUES (405, '/upfile/opus/IMG_7226_1348147984_small.jpg', '/upfile/opus/IMG_7226_1348147984.jpg', '', 34, 7);
INSERT INTO `imgcontent` VALUES (406, '/upfile/opus/IMG_7260_1348147989_small.jpg', '/upfile/opus/IMG_7260_1348147989.jpg', '', 34, 6);
INSERT INTO `imgcontent` VALUES (407, '/upfile/opus/IMG_7268_1348147994_small.jpg', '/upfile/opus/IMG_7268_1348147994.jpg', '', 34, 5);
INSERT INTO `imgcontent` VALUES (408, '/upfile/opus/1_1348149688_small.jpg', '/upfile/opus/1_1348149688.jpg', '', 7, 0);
INSERT INTO `imgcontent` VALUES (409, '/upfile/opus/3_1348149694_small.jpg', '/upfile/opus/3_1348149694.jpg', '', 7, 0);
INSERT INTO `imgcontent` VALUES (410, '/upfile/opus/4_1348149700_small.jpg', '/upfile/opus/4_1348149700.jpg', '', 7, -1);
INSERT INTO `imgcontent` VALUES (411, '/upfile/opus/7_1348149705_small.jpg', '/upfile/opus/7_1348149705.jpg', '', 7, 0);
INSERT INTO `imgcontent` VALUES (412, '/upfile/opus/8_1348149709_small.jpg', '/upfile/opus/8_1348149709.jpg', '', 7, 0);
INSERT INTO `imgcontent` VALUES (425, '/upfile/opus/IMG_5917__1373561334_small.jpg', '/upfile/opus/IMG_5917__1373561334.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (426, '/upfile/opus/IMG_5964__1373561338_small.jpg', '/upfile/opus/IMG_5964__1373561338.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (428, '/upfile/opus/IMG_6496__1373561345_small.jpg', '/upfile/opus/IMG_6496__1373561345.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (429, '/upfile/opus/IMG_6500__1373561349_small.jpg', '/upfile/opus/IMG_6500__1373561349.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (430, '/upfile/opus/IMG_6537__1373561354_small.jpg', '/upfile/opus/IMG_6537__1373561354.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (433, '/upfile/opus/IMG_6862_1373561365_small.jpg', '/upfile/opus/IMG_6862_1373561365.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (434, '/upfile/opus/IMG_6877_1373561369_small.jpg', '/upfile/opus/IMG_6877_1373561369.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (436, '/upfile/opus/IMG_6924__1373561377_small.jpg', '/upfile/opus/IMG_6924__1373561377.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (437, '/upfile/opus/IMG_6958__1373561380_small.jpg', '/upfile/opus/IMG_6958__1373561380.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (443, '/upfile/opus/IMG_7222__1373561405_small.jpg', '/upfile/opus/IMG_7222__1373561405.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (444, '/upfile/opus/IMG_5450__1373634394_small.jpg', '/upfile/opus/IMG_5450__1373634394.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (445, '/upfile/opus/IMG_5604__1373634397_small.jpg', '/upfile/opus/IMG_5604__1373634397.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (446, '/upfile/opus/IMG_5618__1373634399_small.jpg', '/upfile/opus/IMG_5618__1373634399.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (448, '/upfile/opus/IMG_5680__1373634451_small.jpg', '/upfile/opus/IMG_5680__1373634451.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (449, '/upfile/opus/IMG_5685__1373634454_small.jpg', '/upfile/opus/IMG_5685__1373634454.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (450, '/upfile/opus/IMG_5697__1373634459_small.jpg', '/upfile/opus/IMG_5697__1373634459.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (451, '/upfile/opus/IMG_5707__1373634461_small.jpg', '/upfile/opus/IMG_5707__1373634461.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (452, '/upfile/opus/IMG_5799__1373634465_small.jpg', '/upfile/opus/IMG_5799__1373634465.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (454, '/upfile/opus/IMG_5802__1373634469_small.jpg', '/upfile/opus/IMG_5802__1373634469.jpg', '', 40, 0);
INSERT INTO `imgcontent` VALUES (457, '/upfile/opus/IMG_6094__1373634835_small.jpg', '/upfile/opus/IMG_6094__1373634835.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (464, '/upfile/opus/IMG_6454__1373634869_small.jpg', '/upfile/opus/IMG_6454__1373634869.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (466, '/upfile/opus/IMG_6484__1373634880_small.jpg', '/upfile/opus/IMG_6484__1373634880.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (468, '/upfile/opus/IMG_6731__1373634892_small.jpg', '/upfile/opus/IMG_6731__1373634892.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (469, '/upfile/opus/IMG_6734__1373634896_small.jpg', '/upfile/opus/IMG_6734__1373634896.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (470, '/upfile/opus/IMG_6737__1373634905_small.jpg', '/upfile/opus/IMG_6737__1373634905.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (472, '/upfile/opus/IMG_6745__1373634914_small.jpg', '/upfile/opus/IMG_6745__1373634914.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (473, '/upfile/opus/IMG_6746__1373634919_small.jpg', '/upfile/opus/IMG_6746__1373634919.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (474, '/upfile/opus/IMG_6748__1373634924_small.jpg', '/upfile/opus/IMG_6748__1373634924.jpg', '', 41, 0);
INSERT INTO `imgcontent` VALUES (476, '/upfile/opus/6e7f4dbfjw1dvvfzjqff9j__1373776156_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvfzjqff9j__1373776156.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (477, '/upfile/opus/6e7f4dbfjw1dvvfzvw3joj__1373776160_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvfzvw3joj__1373776160.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (478, '/upfile/opus/6e7f4dbfjw1dvvfzzibv3j__1373776165_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvfzzibv3j__1373776165.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (479, '/upfile/opus/6e7f4dbfjw1dvvg0fb6wuj_1373776173_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg0fb6wuj_1373776173.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (480, '/upfile/opus/6e7f4dbfjw1dvvg0gr3rrj__1373776177_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg0gr3rrj__1373776177.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (481, '/upfile/opus/6e7f4dbfjw1dvvg0m3ok2j__1373776182_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg0m3ok2j__1373776182.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (482, '/upfile/opus/6e7f4dbfjw1dvvg2bdhfbj__1373776189_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg2bdhfbj__1373776189.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (483, '/upfile/opus/6e7f4dbfjw1dvvg2r7tqgj_1373776200_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg2r7tqgj_1373776200.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (484, '/upfile/opus/6e7f4dbfjw1dvvg2r9a8wj__1373776205_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg2r9a8wj__1373776205.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (485, '/upfile/opus/6e7f4dbfjw1dvvg2wnm92j__1373776211_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg2wnm92j__1373776211.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (486, '/upfile/opus/6e7f4dbfjw1dvvg03pkjgj__1373776214_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg03pkjgj__1373776214.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (487, '/upfile/opus/6e7f4dbfjw1dvvg10mtl7j__1373776217_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg10mtl7j__1373776217.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (488, '/upfile/opus/6e7f4dbfjw1dvvg12l3frj__1373776239_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg12l3frj__1373776239.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (489, '/upfile/opus/6e7f4dbfjw1dvvg24ceqvj__1373776244_small.jpg', '/upfile/opus/6e7f4dbfjw1dvvg24ceqvj__1373776244.jpg', '', 43, 0);
INSERT INTO `imgcontent` VALUES (491, '/upfile/opus/NelliesHair_1382094721_small.jpg', '/upfile/opus/NelliesHair_1382094721.jpg', '', 44, 0);
INSERT INTO `imgcontent` VALUES (492, '/upfile/opus/1_1390797259_small.png', '/upfile/opus/1_1390797259.png', '', 45, 0);
INSERT INTO `imgcontent` VALUES (493, '/upfile/opus/2_1390797271_small.png', '/upfile/opus/2_1390797271.png', '', 45, 0);
INSERT INTO `imgcontent` VALUES (494, '/upfile/opus/3_1390797273_small.png', '/upfile/opus/3_1390797273.png', '', 45, 0);
INSERT INTO `imgcontent` VALUES (495, '/upfile/opus/4_1390797275_small.png', '/upfile/opus/4_1390797275.png', '', 45, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `imglist`
-- 

CREATE TABLE `imglist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '作品list ID',
  `title` varchar(120) NOT NULL COMMENT '作品你名称',
  `time` date NOT NULL COMMENT '创建时间',
  `author` varchar(40) NOT NULL COMMENT '作者',
  `c_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属分类',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '发布状态',
  `faceid` varchar(11) NOT NULL DEFAULT '0' COMMENT '作品封面图片id',
  `indexid` varchar(11) NOT NULL DEFAULT '0' COMMENT '首页显示图片id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- 
-- Dumping data for table `imglist`
-- 

INSERT INTO `imglist` VALUES (1, '花夏', '2012-09-17', '莫问', 1, 1, '371', '371');
INSERT INTO `imglist` VALUES (2, '泰晤士小镇', '2011-10-23', '莫问', 1, 1, '62', '62');
INSERT INTO `imglist` VALUES (3, '黑色诱惑', '2011-10-27', '莫问', 1, 1, '77', '79');
INSERT INTO `imglist` VALUES (5, '单翼', '2011-11-18', '莫问', 1, 1, '95', '95');
INSERT INTO `imglist` VALUES (7, '海棠秋雨', '2011-11-24', '莫问', 1, 1, '408', '408');
INSERT INTO `imglist` VALUES (9, '海棠飘落的日子', '2012-09-17', '莫问', 1, 1, '378', '378');
INSERT INTO `imglist` VALUES (10, '雀跃在冬季的小路上', '2012-01-12', '莫问', 1, 1, '191', '191');
INSERT INTO `imglist` VALUES (11, '绩溪油菜花海', '2012-08-18', '绩溪', 1, 1, '212', '212');
INSERT INTO `imglist` VALUES (13, '那片海', '2012-09-17', '莫问', 1, 1, '388', '388');
INSERT INTO `imglist` VALUES (14, '薰衣草', '2012-08-17', '莫问', 1, 1, '255', '255');
INSERT INTO `imglist` VALUES (15, '湘里农家', '2012-06-15', '莫问', 1, 1, '261', '261');
INSERT INTO `imglist` VALUES (16, '商业修片', '2012-09-19', '莫言', 2, 1, '291', '291');
INSERT INTO `imglist` VALUES (17, '妆容片', '2012-09-17', '莫言', 2, 1, '294', '294');
INSERT INTO `imglist` VALUES (18, '包装效果图设计', '2011-09-01', '莫问', 3, 1, '332', '332');
INSERT INTO `imglist` VALUES (19, '日落の海风', '2012-09-17', '莫问', 1, 1, '318', '318');
INSERT INTO `imglist` VALUES (21, '服装片', '2012-09-18', '莫言', 2, 1, '298', '298');
INSERT INTO `imglist` VALUES (22, '商业修片', '2012-09-18', '莫言', 2, 1, '301', '301');
INSERT INTO `imglist` VALUES (23, '商业修片', '2012-09-19', '莫言', 2, 1, '304', '304');
INSERT INTO `imglist` VALUES (24, '商业修片', '2012-09-19', '莫言', 2, 1, '308', '308');
INSERT INTO `imglist` VALUES (26, '商业修片', '2012-09-18', '莫言', 2, 1, '314', '314');
INSERT INTO `imglist` VALUES (27, '商业修片', '2012-09-18', '莫言', 2, 1, '317', '317');
INSERT INTO `imglist` VALUES (28, '网页海报设计', '2012-09-19', '莫问', 3, 1, '321', '321');
INSERT INTO `imglist` VALUES (29, '网页设计', '2012-09-19', '莫问', 3, 1, '326', '326');
INSERT INTO `imglist` VALUES (30, '网页设计', '2012-09-19', '莫问', 3, 1, '329', '329');
INSERT INTO `imglist` VALUES (31, '企业网站设计', '2011-09-04', '莫问', 3, 1, '336', '336');
INSERT INTO `imglist` VALUES (32, '企业网站设计', '2011-09-03', '莫问', 3, 1, '340', '340');
INSERT INTO `imglist` VALUES (34, '花季', '2012-08-19', '莫问', 1, 1, '399', '399');
INSERT INTO `imglist` VALUES (35, '游戏专题设计', '2012-09-18', '莫问', 3, 1, '362', '362');
INSERT INTO `imglist` VALUES (37, '游戏专题设计', '2012-09-18', '莫问', 3, 1, '365', '365');
INSERT INTO `imglist` VALUES (39, '游戏专题设计', '2012-09-18', '莫问', 3, 1, '368', '368');
INSERT INTO `imglist` VALUES (40, '秦川', '2013-05-23', '莫问', 1, 1, '445', '445');
INSERT INTO `imglist` VALUES (41, '西藏', '2013-07-11', '莫问', 1, 1, '473', '473');
INSERT INTO `imglist` VALUES (43, '绩溪', '2013-07-14', '莫问', 1, 1, '488', '488');
INSERT INTO `imglist` VALUES (44, 'NelliesHair-首页设计', '2013-10-18', '莫问', 3, 1, '491', '491');
INSERT INTO `imglist` VALUES (45, 'NelliesHair', '2014-01-26', '莫问', 3, 1, '492', '492');

-- --------------------------------------------------------

-- 
-- Table structure for table `newsclass`
-- 

CREATE TABLE `newsclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `title` varchar(25) NOT NULL COMMENT '分类名称',
  `f_id` int(11) NOT NULL COMMENT '父分类ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `newsclass`
-- 

INSERT INTO `newsclass` VALUES (2, '时间轴', 0);
INSERT INTO `newsclass` VALUES (3, '旅行', 0);
INSERT INTO `newsclass` VALUES (4, '川藏', 0);
INSERT INTO `newsclass` VALUES (5, 'Web作品', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `newscontent`
-- 

CREATE TABLE `newscontent` (
  `l_id` int(11) NOT NULL COMMENT '关联newslist ID',
  `content` text NOT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `newscontent`
-- 

INSERT INTO `newscontent` VALUES (1, '<p>\r\n	经过接近3个月的业余时间，终于将流云摄影网站系统开发完成了;网站从策划到编辑，从设计到开发，从前台到后台，各种交互效果都是我一个人完成</p>\r\n<p>\r\n	流云摄影系统v1.0，今天正式上线！</p>\r\n');
INSERT INTO `newscontent` VALUES (3, '<p>\r\n	十一南京打分行，印象分，40分（满分一百）。</p>\r\n<p>\r\n	我实在想不通，南京也算是一个现代文明城市，但为什么一路上碰到的人的道德素质水平却停这么低下。如果当时不是我把机器拿出来，估计再过一个小时也打不上车，马路上的行人，服务行业的各种南京人。我想说，南京！真他妈的！</p>\r\n');
INSERT INTO `newscontent` VALUES (36, '<p>\r\n	发布个人网站《莫问视觉》V1.0版</p>\r\n');
INSERT INTO `newscontent` VALUES (40, '<p>\r\n	《莫问视觉》V2版上线</p>\r\n<p>\r\n	重新进行整站改版和后台二次开发</p>\r\n');
INSERT INTO `newscontent` VALUES (42, '<p>\r\n	总路线：<span style="color:#b22222;"><a href="http://ditu.google.cn/maps?saddr=%E4%B8%8A%E6%B5%B7&amp;daddr=%E5%AE%9C%E6%98%8C+to:%E6%88%90%E9%83%BD+to:%E5%9B%9B%E5%B7%9D%E7%9C%81%E7%94%98%E5%AD%9C%E8%97%8F%E6%97%8F%E8%87%AA%E6%B2%BB%E5%B7%9E%E5%BA%B7%E5%AE%9A%E6%96%B0%E9%83%BD%E6%A1%A5%E9%95%87+to:%E5%9B%9B%E5%B7%9D%E7%9C%81%E7%94%98%E5%AD%9C%E8%97%8F%E6%97%8F%E8%87%AA%E6%B2%BB%E5%B7%9E%E5%B7%B4%E5%A1%98%E5%8E%BF+to:%E8%A5%BF%E8%97%8F%E8%87%AA%E6%B2%BB%E5%8C%BA%E6%98%8C%E9%83%BD%E5%B7%A6%E8%B4%A1%E5%8E%BF+to:%E8%A5%BF%E8%97%8F%E8%87%AA%E6%B2%BB%E5%8C%BA%E6%98%8C%E9%83%BD%E5%85%AB%E5%AE%BF%E5%8E%BF%E7%84%B6%E4%B9%8C%E9%95%87+to:%E8%A5%BF%E8%97%8F%E8%87%AA%E6%B2%BB%E5%8C%BA%E6%9E%97%E8%8A%9D%E6%B3%A2%E5%AF%86%E5%8E%BF+to:%E8%A5%BF%E8%97%8F%E8%87%AA%E6%B2%BB%E5%8C%BA%E6%9E%97%E8%8A%9D%E5%85%AB%E4%B8%80%E9%95%87+to:%E8%A5%BF%E8%97%8F%E8%87%AA%E6%B2%BB%E5%8C%BA%E6%8B%89%E8%90%A8+to:%E8%A5%BF%E8%97%8F%E8%87%AA%E6%B2%BB%E5%8C%BA%E7%BA%B3%E6%9C%A8%E9%94%99+to:%E9%9D%92%E6%B5%B7%E7%9C%81+to:%E7%94%98%E8%82%83%E7%9C%81%E5%85%B0%E5%B7%9E+to:%E9%99%95%E8%A5%BF%E7%9C%81%E8%A5%BF%E5%AE%89+to:%E4%B8%8A%E6%B5%B7%E5%B8%82&amp;hl=zh-CN&amp;ie=UTF8&amp;ll=32.861132,106.765137&amp;spn=15.529502,33.354492&amp;sll=27.44979,110.170898&amp;sspn=33.598937,57.084961&amp;geocode=FbmJ3AEdqIo9BykzPPWxQHCyNTGhZMMjlBKVAg%3BFX9S1AEdxxiiBiktnt2RNYWDNjET4z18739UUw%3BFSnQ0wEdWOczBikhd0QAI8XvNjECDiQuzlKGuQ%3BFbF0ygEdW6sMBilTTvt_x7fjNjGN3BzLgl9hGA%3BFcTVyQEdOk_oBSnjEZEwJeUbNzH2A7L_icS1yg%3BFc2-xAEdJe_UBSlz01PTwtsZNzGsSqq74DQOog%3BFewmwgEdjGDEBSlVhPPmyewWNzEkpR4xmLb-aQ%3BFdScxwEdaU21BSn78Ki3XGMTNzGbOs6wlYVXTg%3BFaWnxAEd8c-fBSkJuakp_KBrNzESmPx4ZVmqrg%3BFfJaxAEd-LJuBSkdVDI6YzFhNzEAuPkG744ckw%3BFV7F1QEdCTNuBSn9v-zEb4RjNzF_YAFzlpSt7g%3BFWXKLgId5woRBintC2J7l4cANzG_nbd9psvtKA%3BFUdAJgIdCWMwBimDOQyZtZBaNjFcJA1dLgBMkQ%3BFZvXCgIdjVt-Bim5F6wi6XljNjEuWJSn_WbUhQ%3BFbmJ3AEdqIo9BykzPPWxQHCyNTGhZMMjlBKVAg&amp;oq=%E4%B8%8A%E6%B5%B7&amp;brcurrent=3,0x31508e64e5c642c1:0x951daa7c349f366f,0%3B5,0,0&amp;mra=ls&amp;t=m&amp;z=6" target="_blank">上海-成都-拉萨-青海-西安-上海</a></span></p>\r\n<p>\r\n	里程：一万公里</p>\r\n<p>\r\n	用时：25天</p>\r\n');
INSERT INTO `newscontent` VALUES (43, '<p>\r\n	2013川藏自驾计划</p>\r\n');
INSERT INTO `newscontent` VALUES (44, '<p>\r\n	上海南汇滴水湖</p>\r\n<p>\r\n	2天，300公里</p>\r\n');
INSERT INTO `newscontent` VALUES (45, '<p>\r\n	安徽绩溪</p>\r\n<p>\r\n	（绩溪+九华山）3天，1200公里</p>\r\n');
INSERT INTO `newscontent` VALUES (46, '<p>\r\n	小九华山</p>\r\n<p>\r\n	（绩溪+九华山）3天，1200公里</p>\r\n');
INSERT INTO `newscontent` VALUES (47, '<p>\r\n	北京</p>\r\n');
INSERT INTO `newscontent` VALUES (48, '<p>\r\n	浙江安吉</p>\r\n<p>\r\n	2天，850公里</p>\r\n');
INSERT INTO `newscontent` VALUES (49, '<p>\r\n	组织车队：4车16人，男女比例1:1。</p>\r\n');
INSERT INTO `newscontent` VALUES (50, '<p>\r\n	青岛</p>\r\n<p>\r\n	6天，1600公里</p>\r\n');
INSERT INTO `newscontent` VALUES (51, '<p>\r\n	个人必备装备：手台、手机、手电、防高反药物及其他常规药物、登山鞋或越野跑鞋、冲锋衣、日常衣物、防晒霜、防潮垫、睡袋、二人帐、60L背包、相关防身用品。</p>\r\n');
INSERT INTO `newscontent` VALUES (52, '<p>\r\n	D1：上海 - 宜昌 （1143 公里 ）</p>\r\n');
INSERT INTO `newscontent` VALUES (53, '<p>\r\n	D2：宜昌 - 成都 （860 公里）</p>\r\n');
INSERT INTO `newscontent` VALUES (54, '<p>\r\n	D3：成都 - 雅安 （145公里）</p>\r\n<p>\r\n	上午：成都 （车辆检修保养、装备检查补给）</p>\r\n<p>\r\n	下午：成都 - &nbsp;雅安</p>\r\n');
INSERT INTO `newscontent` VALUES (55, '<p>\r\n	D4：雅安 - 雅江 &nbsp;（327公里）</p>\r\n<p>\r\n	行程：雅安－（100公里）－泸定－（84公里）－康定－（70公里）－新都桥 - （73公里）－雅江&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n');
INSERT INTO `newscontent` VALUES (56, '<p>\r\n	D5：雅江 - 巴塘 （315公里）</p>\r\n<p>\r\n	行程：雅江－122公里－理塘－193KM－巴塘</p>\r\n');
INSERT INTO `newscontent` VALUES (57, '<p>\r\n	D6：巴塘 -左贡（253km）</p>\r\n<p>\r\n	行程：巴塘－30KM－竹笆笼－68KM－芒康（海拔3899米）&mdash;12KM&mdash;拉乌山（海拔4338米）&mdash;30KM&mdash;竹卡（海拔2600 &nbsp;米）&mdash;52km&mdash;荣喜&mdash;57KM&mdash;左贡县（海拔3807米）</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	备注：巴塘前行40公里，在竹巴茏跨过金沙江进入西藏境内，中午到达进藏后的第一个县城芒康，海拔3780米，川藏公路与滇藏公路在此交汇。路是在横断山脉中穿行，经过著名的三江并流区域，跨过其中的两条大江：金沙江、澜沧江。特别是澜沧江峡谷，海拔起伏较大，路都是悬崖边开凿出来的，像一条腰带缠绕在半山腰，是川藏公路的代表。从高耸云天的山口极目远眺，川藏公路和澜沧江似两条晶亮的丝带，在千山万壑间，时隐时现。</p>\r\n');
INSERT INTO `newscontent` VALUES (58, '<p>\r\n	D7：左贡 - 然乌（330km）</p>\r\n<p>\r\n	行程：左贡（海拔3807米）&mdash;67km&mdash;邦达（海拔4200米）&mdash;100km&mdash;八宿（海拔3910米）&mdash;50km&mdash;然乌湖（海拔 &nbsp;3914米）&mdash;然乌全程291KM</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	备注：左贡前行100公里到邦达，海拔4400米的邦达是川藏南线和北线的交汇处。北通昌都，西至林芝、拉萨，是一个重要的交通枢纽，世界上海拔最高的民用机场--邦达机场就建在开阔的邦达草原上。翻越业拉山（也叫怒江山4839米），从公路往下看，峡谷里全是之字形的拐弯路，十分壮观，此景为川藏公路的地标，据说有108道拐。下山后经八宿县到然乌镇，然乌是川藏公路上的一个小镇，以然乌湖而闻名，住在湖边的宾馆，可以早起拍晨曦。</p>\r\n');
INSERT INTO `newscontent` VALUES (59, '<p>\r\n	D8：然乌 - 八一（245公里）</p>\r\n<p>\r\n	行程：然乌－来古村（来古冰川）－米堆冰川145KM－波密（海拔2737米）&mdash;86km&mdash;通麦&mdash;60km&mdash;鲁郎兵站&mdash;83km&mdash;八一镇（海拔2990米）</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	备注：米堆冰川在然乌出来几十公里，一个左转（向南）的丁字路口处。门票100。景区大门到停车场大约7公里的样子。从米堆村停车场到冰川观景台需要徒步1公里多点。</p>\r\n');
INSERT INTO `newscontent` VALUES (60, '<p>\r\n	D9：八一 &nbsp;-&nbsp;拉萨 (420公里)</p>\r\n<p>\r\n	行程：林芝（八一）&mdash;127km&mdash;工布江达&mdash;206km&mdash;墨竹工卡&mdash;68km&mdash;拉萨 。</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	备注：途中领卡限时，400公里的路要走8个小时。中途折入巴松措（也叫措高湖）高原湖泊风景区，八一镇的&ldquo;一心包子&rdquo;，距岸边大约一百米处有一座小岛名为扎西岛，传说该岛是&ldquo;空心岛&rdquo;，即岛与湖底是不相连而漂浮在湖水上的。虽然只是个传说，却让人觉得蔚为神奇，你到岛上也不妨试试在岛的某些地方跺跺脚，看是否有空心的感觉。</p>\r\n<p>\r\n	八一金达附近有测速，限速30KM。。。三个关卡，领一次限速卡（70公里1个半小时才能走完）、换一次限速卡（147公里3个小时才能走完）、进拉萨城之前的路，有一截有测速哈！就在去巴松错的岔路口，警察会站在很远的地方打手势让你停车，千万注意停车线</p>\r\n');
INSERT INTO `newscontent` VALUES (61, '<p>\r\n	项目：《智取三国》网页小游戏 （模拟游戏场景）</p>\r\n<p>\r\n	职责：web开发</p>\r\n<p>\r\n	技术：JS面向对象、HTML5+css3、Jquery、CMS系统整合</p>\r\n<p>\r\n	<a href="http://mowen-v.com/opus/sg/" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (62, '<p>\r\n	项目：《梦幻套圈圈》官方网站</p>\r\n<p>\r\n	职责：Web前端开发、策划+Axure原型设计</p>\r\n<p>\r\n	技术：DIV+CSS、Jquery</p>\r\n<p>\r\n	<a href="http://m.zygames.com/tqq/" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (63, '<p>\r\n	项目：《寻龙》官方网站</p>\r\n<p>\r\n	职责：Web前端开发、网站策划+Axure原型设计</p>\r\n<p>\r\n	技术：Jquery、Div+css、CMS系统整合、广告数据JS整合</p>\r\n<p>\r\n	其他：2011年至今，共为此网站进行过<strong>4次</strong>大改版。</p>\r\n<p>\r\n	<a href="http://xl.zygames.com" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (64, '<p>\r\n	项目：全球使命《账号管理APP-IOS版》</p>\r\n<p>\r\n	职责：程序开发、原型设计</p>\r\n<p>\r\n	技术：Object-c、IOS5.0</p>\r\n<p>\r\n	<a href="https://itunes.apple.com/cn/app/zhen-you-you-xi-guan-li-quan/id640027523?mt=8" target="_blank">到AppStore查看&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (65, '<p>\r\n	项目：《全球宝典》-全球使命专题页</p>\r\n<p>\r\n	职责：PS设计、交互设计</p>\r\n<p>\r\n	技术：DIV+CSS、Jquery</p>\r\n<p>\r\n	<a href="http://news.zygames.com/qqsmbd/#ljyx/4090.html" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (66, '<p>\r\n	项目：《全球使命2》官方网站</p>\r\n<p>\r\n	职责：Web前端开发、网站策划+Axure原型设计</p>\r\n<p>\r\n	技术：Jquery、Div+css、CMS系统整合、广告数据JS整合</p>\r\n<p>\r\n	其他：2010年至今，共为此网站进行过<strong>7次</strong>大改版。</p>\r\n<p>\r\n	<a href="http://qqsm.zygames.com/index.html" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (67, '<p>\r\n	项目：《智取三国》官方网站</p>\r\n<p>\r\n	职责：Web前端开发、网站策划+Axure原型设计</p>\r\n<p>\r\n	技术：Jquery、Div+css、CMS系统整合、广告数据JS整合</p>\r\n<p>\r\n	说明：兼容手机浏览器布局</p>\r\n<p>\r\n	<strong>电脑版网站</strong>：<a href="http://zqsg.zygames.com/index.html" target="_blank">查看网站&gt;&gt;</a></p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<img alt="" src="/upfile/images/20130719_063153.png" style="width: 100px; float: left; height: 100px;" />&nbsp;<strong> 手机版网站：</strong></p>\r\n<p>\r\n	&nbsp; 用手机扫描此二维码打开。</p>\r\n<p>\r\n	&nbsp; 也可用手机微信&ldquo;扫一扫&rdquo;</p>\r\n');
INSERT INTO `newscontent` VALUES (69, '<p>\r\n	项目：《莫问视觉》个人网站</p>\r\n<p>\r\n	职责：PS设计、交互设计、前端开发、后台开发、内容编辑</p>\r\n<p>\r\n	技术：PHP+MySQL、JS、Jquery、DIV+css</p>\r\n<p>\r\n	<a href="http://mowen-v.com/index.php" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (70, '<p>\r\n	项目：《侠客列传》官方网站</p>\r\n<p>\r\n	职责：Web前端开发</p>\r\n<p>\r\n	技术：js、Div+css、flash</p>\r\n');
INSERT INTO `newscontent` VALUES (71, '<p>\r\n	项目：《露娜》官方网站</p>\r\n<p>\r\n	职责：Web前端开发</p>\r\n<p>\r\n	技术：js、Div+css、flash</p>\r\n');
INSERT INTO `newscontent` VALUES (72, '<p>\r\n	项目：《街头篮球》官方网站</p>\r\n<p>\r\n	职责：Web前端开发</p>\r\n<p>\r\n	技术：js、Div+css、flash</p>\r\n');
INSERT INTO `newscontent` VALUES (73, '<p>\r\n	项目：《33网》视频站</p>\r\n<p>\r\n	职责：Web前端开发、评分系统、盖楼系统前端开发</p>\r\n<p>\r\n	技术：JS、DIV+CSS</p>\r\n<p>\r\n	<a href="http://33tv.com.cn/" target="_blank">查看网站&gt;&gt;</a></p>\r\n<p>\r\n	&nbsp;</p>\r\n');
INSERT INTO `newscontent` VALUES (74, '<p>\r\n	项目：《缘源保健品超市》官方网站</p>\r\n<p>\r\n	职责：ASP积分系统开发、前端开发、网页美术设计、商标设计、广告设计、产品拍摄及后期</p>\r\n<p>\r\n	技术：ASP、PS、DIV+css</p>\r\n<p>\r\n	说明：传说中的设计、开发一条龙</p>\r\n<p>\r\n	&nbsp;</p>\r\n');
INSERT INTO `newscontent` VALUES (75, '<p>\r\n	项目：《九品网络科技工作室》网站</p>\r\n<p>\r\n	职责：PHP后台开发、PS网页美术设计、网站策划、Web前端开发</p>\r\n<p>\r\n	技术：PHP+MySQL、DIV+css、Flash、PS</p>\r\n');
INSERT INTO `newscontent` VALUES (76, '<p>\r\n	项目：《角色名片生成器》</p>\r\n<p>\r\n	职责：前端开发、策划+Axure原型设计</p>\r\n<p>\r\n	技术：Js、Jquery、Div+css</p>\r\n<p>\r\n	其他：2011年至今，共为此网站进行过<strong>4次</strong>大改版。</p>\r\n<p>\r\n	<a href="/opus/mpscq/" target="_blank">前端技术/交互展示&gt;&gt;</a></p>\r\n<p>\r\n	<a href="http://sign.zygames.com/qqsm/index.html" target="_blank">查看完整网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (77, '<p>\r\n	项目：手机网页滑动切换广告</p>\r\n<p>\r\n	技术：JS</p>\r\n<p>\r\n	说明：只做了手机触摸屏的支持，页面支持所有宽度设备的展示，自动等比缩放。</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<img alt="" src="/upfile/images/20130719_061144.png" style="width: 100px; height: 100px; float: left;" /></p>\r\n<p>\r\n	<strong>&nbsp; 查看项目展示：</strong></p>\r\n<p>\r\n	&nbsp; 用手机扫描此二维码打开。</p>\r\n<p>\r\n	&nbsp; 也可用手机微信&ldquo;扫一扫&rdquo;</p>\r\n');
INSERT INTO `newscontent` VALUES (78, '<p>\r\n	项目：《NelliesHair》</p>\r\n<p>\r\n	职责：设计、Web前端开发、PHP程序开发</p>\r\n<p>\r\n	技术：PHP+Mysql、DIV+CSS、Jquery</p>\r\n<p>\r\n	<a href="http://www.nellieshair.com" target="_blank">查看网站&gt;&gt;</a></p>\r\n');
INSERT INTO `newscontent` VALUES (79, '<p>\r\n	项目：《flappyBird》网页版</p>\r\n<p>\r\n	职责：游戏分析，JS开发</p>\r\n<p>\r\n	技术：JS面向对象，DIV+CSS</p>\r\n<p>\r\n	<a href="http://mowen-v.com/opus/flappybird/" target="_blank">查看网站&gt;&gt;</a></p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<img alt="" src="/upfile/images/20140306_095422.png" style="width: 90px; height: 90px; float: left;" /></p>\r\n<p>\r\n	<strong>&nbsp; 手机上玩：</strong></p>\r\n<p>\r\n	&nbsp; 用手机扫描此二维码打开。</p>\r\n<p>\r\n	&nbsp; 也可用手机微信&ldquo;扫一扫&rdquo;</p>\r\n');

-- --------------------------------------------------------

-- 
-- Table structure for table `newslist`
-- 

CREATE TABLE `newslist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '新闻ID',
  `c_id` int(11) NOT NULL COMMENT '所属分类ID',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `time` date NOT NULL COMMENT '时间',
  `author` varchar(30) NOT NULL COMMENT '作者',
  `imgpath` varchar(120) NOT NULL COMMENT '图片地址路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 AUTO_INCREMENT=80 ;

-- 
-- Dumping data for table `newslist`
-- 

INSERT INTO `newslist` VALUES (1, 1, '流云摄影网站系统正式上线！', '2011-09-30', 'jiyu', '/upfile/snews/1_20110930121220.jpg');
INSERT INTO `newslist` VALUES (3, 1, '十月南京', '2011-10-07', 'jiyu', '/upfile/snews/1_20111007045618.jpg');
INSERT INTO `newslist` VALUES (36, 2, '建立自己的个人网站《莫问视觉》', '2011-05-25', 'jiyu', '/upfile/snews/18_20120926081316.jpg');
INSERT INTO `newslist` VALUES (40, 2, '《莫问视觉》V2版', '2012-08-25', 'jiyu', '/upfile/snews/4_20120925101040.jpg');
INSERT INTO `newslist` VALUES (42, 4, '总路线图', '2013-05-02', 'jiyu', '/upfile/snews/5_20120927102756.jpg');
INSERT INTO `newslist` VALUES (43, 4, '2013川藏自驾计划', '2013-05-01', 'jiyu', '/upfile/snews/1_20120927122345.jpg');
INSERT INTO `newslist` VALUES (44, 3, '上海南汇滴水湖', '2012-09-27', 'jiyu', '/upfile/snews/1_20120927114556.jpg');
INSERT INTO `newslist` VALUES (45, 3, '安徽绩溪', '2012-04-26', 'jiyu', '/upfile/snews/1_20120927114824.jpg');
INSERT INTO `newslist` VALUES (46, 3, '小九华山', '2012-04-27', 'jiyu', '/upfile/snews/1_20120927121903.jpg');
INSERT INTO `newslist` VALUES (47, 3, '北京', '2012-06-27', 'jiyu', '/upfile/snews/1_20120927120639.jpg');
INSERT INTO `newslist` VALUES (48, 3, '浙江安吉', '2012-08-27', 'jiyu', '/upfile/snews/1_20120927121454.jpg');
INSERT INTO `newslist` VALUES (49, 4, '车队组队', '2013-05-03', 'jiyu', '/upfile/snews/1_20120927122411.jpg');
INSERT INTO `newslist` VALUES (50, 3, '青岛', '2012-05-27', 'jiyu', '/upfile/snews/1_20120927165723.jpg');
INSERT INTO `newslist` VALUES (51, 4, '个人装备', '2013-05-04', 'jiyu', '/upfile/snews/1_20120928061320.jpg');
INSERT INTO `newslist` VALUES (52, 4, 'D1：上海 - 宜昌 ', '2013-05-06', 'jiyu', '/upfile/snews/1_20120928072837.jpg');
INSERT INTO `newslist` VALUES (53, 4, 'D2：宜昌 - 成都', '2013-05-07', 'jiyu', '/upfile/snews/1_20120928073023.jpg');
INSERT INTO `newslist` VALUES (54, 4, 'D3：成都', '2013-05-10', 'jiyu', '/upfile/snews/1_20120928080340.jpg');
INSERT INTO `newslist` VALUES (55, 4, 'D4：雅安 - 雅江  （327公里）', '2013-05-11', 'jiyu', '/upfile/snews/1_20120928080649.jpg');
INSERT INTO `newslist` VALUES (56, 4, 'D5：雅江 - 巴塘 （315公里）', '2013-05-12', 'jiyu', '/upfile/snews/1_20120928080739.jpg');
INSERT INTO `newslist` VALUES (57, 4, 'D6：巴塘 -左贡（253km）', '2013-05-13', 'jiyu', '/upfile/snews/1_20120928080833.jpg');
INSERT INTO `newslist` VALUES (58, 4, 'D7：左贡 - 然乌（330km）', '2013-05-13', 'jiyu', '/upfile/snews/1_20120928080918.jpg');
INSERT INTO `newslist` VALUES (59, 4, 'D8:然乌 - 八一（245KM）', '2013-05-13', 'jiyu', '/upfile/snews/1_20120928081028.jpg');
INSERT INTO `newslist` VALUES (60, 4, 'D9：八一  - 拉萨', '2013-05-13', 'jiyu', '');
INSERT INTO `newslist` VALUES (61, 5, '智取三国小游戏', '2013-07-08', 'jiyu', '/upfile/snews/1_20130712023047.jpg');
INSERT INTO `newslist` VALUES (62, 5, '梦幻套圈圈', '2012-12-10', 'jiyu', '/upfile/snews/2_20130711141053.jpg');
INSERT INTO `newslist` VALUES (63, 5, '寻龙', '2013-04-02', 'jiyu', '/upfile/snews/-2_20130711145427.jpg');
INSERT INTO `newslist` VALUES (64, 5, '全球使命IOS', '2013-05-15', 'jiyu', '/upfile/snews/-2_20130711145224.jpg');
INSERT INTO `newslist` VALUES (65, 5, '《全球宝典》', '2013-06-20', 'jiyu', '/upfile/snews/1_20130711145700.jpg');
INSERT INTO `newslist` VALUES (66, 5, '《全球使命2》', '2012-03-11', 'jiyu', '/upfile/snews/1_20130711150155.jpg');
INSERT INTO `newslist` VALUES (67, 5, '智取三国官网', '2013-07-06', 'jiyu', '/upfile/snews/2_20130712023059.jpg');
INSERT INTO `newslist` VALUES (69, 5, '莫问视觉', '2012-06-12', 'jiyu', '/upfile/snews/1_20130712024602.jpg');
INSERT INTO `newslist` VALUES (70, 5, '侠客列传', '2010-02-12', 'jiyu', '/upfile/snews/1_20130712120057.jpg');
INSERT INTO `newslist` VALUES (71, 5, '露娜', '2009-08-12', 'jiyu', '/upfile/snews/1_20130712120443.jpg');
INSERT INTO `newslist` VALUES (72, 5, '街头篮球', '2010-04-12', 'jiyu', '/upfile/snews/1_20130712120707.jpg');
INSERT INTO `newslist` VALUES (73, 5, '33网', '2010-02-12', 'jiyu', '/upfile/snews/1_20130712122655.jpg');
INSERT INTO `newslist` VALUES (74, 5, '缘源保健品超市', '2008-08-12', 'jiyu', '/upfile/snews/1_20130712123732.jpg');
INSERT INTO `newslist` VALUES (75, 5, '九品网络科技工作室', '2009-01-12', 'jiyu', '/upfile/snews/1_20130712125543.jpg');
INSERT INTO `newslist` VALUES (76, 5, '名片生成器', '2012-07-10', 'jiyu', '/upfile/snews/1_20130719034730.jpg');
INSERT INTO `newslist` VALUES (77, 5, '手机网页滚动广告', '2013-07-19', 'jiyu', '/upfile/snews/1_20130719060547.jpg');
INSERT INTO `newslist` VALUES (78, 5, 'NelliesHair.com', '2013-10-14', 'jiyu', '/upfile/snews/jpeg_20131014080356.jpg');
INSERT INTO `newslist` VALUES (79, 5, 'flappyBird', '2014-03-05', 'jiyu', '/upfile/snews/1_20140310183033.jpg');
