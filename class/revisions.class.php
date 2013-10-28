<?php
class revision
{
	var $revisionID;
	var $revisionName;
	var $description;
	var $createdDate;
	var $lastChangeDate;

	function __construct($ByRevisionID=0)
	{
		global $db;
		
		if(is_int($ByRevisionID))
		{
			// Find Revision By ID
			$this->revisionID = $ByRevisionID;
			$this->revisionName = "";
			$this->description ="";
			$this->notes = "";
			$this->createdDate = date("Y-m-d H:i:s");
			$this->lastChangeDate = date("Y-m-d H:i:s");
			
			if (is_numeric($this->revision) && $this->revision > 0)
			{		
				$query = $db->prepare('SELECT * FROM revisions WHERE revisionID=?');
				$query->execute(array($this->revisionID));
				$result = $query->fetchAll();
				
				foreach($result as $revision)
				{
					$this->revisionID = $revision['revisionID'];
					$this->revisionName = $revision['revisionName'];
					$this->description = $revision['description'];
					$this->notes = $revision['notes'];
					$this->createdDate = $revision['createdDate'];
					$this->lastChangeDate = $revision['lastChangeDate'];
				}
			}
		}
		else
		{
			// Revision By Name
			$this->revisionID = 0;
			$this->revisionName = $ByRevisionID;
			$this->description ="";
			$this->notes = "";
			$this->createdDate = date("Y-m-d H:i:s");
			$this->lastChangeDate = date("Y-m-d H:i:s");
			
			if (is_numeric($this->revision) && $this->revision > 0)
			{		
				$query = $db->prepare('SELECT * FROM revisions WHERE revisionName=?');
				$query->execute(array($this->revisionName));
				$result = $query->fetchAll();
				
				foreach($result as $revision)
				{
					$this->revisionID = $revision['revisionID'];
					$this->revisionName = $revision['revisionName'];
					$this->description = $revision['description'];
					$this->notes = $revision['notes'];
					$this->createdDate = $revision['createdDate'];
					$this->lastChangeDate = $revision['lastChangeDate'];
				}
			}
		}
	}
	
	function repair()
	{
		global $db;
		
		$revision = new revision("Proposed");
		
		if ($revisionID == 0)
		{
			$query = $db->prepare("INSERT INTO rooms (revisionName,description,createdDate,lastChangeDate) VALUES (?,?,?,?);");
			$query->execute(array("Proposed","Design new configurations before they are implemented",date("Y-m-d H:i:s"),date("Y-m-d H:i:s")));
		}
		$query = $db->query("SELECT LAST_INSERT_ID()");
		$result = $query->fetchAll();
		
		return $result[0]['LAST_INSERT_ID()'];
	}
	
	
	function newRevision($revisionName,$description,$notes)
	{
		global $db;
		
		$query = $db->prepare("INSERT INTO rooms (roomID,floorID,buildingID,ownerID,name,color,notes,revisionID) VALUES (?,?,?,?,?,?,?,?);");
		$query->execute(array($newRoom->roomID,$newRoom->floorID,$newRoom->buildingID,$newRoom->ownerID,$newRoom->name,$newRoom->color,$newRoom->notes,$newRoom->revisionID));
		
		$query = $db->query("SELECT LAST_INSERT_ID()");
		$result = $query->fetchAll();
		
		return $result[0]['LAST_INSERT_ID()'];
	}
};
?>