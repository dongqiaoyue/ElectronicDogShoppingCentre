/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50639
Source Host           : 127.0.0.1:3306
Source Database       : hb_payment

Target Server Type    : MYSQL
Target Server Version : 50639
File Encoding         : 65001

Date: 2018-07-02 14:32:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pay_admins
-- ----------------------------
DROP TABLE IF EXISTS `pay_admins`;
CREATE TABLE `pay_admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '密码',
  `real_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '真实姓名',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态 1 正常 2 禁用',
  `role_id` int(11) NOT NULL DEFAULT '1' COMMENT '用户角色id',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of pay_admins
-- ----------------------------
INSERT INTO `pay_admins` VALUES ('1', 'admin', 'e47cf483a1e24ea7e1097e11aa78d00f', 'admin', '1', '1');
INSERT INTO `pay_admins` VALUES ('3', '小白', '971bd406e02ed4330fcb45cef6dd9208', '小白', '1', '3');
INSERT INTO `pay_admins` VALUES ('4', '小贾', '971bd406e02ed4330fcb45cef6dd9208', '小贾', '1', '2');

-- ----------------------------
-- Table structure for rbac_nodes
-- ----------------------------
DROP TABLE IF EXISTS `rbac_nodes`;
CREATE TABLE `rbac_nodes` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_name` varchar(155) NOT NULL DEFAULT '' COMMENT '节点名称',
  `auth_rule` varchar(155) NOT NULL DEFAULT '' COMMENT '权限规则',
  `is_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是菜单项 1不是 2是',
  `parent_node_id` int(11) NOT NULL COMMENT '父级节点id',
  `style` varchar(155) DEFAULT '' COMMENT '菜单样式',
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of rbac_nodes
-- ----------------------------
INSERT INTO `rbac_nodes` VALUES ('1', '系统管理', '#', '2', '0', 'fa fa-cog');
INSERT INTO `rbac_nodes` VALUES ('2', '管理员管理', 'admins/index', '2', '1', '');
INSERT INTO `rbac_nodes` VALUES ('3', '添加管理员', 'admins/add', '1', '2', '');
INSERT INTO `rbac_nodes` VALUES ('4', '编辑管理员', 'admins/edit', '1', '2', '');
INSERT INTO `rbac_nodes` VALUES ('5', '删除管理员', 'admins/del', '1', '2', '');
INSERT INTO `rbac_nodes` VALUES ('6', '角色管理', 'roles/index', '2', '1', '');
INSERT INTO `rbac_nodes` VALUES ('7', '添加角色', 'roles/add', '1', '6', '');
INSERT INTO `rbac_nodes` VALUES ('8', '编辑角色', 'roles/edit', '1', '6', '');
INSERT INTO `rbac_nodes` VALUES ('9', '删除角色', 'roles/del', '1', '6', '');
INSERT INTO `rbac_nodes` VALUES ('10', '节点管理', 'nodes/index', '2', '1', '');
INSERT INTO `rbac_nodes` VALUES ('11', '添加节点', 'nodes/add', '1', '10', '');
INSERT INTO `rbac_nodes` VALUES ('12', '编辑节点', 'nodes/edit', '1', '10', '');
INSERT INTO `rbac_nodes` VALUES ('13', '删除节点', 'nodes/del', '1', '10', '');
INSERT INTO `rbac_nodes` VALUES ('14', '账单管理', '#', '2', '0', 'fa fa-credit-card');
INSERT INTO `rbac_nodes` VALUES ('15', '账单列表', 'payment/index', '2', '14', '');
INSERT INTO `rbac_nodes` VALUES ('16', '添加账单', 'payment/add', '1', '15', '');
INSERT INTO `rbac_nodes` VALUES ('17', '编辑账单', 'payment/edit', '1', '15', '');
INSERT INTO `rbac_nodes` VALUES ('18', '删除账单', 'payment/del', '1', '15', '');
INSERT INTO `rbac_nodes` VALUES ('19', '历史账单列表', 'log/index', '2', '14', '');
INSERT INTO `rbac_nodes` VALUES ('20', '分配权限', 'roles/allot', '1', '6', '');

-- ----------------------------
-- Table structure for rbac_roles
-- ----------------------------
DROP TABLE IF EXISTS `rbac_roles`;
CREATE TABLE `rbac_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `role_name` varchar(155) NOT NULL COMMENT '角色名称',
  `rule` varchar(255) DEFAULT '' COMMENT '权限节点数据',
  `status` tinyint(255) NOT NULL COMMENT '1 启用 2禁用',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of rbac_roles
-- ----------------------------
INSERT INTO `rbac_roles` VALUES ('1', '超级管理员', '', '1');
INSERT INTO `rbac_roles` VALUES ('2', '运营主管', '1,2,6,7,8,10,11,12,13', '1');
INSERT INTO `rbac_roles` VALUES ('3', '会计主管', '14,15,16,17,18', '1');
