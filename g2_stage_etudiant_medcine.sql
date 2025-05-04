-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 12:42 AM
-- Server version: 9.2.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `g2_stage_etudiant_medcine`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID_ADM` int NOT NULL,
  `NOM_ADM` varchar(50) NOT NULL,
  `PRN_ADM` varchar(50) NOT NULL,
  `MAIL_ADM` varchar(50) NOT NULL,
  `MDP_ADM` varchar(255) NOT NULL,
  `IMG_ADM` varchar(255) DEFAULT NULL
) ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID_ADM`, `NOM_ADM`, `PRN_ADM`, `MAIL_ADM`, `MDP_ADM`, `IMG_ADM`) VALUES
(1, 'Admin', 'Principal', 'admin@mail.com', 'AdminPass123!', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `encadrant`
--

CREATE TABLE `encadrant` (
  `ID_ENC` int NOT NULL,
  `NOM_ENC` varchar(50) NOT NULL,
  `PRN_ENC` varchar(50) NOT NULL,
  `MAIL_ENC` varchar(50) NOT NULL,
  `MDP_ENC` varchar(255) NOT NULL,
  `IMG_ENC` varchar(255) DEFAULT NULL,
  `TEL_ENC` varchar(20) DEFAULT NULL
) ;

--
-- Dumping data for table `encadrant`
--

INSERT INTO `encadrant` (`ID_ENC`, `NOM_ENC`, `PRN_ENC`, `MAIL_ENC`, `MDP_ENC`, `IMG_ENC`, `TEL_ENC`) VALUES
(1, 'Encadrant1', 'Prenom1', 'enc1@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000001'),
(2, 'Encadrant2', 'Prenom2', 'enc2@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000002'),
(3, 'Encadrant3', 'Prenom3', 'enc3@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000003'),
(4, 'Encadrant4', 'Prenom4', 'enc4@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000004'),
(5, 'Encadrant5', 'Prenom5', 'enc5@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000005'),
(6, 'Encadrant6', 'Prenom6', 'enc6@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000006'),
(7, 'Encadrant7', 'Prenom7', 'enc7@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000007'),
(8, 'Encadrant8', 'Prenom8', 'enc8@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000008'),
(9, 'Encadrant9', 'Prenom9', 'enc9@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000009'),
(10, 'Encadrant10', 'Prenom10', 'enc10@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000010'),
(11, 'Encadrant11', 'Prenom11', 'enc11@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000011'),
(12, 'Encadrant12', 'Prenom12', 'enc12@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000012'),
(13, 'Encadrant13', 'Prenom13', 'enc13@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000013'),
(14, 'Encadrant14', 'Prenom14', 'enc14@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000014'),
(15, 'Encadrant15', 'Prenom15', 'enc15@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000015'),
(16, 'Encadrant16', 'Prenom16', 'enc16@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000016'),
(17, 'Encadrant17', 'Prenom17', 'enc17@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000017'),
(18, 'Encadrant18', 'Prenom18', 'enc18@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000018'),
(19, 'Encadrant19', 'Prenom19', 'enc19@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000019'),
(20, 'Encadrant20', 'Prenom20', 'enc20@mail.com', 'Password123!', 'C:\\Users\\Missaoui\\Pictures\\node js ibm\\untilited.png', '+212600000020');

-- --------------------------------------------------------

--
-- Table structure for table `groupe`
--

CREATE TABLE `groupe` (
  `ID_GRP` int NOT NULL,
  `NUM_GRP` varchar(50) NOT NULL,
  `ID_NIV` int NOT NULL,
  `ID_STAGE` int NOT NULL
) ;

--
-- Dumping data for table `groupe`
--

INSERT INTO `groupe` (`ID_GRP`, `NUM_GRP`, `ID_NIV`, `ID_STAGE`) VALUES
(1, 'Groupe A', 1, 1),
(2, 'Groupe B', 2, 2),
(3, 'Groupe C', 3, 3),
(4, 'Groupe D', 4, 4),
(5, 'Groupe E', 5, 5),
(6, 'Groupe F', 6, 6),
(7, 'Groupe G', 7, 7),
(8, 'Groupe H', 1, 8),
(9, 'Groupe I', 2, 9),
(10, 'Groupe J', 3, 10);

-- --------------------------------------------------------

--
-- Table structure for table `messagerie`
--

CREATE TABLE `messagerie` (
  `ID_MSG` int NOT NULL,
  `CONT_MSG` varchar(255) NOT NULL,
  `DATE_MSG` timestamp NOT NULL,
  `ID_ENC` int DEFAULT NULL,
  `ID_STG` int DEFAULT NULL
) ;

--
-- Dumping data for table `messagerie`
--

INSERT INTO `messagerie` (`ID_MSG`, `CONT_MSG`, `DATE_MSG`, `ID_ENC`, `ID_STG`) VALUES
(62, 'tes', '2025-04-21 23:00:00', NULL, 10),
(63, 'hhz', '2025-04-21 23:00:00', NULL, 10),
(64, 'j', '2025-04-21 23:00:00', NULL, 10),
(65, 'gv', '2025-04-21 23:00:00', 1, NULL),
(66, 'vhv', '2025-04-21 23:00:00', 1, NULL),
(67, 'fhd', '2025-04-21 23:00:00', 1, NULL),
(68, 'hh', '2025-04-21 23:00:00', 1, NULL),
(69, 'hhh', '2025-04-21 23:00:00', NULL, 10),
(70, 'hjk', '2025-04-21 23:00:00', NULL, 10),
(71, 'j', '2025-04-21 23:00:00', NULL, 10),
(77, 'hhf', '2025-04-22 00:09:43', NULL, 10),
(83, 'how are u', '2025-04-22 08:41:12', NULL, 10),
(84, 'jj', '2025-04-22 19:57:37', NULL, 10),
(85, 'jj', '2025-04-22 19:57:58', NULL, 10),
(86, 'ifif', '2025-04-22 19:59:52', NULL, 2),
(87, 'j', '2025-04-22 20:14:43', NULL, 2),
(88, 'hh', '2025-04-22 20:16:04', NULL, 2),
(89, 'jcjg', '2025-04-22 20:16:18', NULL, 2),
(90, 'cgjcd', '2025-04-22 20:18:04', NULL, 2),
(91, 'gc', '2025-04-22 20:19:03', NULL, 2),
(92, 'hh', '2025-04-22 20:21:57', NULL, 2),
(93, 'gg', '2025-04-24 19:56:31', NULL, 5),
(94, 'ghj', '2025-04-25 21:58:14', NULL, 1),
(95, 'hz', '2025-04-26 21:42:37', 1, NULL),
(97, 'zxz', '2025-04-27 18:57:58', NULL, 1),
(98, 'kf', '2025-04-27 21:18:20', NULL, 1),
(99, 'gf', '2025-04-27 21:35:36', NULL, 1),
(100, 'hg', '2025-04-27 21:39:35', NULL, 1),
(101, 'ghfd', '2025-04-27 21:39:40', NULL, 1),
(102, 'lyouma ndiro tp', '2025-04-27 21:40:08', NULL, 1),
(103, 'jjjjhhhh', '2025-04-27 22:05:29', NULL, 2),
(104, 's', '2025-04-27 23:21:52', 1, NULL),
(105, 'slm cv', '2025-04-28 17:29:14', NULL, 2),
(106, 'bkhir', '2025-04-28 17:29:22', NULL, 2),
(107, 'sjjd', '2025-04-28 17:29:24', NULL, 2),
(108, 'jj', '2025-05-02 19:24:47', NULL, 5),
(109, 'jk', '2025-05-02 19:24:49', NULL, 5),
(110, 'i', '2025-05-02 19:25:02', NULL, 5),
(111, 'kj', '2025-05-02 19:25:04', NULL, 5),
(112, 'jj', '2025-05-02 19:25:05', NULL, 5),
(113, 'hkzifik', '2025-05-02 19:25:07', NULL, 5),
(114, 'j', '2025-05-02 19:53:47', NULL, 5),
(115, 'ffoor', '2025-05-02 19:54:59', NULL, 5),
(116, 'h', '2025-05-02 19:56:40', NULL, 5),
(117, 'jjj', '2025-05-02 19:57:52', NULL, 5),
(118, 'jjj', '2025-05-02 20:00:59', NULL, 5),
(119, 'hhh', '2025-05-02 20:03:00', NULL, 5),
(120, 'hh', '2025-05-02 20:23:38', NULL, 2),
(122, 'uuu', '2025-05-02 20:26:52', NULL, 2),
(123, 'hj', '2025-05-02 20:49:13', NULL, 2),
(126, 'hj', '2025-05-02 20:57:55', NULL, 2),
(128, 'h', '2025-05-02 21:24:54', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `niveau`
--

CREATE TABLE `niveau` (
  `ID_NIV` int NOT NULL,
  `NUM_NIV` varchar(50) NOT NULL
) ;

--
-- Dumping data for table `niveau`
--

INSERT INTO `niveau` (`ID_NIV`, `NUM_NIV`) VALUES
(1, 'Niveau 1'),
(2, 'Niveau 2'),
(3, 'Niveau 3'),
(4, 'Niveau 4'),
(5, 'Niveau 5'),
(6, 'Niveau 6'),
(7, 'Niveau 7');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `ID_NOTIF` int NOT NULL,
  `DATE_NOTIF` date NOT NULL,
  `CONT_NOTIF` varchar(255) NOT NULL,
  `ID_ADM` int NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `recu`
--

CREATE TABLE `recu` (
  `ID_ENC` int NOT NULL,
  `ID_NOTIF` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `ID_SER` int NOT NULL,
  `NOM_SER` varchar(50) NOT NULL,
  `ID_STAGE` int NOT NULL
) ;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`ID_SER`, `NOM_SER`, `ID_STAGE`) VALUES
(21, 'Service de Médecine Générale', 1),
(22, 'Service de Cardiologie', 2),
(23, 'Service de Neurologie', 3),
(24, 'Service de Pédiatrie', 4),
(25, 'Service de Chirurgie', 5),
(26, 'Service d\'Urgence', 6),
(27, 'Service de Radiologie', 7),
(28, 'Service de Dermatologie', 8),
(29, 'Service de Gynécologie', 9),
(30, 'Service d\'Oncologie', 10);

-- --------------------------------------------------------

--
-- Table structure for table `stage`
--

CREATE TABLE `stage` (
  `ID_STAGE` int NOT NULL,
  `DATE_DEB_STG` date NOT NULL,
  `DATE_FIN_STG` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stage`
--

INSERT INTO `stage` (`ID_STAGE`, `DATE_DEB_STG`, `DATE_FIN_STG`) VALUES
(1, '2025-03-01', '2025-06-30'),
(2, '2025-04-01', '2025-07-31'),
(3, '2025-05-01', '2025-08-31'),
(4, '2025-06-01', '2025-09-30'),
(5, '2025-07-01', '2025-10-31'),
(6, '2025-08-01', '2025-11-30'),
(7, '2025-09-01', '2025-12-31'),
(8, '2025-10-01', '2026-01-31'),
(9, '2025-11-01', '2026-02-28'),
(10, '2025-12-01', '2026-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `stagiaire`
--

CREATE TABLE `stagiaire` (
  `ID_STG` int NOT NULL,
  `NOM_STG` varchar(50) NOT NULL,
  `PRN_STG` varchar(50) NOT NULL,
  `DNAISS_STG` date NOT NULL,
  `MAIL_STG` varchar(50) NOT NULL,
  `MDP_STG` varchar(255) NOT NULL,
  `IMG_STG` varchar(255) DEFAULT NULL,
  `TEL_STG` varchar(20) DEFAULT NULL,
  `ID_ENC` int NOT NULL,
  `ID_NIV` int NOT NULL
) ;

--
-- Dumping data for table `stagiaire`
--

INSERT INTO `stagiaire` (`ID_STG`, `NOM_STG`, `PRN_STG`, `DNAISS_STG`, `MAIL_STG`, `MDP_STG`, `IMG_STG`, `TEL_STG`, `ID_ENC`, `ID_NIV`) VALUES
(1, 'Stagiaire1', 'Prenom1', '2000-01-01', 'stag1@mail.com', 'Password123!', NULL, '+212611111111', 1, 1),
(2, 'Stagiaire2', 'Prenom2', '2000-02-02', 'stag2@mail.com', 'Password123!', NULL, '+212611111112', 2, 1),
(3, 'Stagiaire3', 'Prenom3', '2000-03-03', 'stag3@mail.com', 'Password123!', NULL, '+212611111113', 3, 2),
(4, 'Stagiaire4', 'Prenom4', '2000-04-04', 'stag4@mail.com', 'Password123!', NULL, '+212611111114', 4, 2),
(5, 'Stagiaire5', 'Prenom5', '2000-05-05', 'stag5@mail.com', 'Password123!', NULL, '+212611111115', 5, 3),
(6, 'Stagiaire6', 'Prenom6', '2000-06-06', 'stag6@mail.com', 'Password123!', NULL, '+212611111116', 1, 3),
(7, 'Stagiaire7', 'Prenom7', '2000-07-07', 'stag7@mail.com', 'Password123!', NULL, '+212611111117', 2, 4),
(8, 'Stagiaire8', 'Prenom8', '2000-08-08', 'stag8@mail.com', 'Password123!', NULL, '+212611111118', 3, 4),
(9, 'Stagiaire9', 'Prenom9', '2000-09-09', 'stag9@mail.com', 'Password123!', NULL, '+212611111119', 4, 5),
(10, 'Stagiaire10', 'Prenom10', '2000-10-10', 'stag10@mail.com', 'Password123!', NULL, '+212611111120', 5, 5),
(11, 'Stagiaire11', 'Prenom11', '2000-11-11', 'stag11@mail.com', 'Password123!', NULL, '+212611111121', 1, 6),
(12, 'Stagiaire12', 'Prenom12', '2000-12-12', 'stag12@mail.com', 'Password123!', NULL, '+212611111122', 2, 6),
(13, 'Stagiaire13', 'Prenom13', '2001-01-13', 'stag13@mail.com', 'Password123!', NULL, '+212611111123', 3, 7),
(14, 'Stagiaire14', 'Prenom14', '2001-02-14', 'stag14@mail.com', 'Password123!', NULL, '+212611111124', 4, 7),
(15, 'Stagiaire15', 'Prenom15', '2001-03-15', 'stag15@mail.com', 'Password123!', NULL, '+212611111125', 5, 1),
(16, 'Stagiaire16', 'Prenom16', '2001-04-16', 'stag16@mail.com', 'Password123!', NULL, '+212611111126', 1, 2),
(17, 'Stagiaire17', 'Prenom17', '2001-05-17', 'stag17@mail.com', 'Password123!', NULL, '+212611111127', 2, 3),
(18, 'Stagiaire18', 'Prenom18', '2001-06-18', 'stag18@mail.com', 'Password123!', NULL, '+212611111128', 3, 4),
(19, 'Stagiaire19', 'Prenom19', '2001-07-19', 'stag19@mail.com', 'Password123!', NULL, '+212611111129', 4, 5),
(20, 'Stagiaire20', 'Prenom20', '2001-08-20', 'stag20@mail.com', 'Password123!', NULL, '+212611111130', 5, 6),
(21, 'Stagiaire21', 'Prenom21', '2001-09-21', 'stag21@mail.com', 'Password123!', NULL, '+212611111131', 1, 1),
(22, 'Stagiaire22', 'Prenom22', '2001-10-22', 'stag22@mail.com', 'Password123!', NULL, '+212611111132', 2, 2),
(23, 'Stagiaire23', 'Prenom23', '2001-11-23', 'stag23@mail.com', 'Password123!', NULL, '+212611111133', 3, 3),
(24, 'Stagiaire24', 'Prenom24', '2001-12-24', 'stag24@mail.com', 'Password123!', NULL, '+212611111134', 4, 4),
(25, 'Stagiaire25', 'Prenom25', '2002-01-25', 'stag25@mail.com', 'Password123!', NULL, '+212611111135', 5, 5),
(26, 'Stagiaire26', 'Prenom26', '2002-02-26', 'stag26@mail.com', 'Password123!', NULL, '+212611111136', 1, 6),
(27, 'Stagiaire27', 'Prenom27', '2002-03-27', 'stag27@mail.com', 'Password123!', NULL, '+212611111137', 2, 7),
(28, 'Stagiaire28', 'Prenom28', '2002-04-28', 'stag28@mail.com', 'Password123!', NULL, '+212611111138', 3, 1),
(29, 'Stagiaire29', 'Prenom29', '2002-05-29', 'stag29@mail.com', 'Password123!', NULL, '+212611111139', 4, 2),
(30, 'Stagiaire30', 'Prenom30', '2002-06-30', 'stag30@mail.com', 'Password123!', NULL, '+212611111140', 5, 3),
(31, 'Stagiaire31', 'Prenom31', '2002-07-31', 'stag31@mail.com', 'Password123!', NULL, '+212611111141', 1, 4),
(32, 'Stagiaire32', 'Prenom32', '2002-08-01', 'stag32@mail.com', 'Password123!', NULL, '+212611111142', 2, 5),
(33, 'Stagiaire33', 'Prenom33', '2002-09-02', 'stag33@mail.com', 'Password123!', NULL, '+212611111143', 3, 6),
(34, 'Stagiaire34', 'Prenom34', '2002-10-03', 'stag34@mail.com', 'Password123!', NULL, '+212611111144', 4, 7),
(35, 'Stagiaire35', 'Prenom35', '2002-11-04', 'stag35@mail.com', 'Password123!', NULL, '+212611111145', 5, 1),
(36, 'Stagiaire36', 'Prenom36', '2002-12-05', 'stag36@mail.com', 'Password123!', NULL, '+212611111146', 1, 2),
(37, 'Stagiaire37', 'Prenom37', '2003-01-06', 'stag37@mail.com', 'Password123!', NULL, '+212611111147', 2, 3),
(38, 'Stagiaire38', 'Prenom38', '2003-02-07', 'stag38@mail.com', 'Password123!', NULL, '+212611111148', 3, 4),
(39, 'Stagiaire39', 'Prenom39', '2003-03-08', 'stag39@mail.com', 'Password123!', NULL, '+212611111149', 4, 5),
(40, 'Stagiaire40', 'Prenom40', '2003-04-09', 'stag40@mail.com', 'Password123!', NULL, '+212611111150', 5, 6),
(41, 'Stagiaire41', 'Prenom41', '2003-05-10', 'stag41@mail.com', 'Password123!', NULL, '+212611111151', 1, 7),
(42, 'Stagiaire42', 'Prenom42', '2003-06-11', 'stag42@mail.com', 'Password123!', NULL, '+212611111152', 2, 1),
(43, 'Stagiaire43', 'Prenom43', '2003-07-12', 'stag43@mail.com', 'Password123!', NULL, '+212611111153', 3, 2),
(44, 'Stagiaire44', 'Prenom44', '2003-08-13', 'stag44@mail.com', 'Password123!', NULL, '+212611111154', 4, 3),
(45, 'Stagiaire45', 'Prenom45', '2003-09-14', 'stag45@mail.com', 'Password123!', NULL, '+212611111155', 5, 4),
(46, 'Stagiaire46', 'Prenom46', '2003-10-15', 'stag46@mail.com', 'Password123!', NULL, '+212611111156', 1, 5),
(47, 'Stagiaire47', 'Prenom47', '2003-11-16', 'stag47@mail.com', 'Password123!', NULL, '+212611111157', 2, 6),
(48, 'Stagiaire48', 'Prenom48', '2003-12-17', 'stag48@mail.com', 'Password123!', NULL, '+212611111158', 3, 7),
(49, 'Stagiaire49', 'Prenom49', '2004-01-18', 'stag49@mail.com', 'Password123!', NULL, '+212611111159', 4, 1),
(50, 'Stagiaire50', 'Prenom50', '2004-02-19', 'stag50@mail.com', 'Password123!', NULL, '+212611111160', 5, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID_ADM`),
  ADD UNIQUE KEY `MAIL_ADM` (`MAIL_ADM`);

--
-- Indexes for table `encadrant`
--
ALTER TABLE `encadrant`
  ADD PRIMARY KEY (`ID_ENC`),
  ADD UNIQUE KEY `MAIL_ENC` (`MAIL_ENC`),
  ADD UNIQUE KEY `TEL_ENC` (`TEL_ENC`);

--
-- Indexes for table `groupe`
--
ALTER TABLE `groupe`
  ADD PRIMARY KEY (`ID_GRP`),
  ADD KEY `ID_NIV` (`ID_NIV`),
  ADD KEY `ID_STAGE` (`ID_STAGE`);

--
-- Indexes for table `messagerie`
--
ALTER TABLE `messagerie`
  ADD PRIMARY KEY (`ID_MSG`),
  ADD KEY `ID_ENC` (`ID_ENC`),
  ADD KEY `fk_messagerie_stagiaire` (`ID_STG`);

--
-- Indexes for table `niveau`
--
ALTER TABLE `niveau`
  ADD PRIMARY KEY (`ID_NIV`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`ID_NOTIF`),
  ADD KEY `ID_ADM` (`ID_ADM`);

--
-- Indexes for table `recu`
--
ALTER TABLE `recu`
  ADD PRIMARY KEY (`ID_ENC`,`ID_NOTIF`),
  ADD KEY `ID_NOTIF` (`ID_NOTIF`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`ID_SER`),
  ADD KEY `ID_STAGE` (`ID_STAGE`);

--
-- Indexes for table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`ID_STAGE`);

--
-- Indexes for table `stagiaire`
--
ALTER TABLE `stagiaire`
  ADD PRIMARY KEY (`ID_STG`),
  ADD UNIQUE KEY `MAIL_STG` (`MAIL_STG`),
  ADD UNIQUE KEY `TEL_STG` (`TEL_STG`),
  ADD KEY `ID_ENC` (`ID_ENC`),
  ADD KEY `ID_NIV` (`ID_NIV`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID_ADM` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `encadrant`
--
ALTER TABLE `encadrant`
  MODIFY `ID_ENC` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groupe`
--
ALTER TABLE `groupe`
  MODIFY `ID_GRP` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messagerie`
--
ALTER TABLE `messagerie`
  MODIFY `ID_MSG` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `niveau`
--
ALTER TABLE `niveau`
  MODIFY `ID_NIV` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `ID_NOTIF` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `ID_SER` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stage`
--
ALTER TABLE `stage`
  MODIFY `ID_STAGE` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `stagiaire`
--
ALTER TABLE `stagiaire`
  MODIFY `ID_STG` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groupe`
--
ALTER TABLE `groupe`
  ADD CONSTRAINT `groupe_ibfk_1` FOREIGN KEY (`ID_NIV`) REFERENCES `niveau` (`ID_NIV`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `groupe_ibfk_2` FOREIGN KEY (`ID_STAGE`) REFERENCES `stage` (`ID_STAGE`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messagerie`
--
ALTER TABLE `messagerie`
  ADD CONSTRAINT `fk_messagerie_stagiaire` FOREIGN KEY (`ID_STG`) REFERENCES `stagiaire` (`ID_STG`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messagerie_ibfk_1` FOREIGN KEY (`ID_ENC`) REFERENCES `encadrant` (`ID_ENC`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`ID_ADM`) REFERENCES `admin` (`ID_ADM`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recu`
--
ALTER TABLE `recu`
  ADD CONSTRAINT `recu_ibfk_1` FOREIGN KEY (`ID_ENC`) REFERENCES `encadrant` (`ID_ENC`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recu_ibfk_2` FOREIGN KEY (`ID_NOTIF`) REFERENCES `notification` (`ID_NOTIF`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`ID_STAGE`) REFERENCES `stage` (`ID_STAGE`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stagiaire`
--
ALTER TABLE `stagiaire`
  ADD CONSTRAINT `stagiaire_ibfk_1` FOREIGN KEY (`ID_ENC`) REFERENCES `encadrant` (`ID_ENC`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stagiaire_ibfk_2` FOREIGN KEY (`ID_NIV`) REFERENCES `niveau` (`ID_NIV`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
