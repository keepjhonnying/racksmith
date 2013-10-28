<?php
class attroptions
{
	var $db;
	var $rows=array();

	function __construct()
	{
		global $db;
		$this->db=$db;
	}

	function cacheAll()
	{
		$this->getAll();
	}

	function getAll()
	{
            if (count($this->rows) <= 0)
            {
                $query = $this->db->prepare('SELECT * FROM attroptions');
                $query->execute();
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $attroption)
                {
                    $newItem = new attroption;
                    $newItem->attroptionid = $attroption['attroptionid'];
                    $newItem->attrnameid = $attroption['attrnameid'];
                    $newItem->name = $attroption['name'];
                    array_push($return, $newItem);
                }
                $this->rows = $return;
                return $return;
            }
            else
                return $this->rows;
	}


	function getByID($attroptionid)
	{
		if (count($this->rows) > 0)
		{
                    $return = array();
                    foreach($this->rows as $attroption)
                    {
                        if ($attroption->attroptionid == $attroptionid)
                        {
                            array_push($return, $attroption);
                        }
                    }
                    return $return;
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM attroptions WHERE attroptionid = ?');
			$query->execute(array($roomID));
			$result = $query->fetchAll();

			$return = array();
			foreach($result as $attroption)
			{
				$newItem = new attroption;
                                $newItem->attroptionid = $attroption['attroptionid'];
                                $newItem->attrnameid = $attroption['attrnameid'];
                                $newItem->name = $attroption['name'];
				array_push($return, $newItem);
			}
			return $return;
		}
	}

	function insert($attroption)
	{
		$this->db->prepare("INSERT INTO attroptions VALUES ('',?,?);");
		$created = $this->db->execute(array($attroption->attrnameid,$attroption->name));

		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();

		if($created)
                    return $result[0]['LAST_INSERT_ID()'];
		else
                    return 0;
	}

	function update($attroption)
	{
            $this->db->prepare("UPDATE attroptions SET attrnameid=?,name=? WHERE attroptionid = ?;");
            return $this->db->execute(array($attroption->attrnameid,$attroption->name,$attroption->attroptionid));
	}

	function delete($attroptionid)
	{
		$this->db->prepare("DELETE FROM attroptions WHERE attroptionid = ?;");
		$this->db->execute(array($attroptionid));
	}
}

class attroption
{
	var $attroptionid = 0;
	var $attrnameid = '';
        var $name = '';
        
	function __construct($itemID='0')
	{
		global $db;
		$this->attroptionid = $itemID;

		if(is_numeric($this->attroptionid) && $this->attroptionid > 0)
		{
			$query = $db->prepare('SELECT * FROM attroptions WHERE attroptionid=?');
			$query->execute(array($this->attroptionid));
			$result = $query->fetchAll();
			foreach($result as $attroption)
			{
				$this->attrnameid = $attroption['attrnameid'];
                                $this->name = $attroption['name'];
			}
		}
	}
};
?>
