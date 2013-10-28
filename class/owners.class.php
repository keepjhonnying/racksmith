<?php
class owners
{
	var $db;
	
	function __construct()
	{
		global $db;
		$this->db=$db;		
	}

	var $rows;

	function getAll()
	{
		$return = array();

		$this->db->query('SELECT ownerID, name, contactname,phone,afterHoursPhone,email,fax,mobile,serviceLevel FROM owners ORDER BY ownerID ASC');
		$result = $this->db->fetchAll();
		
		foreach($result as $owner)
		{
			$newOwner = new owner;
			$newOwner->ownerID = $owner['ownerID'];
			$newOwner->name = $owner['name'];
			$newOwner->contactname = $owner['contactname'];
			$newOwner->phone = $owner['phone'];
			$newOwner->afterHoursPhone = $owner['afterHoursPhone'];
			$newOwner->email = $owner['email'];
			$newOwner->fax = $owner['fax'];
			$newOwner->mobile = $owner['mobile'];
			$newOwner->serviceLevel = $owner['serviceLevel'];
			$return[$newOwner->ownerID]=$newOwner;
		}
		$this->rows = $return;
		return $return;		
	}

	function insert($newOwner)
	{
		$this->db->prepare("INSERT INTO owners (name,contactname,phone,afterHoursPhone,email,fax,mobile,serviceLevel) VALUES (?,?,?,?,?,?,?,?);");

		$this->db->execute(array($newOwner->name,$newOwner->contactname, $newOwner->phone,$newOwner->afterHoursPhone,$newOwner->email,$newOwner->fax,$newOwner->mobile,$newOwner->serviceLevel));
	}

	function update($newOwner)
	{
		$this->db->prepare("UPDATE owners SET name = ?,contactname = ?,phone = ?,afterHoursPhone= ?,email = ?,fax = ?,mobile = ?,serviceLevel = ? WHERE ownerID = ?;");
		$this->db->execute(array($newOwner->name,$newOwner->contactname,$newOwner->phone,$newOwner->afterHoursPhone,$newOwner->email,$newOwner->fax,$newOwner->mobile,$newOwner->serviceLevel,$newOwner->ownerID));
	}

	function delete($ownerID)
	{
            $this->db->prepare("DELETE FROM owners WHERE ownerID = ? LIMIT 1;");
            return $this->db->execute(array($ownerID));
	}
};

class owner
{
	var $ownerID;
	var $name;
	var $contactname;
	var $phone;
	var $afterHoursPhone;
	var $email;
	var $fax;
	var $mobile;
	var $serviceLevel;

	var $db;

	function __construct($ByOwnerID='0')
	{
		global $db;
		$this->db=$db;	

		$this->ownerID = $ByOwnerID;
		$this->name = "";
		$this->contactname = "";
		$this->phone = "";
		$this->afterHoursPhone = "";
		$this->email = "";
		$this->fax = "";
		$this->mobile = "";
		$this->serviceLevel = "";

		if (is_numeric($this->ownerID) && $this->ownerID > 0)
		{		
			$query = $this->db->prepare('SELECT ownerID, name, contactname,phone,afterHoursPhone,email,fax,mobile,serviceLevel FROM owners WHERE ownerID = ?');
			$query->execute(array($this->ownerID));
			$result = $query->fetchAll();

			foreach($result as $owner)
			{
				$this->ownerID = $owner['ownerID'];
				$this->name = $owner['name'];
				$this->contactname = $owner['contactname'];
				$this->phone = $owner['phone'];
				$this->afterHoursPhone = $owner['afterHoursPhone'];
				$this->email = $owner['email'];
				$this->fax = $owner['fax'];
				$this->mobile = $owner['mobile'];
				$this->serviceLevel = $owner['serviceLevel'];
			}
		}
	}

};
?>