-- phpMyAdmin SQL Dump
-- version 4.2.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2014 at 02:48 AM
-- Server version: 5.6.21
-- PHP Version: 5.5.14

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `q`
--
CREATE DATABASE IF NOT EXISTS `q` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `q`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `AJOUTER_SERVICE`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AJOUTER_SERVICE`(IN `P_NOM_SERVICE` TEXT)
    NO SQL
INSERT INTO services (name) VALUES (P_NOM_SERVICE)$$

DROP PROCEDURE IF EXISTS `CLOSE_GUICHET`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CLOSE_GUICHET`(IN `P_ID_GUICHET` INT)
BEGIN

	UPDATE guichet
    SET ouvert=1
    WHERE id=P_ID_GUICHET;

    DELETE FROM services_par_guichet WHERE id_guichet = P_ID_GUICHET;
END$$

DROP PROCEDURE IF EXISTS `CLOSE_TICKET`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CLOSE_TICKET`(IN `P_ID_TICKET` INT)
    NO SQL
UPDATE ticket
SET state = 'fini',
	ferme = NOW()
WHERE id = P_ID_TICKET$$

DROP PROCEDURE IF EXISTS `CLOSE_TICKET_ABSENT`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CLOSE_TICKET_ABSENT`(IN `P_ID_TICKET` INT)
    NO SQL
UPDATE ticket
	SET state = 'fini',
    	absent = '1'
WHERE id = P_ID_TICKET$$

DROP PROCEDURE IF EXISTS `ETAT_TICKET`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ETAT_TICKET`(IN `P_ID_TICKET` INT)
    NO SQL
SELECT state FROM ticket
WHERE id = P_ID_TICKET$$

DROP PROCEDURE IF EXISTS `LISTER_SERVICES`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `LISTER_SERVICES`()
    READS SQL DATA
SELECT *
FROM services
GROUP BY services.id$$

DROP PROCEDURE IF EXISTS `LISTER_SERVICE_GUICHET`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `LISTER_SERVICE_GUICHET`(IN `P_ID_GUICHET` INT)
    NO SQL
SELECT *
FROM services_par_guichet
WHERE services_par_guichet.id_guichet = P_ID_GUICHET$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `INSERT_TICKET`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `INSERT_TICKET`(`P_ID_SERVICES` INT, `P_ID_BORNE` INT) RETURNS int(11)
BEGIN

	INSERT INTO ticket(state, ouvert, id_service, id_borne) VALUES ('en attente', NOW(), P_ID_SERVICES, P_ID_BORNE);

	RETURN (SELECT LAST_INSERT_ID());

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bornes`
--

DROP TABLE IF EXISTS `bornes`;
CREATE TABLE IF NOT EXISTS `bornes` (
`id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `nb_delivered` int(11) NOT NULL,
  `token` int(11) NOT NULL,
  `type` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guichets`
--

DROP TABLE IF EXISTS `guichets`;
CREATE TABLE IF NOT EXISTS `guichets` (
`id` int(11) NOT NULL,
  `name` text NOT NULL,
  `ouvert` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
`id` int(11) NOT NULL COMMENT 'cle primaire',
  `name` text NOT NULL COMMENT 'nom du service'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services_par_guichets`
--

DROP TABLE IF EXISTS `services_par_guichets`;
CREATE TABLE IF NOT EXISTS `services_par_guichets` (
`id` int(11) NOT NULL,
  `id_guichet` int(11) DEFAULT NULL,
  `id_service` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `services_par_guichets`:
--   `id_guichet`
--       `guichets` -> `id`
--   `id_service`
--       `services` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
`id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `session` int(11) NOT NULL,
  `state` text NOT NULL COMMENT 'en cours/traite/annulé/rappel',
  `absent` tinyint(1) NOT NULL DEFAULT '0',
  `ouvert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'date emission',
  `ferme` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'date fin',
  `id_borne` int(11) NOT NULL,
  `id_service` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `tickets`:
--   `id_borne`
--       `bornes` -> `id`
--   `id_service`
--       `services` -> `id`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bornes`
--
ALTER TABLE `bornes`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guichets`
--
ALTER TABLE `guichets`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services_par_guichets`
--
ALTER TABLE `services_par_guichets`
 ADD PRIMARY KEY (`id`), ADD KEY `FK_SERVICES_PAR_GUICHER_ID_GUICHET` (`id_guichet`), ADD KEY `FK_SERVICES_PAR_GUICHER_ID_SERVICES` (`id_service`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
 ADD PRIMARY KEY (`id`), ADD KEY `id_borne_2` (`id_borne`), ADD KEY `id_service` (`id_service`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bornes`
--
ALTER TABLE `bornes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `guichets`
--
ALTER TABLE `guichets`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire',AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `services_par_guichets`
--
ALTER TABLE `services_par_guichets`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `services_par_guichets`
--
ALTER TABLE `services_par_guichets`
ADD CONSTRAINT `FK_SERVICES_PAR_GUICHER_ID_GUICHET` FOREIGN KEY (`id_guichet`) REFERENCES `guichets` (`id`),
ADD CONSTRAINT `FK_SERVICES_PAR_GUICHER_ID_SERVICES` FOREIGN KEY (`id_service`) REFERENCES `services` (`id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_borne`) REFERENCES `bornes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `services` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
