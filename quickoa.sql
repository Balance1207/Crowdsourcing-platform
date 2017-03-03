/*
Navicat MySQL Data Transfer

Source Server         : mzmtest
Source Server Version : 50540
Source Host           : 192.168.0.129:3306
Source Database       : quickoa

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-11-28 14:49:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for u_department
-- ----------------------------
DROP TABLE IF EXISTS `u_department`;
CREATE TABLE `u_department` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `d_name` varchar(30) DEFAULT NULL COMMENT '部门名称',
  `d_number` varchar(6) NOT NULL COMMENT '部门编号',
  `d_address` varchar(50) NOT NULL COMMENT '部门地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='部门表';

-- ----------------------------
-- Records of u_department
-- ----------------------------
INSERT INTO `u_department` VALUES ('29', 'test1', '', '');
INSERT INTO `u_department` VALUES ('28', 'test', '', '');
INSERT INTO `u_department` VALUES ('10', '1010bu', '', '');
INSERT INTO `u_department` VALUES ('25', 'fsdf', '', '');
INSERT INTO `u_department` VALUES ('9', '99999bu', '', '');
INSERT INTO `u_department` VALUES ('27', 'gfdsfsd', '', '');
INSERT INTO `u_department` VALUES ('7', '77777bu', '', '');
INSERT INTO `u_department` VALUES ('8', '88888bu', '', '');

-- ----------------------------
-- Table structure for u_department_jobs
-- ----------------------------
DROP TABLE IF EXISTS `u_department_jobs`;
CREATE TABLE `u_department_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `j_name` varchar(255) NOT NULL COMMENT '职务名称',
  `j_did` varchar(255) NOT NULL COMMENT '所在部门',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='职务表';

-- ----------------------------
-- Records of u_department_jobs
-- ----------------------------
INSERT INTO `u_department_jobs` VALUES ('59', 'test2', '28');
INSERT INTO `u_department_jobs` VALUES ('58', 'test1', '28');
INSERT INTO `u_department_jobs` VALUES ('32', '32号职务', '7');
INSERT INTO `u_department_jobs` VALUES ('11', '11', '7');
INSERT INTO `u_department_jobs` VALUES ('10', '什么十', '10');
INSERT INTO `u_department_jobs` VALUES ('9', '什么九', '9');
INSERT INTO `u_department_jobs` VALUES ('8', '什么八', '8');
INSERT INTO `u_department_jobs` VALUES ('60', 'test1', '29');
INSERT INTO `u_department_jobs` VALUES ('52', '5321', '25');
INSERT INTO `u_department_jobs` VALUES ('7', '什么七', '7');
INSERT INTO `u_department_jobs` VALUES ('53', 'fsdfds', '25');
INSERT INTO `u_department_jobs` VALUES ('57', 'fsdfds', '27');

-- ----------------------------
-- Table structure for u_item_comission
-- ----------------------------
DROP TABLE IF EXISTS `u_item_comission`;
CREATE TABLE `u_item_comission` (
  `id` int(90) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `i_id` int(30) NOT NULL COMMENT '项目的id 项目名称',
  `type` int(3) NOT NULL COMMENT '类别 提成对象 职务',
  `percent` float(4,2) NOT NULL COMMENT '所占百分比',
  `keep` int(1) NOT NULL DEFAULT '1' COMMENT '是否有效 1 有效，0，无效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='提成规则表';

-- ----------------------------
-- Records of u_item_comission
-- ----------------------------
INSERT INTO `u_item_comission` VALUES ('1', '19', '7', '12.00', '0');
INSERT INTO `u_item_comission` VALUES ('2', '19', '8', '32.00', '0');
INSERT INTO `u_item_comission` VALUES ('3', '19', '8', '25.00', '0');
INSERT INTO `u_item_comission` VALUES ('4', '19', '8', '24.00', '1');
INSERT INTO `u_item_comission` VALUES ('17', '30', '58', '0.00', '1');
INSERT INTO `u_item_comission` VALUES ('15', '30', '10', '50.00', '1');
INSERT INTO `u_item_comission` VALUES ('7', '19', '9', '50.00', '1');
INSERT INTO `u_item_comission` VALUES ('8', '19', '7', '30.00', '1');
INSERT INTO `u_item_comission` VALUES ('16', '30', '59', '0.00', '1');
INSERT INTO `u_item_comission` VALUES ('10', '26', '9', '20.00', '1');
INSERT INTO `u_item_comission` VALUES ('11', '19', '10', '5.00', '1');
INSERT INTO `u_item_comission` VALUES ('12', '27', '8', '5.00', '1');
INSERT INTO `u_item_comission` VALUES ('13', '26', '32', '0.00', '1');
INSERT INTO `u_item_comission` VALUES ('14', '19', '11', '21.00', '0');
INSERT INTO `u_item_comission` VALUES ('18', '19', '57', '12.00', '1');
INSERT INTO `u_item_comission` VALUES ('19', '7', '8', '0.00', '1');
INSERT INTO `u_item_comission` VALUES ('20', '7', '7', '0.00', '1');

-- ----------------------------
-- Table structure for u_item_develop_log
-- ----------------------------
DROP TABLE IF EXISTS `u_item_develop_log`;
CREATE TABLE `u_item_develop_log` (
  `id` int(90) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `i_id` int(30) NOT NULL COMMENT '项目id',
  `u_id` int(30) NOT NULL COMMENT '操作人员id',
  `content` varchar(255) NOT NULL COMMENT '内容 ',
  `type` int(3) NOT NULL COMMENT '动作类别，如项目概述，留言，项目变更  item_develop_type 表',
  `Date` datetime NOT NULL COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=262 DEFAULT CHARSET=utf8 COMMENT='进度表';

-- ----------------------------
-- Records of u_item_develop_log
-- ----------------------------
INSERT INTO `u_item_develop_log` VALUES ('1', '19', '19', '添加队员:张三,并分配任务：zzzz', '3', '2016-06-15 18:22:50');
INSERT INTO `u_item_develop_log` VALUES ('2', '19', '19', '添加队员:lily,并分配任务：lllll', '3', '2016-06-15 18:22:50');
INSERT INTO `u_item_develop_log` VALUES ('3', '19', '19', '添加职位:前端,提成比例是：12%', '5', '2016-06-15 18:22:50');
INSERT INTO `u_item_develop_log` VALUES ('4', '19', '19', '添加队员:lily,并分配任务：llii', '3', '2016-06-15 18:25:08');
INSERT INTO `u_item_develop_log` VALUES ('5', '19', '19', '添加队员:zz,并分配任务：zzyy', '3', '2016-06-15 18:25:08');
INSERT INTO `u_item_develop_log` VALUES ('6', '19', '19', '添加职位:后端,提成比例是：32%', '5', '2016-06-15 18:25:08');
INSERT INTO `u_item_develop_log` VALUES ('7', '19', '19', '本项目删除【后端】职务。', '5', '2016-06-15 18:28:55');
INSERT INTO `u_item_develop_log` VALUES ('8', '19', '19', '删除队员:lily', '3', '2016-06-15 18:28:55');
INSERT INTO `u_item_develop_log` VALUES ('9', '19', '19', '删除队员:zz', '3', '2016-06-15 18:28:55');
INSERT INTO `u_item_develop_log` VALUES ('10', '19', '19', '添加队员:lily,并分配任务：llii', '3', '2016-06-15 18:30:20');
INSERT INTO `u_item_develop_log` VALUES ('11', '19', '19', '添加队员:zz,并分配任务：zaza', '3', '2016-06-15 18:30:20');
INSERT INTO `u_item_develop_log` VALUES ('12', '19', '19', '添加职位:后端,提成比例是：25%', '5', '2016-06-15 18:30:20');
INSERT INTO `u_item_develop_log` VALUES ('13', '19', '19', '本项目删除【后端】职务。', '5', '2016-06-15 18:36:04');
INSERT INTO `u_item_develop_log` VALUES ('14', '19', '19', '删除队员:lily', '3', '2016-06-15 18:36:04');
INSERT INTO `u_item_develop_log` VALUES ('15', '19', '19', '删除队员:zz', '3', '2016-06-15 18:36:04');
INSERT INTO `u_item_develop_log` VALUES ('16', '19', '19', '添加队员:lily,并分配任务：lili', '3', '2016-06-15 18:38:24');
INSERT INTO `u_item_develop_log` VALUES ('17', '19', '19', '添加队员:zz,并分配任务：zaza', '3', '2016-06-15 18:38:24');
INSERT INTO `u_item_develop_log` VALUES ('18', '19', '19', '添加职位:后端,提成比例是：4%', '5', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('19', '19', '19', '【2-8】\"提成比例\"发生变化,由3.00变成3。\\n', '4', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('20', '19', '19', '【12-8】\"提成比例\"发生变化,由3.00变成3。\\n', '4', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('21', '19', '19', '添加队员:lily,并分配任务：lili', '3', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('22', '19', '19', '添加队员:zz,并分配任务：zaza', '3', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('23', '19', '19', '添加职位:后端,提成比例是：4%', '5', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('24', '19', '19', '添加队员:seven,并分配任务：sasa', '3', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('25', '19', '19', '添加职位:设计,提成比例是：8%', '5', '2016-06-15 18:38:25');
INSERT INTO `u_item_develop_log` VALUES ('26', '19', '19', '删除队员:张三', '3', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('27', '19', '19', '添加队员:yy,并分配任务：ytyt', '3', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('28', '19', '19', '删除队员:zz', '3', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('29', '19', '19', '删除队员:lily', '3', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('30', '19', '19', '【2-8】\"提成比例\"发生变化,由3.00变成3。\\n', '4', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('31', '19', '19', '【12-8】\"提成比例\"发生变化,由3.00变成3。\\n', '4', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('32', '19', '19', '本项目删除【设计】职务。', '5', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('33', '19', '19', '删除队员:seven', '3', '2016-06-15 18:47:30');
INSERT INTO `u_item_develop_log` VALUES ('34', '9', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-15 20:52:18');
INSERT INTO `u_item_develop_log` VALUES ('35', '9', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-15 20:55:50');
INSERT INTO `u_item_develop_log` VALUES ('36', '9', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-15 20:57:13');
INSERT INTO `u_item_develop_log` VALUES ('37', '19', '19', '项目名称变化,由测试项目变成测试项目1。\\n', '2', '2016-06-16 10:12:08');
INSERT INTO `u_item_develop_log` VALUES ('38', '19', '19', '项目名称变化,由测试项目1变成测试项目fff。\\n项目状态变化,由5变成4。\\n开发周期变化,由3m变成5w。\\n', '2', '2016-06-16 02:21:09');
INSERT INTO `u_item_develop_log` VALUES ('39', '19', '19', '添加队员:kk,并分配任务：fsdfdssdfsddfsd', '3', '2016-06-16 02:23:57');
INSERT INTO `u_item_develop_log` VALUES ('40', '19', '19', '添加队员:seven,并分配任务：fsfsdfsdfds', '3', '2016-06-16 02:23:57');
INSERT INTO `u_item_develop_log` VALUES ('41', '19', '19', '添加职位:设计,提成比例是：50%', '5', '2016-06-16 02:23:57');
INSERT INTO `u_item_develop_log` VALUES ('42', '19', '19', '本项目删除【前端】职务。', '5', '2016-06-16 02:24:56');
INSERT INTO `u_item_develop_log` VALUES ('43', '19', '19', '删除队员:lily', '3', '2016-06-16 02:24:56');
INSERT INTO `u_item_develop_log` VALUES ('44', '19', '19', '删除项目<b>测试项目fff</b>成功!', '2', '2016-06-16 02:32:02');
INSERT INTO `u_item_develop_log` VALUES ('45', '17', '19', '删除项目<b>er</b>成功!', '2', '2016-06-16 02:32:13');
INSERT INTO `u_item_develop_log` VALUES ('46', '19', '19', '项目状态变化,由4变成5。\\n开发周期变化,由5w变成5期。\\n', '2', '2016-06-16 02:35:11');
INSERT INTO `u_item_develop_log` VALUES ('47', '19', '19', '项目名称变化,由测试项目fff变成测试项目fffd。\\n', '2', '2016-06-16 02:37:54');
INSERT INTO `u_item_develop_log` VALUES ('48', '19', '19', '开发周期变化,由5期变成5w。\\n', '2', '2016-06-16 10:46:07');
INSERT INTO `u_item_develop_log` VALUES ('49', '19', '19', '开发周期变化,由5w变成5期。\\n', '2', '2016-06-16 02:55:04');
INSERT INTO `u_item_develop_log` VALUES ('50', '19', '19', '开发周期变化,由5期变成51期。\\n', '2', '2016-06-16 02:55:33');
INSERT INTO `u_item_develop_log` VALUES ('51', '19', '19', '开发周期变化,由51期变成20期。\\n', '2', '2016-06-16 02:56:16');
INSERT INTO `u_item_develop_log` VALUES ('52', '19', '19', '开发周期变化,由2w变成2d。\\n', '2', '2016-06-16 11:14:25');
INSERT INTO `u_item_develop_log` VALUES ('53', '19', '19', '开发周期变化,由2d变成2w。\\n', '2', '2016-06-16 11:14:38');
INSERT INTO `u_item_develop_log` VALUES ('54', '19', '19', '项目名称变化,由测试项目fffd变成测试项目fffdf。\\n', '2', '2016-06-16 03:22:09');
INSERT INTO `u_item_develop_log` VALUES ('55', '19', '19', '项目名称变化,由测试项目fffdf变成测试项目fffu。\\n', '2', '2016-06-16 03:30:19');
INSERT INTO `u_item_develop_log` VALUES ('56', '19', '19', '项目名称变化,由测试项目fffu变成测试项目。\\n开发周期变化,由2w变成22。\\n', '2', '2016-06-16 03:30:34');
INSERT INTO `u_item_develop_log` VALUES ('57', '19', '19', '开发周期变化,由22变成22w。\\n', '2', '2016-06-16 11:36:34');
INSERT INTO `u_item_develop_log` VALUES ('58', '19', '19', '开发周期变化,由22w变成22m。\\n', '2', '2016-06-16 11:36:48');
INSERT INTO `u_item_develop_log` VALUES ('59', '19', '19', '开发周期变化,由22m变成22d。\\n', '2', '2016-06-16 11:37:20');
INSERT INTO `u_item_develop_log` VALUES ('60', '19', '19', '添加队员:张三,并分配任务：fdsfsd', '3', '2016-06-16 03:38:08');
INSERT INTO `u_item_develop_log` VALUES ('61', '19', '19', '添加队员:lily,并分配任务：fsdfsdfsdf', '3', '2016-06-16 03:38:08');
INSERT INTO `u_item_develop_log` VALUES ('62', '19', '19', '添加队员:kk,并分配任务：', '3', '2016-06-16 03:38:08');
INSERT INTO `u_item_develop_log` VALUES ('63', '19', '19', '添加职位:前端,提成比例是：50%', '5', '2016-06-16 03:38:08');
INSERT INTO `u_item_develop_log` VALUES ('64', '21', '19', '发布项目<b>测试项目1</b>成功!', '1', '2016-06-16 07:23:16');
INSERT INTO `u_item_develop_log` VALUES ('65', '22', '19', '发布项目<b>测试项目2</b>成功!', '1', '2016-06-16 07:27:41');
INSERT INTO `u_item_develop_log` VALUES ('66', '23', '19', '发布项目<b>测试项目3</b>成功!', '1', '2016-06-16 07:44:32');
INSERT INTO `u_item_develop_log` VALUES ('67', '24', '19', '发布项目<b>测试项目3</b>成功!', '1', '2016-06-16 07:48:57');
INSERT INTO `u_item_develop_log` VALUES ('68', '25', '19', '发布项目<b>cs</b>成功!', '1', '2016-06-16 16:10:43');
INSERT INTO `u_item_develop_log` VALUES ('69', '1', '19', '删除项目<b>kk</b>成功!', '2', '2016-06-16 18:24:54');
INSERT INTO `u_item_develop_log` VALUES ('70', '1', '19', '删除项目<b>kk</b>成功!', '2', '2016-06-16 18:26:03');
INSERT INTO `u_item_develop_log` VALUES ('71', '2', '19', '删除项目<b>22</b>成功!', '2', '2016-06-16 18:26:03');
INSERT INTO `u_item_develop_log` VALUES ('72', '1', '19', '恢复项目<b>kk</b>成功!', '2', '2016-06-16 18:35:11');
INSERT INTO `u_item_develop_log` VALUES ('73', '2', '19', '恢复项目<b>22</b>成功!', '2', '2016-06-16 18:35:11');
INSERT INTO `u_item_develop_log` VALUES ('74', '13', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-17 14:59:24');
INSERT INTO `u_item_develop_log` VALUES ('75', '16', '19', '删除项目<b>WS</b>成功!', '2', '2016-06-17 15:00:23');
INSERT INTO `u_item_develop_log` VALUES ('76', '18', '19', '删除项目<b>uuu</b>成功!', '2', '2016-06-17 15:00:23');
INSERT INTO `u_item_develop_log` VALUES ('77', '19', '19', '本项目删除【设计】职务。', '5', '2016-06-17 07:21:00');
INSERT INTO `u_item_develop_log` VALUES ('78', '19', '19', '删除队员:kk', '3', '2016-06-17 07:21:00');
INSERT INTO `u_item_develop_log` VALUES ('79', '19', '19', '删除队员:seven', '3', '2016-06-17 07:21:00');
INSERT INTO `u_item_develop_log` VALUES ('80', '25', '19', '删除项目<b>cs</b>成功!', '2', '2016-06-17 15:53:36');
INSERT INTO `u_item_develop_log` VALUES ('81', '19', '19', '开发周期变化,由22d变成26d。\\n', '2', '2016-06-18 16:01:52');
INSERT INTO `u_item_develop_log` VALUES ('82', '24', '19', '删除项目<b>测试项目4</b>成功!', '2', '2016-06-20 19:17:15');
INSERT INTO `u_item_develop_log` VALUES ('83', '24', '19', '恢复项目<b>测试项目4</b>成功!', '2', '2016-06-20 19:26:36');
INSERT INTO `u_item_develop_log` VALUES ('84', '25', '19', '恢复项目<b>cs</b>成功!', '2', '2016-06-20 19:26:54');
INSERT INTO `u_item_develop_log` VALUES ('85', '20', '19', '恢复项目<b>ert</b>成功!', '2', '2016-06-20 19:26:54');
INSERT INTO `u_item_develop_log` VALUES ('86', '25', '19', '删除项目<b>cs</b>成功!', '2', '2016-06-20 19:27:40');
INSERT INTO `u_item_develop_log` VALUES ('87', '20', '19', '删除项目<b>ert</b>成功!', '2', '2016-06-20 19:27:40');
INSERT INTO `u_item_develop_log` VALUES ('88', '26', '19', '发布项目<b>测试项目n</b>成功!', '1', '2016-06-21 11:41:47');
INSERT INTO `u_item_develop_log` VALUES ('89', '26', '19', '开发周期变化,由27d变成20d。\\n', '2', '2016-06-21 11:48:24');
INSERT INTO `u_item_develop_log` VALUES ('90', '19', '19', '项目名称变化,由测试项目变成测试项目f。\\n', '2', '2016-06-22 07:06:59');
INSERT INTO `u_item_develop_log` VALUES ('91', '19', '19', '项目需求变化,由三季度四季度经济的角度变成三季度四季度经济的角度fdsfsdf。\\n', '2', '2016-06-22 07:20:15');
INSERT INTO `u_item_develop_log` VALUES ('92', '19', '19', '金额变化,由121212变成1212121。\\n', '2', '2016-06-22 07:20:50');
INSERT INTO `u_item_develop_log` VALUES ('93', '26', '19', '添加队员:kk,并分配任务：', '3', '2016-06-22 15:25:08');
INSERT INTO `u_item_develop_log` VALUES ('94', '26', '19', '添加职位:前端,提成比例是：20%', '5', '2016-06-22 15:25:08');
INSERT INTO `u_item_develop_log` VALUES ('95', '26', '19', '添加队员:kk,并分配任务：sdsdfd', '3', '2016-06-22 15:26:11');
INSERT INTO `u_item_develop_log` VALUES ('96', '26', '19', '添加职位:设计,提成比例是：20%', '5', '2016-06-22 15:26:11');
INSERT INTO `u_item_develop_log` VALUES ('97', '26', '19', '【3-7】\"提成比例\"发生变化,由12.00变成12。\\n【3-7】\"具体任务\"发生变化,由变成rgtdfhbgdfgv。\\n', '4', '2016-06-22 15:26:32');
INSERT INTO `u_item_develop_log` VALUES ('98', '19', '19', '开发周期变化,由26d变成26m。\\n', '2', '2016-06-22 08:06:02');
INSERT INTO `u_item_develop_log` VALUES ('99', '10', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('100', '9', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('101', '8', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('102', '12', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('103', '11', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('104', '13', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('105', '14', '19', '恢复项目<b>WS</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('106', '16', '19', '恢复项目<b>WS</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('107', '15', '19', '恢复项目<b>WS</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('108', '17', '19', '恢复项目<b>er</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('109', '18', '19', '恢复项目<b>uuu</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('110', '25', '19', '恢复项目<b>cs</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('111', '20', '19', '恢复项目<b>ert</b>成功!', '2', '2016-06-24 07:19:34');
INSERT INTO `u_item_develop_log` VALUES ('112', '25', '19', '删除项目<b>cs</b>成功!', '2', '2016-06-24 07:19:46');
INSERT INTO `u_item_develop_log` VALUES ('113', '22', '19', '删除项目<b>测试项目2</b>成功!', '2', '2016-06-24 07:19:59');
INSERT INTO `u_item_develop_log` VALUES ('114', '24', '19', '删除项目<b>测试项目4</b>成功!', '2', '2016-06-24 07:22:24');
INSERT INTO `u_item_develop_log` VALUES ('115', '24', '19', '恢复项目<b>测试项目4</b>成功!', '2', '2016-06-24 07:22:35');
INSERT INTO `u_item_develop_log` VALUES ('116', '27', '19', '发布项目<b>项目6.24</b>成功!', '1', '2016-06-24 07:23:24');
INSERT INTO `u_item_develop_log` VALUES ('117', '19', '2', '添加队员:tt', '3', '2016-06-24 15:29:23');
INSERT INTO `u_item_develop_log` VALUES ('118', '19', '2', '添加职位:技术经理,提成比例是：5%', '5', '2016-06-24 15:29:23');
INSERT INTO `u_item_develop_log` VALUES ('119', '27', '19', '添加队员:张三', '3', '2016-06-24 07:28:01');
INSERT INTO `u_item_develop_log` VALUES ('120', '27', '19', '添加队员:tt', '3', '2016-06-24 07:28:01');
INSERT INTO `u_item_develop_log` VALUES ('121', '27', '19', '添加队员:zz', '3', '2016-06-24 07:28:01');
INSERT INTO `u_item_develop_log` VALUES ('122', '27', '19', '添加职位:后端,提成比例是：5%', '5', '2016-06-24 07:28:01');
INSERT INTO `u_item_develop_log` VALUES ('123', '27', '19', '删除项目<b>项目6.24</b>成功!', '2', '2016-06-25 06:45:59');
INSERT INTO `u_item_develop_log` VALUES ('124', '27', '19', '恢复项目<b>项目6.24</b>成功!', '2', '2016-06-25 08:24:57');
INSERT INTO `u_item_develop_log` VALUES ('125', '28', '19', '发布项目<b>测试测试测试mmmm</b>成功!', '1', '2016-06-25 17:48:28');
INSERT INTO `u_item_develop_log` VALUES ('126', '26', '19', '添加队员:lily', '3', '2016-06-27 01:36:27');
INSERT INTO `u_item_develop_log` VALUES ('127', '26', '19', '添加队员:姓名f', '3', '2016-06-27 01:36:27');
INSERT INTO `u_item_develop_log` VALUES ('128', '26', '19', '添加职位:test2,提成比例是：0%', '5', '2016-06-27 01:36:27');
INSERT INTO `u_item_develop_log` VALUES ('129', '19', '2', '添加队员:yy,并分配任务：dss', '3', '2016-06-27 12:38:27');
INSERT INTO `u_item_develop_log` VALUES ('130', '19', '2', '添加职位:11,提成比例是：21%', '5', '2016-06-27 12:38:27');
INSERT INTO `u_item_develop_log` VALUES ('131', '29', '19', '发布项目<b>fdsfdfds</b>成功!', '1', '2016-06-27 07:09:33');
INSERT INTO `u_item_develop_log` VALUES ('132', '30', '19', '发布项目<b>111111</b>成功!', '1', '2016-06-27 07:46:03');
INSERT INTO `u_item_develop_log` VALUES ('133', '31', '19', '发布项目<b>se</b>成功!', '1', '2016-06-27 07:53:14');
INSERT INTO `u_item_develop_log` VALUES ('134', '32', '19', '发布项目<b>wsedwe</b>成功!', '1', '2016-06-27 07:54:40');
INSERT INTO `u_item_develop_log` VALUES ('135', '33', '19', '发布项目<b>ddd</b>成功!', '1', '2016-06-27 07:55:36');
INSERT INTO `u_item_develop_log` VALUES ('136', '34', '19', '发布项目<b>dsgd</b>成功!', '1', '2016-06-27 07:56:20');
INSERT INTO `u_item_develop_log` VALUES ('137', '34', '19', '删除项目<b>dsgd</b>成功!', '2', '2016-06-27 17:29:39');
INSERT INTO `u_item_develop_log` VALUES ('138', '33', '19', '删除项目<b>ddd</b>成功!', '2', '2016-06-27 17:29:39');
INSERT INTO `u_item_develop_log` VALUES ('139', '32', '19', '删除项目<b>wsedwe</b>成功!', '2', '2016-06-27 17:29:39');
INSERT INTO `u_item_develop_log` VALUES ('140', '31', '19', '删除项目<b>se</b>成功!', '2', '2016-06-27 17:29:39');
INSERT INTO `u_item_develop_log` VALUES ('141', '30', '19', '删除项目<b>111111</b>成功!', '2', '2016-06-27 17:29:39');
INSERT INTO `u_item_develop_log` VALUES ('142', '29', '19', '删除项目<b>fdsfdfds</b>成功!', '2', '2016-06-27 17:29:39');
INSERT INTO `u_item_develop_log` VALUES ('143', '27', '19', '删除项目<b>项目6.24</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('144', '26', '19', '删除项目<b>测试项目n</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('145', '24', '19', '删除项目<b>测试项目4</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('146', '23', '19', '删除项目<b>测试项目3</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('147', '21', '19', '删除项目<b>测试项目1</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('148', '20', '19', '删除项目<b>ert</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('149', '19', '19', '删除项目<b>测试项目f</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('150', '18', '19', '删除项目<b>uuu</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('151', '17', '19', '删除项目<b>er</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('152', '16', '19', '删除项目<b>WS</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('153', '15', '19', '删除项目<b>WS</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('154', '14', '19', '删除项目<b>WS</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('155', '13', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('156', '12', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('157', '11', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('158', '10', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('159', '9', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('160', '8', '19', '删除项目<b>ww</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('161', '28', '19', '删除项目<b>测试测试测试mmmm</b>成功!', '2', '2016-06-27 09:29:15');
INSERT INTO `u_item_develop_log` VALUES ('162', '33', '19', '恢复项目<b>ddd</b>成功!', '2', '2016-06-27 09:30:44');
INSERT INTO `u_item_develop_log` VALUES ('163', '34', '19', '恢复项目<b>dsgd</b>成功!', '2', '2016-06-27 09:30:44');
INSERT INTO `u_item_develop_log` VALUES ('164', '32', '19', '恢复项目<b>wsedwe</b>成功!', '2', '2016-06-27 09:30:44');
INSERT INTO `u_item_develop_log` VALUES ('165', '31', '19', '恢复项目<b>se</b>成功!', '2', '2016-06-27 09:30:44');
INSERT INTO `u_item_develop_log` VALUES ('166', '34', '19', '删除项目<b>dsgd</b>成功!', '2', '2016-06-27 09:30:54');
INSERT INTO `u_item_develop_log` VALUES ('167', '33', '19', '删除项目<b>ddd</b>成功!', '2', '2016-06-27 09:30:54');
INSERT INTO `u_item_develop_log` VALUES ('168', '32', '19', '删除项目<b>wsedwe</b>成功!', '2', '2016-06-27 09:30:54');
INSERT INTO `u_item_develop_log` VALUES ('169', '31', '19', '删除项目<b>se</b>成功!', '2', '2016-06-27 09:30:54');
INSERT INTO `u_item_develop_log` VALUES ('170', '12', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('171', '13', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('172', '11', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('173', '10', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('174', '8', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('175', '9', '19', '恢复项目<b>ww</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('176', '15', '19', '恢复项目<b>WS</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('177', '14', '19', '恢复项目<b>WS</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('178', '16', '19', '恢复项目<b>WS</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('179', '17', '19', '恢复项目<b>er</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('180', '19', '19', '恢复项目<b>测试项目f</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('181', '18', '19', '恢复项目<b>uuu</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('182', '20', '19', '恢复项目<b>ert</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('183', '21', '19', '恢复项目<b>测试项目1</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('184', '23', '19', '恢复项目<b>测试项目3</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('185', '22', '19', '恢复项目<b>测试项目2</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('186', '24', '19', '恢复项目<b>测试项目4</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('187', '25', '19', '恢复项目<b>cs</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('188', '26', '19', '恢复项目<b>测试项目n</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('189', '27', '19', '恢复项目<b>项目6.24</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('190', '28', '19', '恢复项目<b>测试测试测试mmmm</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('191', '30', '19', '恢复项目<b>111111</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('192', '29', '19', '恢复项目<b>fdsfdfds</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('193', '31', '19', '恢复项目<b>se</b>成功!', '2', '2016-06-27 09:31:50');
INSERT INTO `u_item_develop_log` VALUES ('194', '19', '19', '添加队员:姓名f', '3', '2016-06-27 18:29:45');
INSERT INTO `u_item_develop_log` VALUES ('195', '19', '19', '本项目删除【11】职务。', '5', '2016-06-27 18:29:45');
INSERT INTO `u_item_develop_log` VALUES ('196', '19', '19', '删除队员:yy', '3', '2016-06-27 18:29:45');
INSERT INTO `u_item_develop_log` VALUES ('197', '30', '19', '开发周期变化,由88d变成88y。\\n', '2', '2016-07-01 08:42:09');
INSERT INTO `u_item_develop_log` VALUES ('198', '19', '19', '项目名称变化,由测试项目f变成111111。\\n金额变化,由1212121变成12333333。\\n合同日期变化,由2016-05-25 00:00:00变成2016-06-27 00:00:00。\\n项目需求变化,由三季度四季度经济的角度fdsfsdf变成FSFSDFS。\\n项目状态变化,由5变成1。\\n开发周期变化,由26m变成88w。\\n', '2', '2016-07-01 09:25:14');
INSERT INTO `u_item_develop_log` VALUES ('199', '19', '0', '项目名称变化,由111111变成erer。\\n', '2', '2016-07-05 17:42:48');
INSERT INTO `u_item_develop_log` VALUES ('200', '0', '2', '用户：lily申请加入erer。参与什么十', '1', '2016-07-07 16:35:43');
INSERT INTO `u_item_develop_log` VALUES ('201', '19', '2', '用户：lily申请加入erer。参与什么十', '1', '2016-07-07 16:36:37');
INSERT INTO `u_item_develop_log` VALUES ('202', '19', '2', '用户：lily申请加入erer。参与什么十', '1', '2016-07-07 16:37:31');
INSERT INTO `u_item_develop_log` VALUES ('203', '19', '2', '用户：lily申请加入erer。参与什么七', '1', '2016-07-07 16:48:42');
INSERT INTO `u_item_develop_log` VALUES ('204', '19', '2', '用户：lily申请加入erer。参与什么十', '1', '2016-07-07 17:35:54');
INSERT INTO `u_item_develop_log` VALUES ('205', '19', '19', '用户：姓名f申请加入erer。参与什么十', '1', '2016-07-08 03:24:32');
INSERT INTO `u_item_develop_log` VALUES ('206', '19', '2', '【什么七】提成比例发生变化,由50.00变成30。\\n', '5', '2016-07-08 15:41:06');
INSERT INTO `u_item_develop_log` VALUES ('207', '19', '2', '【什么八】提成比例发生变化,由4.00变成24。\\n', '5', '2016-07-08 15:44:31');
INSERT INTO `u_item_develop_log` VALUES ('208', '19', '2', '删除队员:姓名f', '3', '2016-07-08 15:46:08');
INSERT INTO `u_item_develop_log` VALUES ('209', '19', '19', '用户：姓名f申请加入erer。参与什么十', '1', '2016-07-08 09:49:54');
INSERT INTO `u_item_develop_log` VALUES ('210', '19', '19', '项目名称变化,由erer变成ererfff。\\n项目需求变化,由FSFSDFS变成fsdffdsfdsfd12s1f212sd1fsd。\\n', '2', '2016-07-11 10:36:09');
INSERT INTO `u_item_develop_log` VALUES ('211', '19', '19', '项目名称变化,由ererfff变成ererfffffddf。\\n项目需求变化,由fsdffdsfdsfd12s1f212sd1fsd变成111111111111111。\\n', '2', '2016-07-11 10:37:05');
INSERT INTO `u_item_develop_log` VALUES ('212', '19', '19', '项目需求变化,由111111111111111变成。\\n', '2', '2016-07-11 10:46:06');
INSERT INTO `u_item_develop_log` VALUES ('213', '19', '19', '项目需求变化,由变成777777777777。\\n', '2', '2016-07-11 10:48:55');
INSERT INTO `u_item_develop_log` VALUES ('214', '19', '0', '项目需求变化,由777777777777变成tyutyutut。\\n', '2', '2016-07-11 19:13:56');
INSERT INTO `u_item_develop_log` VALUES ('215', '19', '0', '项目需求变化,由tyutyutut变成tyutyututhello,word!。\\n', '2', '2016-07-11 19:14:20');
INSERT INTO `u_item_develop_log` VALUES ('220', '19', '19', '项目需求变化,由<p>hello,<img src=\"/Upload/34488770a8dcb67b6710ec9c6aa95444.jpg\" alt=\"large_1owY_3303g132095[1]\" style=\"max-width: 100%;\"></p><p><br></p>变成<p>hello,<img src=\"/Upload/34488770a8dcb67b6710ec9c6aa95444.jpg\" alt=\"la', '2', '2016-07-12 03:30:18');
INSERT INTO `u_item_develop_log` VALUES ('221', '19', '0', '项目需求变化。', '2', '2016-07-12 15:01:03');
INSERT INTO `u_item_develop_log` VALUES ('222', '19', '0', '项目需求变化。', '2', '2016-07-12 15:06:15');
INSERT INTO `u_item_develop_log` VALUES ('223', '19', '0', '项目需求变化。', '2', '2016-07-12 15:09:47');
INSERT INTO `u_item_develop_log` VALUES ('224', '19', '0', '项目需求变化。', '2', '2016-07-12 15:40:03');
INSERT INTO `u_item_develop_log` VALUES ('225', '19', '19', '项目需求变化。', '2', '2016-07-12 15:48:16');
INSERT INTO `u_item_develop_log` VALUES ('226', '19', '19', '项目需求变化。', '2', '2016-07-12 08:02:12');
INSERT INTO `u_item_develop_log` VALUES ('227', '19', '19', '用户：姓名f申请加入ererfffffddf项目。参与什么七', '3', '2016-07-12 08:03:33');
INSERT INTO `u_item_develop_log` VALUES ('228', '19', '19', '项目需求变化。', '2', '2016-07-12 08:15:43');
INSERT INTO `u_item_develop_log` VALUES ('229', '19', '0', '项目需求变化。', '2', '2016-07-12 16:30:51');
INSERT INTO `u_item_develop_log` VALUES ('230', '30', '19', '项目需求变化。开发周期变化,由88y变成88d。', '2', '2016-07-12 10:15:31');
INSERT INTO `u_item_develop_log` VALUES ('231', '30', '19', '添加队员:lily', '3', '2016-07-12 10:36:49');
INSERT INTO `u_item_develop_log` VALUES ('232', '30', '19', '添加队员:kk', '3', '2016-07-12 10:36:49');
INSERT INTO `u_item_develop_log` VALUES ('233', '30', '19', '添加职位:什么十,提成比例是：50%', '5', '2016-07-12 10:36:49');
INSERT INTO `u_item_develop_log` VALUES ('234', '30', '19', '添加队员:zz', '3', '2016-07-12 10:37:28');
INSERT INTO `u_item_develop_log` VALUES ('235', '30', '19', '添加职位:test2,提成比例是：%', '5', '2016-07-12 10:37:28');
INSERT INTO `u_item_develop_log` VALUES ('236', '30', '19', '添加队员:kk', '3', '2016-07-12 10:38:03');
INSERT INTO `u_item_develop_log` VALUES ('237', '30', '19', '添加队员:tt', '3', '2016-07-12 10:38:03');
INSERT INTO `u_item_develop_log` VALUES ('238', '30', '19', '添加队员:yy', '3', '2016-07-12 10:38:03');
INSERT INTO `u_item_develop_log` VALUES ('239', '30', '19', '添加职位:test1,提成比例是：%', '5', '2016-07-12 10:38:03');
INSERT INTO `u_item_develop_log` VALUES ('240', '30', '19', '用户：姓名f申请加入111111项目。参与test1', '3', '2016-07-12 10:48:53');
INSERT INTO `u_item_develop_log` VALUES ('241', '30', '19', '用户：姓名f申请加入111111项目。参与test2', '3', '2016-07-12 10:48:57');
INSERT INTO `u_item_develop_log` VALUES ('242', '30', '19', '用户：姓名f申请加入111111项目。参与什么十', '3', '2016-07-12 10:48:59');
INSERT INTO `u_item_develop_log` VALUES ('243', '19', '19', '用户：姓名f申请加入ererfffffddf项目。参与什么十', '3', '2016-07-13 07:08:33');
INSERT INTO `u_item_develop_log` VALUES ('244', '19', '19', '用户：姓名f申请加入ererfffffddf项目。参与什么十', '3', '2016-07-13 07:08:35');
INSERT INTO `u_item_develop_log` VALUES ('245', '19', '19', '用户：姓名f申请加入ererfffffddf项目。参与什么十', '3', '2016-07-13 07:08:40');
INSERT INTO `u_item_develop_log` VALUES ('246', '19', '19', '用户：姓名f申请加入ererfffffddf项目。参与什么十', '3', '2016-07-13 07:08:44');
INSERT INTO `u_item_develop_log` VALUES ('247', '19', '19', '用户：姓名f申请加入ererfffffddf项目。参与什么十', '3', '2016-07-13 07:11:27');
INSERT INTO `u_item_develop_log` VALUES ('248', '19', '19', '添加队员:kk', '3', '2016-07-13 07:24:53');
INSERT INTO `u_item_develop_log` VALUES ('249', '19', '19', '添加职位:fsdfds,提成比例是：12%', '5', '2016-07-13 07:24:53');
INSERT INTO `u_item_develop_log` VALUES ('250', '7', '2', 'ww项目添加职位:什么七。', '1', '2016-07-13 17:16:03');
INSERT INTO `u_item_develop_log` VALUES ('251', '19', '2', '用户：lily申请加入ererfffffddf项目。参与fsdfds', '3', '2016-07-13 17:35:29');
INSERT INTO `u_item_develop_log` VALUES ('252', '19', '2', '用户：姓名f成功加入111111项目。参与', '3', '2016-07-14 12:13:12');
INSERT INTO `u_item_develop_log` VALUES ('253', '19', '2', '用户：姓名f撤出111111项目的', '3', '2016-07-14 12:14:34');
INSERT INTO `u_item_develop_log` VALUES ('254', '19', '2', '用户：姓名f成功加入111111项目。参与', '3', '2016-07-14 12:21:14');
INSERT INTO `u_item_develop_log` VALUES ('255', '19', '2', '用户：姓名f撤出111111项目的', '3', '2016-07-14 12:22:00');
INSERT INTO `u_item_develop_log` VALUES ('256', '19', '19', '用户：姓名f加入111111项目，参与申请失败!', '3', '2016-07-14 06:42:36');
INSERT INTO `u_item_develop_log` VALUES ('257', '19', '19', '用户：姓名f加入111111项目，参与申请失败!', '3', '2016-07-14 06:43:52');
INSERT INTO `u_item_develop_log` VALUES ('258', '19', '19', '用户：姓名f加入111111项目，参与申请失败!', '3', '2016-07-14 06:45:37');
INSERT INTO `u_item_develop_log` VALUES ('259', '19', '2', '用户：姓名f成功加入111111项目。参与', '3', '2016-07-14 15:05:09');
INSERT INTO `u_item_develop_log` VALUES ('260', '19', '2', '用户：姓名f离开111111项目的的工作。', '3', '2016-07-14 15:05:24');
INSERT INTO `u_item_develop_log` VALUES ('261', '19', '2', '用户：lily申请加入ererfffffddf项目。参与什么九', '3', '2016-07-14 15:18:46');

-- ----------------------------
-- Table structure for u_item_develop_type
-- ----------------------------
DROP TABLE IF EXISTS `u_item_develop_type`;
CREATE TABLE `u_item_develop_type` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '类别名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of u_item_develop_type
-- ----------------------------
INSERT INTO `u_item_develop_type` VALUES ('1', '项目概述');
INSERT INTO `u_item_develop_type` VALUES ('2', '项目变更');
INSERT INTO `u_item_develop_type` VALUES ('3', '队员变更');
INSERT INTO `u_item_develop_type` VALUES ('4', '任务变更');
INSERT INTO `u_item_develop_type` VALUES ('5', '提成变更');

-- ----------------------------
-- Table structure for u_item_status_type
-- ----------------------------
DROP TABLE IF EXISTS `u_item_status_type`;
CREATE TABLE `u_item_status_type` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='项目状态表';

-- ----------------------------
-- Records of u_item_status_type
-- ----------------------------
INSERT INTO `u_item_status_type` VALUES ('1', '未审核');
INSERT INTO `u_item_status_type` VALUES ('2', '审核');
INSERT INTO `u_item_status_type` VALUES ('3', '分析');
INSERT INTO `u_item_status_type` VALUES ('4', '组队');
INSERT INTO `u_item_status_type` VALUES ('5', '开发');
INSERT INTO `u_item_status_type` VALUES ('6', '完成');

-- ----------------------------
-- Table structure for u_item_task
-- ----------------------------
DROP TABLE IF EXISTS `u_item_task`;
CREATE TABLE `u_item_task` (
  `id` int(90) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `i_id` int(30) NOT NULL COMMENT '项目名称id',
  `u_id` int(30) NOT NULL COMMENT '队员id 即users表的id',
  `type` int(3) NOT NULL COMMENT '对象类别  职位',
  `duty` varchar(255) NOT NULL COMMENT '任务具体内容',
  `percent` float(3,1) NOT NULL COMMENT '佣金百分比',
  `stay` int(1) NOT NULL DEFAULT '1' COMMENT '是否在项目中  1在，0 被踢走了',
  `completion` float(4,2) NOT NULL COMMENT '完成任务百分比',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '佣金结算 1 结算 0 未结算',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='任务表';

-- ----------------------------
-- Records of u_item_task
-- ----------------------------
INSERT INTO `u_item_task` VALUES ('1', '19', '1', '7', 'zzzz', '3.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('2', '19', '2', '7', 'lllll', '2.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('3', '19', '2', '8', 'llii', '5.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('4', '19', '12', '8', 'zzyy', '4.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('5', '19', '2', '8', 'llii', '8.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('6', '19', '12', '8', 'zaza', '6.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('7', '19', '2', '8', 'lili', '3.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('8', '19', '12', '8', 'zaza', '3.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('9', '19', '2', '8', 'lili', '3.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('10', '19', '12', '8', 'zaza', '3.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('11', '19', '19', '7', 'sasa', '4.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('12', '19', '13', '8', 'ytyt', '6.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('13', '19', '3', '9', 'fsdfdssdfsddfsd', '30.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('14', '19', '19', '9', 'fsfsdfsdfds', '20.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('15', '19', '1', '7', 'fdsfsd', '20.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('16', '19', '2', '7', 'fsdfsdfsdf', '30.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('17', '19', '3', '7', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('18', '26', '3', '7', '', '12.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('19', '26', '3', '9', 'sdsdfd', '20.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('20', '19', '6', '10', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('21', '27', '1', '8', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('22', '27', '6', '8', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('23', '27', '12', '8', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('24', '26', '2', '32', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('25', '26', '19', '32', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('26', '19', '13', '11', 'dss', '5.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('27', '19', '19', '8', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('28', '30', '2', '10', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('29', '30', '3', '10', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('30', '30', '12', '59', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('31', '30', '3', '58', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('32', '30', '6', '58', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('33', '30', '13', '58', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('34', '19', '3', '57', '', '0.0', '1', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('35', '30', '19', '58', '', '0.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('36', '30', '19', '58', '', '0.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('37', '30', '19', '58', '', '0.0', '0', '0.00', '0');
INSERT INTO `u_item_task` VALUES ('38', '30', '19', '58', '', '0.0', '1', '0.00', '0');

-- ----------------------------
-- Table structure for u_item_task_apply
-- ----------------------------
DROP TABLE IF EXISTS `u_item_task_apply`;
CREATE TABLE `u_item_task_apply` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `i_id` int(11) NOT NULL COMMENT '项目id',
  `u_id` int(11) NOT NULL COMMENT '用户id',
  `type` int(1) NOT NULL COMMENT ' 申请的职位',
  `flag` int(1) NOT NULL COMMENT '0 申请提交 1申请成功  2 申请失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of u_item_task_apply
-- ----------------------------
INSERT INTO `u_item_task_apply` VALUES ('1', '19', '2', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('2', '19', '2', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('3', '19', '2', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('4', '19', '2', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('5', '19', '2', '7', '0');
INSERT INTO `u_item_task_apply` VALUES ('6', '19', '2', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('7', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('8', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('9', '19', '19', '7', '0');
INSERT INTO `u_item_task_apply` VALUES ('10', '30', '19', '58', '2');
INSERT INTO `u_item_task_apply` VALUES ('11', '30', '19', '59', '2');
INSERT INTO `u_item_task_apply` VALUES ('12', '30', '19', '10', '2');
INSERT INTO `u_item_task_apply` VALUES ('13', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('14', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('15', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('16', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('17', '19', '19', '10', '0');
INSERT INTO `u_item_task_apply` VALUES ('18', '19', '2', '57', '0');

-- ----------------------------
-- Table structure for u_items
-- ----------------------------
DROP TABLE IF EXISTS `u_items`;
CREATE TABLE `u_items` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT COMMENT '项目id 主键',
  `i_name` varchar(255) NOT NULL COMMENT '项目名称 必填',
  `money` varchar(255) NOT NULL COMMENT '金额  必填',
  `contractDate` datetime NOT NULL COMMENT '合同日期 必填',
  `requirement` text NOT NULL COMMENT '需求描述 必填',
  `status` varchar(255) NOT NULL COMMENT '状态信息',
  `publisher` varchar(255) NOT NULL COMMENT '发布人 必填',
  `releaseDate` datetime NOT NULL COMMENT '发布日期 ',
  `days` varchar(255) NOT NULL COMMENT '开发周期 必填',
  `img` varchar(255) DEFAULT NULL COMMENT '二维码',
  `users` varchar(255) DEFAULT NULL COMMENT '成员',
  `online` int(1) DEFAULT NULL COMMENT '项目停用 1未停用 0停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='项目信息表';

-- ----------------------------
-- Records of u_items
-- ----------------------------
INSERT INTO `u_items` VALUES ('1', 'kk', '10000', '2016-06-06 00:00:00', 'dfsfjosifosfi', '1', '2', '2016-04-06 13:23:34', '50', '1.jpg', '1,2,6', '1');
INSERT INTO `u_items` VALUES ('2', '22', '', '0000-00-00 00:00:00', '', '1', '2', '0000-00-00 00:00:00', '', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('3', 'yy', '2000', '2016-06-25 00:00:00', '需求描述据办法要求，支付账户将严格实行实名管理，并按照三类支付账户分级管理。如果用户身份验证情况未达到办法所规定标准，会影响支付账户部分功能使用。通俗地讲，届时未实名的支付宝和微信用户，既不能在账户里存钱，也不能使用余额转账或消费。', '1', '2', '2016-05-25 14:13:51', '30', '1.jpg', null, '0');
INSERT INTO `u_items` VALUES ('4', 'yy', '2000', '2016-06-25 00:00:00', '需求描述据办法要求，支付账户将严格实行实名管理，并按照三类支付账户分级管理。如果用户身份验证情况未达到办法所规定标准，会影响支付账户部分功能使用。通俗地讲，届时未实名的支付宝和微信用户，既不能在账户里存钱，也不能使用余额转账或消费。', '1', '1', '2016-05-25 14:13:51', '30', '1.jpg', null, '0');
INSERT INTO `u_items` VALUES ('5', 'yyu', '2000', '2016-06-25 00:00:00', '需求描述据办法要求，支付账户将严格实行实名管理，并按照三类支付账户分级管理。如果用户身份验证情况未达到办法所规定标准，会影响支付账户部分功能使用。通俗地讲，届时未实名的支付宝和微信用户，既不能在账户里存钱，也不能使用余额转账或消费。', '1', '1', '2016-05-25 14:13:51', '35', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('6', 'yy1', '2000', '2016-06-25 00:00:00', '需求描述据办法要求，支付账户将严格实行实名管理，并按照三类支付账户分级管理。如果用户身份验证情况未达到办法所规定标准，会影响支付账户部分功能使用。通俗地讲，届时未实名的支付宝和微信用户，既不能在账户里存钱，也不能使用余额转账或消费。', '1', '1', '2016-05-25 14:13:51', '30', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('7', 'ww', '123', '2016-06-01 00:00:00', 'asfdsfs师德师风', '2', '2', '2016-06-01 14:37:50', '15d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('8', 'ww', '123', '2016-06-01 00:00:00', 'afda的说法都是', '2', '19', '2016-06-01 14:41:41', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('9', 'ww', '123', '2016-06-01 00:00:00', 'afda的说法都是', '2', '19', '2016-06-01 14:42:35', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('10', 'ww', '123', '2016-06-01 00:00:00', 'afda的说法都是', '2', '19', '2016-06-01 15:17:38', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('11', 'ww', '123', '2016-06-01 00:00:00', 'afda的说法都是', '2', '19', '2016-06-01 15:18:32', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('12', 'ww', '123', '2016-06-01 00:00:00', 'afda的说法都是', '2', '19', '2016-06-01 15:19:06', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('13', 'ww', '12333', '2016-06-02 00:00:00', 'DE', '2', '19', '2016-06-01 15:21:18', '12d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('14', 'WS', '21', '2016-06-01 00:00:00', 'SDAS', '1', '19', '2016-06-01 15:22:51', '34d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('15', 'WS', '21', '2016-06-01 00:00:00', 'SDAS', '1', '19', '2016-06-01 15:22:55', '34d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('16', 'WS', '21', '2016-06-01 00:00:00', 'SDAS', '1', '19', '2016-06-01 15:23:08', '34d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('17', 'er', '12345', '2016-06-01 00:00:00', 'SCX', '1', '19', '2016-06-01 15:26:32', '34d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('18', 'uuu', '50000', '2016-05-25 00:00:00', '什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼', '2', '19', '2016-06-01 15:43:25', '3m', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('19', 'ererfffffddf', '12333333', '2016-06-27 00:00:00', '<p><img src=\"3e21c05f52d304fee2b0e5c95075ca6f.jpg\" alt=\"avatar\" style=\"max-width: 100%; width: 123px; height: 165px;\" class=\"\"><br></p><p><br></p>', '1', '19', '2016-06-01 18:01:09', '88w', '1.jpg', '3,19,6,2,1,13,12', '1');
INSERT INTO `u_items` VALUES ('20', 'ert', '123456', '2016-06-02 00:00:00', 'xhdfxhgfjnhfg', '2', '19', '2016-06-02 14:41:58', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('21', '测试项目1', '20000', '2016-06-16 00:00:00', '空间积分多少快乐可都是附近饭店数据库里放的是会计', '4', '19', '2016-06-16 07:23:16', '3d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('22', '测试项目2', '50000', '2016-06-16 00:00:00', '发送到发送到', '5', '19', '2016-06-16 07:27:41', '8d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('23', '测试项目3', '200000', '2016-06-16 00:00:00', '放松放松的风格上的', '1', '19', '2016-06-16 07:44:32', '80d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('24', '测试项目4', '200000', '2016-06-16 00:00:00', '豆腐干豆腐干豆腐干豆腐干', '1', '19', '2016-06-16 07:48:57', '80d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('25', 'cs', '12', '2016-06-16 00:00:00', 'sdswd', '2', '19', '2016-06-16 16:10:43', '14d', '1.jpg', null, '1');
INSERT INTO `u_items` VALUES ('26', '测试项目n', '3214', '2016-06-15 00:00:00', '人之初性本善', '1', '19', '2016-06-21 11:41:47', '20d', '1.jpg', '19,2,3', '1');
INSERT INTO `u_items` VALUES ('27', '项目6.24', '50000', '2016-06-24 00:00:00', '你猜', '2', '19', '2016-06-24 07:23:24', '3m', null, '12,6,1', '1');
INSERT INTO `u_items` VALUES ('28', '测试测试测试mmmm', '12312', '2016-06-25 00:00:00', '收费的死法斯蒂芬拍摄的结果反馈vjdfjdsp', '1', '19', '2016-06-25 17:48:28', '26d', 'item28.png', null, '1');
INSERT INTO `u_items` VALUES ('29', 'fdsfdfds', '41254121', '2016-06-27 00:00:00', 'fdsfdsfs', '1', '19', '2016-06-27 07:09:32', '12d', 'item29.png', null, '1');
INSERT INTO `u_items` VALUES ('30', '111111', '12333333', '2016-06-27 00:00:00', '', '1', '19', '2016-06-27 07:46:03', '88d', 'item30.png', '13,6,3,12,2', '1');
INSERT INTO `u_items` VALUES ('31', 'se', '123', '2016-06-27 00:00:00', 'wqrwer', '1', '19', '2016-06-27 07:53:14', '12d', 'item31.png', null, '1');
INSERT INTO `u_items` VALUES ('32', 'wsedwe', '123', '2016-06-27 00:00:00', 'ewrtwerw', '3', '19', '2016-06-27 07:54:40', '122d', 'item32.png', null, '0');
INSERT INTO `u_items` VALUES ('33', 'ddd', '456', '2016-06-27 00:00:00', 'cfdsas', '1', '19', '2016-06-27 07:55:35', '23d', 'item33.png', null, '0');
INSERT INTO `u_items` VALUES ('34', 'dsgd', '1234', '2016-06-27 00:00:00', 'gsedg', '1', '19', '2016-06-27 07:56:20', '23d', 'item34.png', null, '0');

-- ----------------------------
-- Table structure for u_users
-- ----------------------------
DROP TABLE IF EXISTS `u_users`;
CREATE TABLE `u_users` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) NOT NULL COMMENT '呢称',
  `photo` varchar(255) DEFAULT NULL COMMENT '头像',
  `qq` varchar(255) DEFAULT NULL COMMENT 'QQ号码',
  `tel` varchar(255) NOT NULL COMMENT '联系方式',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `department` varchar(255) NOT NULL COMMENT '所属部门',
  `registerDate` datetime NOT NULL COMMENT '注册时间或入职时间',
  `user_number` varchar(255) NOT NULL COMMENT '员工编号',
  `job` varchar(255) NOT NULL COMMENT '职务/任职',
  `salary` int(10) NOT NULL COMMENT '工资',
  `email` varchar(255) DEFAULT NULL COMMENT '邮件',
  `weixin` varchar(255) DEFAULT NULL COMMENT '微信号',
  `level` int(2) DEFAULT NULL COMMENT '用户级别 1--超级管理员 2-- 管理员 3-- 普通的人员',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tel` (`tel`) USING BTREE COMMENT '手机号'
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of u_users
-- ----------------------------
INSERT INTO `u_users` VALUES ('1', '张三', null, null, '13345678904', 'a02a0bd6c48cfaaa74a20dcefc3b8cf6', '9,7,10,28', '2016-05-16 09:11:20', 'cwb_0001', '9,7,10,58', '0', '984456784@qq.com', null, null);
INSERT INTO `u_users` VALUES ('2', 'lily', '9b55e4adb896f8690608745c5362d26d.png', '987765678', '13281870917', 'a02a0bd6c48cfaaa74a20dcefc3b8cf6', '8,28,10,7', '2016-05-14 13:17:44', '000000001', '8,59,10,32', '9000', '987765678@qq.com', '987765678', null);
INSERT INTO `u_users` VALUES ('3', 'kk', '', null, '18281965072', 'a02a0bd6c48cfaaa74a20dcefc3b8cf6', '25,9,27,7,28,10', '2016-05-10 13:17:38', '11111111111', '52,9,57,11,58,10', '6000', '987654367@qq.com', null, null);
INSERT INTO `u_users` VALUES ('6', 'tt', 'b843c32bb4e0dcebdaaa876bbd9c2f8f.jpg', null, '13383734567', '179a804f76a512d43bceccb605cc8d24', '25,7,28', '2016-05-17 13:17:09', '0000000002', '52,7,58', '10000', '8989@qq.com', null, null);
INSERT INTO `u_users` VALUES ('12', 'zz', 'd474d5f451e9a04ce6ad7c22222b98d1.jpg', null, '13398221801', '179a804f76a512d43bceccb605cc8d24', '28', '2016-05-23 16:08:22', '0009', '59', '0', '8767898766@qq.com', null, null);
INSERT INTO `u_users` VALUES ('13', 'yy', '8b55d37c77add8c16eb54bd5c44128c9.jpg', null, '13456789561', '179a804f76a512d43bceccb605cc8d24', '25,28', '2016-05-23 16:11:49', '00008', '53,58', '0', '1234567@qq.com', null, null);
INSERT INTO `u_users` VALUES ('14', '', null, null, '13445678904', '126e7d9fc4328dbf4ef254ec716d3e40', '', '2016-05-25 09:57:47', '', '', '0', null, null, null);
INSERT INTO `u_users` VALUES ('19', '姓名f', '4f8bc79698a645a1d4951c7341d99e17.jpg', '342894617', '18728284998', 'a02a0bd6c48cfaaa74a20dcefc3b8cf6', '25,8,28', '2016-05-27 15:59:35', '', '25,8,58', '0', 'zhengji1991@gmail.com', '342894617f', null);
INSERT INTO `u_users` VALUES ('22', '', null, null, '13209873456', '7e4b8ad821d10cb27faf4368c34af058', '', '2016-05-27 08:10:05', '', '', '0', null, null, null);
INSERT INTO `u_users` VALUES ('23', '', null, null, '18281965073', '84fa3b21a55d641a455ed83236b13554', '', '2016-05-27 08:42:13', '', '', '0', null, null, null);
INSERT INTO `u_users` VALUES ('24', '', null, null, '13730879537', 'a02a0bd6c48cfaaa74a20dcefc3b8cf6', '', '2016-05-28 11:13:59', '', '', '0', null, null, null);
INSERT INTO `u_users` VALUES ('27', '', 'b9c326d221ee657e28e4323fee89079e.jpg', '1234561', '112233', 'a02a0bd6c48cfaaa74a20dcefc3b8cf6', '', '2016-10-26 21:08:16', '', '', '0', 'we@qq.com', '123456', '3');
