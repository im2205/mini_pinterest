-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 29 Avril 2020 à 20:02
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `bdd.sql`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE IF NOT EXISTS `categorie` (
  `catId` int(11) NOT NULL,
  `nomCat` varchar(250) NOT NULL,
  PRIMARY KEY (`catId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `categorie`
--

INSERT INTO `categorie` (`catId`, `nomCat`) VALUES
(0, 'Jeux'),
(1, 'Animaux'),
(2, 'Personnage'),
(3, 'Informatique'),
(4, 'Paysage'),
(5, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

CREATE TABLE IF NOT EXISTS `photo` (
  `photoId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomFich` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `catId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `etat` varchar(11) NOT NULL,
  PRIMARY KEY (`photoId`),
  KEY `catId` (`catId`),
  KEY `photo_ibfk_2` (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Contenu de la table `photo`
--

INSERT INTO `photo` (`photoId`, `nomFich`, `description`, `catId`, `UserId`, `etat`) VALUES
(1, 'sun.jpg', 'Coucher de soleil', 4, 2, 'montre'),
(2, 'joel.png', 'This is a game', 0, 1, 'montre'),
(3, 'tlou2.jpg', 'Environnement du jeu The Last Of Us 2 ', 0, 1, 'montre'),
(4, 'funnyGoat.jpg', 'Juste un mouton entrain profiter de sa vie, alors que nous on est en confinement', 1, 3, 'montre'),
(5, 'funny-goat-1.jpg', 'un agneau entrain de tirer sa langue', 1, 1, 'montre'),
(6, 'snoop-dog.jpg', 'Dessin caricaturique de snoop-dog réalisé par TwitterPicasso', 2, 1, 'montre'),
(7, 'smith.jpg', 'Dessin caricaturique réalisé par TwitterPicasso ', 2, 0, 'montre'),
(8, 'johnny.jpg', 'Dessin caricaturique réalisé par TwitterPicasso', 2, 0, 'montre'),
(9, 'bully.jpg', 'Dessin caricaturique réalisé par TwitterPicasso', 2, 1, 'montre'),
(10, 'faze.jpg', 'Dessin caricaturique réalisé par TwitterPicasso', 2, 0, 'montre'),
(11, 'kali-linux.jpg', 'Distribution linux qui vient remplacer BackTrack, dédié aux tests de sécurité d''un système d''information, notamment le test d''intrusion', 3, 0, 'montre'),
(12, 'funny-dog.jpg', 'Un animal mega drole', 1, 0, 'montre'),
(13, 'index.png', 'hello guys', 0, 0, 'montre'),
(14, 'sp4.png', 'vaisseau de ouf', 0, 0, 'montre');
-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `UserId` int(10) NOT NULL AUTO_INCREMENT,
  `Pseudo` varchar(25) NOT NULL,
  `PassWord` varchar(25) NOT NULL,
  `Profil` varchar(25) NOT NULL,
  `connecte` int(11) NOT NULL,
  `active` varchar(5) NOT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`UserId`, `Pseudo`, `PassWord`, `Profil`, `connecte`, `active`) VALUES
(0, 'Admin1', 'admin', 'Administrateur', 0, 'oui'),
(1, 'user1', 'u', 'Utilisateur', 0, 'oui'),
(2, 'user2', 'u2', 'Utilisateur', 0,  'oui'),
(3, 'freak', 'rien', 'Utilisateur', 0,  'oui'),
(5, 'boss', 'boss', 'Utilisateur', 0, 'oui');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `photo_ibfk_1` FOREIGN KEY (`catId`) REFERENCES `categorie` (`catId`),
  ADD CONSTRAINT `photo_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `utilisateur` (`UserId`);



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
