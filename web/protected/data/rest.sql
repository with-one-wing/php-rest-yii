-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 20, 2016 at 03:51 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.19-1+debphp.org~precise+3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rest`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`) VALUES
(3, 'Sasha'),
(2, 'Viktor');

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`id`, `name`, `is_active`) VALUES
(1, 'Pick your genre', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vote_option`
--

CREATE TABLE IF NOT EXISTS `vote_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_vote` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `vote_option`
--

INSERT INTO `vote_option` (`id`, `id_vote`, `name`) VALUES
(1, 1, 'Guitar'),
(2, 1, 'Electric'),
(3, 1, 'Bass'),
(4, 1, 'Banjo');

-- --------------------------------------------------------

--
-- Table structure for table `vote_result`
--

CREATE TABLE IF NOT EXISTS `vote_result` (
  `id_vote` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_option` int(11) NOT NULL,
  UNIQUE KEY `id_vote` (`id_vote`,`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
