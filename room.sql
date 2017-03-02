# Host: 127.0.0.1  (Version 5.7.10)
# Date: 2017-03-02 17:32:45
# Generator: MySQL-Front 6.0  (Build 1.53)

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
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='城市';

#
# Data for table "city"
#


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
  `Facility` varchar(500) NOT NULL DEFAULT '' COMMENT '公寓设施字符串 1,2,3',
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
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='51room公寓信息';

#
# Data for table "room51room"
#


#
# Structure for table "roomfacility"
#

DROP TABLE IF EXISTS `roomfacility`;
CREATE TABLE `roomfacility` (
  `FacilityId` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `FacilityName` varchar(20) NOT NULL DEFAULT '' COMMENT '设施名称',
  `AddTime` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`FacilityId`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='公寓设施';

#
# Data for table "roomfacility"
#


#
# Structure for table "roompic"
#

DROP TABLE IF EXISTS `roompic`;
CREATE TABLE `roompic` (
  `PicId` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ApartmentId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公寓id',
  `picPath` varchar(30) NOT NULL DEFAULT '' COMMENT '图片路径',
  PRIMARY KEY (`PicId`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='公寓图片';

#
# Data for table "roompic"
#

