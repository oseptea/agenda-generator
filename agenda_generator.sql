-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 26, 2018 at 08:06 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `agenda_generator`
--

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE IF NOT EXISTS `agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(50) NOT NULL,
  `event` varchar(200) NOT NULL,
  `time` varchar(30) DEFAULT NULL,
  `time_end` varchar(30) DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `notes` text,
  `week` tinyint(1) DEFAULT NULL,
  `day` varchar(100) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(50) NOT NULL,
  `event` varchar(200) NOT NULL,
  `time` varchar(30) DEFAULT NULL,
  `time_end` varchar(30) DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `notes` text,
  `start_on` date DEFAULT NULL,
  `day` varchar(100) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `week` tinyint(1) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `period` tinyint(1) DEFAULT NULL,
  `is_repeat` tinyint(1) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `irregular_dates` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
