CREATE DATABASE  IF NOT EXISTS `thinkphp6_shop` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `thinkphp6_shop`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: thinkphp6_shop
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '购物车ID',
  `user_id` int NOT NULL COMMENT '用户ID',
  `product_id` int NOT NULL COMMENT '商品ID',
  `quantity` int NOT NULL DEFAULT '1' COMMENT '商品数量',
  `selected` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否选中：0否，1是',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='购物车表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (12,5,6,4,1,'2025-06-13 14:11:18','2025-06-13 17:06:36'),(15,5,4,5,1,'2025-06-13 17:06:28','2025-06-13 17:06:38'),(19,2,5,1,1,'2025-06-13 19:20:03','2025-06-15 13:06:16');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `parent_id` int NOT NULL DEFAULT '0' COMMENT '父分类ID',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示：0否，1是',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='商品分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'电子产品',0,1,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(2,'摩托车',0,2,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(3,'手机',1,1,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(4,'笔记本电脑',1,2,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(5,'智能手表',1,3,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(6,'耳机',1,4,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(7,'街车',2,1,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(8,'跑车',2,2,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(9,'越野车',2,3,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL),(10,'巡航车',2,4,1,'2025-06-13 12:02:53','2025-06-13 12:02:53',NULL);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_no` varchar(50) NOT NULL COMMENT '订单编号',
  `user_id` int NOT NULL COMMENT '用户ID',
  `total_amount` decimal(10,2) NOT NULL COMMENT '订单总金额',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '订单状态：0待付款，1已付款，2已发货，3已完成，4已取消',
  `address` varchar(255) DEFAULT NULL COMMENT '收货地址',
  `consignee` varchar(50) DEFAULT NULL COMMENT '收货人',
  `mobile` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `pay_time` datetime DEFAULT NULL COMMENT '支付时间',
  `delivery_time` datetime DEFAULT NULL COMMENT '发货时间',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,'202506122355569449',5,9998.00,2,'吧v从v吧','qdsdsdwed','15433234326','2025-06-12 23:56:23','2025-06-13 12:11:05',NULL,'2025-06-12 23:55:56','2025-06-13 12:11:05',NULL),(2,'202506130823253426',5,11998.00,2,'好哥哥hh\r\n','qdsdsdwed','15433234326','2025-06-13 08:24:20','2025-06-13 10:59:04',NULL,'2025-06-13 08:23:25','2025-06-13 10:59:04',NULL),(3,'202506130828163395',5,319920.00,3,'dd','qdsdsdwed','15433234326','2025-06-13 08:28:24','2025-06-13 09:49:21','2025-06-13 09:57:34','2025-06-13 08:28:17','2025-06-13 09:57:35',NULL),(4,'202506131000247463',5,0.03,4,'极简','qdsdsdwed','15433234326',NULL,NULL,NULL,'2025-06-13 10:00:24','2025-06-13 10:00:31',NULL),(5,'202506131033127649',5,0.21,0,'jj','qdsdsdwed','15433234326',NULL,NULL,NULL,'2025-06-13 10:33:12','2025-06-13 10:33:12',NULL),(6,'202506131041423881',2,13997.06,2,'Huh\r\nHuh\r\n','系统管理员','13900139000','2025-06-13 14:11:51','2025-06-13 14:13:21',NULL,'2025-06-13 10:41:42','2025-06-13 14:13:22',NULL),(7,'202506131411466284',2,1398.00,2,'规范','系统管理员','13900139000','2025-06-13 14:11:54','2025-06-13 14:13:17',NULL,'2025-06-13 14:11:47','2025-06-13 14:13:17',NULL),(8,'202506131434404690',3,59999.00,2,'asdx','wwxwguojie','17688901373','2025-06-13 14:34:45','2025-06-13 14:36:36',NULL,'2025-06-13 14:34:41','2025-06-13 14:36:36',NULL),(9,'202506131918042693',2,275875.00,2,' vccdxvx','系统管理员','13900139000','2025-06-13 19:18:08','2025-06-13 19:43:29',NULL,'2025-06-13 19:18:04','2025-06-13 19:43:30',NULL),(10,'202506151340502055',3,48597.00,1,'rwefewr','wwxwguojie','17688901373','2025-06-15 13:40:58',NULL,NULL,'2025-06-15 13:40:51','2025-06-15 13:40:58',NULL),(11,'202506151502509908',3,61298.00,1,'复旦光华豆腐干地方','wwxwguojie','17688901373','2025-06-15 15:03:51',NULL,NULL,'2025-06-15 15:02:51','2025-06-15 15:03:52',NULL);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_detail` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '订单详情ID',
  `order_id` int NOT NULL COMMENT '订单ID',
  `product_id` int NOT NULL COMMENT '商品ID',
  `product_name` varchar(100) NOT NULL COMMENT '商品名称',
  `product_image` varchar(255) NOT NULL COMMENT '商品图片',
  `product_price` decimal(10,2) NOT NULL COMMENT '商品单价',
  `quantity` int NOT NULL COMMENT '商品数量',
  `total_price` decimal(10,2) NOT NULL COMMENT '商品总价',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='订单详情表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_detail`
--

LOCK TABLES `order_detail` WRITE;
/*!40000 ALTER TABLE `order_detail` DISABLE KEYS */;
INSERT INTO `order_detail` VALUES (1,1,1,'高级智能手机','/static/uploads/product/phone.jpg',3999.00,1,3999.00,'2025-06-12 23:55:56','2025-06-12 23:55:56'),(2,1,2,'超薄笔记本电脑','/static/uploads/product/laptop.jpg',5999.00,1,5999.00,'2025-06-12 23:55:56','2025-06-12 23:55:56'),(3,2,2,'超薄笔记本电脑','/static/uploads/product/laptop.jpg',5999.00,2,11998.00,'2025-06-13 08:23:25','2025-06-13 08:23:25'),(4,3,1,'高级智能手机','/static/uploads/product/phone.jpg',3999.00,80,319920.00,'2025-06-13 08:28:17','2025-06-13 08:28:17'),(5,4,3,'wu','/static/uploads/product/default.jpg',0.03,1,0.03,'2025-06-13 10:00:24','2025-06-13 10:08:04'),(6,5,4,'wow ','/static/uploads/product/20250613/9d27f2543a26a6e9be3c4029d77e5f02.png',0.21,1,0.21,'2025-06-13 10:33:12','2025-06-13 10:33:12'),(7,6,1,'高级智能手机','/static/uploads/product/phone.jpg',3999.00,2,7998.00,'2025-06-13 10:41:42','2025-06-13 10:41:42'),(8,6,2,'超薄笔记本电脑','/static/uploads/product/laptop.jpg',5999.00,1,5999.00,'2025-06-13 10:41:42','2025-06-13 10:41:42'),(9,6,3,'wu','/static/uploads/product/default.jpg',0.03,2,0.06,'2025-06-13 10:41:42','2025-06-13 10:41:42'),(10,7,6,'无线蓝牙耳机','/static/uploads/product/6f/595e9b9e84dc35d30e04f11b188ce6.gif',699.00,2,1398.00,'2025-06-13 14:11:47','2025-06-13 14:11:47'),(11,8,8,'高性能跑车','/static/uploads/product/2e/ff7f0472d96ba4750a0d5924913a12.jpg',59999.00,1,59999.00,'2025-06-13 14:34:41','2025-06-13 14:34:41'),(12,9,6,'无线蓝牙耳机','/static/uploads/product/6f/595e9b9e84dc35d30e04f11b188ce6.gif',699.00,17,11883.00,'2025-06-13 19:18:04','2025-06-13 19:18:04'),(13,9,9,'多功能越野摩托','/static/uploads/product/f2/7d1810e9711de1e251d3df513b553f.jpg',32999.00,8,263992.00,'2025-06-13 19:18:04','2025-06-13 19:18:04'),(14,10,5,'智能运动手表','/static/uploads/product/36/47566429b55703e3e2984134eff2f2.gif',1299.00,2,2598.00,'2025-06-15 13:40:51','2025-06-15 13:40:51'),(15,10,10,'舒适巡航摩托','/static/uploads/product/20250613/137ba18165d471db60a4cfa18a63bc35.jpeg',45999.00,1,45999.00,'2025-06-15 13:40:51','2025-06-15 13:40:51'),(16,11,5,'智能运动手表','/static/uploads/product/36/47566429b55703e3e2984134eff2f2.gif',1299.00,1,1299.00,'2025-06-15 15:02:51','2025-06-15 15:02:51'),(17,11,8,'高性能跑车','/static/uploads/product/2e/ff7f0472d96ba4750a0d5924913a12.jpg',59999.00,1,59999.00,'2025-06-15 15:02:51','2025-06-15 15:02:51');
/*!40000 ALTER TABLE `order_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `category_id` int NOT NULL COMMENT '分类ID',
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `image` varchar(255) NOT NULL COMMENT '商品图片',
  `price` decimal(10,2) NOT NULL COMMENT '商品单价',
  `stock` int NOT NULL DEFAULT '0' COMMENT '库存数量',
  `sales` int NOT NULL DEFAULT '0' COMMENT '销量',
  `description` text COMMENT '商品描述',
  `manufacturer` varchar(100) DEFAULT NULL COMMENT '厂商',
  `is_on_sale` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架：0否，1是',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐：0否，1是',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `is_on_sale` (`is_on_sale`),
  KEY `price` (`price`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,3,'高级智能手机','/static/uploads/product/49\\cccd02677407e3ee7f7237e3598c74.jpg',3999.00,100,15,'这是一款高级智能手机，配置强大，外观精美。搭载最新处理器，拥有超长续航和出色的拍照能力。','科技公司',1,1,'2025-06-13 12:05:05','2025-06-13 13:06:12',NULL),(2,3,'入门级智能手机','/static/uploads/product/72\\dea741496d2f2e4ae4c7e4f167cc99.jpg',1299.00,200,25,'这是一款性价比极高的入门级智能手机，满足日常使用需求，轻薄机身，长效电池。','科技公司',1,0,'2025-06-13 12:05:05','2025-06-13 13:07:25',NULL),(3,4,'超薄笔记本电脑','/static/uploads/product/9e\\2ea0acab281ba5eca04d74b7e30cb1.jpg',5999.00,50,8,'这是一款超薄笔记本电脑，性能强劲，携带方便。搭载高性能处理器和独立显卡，适合办公和轻度游戏。','电脑科技',1,1,'2025-06-13 12:05:05','2025-06-13 13:58:03',NULL),(4,4,'游戏笔记本电脑','/static/uploads/product/0b\\5d3d2f82e3979f1a18334428b2bc2c.jpg',8999.00,30,12,'这是一款专为游戏设计的高性能笔记本电脑，搭载顶级显卡和处理器，散热系统出色，为游戏玩家提供极致体验。','游戏电脑',1,1,'2025-06-13 12:05:05','2025-06-13 13:03:30',NULL),(5,5,'智能运动手表','/static/uploads/product/36\\47566429b55703e3e2984134eff2f2.gif',1299.00,77,21,'这是一款功能齐全的智能运动手表，支持多种运动模式，心率监测，睡眠分析，防水设计，续航长达7天。','智能穿戴',1,0,'2025-06-13 12:05:05','2025-06-15 15:02:51',NULL),(6,6,'无线蓝牙耳机','/static/uploads/product/6f\\595e9b9e84dc35d30e04f11b188ce6.gif',699.00,101,54,'这是一款高音质无线蓝牙耳机，主动降噪，长效续航，舒适佩戴，支持快速充电。','音频科技',1,1,'2025-06-13 12:05:05','2025-06-13 19:18:04',NULL),(7,7,'经典街车','/static/uploads/product/dc\\eb709bdbeac79b8efab99573caeb16.jpg',29999.00,10,3,'这是一款经典街车摩托，排量650cc，动力充沛，操控灵活，适合城市通勤和短途旅行。','摩托车厂商A',1,1,'2025-06-13 12:05:05','2025-06-13 12:48:47',NULL),(8,8,'高性能跑车','/static/uploads/product/2e\\ff7f0472d96ba4750a0d5924913a12.jpg',59999.00,4,4,'这是一款高性能跑车摩托，排量1000cc，极速可达280km/h，采用最新电控系统，为追求速度的骑士提供极致体验。','摩托车厂商B',1,1,'2025-06-13 12:05:05','2025-06-15 15:02:51',NULL),(9,9,'多功能越野摩托','/static/uploads/product/f2\\7d1810e9711de1e251d3df513b553f.jpg',32999.00,6,12,'这是一款多功能越野摩托车，适应各种复杂路况，强大的动力输出和出色的悬挂系统，为越野爱好者提供可靠的伙伴。','摩托车厂商C',1,0,'2025-06-13 12:05:05','2025-06-13 19:47:09',NULL),(10,10,'舒适巡航摩托','/static/uploads/product/20250613\\137ba18165d471db60a4cfa18a63bc35.jpeg',45999.00,6,3,'这是一款舒适的巡航摩托车，宽大的座椅和舒适的骑行姿势，适合长途旅行，配备先进的导航和音响系统。','摩托车厂商D',1,1,'2025-06-13 12:05:05','2025-06-15 13:40:51',NULL),(11,2,'vcds','/static/uploads/product/49\\cccd02677407e3ee7f7237e3598c74.jpg',34.00,3,0,'esdgfs','摩托车厂商B',1,0,'2025-06-13 19:59:41','2025-06-13 19:59:45','2025-06-13 19:59:45');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `mobile` varchar(20) NOT NULL COMMENT '手机号码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `real_name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否管理员：0否，1是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'test_user','$2y$10$NyWVzBVYjmIAlUlw0Q4oiepAsrH1spGHZOxGnwaGbfGW8u8jiqV.y','13800138000','test@example.com','测试用户',NULL,1,1,'2025-06-12 12:21:16','2025-06-13 10:43:57',NULL),(2,'admin','$2y$10$/pWJliZEYB5SF5QrQvJhhutuwslqai9YMYKhAFXWZLn02Uf0NeJvO','13900139000','admin@example.com','系统管理员',NULL,1,1,'2025-06-12 12:21:16','2025-06-13 09:24:55',NULL),(3,'qwe123','$2y$10$HHYU1B.8mrnunA2A8ZYv7.QnoevJkk62D37njUalY2B43ZayhruSi','17688901373','2255411466@google.com','wwxwguojie',NULL,0,1,'2025-06-12 14:36:28','2025-06-15 13:47:50',NULL),(4,'qweqwe','$2y$10$mxFVoN0D.xOoVqnfx17fFutFKSDoEdpCjAzVFlk07rPetLZ/dvgmC','15433234328','','',NULL,0,0,'2025-06-12 16:37:13','2025-06-15 14:03:10',NULL),(5,'qweewq','$2y$10$cHHfBd4IdA2TCSQreQXgterbi6Wwad3MN5q253lMz2wiPPbMIAYRK','15433234326','2255446688@google.com','qdsdsdwed',NULL,0,1,'2025-06-12 23:45:30','2025-06-12 23:45:30',NULL),(6,'qweasd','$2y$10$xJl6r4owBya2zMQ1QO9RX.DvUoGNbl2ebXsktKbUour8ewlklJLcK','13800138004','34185438680@qq.com','张三',NULL,0,1,'2025-06-15 13:47:13','2025-06-15 13:47:13',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-15 15:37:48
