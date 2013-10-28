<?php
error_reporting(0);
header('Content-type: text/css');
header('Cache-Control: public');

define('RACKSMITH_PATH', "../");
include "../class/pdo.php";
$db = new DB;

$query = $db->query("SELECT templateID,frontPanelImage from templates WHERE frontPanelImage!='0'");
$result = $query->fetchAll();

foreach($result as $template)
	if(file_exists("../".$template['frontPanelImage']))
		echo '.template'.$template['templateID']." { background: url('../".$template['frontPanelImage']."') no-repeat center; }\n";
?>