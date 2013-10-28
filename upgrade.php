<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";
$config = new config();
$currentVersion = $config->returnItem("version");
$newVersion="1.0RC";

if(strcmp($currentVersion,$newVersion)==0)
{
	include 'theme/top.php';
	echo "<center><h1>Already Upgraded</h1><br/>
                <div style=\"background-color: #c9c9c9;padding: 15px; height: 60px; width: 650px;border: 1px solid #000000;text-align: left;\" >
                Your system has already been upgraded, this tool is now inactive<br/>
                </div></center>";
	include 'theme/base.php';
	exit(0);
}

else if($currentVersion=="0.9.0" && !isset($_GET['confirm']))
{
$out="<ul>"; //forward reference;

include "theme/top.php";
?>
<div style="padding-left:20px;">
    <div class="module" id="module" style="width: 90%;margin: 25px auto;">
        <h3>Upgrade RackSmith</h3>
        <p>
            A few DB changes still need to be made...
        </p>
        <p>
            <strong>Current Version:</strong> <font color='red' ><?php echo $currentVersion; ?></font><br/>
            <strong>Upgrade To:</strong> <font color='green' ><?php echo $newVersion; ?></font>
        </p>


        <a href="upgrade.php?confirm=1" ><h3>Complete Upgrade</h3></a>
    </div>
</div>
<?php
include "theme/base.php";
}
elseif(isset($_GET['confirm']) && $_GET['confirm']==1)
{
    include "theme/top.php";
    echo "<div class='module' style='margin:40px 100px;'>
        <h3>Performing upgrade</h3><hr/><ul>";
    $upgradeReturns=array();


    // check and insert the serial for devices
    $serial=0;
    $warranty=0;
    $back=0;
    $configInsert=$db->prepare("SHOW COLUMNS FROM rackdevices;");
    $configInsert->execute();
    $result = $configInsert->fetchAll(PDO::FETCH_ASSOC);
    foreach($result as $column)
    {
        if($column['Field']=="serial")
            $serial=1;
        if($column['Field']=="warranty")
            $warranty=1;
        if($column['Field']=="back")
            $back=1;
    }

    if(!$serial)
    {
        echo "<li>serial not found... ";
        $serialAdd = $db->exec("ALTER TABLE  `rackdevices` ADD  `serial` VARCHAR( 50 ) NOT NULL AFTER  `systemName`;");
        if($serialAdd)
            echo "<font color='green' >added</font>";
        else
        {
            echo "<font color='red' >failed</font>";
            $upgradeReturns[]=0;
        }
        echo "</li>";
    }

    if(!$warranty)
    {
        echo "<li>warranty not found... ";
        $warrantyAdd = $db->exec("ALTER TABLE  `rackdevices` ADD  `warranty` DATE NOT NULL AFTER  `serial`");
        if($warrantyAdd)
            echo "<font color='green' >added</font>";
        else
        {
            echo "<font color='red' >failed</font>";
            $upgradeReturns[]=0;
        }
        echo "</li>";
    }

    if(!$back)
    {
        echo "<li>back not found... ";
        $backAdd = $db->exec("ALTER TABLE  `rackdevices` ADD  `back` TINYINT( 1 ) NOT NULL AFTER  `ownerID`");
        if($backAdd)
            echo "<font color='green' >added</font>";
        else
        {
            echo "<font color='red' >failed</font>";
            $upgradeReturns[]=0;
        }
        echo "</li>";
    }


    $adjustSession=0;
    $adjustSession=$db->exec("ALTER TABLE  `sessionitems` CHANGE  `itemID`  `itemID` VARCHAR( 11 ) NOT NULL;");
    echo "<li>adjusting session table... ";
    echo "</li>";

    $configUn=0;
    $configUn=$db->exec("ALTER TABLE `config` ADD UNIQUE (`name`);");
    echo "<li>configs become unique... ";
    if($configUn)
        echo "<font color='green' >added</font>";
    else
        echo "<font color='red' >failed</font> but not required";
    echo "</li>";


    // Adjust the new values in the config
    $upgradeReturns[]=$config->insertItem("attachments_enabled",0);
    $upgradeReturns[]=$config->insertItem("attachment_path","images/uploads/files");
    $upgradeReturns[]=$config->insertItem("attachment_maxUpload",30);
    $upgradeReturns[]=$config->insertItem("lockFloorTiles",0);
    $upgradeReturns[]=$config->insertItem("ldap_postfix",'');
    $upgradeReturns[]=$config->insertItem("ldap_field","sAMAccountName");
    $upgradeReturns[]=$config->insertItem("attachments_enabled",0);
    $upgradeReturns[]=$config->insertItem("attachment_path",'images/uploads/files/');
    $upgradeReturns[]=$config->insertItem("attachment_maxUpload",30);
    $config->setItem("version","1.0RC1");

    $makeAttach=$db->exec("CREATE TABLE IF NOT EXISTS `attachments` (`objectID` smallint(7) NOT NULL AUTO_INCREMENT,  `assetID` smallint(7) NOT NULL,  `assetType` varchar(7) NOT NULL,  `name` varchar(100) NOT NULL,  `filename` varchar(150) NOT NULL,  `userID` smallint(6) NOT NULL,  `date` date NOT NULL,  PRIMARY KEY (`objectID`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
    echo "<li>create attachments table... ";
    $findAttach=$db->prepare("SHOW tables like 'attachments';");
    $findAttach->execute();
    $resultAttach = $findAttach->fetch();
    if($resultAttach[0]=="attachments")
        echo "<font color='green' >added</font>";
    else
        echo "<font color='red' >unable to find a table, there was a problem</font>";
    echo "</li>";


    echo "<p>";

    if(in_array(0, $upgradeReturns))
        echo "There was an error during one of the upgrade steps";
    else
    {
        $log=new log;
        $log->event = "Upgraded RackSmith to ".$newVersion;
        $log->eventType="upgrade_racksmith";
        $log->itemID=0;
        $logs = new logs();
        $logs->insert($log);

        echo "<font color='green' ><strong>Complete!</strong>
            Ready to continue</font>

            <br/><br/><a href='index.php' ><strong>Continue to RackSmith</strong></a>";
    }
    echo "</p></ul>";
    include "theme/base.php";

}
else
    header("Location: index.php");
?>