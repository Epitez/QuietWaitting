-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 21 Octobre 2014 à 13:37
-- Version du serveur :  5.6.20
-- Version de PHP :  5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `q`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AJOUTER_SERVICE`(IN `P_NOM_SERVICE` TEXT)
    NO SQL
INSERT INTO services (name) VALUES (P_NOM_SERVICE)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CLOSE_GUICHET`(IN `P_ID_GUICHET` INT)
BEGIN

	UPDATE guichet
    SET ouvert=1
    WHERE id=P_ID_GUICHET;

    DELETE FROM services_par_guichet WHERE id_guichet = P_ID_GUICHET;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CLOSE_TICKET`(IN `P_ID_TICKET` INT)
    NO SQL
UPDATE ticket
SET state = 'fini',
	ferme = NOW()
WHERE id = P_ID_TICKET$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CLOSE_TICKET_ABSENT`(IN `P_ID_TICKET` INT)
    NO SQL
UPDATE ticket
	SET state = 'fini',
    	absent = '1'
WHERE id = P_ID_TICKET$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ETAT_TICKET`(IN `P_ID_TICKET` INT)
    NO SQL
SELECT state FROM ticket
WHERE id = P_ID_TICKET$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LISTER_SERVICES`()
    READS SQL DATA
SELECT *
FROM services
GROUP BY services.id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LISTER_SERVICE_GUICHET`(IN `P_ID_GUICHET` INT)
    NO SQL
SELECT *
FROM services_par_guichet
WHERE services_par_guichet.id_guichet = P_ID_GUICHET$$

--
-- Fonctions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `INSERT_TICKET`(`P_ID_SERVICES` INT, `P_ID_BORNE` INT) RETURNS int(11)
BEGIN

	INSERT INTO ticket(state, ouvert, id_service, id_borne) VALUES ('en attente', NOW(), P_ID_SERVICES, P_ID_BORNE);

	RETURN (SELECT LAST_INSERT_ID());

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `borne`
--

CREATE TABLE IF NOT EXISTS `borne` (
`id` int(3) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `nb_delivered` int(3) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `guichet`
--

CREATE TABLE IF NOT EXISTS `guichet` (
`id` int(11) NOT NULL,
  `name` text NOT NULL,
  `ouvert` tinyint(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
`id` int(3) NOT NULL COMMENT 'cle primaire',
  `name` text NOT NULL COMMENT 'nom du service'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `services_par_guichet`
--

CREATE TABLE IF NOT EXISTS `services_par_guichet` (
  `id` int(11) NOT NULL DEFAULT '0',
  `id_guichet` int(11) DEFAULT NULL,
  `id_service` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

CREATE TABLE IF NOT EXISTS `ticket` (
`id` int(3) NOT NULL,
  `state` text NOT NULL COMMENT 'en cours/traite/annulé/rappel',
  `absent` tinyint(1) NOT NULL DEFAULT '0',
  `ouvert` datetime NOT NULL COMMENT 'date emission',
  `ferme` datetime NOT NULL COMMENT 'date fin',
  `id_borne` int(11) NOT NULL,
  `id_service` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=388 ;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `borne`
--
ALTER TABLE `borne`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `guichet`
--
ALTER TABLE `guichet`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `services_par_guichet`
--
ALTER TABLE `services_par_guichet`
 ADD PRIMARY KEY (`id`), ADD KEY `FK_SERVICES_PAR_GUICHER_ID_GUICHET` (`id_guichet`);

--
-- Index pour la table `ticket`
--
ALTER TABLE `ticket`
 ADD PRIMARY KEY (`id`), ADD KEY `id_borne_2` (`id_borne`), ADD KEY `id_service` (`id_service`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `borne`
--
ALTER TABLE `borne`
MODIFY `id` int(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `guichet`
--
ALTER TABLE `guichet`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
MODIFY `id` int(3) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `ticket`
--
ALTER TABLE `ticket`
MODIFY `id` int(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=388;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `services_par_guichet`
--
ALTER TABLE `services_par_guichet`
ADD CONSTRAINT `FK_SERVICES_PAR_GUICHER_ID_GUICHET` FOREIGN KEY (`id_guichet`) REFERENCES `guichet` (`id`);

--
-- Contraintes pour la table `ticket`
--
ALTER TABLE `ticket`
ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`id_borne`) REFERENCES `borne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `services` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
