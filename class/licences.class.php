<?php
class licences
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

			$query = $this->db->prepare('SELECT * FROM licences WHERE deviceID = ?');
			$query->execute(array($deviceID));
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $newlicence)
			{
				$licence = new licence;
				$licence->licenceID = $newlicence['licenceID'];
				$licence->deviceID = $newlicence['deviceID'];
				$licence->software = $newlicence['software'];
				$licence->licence = $newlicence['licence'];
				$licence->softwareNotes = $newlicence['softwareNotes'];
				array_push($return, $licence);
			}
			return $return;	
		}
		else
			return $this->rows;
	}

	
	function insert($licence)
	{
		$this->db->prepare("INSERT INTO licences (licenceID,deviceID,software,licence,softwareNotes) VALUES ('',?,?,?,?);");
		$created = $this->db->execute(array($licence->deviceID,$licence->software,$licence->licence,$licence->softwareNotes));
		
		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();
		
		if ($created)
			return $result[0]['LAST_INSERT_ID()'];
		else
			return $created;
	}

	function update($licence)
	{
		$this->db->prepare("UPDATE licences SET software = ?,licence = ?,softwareNotes = ? WHERE licenceID = ?");
		$this->db->execute(array($licence->software,$licence->licence,$licence->softwareNotes,$licence->licenceID));
	}

	function delete($licenceID)
	{
		$this->db->prepare("DELETE FROM licences WHERE licenceID = ?;");
		$this->db->execute(array($licenceID));
	}


        function createForm()
        { ?>
        <script type="text/javascript" >
            $(document).ready(function()
            {
            	$("#templateAddSoftware").live('click',function()
                {
		var software = $("#deviceSoftware input[name=software]").attr("value");
                if(software=="software")
                    software="";
		var licence = $("#deviceSoftware input[name=licence]").attr("value");
                if(licence=="licence key")
                    licence="--------";
		var softwareDetails = $("#deviceSoftware  input[name=softwareDetails]").attr("value");
                if(softwareDetails=="notes")
                    softwareDetails="";

		$("#deviceSoftware tbody").prepend('<tr><td><input type="hidden" name="softwareName[]" value="' + software + '" />' + software +
		'</td><td><input type="hidden" name="licenceKey[]" value="' + licence + '" />' + licence +
		'</td><td><input type="hidden" name="softwareNotes[]" value="' + softwareDetails + '" />' + softwareDetails +
		'<td><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" >delete</a></td></tr>');
                return false;
                });
            });
            </script>
	<div id="deviceSoftware" >
	<table width="80%" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
                    <tr>
                        <td><input name="software" value="software" style="font-style: italic;" /></td>
                        <td><input name="licence" value="licence key" style="font-style: italic;" /></td>
                        <td><input name="softwareDetails" value="notes" size="60" style="font-style: italic;" /></td>
                        <td align="right" colspan="2"><a href="#" id="templateAddSoftware" >Add Software</a></td></tr>
		</thead>

		<tbody>

		</tbody>
	</table>
	</div>
         <?php
        }
}

class licence
{
	var $db;
	var $licenceID;
	var $deviceID='0';
	var $softwar='0';
	var $licence='0';
	var $softwareNotes='0';

	function __construct($bylicenceID='0')
	{
		global $db;
		$this->db=$db;	
		$this->licenceID = $bylicenceID;
		
		if (is_numeric($this->licenceID) && $this->licenceID > 0)
		{		
			$query = $this->db->prepare('SELECT * FROM licences WHERE licenceID = ?');
			$query->execute(array($this->licenceID));
			$result = $query->fetchAll();

			foreach($result as $licence)
			{
				$this->deviceID = $licence['deviceID'];
				$this->software = $licence['software'];
				$this->licence = $licence['licence'];
				$this->softwareNotes = $licence['softwareNotes'];
			}
		}
	}
};
?>