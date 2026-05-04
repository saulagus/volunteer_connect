-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: 127.0.0.1    Database: volunteerConnect
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `bookingId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  `bookingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('booked','cancelled','attended') DEFAULT 'booked',
  PRIMARY KEY (`bookingId`),
  UNIQUE KEY `uniqueBooking` (`userId`,`eventId`),
  KEY `eventId` (`eventId`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`eventId`) REFERENCES `events` (`eventId`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,5,1,'2026-05-03 07:20:53','attended'),(2,6,1,'2026-05-03 07:20:53','attended'),(3,7,1,'2026-05-03 07:20:53','attended'),(4,8,1,'2026-05-03 07:20:53','attended'),(5,9,1,'2026-05-03 07:20:53','attended'),(6,5,2,'2026-05-03 07:20:53','cancelled'),(7,10,2,'2026-05-03 07:20:53','cancelled'),(8,6,3,'2026-05-03 07:20:53','booked'),(9,9,3,'2026-05-03 07:20:53','booked'),(10,11,3,'2026-05-03 07:20:53','booked'),(11,5,4,'2026-05-03 07:20:53','booked'),(12,6,4,'2026-05-03 07:20:53','booked'),(13,7,4,'2026-05-03 07:20:53','booked'),(14,8,4,'2026-05-03 07:20:53','booked'),(15,9,4,'2026-05-03 07:20:53','booked'),(16,10,4,'2026-05-03 07:20:53','booked'),(17,11,4,'2026-05-03 07:20:53','booked'),(18,12,4,'2026-05-03 07:20:53','booked'),(19,7,5,'2026-05-03 07:20:53','booked'),(20,12,5,'2026-05-03 07:20:53','booked'),(21,5,6,'2026-05-03 07:20:53','attended'),(22,8,6,'2026-05-03 07:20:53','attended'),(23,10,6,'2026-05-03 07:20:53','attended'),(24,6,7,'2026-05-03 07:20:53','attended'),(25,7,7,'2026-05-03 07:20:53','attended'),(26,9,7,'2026-05-03 07:20:53','attended'),(27,11,7,'2026-05-03 07:20:53','attended'),(28,12,7,'2026-05-03 07:20:53','attended'),(29,5,8,'2026-05-03 07:20:53','booked'),(30,10,8,'2026-05-03 07:20:53','booked'),(31,6,9,'2026-05-03 07:20:53','booked'),(32,8,9,'2026-05-03 07:20:53','booked'),(33,11,9,'2026-05-03 07:20:53','booked'),(34,5,12,'2026-05-03 07:20:53','booked'),(35,6,12,'2026-05-03 07:20:53','booked'),(36,7,12,'2026-05-03 07:20:53','booked'),(37,8,12,'2026-05-03 07:20:53','booked'),(38,9,12,'2026-05-03 07:20:53','booked'),(39,10,12,'2026-05-03 07:20:53','booked'),(40,11,12,'2026-05-03 07:20:53','booked'),(41,12,12,'2026-05-03 07:20:53','booked'),(42,7,13,'2026-05-03 07:20:53','booked'),(43,9,13,'2026-05-03 07:20:53','booked'),(44,12,13,'2026-05-03 07:20:53','booked'),(45,3,11,'2026-05-03 07:23:13','booked'),(46,3,3,'2026-05-03 07:23:20','booked'),(47,3,8,'2026-05-03 07:23:27','booked');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `categoryId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`categoryId`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Environmental','Events focused on nature and conservation.'),(2,'Education','Tutoring and school-related support.'),(3,'Animal Welfare','Helping at shelters and wildlife.'),(4,'Community Outreach','Food drives and local support.'),(5,'Health & Wellness','Hospitals and awareness.'),(6,'Disaster Relief','Emergency response.'),(7,'Other','Miscellaneous events.');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `eventId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `eventDate` datetime NOT NULL,
  `capacity` int(10) unsigned NOT NULL,
  `organiserId` int(11) NOT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `status` enum('active','full','completed','cancelled') DEFAULT 'active',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`eventId`),
  KEY `organiserId` (`organiserId`),
  KEY `categoryId` (`categoryId`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organiserId`) REFERENCES `users` (`userId`),
  CONSTRAINT `events_ibfk_2` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`categoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Beach Clean-Up','Join us to clean Bondi Beach and surrounding areas. Gloves and bags provided.','Bondi Beach, Sydney','2026-03-15 09:00:00',40,2,1,'completed','2026-05-03 07:20:53'),(2,'Food Drive Downtown','Help collect and sort non-perishable food donations at the city centre.','City Hall, Sydney','2026-04-01 08:00:00',20,2,4,'cancelled','2026-05-03 07:20:53'),(3,'After-School Tutoring','Volunteer tutors needed for maths and English. Year 5–8 students.','Greenfield Primary School','2026-05-15 15:30:00',10,2,2,'active','2026-05-03 07:20:53'),(4,'Animal Shelter Help','Walk dogs, clean pens, and socialise cats at the local RSPCA.','RSPCA Sydney North','2026-05-20 10:00:00',15,2,3,'full','2026-05-03 07:20:53'),(5,'Community Garden Day','Help plant vegetables and maintain the community garden. Tools supplied.','Newtown Community Garden','2026-06-07 09:30:00',25,2,1,'active','2026-05-03 07:20:53'),(6,'Hospital Visiting Program','Spend time with long-term patients. No medical background needed.','Royal Prince Alfred Hospital','2026-03-22 13:00:00',8,3,5,'completed','2026-05-03 07:20:53'),(7,'Flood Relief Packing','Pack supply boxes for flood-affected families in regional NSW.','Parramatta Relief Centre','2026-04-10 07:00:00',50,3,6,'completed','2026-05-03 07:20:53'),(8,'Senior Tech Help','Help elderly residents learn smartphones and tablets.','Randwick Senior Centre','2026-05-28 10:00:00',12,3,2,'active','2026-05-03 07:20:53'),(9,'Park Restoration','Plant native trees and remove invasive species in Centennial Park.','Centennial Park, Sydney','2026-06-14 08:00:00',30,3,1,'active','2026-05-03 07:20:53'),(10,'Blood Drive Support','Assist Red Cross coordinators at the quarterly blood donation drive.','Sydney Town Hall','2026-07-05 09:00:00',20,3,5,'active','2026-05-03 07:20:53'),(11,'Wildlife Survey','Help biologists count native bird species in Western Sydney Parklands.','Western Sydney Parklands','2026-05-10 06:30:00',16,4,1,'active','2026-05-03 07:20:53'),(12,'Kids Reading Club','Read with primary school children every Saturday morning.','Marrickville Library','2026-05-16 10:00:00',8,4,2,'full','2026-05-03 07:20:53'),(13,'Homeless Outreach Night','Distribute meals and care packs to rough sleepers in the CBD.','Martin Place, Sydney','2026-05-30 18:00:00',20,4,4,'active','2026-05-03 07:20:53'),(14,'Cat Fostering Info Day','Learn how to foster cats awaiting adoption. Take a cat home today!','Inner West Animal Rescue','2026-06-21 11:00:00',35,4,3,'active','2026-05-03 07:20:53'),(15,'Disaster Prep Workshop','Community workshop on emergency kits, evacuation plans, and first aid.','Leichhardt Town Hall','2026-04-05 10:00:00',60,4,6,'cancelled','2026-05-03 07:20:53'),(16,'Hello ','Nothing','Cork','2026-05-12 12:00:00',10,2,7,'active','2026-05-03 07:25:03');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','organiser','attendee') NOT NULL DEFAULT 'attendee',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2026-05-03 07:20:53'),(2,'Jane Organiser','jane@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','organiser','2026-05-03 07:20:53'),(3,'Marcus Green','marcus@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','organiser','2026-05-03 07:20:53'),(4,'Sofia Reyes','sofia@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','organiser','2026-05-03 07:20:53'),(5,'Tom Attendee','tom@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(6,'Alice Nguyen','alice@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(7,'Ben Carter','ben@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(8,'Clara Walsh','clara@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(9,'David Kim','david@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(10,'Emma Patel','emma@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(11,'Finn O\'Brien','finn@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53'),(12,'Grace Liu','grace@vc.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','attendee','2026-05-03 07:20:53');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-04 18:26:05
