<?php
session_start();
include "class/db.class.php";
$selectedPage="configure";
// display the upload form and the templates available for exporting
if(!$_POST && !$_FILES && !isset($_GET['action']))
{
    include "theme/" . $theme . "/top.php";
?>
    <div id="main" >
        <div id="left" >
            
            
        <div class="module error"  >
            <strong>Feature Unavailable</strong>
            <p>
                Please note this feature is currently being expanded and will be available within the next release of RackSmith.<br/>
                It should only be used for legacy installs &amp; largely ignored in this release.
            </p>
        </div>
            
            
            <div class="module" id="templateUploadForm" style="opacity: 0.7;">
                <strong>Import Templates</strong>
                <p>
                    You may manually import templates from a provided XML file. This proccess will list available templates which you can then select for importion.<br/>
                    Please note you will require write access to the images directory to import backgrounds.
                </p>
                <form method="post" action="?action=importFromXML" enctype="multipart/form-data" >
                    <div class="form" >
                        <table style='width: 600px;' class="formTable">
                            <colgroup align="left" class="tblfirstRow"></colgroup>
                        <tr>
                            <td><label for="templateFile" >XML Template File:</label></td>
                            <td><input type="file" name="templateFile" /></td>
                        </tr>
                        <tr><td colspan='2' ><input type="submit" id="submitTemplate"  name="submitTemplate"  value="Start Import" /></td></tr>
                        </table>
                    </div>
                </form>
            </div>
			
				

	<div class="module" id="templates" style="opacity: 0.7;">
	<strong>Export Templates</strong>
	<p>
	Select all templates you'd like to export. From here you can move them to a new system or submit them back to the RackSmith project for others to use.
            <form method="post" action="?action=exportToXML" >
                <table class="dataTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
                    <thead>
			<tr>
				<th scope="col" align="center" style='width: 5px;'>
                                    <input type="checkbox" onclick="var checked=$('input:checkbox:enabled').attr('checked'); $('input:checkbox:enabled').attr('checked',checked);" />
				</th>
				<th scope="col">Type</th>
				<th scope="col" style='width: 180px'>Vendor</th>
				<th scope="col" >Model</th>
				<th scope="col" style='width: 90px;'>Rack Units</th>
			</tr>
			</thead>
			<tbody>
	<?php 
		// passed as an array of strings (vendorModelName) is used to highlight new items
		if(isset($_GET['added']))
			$added=unserialize(stripslashes($_GET['added']));
		else
			$added=array();
	
		$deviceTypes = new deviceTypes;
		foreach($deviceTypes->getAll() as $type)
		{ 
			//if(!$type->canTemplate)
				continue;

			// Loop Through Owners
			$templates = new templates;
			$listType = $templates->getByDeviceType($type->deviceTypeID);
			if($listType)
			foreach($listType as $template) 
			{ 
				if($template->display)
				{ 
					if(in_array($template->vendor.$template->modelName,$added))
						echo "<tr class='newRow' >";
					else
						echo "<tr>";
					?>
						<td align="center"><input onclick='$(this).closest("tr").toggleClass("selected");' type="checkbox" name="<?php echo $template->templateID;?>" /></td>
						<td style='width: 140px;'><?php echo$type->name;?></td>
						<td><?php echo $template->vendor ?></td> 
						<td><b><?php echo $template->modelName ?></b></td> 
						<td><?php echo $template->RU; ?></td> 
					</tr><?php 
				}
			} 
		} 
		?>
					</tbody>
					</table>
					<p>
                                            <input type="submit" id="exportTemplates"  name="exportTemplates"  value="Start Export" />
					</p>
				</p>	
			</div>

		
			</div>
			
    <div id="right" >
        <div class="module sideMenu">
            <strong>Devices &amp; Templates</strong>
            <p>
                <ul>
                    <li><a href="manageTemplates.php">Create a new Template</a></li>
                    <li><a href="templates.php">Templates List</a></li>
                    <li><a href="templateMover.php">Import/Export Templates</a></li>
                    <li><a href="metadata.php" >Device Information</a></li>
                </ul>
            </p>
        </div>

        <div class="module sideMenu">
            <strong>System</strong>
            <p>
            <ul>
                <li><a href="cables.php" >Cable Types</a></li>
                <li><a href="owners.php" >Equipment Owners</a></li>
                <li><a href="management.php?action=accounts" >User Accounts</a></li>
                <li><a href="system.php" >System</a></li>
                <li><a href="logs.php" >Logs</a></li>
                <li><a href="templateMover.php" >Import / Export Templates</a></li>
            </ul>
            </p>
        </div>
     </div>
        </div>
<?php
	include "theme/".$theme."/base.php";
}


// Post the XML file and then display a table of available templates
elseif(isset($_POST) && isset($_GET['action']) && $_GET['action']=='importFromXML' && isset($_FILES['templateFile']['name']))
{
	// only allow xml
	$extension = end(explode(".", $_FILES['templateFile']['name']));
	if(strtolower($extension == "xml"))
	{
		// the filename looks valid so upload and move it to our working folder
		$fakeName=substr(md5(rand(0,100).mktime()),0,15)."_".date("Ymd");	
		move_uploaded_file($_FILES['templateFile']['tmp_name'], "uploads/template_exports/".$fakeName.".xml");
		$tmpFile="uploads/template_exports/".$fakeName.".xml";
		$templates=0;
		
		// if it made it to the working folder, upload was complete
		if(file_exists($tmpFile )) 
		{
			// if we've got valid XML open and try make an object, we should confirm the structure somewhere here.
			$xml = simplexml_load_file($tmpFile);
			if($xml)
			{
				// loop over each level and generate a template for each
				$templateCount=0;
				$templates=array();
				foreach($xml as $template)
				{
					$templates[$templateCount]['templateID']="{$template->templateID}";
					$templates[$templateCount]['modelName']="{$template->modelName}";
					$templates[$templateCount]['vendor']="{$template->vendor}";
					$templates[$templateCount]['deviceTypeID']="{$template->deviceTypeID}";
					$templates[$templateCount]['notes']="{$template->notes}";
					$templates[$templateCount]['RU']="{$template->RU}";
					$templates[$templateCount]['depth']="{$template->depth}";
					$templates[$templateCount]['PSUs']="{$template->PSUs}";
					$templates[$templateCount]['PSUWattage']="{$template->PSUWattage}";
					$templates[$templateCount]['peakPSUWattage']="{$template->peakPSUWattage}";
					$templates[$templateCount]['PSUs']="{$template->PSUs}";
					$templates[$templateCount]['averageBTU']="{$template->averageBTU}";
					$templates[$templateCount]['weight']="{$template->weight}";
					$templates[$templateCount]['copperPorts']="{$template->copperPorts}";
					$templates[$templateCount]['ILO']="{$template->ILO}";
					$templates[$templateCount]['frontPanelImage']="{$template->frontPanelImage}";
					$templateCount++;
				}
			}
			else
				exit("XML file uploaded but didn't seem valid");
		} 
		else 
			exit("unable to open the XML file for reading");
	}
	else
		exit("you didn't upload an XML file");
	
	
/* Import into an array complete, move on to display table of available templates
we take the filename and array of selected templateIDs from here to import in the next page */
	
	// if the imports worked we'll have some here
	if($templates)
	{
		// prepare an array of device types for use in the table later
		// use deviceTypeID as index
		$deviceTypes=array();
		$types=new deviceTypes;
		foreach($types->getAll() as $type)
			$deviceTypes[$type->deviceTypeID]=$type->name;
		
		$templateClass = new templates;
	
		include "theme/default/top.php";
	?>
<div id="main"> 
	<div id="left">
	    <div class="module" id="logs">
			<strong>Select Templates for Import</strong>
			<p>
			Please select all the templates you wish to import.<br/> <i>note:</i> Matches which appear to be duplicates of existing templates are automatically excluded. 
			This occurs when there is a vendor &amp; name match of items of the same type (eg. server or patch panel)
			</p>
			<p>
			<table width="100%" class="dataTable">
			<colgroup align="left" class="tblfirstRow"></colgroup>
			<thead>
                            <tr>
                                <th scope="col" align="center" style='padding-left:0px;'>
                                    <input type="checkbox" onclick="var checked=$('input:checkbox:enabled').attr('checked'); $('input:checkbox:enabled').attr('checked',checked);" />
                                </th>
                                <th scope="col" >Vendor</th>
                                <th scope="col" >Template Name</th>
                                <th scope="col" >DeviceType</th>
                                <th scope="col" >RU</th>
                                <th scope="col" >Copper Ports</th>
                                <th scope="col" >Background</th>
                                <th scope="col" >Already Exists</th>
                            </tr>
			</thead>
			<form method="post" action="?action=confirmedXMLImport" >
			<input type='hidden' name='importData' value='<?php echo $fakeName;?>' />
			<tbody>
			<?php
			$usableTemplates=0;
			foreach($templates as $template)
			{ 
				// determine if a duplicate mode has been found
				$duplicate = $templateClass->quickSearch($template['vendor'],$template['modelName'],$template['deviceTypeID']);
				
				/* style the table row differently if it already exists
				   the checkbox is also disabled to disable import,
				   as we don't want BG images with the same name (change this to ID?) */
				if($duplicate)
					echo "<tr class='duplicateRow' >";
				else
				{
					$usableTemplates++;
					echo "<tr>";
				}
			?>
				<td align="center"><input onclick='$(this).closest("tr").toggleClass("selected");' type="checkbox" name="<?php echo $template['templateID'];?>" <?if($duplicate) { echo 'disabled'; }?>/></td>
				<td><?php echo $template['vendor'];?></td>
				<td><?php echo $template['modelName'];?></td>
				<td><?php echo $deviceTypes[$template['deviceTypeID']];?></td>
				<td><?php echo $template['RU'];?></td>
				<td><?php echo $template['copperPorts'];?></td>
				<td align='center'><?php if($template['frontPanelImage']) echo "<img src='images/list_ok.png' alt='BG Provided' />"; ?></td>
				<td align='center'><?php if($duplicate) echo "<img src='images/list_ok.png' alt='BG Provided' />"; ?></td>
			</tr>
			<?php }?>
			</tbody>
			</table>
		</p>
		<input type="submit" id="submit"  name="submit"  value="Start Importing" <?php if(!$templates || $usableTemplates==0) { echo 'disabled'; }?>/> <input type='button' value='Cancel' onclick="window.location='templateMover.php'"/>
		</form>
		</div>
	</div>
    <div id="right">
        <div class="module sideMenu">
            <strong>Devices &amp; Templates</strong>
            <p>
                <ul>
                    <li><a href="manageTemplates.php">Create a new Template</a></li>
                    <li><a href="templates.php">Templates List</a></li>
                    <li><a href="templateMover.php">Import/Export Templates</a></li>
                    <li><a href="metadata.php" >Device Information</a></li>
                </ul>
            </p>
        </div>

        <div class="module sideMenu">
            <strong>System</strong>
            <p>
            <ul>
                <li><a href="cables.php" >Cable Types</a></li>
                <li><a href="owners.php" >Equipment Owners</a></li>
                <li><a href="management.php?action=accounts" >User Accounts</a></li>
                <li><a href="system.php" >System</a></li>
                <li><a href="logs.php" >Logs</a></li>
                <li><a href="templateMover.php" >Import / Export Templates</a></li>
            </ul>
            </p>
        </div>
    </div>
</div>
	<?php
		include "theme/default/base.php";
	}
	
}


/* We've submitted the form and should have a post with the name of the imported file and
an array of IDs we want to move into the database
basically need to double check the objects are valid, create a template object and then insert */

else if(isset($_POST) && isset($_GET['action']) && $_GET['action']=='confirmedXMLImport')
{
	// check that the file ID is valid so users arn't opening any old thing
	if(!preg_match("/[a-z0-9]{10}_[0-9]{8}/", $_POST['importData']))
		exit("The file you were looking for isn't valid, please report this to the Racksmith Forum");
		
	$filename="uploads/template_exports/".$_POST['importData'].".xml";
	
	// generate an array of IDs we would want to import
	$importIDs=array();
	foreach($_POST as $key=>$val)
	{
		if(is_int($key) && $val=='on')
			$importIDs[]=$key;
	}
	
	// if it made it to the working folder, upload was complete
	if(file_exists($filename)) 
	{
		// if we've got valid XML open and try make an object, we should confirm the structure somewhere here.
		$xml = simplexml_load_file($filename);
		if($xml)
		{
			// loop over each level and generate a template for each
			$templateCount=0;
			$templates=new templates;
			$createdDevices=array();
			foreach($xml as $xmlEntry)
			{
				if(in_array("{$xmlEntry->templateID}",$importIDs))
				{
					$newTemplate = new template();
					$newTemplate->modelName = "{$xmlEntry->modelName}";
					$newTemplate->display = 1;
					$newTemplate->vendor = "{$xmlEntry->vendor}";
					$newTemplate->deviceTypeID = "{$xmlEntry->deviceTypeID}";
					$newTemplate->notes = "{$xmlEntry->notes}";
					$newTemplate->RU = "{$xmlEntry->RU}";
					$newTemplate->depth = "{$xmlEntry->depth}";
					$newTemplate->PSUs = "{$xmlEntry->PSUs}";
					$newTemplate->PSUWattage = "{$xmlEntry->PSUWattage}";
					$newTemplate->peakPSUWattage = "{$xmlEntry->peakPSUWattage}";
					$newTemplate->averageBTU = "{$xmlEntry->averageBTU}";
					$newTemplate->weight = "{$xmlEntry->weight}";
					$newTemplate->copperPorts = "{$xmlEntry->copperPorts}";
					$newTemplate->ILO = "{$xmlEntry->ILO}";
					$newTemplate->frontPanelImage = "{$xmlEntry->frontPanelImage}";
					if($templates->insert($newTemplate))
					{
						if($newTemplate->frontPanelImage)
						{
							$acceptedExtensions=array('gif','jpg','jpeg','png');
							$extension = strtolower(end(explode(".", $newTemplate->frontPanelImage)));
							if(in_array($extension,$acceptedExtensions))
							{
								if($handle = fopen($newTemplate->frontPanelImage, "wb"))
								{
									if (fwrite($handle, base64_decode("{$xmlEntry->imagedata}"))!=FALSE)
									{
									
									
									}
									else
										exit("Cannot write to file: ".$newTemplate->frontPanelImage);
								}
								else
									exit("Cannot open file to write");
							}
						}
						$createdDevices[]=$newTemplate->vendor.$newTemplate->modelName;
					}
				}
			}
			if(count($createdDevices) > 0)
			{
				// Log this import even if >=1 submitted
				$log=new log;
				$log->event = "Imported ".count($createdDevices)." new device template/s";
				$log->eventType="import_templates";
				$log->itemID=0;
				$logs = new logs();
				$logs->insert($log);
			}
			header("Location: ".$_SERVER['PHP_SELF']."?added=".serialize($createdDevices));
		}
		else
			exit("XML file uploaded but didn't seem valid");
	} 

}



/* We've submitted the form and should have a post with the name of the imported file and
an array of IDs we want to move into the database
basically need to double check the objects are valid, create a template object and then insert */

else if(isset($_POST) && isset($_GET['action']) && $_GET['action']=='exportToXML')
{
    // generate an array of IDs we would want to import
    $exportIDs=array();
    foreach($_POST as $key=>$val)
        if(is_int($key) && $val=='on')
            $exportIDs[]=$key;

   header('Content-Type: application/xml');
   header('Content-Disposition: attachment; filename="racksmith-templates-'.date('Ymd').'.xml"');
	
    echo "<?xml version='1.0' encoding='UTF-8' ?>
    <templates>\n";

    // Loop over each of the templates
    $templateClass = new templates;
    $templates=$templateClass->getAll();

    if($templates)
    foreach($templates as $templateVal)
    {
        if(!in_array($templateVal->templateID,$exportIDs))
            continue;
	?>
        <template id='<?php echo $templateVal->templateID;?>'>
<?php
        foreach($templateVal as $key=>$value)
        {
            if(is_object($key) || is_object($value))
                continue;

            if($key=='frontPanelImage' && file_exists($value))
            {
                echo "		<imagedata>";
                $handle = fopen($value,'rb');
                $file_content = fread($handle,filesize($value));
                fclose($handle);
                echo chunk_split(base64_encode($file_content));
                echo "		</imagedata>\n";
            }
            elseif($key=="ports")
            {
                if($value)
                {
                    echo "<ports>";
                    foreach($value as $port)
                    {
                        echo '          <port id="'.$port['tempID'].'">
                            <cableTypeID>'.$port['cableTypeID'].'</cableTypeID>
                            <count>'.$port['count'].'</count>
                            <isPower>'.$port['isPower'].'</isPower>
                            </port>
                        ';
                    }
                    echo "</ports>";
                }
            }
            else
                echo "		<$key>$value</$key>\n";
        }

    echo "	</template>\n\n";
    }
	echo "</templates>";
}
?>