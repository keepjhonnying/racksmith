-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 16, 2011 at 01:43 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `racksmith`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `objectID` smallint(7) NOT NULL AUTO_INCREMENT,
  `assetID` smallint(7) NOT NULL,
  `assetType` varchar(7) NOT NULL,
  `name` varchar(100) NOT NULL,
  `filename` varchar(150) NOT NULL,
  `userID` smallint(6) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`objectID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `attachments`
--


-- --------------------------------------------------------

--
-- Table structure for table `attrcategory`
--

CREATE TABLE `attrcategory` (
  `attrcategoryid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `static` tinyint(1) NOT NULL DEFAULT '1',
  `sort` smallint(6) NOT NULL,
  PRIMARY KEY (`attrcategoryid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `attrcategory`
--

INSERT INTO `attrcategory` VALUES(7, 'Draws Power', 1, 5);
INSERT INTO `attrcategory` VALUES(8, 'Generates Power', 1, 3);
INSERT INTO `attrcategory` VALUES(2, 'Rack Mountable', 1, 15);
INSERT INTO `attrcategory` VALUES(3, 'Floor Device', 1, 14);
INSERT INTO `attrcategory` VALUES(1, 'Generic', 1, 16);
INSERT INTO `attrcategory` VALUES(10, 'Provides Cooling', 1, 2);
INSERT INTO `attrcategory` VALUES(9, 'Is UPS', 1, 4);
INSERT INTO `attrcategory` VALUES(12, 'Has Software', 1, 9);
INSERT INTO `attrcategory` VALUES(13, 'Has Operating System', 1, 10);
INSERT INTO `attrcategory` VALUES(14, 'Is Patch', 1, 6);
INSERT INTO `attrcategory` VALUES(15, 'Network Ports', 1, 8);
INSERT INTO `attrcategory` VALUES(17, 'Provides Data Storage', 1, 1);
INSERT INTO `attrcategory` VALUES(5, 'Is Shelf', 1, 12);
INSERT INTO `attrcategory` VALUES(16, 'Has LOM', 1, 7);
INSERT INTO `attrcategory` VALUES(18, 'Requires Servicing', 1, 0);
INSERT INTO `attrcategory` VALUES(4, 'Outdoor Item', 1, 13);
INSERT INTO `attrcategory` VALUES(6, 'Is Chassis', 1, 11);
INSERT INTO `attrcategory` VALUES(25, 'Is PDU', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `attrcategoryvalues`
--

CREATE TABLE `attrcategoryvalues` (
  `attrcatvalid` smallint(12) NOT NULL AUTO_INCREMENT,
  `parentID` smallint(12) NOT NULL,
  `parentType` varchar(50) NOT NULL,
  `categoryID` smallint(12) NOT NULL,
  PRIMARY KEY (`attrcatvalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=185 ;

--
-- Dumping data for table `attrcategoryvalues`
--

INSERT INTO `attrcategoryvalues` VALUES(1, 58, 'template', 1);
INSERT INTO `attrcategoryvalues` VALUES(2, 58, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(3, 58, 'template', 3);
INSERT INTO `attrcategoryvalues` VALUES(4, 58, 'template', 12);
INSERT INTO `attrcategoryvalues` VALUES(5, 58, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(6, 58, 'template', 7);
INSERT INTO `attrcategoryvalues` VALUES(7, 7, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(8, 7, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(9, 7, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(10, 7, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(11, 7, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(12, 7, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(13, 8, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(14, 8, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(15, 8, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(16, 8, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(17, 8, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(18, 8, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(19, 9, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(20, 9, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(21, 9, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(22, 9, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(23, 9, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(24, 9, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(25, 59, 'template', 1);
INSERT INTO `attrcategoryvalues` VALUES(26, 59, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(27, 10, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(28, 10, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(29, 10, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(30, 10, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(31, 10, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(32, 10, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(33, 11, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(34, 11, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(35, 11, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(36, 11, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(37, 11, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(38, 11, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(40, 12, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(41, 12, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(42, 12, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(43, 12, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(44, 12, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(75, 15, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(46, 13, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(47, 13, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(48, 13, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(49, 13, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(50, 13, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(51, 60, 'template', 1);
INSERT INTO `attrcategoryvalues` VALUES(52, 60, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(53, 60, 'template', 12);
INSERT INTO `attrcategoryvalues` VALUES(54, 60, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(55, 60, 'template', 7);
INSERT INTO `attrcategoryvalues` VALUES(56, 60, 'template', 17);
INSERT INTO `attrcategoryvalues` VALUES(58, 14, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(59, 14, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(60, 14, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(61, 14, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(62, 14, 'device', 17);
INSERT INTO `attrcategoryvalues` VALUES(63, 61, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(64, 61, 'template', 5);
INSERT INTO `attrcategoryvalues` VALUES(65, 61, 'template', 14);
INSERT INTO `attrcategoryvalues` VALUES(66, 62, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(67, 62, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(68, 62, 'template', 17);
INSERT INTO `attrcategoryvalues` VALUES(69, 63, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(70, 63, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(71, 63, 'template', 9);
INSERT INTO `attrcategoryvalues` VALUES(72, 64, 'template', 14);
INSERT INTO `attrcategoryvalues` VALUES(73, 65, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(74, 65, 'template', 14);
INSERT INTO `attrcategoryvalues` VALUES(77, 16, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(78, 16, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(79, 16, 'device', 17);
INSERT INTO `attrcategoryvalues` VALUES(80, 66, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(81, 66, 'template', 9);
INSERT INTO `attrcategoryvalues` VALUES(82, 17, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(83, 17, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(84, 17, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(85, 17, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(86, 17, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(87, 17, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(90, 18, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(91, 18, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(92, 18, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(93, 18, 'device', 17);
INSERT INTO `attrcategoryvalues` VALUES(94, 19, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(95, 19, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(96, 19, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(97, 19, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(98, 19, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(99, 19, 'device', 17);
INSERT INTO `attrcategoryvalues` VALUES(100, 20, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(101, 20, 'device', 14);
INSERT INTO `attrcategoryvalues` VALUES(102, 67, 'template', 8);
INSERT INTO `attrcategoryvalues` VALUES(103, 68, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(104, 68, 'template', 5);
INSERT INTO `attrcategoryvalues` VALUES(105, 64, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(106, 21, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(107, 21, 'device', 5);
INSERT INTO `attrcategoryvalues` VALUES(108, 67, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(109, 22, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(110, 22, 'device', 8);
INSERT INTO `attrcategoryvalues` VALUES(111, 69, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(112, 69, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(113, 69, 'template', 7);
INSERT INTO `attrcategoryvalues` VALUES(114, 70, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(115, 70, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(116, 70, 'template', 7);
INSERT INTO `attrcategoryvalues` VALUES(119, 23, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(120, 24, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(121, 24, 'device', 5);
INSERT INTO `attrcategoryvalues` VALUES(122, 25, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(123, 25, 'device', 5);
INSERT INTO `attrcategoryvalues` VALUES(124, 26, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(125, 26, 'device', 5);
INSERT INTO `attrcategoryvalues` VALUES(126, 71, 'template', 1);
INSERT INTO `attrcategoryvalues` VALUES(127, 71, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(128, 71, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(129, 71, 'template', 7);
INSERT INTO `attrcategoryvalues` VALUES(130, 27, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(131, 27, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(132, 27, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(133, 27, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(134, 72, 'template', 3);
INSERT INTO `attrcategoryvalues` VALUES(135, 72, 'template', 9);
INSERT INTO `attrcategoryvalues` VALUES(136, 72, 'template', 18);
INSERT INTO `attrcategoryvalues` VALUES(137, 28, 'device', 3);
INSERT INTO `attrcategoryvalues` VALUES(138, 28, 'device', 9);
INSERT INTO `attrcategoryvalues` VALUES(139, 28, 'device', 18);
INSERT INTO `attrcategoryvalues` VALUES(140, 29, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(141, 29, 'device', 14);
INSERT INTO `attrcategoryvalues` VALUES(142, 30, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(143, 30, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(144, 30, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(145, 73, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(146, 73, 'template', 14);
INSERT INTO `attrcategoryvalues` VALUES(147, 74, 'template', 14);
INSERT INTO `attrcategoryvalues` VALUES(148, 31, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(149, 31, 'device', 14);
INSERT INTO `attrcategoryvalues` VALUES(150, 27, 'device', 12);
INSERT INTO `attrcategoryvalues` VALUES(151, 75, 'template', 1);
INSERT INTO `attrcategoryvalues` VALUES(152, 75, 'template', 16);
INSERT INTO `attrcategoryvalues` VALUES(153, 75, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(154, 75, 'template', 25);
INSERT INTO `attrcategoryvalues` VALUES(156, 32, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(157, 32, 'device', 16);
INSERT INTO `attrcategoryvalues` VALUES(158, 32, 'device', 25);
INSERT INTO `attrcategoryvalues` VALUES(159, 33, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(160, 33, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(161, 33, 'device', 16);
INSERT INTO `attrcategoryvalues` VALUES(162, 33, 'device', 25);
INSERT INTO `attrcategoryvalues` VALUES(163, 76, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(164, 76, 'template', 15);
INSERT INTO `attrcategoryvalues` VALUES(165, 76, 'template', 7);
INSERT INTO `attrcategoryvalues` VALUES(166, 76, 'template', 18);
INSERT INTO `attrcategoryvalues` VALUES(167, 34, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(168, 34, 'device', 15);
INSERT INTO `attrcategoryvalues` VALUES(169, 34, 'device', 7);
INSERT INTO `attrcategoryvalues` VALUES(170, 34, 'device', 18);
INSERT INTO `attrcategoryvalues` VALUES(171, 35, 'device', 1);
INSERT INTO `attrcategoryvalues` VALUES(172, 35, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(173, 35, 'device', 16);
INSERT INTO `attrcategoryvalues` VALUES(174, 35, 'device', 25);
INSERT INTO `attrcategoryvalues` VALUES(175, 77, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(176, 77, 'template', 25);
INSERT INTO `attrcategoryvalues` VALUES(177, 36, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(178, 36, 'device', 8);
INSERT INTO `attrcategoryvalues` VALUES(179, 78, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(180, 78, 'template', 25);
INSERT INTO `attrcategoryvalues` VALUES(181, 79, 'template', 2);
INSERT INTO `attrcategoryvalues` VALUES(182, 79, 'template', 25);
INSERT INTO `attrcategoryvalues` VALUES(183, 37, 'device', 2);
INSERT INTO `attrcategoryvalues` VALUES(184, 37, 'device', 25);

-- --------------------------------------------------------

--
-- Table structure for table `attrnames`
--

CREATE TABLE `attrnames` (
  `attrnameid` int(12) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL,
  `parenttype` varchar(25) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(25) NOT NULL,
  `default` varchar(250) NOT NULL,
  `units` varchar(25) NOT NULL,
  `options` varchar(250) NOT NULL,
  `desc` varchar(400) NOT NULL,
  `static` tinyint(1) NOT NULL,
  `control` smallint(1) NOT NULL,
  `sort` smallint(6) NOT NULL,
  PRIMARY KEY (`attrnameid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `attrnames`
--

INSERT INTO `attrnames` VALUES(8, 1, 'attrcategory', 'Height', 'Number', '', 'mm', '', 'Generic height of the item Generic height of the item Generic height of the item', 1, 0, 7);
INSERT INTO `attrnames` VALUES(7, 1, 'attrcategory', 'Width', 'Number', '', 'mm', '', '', 1, 0, 5);
INSERT INTO `attrnames` VALUES(14, 1, 'attrcategory', 'Weight', 'Number', '', 'kg', '', '', 1, 0, 3);
INSERT INTO `attrnames` VALUES(5, 2, 'attrcategory', 'Rack Units', 'Number', '0', 'RU', '', '', 1, 1, 0);
INSERT INTO `attrnames` VALUES(9, 1, 'attrcategory', 'Depth', 'Number', '', 'mm', '', '', 1, 0, 2);
INSERT INTO `attrnames` VALUES(10, 1, 'attrcategory', 'Serial Number', 'Textbox', '', '', '', '', 1, 0, 6);
INSERT INTO `attrnames` VALUES(11, 1, 'attrcategory', 'Barcode', 'Textbox', '', '', '', '', 1, 0, 1);
INSERT INTO `attrnames` VALUES(12, 1, 'attrcategory', 'Vendor', 'Textbox', '', '', '', '', 1, 0, 8);
INSERT INTO `attrnames` VALUES(13, 1, 'attrcategory', 'Warranty Details', 'Text Area', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(16, 6, 'attrcategory', 'Vertical Mount Points', 'Number', '1', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(17, 6, 'attrcategory', 'Horizontal Mount Points', 'Number', '1', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(18, 7, 'attrcategory', 'Power Supplies', 'Number', '1', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(19, 7, 'attrcategory', 'Max Rating', 'Number', '', 'Watts', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(20, 7, 'attrcategory', 'Normal Draw', 'Textbox', '', 'Watts', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(21, 7, 'attrcategory', 'PSU Hot Swap', 'Checkbox', 'No', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(22, 18, 'attrcategory', 'Last Service', 'Date', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(23, 18, 'attrcategory', 'Service Frequency', 'Number', '', 'months', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(24, 18, 'attrcategory', 'Last Serviced By', 'Textbox', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(26, 17, 'attrcategory', 'Capacity', 'Number', '', 'Gigabytes', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(27, 16, 'attrcategory', 'IP Address', 'Textbox', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(28, 13, 'attrcategory', 'Operating System', 'Textbox', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(29, 13, 'attrcategory', 'Version/Service Pack', 'Textbox', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(30, 13, 'attrcategory', 'Architecture', 'Radio Buttons', 'unknown', '', 'x86,x86_64,SPARC,other/unknown', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(31, 8, 'attrcategory', 'Max Rating', 'Number', '', 'Watts', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(35, 8, 'attrcategory', 'Start Up Delay', 'Number', '0', 'seconds', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(34, 8, 'attrcategory', 'Phases', 'Number', '', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(36, 8, 'attrcategory', 'Voltage', 'Number', '240', 'Volts', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(37, 9, 'attrcategory', 'Voltage', 'Number', '', 'Volts', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(38, 9, 'attrcategory', 'Phases', 'Number', '1', '', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(39, 9, 'attrcategory', 'Capacity', 'Number', '', 'VA', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(40, 10, 'attrcategory', 'Output Capacity', 'Number', '', 'kW', '', '', 1, 0, 0);
INSERT INTO `attrnames` VALUES(42, 1, 'attrcategory', 'Model', 'Textbox', '', '', '', '', 0, 0, 4);
INSERT INTO `attrnames` VALUES(45, 25, 'attrcategory', 'Max connections', 'Number', '0', '', '', '', 0, 0, 0);
INSERT INTO `attrnames` VALUES(46, 25, 'attrcategory', 'Fuse Maximum', 'Number', '16', 'Amps', '', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `attroptions`
--

CREATE TABLE `attroptions` (
  `attroptionID` int(12) NOT NULL,
  `attrnameid` int(12) NOT NULL,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`attroptionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attroptions`
--

INSERT INTO `attroptions` VALUES(0, 4, '');

-- --------------------------------------------------------

--
-- Table structure for table `attroptionvalues`
--

CREATE TABLE `attroptionvalues` (
  `attroptionvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `attrvalueid` int(11) NOT NULL,
  `attroptionid` int(11) NOT NULL,
  PRIMARY KEY (`attroptionvalueid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `attroptionvalues`
--


-- --------------------------------------------------------

--
-- Table structure for table `attrvalues`
--

CREATE TABLE `attrvalues` (
  `attrvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `attrnameid` int(11) NOT NULL,
  `value` varchar(400) NOT NULL,
  `parentid` int(11) NOT NULL,
  `parenttype` varchar(25) NOT NULL,
  PRIMARY KEY (`attrvalueid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=500 ;

--
-- Dumping data for table `attrvalues`
--

INSERT INTO `attrvalues` VALUES(1, 12, 'Dell', 58, 'template');
INSERT INTO `attrvalues` VALUES(2, 8, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(3, 10, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(4, 7, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(5, 42, 'A406', 58, 'template');
INSERT INTO `attrvalues` VALUES(6, 14, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(7, 9, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(8, 11, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(9, 13, '', 58, 'template');
INSERT INTO `attrvalues` VALUES(10, 5, '2', 58, 'template');
INSERT INTO `attrvalues` VALUES(11, 18, '2', 58, 'template');
INSERT INTO `attrvalues` VALUES(12, 19, '560', 58, 'template');
INSERT INTO `attrvalues` VALUES(13, 20, '450', 58, 'template');
INSERT INTO `attrvalues` VALUES(14, 21, 'on', 58, 'template');
INSERT INTO `attrvalues` VALUES(15, 12, 'Dell', 7, 'device');
INSERT INTO `attrvalues` VALUES(16, 8, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(17, 10, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(18, 7, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(19, 42, 'A406', 7, 'device');
INSERT INTO `attrvalues` VALUES(20, 14, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(21, 9, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(22, 11, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(23, 13, '', 7, 'device');
INSERT INTO `attrvalues` VALUES(24, 5, '2', 7, 'device');
INSERT INTO `attrvalues` VALUES(25, 18, '2', 7, 'device');
INSERT INTO `attrvalues` VALUES(26, 19, '560', 7, 'device');
INSERT INTO `attrvalues` VALUES(27, 20, '450', 7, 'device');
INSERT INTO `attrvalues` VALUES(28, 21, 'on', 7, 'device');
INSERT INTO `attrvalues` VALUES(29, 12, 'Dell', 8, 'device');
INSERT INTO `attrvalues` VALUES(30, 8, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(31, 10, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(32, 7, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(33, 42, 'A406', 8, 'device');
INSERT INTO `attrvalues` VALUES(34, 14, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(35, 9, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(36, 11, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(37, 13, '', 8, 'device');
INSERT INTO `attrvalues` VALUES(38, 5, '2', 8, 'device');
INSERT INTO `attrvalues` VALUES(39, 18, '2', 8, 'device');
INSERT INTO `attrvalues` VALUES(40, 19, '560', 8, 'device');
INSERT INTO `attrvalues` VALUES(41, 20, '450', 8, 'device');
INSERT INTO `attrvalues` VALUES(42, 21, 'on', 8, 'device');
INSERT INTO `attrvalues` VALUES(43, 12, 'Dell', 9, 'device');
INSERT INTO `attrvalues` VALUES(44, 8, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(45, 10, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(46, 7, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(47, 42, 'A406', 9, 'device');
INSERT INTO `attrvalues` VALUES(48, 14, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(49, 9, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(50, 11, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(51, 13, '', 9, 'device');
INSERT INTO `attrvalues` VALUES(52, 5, '2', 9, 'device');
INSERT INTO `attrvalues` VALUES(53, 18, '2', 9, 'device');
INSERT INTO `attrvalues` VALUES(54, 19, '560', 9, 'device');
INSERT INTO `attrvalues` VALUES(55, 20, '450', 9, 'device');
INSERT INTO `attrvalues` VALUES(56, 21, 'on', 9, 'device');
INSERT INTO `attrvalues` VALUES(57, 12, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(58, 8, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(59, 10, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(60, 7, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(61, 42, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(62, 14, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(63, 9, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(64, 11, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(65, 13, '', 59, 'template');
INSERT INTO `attrvalues` VALUES(66, 12, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(67, 8, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(68, 10, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(69, 7, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(70, 42, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(71, 14, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(72, 9, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(73, 11, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(74, 13, '', 10, 'device');
INSERT INTO `attrvalues` VALUES(75, 5, '2', 10, 'device');
INSERT INTO `attrvalues` VALUES(76, 18, '2', 10, 'device');
INSERT INTO `attrvalues` VALUES(77, 19, '560', 10, 'device');
INSERT INTO `attrvalues` VALUES(78, 20, '450', 10, 'device');
INSERT INTO `attrvalues` VALUES(79, 21, 'on', 10, 'device');
INSERT INTO `attrvalues` VALUES(80, 12, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(81, 8, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(82, 10, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(83, 7, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(84, 42, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(85, 14, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(86, 9, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(87, 11, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(88, 13, '', 11, 'device');
INSERT INTO `attrvalues` VALUES(89, 5, '2', 11, 'device');
INSERT INTO `attrvalues` VALUES(90, 18, '2', 11, 'device');
INSERT INTO `attrvalues` VALUES(91, 19, '560', 11, 'device');
INSERT INTO `attrvalues` VALUES(92, 20, '450', 11, 'device');
INSERT INTO `attrvalues` VALUES(93, 21, 'on', 11, 'device');
INSERT INTO `attrvalues` VALUES(161, 5, '1', 16, 'device');
INSERT INTO `attrvalues` VALUES(95, 8, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(96, 10, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(97, 7, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(98, 42, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(99, 14, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(100, 9, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(101, 11, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(102, 13, '', 12, 'device');
INSERT INTO `attrvalues` VALUES(103, 5, '2', 12, 'device');
INSERT INTO `attrvalues` VALUES(104, 18, '2', 12, 'device');
INSERT INTO `attrvalues` VALUES(105, 19, '560', 12, 'device');
INSERT INTO `attrvalues` VALUES(106, 20, '450', 12, 'device');
INSERT INTO `attrvalues` VALUES(107, 21, 'on', 12, 'device');
INSERT INTO `attrvalues` VALUES(109, 8, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(110, 10, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(111, 7, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(112, 42, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(113, 14, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(114, 9, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(115, 11, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(116, 13, '', 13, 'device');
INSERT INTO `attrvalues` VALUES(117, 5, '2', 13, 'device');
INSERT INTO `attrvalues` VALUES(118, 18, '2', 13, 'device');
INSERT INTO `attrvalues` VALUES(119, 19, '560', 13, 'device');
INSERT INTO `attrvalues` VALUES(120, 20, '450', 13, 'device');
INSERT INTO `attrvalues` VALUES(121, 21, 'on', 13, 'device');
INSERT INTO `attrvalues` VALUES(122, 12, 'Futijitsu', 60, 'template');
INSERT INTO `attrvalues` VALUES(123, 8, '1689', 60, 'template');
INSERT INTO `attrvalues` VALUES(124, 10, '123456789', 60, 'template');
INSERT INTO `attrvalues` VALUES(125, 7, '484', 60, 'template');
INSERT INTO `attrvalues` VALUES(126, 42, 'CS800-S2', 60, 'template');
INSERT INTO `attrvalues` VALUES(127, 14, '375', 60, 'template');
INSERT INTO `attrvalues` VALUES(128, 9, '770', 60, 'template');
INSERT INTO `attrvalues` VALUES(129, 11, 'CS800S2TEST', 60, 'template');
INSERT INTO `attrvalues` VALUES(130, 13, '1 year / 5 x 9 / On-site service', 60, 'template');
INSERT INTO `attrvalues` VALUES(131, 5, '38', 60, 'template');
INSERT INTO `attrvalues` VALUES(132, 18, '16', 60, 'template');
INSERT INTO `attrvalues` VALUES(133, 19, '5900', 60, 'template');
INSERT INTO `attrvalues` VALUES(134, 20, '2500', 60, 'template');
INSERT INTO `attrvalues` VALUES(135, 21, 'on', 60, 'template');
INSERT INTO `attrvalues` VALUES(136, 26, '160', 60, 'template');
INSERT INTO `attrvalues` VALUES(138, 8, '1689', 14, 'device');
INSERT INTO `attrvalues` VALUES(139, 10, '123456789', 14, 'device');
INSERT INTO `attrvalues` VALUES(140, 7, '484', 14, 'device');
INSERT INTO `attrvalues` VALUES(141, 42, 'CS800-S2', 14, 'device');
INSERT INTO `attrvalues` VALUES(142, 14, '375', 14, 'device');
INSERT INTO `attrvalues` VALUES(143, 9, '770', 14, 'device');
INSERT INTO `attrvalues` VALUES(144, 11, 'CS800S2TEST', 14, 'device');
INSERT INTO `attrvalues` VALUES(145, 13, '1 year / 5 x 9 / On-site service', 14, 'device');
INSERT INTO `attrvalues` VALUES(146, 5, '38', 14, 'device');
INSERT INTO `attrvalues` VALUES(147, 18, '2', 14, 'device');
INSERT INTO `attrvalues` VALUES(148, 19, '5900', 14, 'device');
INSERT INTO `attrvalues` VALUES(149, 20, '2500', 14, 'device');
INSERT INTO `attrvalues` VALUES(150, 21, 'on', 14, 'device');
INSERT INTO `attrvalues` VALUES(151, 26, '160', 14, 'device');
INSERT INTO `attrvalues` VALUES(152, 5, '38', 61, 'template');
INSERT INTO `attrvalues` VALUES(153, 5, '2', 62, 'template');
INSERT INTO `attrvalues` VALUES(154, 26, '3000', 62, 'template');
INSERT INTO `attrvalues` VALUES(155, 5, '2', 63, 'template');
INSERT INTO `attrvalues` VALUES(156, 37, '240', 63, 'template');
INSERT INTO `attrvalues` VALUES(157, 38, '', 63, 'template');
INSERT INTO `attrvalues` VALUES(158, 39, '3000', 63, 'template');
INSERT INTO `attrvalues` VALUES(159, 5, '1', 65, 'template');
INSERT INTO `attrvalues` VALUES(162, 26, '3000', 16, 'device');
INSERT INTO `attrvalues` VALUES(163, 5, '2', 66, 'template');
INSERT INTO `attrvalues` VALUES(164, 37, '', 66, 'template');
INSERT INTO `attrvalues` VALUES(165, 38, '', 66, 'template');
INSERT INTO `attrvalues` VALUES(166, 39, '', 66, 'template');
INSERT INTO `attrvalues` VALUES(167, 12, 'Futijitsu', 17, 'device');
INSERT INTO `attrvalues` VALUES(168, 8, '1689', 17, 'device');
INSERT INTO `attrvalues` VALUES(169, 10, '123456789', 17, 'device');
INSERT INTO `attrvalues` VALUES(170, 7, '484', 17, 'device');
INSERT INTO `attrvalues` VALUES(171, 42, 'CS800-S2', 17, 'device');
INSERT INTO `attrvalues` VALUES(172, 14, '375', 17, 'device');
INSERT INTO `attrvalues` VALUES(173, 9, '770', 17, 'device');
INSERT INTO `attrvalues` VALUES(174, 11, 'CS800S2TEST', 17, 'device');
INSERT INTO `attrvalues` VALUES(175, 13, '1 year / 5 x 9 / On-site service', 17, 'device');
INSERT INTO `attrvalues` VALUES(176, 5, '2', 17, 'device');
INSERT INTO `attrvalues` VALUES(177, 18, '2', 17, 'device');
INSERT INTO `attrvalues` VALUES(178, 19, '5900', 17, 'device');
INSERT INTO `attrvalues` VALUES(179, 20, '2500', 17, 'device');
INSERT INTO `attrvalues` VALUES(180, 21, 'on', 17, 'device');
INSERT INTO `attrvalues` VALUES(183, 10, '123456789', 18, 'device');
INSERT INTO `attrvalues` VALUES(184, 7, '484', 18, 'device');
INSERT INTO `attrvalues` VALUES(185, 42, 'CS800-S2', 18, 'device');
INSERT INTO `attrvalues` VALUES(186, 14, '375', 18, 'device');
INSERT INTO `attrvalues` VALUES(187, 9, '770', 18, 'device');
INSERT INTO `attrvalues` VALUES(188, 11, 'CS800S2TEST', 18, 'device');
INSERT INTO `attrvalues` VALUES(189, 13, '1 year / 5 x 9 / On-site service', 18, 'device');
INSERT INTO `attrvalues` VALUES(190, 5, '2', 18, 'device');
INSERT INTO `attrvalues` VALUES(191, 18, '2', 18, 'device');
INSERT INTO `attrvalues` VALUES(192, 19, '5900', 18, 'device');
INSERT INTO `attrvalues` VALUES(193, 20, '2500', 18, 'device');
INSERT INTO `attrvalues` VALUES(194, 21, 'on', 18, 'device');
INSERT INTO `attrvalues` VALUES(195, 26, '3000', 18, 'device');
INSERT INTO `attrvalues` VALUES(196, 12, 'Futijitsu', 19, 'device');
INSERT INTO `attrvalues` VALUES(197, 8, '1689', 19, 'device');
INSERT INTO `attrvalues` VALUES(198, 10, '123456789', 19, 'device');
INSERT INTO `attrvalues` VALUES(199, 7, '484', 19, 'device');
INSERT INTO `attrvalues` VALUES(200, 42, 'CS800-S2', 19, 'device');
INSERT INTO `attrvalues` VALUES(201, 14, '375', 19, 'device');
INSERT INTO `attrvalues` VALUES(202, 9, '770', 19, 'device');
INSERT INTO `attrvalues` VALUES(203, 11, 'CS800S2TEST', 19, 'device');
INSERT INTO `attrvalues` VALUES(204, 13, '1 year / 5 x 9 / On-site service', 19, 'device');
INSERT INTO `attrvalues` VALUES(205, 5, '2', 19, 'device');
INSERT INTO `attrvalues` VALUES(206, 18, '2', 19, 'device');
INSERT INTO `attrvalues` VALUES(207, 19, '5900', 19, 'device');
INSERT INTO `attrvalues` VALUES(208, 20, '2500', 19, 'device');
INSERT INTO `attrvalues` VALUES(209, 21, 'on', 19, 'device');
INSERT INTO `attrvalues` VALUES(210, 26, '3000', 19, 'device');
INSERT INTO `attrvalues` VALUES(211, 5, '2', 20, 'device');
INSERT INTO `attrvalues` VALUES(212, 31, '10000', 67, 'template');
INSERT INTO `attrvalues` VALUES(213, 35, '0', 67, 'template');
INSERT INTO `attrvalues` VALUES(214, 34, '2', 67, 'template');
INSERT INTO `attrvalues` VALUES(215, 36, '220', 67, 'template');
INSERT INTO `attrvalues` VALUES(216, 5, '1', 68, 'template');
INSERT INTO `attrvalues` VALUES(217, 5, '1', 64, 'template');
INSERT INTO `attrvalues` VALUES(218, 5, '1', 21, 'device');
INSERT INTO `attrvalues` VALUES(219, 5, '1', 67, 'template');
INSERT INTO `attrvalues` VALUES(220, 5, '1', 22, 'device');
INSERT INTO `attrvalues` VALUES(221, 31, '10000', 22, 'device');
INSERT INTO `attrvalues` VALUES(222, 35, '0', 22, 'device');
INSERT INTO `attrvalues` VALUES(223, 34, '2', 22, 'device');
INSERT INTO `attrvalues` VALUES(224, 36, '220', 22, 'device');
INSERT INTO `attrvalues` VALUES(225, 5, '1', 69, 'template');
INSERT INTO `attrvalues` VALUES(226, 18, '1', 69, 'template');
INSERT INTO `attrvalues` VALUES(227, 19, '160', 69, 'template');
INSERT INTO `attrvalues` VALUES(228, 20, '160', 69, 'template');
INSERT INTO `attrvalues` VALUES(229, 21, '', 69, 'template');
INSERT INTO `attrvalues` VALUES(230, 5, '1', 70, 'template');
INSERT INTO `attrvalues` VALUES(231, 18, '1', 70, 'template');
INSERT INTO `attrvalues` VALUES(232, 19, '160', 70, 'template');
INSERT INTO `attrvalues` VALUES(233, 20, '160', 70, 'template');
INSERT INTO `attrvalues` VALUES(234, 21, '', 70, 'template');
INSERT INTO `attrvalues` VALUES(237, 19, '160', 23, 'device');
INSERT INTO `attrvalues` VALUES(238, 20, '160', 23, 'device');
INSERT INTO `attrvalues` VALUES(239, 21, '', 23, 'device');
INSERT INTO `attrvalues` VALUES(240, 5, '1', 24, 'device');
INSERT INTO `attrvalues` VALUES(241, 5, '1', 25, 'device');
INSERT INTO `attrvalues` VALUES(242, 5, '1', 26, 'device');
INSERT INTO `attrvalues` VALUES(243, 12, 'Gen', 71, 'template');
INSERT INTO `attrvalues` VALUES(244, 8, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(245, 10, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(246, 7, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(247, 42, 'Generic', 71, 'template');
INSERT INTO `attrvalues` VALUES(248, 14, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(249, 9, '900', 71, 'template');
INSERT INTO `attrvalues` VALUES(250, 11, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(251, 13, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(252, 5, '1', 71, 'template');
INSERT INTO `attrvalues` VALUES(253, 18, '1', 71, 'template');
INSERT INTO `attrvalues` VALUES(254, 19, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(255, 20, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(256, 21, '', 71, 'template');
INSERT INTO `attrvalues` VALUES(257, 12, 'Gen', 27, 'device');
INSERT INTO `attrvalues` VALUES(258, 8, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(259, 10, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(260, 7, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(261, 42, 'Generic', 27, 'device');
INSERT INTO `attrvalues` VALUES(262, 14, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(263, 9, '900', 27, 'device');
INSERT INTO `attrvalues` VALUES(264, 11, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(265, 13, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(266, 5, '2', 27, 'device');
INSERT INTO `attrvalues` VALUES(267, 18, '1', 27, 'device');
INSERT INTO `attrvalues` VALUES(268, 19, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(269, 20, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(270, 21, '', 27, 'device');
INSERT INTO `attrvalues` VALUES(271, 37, '230', 72, 'template');
INSERT INTO `attrvalues` VALUES(272, 38, '3', 72, 'template');
INSERT INTO `attrvalues` VALUES(273, 39, '420', 72, 'template');
INSERT INTO `attrvalues` VALUES(274, 22, '', 72, 'template');
INSERT INTO `attrvalues` VALUES(275, 23, '', 72, 'template');
INSERT INTO `attrvalues` VALUES(276, 24, '', 72, 'template');
INSERT INTO `attrvalues` VALUES(277, 37, '230', 28, 'device');
INSERT INTO `attrvalues` VALUES(278, 38, '3', 28, 'device');
INSERT INTO `attrvalues` VALUES(279, 39, '420', 28, 'device');
INSERT INTO `attrvalues` VALUES(280, 22, '', 28, 'device');
INSERT INTO `attrvalues` VALUES(281, 23, '', 28, 'device');
INSERT INTO `attrvalues` VALUES(282, 24, '', 28, 'device');
INSERT INTO `attrvalues` VALUES(283, 5, '2', 29, 'device');
INSERT INTO `attrvalues` VALUES(284, 5, '2', 30, 'device');
INSERT INTO `attrvalues` VALUES(285, 18, '1', 30, 'device');
INSERT INTO `attrvalues` VALUES(286, 19, '', 30, 'device');
INSERT INTO `attrvalues` VALUES(287, 20, '', 30, 'device');
INSERT INTO `attrvalues` VALUES(288, 21, '', 30, 'device');
INSERT INTO `attrvalues` VALUES(289, 5, '1', 73, 'template');
INSERT INTO `attrvalues` VALUES(290, 5, '1', 31, 'device');
INSERT INTO `attrvalues` VALUES(291, 12, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(292, 8, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(293, 10, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(294, 7, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(295, 42, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(296, 14, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(297, 9, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(298, 11, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(299, 13, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(300, 27, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(301, 5, '0', 75, 'template');
INSERT INTO `attrvalues` VALUES(302, 45, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(303, 46, '', 75, 'template');
INSERT INTO `attrvalues` VALUES(305, 8, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(306, 10, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(307, 7, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(308, 42, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(309, 14, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(310, 9, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(311, 11, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(312, 13, '', 32, 'device');
INSERT INTO `attrvalues` VALUES(313, 5, '0', 32, 'device');
INSERT INTO `attrvalues` VALUES(314, 27, 'http://10.2.73.5', 32, 'device');
INSERT INTO `attrvalues` VALUES(315, 45, '13', 32, 'device');
INSERT INTO `attrvalues` VALUES(316, 46, '32', 32, 'device');
INSERT INTO `attrvalues` VALUES(317, 12, 'APC', 33, 'device');
INSERT INTO `attrvalues` VALUES(318, 8, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(319, 10, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(320, 7, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(321, 42, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(322, 14, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(323, 9, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(324, 11, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(325, 13, '', 33, 'device');
INSERT INTO `attrvalues` VALUES(326, 5, '0', 33, 'device');
INSERT INTO `attrvalues` VALUES(327, 27, 'http://10.2.73.5', 33, 'device');
INSERT INTO `attrvalues` VALUES(328, 45, '13', 33, 'device');
INSERT INTO `attrvalues` VALUES(329, 46, '32', 33, 'device');
INSERT INTO `attrvalues` VALUES(330, 5, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(331, 18, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(332, 19, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(333, 20, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(334, 21, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(335, 22, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(336, 23, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(337, 24, '', 76, 'template');
INSERT INTO `attrvalues` VALUES(338, 5, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(339, 18, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(340, 19, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(341, 20, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(342, 21, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(343, 22, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(344, 23, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(345, 24, '', 34, 'device');
INSERT INTO `attrvalues` VALUES(346, 12, 'APC', 35, 'device');
INSERT INTO `attrvalues` VALUES(347, 8, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(348, 10, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(349, 7, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(350, 42, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(351, 14, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(352, 9, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(353, 11, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(354, 13, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(355, 5, '', 35, 'device');
INSERT INTO `attrvalues` VALUES(356, 27, 'http://10.2.73.5', 35, 'device');
INSERT INTO `attrvalues` VALUES(357, 45, '13', 35, 'device');
INSERT INTO `attrvalues` VALUES(358, 46, '32', 35, 'device');
INSERT INTO `attrvalues` VALUES(359, 5, '1', 77, 'template');
INSERT INTO `attrvalues` VALUES(360, 45, '8', 77, 'template');
INSERT INTO `attrvalues` VALUES(361, 46, '32', 77, 'template');
INSERT INTO `attrvalues` VALUES(362, 5, '1', 36, 'device');
INSERT INTO `attrvalues` VALUES(363, 31, '10000', 36, 'device');
INSERT INTO `attrvalues` VALUES(364, 35, '0', 36, 'device');
INSERT INTO `attrvalues` VALUES(365, 34, '2', 36, 'device');
INSERT INTO `attrvalues` VALUES(366, 36, '220', 36, 'device');
INSERT INTO `attrvalues` VALUES(367, 5, '1', 78, 'template');
INSERT INTO `attrvalues` VALUES(368, 45, '8', 78, 'template');
INSERT INTO `attrvalues` VALUES(369, 46, '32', 78, 'template');
INSERT INTO `attrvalues` VALUES(370, 5, '1', 79, 'template');
INSERT INTO `attrvalues` VALUES(371, 45, '8', 79, 'template');
INSERT INTO `attrvalues` VALUES(372, 46, '32', 79, 'template');
INSERT INTO `attrvalues` VALUES(373, 5, '5', 37, 'device');
INSERT INTO `attrvalues` VALUES(374, 45, '8', 37, 'device');
INSERT INTO `attrvalues` VALUES(375, 46, '32', 37, 'device');

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `buildingID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `ownerID` varchar(100) NOT NULL,
  `revisionID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`buildingID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` VALUES(1, 'testBuilding', '', '', '1', 0);
INSERT INTO `buildings` VALUES(2, 'Main DC', '', '', '1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cabinets`
--

CREATE TABLE `cabinets` (
  `cabinetID` int(12) NOT NULL AUTO_INCREMENT,
  `parentType` varchar(50) NOT NULL,
  `parentID` int(12) NOT NULL,
  `name` varchar(120) NOT NULL,
  `ownerID` int(12) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`cabinetID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cabinets`
--

INSERT INTO `cabinets` VALUES(1, 'site', 0, 'Cab1', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `cablecategories`
--

CREATE TABLE `cablecategories` (
  `categoryID` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `enabled` smallint(1) NOT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `cablecategories`
--

INSERT INTO `cablecategories` VALUES(1, 'RJ45', '1', 1);
INSERT INTO `cablecategories` VALUES(11, 'USB', '1', 0);
INSERT INTO `cablecategories` VALUES(2, 'Fiber', '1', 1);
INSERT INTO `cablecategories` VALUES(17, 'Fiber Multimode', '1', 1);
INSERT INTO `cablecategories` VALUES(18, 'Power cable', '2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cables`
--

CREATE TABLE `cables` (
  `cableID` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(100) NOT NULL,
  `cableTypeID` int(11) NOT NULL,
  `revisionID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cableID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cables`
--


-- --------------------------------------------------------

--
-- Table structure for table `cabletypejoins`
--

CREATE TABLE `cabletypejoins` (
  `entryID` int(9) NOT NULL AUTO_INCREMENT,
  `categoryID` int(9) NOT NULL,
  `cableTypeID` int(9) NOT NULL,
  PRIMARY KEY (`entryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `cabletypejoins`
--

INSERT INTO `cabletypejoins` VALUES(55, 1, 22);
INSERT INTO `cabletypejoins` VALUES(54, 1, 21);
INSERT INTO `cabletypejoins` VALUES(53, 2, 4);
INSERT INTO `cabletypejoins` VALUES(56, 1, 23);
INSERT INTO `cabletypejoins` VALUES(58, 1, 5);
INSERT INTO `cabletypejoins` VALUES(59, 2, 23);
INSERT INTO `cabletypejoins` VALUES(60, 18, 2);

-- --------------------------------------------------------

--
-- Table structure for table `cabletypes`
--

CREATE TABLE `cabletypes` (
  `cableTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `isPower` tinyint(1) NOT NULL,
  PRIMARY KEY (`cableTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `cabletypes`
--

INSERT INTO `cabletypes` VALUES(1, 'Power DC12', 1);
INSERT INTO `cabletypes` VALUES(18, 'InfiniBand', 0);
INSERT INTO `cabletypes` VALUES(2, '3 Phase Power', 1);
INSERT INTO `cabletypes` VALUES(4, 'Ethernet Cat 5/6', 0);
INSERT INTO `cabletypes` VALUES(5, 'Fiber ST', 0);
INSERT INTO `cabletypes` VALUES(8, 'Fiber LC', 0);
INSERT INTO `cabletypes` VALUES(9, 'Fiber FC', 0);
INSERT INTO `cabletypes` VALUES(10, 'Fiber SC', 0);
INSERT INTO `cabletypes` VALUES(11, 'Fiber E2000', 0);
INSERT INTO `cabletypes` VALUES(12, 'Fiber LX.5', 0);
INSERT INTO `cabletypes` VALUES(14, 'Serial', 0);
INSERT INTO `cabletypes` VALUES(21, 'Cat 5', 0);
INSERT INTO `cabletypes` VALUES(22, 'Cat 5e', 0);
INSERT INTO `cabletypes` VALUES(23, 'Cat 6', 0);

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `name` varchar(200) NOT NULL,
  `value` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config`
--

INSERT INTO `config` VALUES('install_date', '20110220');
INSERT INTO `config` VALUES('version', '1.0_dev-##');
INSERT INTO `config` VALUES('ldap_auth', '0');
INSERT INTO `config` VALUES('ldap_server', '');
INSERT INTO `config` VALUES('buildingCanvasX', '2000');
INSERT INTO `config` VALUES('buildingCanvasY', '1412');
INSERT INTO `config` VALUES('ldap_basedn', '');
INSERT INTO `config` VALUES('ldaps_enabled', '0');
INSERT INTO `config` VALUES('ldap_prefix', '');
INSERT INTO `config` VALUES('ldap_group', '');
INSERT INTO `config` VALUES('webaddress', 'http://localhost/racksmith/');
INSERT INTO `config` VALUES('lockFloorTiles', '0');
INSERT INTO `config` VALUES('ldap_postfix', '');
INSERT INTO `config` VALUES('ldap_postfix', '');
INSERT INTO `config` VALUES('lockFloorTiles', '0');
INSERT INTO `config` VALUES('attachments_enabled', '1');
INSERT INTO `config` VALUES('attachment_path', 'images/uploads/files/');
INSERT INTO `config` VALUES('ldap_field', 'sAMAccountName');
INSERT INTO `config` VALUES('attachment_maxUpload', '30');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `deviceID` smallint(9) NOT NULL AUTO_INCREMENT,
  `parentID` smallint(9) NOT NULL,
  `parentType` varchar(200) NOT NULL,
  `position` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `background` varchar(200) NOT NULL,
  `deviceTypeID` smallint(9) NOT NULL,
  `templateID` smallint(9) NOT NULL,
  `ownerID` smallint(9) NOT NULL,
  PRIMARY KEY (`deviceID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` VALUES(7, 0, 'room', '0', 'WebServ1', '', 3, 58, 1);
INSERT INTO `devices` VALUES(8, 118, 'room', '0', 'DNSserv1', '', 3, 58, 1);
INSERT INTO `devices` VALUES(9, 0, 'room', '0', 'storeBox', '', 3, 58, 1);
INSERT INTO `devices` VALUES(10, 0, 'room', '0', 'test', '', 3, 58, 1);
INSERT INTO `devices` VALUES(11, 0, 'room', '0', 'test2', '', 3, 58, 1);
INSERT INTO `devices` VALUES(16, 3, 'rack', '39', 'eee', '', 2, 62, 1);
INSERT INTO `devices` VALUES(17, 3, 'rack', '19', 'dddd', '', 3, 58, 1);
INSERT INTO `devices` VALUES(19, 7, 'rack', '2', 'CS-800S2-1', '', 2, 60, 1);
INSERT INTO `devices` VALUES(20, 7, 'rack', '11', 'Patch 48', '', 7, 65, 1);
INSERT INTO `devices` VALUES(21, 4, 'rack', '20', 'Dividing Panel', '', 7, 68, 1);
INSERT INTO `devices` VALUES(22, 7, 'rack', '7', 'PSU1', '', 5, 67, 1);
INSERT INTO `devices` VALUES(24, 10, 'rack', '22', 'Dividing Panel', '', 7, 68, 1);
INSERT INTO `devices` VALUES(25, 9, 'rack', '33', 'Dividing Panel', '', 7, 68, 1);
INSERT INTO `devices` VALUES(26, 9, 'rack', '14', 'Dividing Panel', '', 7, 68, 1);
INSERT INTO `devices` VALUES(27, 9, 'rack', '31', 'Server1', '', 3, 71, 1);
INSERT INTO `devices` VALUES(28, 118, 'room', '0', 'vxfvxv', '', 5, 72, 1);
INSERT INTO `devices` VALUES(29, 9, 'rack', '20', 'Patch1', '', 7, 64, 1);
INSERT INTO `devices` VALUES(30, 9, 'rack', '29', 'Switch1', '', 4, 70, 1);
INSERT INTO `devices` VALUES(31, 10, 'rack', '13', 'AAAA', '', 7, 65, 1);
INSERT INTO `devices` VALUES(33, 11, 'rack', '29', 'test', '', 5, 75, 1);
INSERT INTO `devices` VALUES(34, 11, 'rack', '5', 'opopopoop', '', 4, 76, 1);
INSERT INTO `devices` VALUES(35, 11, 'rack', '7', 'ww', '', 5, 75, 1);
INSERT INTO `devices` VALUES(36, 11, 'rack', '9', 'testptu', '', 5, 67, 1);
INSERT INTO `devices` VALUES(37, 10, 'rack', '11', 'testptu2', '', 5, 78, 1);

-- --------------------------------------------------------

--
-- Table structure for table `devicetypes`
--

CREATE TABLE `devicetypes` (
  `deviceTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`deviceTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `devicetypes`
--

INSERT INTO `devicetypes` VALUES(1, 'Cooling');
INSERT INTO `devicetypes` VALUES(2, 'Data Storage');
INSERT INTO `devicetypes` VALUES(3, 'Server');
INSERT INTO `devicetypes` VALUES(4, 'Switch');
INSERT INTO `devicetypes` VALUES(5, 'Power');
INSERT INTO `devicetypes` VALUES(6, 'Power Generator');
INSERT INTO `devicetypes` VALUES(7, 'Patch Panel');

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `floorID` int(11) NOT NULL AUTO_INCREMENT,
  `buildingID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `sort` tinyint(3) NOT NULL,
  `revisionID` int(11) NOT NULL,
  PRIMARY KEY (`floorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` VALUES(2, 1, 'G', '', 0, 0);
INSERT INTO `floors` VALUES(4, 2, 'G', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `joins`
--

CREATE TABLE `joins` (
  `deviceID` int(11) NOT NULL,
  `joinID` int(11) NOT NULL AUTO_INCREMENT,
  `disporder` int(11) NOT NULL,
  `primPort` int(11) NOT NULL,
  `secPort` int(11) NOT NULL,
  `cableTypeID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`joinID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=145 ;

--
-- Dumping data for table `joins`
--

INSERT INTO `joins` VALUES(20, 49, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 50, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 51, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 52, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 53, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 54, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 55, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 56, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 57, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 58, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 59, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 60, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 61, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 62, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 63, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 64, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 65, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 66, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 67, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 68, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 69, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 70, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 71, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 72, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 73, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 74, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 75, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 76, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 77, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 78, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 79, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 80, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 81, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 82, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 83, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 84, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 85, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 86, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 87, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 88, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 89, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 90, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 91, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 92, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 93, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 94, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 95, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(20, 96, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 97, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 98, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 99, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 100, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 101, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 102, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 103, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 104, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 105, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 106, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 107, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 108, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 109, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 110, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 111, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 112, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 113, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 114, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 115, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 116, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 117, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 118, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 119, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 120, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 121, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 122, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 123, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 124, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 125, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 126, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 127, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 128, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 129, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 130, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 131, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 132, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 133, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 134, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 135, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 136, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 137, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 138, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 139, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 140, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 141, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 142, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 143, 0, 0, 0, 1);
INSERT INTO `joins` VALUES(31, 144, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `layoutitems`
--

CREATE TABLE `layoutitems` (
  `layoutItemID` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `parentName` varchar(50) NOT NULL,
  `itemName` varchar(50) NOT NULL,
  `parentType` varchar(50) NOT NULL,
  `itemType` varchar(50) NOT NULL,
  `posX` int(11) NOT NULL,
  `posY` int(11) NOT NULL,
  `rotation` smallint(3) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `zindex` smallint(4) NOT NULL,
  `revisionID` int(11) NOT NULL,
  PRIMARY KEY (`layoutItemID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `layoutitems`
--

INSERT INTO `layoutitems` VALUES(1, 2, 116, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(2, 0, 1, '', '', 'Building', 'Building', 268, 190, 0, 330, 395, 0, 0);
INSERT INTO `layoutitems` VALUES(3, 3, 117, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(4, 116, 0, '', 'Raised Floor', 'Room', 'floortile1', 2, 2, 0, 624, 793, 0, 0);
INSERT INTO `layoutitems` VALUES(5, 116, 3, '', 'testR1', 'Room', 'rack1', 67, 32, 0, 32, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(6, 116, 4, '', 'testR2', 'Room', 'rack1', 161, 33, 0, 32, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(7, 0, 1, '', 'Cab1', 'Building', 'Cabinet', 622, 301, 0, 32, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(22, 118, 0, '', 'Cable Tray', 'Room', 'cabletray1', 191, 405, 0, 352, 20, 0, 0);
INSERT INTO `layoutitems` VALUES(16, 116, 7, '', 'hey', 'Room', 'rack1', 513, 44, 0, 32, 53, 0, 0);
INSERT INTO `layoutitems` VALUES(18, 4, 118, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 525, 561, 0, 0);
INSERT INTO `layoutitems` VALUES(19, 0, 2, '', '', 'Building', 'Building', 762, 81, 0, 230, 277, 0, 0);
INSERT INTO `layoutitems` VALUES(25, 118, 28, 'room', 'vxfvxv', 'room', 'device', 626, 180, 0, 32, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(23, 118, 9, '', '36S', 'Room', 'rack1', 462, 308, 0, 32, 59, 0, 0);
INSERT INTO `layoutitems` VALUES(24, 118, 10, '', '36R', 'Room', 'rack1', 540, 322, 0, 32, 53, 0, 0);
INSERT INTO `layoutitems` VALUES(26, 4, 0, '', '', 'Floor', 'Door', 871, 33, 0, 258, 205, 0, 0);
INSERT INTO `layoutitems` VALUES(27, 118, 8, '', 'DNSserv1', 'Room', 'device', 710, 297, 0, 32, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(29, 118, 11, '', 'test', 'Room', 'rack1', 885, 104, 0, 32, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(36, 5, 119, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(37, 6, 120, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(38, 7, 121, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(39, 8, 122, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(40, 9, 123, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(41, 10, 124, '', 'Room 1', 'floor', 'Room', 25, 25, 0, 300, 200, 0, 0);
INSERT INTO `layoutitems` VALUES(42, 118, 0, '', 'Cold Aisle', 'Room', 'coldAisle', 65, 27, 0, 32, 399, 0, 0);
INSERT INTO `layoutitems` VALUES(43, 116, 0, '', 'Hot Aisle', 'Room', 'hotAisle', 372, 174, 0, 361, 32, 0, 0);
INSERT INTO `layoutitems` VALUES(44, 116, 0, '', 'Cable Tray', 'Room', 'cabletray2', 374, 222, 0, 549, 33, 0, 0);
-- --------------------------------------------------------

--
-- Table structure for table `licences`
--

CREATE TABLE `licences` (
  `licenceID` int(11) NOT NULL AUTO_INCREMENT,
  `deviceID` int(11) NOT NULL,
  `software` varchar(50) NOT NULL,
  `licence` varchar(150) NOT NULL,
  `softwareNotes` varchar(250) NOT NULL,
  `revisionID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`licenceID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `licences`
--


-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(400) NOT NULL,
  `eventType` varchar(100) NOT NULL,
  `itemID` int(11) NOT NULL,
  `previous` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `userID` int(11) NOT NULL,
  `eventTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `revisionID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`logID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=128 ;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` VALUES(1, 'Added Rack testR1: RU', 'new_device', 1, '', '', 1, '2011-02-15 11:12:15', 0);
INSERT INTO `logs` VALUES(2, 'Added Rack testR2: RU', 'new_device', 1, '', '', 1, '2011-02-15 11:12:29', 0);
INSERT INTO `logs` VALUES(3, 'Added Cabinet Cab1', 'new_device', 1, '', '', 1, '2011-02-15 11:12:46', 0);
INSERT INTO `logs` VALUES(4, 'Added Server: WebServ1', 'new_device', 7, '', '', 1, '2011-02-15 11:15:22', 0);
INSERT INTO `logs` VALUES(5, 'Added Server: DNSserv1', 'new_device', 8, '', '', 1, '2011-02-15 11:16:24', 0);
INSERT INTO `logs` VALUES(6, 'Added Server: storeBox', 'new_device', 9, '', '', 1, '2011-02-15 11:19:51', 0);
INSERT INTO `logs` VALUES(7, 'Updated Server: storeBox', 'update_device', 9, '', '', 1, '2011-02-15 11:19:54', 0);
INSERT INTO `logs` VALUES(8, 'Updated Server: DNSserv1', 'update_device', 8, '', '', 1, '2011-02-15 13:40:50', 0);
INSERT INTO `logs` VALUES(9, 'Updated Server: DNSserv1', 'update_device', 8, '', '', 1, '2011-02-15 13:40:56', 0);
INSERT INTO `logs` VALUES(10, 'Updated Server: storeBox', 'update_device', 9, '', '', 1, '2011-02-15 13:41:00', 0);
INSERT INTO `logs` VALUES(11, 'Updated Server: storeBox', 'update_device', 9, '', '', 1, '2011-02-15 13:41:06', 0);
INSERT INTO `logs` VALUES(12, 'Updated Server: storeBox', 'update_device', 9, '', '', 1, '2011-02-15 13:41:20', 0);
INSERT INTO `logs` VALUES(13, 'Added Server: test', 'new_device', 10, '', '', 1, '2011-02-15 18:12:50', 0);
INSERT INTO `logs` VALUES(14, 'Added Server: test2', 'new_device', 11, '', '', 1, '2011-02-15 18:13:50', 0);
INSERT INTO `logs` VALUES(15, 'Added Server: test22', 'new_device', 12, '', '', 1, '2011-02-15 18:16:04', 0);
INSERT INTO `logs` VALUES(16, 'Updated Server: test22', 'update_device', 12, '', '', 1, '2011-02-15 18:17:56', 0);
INSERT INTO `logs` VALUES(17, 'Added Server: test44', 'new_device', 13, '', '', 1, '2011-02-15 18:18:45', 0);
INSERT INTO `logs` VALUES(18, 'Deleted Server: test22', 'delete_device', 12, '', '', 1, '2011-02-15 19:06:59', 0);
INSERT INTO `logs` VALUES(19, 'Added Data Storage: CS800-1', 'new_device', 14, '', '', 1, '2011-02-15 19:07:40', 0);
INSERT INTO `logs` VALUES(20, 'Deleted Data Storage: CS800-1', 'delete_device', 14, '', '', 1, '2011-02-15 19:11:32', 0);
INSERT INTO `logs` VALUES(21, 'Deleted Server: test44', 'delete_device', 13, '', '', 1, '2011-02-15 19:12:39', 0);
INSERT INTO `logs` VALUES(22, 'Added Patch Panel: eeee', 'new_device', 15, '', '', 1, '2011-02-15 19:12:42', 0);
INSERT INTO `logs` VALUES(23, 'Updated Patch Panel: eeee', 'update_device', 15, '', '', 1, '2011-02-15 19:12:46', 0);
INSERT INTO `logs` VALUES(24, 'Updated Server: WebServ1', 'update_device', 7, '', '', 1, '2011-02-15 19:12:53', 0);
INSERT INTO `logs` VALUES(25, 'Updated Server: test2', 'update_device', 11, '', '', 1, '2011-02-15 19:12:56', 0);
INSERT INTO `logs` VALUES(26, 'Updated Server: storeBox', 'update_device', 9, '', '', 1, '2011-02-15 19:12:57', 0);
INSERT INTO `logs` VALUES(27, 'Updated Server: test', 'update_device', 10, '', '', 1, '2011-02-15 19:12:59', 0);
INSERT INTO `logs` VALUES(28, 'Updated Server: DNSserv1', 'update_device', 8, '', '', 1, '2011-02-15 19:13:00', 0);
INSERT INTO `logs` VALUES(29, 'Added Rack hey: RU', 'new_device', 1, '', '', 1, '2011-02-15 19:13:36', 0);
INSERT INTO `logs` VALUES(30, 'Added Data Storage: eee', 'new_device', 16, '', '', 1, '2011-02-15 19:13:58', 0);
INSERT INTO `logs` VALUES(31, 'Added Rack hey: RU', 'new_device', 1, '', '', 1, '2011-02-15 19:14:00', 0);
INSERT INTO `logs` VALUES(32, 'Added Server: dddd', 'new_device', 17, '', '', 1, '2011-02-15 19:16:20', 0);
INSERT INTO `logs` VALUES(33, 'Added Data Storage: CS-800S2-1', 'new_device', 18, '', '', 1, '2011-02-15 19:16:23', 0);
INSERT INTO `logs` VALUES(34, 'Added Data Storage: CS-800S2-1', 'new_device', 19, '', '', 1, '2011-02-15 19:16:24', 0);
INSERT INTO `logs` VALUES(35, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-15 19:16:33', 0);
INSERT INTO `logs` VALUES(36, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-15 19:16:35', 0);
INSERT INTO `logs` VALUES(37, 'Added Patch Panel: Patch 48', 'new_device', 20, '', '', 1, '2011-02-15 19:17:32', 0);
INSERT INTO `logs` VALUES(38, 'Added Patch Panel: Dividing Panel', 'new_device', 21, '', '', 1, '2011-02-15 19:21:36', 0);
INSERT INTO `logs` VALUES(39, 'Updated Patch Panel: Dividing Panel', 'update_device', 21, '', '', 1, '2011-02-15 19:21:44', 0);
INSERT INTO `logs` VALUES(40, 'Added Power: PSU1', 'new_device', 22, '', '', 1, '2011-02-15 19:22:50', 0);
INSERT INTO `logs` VALUES(41, 'Added Switch: Switch Hey', 'new_device', 23, '', '', 1, '2011-02-15 19:25:50', 0);
INSERT INTO `logs` VALUES(42, 'Added Rack 36S: RU', 'new_device', 1, '', '', 1, '2011-02-15 19:26:53', 0);
INSERT INTO `logs` VALUES(43, 'Added Rack 36R: RU', 'new_device', 1, '', '', 1, '2011-02-15 19:27:35', 0);
INSERT INTO `logs` VALUES(44, 'Added Patch Panel: Dividing Panel', 'new_device', 24, '', '', 1, '2011-02-15 19:29:26', 0);
INSERT INTO `logs` VALUES(45, 'Added Patch Panel: Dividing Panel', 'new_device', 25, '', '', 1, '2011-02-15 19:29:53', 0);
INSERT INTO `logs` VALUES(46, 'Updated Patch Panel: Dividing Panel', 'update_device', 25, '', '', 1, '2011-02-15 19:30:01', 0);
INSERT INTO `logs` VALUES(47, 'Added Patch Panel: Dividing Panel', 'new_device', 26, '', '', 1, '2011-02-15 19:30:23', 0);
INSERT INTO `logs` VALUES(48, 'Updated Patch Panel: Dividing Panel', 'update_device', 26, '', '', 1, '2011-02-15 19:30:55', 0);
INSERT INTO `logs` VALUES(49, 'Added Server: Server1', 'new_device', 27, '', '', 1, '2011-02-15 19:35:56', 0);
INSERT INTO `logs` VALUES(50, 'Added Power: vxfvxv', 'new_device', 28, '', '', 1, '2011-02-15 22:00:16', 0);
INSERT INTO `logs` VALUES(51, 'Added Patch Panel: Patch1', 'new_device', 29, '', '', 1, '2011-02-15 23:23:43', 0);
INSERT INTO `logs` VALUES(52, 'Updated Patch Panel: Patch1', 'update_device', 29, '', '', 1, '2011-02-15 23:30:58', 0);
INSERT INTO `logs` VALUES(53, 'Added Switch: Switch1', 'new_device', 30, '', '', 1, '2011-02-15 23:32:03', 0);
INSERT INTO `logs` VALUES(54, 'Added Patch Panel: AAAA', 'new_device', 31, '', '', 1, '2011-02-16 00:32:05', 0);
INSERT INTO `logs` VALUES(55, 'Updated Server: DNSserv1', 'update_device', 8, '', '', 1, '2011-02-16 02:14:28', 0);
INSERT INTO `logs` VALUES(56, 'Updated Server: Server1', 'update_device', 27, '', '', 1, '2011-02-16 02:26:33', 0);
INSERT INTO `logs` VALUES(57, 'Updated Switch: Switch Hey', 'update_device', 23, '', '', 1, '2011-02-16 02:43:05', 0);
INSERT INTO `logs` VALUES(58, 'Added Power: PDU-1', 'new_device', 32, '', '', 1, '2011-02-16 04:39:25', 0);
INSERT INTO `logs` VALUES(59, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:39:58', 0);
INSERT INTO `logs` VALUES(60, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:40:18', 0);
INSERT INTO `logs` VALUES(61, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:40:21', 0);
INSERT INTO `logs` VALUES(62, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:40:24', 0);
INSERT INTO `logs` VALUES(63, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:40:25', 0);
INSERT INTO `logs` VALUES(64, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:40:26', 0);
INSERT INTO `logs` VALUES(65, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 04:40:29', 0);
INSERT INTO `logs` VALUES(66, 'Added Rack test: RU', 'new_device', 1, '', '', 1, '2011-02-16 04:42:30', 0);
INSERT INTO `logs` VALUES(67, 'Added Power: test', 'new_device', 33, '', '', 1, '2011-02-16 04:45:06', 0);
INSERT INTO `logs` VALUES(68, 'Added Switch: opopopoop', 'new_device', 34, '', '', 1, '2011-02-16 09:20:44', 0);
INSERT INTO `logs` VALUES(69, 'Updated Switch: opopopoop', 'update_device', 34, '', '', 1, '2011-02-16 09:21:01', 0);
INSERT INTO `logs` VALUES(70, 'Updated Switch: opopopoop', 'update_device', 34, '', '', 1, '2011-02-16 09:21:17', 0);
INSERT INTO `logs` VALUES(71, 'Added Power: ww', 'new_device', 35, '', '', 1, '2011-02-16 09:25:08', 0);
INSERT INTO `logs` VALUES(72, 'Added Power: testptu', 'new_device', 36, '', '', 1, '2011-02-16 09:30:05', 0);
INSERT INTO `logs` VALUES(73, 'Added Power: testptu2', 'new_device', 37, '', '', 1, '2011-02-16 09:32:49', 0);
INSERT INTO `logs` VALUES(74, 'Updated Patch Panel: eeee', 'update_device', 15, '', '', 1, '2011-02-16 12:59:39', 0);
INSERT INTO `logs` VALUES(75, 'Updated Patch Panel: Dividing Panel', 'update_device', 25, '', '', 1, '2011-02-16 12:59:49', 0);
INSERT INTO `logs` VALUES(76, 'Updated Patch Panel: Dividing Panel', 'update_device', 25, '', '', 1, '2011-02-16 12:59:51', 0);
INSERT INTO `logs` VALUES(77, 'Updated Patch Panel: Dividing Panel', 'update_device', 25, '', '', 1, '2011-02-16 12:59:53', 0);
INSERT INTO `logs` VALUES(78, 'Deleted Patch Panel: eeee', 'delete_device', 15, '', '', 1, '2011-02-16 13:01:04', 0);
INSERT INTO `logs` VALUES(79, 'Updated Power: testptu2', 'update_device', 37, '', '', 1, '2011-02-16 13:01:11', 0);
INSERT INTO `logs` VALUES(80, 'Updated Power: testptu2', 'update_device', 37, '', '', 1, '2011-02-16 13:01:14', 0);
INSERT INTO `logs` VALUES(81, 'Updated Power: testptu2', 'update_device', 37, '', '', 1, '2011-02-16 13:01:16', 0);
INSERT INTO `logs` VALUES(82, 'Updated Switch: Switch Hey', 'update_device', 23, '', '', 1, '2011-02-16 13:15:20', 0);
INSERT INTO `logs` VALUES(83, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:15:22', 0);
INSERT INTO `logs` VALUES(84, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-16 13:15:23', 0);
INSERT INTO `logs` VALUES(85, 'Updated Power: PSU1', 'update_device', 22, '', '', 1, '2011-02-16 13:16:31', 0);
INSERT INTO `logs` VALUES(86, 'Updated Power: PSU1', 'update_device', 22, '', '', 1, '2011-02-16 13:16:33', 0);
INSERT INTO `logs` VALUES(87, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:16:34', 0);
INSERT INTO `logs` VALUES(88, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-16 13:16:38', 0);
INSERT INTO `logs` VALUES(89, 'Updated Switch: Switch Hey', 'update_device', 23, '', '', 1, '2011-02-16 13:16:40', 0);
INSERT INTO `logs` VALUES(90, 'Updated Power: PSU1', 'update_device', 22, '', '', 1, '2011-02-16 13:16:41', 0);
INSERT INTO `logs` VALUES(91, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:16:45', 0);
INSERT INTO `logs` VALUES(92, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-16 13:16:47', 0);
INSERT INTO `logs` VALUES(93, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:16:49', 0);
INSERT INTO `logs` VALUES(94, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:16:52', 0);
INSERT INTO `logs` VALUES(95, 'Updated Power: PSU1', 'update_device', 22, '', '', 1, '2011-02-16 13:16:54', 0);
INSERT INTO `logs` VALUES(96, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:16:57', 0);
INSERT INTO `logs` VALUES(97, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-16 13:17:01', 0);
INSERT INTO `logs` VALUES(98, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:17:51', 0);
INSERT INTO `logs` VALUES(99, 'Updated Switch: Switch Hey', 'update_device', 23, '', '', 1, '2011-02-16 13:17:53', 0);
INSERT INTO `logs` VALUES(100, 'Updated Power: PSU1', 'update_device', 22, '', '', 1, '2011-02-16 13:17:55', 0);
INSERT INTO `logs` VALUES(101, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:19:44', 0);
INSERT INTO `logs` VALUES(102, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:21:49', 0);
INSERT INTO `logs` VALUES(103, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:22:17', 0);
INSERT INTO `logs` VALUES(104, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:22:30', 0);
INSERT INTO `logs` VALUES(105, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:24:25', 0);
INSERT INTO `logs` VALUES(106, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:27:41', 0);
INSERT INTO `logs` VALUES(107, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:27:56', 0);
INSERT INTO `logs` VALUES(108, 'Updated Switch: Switch Hey', 'update_device', 23, '', '', 1, '2011-02-16 13:28:06', 0);
INSERT INTO `logs` VALUES(109, 'Updated Data Storage: CS-800S2-1', 'update_device', 19, '', '', 1, '2011-02-16 13:28:11', 0);
INSERT INTO `logs` VALUES(110, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:28:15', 0);
INSERT INTO `logs` VALUES(111, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:28:17', 0);
INSERT INTO `logs` VALUES(112, 'Updated Switch: Switch Hey', 'update_device', 23, '', '', 1, '2011-02-16 13:28:20', 0);
INSERT INTO `logs` VALUES(113, 'Updated Power: PSU1', 'update_device', 22, '', '', 1, '2011-02-16 13:28:23', 0);
INSERT INTO `logs` VALUES(114, 'Updated Data Storage: CS-800S2-1', 'update_device', 18, '', '', 1, '2011-02-16 13:28:29', 0);
INSERT INTO `logs` VALUES(115, 'Updated Data Storage: CS-800S2-1', 'update_device', 18, '', '', 1, '2011-02-16 13:33:34', 0);
INSERT INTO `logs` VALUES(116, 'Updated Patch Panel: Dividing Panel', 'update_device', 25, '', '', 1, '2011-02-16 13:33:41', 0);
INSERT INTO `logs` VALUES(117, 'Updated Server: Server1', 'update_device', 27, '', '', 1, '2011-02-16 13:33:43', 0);
INSERT INTO `logs` VALUES(118, 'Updated Switch: Switch1', 'update_device', 30, '', '', 1, '2011-02-16 13:33:45', 0);
INSERT INTO `logs` VALUES(119, 'Updated Patch Panel: Patch1', 'update_device', 29, '', '', 1, '2011-02-16 13:33:47', 0);
INSERT INTO `logs` VALUES(120, 'Updated Patch Panel: Dividing Panel', 'update_device', 26, '', '', 1, '2011-02-16 13:33:48', 0);
INSERT INTO `logs` VALUES(121, 'Deleted Data Storage: CS-800S2-1', 'delete_device', 18, '', '', 1, '2011-02-16 13:33:59', 0);
INSERT INTO `logs` VALUES(122, 'Deleted : ', 'delete_device', 18, '', '', 1, '2011-02-16 13:33:59', 0);
INSERT INTO `logs` VALUES(123, 'Deleted Switch: Switch Hey', 'delete_device', 23, '', '', 1, '2011-02-16 13:34:10', 0);
INSERT INTO `logs` VALUES(124, 'Deleted : ', 'delete_device', 23, '', '', 1, '2011-02-16 13:34:10', 0);
INSERT INTO `logs` VALUES(125, 'Updated Power: PDU-1', 'update_device', 32, '', '', 1, '2011-02-16 13:34:57', 0);
INSERT INTO `logs` VALUES(126, 'Deleted Power: PDU-1', 'delete_device', 32, '', '', 1, '2011-02-16 13:35:06', 0);
INSERT INTO `logs` VALUES(127, 'Updated Patch Panel: Patch 48', 'update_device', 20, '', '', 1, '2011-02-16 13:43:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `ownerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `contactname` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `afterHoursPhone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fax` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `serviceLevel` text NOT NULL,
  PRIMARY KEY (`ownerID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` VALUES(1, 'Default', 'Root', '1111', '2222', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `ports`
--

CREATE TABLE `ports` (
  `portID` int(11) NOT NULL AUTO_INCREMENT,
  `deviceID` int(11) NOT NULL,
  `vlan` varchar(20) NOT NULL,
  `cableTypeID` int(11) NOT NULL,
  `ipAddress` varchar(50) NOT NULL,
  `macAddress` varchar(50) NOT NULL,
  `bandwidth` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `cableID` int(11) NOT NULL,
  `disporder` int(11) NOT NULL DEFAULT '0',
  `joinID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`portID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=138 ;

--
-- Dumping data for table `ports`
--

INSERT INTO `ports` VALUES(1, 7, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(2, 7, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(3, 7, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(4, 8, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(5, 8, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(6, 8, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(7, 9, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(8, 9, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(9, 9, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(10, 10, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(11, 10, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(12, 10, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(13, 11, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(14, 11, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(15, 11, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(33, 17, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(32, 16, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(31, 16, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(35, 17, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(34, 17, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(45, 19, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(46, 19, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(47, 19, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(48, 19, '', 1, '', '', '10/100/1000', '4', 0, 3, 0);
INSERT INTO `ports` VALUES(49, 19, '', 1, '', '', '10/100/1000', '5', 0, 4, 0);
INSERT INTO `ports` VALUES(50, 19, '', 17, '', '', '10 Gbit/s', '6', 0, 5, 0);
INSERT INTO `ports` VALUES(51, 19, '', 17, '', '', '10 Gbit/s', '7', 0, 6, 0);
INSERT INTO `ports` VALUES(52, 19, '', 17, '', '', '10 Gbit/s', '8', 0, 7, 0);
INSERT INTO `ports` VALUES(53, 19, '', 17, '', '', '10 Gbit/s', '9', 0, 8, 0);
INSERT INTO `ports` VALUES(70, 27, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(71, 27, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(72, 30, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(73, 30, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(74, 30, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(75, 30, '', 1, '', '', '10/100/1000', '4', 0, 3, 0);
INSERT INTO `ports` VALUES(76, 30, '', 1, '', '', '10/100/1000', '5', 0, 4, 0);
INSERT INTO `ports` VALUES(77, 30, '', 1, '', '', '10/100/1000', '6', 0, 5, 0);
INSERT INTO `ports` VALUES(78, 30, '', 1, '', '', '10/100/1000', '7', 0, 6, 0);
INSERT INTO `ports` VALUES(79, 30, '', 1, '', '', '10/100/1000', '8', 0, 7, 0);
INSERT INTO `ports` VALUES(80, 30, '', 1, '', '', '10/100/1000', '9', 0, 8, 0);
INSERT INTO `ports` VALUES(81, 30, '', 1, '', '', '10/100/1000', '10', 0, 9, 0);
INSERT INTO `ports` VALUES(82, 30, '', 1, '', '', '10/100/1000', '11', 0, 10, 0);
INSERT INTO `ports` VALUES(83, 30, '', 1, '', '', '10/100/1000', '12', 0, 11, 0);
INSERT INTO `ports` VALUES(84, 30, '', 1, '', '', '10/100/1000', '13', 0, 12, 0);
INSERT INTO `ports` VALUES(85, 30, '', 1, '', '', '10/100/1000', '14', 0, 13, 0);
INSERT INTO `ports` VALUES(86, 30, '', 1, '', '', '10/100/1000', '15', 0, 14, 0);
INSERT INTO `ports` VALUES(87, 30, '', 1, '', '', '10/100/1000', '16', 0, 15, 0);
INSERT INTO `ports` VALUES(88, 34, '', 1, '', '', '10/100/1000', '1', 0, 0, 0);
INSERT INTO `ports` VALUES(89, 34, '', 1, '', '', '10/100/1000', '2', 0, 1, 0);
INSERT INTO `ports` VALUES(90, 34, '', 1, '', '', '10/100/1000', '3', 0, 2, 0);
INSERT INTO `ports` VALUES(91, 34, '', 1, '', '', '10/100/1000', '4', 0, 3, 0);
INSERT INTO `ports` VALUES(92, 34, '', 1, '', '', '10/100/1000', '5', 0, 4, 0);
INSERT INTO `ports` VALUES(93, 34, '', 1, '', '', '10/100/1000', '6', 0, 5, 0);
INSERT INTO `ports` VALUES(94, 34, '', 1, '', '', '10/100/1000', '7', 0, 6, 0);
INSERT INTO `ports` VALUES(95, 34, '', 1, '', '', '10/100/1000', '8', 0, 7, 0);
INSERT INTO `ports` VALUES(96, 34, '', 1, '', '', '10/100/1000', '9', 0, 8, 0);
INSERT INTO `ports` VALUES(97, 34, '', 1, '', '', '10/100/1000', '10', 0, 9, 0);
INSERT INTO `ports` VALUES(98, 34, '', 1, '', '', '10/100/1000', '11', 0, 10, 0);
INSERT INTO `ports` VALUES(99, 34, '', 1, '', '', '10/100/1000', '12', 0, 11, 0);
INSERT INTO `ports` VALUES(100, 34, '', 1, '', '', '10/100/1000', '13', 0, 12, 0);
INSERT INTO `ports` VALUES(101, 34, '', 1, '', '', '10/100/1000', '14', 0, 13, 0);
INSERT INTO `ports` VALUES(102, 34, '', 1, '', '', '10/100/1000', '15', 0, 14, 0);
INSERT INTO `ports` VALUES(103, 34, '', 1, '', '', '10/100/1000', '16', 0, 15, 0);
INSERT INTO `ports` VALUES(104, 34, '', 1, '', '', '10/100/1000', '17', 0, 16, 0);
INSERT INTO `ports` VALUES(105, 34, '', 1, '', '', '10/100/1000', '18', 0, 17, 0);
INSERT INTO `ports` VALUES(106, 34, '', 1, '', '', '10/100/1000', '19', 0, 18, 0);
INSERT INTO `ports` VALUES(107, 34, '', 1, '', '', '10/100/1000', '20', 0, 19, 0);
INSERT INTO `ports` VALUES(108, 34, '', 1, '', '', '10/100/1000', '21', 0, 20, 0);
INSERT INTO `ports` VALUES(109, 34, '', 1, '', '', '10/100/1000', '22', 0, 21, 0);
INSERT INTO `ports` VALUES(110, 34, '', 1, '', '', '10/100/1000', '23', 0, 22, 0);
INSERT INTO `ports` VALUES(111, 34, '', 1, '', '', '10/100/1000', '24', 0, 23, 0);
INSERT INTO `ports` VALUES(112, 34, '', 1, '', '', '10/100/1000', '25', 0, 24, 0);
INSERT INTO `ports` VALUES(113, 34, '', 1, '', '', '10/100/1000', '26', 0, 25, 0);
INSERT INTO `ports` VALUES(114, 34, '', 1, '', '', '10/100/1000', '27', 0, 26, 0);
INSERT INTO `ports` VALUES(115, 34, '', 1, '', '', '10/100/1000', '28', 0, 27, 0);
INSERT INTO `ports` VALUES(116, 34, '', 1, '', '', '10/100/1000', '29', 0, 28, 0);
INSERT INTO `ports` VALUES(117, 34, '', 1, '', '', '10/100/1000', '30', 0, 29, 0);
INSERT INTO `ports` VALUES(118, 34, '', 1, '', '', '10/100/1000', '31', 0, 30, 0);
INSERT INTO `ports` VALUES(119, 34, '', 1, '', '', '10/100/1000', '32', 0, 31, 0);
INSERT INTO `ports` VALUES(120, 34, '', 1, '', '', '10/100/1000', '33', 0, 32, 0);
INSERT INTO `ports` VALUES(121, 34, '', 1, '', '', '10/100/1000', '34', 0, 33, 0);
INSERT INTO `ports` VALUES(122, 34, '', 1, '', '', '10/100/1000', '35', 0, 34, 0);
INSERT INTO `ports` VALUES(123, 34, '', 1, '', '', '10/100/1000', '36', 0, 35, 0);
INSERT INTO `ports` VALUES(124, 34, '', 1, '', '', '10/100/1000', '37', 0, 36, 0);
INSERT INTO `ports` VALUES(125, 34, '', 1, '', '', '10/100/1000', '38', 0, 37, 0);
INSERT INTO `ports` VALUES(126, 34, '', 1, '', '', '10/100/1000', '39', 0, 38, 0);
INSERT INTO `ports` VALUES(127, 34, '', 1, '', '', '10/100/1000', '40', 0, 39, 0);
INSERT INTO `ports` VALUES(128, 34, '', 1, '', '', '10/100/1000', '41', 0, 40, 0);
INSERT INTO `ports` VALUES(129, 34, '', 1, '', '', '10/100/1000', '42', 0, 41, 0);
INSERT INTO `ports` VALUES(130, 34, '', 1, '', '', '10/100/1000', '43', 0, 42, 0);
INSERT INTO `ports` VALUES(131, 34, '', 1, '', '', '10/100/1000', '44', 0, 43, 0);
INSERT INTO `ports` VALUES(132, 34, '', 1, '', '', '10/100/1000', '45', 0, 44, 0);
INSERT INTO `ports` VALUES(133, 34, '', 1, '', '', '10/100/1000', '46', 0, 45, 0);
INSERT INTO `ports` VALUES(134, 34, '', 1, '', '', '10/100/1000', '47', 0, 46, 0);
INSERT INTO `ports` VALUES(135, 34, '', 1, '', '', '10/100/1000', '48', 0, 47, 0);
INSERT INTO `ports` VALUES(136, 34, '', 17, '', '', '10/100/1000', '49', 0, 48, 0);
INSERT INTO `ports` VALUES(137, 34, '', 17, '', '', '10/100/1000', '50', 0, 49, 0);

-- --------------------------------------------------------

--
-- Table structure for table `racks`
--

CREATE TABLE `racks` (
  `rackID` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` int(11) NOT NULL,
  `parentType` varchar(50) NOT NULL,
  `ownerID` int(11) NOT NULL,
  `model` varchar(100) NOT NULL,
  `deviceTypeID` int(11) NOT NULL,
  `sideMountable` int(4) NOT NULL,
  `width` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `RU` smallint(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`rackID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `racks`
--

INSERT INTO `racks` VALUES(1, 115, 'room', 1, 'ss', 3, 0, 19, 600, 0, 33, 'aaa', '');
INSERT INTO `racks` VALUES(2, 1, 'cabinet', 1, '0', 0, 0, 0, 0, 0, 10, 'aa Rack 1', '');
INSERT INTO `racks` VALUES(3, 116, 'room', 1, 'APC', 3, 0, 19, 600, 0, 46, 'testR1', '');
INSERT INTO `racks` VALUES(4, 116, 'room', 1, 'APC', 3, 0, 19, 600, 0, 46, 'testR2', '');
INSERT INTO `racks` VALUES(5, 1, 'cabinet', 1, '0', 0, 0, 0, 0, 0, 4, 'Cab1 Rack 1', '');
INSERT INTO `racks` VALUES(6, 1, 'cabinet', 1, '0', 0, 0, 0, 0, 0, 4, 'Cab1 Rack 2', '');
INSERT INTO `racks` VALUES(7, 116, 'room', 1, 'APC', 3, 3, 19, 1000, 0, 42, 'hey', '');
INSERT INTO `racks` VALUES(8, 0, 'room', 1, 'APC', 3, 3, 19, 1000, 0, 32, 'hey', '');
INSERT INTO `racks` VALUES(9, 118, 'room', 1, 'HP', 3, 0, 19, 1000, 88, 42, '36S', '');
INSERT INTO `racks` VALUES(10, 118, 'room', 1, 'HP', 3, 0, 19, 1000, 88, 42, '36R', '');
INSERT INTO `racks` VALUES(11, 118, 'room', 1, 'test', 3, 3, 19, 600, 0, 42, 'test', '');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `roomID` int(11) NOT NULL AUTO_INCREMENT,
  `floorID` int(11) NOT NULL,
  `buildingID` int(11) NOT NULL,
  `ownerID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `revisionID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roomID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=119 ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` VALUES(115, 1, 1, 1, 'Room 1', '#688bc3', '', 0);
INSERT INTO `rooms` VALUES(116, 2, 2, 0, 'Room 1', '#688bc3', '', 0);
INSERT INTO `rooms` VALUES(117, 3, 3, 0, 'Room 1', '#688bc3', '', 0);
INSERT INTO `rooms` VALUES(118, 4, 4, 0, 'Room 1', '#688bc3', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `savedevents`
--

CREATE TABLE `savedevents` (
  `eventID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `logs` varchar(255) NOT NULL,
  PRIMARY KEY (`eventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores serialized arrays of log table IDs used for work orde' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `savedevents`
--


-- --------------------------------------------------------

--
-- Table structure for table `sessionitems`
--

CREATE TABLE `sessionitems` (
  `sessionID` int(20) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `itemID` varchar(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`sessionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='sessionitems' AUTO_INCREMENT=39 ;

--
-- Dumping data for table `sessionitems`
--

INSERT INTO `sessionitems` VALUES(26, 1, '7', 'rack');
INSERT INTO `sessionitems` VALUES(38, 1, '9', 'rack');

-- --------------------------------------------------------

--
-- Table structure for table `templateports`
--

CREATE TABLE `templateports` (
  `tempID` tinyint(8) NOT NULL AUTO_INCREMENT,
  `templateID` tinyint(8) NOT NULL,
  `portTypeID` tinyint(5) NOT NULL,
  `isJoin` tinyint(2) NOT NULL,
  `bandwidth` varchar(20) NOT NULL,
  `count` tinyint(4) NOT NULL,
  `disporder` tinyint(3) NOT NULL,
  PRIMARY KEY (`tempID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=86 ;

--
-- Dumping data for table `templateports`
--

INSERT INTO `templateports` VALUES(75, 58, 1, 0, '10/100/1000', 3, 1);
INSERT INTO `templateports` VALUES(64, 61, 1, 1, '', 16, 1);
INSERT INTO `templateports` VALUES(84, 60, 1, 0, '10/100/1000', 5, 1);
INSERT INTO `templateports` VALUES(67, 62, 1, 0, '10/100/1000', 2, 1);
INSERT INTO `templateports` VALUES(68, 63, 1, 0, '10/100/1000', 1, 1);
INSERT INTO `templateports` VALUES(72, 65, 1, 1, '', 48, 1);
INSERT INTO `templateports` VALUES(76, 70, 1, 0, '10/100/1000', 16, 1);
INSERT INTO `templateports` VALUES(83, 71, 1, 0, '10/100/1000', 2, 1);
INSERT INTO `templateports` VALUES(79, 73, 1, 1, '', 24, 1);
INSERT INTO `templateports` VALUES(80, 74, 1, 1, '', 24, 1);
INSERT INTO `templateports` VALUES(81, 76, 1, 0, '10/100/1000', 48, 1);
INSERT INTO `templateports` VALUES(82, 76, 17, 0, '10/100/1000', 2, 2);
INSERT INTO `templateports` VALUES(85, 60, 17, 0, '10 Gbit/s', 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `templateID` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '1',
  `deviceTypeID` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `background` varchar(200) NOT NULL,
  PRIMARY KEY (`templateID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` VALUES(58, 0, 3, 'A406', '');
INSERT INTO `templates` VALUES(59, 0, 3, 'aaaa', '');
INSERT INTO `templates` VALUES(60, 0, 2, 'Futijitsu CS800-S2', '');
INSERT INTO `templates` VALUES(61, 1, 7, 'bbbb', '');
INSERT INTO `templates` VALUES(62, 0, 2, 'SX', '');
INSERT INTO `templates` VALUES(63, 0, 5, 'cccc', '');
INSERT INTO `templates` VALUES(64, 0, 7, 'Patch1', '');
INSERT INTO `templates` VALUES(65, 0, 7, '48', '');
INSERT INTO `templates` VALUES(66, 0, 5, 'apc', '');
INSERT INTO `templates` VALUES(67, 0, 5, 'PSU', '');
INSERT INTO `templates` VALUES(68, 0, 7, 'Dividing Panel', '');
INSERT INTO `templates` VALUES(69, 1, 4, 'dddd', '');
INSERT INTO `templates` VALUES(70, 0, 4, 'Switch1', '');
INSERT INTO `templates` VALUES(71, 0, 3, 'Gen Server', '');
INSERT INTO `templates` VALUES(72, 0, 5, 'power', '');
INSERT INTO `templates` VALUES(73, 0, 7, 'eeee', '');
INSERT INTO `templates` VALUES(74, 0, 7, 'patch 24 optic', '');
INSERT INTO `templates` VALUES(75, 0, 5, 'PDU', '');
INSERT INTO `templates` VALUES(76, 0, 4, 'test', '');
INSERT INTO `templates` VALUES(77, 0, 5, 'ffff', '');
INSERT INTO `templates` VALUES(78, 0, 5, 'testppp', '');
INSERT INTO `templates` VALUES(79, 0, 5, 'testppp', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(40) NOT NULL,
  `external` varchar(10) NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `sessionKey` varchar(50) NOT NULL,
  `resetRequestKey` varchar(32) NOT NULL DEFAULT '0',
  `metric` tinyint(1) NOT NULL DEFAULT '1',
  `dateformat` varchar(15) NOT NULL DEFAULT 'd-m-Y',
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 'root', '0', 'e99a18c428cb38d5f260853678922e03', '', '123123', 'c3f1f7c2c10e0f1efb58e9b95393f8a6', '', 1, 'd-m-Y');
INSERT INTO `users` VALUES(43, 'test', '0', '098f6bcd4621d373cade4e832627b4f6', '', '', '', '', 1, 'm-d-Y');
