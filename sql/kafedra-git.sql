-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 21, 2014 at 03:52 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kafedra-git`
--

-- --------------------------------------------------------

--
-- Table structure for table `authorpublications`
--

CREATE TABLE IF NOT EXISTS `authorpublications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authorid` int(11) NOT NULL,
  `publicationid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=262 ;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `isforeign` int(11) NOT NULL,
  `studentgroup` varchar(16) NOT NULL,
  `isaspirant` int(11) NOT NULL DEFAULT '0',
  `welcomename` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `pwd` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `isforeign`, `studentgroup`, `isaspirant`, `welcomename`, `email`, `pwd`) VALUES
(36, 'Иванов И. И.', 0, '', 0, 'Иван Иванович', 'test@example.com', '4609382ecb327413846c2f192e8dbf7f01999de1');

-- --------------------------------------------------------

--
-- Table structure for table `evlevels`
--

CREATE TABLE IF NOT EXISTS `evlevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `evlevels`
--

INSERT INTO `evlevels` (`id`, `name`) VALUES
(1, 'международный'),
(2, 'всероссийский'),
(3, 'региональный'),
(4, 'на базе университета');

-- --------------------------------------------------------

--
-- Table structure for table `evstatuses`
--

CREATE TABLE IF NOT EXISTS `evstatuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `evstatuses`
--

INSERT INTO `evstatuses` (`id`, `name`) VALUES
(1, 'студенческий'),
(2, 'молодых ученых'),
(3, 'профессорско-преподавательского состава');

-- --------------------------------------------------------

--
-- Table structure for table `evtypes`
--

CREATE TABLE IF NOT EXISTS `evtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `evtypes`
--

INSERT INTO `evtypes` (`id`, `name`) VALUES
(1, 'научно-практическая конференция'),
(2, 'симпозиум'),
(3, 'научно-практический семинар'),
(4, 'круглый стол'),
(5, 'учебно-научная конференция'),
(6, 'научная конференция'),
(7, 'научный конгресс');

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE IF NOT EXISTS `journals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `publisherid` int(11) NOT NULL,
  `inscopus` int(11) NOT NULL COMMENT 'bool',
  `type` int(11) NOT NULL COMMENT 'journaltypes.id',
  `impfactor` float NOT NULL,
  `invak` int(11) NOT NULL COMMENT 'bool',
  `reviewed` int(11) NOT NULL COMMENT 'bool',
  `sceventid` int(11) NOT NULL,
  `inrinc` int(11) NOT NULL COMMENT 'bool',
  `inwos` int(11) NOT NULL COMMENT 'bool',
  `inforeignindex` int(11) NOT NULL COMMENT 'bool',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `journaltypes`
--

CREATE TABLE IF NOT EXISTS `journaltypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `journaltypes`
--

INSERT INTO `journaltypes` (`id`, `name`) VALUES
(1, 'рецензируемый журнал с импакт-фактором 0,001–0,100 или без импакт-фактора, но входящий в перечень ВАК'),
(2, 'рецензируемый журнал с импакт-фактором выше 0,100'),
(3, 'другое издание, зарегистрированное в РИНЦ, или закрытый сборник научных работ'),
(4, 'другой журнал или сборник научных работ');

-- --------------------------------------------------------

--
-- Table structure for table `langtypes`
--

CREATE TABLE IF NOT EXISTS `langtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `langtypes`
--

INSERT INTO `langtypes` (`id`, `name`) VALUES
(1, 'рус.'),
(2, 'англ.');

-- --------------------------------------------------------

--
-- Table structure for table `ordertypes`
--

CREATE TABLE IF NOT EXISTS `ordertypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ordertypes`
--

INSERT INTO `ordertypes` (`id`, `name`) VALUES
(1, 'с грифом уполномоченного вуза'),
(2, 'с грифом УМО'),
(3, 'по решению вуза'),
(4, 'с грифом Минобрнауки'),
(5, 'с грифом НМС'),
(6, 'с грифом других федеральных органов исполнительной власти');

-- --------------------------------------------------------

--
-- Table structure for table `participations`
--

CREATE TABLE IF NOT EXISTS `participations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sceventid` int(11) NOT NULL,
  `authorid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE IF NOT EXISTS `publications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `journalid` int(11) NOT NULL DEFAULT '0' COMMENT 'if it is article',
  `publisherid` int(11) NOT NULL DEFAULT '0' COMMENT 'if it is not article',
  `year` int(11) NOT NULL,
  `journalnumber` varchar(16) NOT NULL DEFAULT '',
  `journalpagestart` int(11) NOT NULL DEFAULT '0',
  `journalpageend` int(11) NOT NULL DEFAULT '0',
  `numpages` int(11) NOT NULL DEFAULT '0' COMMENT 'if it is not article',
  `type` int(11) NOT NULL COMMENT 'publicationtypes.id',
  `grif` int(11) NOT NULL DEFAULT '0' COMMENT 'ordertypes.id',
  `tirazh` int(11) NOT NULL DEFAULT '0',
  `lang` int(11) NOT NULL,
  `url` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=94 ;

-- --------------------------------------------------------

--
-- Table structure for table `publicationtypes`
--

CREATE TABLE IF NOT EXISTS `publicationtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `publicationtypes`
--

INSERT INTO `publicationtypes` (`id`, `name`) VALUES
(1, 'статья'),
(2, 'монография'),
(3, 'учебник, учебное пособие'),
(4, 'методические указания');

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE IF NOT EXISTS `publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `city` varchar(32) NOT NULL,
  `type` int(11) NOT NULL COMMENT 'publishertypes.id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Table structure for table `publishertypes`
--

CREATE TABLE IF NOT EXISTS `publishertypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `publishertypes`
--

INSERT INTO `publishertypes` (`id`, `name`) VALUES
(1, 'центральное издательство'),
(2, 'издательство вуза'),
(3, 'другое издательство'),
(4, 'зарубежное издательство');

-- --------------------------------------------------------

--
-- Table structure for table `scevents`
--

CREATE TABLE IF NOT EXISTS `scevents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `status` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `place` varchar(64) NOT NULL,
  `date` varchar(64) NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
