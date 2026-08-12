-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: freemessagev2
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorie` (
  `IdCat` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `imgSrcCat` varchar(1000) NOT NULL,
  PRIMARY KEY (`IdCat`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorie`
--

LOCK TABLES `categorie` WRITE;
/*!40000 ALTER TABLE `categorie` DISABLE KEYS */;
INSERT INTO `categorie` VALUES (1,'jeux video','Catégorie consacrée aux jeux vidéo',''),(2,'musique','Tout ce qui concerne la musique',''),(3,'films','Cinéma',''),(4,'livres','Discussions autour des livres',''),(5,'sport','Actualités et discussions sportives',''),(6,'peinture et dessin','Arts visuels : peinture et dessin',''),(7,'photographie','Photographie et matériel photo',''),(8,'series','Séries TV','');
/*!40000 ALTER TABLE `categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentaire` (
  `IdComment` int(11) NOT NULL AUTO_INCREMENT,
  `texte` text NOT NULL,
  `dateCom` datetime NOT NULL,
  `IdUser` int(11) NOT NULL,
  `IdMsg` int(11) NOT NULL,
  PRIMARY KEY (`IdComment`),
  KEY `IdUser` (`IdUser`),
  KEY `IdMsg` (`IdMsg`),
  CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`IdUser`) REFERENCES `utilisateur` (`IdUser`),
  CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`IdMsg`) REFERENCES `message` (`IdMsg`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commentaire`
--

LOCK TABLES `commentaire` WRITE;
/*!40000 ALTER TABLE `commentaire` DISABLE KEYS */;
INSERT INTO `commentaire` VALUES (55,'Le rendu des animations est top, ça donne envie d\'y jouer !','2026-07-30 19:28:57',35,45),(56,'Le level design du niveau 2 est un peu confus je trouve.','2026-07-31 19:28:57',39,45),(57,'C\'était énorme, merci pour les photos !','2026-08-06 19:28:57',36,47),(61,'Essaie Le Maître du Haut Château, ça devrait te plaire.','2026-07-27 19:28:57',42,51),(62,'Courage, la pluie ça forge le mental !','2026-08-10 19:28:57',40,53),(63,'Bravo pour les 10km !','2026-08-11 19:28:57',41,53),(64,'Superbe rendu, tu utilises quelles encres ?','2026-07-16 19:28:57',36,55),(65,'J\'adore le trait, très expressif.','2026-07-17 19:28:57',37,55),(66,'Ça donne un rendu presque photographique.','2026-07-18 19:28:57',40,55),(67,'Les couleurs sont magnifiques, quel appareil ?','2026-08-08 19:28:57',41,57),(68,'Le dernier épisode m\'a mis une claque aussi.','2026-08-06 19:28:57',38,59),(69,'Meilleure série de l\'année pour moi.','2026-08-07 19:28:57',37,59);
/*!40000 ALTER TABLE `commentaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `IdMsg` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `texte` text NOT NULL,
  `imageSrc` varchar(255) DEFAULT NULL,
  `nbrLike` int(11) DEFAULT 0,
  `nbrDislike` int(11) DEFAULT 0,
  `nbrCom` int(100) NOT NULL,
  `IdCat` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  PRIMARY KEY (`IdMsg`),
  KEY `IdCat` (`IdCat`),
  KEY `IdUser` (`IdUser`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`IdCat`) REFERENCES `categorie` (`IdCat`),
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`IdUser`) REFERENCES `utilisateur` (`IdUser`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (45,'2026-07-28 19:28:57','Retours cherchés sur mon prototype de jeu de plateforme, je bosse dessus depuis 3 mois !','images-upload/1786555737-prototype-jeu.png',12,1,2,1,36),(46,'2026-08-03 19:28:57','Quelqu\'un a testé le dernier Zelda ? Je débute l\'aventure ce week-end.','',6,0,0,1,35),(47,'2026-08-06 19:28:57','Concert de vendredi soir, ambiance de folie.','images-upload/1786555738-concert-vendredi.png',18,0,1,2,41),(48,'2026-08-09 19:28:57','Vous écoutez quoi en ce moment ? Je cherche de nouvelles découvertes.','',4,1,0,2,36),(50,'2026-07-31 19:28:57','Le dernier Dune m\'a scotchée, quelqu\'un l\'a vu ?','',9,0,0,3,37),(51,'2026-07-26 19:28:57','Je viens de finir Le Nom de la rose, des idées de lecture similaires ?','',5,0,1,4,37),(52,'2026-08-08 19:28:57','Petite pile à lire pour cet été.','images-upload/1786555740-pile-a-lire.png',8,0,0,4,42),(53,'2026-08-10 19:28:57','10km ce matin sous la pluie, dur mais fier !','images-upload/1786555741-course-matin.png',14,2,2,5,38),(54,'2026-08-11 19:28:57','Qui regarde le match ce soir ?','',2,0,0,5,40),(55,'2026-07-15 19:28:57','Petit croquis fait ce week-end, un essai d\'encre de chine.','images-upload/1786555742-croquis-encre.png',3,1,3,6,35),(56,'2026-08-04 19:28:57','Je débute l\'aquarelle, des conseils pour les débutants ?','',7,0,0,6,39),(57,'2026-08-07 19:28:57','Coucher de soleil capturé hier soir, mon meilleur cliché du mois.','images-upload/1786555743-coucher-soleil.png',22,0,1,7,40),(58,'2026-07-29 19:28:57','Quel appareil pour débuter en photo argentique ?','',3,0,0,7,41),(59,'2026-08-05 19:28:57','Fin de saison de The Bear, quelle claque.','images-upload/1786555744-fin-de-saison.png',16,1,2,8,42),(60,'2026-07-18 19:28:57','Une série sportive à recommander ?','',2,0,0,8,38);
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reaction`
--

DROP TABLE IF EXISTS `reaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reaction` (
  `IdReact` int(11) NOT NULL AUTO_INCREMENT,
  `IdType` int(11) NOT NULL,
  `IdMsg` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  PRIMARY KEY (`IdReact`),
  KEY `IdType` (`IdType`),
  KEY `IdMsg` (`IdMsg`),
  KEY `IdUser` (`IdUser`),
  CONSTRAINT `reaction_ibfk_1` FOREIGN KEY (`IdType`) REFERENCES `type` (`IdType`),
  CONSTRAINT `reaction_ibfk_2` FOREIGN KEY (`IdMsg`) REFERENCES `message` (`IdMsg`),
  CONSTRAINT `reaction_ibfk_3` FOREIGN KEY (`IdUser`) REFERENCES `utilisateur` (`IdUser`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reaction`
--

LOCK TABLES `reaction` WRITE;
/*!40000 ALTER TABLE `reaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `reaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type` (
  `IdType` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`IdType`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type`
--

LOCK TABLES `type` WRITE;
/*!40000 ALTER TABLE `type` DISABLE KEYS */;
INSERT INTO `type` VALUES (1,'like'),(2,'dislike');
/*!40000 ALTER TABLE `type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `IdUser` int(11) NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `biographie` text DEFAULT NULL,
  PRIMARY KEY (`IdUser`),
  UNIQUE KEY `identifiant` (`identifiant`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (20,'admin','$2y$12$LCa0cEjPOmROYdhiN/ECM.VFd1wiPqwxQD/mWKUMLpkr294deZ2jq',NULL),(35,'nina.arts','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','Passionnée de dessin et peinture depuis toujours.'),(36,'theo_dev','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','Développeur de jeux indé le soir, testeur le jour.'),(37,'clara_l','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','Dévoreuse de livres, une critique par semaine.'),(38,'julien.sport','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','Coureur du dimanche, fan de foot.'),(39,'emma.d','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu',''),(40,'maxime_photo','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','Photographe amateur, toujours l\'appareil sur moi.'),(41,'lea_music','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','Vinyles et concerts, ma vie en musique.'),(42,'yanis_series','$2y$10$tNMK9Np6fruOWVdR0jPJVePj4Olw7rZJPI9nVwJufSoVNbypsIXAu','');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-12 19:54:00
