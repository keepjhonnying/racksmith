<?php
class joins
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
		$ports = new ports;
		if (count($this->rows) <= 0)
		{
			$query = $this->db->prepare('SELECT * FROM joins WHERE deviceID = ? ORDER BY disporder asc');
			$query->execute(array($deviceID));
			$result = $query->fetchAll();
			
			$return = array();
			foreach($result as $newJoin)
			{
                            /*
				$join = new join;
				$join->joinID = $newjoin['joinID'];
				$join->deviceID = $newjoin['deviceID'];
				$join->disporder = $newjoin['disporder'];
				$join->primPort = $newjoin['primPort'];
				$join->secPort = $newjoin['secPort'];
				$join->cableTypeID = $newjoin['cableTypeID'];
				
				$join->primConnectedToPortID = 0;
				$join->primConnectedToPortName= "";
				$join->primConnectedToDeviceID = 0;
				$join->secConnectedToPortID = 0;
				$join->secConnectedToPortName= "";
				$join->secConnectedToDeviceID = 0;

				if($join->primPort != 0)
				{
					$primPort = new port($join->primPort);
					$primCable = $primPort->cableID;
					
					$portsOnCable = $ports->getByCableID($primCable);
					foreach($portsOnCable as $port)
					{
						if($port->deviceID != $join->deviceID)
						{
							$join->primConnectedToPortID = $port->portID;
							$join->primConnectedToPortName = $port->label;
							$join->primConnectedToDeviceID = $port->deviceID;
							$dev = new device($port->deviceID);
							$join->primConnectedToDeviceName = $dev->systemName;
						}
					}
				}
				
				if($join->secPort != 0)
				{
					$secPort = new port($join->secPort);
					$secCable = $secPort->cableID;
					
					$portsOnCable = $ports->getByCableID($secCable);
					foreach($portsOnCable as $port)
					{
						if($port->deviceID != $join->deviceID)
						{
							$join->secConnectedToPortID = $port->portID;
							$join->secConnectedToPortName= $port->label;
							$join->secConnectedToDeviceID = $port->deviceID;
						}
					}
				}

                                */
                                $join = new join($newJoin['joinID']);
				array_push($return, $join);
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
			$query = $this->db->prepare('SELECT portID FROM ports WHERE deviceID = ? AND cableTypeID = ?');
			$query->execute(array($deviceID,$cableTypeID));
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $newPort)
			{
                            /*
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
				$port->joinID = $newPort['joinID'];
				
				$device = new device($port->deviceID);
				
				$port->deviceName = $device->systemName;
				if ($port->cableID > 0)
				{
					$query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? AND portID <> ?');
					$query->execute(array($port->cableID,$port->portID));
					$result2 = $query->fetchAll();
					
					foreach($result2 as $attachedPort)
					{
						$port->connectedToPortID = $attachedPort['portID'];
						$port->connectedToPortName = $attachedPort['label'];
						$port->connectedToDeviceID = $attachedPort['deviceID'];
						
						$device = new device($port->connectedToDeviceID);
						$port->connectedToDeviceName = $device->systemName;
					}
				}*/
                                $port = new port($newPort['portID']);
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
			$query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ?');
			$query->execute(array($cableID));
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $newPort)
			{
                            /*
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
				$port->joinID = $newPort['joinID'];
				
				$device = new device($port->deviceID);
				
				$port->deviceName = $device->systemName;
				if ($port->cableID > 0)
				{
					$query = $this->db->prepare('SELECT * FROM ports WHERE cableID = ? AND portID <> ?');
					$query->execute(array($port->cableID,$port->portID));
					$result2 = $query->fetchAll();
					
					foreach($result2 as $attachedPort)
					{
						$port->connectedToPortID = $attachedPort['portID'];
						$port->connectedToPortName = $attachedPort['label'];
						$port->connectedToDeviceID = $attachedPort['deviceID'];
						
						$device = new device($port->connectedToDeviceID);
						$port->connectedToDeviceName = $device->systemName;
					}
				}

                             */
                                $port = new port($newPort['portID']);
				array_push($return, $port);
			}
			return $return;	
		}
		else
			return $this->rows;
	}
	
	
	function insert($join)
	{
            $status=array();
            $this->db->prepare("INSERT INTO joins (deviceID,joinID,disporder,primPort,secPort,cableTypeID) VALUES (?,'',?,?,?,?);");
            if(is_array($join))
                foreach($join as $create)
                    $status[]=$this->db->execute(array($create->deviceID,$create->disporder,$create->primPort,$create->secPort,$create->cableTypeID));
            else
                $status[]=$this->db->execute(array($join->deviceID,$join->disporder,$join->primPort,$join->secPort,$join->cableTypeID));

            if(!in_array(0,$status))
                return 1;
            else
                return 0;
	}

	function update($join)
	{
		$this->db->prepare("UPDATE joins SET deviceID = ?,disporder=?,joinID = ?,primPort = ?,secPort = ?,cableTypeID=? WHERE joinID = ?");
		return $this->db->execute(array($join->deviceID,$join->disporder,$join->joinID,$join->primPort,$join->secPort,$join->cableTypeID,$join->joinID));
	}

	function delete($join,$deleteCables=1,$deleteJoin=1)
	{
		if(is_numeric($join))
			$joinID=$join;
		else
			$joinID=$join->joinID;
			
		if($deleteCables)
		{
			$ports = new ports;
			$cables = new cables;
			$this->db->prepare("SELECT primPort,secPort from joins WHERE joinID = ?;");
			$this->db->execute(array($joinID));
			foreach($this->db->fetchAll() as $port)
			{
				// delete the cable and remove any reference to it in the joins
				if($port['primPort'])
					$ports->delete($port['primPort'],$deleteCables);
				if($port['secPort'])
					$ports->delete($port['primPort'],$deleteCables);
			}
		}
		
		if($deleteJoin)
		{
			$this->db->prepare("DELETE FROM joins WHERE joinID = ?;");
			$this->db->execute(array($joinID));			
		}
	}

	function removePort($portID)
	{
            $this->db->prepare("UPDATE joins set primPort='0' WHERE primPort=?;");
            $this->db->execute(array($portIDID));

            $this->db->prepare("UPDATE joins set secPort='0' WHERE secPort=?;");
            $this->db->execute(array($portIDID));
	}

        // check to see if any other free joins exist next to the current one
        // if available the user can then select to flood  these ports
        function checkFlood($joinID,$joinFocus,$existingFlood=0)
        {
            // search for all ports in this device with a disporder > then current port
            // also take into account the focus of the patch (front/back)
            $start = new join($joinID);
            if($joinFocus=="prim")
                $query = $this->db->prepare("select joinID,secPort,disporder from joins WHERE deviceID=? AND disporder > ? AND primPort=0 ORDER BY disporder ASC;");
            else
                $query = $this->db->prepare("select joinID,primPort,disporder from joins WHERE deviceID=? AND disporder > ? AND secPort=0 ORDER BY disporder ASC;");
            
            $query->execute(array($start->deviceID,$start->disporder));

            // count up from the current port and ensure the disporders are sequential
            $floodableID=array();
            foreach($query->fetchAll() as $l)
            {
                if($l['disporder'] == $start->disporder+(count($floodableID)+1))
                    $floodableID[]="J".$l['joinID'];
            }

            if($existingFlood)
            {
                $ports = new ports;
                if($joinFocus=="sec")
                    $floodableID=$ports->checkFlood($start->secPort,1);
                else
                    $floodableID=$ports->checkFlood($start->primPort,1);
            }

            
            return $floodableID;
        }


        function getTemplateJoins($templateID)
        {
            $query = $this->db->prepare('SELECT * FROM templateports WHERE templateID = ? AND isJoin=1 ORDER BY `disporder` ASC;');
            $query->execute(array($templateID));
            return $query->fetchAll();
        }

        function templateCreateForm()
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
                $("#templateAddJoin").live('click',function()
                {
                    var portType = $("#deviceJoin thead select[name=joinPortType]").attr("value");
                    var portTypeName = $("#deviceJoin thead select[name=joinPortType] :selected").text();
                    var count = parseInt($("#deviceJoin thead input[name=joinCount]").val());

                    if($('#templateJoinRow'+portType+' td').length!=0)
                    {
                        var existingCount=parseInt($('#templateJoinRow'+portType+' td input[name=joinCount[]]').val());
                        var newCount=existingCount+count;
                       $('#templateJoinRow'+portType+' td.count').html('<input type="hidden" name="joinCount[]" value="' + newCount + '" />' + newCount );
                    }
                    else
                    {
                        $("#deviceJoin tbody").append('<tr id="templateJoinRow'+portType+'"><td><input type="hidden" name="joinPortType[]" value="' + portType + '" />' + portTypeName +
                        '</td><td class="count"><input type="hidden" name="joinCount[]" value="' + count + '" />' + count +
                        '<td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>');
                    }
                    return false;
                });

                $("#deviceJoin tbody").sortable({
                    items: "tr",
                    handle: "span.handle",
                    update : function ()
                    {

                    }
                });
            });
        </script>
	<div id="deviceJoin" >
	<table width="100px" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
                    <tr><th width="250px;">Port Type:</th><th width="100px" colspan="2">Count</th></tr>
                    <tr>
			<td><select name="joinPortType" >
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
			<td width="100px"><input name="joinCount" value="1" size="2" style="font-style: italic;" /></td>
			<td align="left" colspan="2"><a href="#" id="templateAddJoin" >Add</a></td></tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
        <?php
        }

        function createForm($templateID)
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
                $("#templateAddJoin").live('click',function()
                {
                    var portType = $("#deviceJoin thead select[name=joinPortType]").attr("value");
                    var portTypeName = $("#deviceJoin thead select[name=joinPortType] :selected").text();
                    var count = parseInt($("#deviceJoin thead input[name=joinCount]").val());

                    if($('#templateJoinRow'+portType+' td').length!=0)
                    {
                        var existingCount=parseInt($('#templateJoinRow'+portType+' td input[name=joinCount[]]').val());
                        var newCount=existingCount+count;
                       $('#templateJoinRow'+portType+' td.count').html('<input type="hidden" name="joinCount[]" value="' + newCount + '" />' + newCount );
                    }
                    else
                    {
                        $("#deviceJoin tbody").append('<tr id="templateJoinRow'+portType+'"><td><input type="hidden" name="joinPortType[]" value="' + portType + '" />' + portTypeName +
                        '</td><td class="count"><input type="hidden" name="joinCount[]" value="' + count + '" />' + count +
                        '<td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>');
                    }
                    return false;
                });

                $("#deviceJoin tbody").sortable({
                    items: "tr",
                    handle: "span.handle",
                    update : function ()
                    {

                    }
                });
            });
        </script>
	<div id="deviceJoin" >
	<table width="100px" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
                    <tr><th>Port Type:</th><th>Count</th></tr>
                    <tr>
			<td><select name="joinPortType" >
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
			<td><input name="joinCount" value="1" size="2" style="font-style: italic;" /></td>
			<td align="right" colspan="2"><a href="#" id="templateAddJoin" >Add</a></td></tr>
		</thead>
		<tbody>
                    <?php
                     $templateJoins = $this->getTemplateJoins($templateID);
                        foreach($templateJoins as $join)
                        {
				foreach($cableTypes as $cableCategory)
				{
                                    if($cableCategory['type']!=2 && $cableCategory['enabled']==1)
                                    { ?>
                                        <?php
                                        if($cableCategory['categoryID']==$join['portTypeID']) {
                                            echo '<tr id="templateJoinRow'.$cableCategory['type'].'"><td><input type="hidden" name="joinPortType[]" value="'.$cableCategory['type'].'" />'.$cableCategory['name'].
                                            '</td><td class="count"><input type="hidden" name="joinCount[]" value="'.$join['count'].'" />'.$join['count'].
                                        '<td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>';


                                            }?>
				<?php  }
				}
                        }
                    ?>
		</tbody>
	</table>
	</div>
        <?php
        }

        function createJoins($deviceID,$postData)
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
            if(isset($postData['joinPortType']) && is_array($postData['joinPortType']))
            {
                foreach($postData['joinPortType'] as $key=>$portType)
                {
                    for($i=1;$i<=$postData['joinCount'][$key];$i++)
                    {
                        $port = new join;
                        $port->deviceID=$deviceID;
                        $port->cableTypeID=$portType;
                        $port->ipAddress='';
                        $port->bandwidth='';
                        $port->label=$i;
                        $port->macAddress='';
                        $port->disporder=$key;
                        array_push($toCreate, $port);
                    }
                }
            }
            if($this->insert($toCreate))
                return 1;
            else
                return 0;
        }

      function templateEditForm($templateID)
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
                $("#templateAddJoin").live('click',function()
                {
                    var portType = $("#deviceJoins thead select[name=joinPortType]").attr("value");
                    var portTypeName = $("#deviceJoins thead select[name=joinPortType] :selected").text();
                    var bandwidth = $("#deviceJoins thead select[name=joinBandwidth]").attr("value");
                    var bandwidthText = $("#deviceJoins thead select[name=joinBandwidth] :selected").text();
                    var count = parseInt($("#deviceJoins thead input[name=joinCount]").val());

                    if($('#templateJoinRow'+portType+bandwidth+' td').length!=0)
                    {
                        var existingCount=parseInt($('#templateJoinRow'+portType+' td input[name=joinCount[]]').val());
                        var newCount=existingCount+count;
                       $('#templateJoinRow'+portType+' td.count').html('<input type="hidden" name="joinCount[]" value="' + newCount + '" />' + newCount );
                    }
                    else
                    {
                        $("#deviceJoins tbody").append('<tr id="templateJoinRow'+portType+bandwidth+'"><td><input type="hidden" name="joinPortType[]" value="' + portType + '" />' + portTypeName +
                        '</td><td><input type="hidden" name="joinBandwidth[]" value="' + bandwidth + '" />' + bandwidthText +
                        '</td><td class="count"><input type="hidden" name="joinCount[]" value="' + count + '" />' + count +
                        '<td><span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" ><img src="images/icons/close_rack.gif" border="0" /></a></td></tr>');
                    }
                    return false;
                });

                $("#deviceJoins tbody").sortable({
                    items: "tr",
                    handle: "span.handle",
                    update : function ()
                    {

                    }
                });
            });
        </script>
	<div id="deviceJoins" >
	<table width="100px" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
                    <tr><th>Port Type:</th><th>Bandwidth:</th><th>Count</th></tr>
                    <tr>
			<td><select name="joinPortType" >
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
			<td><select name="joinBandwidth" style="font-style:italic;" >
				<option value="10" >10 Mbit/s</option>
				<option value="10/100" >10/100 Mbit/s</option>
				<option value="10/100/1000" SELECTED>10/100/1000 Mbit/s</option>
				<option value="10 Gbit/s" >10 Gbit/s</option>
				</select></td>
			<td><input name="joinCount" value="1" size="2" style="font-style: italic;" /></td>
			<td align="right" colspan="2"><a href="#" id="templateAddJoin" >Add</a></td></tr>
		</thead>
		<tbody>
                    <?php
                    foreach($this->getTemplateJoins($templateID) as $template)
                    {
                        if($template['isJoin'])
                        {
                            
                            $cleanName=preg_replace('/[^a-zA-Z0-9]+/','',$template['bandwidth']);
                        echo '<tr id="templateJoinRow'.$template['portTypeID'].$cleanName.'"><td><input type="hidden" name="joinPortType[]" value="'.$template['portTypeID'].'" />'.$cableTypes    [$template['portTypeID']].'
                        </td><td><input type="hidden" name="joinBandwidth[]" value="'.$template['bandwidth'].'" />'.$template['bandwidth'].'
                        </td><td class="count"><input type="hidden" name="joinCount[]" value="'.$template['count'].'" />'.$template['count'].'
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
            $del=$this->db->prepare("DELETE from templateports WHERE templateID=? AND isJoin=1;");
            $del->execute(array($templateID));

            if(isset($_POST['joinPortType']) && is_array($_POST['joinPortType']))
            {
                $joinsToCreate=array();
                // loop over them and fill the array we pass in later
                foreach($_POST['joinPortType'] as $key=>$item)
                {
                    $entry=array();
                    $entry['templateID']=$templateID;
                    $entry['portTypeID']=$_POST['joinPortType'][$key];
                    $entry['isJoin']="1";
                    $entry['bandwidth']=$_POST['joinBandwidth'][$key];
                    $entry['count']=$_POST['joinCount'][$key];
                    $entry['disporder']=($key+1);
                    $joinsToCreate[]=$entry;
                }
                $templates=new templates;
                if(!$templates->createTemplatePorts($joinsToCreate))
                    echo "Error creating template joins";
                else
                    return 1;
            }
            else
                return 1;
        }


        function showPatchFull($deviceID,$startID=1,$groupPorts=8)
        {
            // start the box for content
            echo "<div class='device' title='deviceID".$deviceID."' >";

            // get all joins and show a basic error if none found
            $patches=$this->getByDevice($deviceID);
            if(empty($patches))
                echo "<div class='desc' ><em>No configured Ports</em></div>";
            else
            {
                // how many ports do we want to group together
                // we change the css and looping in the interface based off this
                if($groupPorts==8)
                    $panelWidth='eightWide';
                else if($groupPorts==5)
                    $panelWidth='fiveWide';
                else
                    $panelWidth='fourWide';

                // start our list and count to determine spacing
               echo " <ul class='patches ".$panelWidth."' >";
                $count=0;
                foreach($patches as $key=>$patch)
                {
                    // if we aren't the first entry and are at a point in our spacing place the gap
                    if($count%$groupPorts==0 && $count!=0)
                        echo "<li class='split' ></li>";

                    // display the port and determine its level of connectivity
                    // different popups based on this
                    echo "<li class='join' >";
                    if($patch->primConnectedToPortID >0 && $patch->secConnectedToPortID >0)
                        echo "<a class='connected' onclick='loadDevice(".$deviceID.");manageConnection(2);'>";
                    elseif($patch->primConnectedToPortID >0 || $patch->secConnectedToPortID >0)
                        echo "<a class='singleconnected' onclick='loadDevice(".$deviceID.");manageConnection(2);'>";
                    else
                        echo "<a class='disconnected' onclick='loadDevice(".$deviceID.");manageConnection(2);'>";

                    // show the label for the port based on our starting port ID(typically 0 or 1)
                    echo ($key+$startID);
                    // provide some more detail on the ports connection, this gets placed in the hover-over menu
                    echo "<strong style='display:none;'>";
                    if($patch->primConnectedToDeviceID > 0)
                            echo "Front Connected to ".$patch->primConnectedToDeviceName." &raquo; Port ".$patch->primConnectedToPortName."<br/>";
                    if($patch->secConnectedToDeviceID > 0)
                            echo "Back Connected to ".$patch->secConnectedToDeviceName." &raquo; Port ".$patch->secConnectedToPortName."<br/>";
                    if($patch->secConnectedToDeviceID == 0 && $patch->primConnectedToDeviceID == 0)
                            echo "no connection";
                    echo "</strong>";

                  echo '</a></li>';
                  $count++;
                }
                ?>
                </ul>
        <?php
            }
            echo "</div>";
        }
}

class join
{
	var $db;
	var $joinID;
	var $deviceID;
	var $primPort;
	var $secPort;
	
	//var $connectedToPortID;
	//var $connectedToPortName;
	//var $connectedToDeviceID;
	//var $connectedToDeviceName;
	function __construct($byJoinID='0',$lookupPorts=1)
	{
		global $db;
		$this->db=$db;	
		$this->joinID = $byJoinID;
		$this->disporder = 0;
		$this->deviceID=0;
		$this->primPort="";
		$this->secPort="";
		$this->cableTypeID=0;

			
		$this->primConnectedToPortID = 0;
		$this->primConnectedToPortName= "";
		$this->primConnectedToDeviceID = 0;
		$this->secConnectedToPortID = 0;
		$this->secConnectedToPortName= "";
		$this->secConnectedToDeviceID = 0;
		$ports = new ports;
		
		if (is_numeric($this->joinID) && $this->joinID > 0)
		{		
                    $query = $this->db->prepare('SELECT * FROM joins WHERE joinID = ?');
                    $query->execute(array($this->joinID));
                    $result = $query->fetchAll();
                    foreach($result as $join)
                    {
                        $this->joinID = $join['joinID'];
                        $this->disporder = $join['disporder'];
                        $this->deviceID = $join['deviceID'];
                        $this->primPort = $join['primPort'];
                        $this->secPort = $join['secPort'];
                        $this->cableTypeID= $join['cableTypeID'];
                    }
                        
                    if($this->primPort != 0 && $lookupPorts)
                    {
                        $primPort = new port($this->primPort);
                        $primCable = $primPort->cableID;

                        $portsOnCable = $ports->getByCableID($primCable);
                        foreach($portsOnCable as $port)
                        {
                            if($port->portID != $this->primPort)
                            {
                                $this->primConnectedToPortName=$primCable;
                                $this->primConnectedToPortID = $port->portID;
                                $this->primConnectedToPortName = $port->label;
                                $this->primConnectedToDeviceID = $port->deviceID;
                                $dev = new device($port->deviceID);
                                $this->primConnectedToDeviceName = $dev->name;
                            }
                        }
                    }
			
                    if($this->secPort != 0 && $lookupPorts)
                    {
                        $secPort = new port($this->secPort);
                        $secCable = $secPort->cableID;

                        $portsOnCable = $ports->getByCableID($secCable);
                        foreach($portsOnCable as $port)
                        {
                            if($port->portID != $this->secPort)
                            {
                                $this->secConnectedToPortName=$secCable;
                                $this->secConnectedToPortID = $port->portID;
                                $this->secConnectedToPortName = $port->label;
                                $this->secConnectedToDeviceID = $port->deviceID;
                                $dev = new device($port->deviceID);
                                $this->secConnectedToDeviceName = $dev->name;
                            }
                        }
                    }
		}
	}
};
?>