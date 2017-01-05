/*
Navicat MySQL Data Transfer

Source Server         : lagou
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : blog26

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2017-01-02 11:38:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for arts
-- ----------------------------
DROP TABLE IF EXISTS `arts`;
CREATE TABLE `arts` (
  `art_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `cat_id` int(10) unsigned NOT NULL COMMENT '栏目id',
  `title` char(25) NOT NULL COMMENT '文章标题',
  `content` text NOT NULL COMMENT '文章内容',
  `thumb` char(60) NOT NULL COMMENT '文章配图缩略图',
  `pic` char(60) NOT NULL COMMENT '文章内容配图',
  `commentnum` int(10) unsigned NOT NULL COMMENT '文章评论数',
  `pubtime` int(10) unsigned NOT NULL COMMENT '文章发表时间',
  PRIMARY KEY (`art_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Table structure for cats
-- ----------------------------
DROP TABLE IF EXISTS `cats`;
CREATE TABLE `cats` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目id',
  `catname` char(25) NOT NULL COMMENT '栏目名称',
  `artnum` int(10) unsigned NOT NULL COMMENT '该栏目下的文章数',
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='栏目表';

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `art_id` int(10) unsigned NOT NULL COMMENT '文章id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `nickname` char(25) NOT NULL COMMENT '评论者昵称',
  `email` char(25) DEFAULT NULL COMMENT '评论者邮箱',
  `content` varchar(100) NOT NULL COMMENT '用户评论内容',
  `pubtime` int(10) unsigned NOT NULL COMMENT '发表时间',
  `ip` int(10) unsigned NOT NULL COMMENT '评论者ip',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签id',
  `art_id` int(10) unsigned NOT NULL COMMENT '文章id',
  `tagname` char(25) NOT NULL COMMENT '标签名',
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='标签表';

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` char(25) NOT NULL COMMENT '用户名',
  `nickname` char(25) NOT NULL COMMENT '用户昵称',
  `email` char(25) NOT NULL COMMENT '注册邮箱',
  `salt` char(10) NOT NULL COMMENT '密码的盐',
  `password` char(32) NOT NULL COMMENT '登陆密码',
  `logintime` int(10) unsigned NOT NULL COMMENT '登陆时间',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';
