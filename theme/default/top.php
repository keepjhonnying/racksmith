<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<title>RackSmith :: <?php if(isset($globalTopic)) { echo $globalTopic; } else { echo "An OpenSource Rack &amp; Patch Management Tool"; } ?></title> 
	<meta name="robots" content="noindex, nofollow" />
	<link rel='stylesheet' type='text/css' href='theme/main.css' />
	<link rel='stylesheet' type='text/css' href='theme/customTemplates.css.php' />
	<link rel='stylesheet' type="text/css" href="theme/ui.all.css" /> 
	<script type="text/javascript" src="theme/jquery-1.4.2.min.js"></script> 

	<script type="text/javascript" src="theme/common.js"></script> 
	<script type="text/javascript" src="theme/template.js"></script> 
	<script type="text/javascript" src="theme/js/rotate.js"></script> 
	

	<!-- Jquery UI items should be bundled together -->
	<script type="text/javascript" src="theme/ui.core.js"></script>
	<script type="text/javascript" src="theme/ui.resizable.js"></script>
	<script type="text/javascript" src="theme/ui.draggable.js"></script>
	<script type="text/javascript" src="theme/ui.droppable.js"></script>
	<script type="text/javascript" src="theme/ui.sortable.js"></script>
	<script type="text/javascript" src="theme/ui.accordion.js"></script>
	<script type="text/javascript" src="theme/ui.selectable.js"></script>
        
	<script type="text/javascript" src="theme/jquery.DOMWindow.js"></script>
	
	<?php if(isset($header)) echo $header; ?>
</head> 
<body> 

<div id="header"> 
	<div id="logo">
            <a href="index.php"><span>RackSmith</span></a>
	</div>
    <?php
        if(isset($_SESSION['username']))
        {
            if(!isset($selectedPage))
                $selectedPage="";
    ?>
        <div id="menu">
            <ul> 
                <li <?php if($selectedPage=="home") echo 'class="selected"';   ?> onclick="location.href = 'index.php';">Home</li>
                <li <?php if($selectedPage=="layout") echo 'class="selected"'; ?> onclick="location.href = 'buildings.php';">Layout</li>
                <li <?php if($selectedPage=="device") echo 'class="selected"'; ?> onclick="location.href = 'racks.php';">Devices</li>
                <li <?php if($selectedPage=="search") echo 'class="selected"'; ?> onclick="location.href = 'devices.php';">Search</li>
                <li <?php if($selectedPage=="configure") echo 'class="selected"'; ?> onclick="location.href = 'management.php';">Configure</li>
                <li <?php if($selectedPage=="logout") echo 'class="selected"'; ?> onclick="location.href = 'login.php?action=logout';">Logout</li>
            </ul>
	</div>
    <?php
        }
    ?>
</div> 
