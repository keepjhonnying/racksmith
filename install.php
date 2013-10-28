<?php
$install=array();
$install[]="CREATE TABLE `attachments` (`objectID` smallint(7) NOT NULL AUTO_INCREMENT,`assetID` smallint(7) NOT NULL,`assetType` varchar(7) NOT NULL,`name` varchar(100) NOT NULL,`filename` varchar(150) NOT NULL,`userID` smallint(6) NOT NULL,`date` date NOT NULL,PRIMARY KEY (`objectID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
$install[]="CREATE TABLE `attrcategory` (`attrcategoryid` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(50) NOT NULL,`static` tinyint(1) NOT NULL DEFAULT '1',`sort` smallint(6) NOT NULL,PRIMARY KEY (`attrcategoryid`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=250; ";
$install[]="INSERT INTO `attrcategory` VALUES(7, 'Draws Power', 1, 5); ";
$install[]="INSERT INTO `attrcategory` VALUES(8, 'Generates Power', 1, 3); ";
$install[]="INSERT INTO `attrcategory` VALUES(2, 'Rack Mountable', 1, 15); ";
$install[]="INSERT INTO `attrcategory` VALUES(3, 'Floor Device', 1, 14); ";
$install[]="INSERT INTO `attrcategory` VALUES(1, 'Generic', 1, 16); ";
$install[]="INSERT INTO `attrcategory` VALUES(10, 'Provides Cooling', 1, 2); ";
$install[]="INSERT INTO `attrcategory` VALUES(9, 'Is UPS', 1, 4); ";
$install[]="INSERT INTO `attrcategory` VALUES(12, 'Has Software', 1, 9); ";
$install[]="INSERT INTO `attrcategory` VALUES(13, 'Has Operating System', 1, 10); ";
$install[]="INSERT INTO `attrcategory` VALUES(14, 'Is Patch', 1, 6); ";
$install[]="INSERT INTO `attrcategory` VALUES(15, 'Network Ports', 1, 8); ";
$install[]="INSERT INTO `attrcategory` VALUES(17, 'Provides Data Storage', 1, 1); ";
$install[]="INSERT INTO `attrcategory` VALUES(5, 'Is Shelf', 1, 12); ";
$install[]="INSERT INTO `attrcategory` VALUES(16, 'Has LOM', 1, 7); ";
$install[]="INSERT INTO `attrcategory` VALUES(18, 'Requires Servicing', 1, 0); ";
$install[]="INSERT INTO `attrcategory` VALUES(4, 'Outdoor Item', 1, 13); ";
$install[]="INSERT INTO `attrcategory` VALUES(6, 'Is Chassis', 1, 11); ";
$install[]="INSERT INTO `attrcategory` VALUES(25, 'Is PDU', 0, 0); ";
$install[]="CREATE TABLE `attrcategoryvalues` (`attrcatvalid` smallint(12) NOT NULL AUTO_INCREMENT, `parentID` smallint(12) NOT NULL, `parentType` varchar(50) NOT NULL, `categoryID` smallint(12) NOT NULL, PRIMARY KEY (`attrcatvalid`), KEY `parentID` (`parentID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=500; ";
$install[]="CREATE TABLE `attrnames` ( `attrnameid` int(12) NOT NULL AUTO_INCREMENT, `parentid` int(11) NOT NULL, `parenttype` varchar(25) NOT NULL, `name` varchar(100) NOT NULL, `type` varchar(25) NOT NULL, `default` varchar(250) NOT NULL, `units` varchar(25) NOT NULL, `options` varchar(250) NOT NULL, `desc` varchar(400) NOT NULL, `static` tinyint(1) NOT NULL, `control` smallint(1) NOT NULL, `sort` smallint(6) NOT NULL, PRIMARY KEY (`attrnameid`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=500; ";
$install[]="INSERT INTO `attrnames` VALUES(8, 1, 'attrcategory', 'Height', 'Number', '', 'mm', '', 'Generic height of the item Generic height of the item Generic height of the item', 1, 0, 7); ";
$install[]="INSERT INTO `attrnames` VALUES(7, 1, 'attrcategory', 'Width', 'Number', '', 'mm', '', '', 1, 0, 5); ";
$install[]="INSERT INTO `attrnames` VALUES(14, 1, 'attrcategory', 'Weight', 'Number', '', 'kg', '', '', 1, 0, 3); ";
$install[]="INSERT INTO `attrnames` VALUES(5, 2, 'attrcategory', 'Rack Units', 'Number', '0', 'RU', '', '', 1, 1, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(9, 1, 'attrcategory', 'Depth', 'Number', '', 'mm', '', '', 1, 0, 2); ";
$install[]="INSERT INTO `attrnames` VALUES(10, 1, 'attrcategory', 'Serial Number', 'Textbox', '', '', '', '', 1, 0, 6); ";
$install[]="INSERT INTO `attrnames` VALUES(11, 1, 'attrcategory', 'Barcode', 'Textbox', '', '', '', '', 1, 0, 1); ";
$install[]="INSERT INTO `attrnames` VALUES(12, 1, 'attrcategory', 'Vendor', 'Textbox', '', '', '', '', 1, 0, 8); ";
$install[]="INSERT INTO `attrnames` VALUES(13, 1, 'attrcategory', 'Warranty Details', 'Text Area', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(16, 6, 'attrcategory', 'Vertical Mount Points', 'Number', '1', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(17, 6, 'attrcategory', 'Horizontal Mount Points', 'Number', '1', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(18, 7, 'attrcategory', 'Power Supplies', 'Number', '1', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(19, 7, 'attrcategory', 'Max Rating', 'Number', '', 'Watts', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(20, 7, 'attrcategory', 'Normal Draw', 'Textbox', '', 'Watts', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(21, 7, 'attrcategory', 'PSU Hot Swap', 'Checkbox', 'No', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(22, 18, 'attrcategory', 'Last Service', 'Date', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(23, 18, 'attrcategory', 'Service Frequency', 'Number', '', 'months', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(24, 18, 'attrcategory', 'Last Serviced By', 'Textbox', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(26, 17, 'attrcategory', 'Capacity', 'Number', '', 'Gigabytes', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(27, 16, 'attrcategory', 'IP Address', 'Textbox', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(28, 13, 'attrcategory', 'Operating System', 'Textbox', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(29, 13, 'attrcategory', 'Version/Service Pack', 'Textbox', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(30, 13, 'attrcategory', 'Architecture', 'Radio Buttons', 'unknown', '', 'x86,x86_64,SPARC,other/unknown', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(31, 8, 'attrcategory', 'Max Rating', 'Number', '', 'Watts', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(35, 8, 'attrcategory', 'Start Up Delay', 'Number', '0', 'seconds', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(34, 8, 'attrcategory', 'Phases', 'Number', '', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(36, 8, 'attrcategory', 'Voltage', 'Number', '240', 'Volts', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(37, 9, 'attrcategory', 'Voltage', 'Number', '', 'Volts', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(38, 9, 'attrcategory', 'Phases', 'Number', '1', '', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(39, 9, 'attrcategory', 'Capacity', 'Number', '', 'VA', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(40, 10, 'attrcategory', 'Output Capacity', 'Number', '', 'kW', '', '', 1, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(42, 1, 'attrcategory', 'Model', 'Textbox', '', '', '', '', 0, 0, 4); ";
$install[]="INSERT INTO `attrnames` VALUES(45, 25, 'attrcategory', 'Max connections', 'Number', '0', '', '', '', 0, 0, 0); ";
$install[]="INSERT INTO `attrnames` VALUES(46, 25, 'attrcategory', 'Fuse Maximum', 'Number', '16', 'Amps', '', '', 0, 0, 0); ";
$install[]="CREATE TABLE `attroptions` ( `attroptionID` int(12) NOT NULL, `attrnameid` int(12) NOT NULL, `name` varchar(200) NOT NULL, PRIMARY KEY (`attroptionID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1; ";
$install[]="INSERT INTO `attroptions` VALUES(0, 4, ''); ";
$install[]="CREATE TABLE `attroptionvalues` ( `attroptionvalueid` int(11) NOT NULL AUTO_INCREMENT, `attrvalueid` int(11) NOT NULL, `attroptionid` int(11) NOT NULL, PRIMARY KEY (`attroptionvalueid`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000; ";
$install[]="CREATE TABLE `attrvalues` ( `attrvalueid` int(11) NOT NULL AUTO_INCREMENT, `attrnameid` int(11) NOT NULL, `value` varchar(400) NOT NULL, `parentid` int(11) NOT NULL, `parenttype` varchar(25) NOT NULL, PRIMARY KEY (`attrvalueid`), KEY `parentid` (`parentid`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000; ";
$install[]="CREATE TABLE `buildings` ( `buildingID` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `description` varchar(500) NOT NULL, `notes` varchar(500) NOT NULL, `ownerID` varchar(100) NOT NULL, `revisionID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`buildingID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `cabinets` ( `cabinetID` int(12) NOT NULL AUTO_INCREMENT, `parentType` varchar(50) NOT NULL, `parentID` int(12) NOT NULL, `name` varchar(120) NOT NULL, `ownerID` int(12) NOT NULL, `notes` text NOT NULL, PRIMARY KEY (`cabinetID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `cablecategories` ( `categoryID` int(9) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `type` varchar(50) NOT NULL, `enabled` smallint(1) NOT NULL, PRIMARY KEY (`categoryID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=100; ";
$install[]="INSERT INTO `cablecategories` VALUES(1, 'RJ45', '1', 1); ";
$install[]="INSERT INTO `cablecategories` VALUES(11, 'USB', '1', 0); ";
$install[]="INSERT INTO `cablecategories` VALUES(2, 'Fiber', '1', 1); ";
$install[]="INSERT INTO `cablecategories` VALUES(17, 'Fiber Multimode', '1', 1); ";
$install[]="INSERT INTO `cablecategories` VALUES(18, 'Power cable', '2', 1); ";
$install[]="CREATE TABLE `cables` ( `cableID` int(11) NOT NULL AUTO_INCREMENT, `barcode` varchar(100) NOT NULL, `cableTypeID` int(11) NOT NULL, `revisionID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`cableID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `cabletypejoins` ( `entryID` int(9) NOT NULL AUTO_INCREMENT, `categoryID` int(9) NOT NULL, `cableTypeID` int(9) NOT NULL, PRIMARY KEY (`entryID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=61 ; ";
$install[]="INSERT INTO `cabletypejoins` VALUES(55, 1, 22); ";
$install[]="INSERT INTO `cabletypejoins` VALUES(54, 1, 21); ";
$install[]="INSERT INTO `cabletypejoins` VALUES(53, 2, 4); ";
$install[]="INSERT INTO `cabletypejoins` VALUES(56, 1, 23); ";
$install[]="INSERT INTO `cabletypejoins` VALUES(58, 1, 5); ";
$install[]="INSERT INTO `cabletypejoins` VALUES(59, 2, 23); ";
$install[]="INSERT INTO `cabletypejoins` VALUES(60, 18, 2); ";
$install[]="CREATE TABLE `cabletypes` ( `cableTypeID` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `isPower` tinyint(1) NOT NULL, PRIMARY KEY (`cableTypeID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=100; ";
$install[]="INSERT INTO `cabletypes` VALUES(1, 'Power DC12', 1); ";
$install[]="INSERT INTO `cabletypes` VALUES(18, 'InfiniBand', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(2, '3 Phase Power', 1); ";
$install[]="INSERT INTO `cabletypes` VALUES(4, 'Ethernet Cat 5/6', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(5, 'Fiber ST', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(8, 'Fiber LC', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(9, 'Fiber FC', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(10, 'Fiber SC', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(11, 'Fiber E2000', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(12, 'Fiber LX.5', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(14, 'Serial', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(21, 'Cat 5', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(22, 'Cat 5e', 0); ";
$install[]="INSERT INTO `cabletypes` VALUES(23, 'Cat 6', 0); ";
/* Created during install phase to test permissions, no longer needed here
 * $install[]="CREATE TABLE `config` ( `name` varchar(200) NOT NULL, `value` varchar(200) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1; ";
$install[]="INSERT INTO `config` VALUES('install_date', '20110502'); ";
$install[]="INSERT INTO `config` VALUES('version', '1.0_dev-02'); ";
$install[]="INSERT INTO `config` VALUES('ldap_auth', '0'); ";
$install[]="INSERT INTO `config` VALUES('ldap_server', ''); ";
$install[]="INSERT INTO `config` VALUES('buildingCanvasX', '2000'); ";
$install[]="INSERT INTO `config` VALUES('buildingCanvasY', '1412'); ";
$install[]="INSERT INTO `config` VALUES('ldap_basedn', ''); ";
$install[]="INSERT INTO `config` VALUES('ldaps_enabled', '0'); ";
$install[]="INSERT INTO `config` VALUES('ldap_prefix', ''); ";
$install[]="INSERT INTO `config` VALUES('ldap_group', ''); ";
$install[]="INSERT INTO `config` VALUES('webaddress', ''); ";
$install[]="INSERT INTO `config` VALUES('lockFloorTiles', '0'); ";
$install[]="INSERT INTO `config` VALUES('ldap_postfix', ''); ";
$install[]="INSERT INTO `config` VALUES('ldap_postfix', ''); ";
$install[]="INSERT INTO `config` VALUES('lockFloorTiles', '0'); ";
$install[]="INSERT INTO `config` VALUES('attachments_enabled', '1'); ";
$install[]="INSERT INTO `config` VALUES('attachment_path', 'images/uploads/files/'); ";
$install[]="INSERT INTO `config` VALUES('ldap_field', 'sAMAccountName'); ";
$install[]="INSERT INTO `config` VALUES('attachment_maxUpload', '30'); "; */
$install[]="CREATE TABLE `devices` ( `deviceID` smallint(9) NOT NULL AUTO_INCREMENT, `parentID` smallint(9) NOT NULL, `parentType` varchar(200) NOT NULL, `position` varchar(20) NOT NULL, `orientation` varchar(50) NOT NULL, `name` varchar(200) NOT NULL, `background` varchar(200) NOT NULL, `deviceTypeID` smallint(9) NOT NULL, `templateID` smallint(9) NOT NULL, `ownerID` smallint(9) NOT NULL, PRIMARY KEY (`deviceID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `devicetypes` ( `deviceTypeID` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, PRIMARY KEY (`deviceTypeID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ; ";
$install[]="INSERT INTO `devicetypes` VALUES(1, 'Cooling'); ";
$install[]="INSERT INTO `devicetypes` VALUES(2, 'Data Storage'); ";
$install[]="INSERT INTO `devicetypes` VALUES(3, 'Server'); ";
$install[]="INSERT INTO `devicetypes` VALUES(4, 'Switch');";
$install[]="INSERT INTO `devicetypes` VALUES(5, 'Power');";
$install[]="INSERT INTO `devicetypes` VALUES(6, 'Power Generator')";
$install[]="INSERT INTO `devicetypes` VALUES(7, 'Patch Panel');";
$install[]="CREATE TABLE `floors` ( `floorID` int(11) NOT NULL AUTO_INCREMENT, `buildingID` int(11) NOT NULL, `name` varchar(100) NOT NULL, `notes` varchar(500) NOT NULL, `sort` tinyint(3) NOT NULL, `revisionID` int(11) NOT NULL, PRIMARY KEY (`floorID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5; ";
$install[]="CREATE TABLE `joins` ( `deviceID` int(11) NOT NULL, `joinID` int(11) NOT NULL AUTO_INCREMENT, `disporder` int(11) NOT NULL, `primPort` int(11) NOT NULL, `secPort` int(11) NOT NULL, `cableTypeID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`joinID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `layoutitems` ( `layoutItemID` int(11) NOT NULL AUTO_INCREMENT, `parentID` int(11) NOT NULL, `itemID` int(11) NOT NULL, `parentName` varchar(50) NOT NULL, `itemName` varchar(50) NOT NULL, `parentType` varchar(50) NOT NULL, `itemType` varchar(50) NOT NULL, `posX` int(11) NOT NULL, `posY` int(11) NOT NULL, `rotation` smallint(3) NOT NULL DEFAULT '0', `width` int(11) NOT NULL, `height` int(11) NOT NULL, `zindex` smallint(4) NOT NULL, `revisionID` int(11) NOT NULL, PRIMARY KEY (`layoutItemID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `licences` ( `licenceID` int(11) NOT NULL AUTO_INCREMENT, `deviceID` int(11) NOT NULL, `software` varchar(50) NOT NULL, `licence` varchar(150) NOT NULL, `softwareNotes` varchar(250) NOT NULL, `revisionID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`licenceID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `logs` ( `logID` int(11) NOT NULL AUTO_INCREMENT, `event` varchar(400) NOT NULL, `eventType` varchar(100) NOT NULL, `itemID` int(11) NOT NULL, `previous` varchar(50) NOT NULL, `comment` text NOT NULL, `userID` int(11) NOT NULL, `eventTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `revisionID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`logID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `owners` ( `ownerID` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `contactname` varchar(100) NOT NULL, `phone` varchar(100) NOT NULL, `afterHoursPhone` varchar(15) NOT NULL, `email` varchar(100) NOT NULL, `fax` varchar(100) NOT NULL, `mobile` varchar(100) NOT NULL, `serviceLevel` text NOT NULL, PRIMARY KEY (`ownerID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `ports` ( `portID` int(11) NOT NULL AUTO_INCREMENT, `deviceID` int(11) NOT NULL, `vlan` varchar(20) NOT NULL, `cableTypeID` int(11) NOT NULL, `ipAddress` varchar(50) NOT NULL, `macAddress` varchar(50) NOT NULL, `bandwidth` varchar(50) NOT NULL, `label` varchar(50) NOT NULL, `cableID` int(11) NOT NULL, `disporder` int(11) NOT NULL DEFAULT '0', `joinID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`portID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `racks` ( `rackID` int(11) NOT NULL AUTO_INCREMENT, `parentID` int(11) NOT NULL, `parentType` varchar(50) NOT NULL, `ownerID` int(11) NOT NULL, `model` varchar(100) NOT NULL, `deviceTypeID` int(11) NOT NULL, `sideMountable` int(4) NOT NULL, `width` int(11) NOT NULL, `depth` int(11) NOT NULL, `height` int(11) NOT NULL, `RU` smallint(6) NOT NULL, `name` varchar(100) NOT NULL, `notes` text NOT NULL, PRIMARY KEY (`rackID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `rooms` ( `roomID` int(11) NOT NULL AUTO_INCREMENT, `floorID` int(11) NOT NULL, `buildingID` int(11) NOT NULL, `ownerID` int(11) NOT NULL, `name` varchar(100) NOT NULL, `color` varchar(7) NOT NULL, `notes` varchar(500) NOT NULL, `revisionID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`roomID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `savedevents` ( `eventID` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(30) NOT NULL, `logs` varchar(255) NOT NULL, PRIMARY KEY (`eventID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores serialized arrays of log table IDs used for work orde' AUTO_INCREMENT=1 ; ";
$install[]="CREATE TABLE `sessionitems` ( `sessionID` int(20) NOT NULL AUTO_INCREMENT, `userID` int(11) NOT NULL, `itemID` varchar(11) NOT NULL, `type` varchar(10) NOT NULL, PRIMARY KEY (`sessionID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='sessionitems' AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `templateports` ( `tempID` tinyint(8) NOT NULL AUTO_INCREMENT, `templateID` tinyint(8) NOT NULL, `portTypeID` tinyint(5) NOT NULL, `isJoin` tinyint(2) NOT NULL, `bandwidth` varchar(20) NOT NULL, `count` tinyint(4) NOT NULL, `disporder` tinyint(3) NOT NULL, PRIMARY KEY (`tempID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `templates` ( `templateID` int(11) NOT NULL AUTO_INCREMENT, `deleted` tinyint(1) NOT NULL DEFAULT '1', `deviceTypeID` int(11) NOT NULL, `name` varchar(150) NOT NULL, `background` varchar(200) NOT NULL, PRIMARY KEY (`templateID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";
$install[]="CREATE TABLE `users` ( `userID` int(11) NOT NULL AUTO_INCREMENT, `userName` varchar(40) NOT NULL, `external` varchar(10) NOT NULL DEFAULT '0', `password` varchar(50) NOT NULL, `email` varchar(150) NOT NULL, `phone` varchar(20) NOT NULL, `sessionKey` varchar(50) NOT NULL, `resetRequestKey` varchar(32) NOT NULL DEFAULT '0', `metric` tinyint(1) NOT NULL DEFAULT '1', `dateformat` varchar(15) NOT NULL DEFAULT 'd-m-Y', PRIMARY KEY (`userID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

if(file_exists("class/config.inc.php"))
    include 'class/config.inc.php';
else
{
    $fp = @fopen('class/config.inc.php', 'a');
    if (!$fp)
    {
    include 'theme/top.php';
    echo "<div id=main>
	<div id=full class=module><h2>Please create config file</h2>
        <p>The config file  class/config.inc.php was not found.<br/></p><p>
        To continue the installation this file must exist and be writable by the web user</p>
        </div></div>";
    include 'theme/base.php';
    exit(0);
    }
    fclose($fp);
}

if(isset($sqltype) && isset($host) && isset($user) && isset($pass) && isset($dbname))
{
    include 'theme/top.php';
    echo "<div id=main>
	<div id=full class=module style='padding:0px;'>
        <div class='sectionHeader' >
            <strong><img src='images/icons/attn.png' style='float:left;margin-right:10px;margin-top: -3px;'/> Install Unavailable</strong>
        </div>
        <div style='padding:15px;' >
        <p>We have detected an existing Racksmith installation.
        <ul>
        <li>If you are unable to sign in to your fresh installation please refer to <br/><a href='http://help.racksmith.net' target='_new' style='font-style:italic;'>http://help.racksmith.net</a><br/></p><p></li>
        <li>To do a fresh install, please make sure you have dropped any racksmith tables from your database and delete the contents of the theme/config.inc.php file</li>
        </ul>
        It is recommended that you remove install.php after an installation.</p>
        </div></div></div>";
    include 'theme/base.php';
    exit(0);
}

if(!is_writable('class/config.inc.php'))
{
    include 'theme/top.php';
    echo "<div id=main>
	<div id=full class=module style='padding:0px;'>
        <div class='sectionHeader' >
            <strong><img src='images/icons/attn.png' style='float:left;margin-right:10px;margin-top: -3px;'/> Install Unavailable</strong>
        </div>
        <div style='padding:15px;' >
        Racksmith does not have write permission to class/config.inc.php.<br />
        <p>This is required before installation can complete, Please ensure that the permissions are correct on this file.</p>

        <p>To find out more about assigning permissions you can refer to the help section at <a href='http://help.racksmith.net/guide.php?guide=install'>http://help.racksmith.net/guide.php?guide=install</a>

        <center><p><h1><a href='install.php'>Fixed it?..... Try again</a></h1></p></center>
        </div></div></div>";
    include 'theme/base.php';
    exit(0);
}

$systemVersion='1.0RC1';
include "./class/pdo.php";
include "./theme/top.php";
?>
<script type="text/javascript">
    function Validate()
    {
        valid=true;

        //Validate for MySQL
        if (document.getElementById("dbEngine").value == "MySQL")
        {
            if(document.getElementById("dbhost").value=="")
                valid=false;
            if(document.getElementById("dbname").value=="")
                valid=false;
            if(document.getElementById("dbuid").value=="")
                valid=false;
        }

        //Validate new user information
        if(document.getElementById("rsUser").value=="")
            valid=false;
        if(document.getElementById("rsPassA").value != document.getElementById("rsPassB").value)
            valid=false;
        return valid;
    }

    function LDAPCheck()
    {
        if(document.getElementById("chkEnableLDAP").checked == 1)
            document.getElementById("txtLDAPServer").readOnly = false;
        else
            document.getElementById("txtLDAPServer").readOnly = true;
    }
  </script>

<?php
$out="<ul>"; //forward reference;
$dbaFail=0;

if (isset($_POST["dbEngine"]))
{
    // given the host portion could include a port
    // check and change the format if it is included
    // we pass the variable down to the connect statement either blank or with the new correct port details
    $portPortion="";
    $dbPortCheck = strpos($_POST['dbhost'],":");
    if($dbPortCheck)
    {
        //$_POST['dbhost']=substr($_POST['dbhost'],0,$dbPortCheck);
        $portPortion=substr($_POST['dbhost'],$dbPortCheck+1);
        if(is_numeric($portPortion))
        {
            $portPortion="port=".$portPortion.";";
        }
    }
    
	// Test database
	try {
            if ($_POST["dbEngine"]=="MySQL")
                $dbtest = new PDO("mysql:host=".$_POST["dbhost"] . ";".$portPortion."dbname=".$_POST["dbname"], $_POST["dbuid"], $_POST["dbpw"]);
		
		$dbaFail=0;
	} catch(PDOException $e) {
            $dbaFail=1;
            $out.= "<li class='statuserror'>Database connection failed
                <ul><li><i style='color: black;'>Response: ".$e->getMessage()."</i></li></ul>";
	}
        
	if ($dbaFail!=1 && $dbtest)//Db con hasnt failed
	{
		$out.="<li class='statusok'>Connected to database...</li>";

		// try create a table and then try and insert to it
		// only insert if the table was made so we can catch the related error later on
		$dbtest->exec("CREATE TABLE `config` (`name` VARCHAR( 200 ) NOT NULL ,`value` VARCHAR( 200 ) NOT NULL);");               
		$error=$dbtest->errorInfo();
		if($error[0]=='00000')			
                    $writeSuccess=$dbtest->exec("INSERT INTO `config` (`name`, `value`) VALUES('install_date', '".@date("Ymd")."'),('version', '".$systemVersion."'),('ldap_auth', '0'),('ldap_server', ''),('buildingCanvasX', ''),('buildingCanvasY', ''),('ldap_basedn', ''),('ldaps_enabled', '0'),('ldap_prefix', ''),('ldap_group', '');");
		else
                    $writeSuccess=0;
			
		if($writeSuccess)
		{
                    $out.= "<li class='statusok'>Database Modification Successful</li>";
                    $confFile="<?php \n";
                    //prepare config file for use
                    $confFile.='$sqlType="'.strtolower($_POST['dbEngine']). "\";\n";
                    if (isset($_POST['dbhost']))
                        $confFile.='$host="'.$_POST['dbhost']."\";\n";
                    if (isset($_POST['dbuid']))
                        $confFile.='$user="'.$_POST['dbuid']."\";\n";

                    if (isset($_POST['dbpw']))
                        $confFile.='$pass="'.$_POST['dbpw']."\";\n";
                    else
                        $confFile.='$pass="";'."\n";

                    if (isset($_POST['dbname']))
                        $confFile.='$dbname="'.$_POST['dbname']."\";\n";
                    if (isset($_POST['manualHandle']))
                        $confFile.='$dbname="'.$_POST['manualHandle']."\";\n";

                    $file=fopen("./class/config.inc.php","w");
                    if ($file)
                    {
                        $confFile.="?>";
                        fwrite($file,$confFile);
                        fclose($file);
                        $out.="<li id='statusok'>Configuration file saved successfully</li>";
                        $dbaFail=0;
                    }
                    else
                    {
                        $out.="<li id='statuserror'><b>Configuration file could not be saved</b></li>";
                        $dbaFail=1;
                    }
							
		}
                // the write was not successful
                else
                {
                    $out.="<li class='statuserror'>Database Modification Failure
                    <ul><li style='color: black;' ><i>Response: ".print_r($dbtest->errorInfo(),1)."</li></ul>
                    </li><li style='color: red;'><b>Installation Failed</b></li>";
                    $dbaFail=1;
		}		

	} else
        { //ELSE IF $swa
		$out.="<li class='statuserror'><b>Installation Failed</b></li>";
		$dbaFail=1;
	}

	
	if ($dbaFail!=1)
	{
            //Forware reference for return value;
            $retVal="";

            foreach($install as $line_num=>$line)
            {
                try
                {
                    $dbtest->exec($line);
                } catch(PDOException $e)
                {
                    $dbaFail=1;
                    $out.= "<li class='statuserror'>Failed preparing database tables, we were up to entry: ".$line_num."
                    <ul><li><i style='color: black;'>Response: ".$e->getMessage()."</i></li></ul>";
                    break;
                }
            }

            $a=$dbtest->prepare("INSERT INTO users (userName,password,email,phone,sessionKey,resetRequestKey) VALUES (?,?,?,?,'','');");
            if (!$a) {
                    $out.="<li class='statuserror'>Failed to create user</li><li id='statuserror'><b>Installation Failed</b></li>";
                    $dbaFail=1;
            } else {
                $a->execute(array("root",md5($_POST['rsPassA']),$_POST['rsEmail'],$_POST['rsPhone']));
                if ($a->rowCount() >0)
                    $out.="<li class='statusok'>User Created Successfully</li>";
                else
                {
                    $out.="<li class='statuserror'>Failed to create user</li><li id='statuserror'><b>Installation Failed</b></li>";
                    $dbaFail=1;
                }
            }

            unset($a);
            $a=$dbtest->prepare("INSERT INTO owners (name,contactname,phone,email) VALUES ('Root','Root',?,?);");
            if (!$a) {
                    $out.="<li class='statuserror'>Failed to a default owner</li><li id='statuserror'><b>Installation Failed</b></li>";
                    $dbaFail=1;
            } else {
                $a->execute(array($_POST['rsEmail'],$_POST['rsPhone']));
                if ($a->rowCount() >0)
                    $out.="<li class='statusok'>Default owner Created Successfully</li>";
                else
                {
                    print_r($a->errorInfo());
                    $out.="<li class='statuserror'>Failed to create a default owner</li>
                    <li class='statuserror'><b>Installation Failed</b></li>";
                    $dbaFail=1;
                }
            }


            if(isset($_POST['txtWebAddress']))
                $webadd=$_POST['txtWebAddress'];
            else
                $webadd="";

            $configInsert=$dbtest->prepare("INSERT INTO config(name,value) VALUES (?,?);");
            $configInsert->execute(array("attachments_enabled",0));
            $configInsert->execute(array("attachment_maxUpload",30));
            $configInsert->execute(array("attachment_path","images/uploads/files"));
            $configInsert->execute(array("lockFloorTiles",0));
            $configInsert->execute(array("ldap_postfix",0));
            $configInsert->execute(array("ldap_field","sAMAccountName"));
            $configInsert->execute(array("webaddress",$webadd));
	}			
	//Output Installation Results
	?>
<div id="main">
	<div id="full" class=module>
		<h2>Installation Summary:</h2>
		<p><?php echo $out; ?></ul></p>
	<?php
		if ($dbaFail==1) //Installation failed
                    echo "<p>Racksmith has not been installed successfully.<br /><i> Click <a href=\"./install.php\">here</a> to go back to the install page. Check the information you provided during the installation and try again.</i></p>";
		else
                    echo "<p>Racksmith has been installed successfully.<br /><i> Remember to delete ./install.php when your done.</i></p><br/><br/><h3><a href='index.php' >Click here to continue</a>";
    	?>
      </div>
</div>
<?php
} else { ?>
<div id="main">
    <div class="module" id="full" style="padding:0px;">
        <div class="sectionHeader" >
            <strong>Installing RackSmith</strong>
        </div>
        <div style="padding: 10px;" >
            <table><tr><td width="50%" valign="top" >
    <form name="myform" onSubmit="return Validate();" action="install.php" method="post">
    <?php
    if(!is_writable('./class/config.inc.php'))
        echo '<center><h3><font color="red" >Please confirm ./class/config.inc.php is writable before you continue</font></h3></center>';
    ?>

    <strong>Database:</strong> <br />
        <table style="width:460px" class="formTable">
            <tr>
                <td width="170" >Database Engine</td>
                <td width="290">
                    <select name="dbEngine" id="dbEngine"  >
                        <option value="MySQL">MySQL</option>
                    </select>
                </td>
            </tr>

            <tr><td>Host Address:</td><td><input type=text name=dbhost id=dbhost value="localhost"> <span class="required">*</span></td></tr>
            <tr><td>Database Name:</td><td><input type=text name=dbname id=dbname' /> <span class="required">*</span></td></tr>
            <tr><td>Username:</td><td><input type=text name=dbuid id=dbuid /> <span class="required">*</span></td></tr>
            <tr><td>Password:</td><td><input type=text name=dbpw id=dbpw /> <span class="required">*</span></td></tr>
        </table>


            <br /><br/>
            <strong>Installation Address</strong>
            <br/>The web address where your copy of RackSmith will be installed.
            <table style="width:460px" class="formTable">
                <tbody>
                    <tr>
                        <td width="170px">Address:</td><?php
                        $path = explode("/",$_SERVER['REQUEST_URI']);
                        array_pop($path);
                        ?><td width="290px"><input type="text" size='50' name="txtWebAddress" id="txtWebAddress" value="http://<?php echo $_SERVER['SERVER_NAME'].implode("/",$path)."/";?>" /></td>
                    </tr>
                </tbody>
            </table>
            <br/><br/>

            <strong>User Account:</strong> <br />
            Setup the root user, this account is hosted locally even when LDAP is enabled:<br />
            <table style="width:460px" class="formTable">
                <tbody>
                    <tr><td width="170px">User Name:</td><td width="290px"><input type=text name=rsUser id=rsUser disabled value="root" /></td></tr>
                    <tr><td>Password:</td><td><input type=password name=rsPassA id=rsPassA /> <span class="required">*</span></td></tr>
                    <tr><td>Confirm Password:</td><td><input type=password name=rsPassB id=rsPassB /> <span class="required">*</span></td></tr>
                    <tr><td>Email:</td><td><input type=text name=rsEmail /></td></tr>
                    <tr><td>Phone:</td><td><input type=text name=rsPhone /></td></tr>
                </tbody>
            </table>
            <p>
                <input type="submit" name="Submit" value="Lets Go">
            </p>
            </form>
    <br/><font color="red" >* required</font>
                    </td><td width="50%" valign="top" style="border-left: 1px dashed #cccccc;padding-left:25px;">

    Almost done, just a few more details and you're ready to go.<br/>
    If you run into problems why not check out the <a href="http://help.racksmith.net/guide.php?guide=install" target="_help" >Install documentation</a> online.

                    </td></tr></table>
        </div>
    </div>
    </div>
</div>
<?php
include "theme/base.php";
}

?>