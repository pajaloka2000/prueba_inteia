-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: prueba_inteia
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `rol` enum('administrador','basico') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'basico',
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Administrador Principal','admin@empresa.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','administrador','activo','2025-08-06 04:50:55','2025-08-06 04:50:55'),(2,'Juan Pérez','juan@empresa.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','basico','activo','2025-08-06 04:50:55','2025-08-06 04:50:55'),(3,'María García','maria@empresa.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','basico','inactivo','2025-08-06 04:50:55','2025-08-06 04:50:55'),(4,' julio iglesias','julio@basico.com','$2y$10$oPHzB1TA76L/EKwcKHhnOeFv5VBg681Yw/A5ZnwcgVUyi0J/Pa/tC','basico','activo','2025-08-07 03:10:52','2025-08-07 03:10:52');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `estado` enum('activa','inactiva') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'activa',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `presupuesto` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Presupuesto asignado a la categoría en moneda local',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Electrónicos','activa','2025-08-06 02:49:28','2025-11-07 20:26:55',100000.00),(2,'Ropa','activa','2025-08-06 02:49:28','2025-11-07 20:27:05',500000.00),(3,'Hogar','activa','2025-08-06 02:49:28','2025-11-07 20:27:17',800000.00);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo` enum('pedido_aprobado','pedido_rechazado','pedido_entregado','sistema') NOT NULL,
  `referencia_id` int DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_lectura` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_leida` (`usuario_id`,`leida`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado','entregado') DEFAULT 'pendiente',
  `fecha_pedido` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `comentarios` text,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_fecha` (`fecha_pedido`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Tabla para registrar pedidos de productos por usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,2,4,1,1,3000.00,3000.00,'aprobado','2025-11-08 02:21:42','2025-11-07 21:37:40','nada \n\nComentarios del admin: todo bien pana'),(2,2,4,1,5,3000.00,15000.00,'aprobado','2025-11-08 02:22:11','2025-11-07 21:37:12','otro pedido \n\nComentarios del admin: aprobado por manuel'),(3,2,2,1,1,4500.00,4500.00,'pendiente','2025-11-08 02:53:51','2025-11-07 21:53:51','oe ');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'activo',
  `categoria_id` int NOT NULL,
  `subcategoria_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio unitario del producto establecido por el administrador',
  PRIMARY KEY (`id`),
  KEY `fk_productos_subcategoria` (`subcategoria_id`) USING BTREE,
  KEY `idx_productos_categoria_id` (`categoria_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'iPhone 14','activo',1,1,'2025-08-06 02:49:29','2025-11-07 20:55:03',5000.00),(2,'Samsung Galaxy S23','activo',1,1,'2025-08-06 02:49:29','2025-11-07 20:55:03',4500.00),(3,'MacBook Air','activo',1,2,'2025-08-06 02:49:29','2025-11-07 20:55:03',8000.00),(4,'Dell XPS 13','activo',1,2,'2025-08-06 02:49:29','2025-11-07 20:55:03',3000.00),(5,'Camisa Polo','activo',2,3,'2025-08-06 02:49:29','2025-11-07 20:55:03',500.00),(6,'Jeans Levi\'s','activo',2,4,'2025-08-06 02:49:29','2025-11-07 20:55:03',400.00),(7,'Licuadora Oster','activo',3,5,'2025-08-06 02:49:29','2025-11-07 20:55:03',250.00),(8,'Espejo de Baño','activo',3,6,'2025-08-06 02:49:29','2025-11-07 20:57:34',150.00),(9,' Xiami 11 pro','activo',1,1,'2025-08-07 03:45:31','2025-11-07 20:55:03',2000.00);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos_subcategorias`
--

DROP TABLE IF EXISTS `productos_subcategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos_subcategorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `subcategoria_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_producto_subcategoria` (`producto_id`,`subcategoria_id`),
  KEY `idx_productos_subcategorias_producto_id` (`producto_id`) USING BTREE,
  KEY `idx_productos_subcategorias_subcategoria_id` (`subcategoria_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos_subcategorias`
--

LOCK TABLES `productos_subcategorias` WRITE;
/*!40000 ALTER TABLE `productos_subcategorias` DISABLE KEYS */;
INSERT INTO `productos_subcategorias` VALUES (1,1,1,'2025-08-06 02:49:29'),(2,2,1,'2025-08-06 02:49:29'),(3,3,2,'2025-08-06 02:49:29'),(4,4,2,'2025-08-06 02:49:29'),(5,5,3,'2025-08-06 02:49:29'),(6,6,4,'2025-08-06 02:49:29'),(7,7,5,'2025-08-06 02:49:29'),(8,8,6,'2025-08-06 02:49:29');
/*!40000 ALTER TABLE `productos_subcategorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resumen_presupuesto`
--

DROP TABLE IF EXISTS `resumen_presupuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resumen_presupuesto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria_id` int NOT NULL,
  `presupuesto_asignado` decimal(10,2) NOT NULL,
  `presupuesto_usado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `presupuesto_disponible` decimal(10,2) NOT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_categoria` (`categoria_id`),
  KEY `idx_categoria` (`categoria_id`),
  CONSTRAINT `resumen_presupuesto_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Resumen del uso de presupuesto por categoría';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resumen_presupuesto`
--

LOCK TABLES `resumen_presupuesto` WRITE;
/*!40000 ALTER TABLE `resumen_presupuesto` DISABLE KEYS */;
INSERT INTO `resumen_presupuesto` VALUES (1,1,100000.00,0.00,100000.00,'2025-11-07 20:48:59'),(2,2,500000.00,0.00,500000.00,'2025-11-07 20:48:59'),(3,3,800000.00,0.00,800000.00,'2025-11-07 20:48:59');
/*!40000 ALTER TABLE `resumen_presupuesto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcategorias`
--

DROP TABLE IF EXISTS `subcategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subcategorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `estado` enum('activa','inactiva') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'activa',
  `categoria_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_subcategorias_categoria_id` (`categoria_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategorias`
--

LOCK TABLES `subcategorias` WRITE;
/*!40000 ALTER TABLE `subcategorias` DISABLE KEYS */;
INSERT INTO `subcategorias` VALUES (1,'Smartphones','activa',1,'2025-08-06 02:49:29','2025-08-06 17:12:14'),(2,'Laptops','activa',1,'2025-08-06 02:49:29','2025-08-06 17:12:14'),(3,'Camisas','activa',2,'2025-08-06 02:49:29','2025-08-06 16:51:55'),(4,'Pantalones','activa',2,'2025-08-06 02:49:29','2025-08-06 16:51:33'),(5,'Cocina','activa',3,'2025-08-06 02:49:29','2025-08-06 02:49:29'),(6,'Baño','activa',3,'2025-08-06 02:49:29','2025-08-06 02:49:29');
/*!40000 ALTER TABLE `subcategorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_presupuesto_categorias`
--

DROP TABLE IF EXISTS `vista_presupuesto_categorias`;
/*!50001 DROP VIEW IF EXISTS `vista_presupuesto_categorias`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_presupuesto_categorias` AS SELECT 
 1 AS `id`,
 1 AS `nombre`,
 1 AS `estado`,
 1 AS `presupuesto_original`,
 1 AS `presupuesto_asignado`,
 1 AS `presupuesto_usado`,
 1 AS `presupuesto_disponible`,
 1 AS `porcentaje_usado`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'prueba_inteia'
--

--
-- Final view structure for view `vista_presupuesto_categorias`
--

/*!50001 DROP VIEW IF EXISTS `vista_presupuesto_categorias`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_presupuesto_categorias` AS select `c`.`id` AS `id`,`c`.`nombre` AS `nombre`,`c`.`estado` AS `estado`,`c`.`presupuesto` AS `presupuesto_original`,coalesce(`rp`.`presupuesto_asignado`,`c`.`presupuesto`) AS `presupuesto_asignado`,coalesce(`rp`.`presupuesto_usado`,0) AS `presupuesto_usado`,coalesce(`rp`.`presupuesto_disponible`,`c`.`presupuesto`) AS `presupuesto_disponible`,round(((coalesce(`rp`.`presupuesto_usado`,0) / `c`.`presupuesto`) * 100),2) AS `porcentaje_usado` from (`categorias` `c` left join `resumen_presupuesto` `rp` on((`c`.`id` = `rp`.`categoria_id`))) where (`c`.`estado` = 'activa') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07 17:10:41
