
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `blacklisted_jwts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blacklisted_jwts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jwt` text NOT NULL,
  `expiration` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expiration` (`expiration`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `blacklisted_jwts` WRITE;
/*!40000 ALTER TABLE `blacklisted_jwts` DISABLE KEYS */;
INSERT INTO `blacklisted_jwts` VALUES (1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJjb29raWVjbXMiLCJzdWIiOiI5Mjg4MDkzNDQ4Mzg1NDM2ODMiLCJpYXQiOjE3MzE4ODkxMDYsImV4cCI6MTczMTg5MjcwNn0.QEgRW_1rcu9PN4u3wRilwyQAWLOZ99d4tdJ33PjxhV4',NULL),(2,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJjb29raWVjbXMiLCJzdWIiOiIyODgzOTkwOTAwOTIzNDg5MDMiLCJpYXQiOjE3MzIxMDQyNDgsImV4cCI6MTczMjEwNzg0OH0.dkDOBs0OvzbZpKZjUxXE5Mtf-q3LTJFM19TU-wsSgvw',NULL);
/*!40000 ALTER TABLE `blacklisted_jwts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cloaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cloaks` (
  `id` int NOT NULL,
  `uid` bigint NOT NULL,
  `cid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cloaks` WRITE;
/*!40000 ALTER TABLE `cloaks` DISABLE KEYS */;
INSERT INTO `cloaks` VALUES (1,288399090092348903,'1');
/*!40000 ALTER TABLE `cloaks` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cloaks_lib`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cloaks_lib` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cloaks_lib` WRITE;
/*!40000 ALTER TABLE `cloaks_lib` DISABLE KEYS */;
INSERT INTO `cloaks_lib` VALUES (1,'test');
/*!40000 ALTER TABLE `cloaks_lib` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `hwids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hwids` (
  `id` bigint NOT NULL,
  `publickey` blob,
  `hwDiskId` varchar(255) DEFAULT NULL,
  `baseboardSerialNumber` varchar(255) DEFAULT NULL,
  `graphicCard` varchar(255) DEFAULT NULL,
  `displayId` blob,
  `bitness` int DEFAULT NULL,
  `totalMemory` bigint DEFAULT NULL,
  `logicalProcessors` int DEFAULT NULL,
  `physicalProcessors` int DEFAULT NULL,
  `processorMaxFreq` bigint DEFAULT NULL,
  `battery` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `hwids` WRITE;
/*!40000 ALTER TABLE `hwids` DISABLE KEYS */;
/*!40000 ALTER TABLE `hwids` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `job_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_name` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_id` int NOT NULL,
  `scheduled_date` bigint NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` bigint DEFAULT NULL,
  `updated_at` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `job_schedule` WRITE;
/*!40000 ALTER TABLE `job_schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_schedule` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `skins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `skins` (
  `id` int NOT NULL,
  `uuid` varchar(256) NOT NULL,
  `slim` int NOT NULL,
  `locked` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `skins` WRITE;
/*!40000 ALTER TABLE `skins` DISABLE KEYS */;
/*!40000 ALTER TABLE `skins` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `skins_lib`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `skins_lib` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `nff` varchar(100) NOT NULL,
  UNIQUE KEY `skins_lib_unique` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `skins_lib` WRITE;
/*!40000 ALTER TABLE `skins_lib` DISABLE KEYS */;
INSERT INTO `skins_lib` VALUES (1,288399090092348903,'test','uuid');
/*!40000 ALTER TABLE `skins_lib` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `uuid` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` varchar(256) NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `dsid` bigint DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `mail_verify` int DEFAULT '0',
  `uuid` char(36) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `perms` int NOT NULL DEFAULT '1',
  `accessToken` char(32) DEFAULT NULL,
  `serverID` varchar(41) DEFAULT NULL,
  `hwidId` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('288399090092348903','1',741957448532754493,'misha.lebedev.kiev@gmail.com',1,'f85cfd52-a76b-42fe-97dd-838db719d928','$2y$10$mosJuoTPWgoVsbQ.zLB86.HTOZ9HpMq6rusCQDXPmc44Ht3vN7d2O',1,NULL,NULL,NULL),('372428509030079699',NULL,NULL,'string@string.com',0,NULL,'$2y$10$kmVHoHVL3F/MkWKSigh0uO2XQ6ftJk5D1da/syJtuP7C8a6PkOT4u',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `verify_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `expire` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `verify_codes` WRITE;
/*!40000 ALTER TABLE `verify_codes` DISABLE KEYS */;
INSERT INTO `verify_codes` VALUES (6,372428509030079699,'Ou6VH5',0);
/*!40000 ALTER TABLE `verify_codes` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

