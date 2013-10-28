<?php
class attroptionvalues
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
                $query = $this->db->prepare('SELECT * FROM attroptionvalues');
                $query->execute();
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $attroptionvalue)
                {
                    $newItem = new attroptionvalue;
                    $newItem->attrvalueoptionid = $attroptionvalue['attrvalueoptionid'];
                    $newItem->attrvalueid = $attroptionvalue['attrvalueid'];
                    $newItem->attroptionid = $attroptionvalue['attroptionid'];
                    array_push($return, $newItem);
                }
                $this->rows = $return;
                return $return;
            }
            else
                return $this->rows;
	}


	function getByID($attrvalueoptionid)
	{
		if (count($this->rows) > 0)
		{
                    $return = array();
                    foreach($this->rows as $attroptionvalue)
                    {
                        if ($attroptionvalue->attrvalueoptionid == $attrvalueoptionid)
                        {
                            array_push($return, $attroptionvalue);
                        }
                    }
                    return $return;
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM attroptionvalues WHERE attrvalueoptionid = ?');
			$query->execute(array($roomID));
			$result = $query->fetchAll();

			$return = array();
			foreach($result as $attroptionvalue)
			{
				$newItem = new attroptionvalue;
				$newItem->attrvalueoptionid = $attroptionvalue['attrvalueoptionid'];
                                $newItem->attrvalueid = $attroptionvalue['attrvalueid'];
                                $newItem->attroptionid = $attroptionvalue['attroptionid'];
				array_push($return, $newItem);
			}
			return $return;
		}
	}

	function insert($attroptionvalue)
	{
		$this->db->prepare("INSERT INTO attroptionvalues VALUES ('',?,?);");
		$created = $this->db->execute(array($attroptionvalue->attrvalueid,$attroptionvalue->attroptionid));

		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();

		if($created)
                    return $result[0]['LAST_INSERT_ID()'];
		else
                    return 0;
	}

	function update($attroptionvalue)
	{
            $this->db->prepare("UPDATE attroptionvalues SET attrvalueid=?,attroptionid=? WHERE attrvalueoptionid = ?;");
            return $this->db->execute(array($attroptionvalue->attrvalueid,$attroptionvalue->attroptionid,$attroptionvalue->attrvalueoptionid));
	}

	function delete($attrvalueoptionid)
	{
		$this->db->prepare("DELETE FROM attroptionvalues WHERE attrvalueoptionid = ?;");
		$this->db->execute(array($attrvalueoptionid));
	}
}

class attroptionvalue
{
	var $attrvalueoptionid = 0;
	var $attrvalueid = '';
        var $attroptinoid = '';

	function __construct($itemID='0')
	{
		global $db;
		$this->attrvalueoptionid = $itemID;

		if(is_numeric($this->attrvalueoptionid) && $this->attrvalueoptionid > 0)
		{
			$query = $db->prepare('SELECT * FROM attroptionvalues WHERE attrvalueoptionid=?');
			$query->execute(array($this->attrvalueoptionid));
			$result = $query->fetchAll();
			foreach($result as $attroptionvalue)
			{
				$this->attrvalueoptionid = $attroptionvalue['attrvalueoptionid'];
                                $this->attrvalueid = $attroptionvalue['attrvalueid'];
                                $this->attroptionid = $attroptionvalue['attroptionid'];
			}
		}
	}
};
?>
