# Host: localhost  (Version: 5.5.53)
# Date: 2017-03-02 14:13:09
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "city"
#

DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `CityId` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `CityName` varchar(50) NOT NULL DEFAULT '' COMMENT '城市名称',
  `CityUrl` varchar(100) NOT NULL DEFAULT '' COMMENT '城市链接',
  `QueryNum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查询次数',
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`CityId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='城市';

#
# Data for table "city"
#

/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` VALUES (1,'艾迪生','http://www.51room.co.uk/property/rent/us/addison',0,1);
/*!40000 ALTER TABLE `city` ENABLE KEYS */;

#
# Structure for table "room51room"
#

DROP TABLE IF EXISTS `room51room`;
CREATE TABLE `room51room` (
  `ApartmentId` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ApartmentName` varchar(50) NOT NULL DEFAULT '' COMMENT '公寓名称',
  `ApartmentDesc` varchar(1000) NOT NULL DEFAULT '' COMMENT '公寓介绍',
  `CityId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市id',
  `SchoolId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学校id',
  `Addr` varchar(200) NOT NULL DEFAULT '' COMMENT '公寓地址',
  `Price` double(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '公寓价格',
  `MinLease` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '最短租期',
  `MaxLease` tinyint(3) unsigned NOT NULL DEFAULT '24' COMMENT '最长租期',
  `Longitude` varchar(12) NOT NULL DEFAULT '' COMMENT '公寓所在经度',
  `Latitude` varchar(12) NOT NULL DEFAULT '' COMMENT '公寓所在纬度',
  `Attr` varchar(100) NOT NULL DEFAULT '' COMMENT '公寓参数',
  `BedroomNum` tinyint(3) unsigned DEFAULT '0' COMMENT '卧室数量',
  `BathroomNum` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '浴室数量',
  `Notice` varchar(1000) NOT NULL DEFAULT '' COMMENT '预订须知',
  `View` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `Advert` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '广告位',
  `AdvertSort` int(11) NOT NULL DEFAULT '0' COMMENT '广告位排序',
  `AddTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `UpdateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `AttrStr` varchar(255) NOT NULL DEFAULT '' COMMENT '公寓参数文本',
  `RoomNo` varchar(50) NOT NULL DEFAULT '' COMMENT '51room房源编号',
  `RoomDevice` varchar(100) NOT NULL DEFAULT '' COMMENT '51room右上角房间设备',
  PRIMARY KEY (`ApartmentId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='51room公寓信息';

#
# Data for table "room51room"
#

/*!40000 ALTER TABLE `room51room` DISABLE KEYS */;
INSERT INTO `room51room` VALUES (1,'Furnished 2-Bedroom Apartment at E Lake St & N Mic','公寓能够满足您高层次的入住需要，提供全套家具。<br/><br/>设施<br/>阳台<br/>室内游泳池<br/>健身中心<br/>瑜伽房<br/>响应人员<br/>代客泊车（需额外费用）<br/>商务中心<br/>网络<br/>电视<br/><br/>社区<br/>几条街区之外即有高档餐厅、剧院、商店等。<br/><br/>入住起始：2016-09-16<br/>租期1-12个月<br/>押金$500<br/>租金包含网络、暖气费用<br/>最多可接纳6人入住<br/>可饲养猫狗类宠物',1,0,'151 North Michigan Avenue                 Addison                   IL       \t        \t        60601',5040.00,0,24,'41.9308','-87.9847','',2,2,'第1步：提交预订信息<br>第2步：会在1-2个工作日内发给您预订确认信<br>第3步：<spanstyle=\\\"line-height:18.5714px;\\\">支付$500押金，</span>接受电子租房合同<br>第4步：支付剩余的押金和第一个月房租<div>第5布：确认如何办理入住<br><div><br></div><div>价格为参考价格会出现少许浮动详细请联系客服（可能更低）</div></div>',0,0,0,1488422329,0,1,'公共车库,洗衣房,有线电视,网络,暖气,电,水,空调,健身房,门卫,电梯,游泳池','房源编号：US-05219','卧室 x 2 卫浴 x 2');
/*!40000 ALTER TABLE `room51room` ENABLE KEYS */;

#
# Structure for table "roomfacility"
#

DROP TABLE IF EXISTS `roomfacility`;
CREATE TABLE `roomfacility` (
  `AttrId` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `AttrName` varchar(20) NOT NULL DEFAULT '' COMMENT '设施名称',
  PRIMARY KEY (`AttrId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='公寓设施';

#
# Data for table "roomfacility"
#

/*!40000 ALTER TABLE `roomfacility` DISABLE KEYS */;
/*!40000 ALTER TABLE `roomfacility` ENABLE KEYS */;

#
# Structure for table "roompic"
#

DROP TABLE IF EXISTS `roompic`;
CREATE TABLE `roompic` (
  `PicId` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ApartmentId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公寓id',
  `picPath` varchar(30) NOT NULL DEFAULT '' COMMENT '图片路径',
  PRIMARY KEY (`PicId`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='公寓图片';

#
# Data for table "roompic"
#

/*!40000 ALTER TABLE `roompic` DISABLE KEYS */;
INSERT INTO `roompic` VALUES (1,1,'201703021033288275.jpg'),(2,1,'201703021033298535.jpg'),(3,1,'201703021033308560.jpg'),(4,1,'201703021033318391.jpg'),(5,1,'201703021034361089.jpg'),(6,1,'201703021034582056.jpg'),(7,1,'201703021035114951.jpg'),(8,1,'201703021035159322.jpg'),(9,1,'201703021035178581.jpg'),(10,1,'201703021035392451.jpg'),(11,1,'201703021035413504.jpg'),(12,1,'201703021036422922.jpg'),(13,1,'201703021037429094.jpg'),(14,1,'201703021038447187.jpg'),(15,1,'201703021038457313.jpg'),(16,1,'201703021038462412.jpg');
/*!40000 ALTER TABLE `roompic` ENABLE KEYS */;
