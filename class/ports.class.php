<?php
class ports
{
	var $db;
	var $rows=array();

	function __construct()
	{
		global $db;
		$this->db=$db;
	}

	function getByDevice($deviceID)
	{
            if (count($this->rows) <= 0)
            {
                $query = $this->db->prepare('SELECT * FROM ports WHERE deviceID = ? ORDER BY `disporder` ASC;');
                $query->execute(array($deviceID));
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $newPort)
                {
                    $port = new port;
                    $port->portID = $newPort['portID'];
                    $port->deviceID = $newPort['deviceID'];
                    $port->vlan = $newPort['vlan'];
                    $port->cableTypeID = $newPort['cableTypeID'];
                    $port->ipAddress = $newPort['ipAddress'];
                    $port->macAddress = $newPort['macAddress'];
                    $port->bandwidth = $newPort['bandwidth'];
                    $port->label = $newPort['label'];
                    $port->cableID = $newPort['cableID'];
                    $port->disporder = $newPort['disporder'];
                    $port->joinID = $newPort['joinID'];

                    $device = new device($port->deviceID);

                    $port->deviceName = $device->name;
                    if ($port->cableID > 0)
                    {
                        $query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? AND portID <> ?;');
                        $query->execute(array($port->cableID,$port->portID));
                        $result2 = $query->fetchAll();

                        foreach($result2 as $attachedPort)
                        {
                            $port->connectedToPortID = $attachedPort['portID'];
                            $port->connectedToPortName = $attachedPort['label'];
                            $port->connectedToDeviceID = $attachedPort['deviceID'];

                            $device = new device($port->connectedToDeviceID);
                            $port->connectedToDeviceName = $device->name;
                        }
                    }
                    array_push($return, $port);
                }
                    return $return;
            }
            else
                return $this->rows;
	}
	
	function getByType($deviceID,$cableTypeID)
	{
		if (count($this->rows) <= 0)
		{
			$query = $this->db->prepare('SELECT * FROM ports WHERE deviceID = ? AND cableTypeID = ? ORDER BY `disporder` ASC;');
			$query->execute(array($deviceID,$cableTypeID));
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $newPort)
			{
				$port = new port;
				$port->portID = $newPort['portID'];
				$port->deviceID = $newPort['deviceID'];
				$port->vlan = $newPort['vlan'];
				$port->cableTypeID = $newPort['cableTypeID'];
				$port->ipAddress = $newPort['ipAddress'];
				$port->macAddress = $newPort['macAddress'];
				$port->bandwidth = $newPort['bandwidth'];
				$port->label = $newPort['label'];
				$port->cableID = $newPort['cableID'];
				$port->disporder = $newPort['disporder'];
				$port->joinID = $newPort['joinID'];
				
				$device = new device($port->deviceID);
				
				$port->deviceName = $device->name;
				if ($port->cableID > 0)
				{
					$query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? AND portID <> ? ORDER BY `disporder` ASC;');
					$query->execute(array($port->cableID,$port->portID));
					$result2 = $query->fetchAll();
					
					foreach($result2 as $attachedPort)
					{
						$port->connectedToPortID = $attachedPort['portID'];
						$port->connectedToPortName = $attachedPort['label'];
						$port->connectedToDeviceID = $attachedPort['deviceID'];
						
						$device = new device($port->connectedToDeviceID);
						$port->connectedToDeviceName = $device->name;
					}
				}
				array_push($return, $port);
			}
			return $return;	
		}
		else
			return $this->rows;
	}
	
	
	function getByCableID($cableID)
	{
		if (count($this->rows) <= 0)
		{
			$query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? ORDER BY `disporder` ASC;');
			$query->execute(array($cableID));
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $newPort)
			{
				$port = new port;
				$port->portID = $newPort['portID'];
				$port->deviceID = $newPort['deviceID'];
				$port->vlan = $newPort['vlan'];
				$port->cableTypeID = $newPort['cableTypeID'];
				$port->ipAddress = $newPort['ipAddress'];
				$port->macAddress = $newPort['macAddress'];
				$port->bandwidth = $newPort['bandwidth'];
				$port->label = $newPort['label'];
				$port->cableID = $newPort['cableID'];
				$port->disporder = $newPort['disporder'];
				$port->joinID = $newPort['joinID'];
				
				$device = new device($port->deviceID);
				
				$port->deviceName = $device->name;
				if ($port->cableID > 0)
				{
					$query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? AND portID <> ?;');
					$query->execute(array($port->cableID,$port->portID));
					$result2 = $query->fetchAll();
					
					foreach($result2 as $attachedPort)
					{
						$port->connectedToPortID = $attachedPort['portID'];
						$port->connectedToPortName = $attachedPort['label'];
						$port->connectedToDeviceID = $attachedPort['deviceID'];
						
						$device = new device($port->connectedToDeviceID);
						$port->connectedToDeviceName = $device->name;
					}
				}
				array_push($return, $port);
			}
			return $return;	
		}
		else
			return $this->rows;
	}
	
	
	
	
	function insert($port)
	{
            $status=array();
            $this->db->prepare("INSERT INTO ports (deviceID,vlan,cableTypeID,ipAddress,macAddress,bandwidth,label,cableID,disporder,joinID) VALUES (?,?,?,?,?,?,?,?,?,?);");

            if(is_array($port))
                foreach($port as $create)
                    $status[] = $this->db->execute(array($create->deviceID,$create->vlan,$create->cableTypeID,$create->ipAddress,$create->macAddress,$create->bandwidth,$create->label,$create->cableID,$create->disporder,$create->joinID));
            else
                $status[] = $this->db->execute(array($port->deviceID,$port->vlan,$port->cableTypeID,$port->ipAddress,$port->macAddress,$port->bandwidth,$port->label,$port->cableID,$port->disporder,$port->joinID));

            $this->db->query("SELECT LAST_INSERT_ID()");
            $result = $this->db->fetchAll();

            if(!in_array(0,$status))
                return $result[0]['LAST_INSERT_ID()'];
            else
                return $created;
	}

	function update($port)
	{
		$this->db->prepare("UPDATE ports SET deviceID = ?,vlan = ?,cableTypeID = ?,ipAddress = ?,macAddress = ?,bandwidth = ?,label = ?,cableID = ?,disporder= ?,joinID = ? WHERE portID = ?");
		return $this->db->execute(array($port->deviceID,$port->vlan,$port->cableTypeID,$port->ipAddress,$port->macAddress,$port->bandwidth,$port->label,$port->cableID,$port->disporder,$port->joinID,$port->portID));
	}

	function delete($port,$deleteCables=1,$deletePort=1)
	{
		if(is_numeric($port))
			$portID=$port;
		else
			$portID=$port->portID;
			
		if($deleteCables)
		{
			$cablesClass = new cables;
			$this->db->prepare("SELECT cableID from ports WHERE portID = ?;");
			$this->db->execute(array($portID));
                        $cables = $this->db->fetchAll();
			foreach($cables as $cableID)
			{
                            	$this->db->prepare("SELECT portID from ports WHERE cableID = ?;");
                                $this->db->execute(array($cableID['cableID']));
                                foreach($this->db->fetchAll() as $delPortID)
                                {
                                    $this->db->prepare("UPDATE joins set primPort=0 WHERE primPort=?;");
                                    $this->db->execute(array($delPortID['portID']));

                                    $this->db->prepare("UPDATE joins set secPort=0 WHERE secPort=?;");
                                    $this->db->execute(array($delPortID['portID']));
                                    
                                    $this->db->prepare("DELETE FROM ports WHERE cableID=? AND joinID!=0");
                                    $this->db->execute(array($cableID['cableID']));

                                    $this->db->prepare("UPDATE ports SET cableID=0 WHERE cableID=?");
                                    $this->db->execute(array($cableID['cableID']));
                                }
                                
				// delete the cable and remove any reference to it in the ports
				$cablesClass->delete($cableID['cableID']);

			}
		}
		
		if($deletePort)
		{
			$this->db->prepare("DELETE FROM ports WHERE portID = ?;");
			$this->db->execute(array($portID));			
		}
                return 1;
	}

        // check to see if any other free joins ports next to the current one
        // if available the user can then select to flood these ports
        function checkFlood($portID,$existingCable=0)
        {
            // search for all ports in this device with a disporder > then current port
            $start = new port($portID);
            $query = $this->db->prepare("select portID,disporder,cableID from ports WHERE deviceID=? AND disporder > ? ORDER BY disporder ASC;");
            $query->execute(array($start->deviceID,$start->disporder));

            // count up from the current port and ensure the disporders are sequential
            $floodableID=array();
            $cableID=array();
            $nextPorts=$query->fetchAll(PDO::FETCH_ASSOC);
            foreach($nextPorts as $l)
            {
                // generate a list of portIDs that are in sequence
                if($l['disporder'] == ($start->disporder+(count($floodableID)+1)))
                {
                    $floodableID[]=$l['portID'];
                    $cableID[]=$l['cableID'];
                }
            }

            // if we are looking at an existing cable
            // check the following ports to see if it is a flood
            if($existingCable)
            {
                $query="";
                $results="";

                // add  the first port / cable into the query
                // we use this to detmine if following cables have the same endpoint
                $cableID[]=$start->cableID;
                $floodableID[]=$start->portID;

                // select the portIDs where any of our cables exist but make sure they are the remote ports
                $query = $this->db->prepare("SELECT portID,deviceID,disporder,cableID from ports WHERE cableID IN ('".join("','",$cableID)."') AND portID NOT IN ('".join("','",$floodableID)."') ORDER BY disporder ASC;");
                $query->execute(array());

                // now we have a list of remote ports in order by disporder
                $results = $query->fetchAll(PDO::FETCH_ASSOC);

                $delIDs=array();        // portIDs that we will delete
                $remoteDeviceID=0;      // deviceID of first connected port (one we clicked on)
                foreach($results as $l)
                {
                    // as disporder can vary between devices we need to determine the end device of the first port
                    // to do this find a match on the cableID
                    if($l['cableID']==$start->cableID)
                    {
                       $remoteDeviceID=$l['deviceID'];
                       $originalDisp=$l['disporder'];
                       
                       // return the ID of the first port, this only occurs when existingCable is set
                       $delIDs[]=$l['portID'];
                    }

                    // find devices which match the end device and are in sequence
                    if($remoteDeviceID==$l['deviceID'] && $l['disporder']==$originalDisp+(count($delIDs)))
                        $delIDs[]=$l['portID'];
                }
                // overwrite the previous return value with the portIDs we can delete
                $floodableID=$delIDs;
            }
            
            return $floodableID;
        }

        function createForm($templateID)
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
                $("#deviceInsertPort").live('click',function()
                {
                    var portType = $("#deviceNetwork #newPort select[name=portType]").attr("value");
                    var portTypeName = $("#deviceNetwork #newPort select[name=portType] :selected").text();
                    var IPAddress = $("#deviceNetwork #newPort :input[name=IPAddress]").attr("value");
                    if(IPAddress == "IP Address")
                            IPAddress ='';
                    var MACAddress = $("#deviceNetwork #newPort :input[name=MACAddress]").attr("value");
                    if(MACAddress == "MAC Address")
                            MACAddress='';
                    var bandwidth = $("#deviceNetwork #newPort select[name=bandwidth]").attr("value");
                    var bandwidthText = $("#deviceNetwork #newPort select[name=bandwidth] :selected").text();
                    var label = $("#deviceNetwork #newPort :input[name=label]").attr("value");

                    $("#deviceNetwork tbody").append('<tr><td><input type="hidden" name="portID[]" value="0" /><input type="hidden" name="portType[]" value="' + portType + '" />' + portTypeName +
                    '</td><td><input type="text" name="ipAddr[]" value="' + IPAddress + '" />' +
                    '</td><td><input type="text" name="macAddr[]" value="' + MACAddress + '" />' +
                    '</td><td><input type="hidden" name="bandwidth[]" value="' + bandwidth + '" />' + bandwidth + 
                    '</td><td><input type="text" name="label[]" value="' + label + '" />' +
                    '<td><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" >delete</a></td></tr>');

                    // generate the label for the next port by counting the rows in the tbody containing existing ports
                    // we don't need to +1 this value as the ilom row counts as the next item
                    var nextPortID = $('#deviceNetwork tbody tr').length+1;
                    $("#deviceNetwork #newPort :input[name=label]").val(nextPortID);
                    $("#deviceNetwork :input[name=disporder]").val(nextPortID);
                    return false;
                });
            });
        </script>
        <?php
        $templatePorts = $this->getTemplatePorts($templateID);
        $nextPortLabel=1;
        foreach($templatePorts as $port)
            $nextPortLabel+=$port['count'];
        ?>
	<div id="deviceNetwork" >
	<table width="80%" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead id="newPort">
			<tr>
			<td><span id='existingPorts' style='display:none;'>0</span><select name="portType" >
                            <?php
					// List all the port types available
				$cableTypeClass = new cableTypes();
                                $cableTypes = $cableTypeClass->getCategories();
				foreach($cableTypes as $cableCategory)
				{
                                    if($cableCategory['type']!=2 && $cableCategory['enabled']==1)
                                    { ?>
                                        <option value="<?php echo $cableCategory['categoryID'];?>" <?php if($cableCategory['categoryID']==1) { echo "SELECTED"; }?>><?php echo $cableCategory['name'];?></option>
				<?php  }
                            }?>
			</select></td>
			<td><input name="IPAddress" value="IP Address" style="font-style: italic;" /></td>
			<td><input name="MACAddress" value="MAC Address" style="font-style: italic;" /></td>
			<td><select name="bandwidth" style="font-style:italic;" >
				<option value="10" >10 Mbit/s</option>
				<option value="10/100" >10/100 Mbit/s</option>
				<option value="10/100/1000" SELECTED>10/100/1000 Mbit/s</option>
				<option value="10 Gbit/s" >10 Gbit/s</option>
				</select></td>
			<td><input name="label" value="<?php echo $nextPortLabel; ?>" style="font-style: italic;" /></td>
			<td align="right" colspan="2"><a href="#" id="deviceInsertPort" >Add Port</a></td></tr>
		</thead>
		<tbody>
                    <?php
                    $overallCount=1;
                        foreach($templatePorts as $port)
                        {
                            for($i=0;$i<$port['count'];$i++)
                            {
                                echo "<tr>
                                <td><select name='portType[]' >";
				foreach($cableTypes as $cableCategory)
				{
                                    if($cableCategory['type']!=2 && $cableCategory['enabled']==1)
                                    { ?>
                                        <option value="<?php echo $cableCategory['categoryID'];?>" <?php if($cableCategory['categoryID']==$port['portTypeID']) { echo "SELECTED"; }?>><?php echo $cableCategory['name'];?></option>
				<?php  }
				}
                                echo "</select></td>
                                <td><input type='text' name='ipaddress[]' /></td>
                                <td><input type='text' name='macaddress[]' /></td>
                                <td><select name='bandwidth[]' style='font-style:italic;' >
                                    <option value='10' "; if($port['bandwidth']=="10") { echo "SELECTED"; } echo ">10 Mbit/s</option>
                                    <option value='10/100' "; if($port['bandwidth']=="10/100") { echo "SELECTED"; } echo ">10/100 Mbit/s</option>
                                    <option value='10/100/1000' "; if($port['bandwidth']=="10/100/1000") { echo "SELECTED"; } echo ">10/100/1000 Mbit/s</option>
                                    <option value='10 Gbit/s' "; if($port['bandwidth']=="10 Gbit/s") { echo "SELECTED"; } echo ">10 Gbit/s</option>
                                    </select></td>
                                <td><input type='text' name='label[]' value='".$overallCount."'/></td>
                                    <td><a href='#' onclick='$(this).closest(\"tr\").remove();return false;' >Delete</a></td>
                                </tr>";
                                    $overallCount++;
                            }
                            
                        }
                    ?>
		</tbody>
	</table>
	</div>
        <?php
        }



        function createPorts($deviceID,$postData)
        {
            /* : post data comes in from the form
             * portType[]
             * bandwidth[]
             * ipaddress[]
             * macaddress[]
             * label[]
             * assumed all the indexes match up
             */
            $toCreate=array();
            if(isset($postData['portType']) && is_array($postData['portType']))
            {
                foreach($postData['portType'] as $key=>$portType)
                {
                    $port = new port;
                    $port->deviceID=$deviceID;
                    $port->cableTypeID=$portType;
                    $port->ipAddress=$postData['ipaddress'][$key];
                    $port->bandwidth=$postData['bandwidth'][$key];
                    $port->label=$postData['label'][$key];
                    $port->macAddress=$postData['macaddress'][$key];
                    $port->disporder=$key;
                    array_push($toCreate, $port);
                }
            }
            if($this->insert($toCreate))
                return 1;
            else
                return 0;
        }
        
        function templateCreateForm()
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
                $("#templateAddPort").live('click',function()
                {
                    var portType = $("#deviceNetwork thead select[name=portType]").attr("value");
                    var portTypeName = $("#deviceNetwork thead select[name=portType] :selected").text();
                    var bandwidth = $("#deviceNetwork thead select[name=bandwidth]").attr("value");
                    var bandwidthText = $("#deviceNetwork thead select[name=bandwidth] :selected").text();
                    var count = parseInt($("#deviceNetwork thead input[name=count]").val());
                    if($('#templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+' td').length!=0)
                    {
                        var existingCount=parseInt($('#templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+' td input[name=count[]]').val());
                        var newCount=existingCount+count;
                       $('#templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+' td.count').html('<input type="hidden" name="count[]" value="' + newCount + '" />' + newCount );
                    }
                    else
                    {
                        $("#deviceNetwork tbody").append('<tr id="templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+'"><td><input type="hidden" name="portType[]" value="' + portType + '" />' + portTypeName +
                        '</td><td><input type="hidden" name="bandwidth[]" value="' + bandwidth + '" />' + bandwidthText +
                        '</td><td class="count"><input type="hidden" name="count[]" value="' + count + '" />' + count +
                        '<td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>');
                    }
                    return false;
                });
                
                $("#deviceNetwork tbody").sortable({
                    items: "tr",
                    handle: "span.handle",
                    update : function ()
                    {

                    }
                });
            });
        </script>
	<div id="deviceNetwork" >
	<table width="100px" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
                    <tr><th>Port Type:</th><th>Bandwidth:</th><th>Count</th></tr>
                    <tr>
			<td><select name="portType" >
				<?php
					// List all the port types available
				$cableTypeClass = new cableTypes();
                                $cableTypes = $cableTypeClass->getCategories();
				foreach($cableTypes as $cableCategory)
				{
                                    if($cableCategory['type']!=2 && $cableCategory['enabled']==1)
                                    { ?>
                                        <option value="<?php echo $cableCategory['categoryID'];?>" <?php if($cableCategory['categoryID']==1) { echo "SELECTED"; }?>><?php echo $cableCategory['name'];?></option>
				<?php  }
				}?>
			</select></td>
			<td><select name="bandwidth" style="font-style:italic;" >
				<option value="10" >10 Mbit/s</option>
				<option value="10/100" >10/100 Mbit/s</option>
				<option value="10/100/1000" SELECTED>10/100/1000 Mbit/s</option>
				<option value="10 Gbit/s" >10 Gbit/s</option>
				</select></td>
			<td><input name="count" value="1" size="2" style="font-style: italic;" /></td>
			<td align="right" colspan="2"><a href="#" id="templateAddPort" >Add</a></td></tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
        <?php
        }

        function getTemplatePorts($templateID)
        {
            $query = $this->db->prepare('SELECT * FROM templateports WHERE templateID = ? AND isJoin=0 ORDER BY `disporder` ASC;');
            $query->execute(array($templateID));
            return $query->fetchAll();
        }

        function templateEditForm($templateID)
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
                $("#templateAddPort").live('click',function()
                {
                    var portType = $("#deviceNetwork thead select[name=portType]").attr("value");
                    var portTypeName = $("#deviceNetwork thead select[name=portType] :selected").text();
                    var bandwidth = $("#deviceNetwork thead select[name=bandwidth]").attr("value");
                    var bandwidthText = $("#deviceNetwork thead select[name=bandwidth] :selected").text();
                    var count = parseInt($("#deviceNetwork thead input[name=count]").val());
                    
                    if($('#templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+' td').length!=0)
                    {
                        var existingCount=parseInt($('#templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+' td input[name=count[]]').val());
                        var newCount=existingCount+count;
                       $('#templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+' td.count').html('<input type="hidden" name="count[]" value="' + newCount + '" />' + newCount );
                    }
                    else
                    {
                        $("#deviceNetwork tbody").append('<tr id="templatePortRow'+portType+bandwidth.replace(/[^a-zA-Z0-9]+/g,'')+'"><td><input type="hidden" name="portType[]" value="' + portType + '" />' + portTypeName +
                        '</td><td><input type="hidden" name="bandwidth[]" value="' + bandwidth + '" />' + bandwidthText +
                        '</td><td class="count"><input type="hidden" name="count[]" value="' + count + '" />' + count +
                        '<td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>');
                    }
                    return false;
                });

                $("#deviceNetwork tbody").sortable({
                    items: "tr",
                    handle: "span.handle",
                    update : function ()
                    {

                    }
                });
            });
        </script>
	<div id="deviceNetwork" >
	<table width="100px" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
                    <tr><th>Port Type:</th><th>Bandwidth:</th><th>Count</th></tr>
                    <tr>
			<td><select name="portType" >
				<?php
				$cableTypeClass = new cableTypes();
                                $cableTypes = array();
				foreach($cableTypeClass->getCategories() as $cableCategory)
				{
                                    $cableTypes[$cableCategory['categoryID']]=$cableCategory['name'];
                                    if($cableCategory['type']!=2 && $cableCategory['enabled']==1)
                                    { ?>
                                        <option value="<?php echo $cableCategory['categoryID'];?>" <?php if($cableCategory['categoryID']==1) { echo "SELECTED"; }?>><?php echo $cableCategory['name'];?></option>
				<?php  }
				}?>
			</select></td>
			<td><select name="bandwidth" style="font-style:italic;" >
				<option value="10" >10 Mbit/s</option>
				<option value="10/100" >10/100 Mbit/s</option>
				<option value="10/100/1000" SELECTED>10/100/1000 Mbit/s</option>
				<option value="10 Gbit/s" >10 Gbit/s</option>
				</select></td>
			<td><input name="count" value="1" size="2" style="font-style: italic;" /></td>
			<td align="right" colspan="2"><a href="#" id="templateAddPort" >Add</a></td></tr>
		</thead>
		<tbody>
                    <?php
                    foreach($this->getTemplatePorts($templateID) as $template)
                    {
                        if(!$template['isJoin'])
                        {
                            $cleanName=preg_replace('/[^a-zA-Z0-9]+/','',$template['bandwidth']);

                        echo '<tr id="templatePortRow'.$template['portTypeID'].$cleanName.'"><td><input type="hidden" name="portType[]" value="'.$template['portTypeID'].'" />'.$cableTypes    [$template['portTypeID']].'
                        </td><td><input type="hidden" name="bandwidth[]" value="'.$template['bandwidth'].'" />'.$template['bandwidth'].'
                        </td><td class="count"><input type="hidden" name="count[]" value="'.$template['count'].'" />'.$template['count'].'
                        <td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>';
                        }
                    }
                    ?>
		</tbody>
	</table>
	</div>
        <?php
        }

        function templateEditPost($templateID,$postData)
        {

            $del=$this->db->prepare("DELETE from templateports WHERE templateID=? AND isJoin=0;");
            $del->execute(array($templateID));
            
            if(isset($_POST['portType']) && is_array($_POST['portType']))
            {
                $joinsToCreate=array();
                // loop over them and fill the array we pass in later
                foreach($_POST['portType'] as $key=>$item)
                {
                    $entry=array();
                    $entry['templateID']=$templateID;
                    $entry['portTypeID']=$_POST['portType'][$key];
                    $entry['isJoin']="0";
                    $entry['bandwidth']=$_POST['bandwidth'][$key];
                    $entry['count']=$_POST['count'][$key];
                    $entry['disporder']=($key+1);
                    $joinsToCreate[]=$entry;
                }
                $templates=new templates;
                if(!$templates->createTemplatePorts($joinsToCreate))
                    echo "Error creating template ports";
                else
                    return 1;
            }
            else
                return 1;
        }

        function showPorts($deviceID,$startID=0,$groupPorts=4)
        {
            echo "<div class='device' title='deviceID".$deviceID."' >";
            $ports = $this->getByDevice($deviceID);
            if(empty($ports))
                echo "<div class='desc' ><em>No configured Ports</em></div>";
            else
            {
                if($groupPorts==8)
                    $panelWidth='eightWide';
                else if($groupPorts==5)
                    $panelWidth='fiveWide';
                else
                    $panelWidth='fourWide';
                echo "<ul class='ports ".$panelWidth."' >";

                $count=0;
                foreach($ports as $key=>$port)
                {

                    if($count%$groupPorts==0 && $count!=0)
                        echo "<li class='split' ></li>";

                    echo "<li class='port ".$groupPorts."' id='portID".$port->portID."' >";
                    if($port->connectedToPortID >0)
                            echo "<a class='connected' onclick='launchCableMGR(".$port->portID.");manageConnection(2);'>";
                    elseif(isset($port->cableID) && $port->cableID > 0 && $device->deviceTypeID!=7)
                            echo "<a class='singleconnected' onclick='launchCableMGR(".$port->portID.");manageConnection(4);'>";
                    else
                            echo "<a class='disconnected' onclick='launchCableMGR(".$port->portID.");manageConnection(0);'>";
                    echo ($key+$startID);
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
                  echo '</a></li>';
                  $count++;
                }

            echo "</ul>";
            }
            echo "</div>";
        }

}



class port
{
	var $db;
	var $portID;
	var $deviceID;
	var $deviceName;
	var $vlan;
	var $cableTypeID;
	var $ipAddress;
	var $macAddress;
	var $bandwidth;
	var $label;
	var $cableID;
	var $disporder;
	var $joinID;
	
	var $connectedToPortID;
	var $connectedToPortName;
	var $connectedToDeviceID;
	var $connectedToDeviceName;
	function __construct($byPortID='0',$lookupPorts=1)
	{
            global $db;
            $this->db=$db;
            $this->portID = $byPortID;
            $this->deviceID = 0;
            $this->deviceName = "";
            $this->vlan = "";
            $this->cableTypeID = 0;
            $this->ipAddress = "";
            $this->macAddress = "";
            $this->bandwidth = "";
            $this->label = "";
            $this->cableID = 0;
            $this->disporder=0;
            $this->joinID = "";

            $this->connectedToPortID = 0;
            $this->connectedToPortName = "";
            $this->connectedToDeviceID = 0;
            $this->connectedToDeviceName = "";

            if (is_numeric($this->portID) && $this->portID > 0)
            {
                $query = $this->db->prepare('SELECT * FROM ports WHERE portID = ? ORDER BY `disporder` ASC;');
                $query->execute(array($this->portID));
                $result = $query->fetchAll();

                foreach($result as $port)
                {
                    $this->deviceID = $port['deviceID'];
                    $this->vlan = $port['vlan'];
                    $this->cableTypeID = $port['cableTypeID'];
                    $this->ipAddress = $port['ipAddress'];
                    $this->macAddress = $port['macAddress'];
                    $this->bandwidth = $port['bandwidth'];
                    $this->label = $port['label'];
                    $this->cableID = $port['cableID'];
                    $this->disporder = $port['disporder'];
                    $this->joinID = $port['joinID'];
                }

                $device = new device($this->deviceID);

                $this->deviceName = $device->name;

                if ($this->cableID > 0 && $lookupPorts)
                {
                    $query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? AND portID <> ?');
                    $query->execute(array($this->cableID,$this->portID));
                    $result = $query->fetchAll();

                    foreach($result as $port)
                    {
                        $this->connectedToPortID = $port['portID'];
                        $this->connectedToPortName = $port['label'];
                        $this->connectedToDeviceID = $port['deviceID'];

                        $device = new device($this->connectedToDeviceID);
                        $this->connectedToDeviceName = $device->name;
                    }
                }
            }
	}
	

};
?>