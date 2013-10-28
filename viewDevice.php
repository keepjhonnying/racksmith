<?php
session_start();
include "class/db.class.php";

if(isset($_GET['deviceID']) && !isset($_GET['action']) && is_numeric($_GET['deviceID']))
{
    // we need custom close links depdending if we're showing this in a modal or embedded
    if(isset($_GET['close']) && $_GET['close']=='min')
        $closeString='<a href="#" onclick="$(\'#deviceHolder\').hide();$(\'#deviceMiniMap\').hide();$(\'#deviceManagement\').hide();$(\'#deviceSearchResults\').slideDown(500);" class="closeDOMWindow closeLink"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>';
    else
    $closeString='<a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>';

    $device = new device($_GET['deviceID']);

    // get the attributes for the device and some other details for use later
    $device->fillCategories(1,1);

    // get the owner of this device which we use later
    $owner = new owner($device->ownerID);
    
    $categoriesClass = new attrcategories;
    $listOfCategories=$categoriesClass->getAll();
    if(!$device)
    {
	?>
    <div <?php if(isset($_GET['close']) && $_GET['close']=="min") { echo "class='module' "; }?>id="viewDevice" >
        <strong>Error</strong>
        <?php echo $closeString;?>
        <br/><font color="red" >Unable to find the device you requested.<br/>Please refresh the page and try again.</font>
    </div>
    <?php
    exit();
    }
    ?>


    <div id="viewDevice" >
        <div class="sectionHeader" >
                <strong><?php echo $device->name; ?></strong>
                <?php echo $closeString; ?>
            </tr></table> 
        </div>
        <div class="categoryData" >

            <table class="formTable">
                <colgroup class="tblfirstRow"></colgroup>
                <tr><td align="right" width="150px">Hostname:</td><td width="200px;"><?php echo $device->name; ?></td>
                    <td rowspan="4" valign="top">
                    <?php
                    $emptyTraits=false;
                    foreach($device->categories as $key=>$cat)
                    {
                        if(empty($cat) && !($key==HAS_NETWORK_PORTS||$key==IS_PATCH))
                            $emptyTraits.= "<li>".$listOfCategories[$key]->name."</li>";
                    }
                    if($emptyTraits)
                        echo '<strong>Generic Traits</strong><ul>'.$emptyTraits.'</ul>';
                    ?>
                    </td></tr>
                <tr><td align="right">Model:</td><td><?php
                    if(isset($device->categories[1][12]))
                        echo $device->categories[1][12]->value." ";
                    
                    if(isset($device->categories[1][42]))
                        echo $device->categories[1][42]->value;
                ?></td></tr>
                <tr><td align="right">Owner:</td><td><?php echo $owner->name; ?></td></tr>
                <tr><td align="right">Parent:</td><td><?php 
                    if($device->parentName!="") 
                        echo $device->parentName; 
                    else
                        echo $device->parentType; 
                    ?></td></tr>
            </table>
<?php
        // Get all the attributes associated with this device
        $names = new attrnames;
        /*echo "<pre>";
        print_r($device->categories);
        echo "</pre>";*/
        foreach($listOfCategories as $category)
        {
            // if cat does exist, isn't network related and is empty we want to skip it.
            if(empty($device->categories[$category->attrcategoryid]) && !($category->attrcategoryid==HAS_NETWORK_PORTS||$category->attrcategoryid==IS_PATCH))
                continue;
            
            // check to see if the device has this category
            // if not we want to continue to the next
            if(isset($device->categories[$category->attrcategoryid]))
                echo "<div id='category".$category->attrcategoryid."' >";
            else
                continue;
            ?>
              <strong style="display:block;padding:5px;"><?php echo $category->name; ?></strong>
              <table class="formTable" cellpadding="5">
                  <colgroup class="tblfirstRow"></colgroup>
            <?php
            // pickup the custom forms for ports and patches
            switch($category->attrcategoryid)
            {
                case HAS_NETWORK_PORTS:
                echo "<tr><td colspan='2'>";
                $portsClass = new ports;
                $portsClass->showPorts($device->deviceID);
                echo "</td></tr>";
                $attributesfound = true;
                break;

                case IS_PATCH:
                echo "<tr><td colspan='2'><strong>Patch Ports:</strong>";
                $joins = new joins;
                $joins->showPatchFull($device->deviceID);
                echo "</td></tr>";
                $attributesfound = true;
                break;
            }


            // we now have 2 lists of categories
            // list of categories contains everything
            // template->category[<catID>][<nameID>]-> contains registered values
            foreach($names->getByParent($category->attrcategoryid, 'attrcategory') as $name)
            {
                
                $itemName="";
                $itemValue="";
                
                // display the title, hide it if the internal list of values doesnt exist (value isnt set)
                if(isset($device->categories[$category->attrcategoryid]) && is_array($device->categories[$category->attrcategoryid]))
                {
                    $itemName="name".$name->attrnameid;
                    // as the item may be from a template and now actually associated with this device
                    // we need to check it has a value before we go ahead to set it
                    if(isset($device->categories[$category->attrcategoryid][$name->attrnameid]->value))
                        $itemValue=$device->categories[$category->attrcategoryid][$name->attrnameid]->value;
                }

                echo "<tr><td width='150px' align='right'>".$name->name."</td><td>";
                // Values that already exist we name differently in the form
                // existing<attrvalueid> so we can simply update the existing record (or delete as needed)
                // new values all get named name<attrnameid>

                switch ($name->type)
                {
                    case "Textbox":
                    case "Date":
                    case "Text Area":
                    case "Checkbox":
                    case "Checkbox List":
                    case "Dropdown List":
                    case "Radio Buttons":
                        echo $itemValue;
                        break;
                    case "Number":
                        if($itemValue)
                            echo $itemValue." ".$name->units;
                        break;
                    case "File":
                        $hasFileUpload=1;
                        break;
                    case "Image":
                        $hasFileUpload=1;
                        break;
                }
                echo "</td></tr>";
            }
            echo "</table></div>";
        }
        echo "</div>";



/*
    echo "<table width='80%' ><tr><td> ";
	// Display options for a device / switch
	if($device->deviceTypeID != 7)
	{
		$ports = new ports;
		$ports = $ports->getByDevice($device->deviceID);
                if(count($ports) > 0)
                {
                        $i=0;
                        echo "<div class='patchPanel' ><ul class='devicePorts' >";
                        foreach($ports as $port)
                        {

                                echo "<li class='port'>";
                                if($port->connectedToPortID >0)
                                        echo "<a class='connected' >";
                                elseif(isset($port->cableID) && $port->cableID > 0 && $device->deviceTypeID!=7)
                                        echo "<a class='singleconnected' >";
                                else
                                        echo "<a class='disconnected' >";

                                echo ($i+1);
                                $i++;
                                if($port->connectedToPortID >0)
                                {
                                    echo "<strong style='display:none;'>";
                                    echo "Connected to ".$port->connectedToDeviceName." &raquo; ".$port->connectedToPortName;
                                    echo "</strong>";
                                }
                                elseif($port->cableID >0)
                                {
                                    echo "<strong style='display:none;'>";
                                    echo "Cable only connected at this end";
                                    echo "</strong>";
                                }
                              echo '</li>';
                        }
                    echo "</ul></div>";
                    echo "</td></tr>
                        <tr><td><div class='hoverMenu' ></div>";
                }
                else
                    echo "This device has no ports";
	}
	else
	{
		$joins = new joins;
		$joins = $joins->getByDevice($device->deviceID);
                if(count($joins) > 0)
                {
                        $i=0;
                        echo "<div class='patchPanel' ><ul class='devicePorts' >";
                        foreach($joins as $join)
                        {

                                echo "<li class='port'>";
                                if($join->primPort >0 && $join->secPort > 0)
                                        echo "<a class='connected' >";
                                elseif($join->primPort > 0)
                                        echo "<a class='singleconnected' >";
                                elseif($join->secPort > 0)
                                        echo "<a class='singleconnected' >";
                                else
                                        echo "<a class='disconnected' >";

                                echo ($i+1);
                                $i++;
                                if($join->primPort >0 && $join->secPort > 0)
                                {
                                    echo "<strong style='display:none;'>";
                                    echo "Front connected to ".$join->primConnectedToDeviceName." Port ".$join->primConnectedToPortName."<br/>";
                                    echo "Back connected to ".$join->secConnectedToDeviceName." Port ".$join->secConnectedToPortName;
                                    echo "</strong>";
                                    
                                }
                                else if($join->primPort >0)
                                {
                                    echo "<strong style='display:none;'>";
                                    echo "Front connected to ".$join->primConnectedToDeviceName." Port ".$join->primConnectedToPortName."<br/>";
                                    echo "</strong>";
                                }
                                else if($join->secPort >0)
                                {
                                    echo "<strong style='display:none;'>";
                                    echo "Back connected to ".$join->secConnectedToDeviceName." Port ".$join->secConnectedToPortName."<br/>";
                                    echo "</strong>";
                                }
                                echo '</li>';
                        }
                    echo "</ul></div>";
                    echo "</td></tr>
                        <tr><td><div class='hoverMenu' ></div>";
                }
                else
                    echo "This patch panel has no ports - system error?";
	}
        echo "</td></tr></table>";
echo "</div></td></tr></table>";*/
}
elseif(isset($_GET['action']) && $_GET['action']=="deviceMenu" && isset($_GET['deviceID']) && is_numeric($_GET['deviceID']))
{
    $device=new device($_GET['deviceID']);
    $device->fillCategories(1,1,GENERIC,HAS_NETWORK_PORTS,IS_PATCH);
    ?>
    <div class='popupMenu' style='float:left;'>
        <div class='title'>
            <table onclick="$('#deviceDetails').slideToggle('normal');$('#portsAttach').hide();">
                <thead><tr>
                    <th width='100%'><?php echo $device->name; ?></th>
                    <th onclick='$("#menus").fadeOut("fast",function() { $("#menus").html(""); });deviceMenu=false;'  >
                        <img src="images/icons/close_rack.gif" border="0" alt="Close">
                    </th>
                </tr></thead>
            </table>
        </div>

        <div class='desc' ><table><tr><td width='100%' valign='bottom'>
        <?php
            if(isset($device->categories[GENERIC][12]->value) || isset($device->categories[HAS_OS][28]->value))
            echo "<strong>Details</strong><br/>";
           if(isset($device->categories[GENERIC][12]) && isset($device->categories[GENERIC][12]->value))
                echo "Model: ".$device->categories[GENERIC][12]->value." ".$device->categories[GENERIC][42]->value;
           if(isset($device->categories[HAS_OS][28]) && isset($device->categories[HAS_OS][28]->value))
                echo "OS: ".$device->categories[HAS_OS][28]->value." ".$device->categories[HAS_OS][29]->value;


         echo "<br/><a href='#".$device->deviceID."' class='moreDeviceDetails' >View Configuration</a></td>
            <td align='right' valign='top'><a href='#".$device->deviceID."' class='editDevice' >[Edit]</a><br/>";
                //if(deviceInfo.uploads==1)
                //    menu += "<a href='#"+deviceID+"device' class='manageAttachments' >[Files]</a><br/>";
        echo "<a href='#".$device->deviceID."' class='deleteDevice' >[Delete]</a><br/>
            <a href='#".$device->deviceID."' class='upgradeDevice' >[Upgrade]</a></div><br/>
            </td></tr></table></div>";

        if(isset($device->categories[IS_PATCH]))
        {
            echo "<div class='subtitle' style='cursor:pointer;border-bottom:0px;' onclick=\"$('.patchPanel').slideToggle('fast');return false;\">
            <table><thead><tr><th align='left'>Patches</th></tr></thead></table></div>
            <div class='patchPanel' >";

            $joins = new joins;
            $joins->showPatchFull($device->deviceID,1,8);
            echo "</div>";
        }

        if(isset($device->categories[HAS_NETWORK_PORTS]))
        {
            echo "<div class='subtitle' style='cursor:pointer;border-bottom:0px;' onclick=\"$('.devicePorts').slideToggle('fast');return false;\">
            <table style='border-bottom:0px;'><thead><tr><th align='left'>Ports</th></tr></thead></table></div>
            <div class='devicePorts' >";

            $ports = new ports;
            $ports->showPorts($device->deviceID,0,5);
            echo "</div>";
        }
        ?>
        <div class='title subtitle devicePortSummary' id='hoverMenu' style='display:none;'></div>
        </div>
        <div id='cableConnectionMenu' class='popupMenu' style="display:none;">
            <div class='subtitle' >
            <table>
                <thead><tr>
                    <th width="100%" >Cable Management</strong></th>
                    <th align="right"><img onclick="$('#cableConnectionMenu').fadeOut('fast');" src='images/icons/slide_left.gif' border='0' alt='Close Cable Menu'></th>
                </tr></thead>    
            </table>
            </div>
            <div class='navMenu' >
                <table cellpadding="2" cellspacing="0">
                    <tr>
                        <td id="backText" style="border-right: 1px solid #000000;">&lt; Previous</td>
                        <td id="currentTitle">Minimap Name</td>
                    </tr>
                </table>
            </div>
            <div id='displayArea' class='popupMenu'>displayArea

            </div>
            
        </div>
<?php
}
?>