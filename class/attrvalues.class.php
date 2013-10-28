<?php
class attrvalues
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
                $query = $this->db->prepare('SELECT * FROM attrvalues');
                $query->execute();
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $attrvalue)
                {
                    $newItem = new attrvalue;
                    $newItem->attrvalueid = $attrvalue['attrvalueid'];
                    $newItem->attrnameid = $attrvalue['attrnameid'];
                    $newItem->value = $attrvalue['value'];
                    $newItem->parentid = $attrvalue['parentid'];
                    $newItem->parenttype = $attrvalue['parenttype'];
                    array_push($return, $newItem);
                }
                $this->rows = $return;
                return $return;
            }
            else
                return $this->rows;
	}


	function getByID($attrvalueid)
	{
		if (count($this->rows) > 0)
		{
                    $return = array();
                    foreach($this->rows as $attrvalue)
                    {
                        if ($attrvalue->attrvalueid == $attrvalueid)
                        {
                            array_push($return, $attrvalue);
                        }
                    }
                    return $return;
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM attrvalues WHERE attrvalueid = ?');
			$query->execute(array($roomID));
			$result = $query->fetchAll();

			$return = array();
			foreach($result as $attrvalue)
			{
                            $newItem = new attrvalue;
                            $newItem->attrvalueid = $attrvalue['attrvalueid'];
                            $newItem->attrnameid = $attrvalue['attrnameid'];
                            $newItem->value = $attrvalue['value'];
                            $newItem->parentid = $attrvalue['parentid'];
                            $newItem->parenttype = $attrvalue['parenttype'];
                            array_push($return, $newItem);
			}
			return $return;
		}
	}

        function getByParent()
        {


            return "asd";
        }

	function insert($attrvalue)
	{
		$this->db->prepare("INSERT INTO attrvalues VALUES ('',?,?,?,?);");
		$created = $this->db->execute(array($attrvalue->attrnameid,$attrvalue->name,$attrvalue->parentid,$attrvalue->parenttype));

		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();

		if($created)
                    return $result[0]['LAST_INSERT_ID()'];
		else
                    return 0;
	}

        function insertMultiple($entries)
        {
            $status=array();
            $prepared = $this->db->prepare("INSERT INTO attrvalues VALUES ('',?,?,?,?);");
            foreach($entries as $entry)
                $status[] = $prepared->execute(array($entry->attrnameid,$entry->value,$entry->parentid,$entry->parenttype));
            
            if(in_array('0',$status))
                return 0;
            else
                return 1;
        }

	function update($attrvalue)
	{
            $this->db->prepare("UPDATE attrvalues SET attrnameid=?,value=?,parentid=?,parenttype=? WHERE attrvalueid = ?;");
            return $this->db->execute(array($attrvalue->attrnameid,$attrvalue->value,$attrvalue->value,$attrvalue->value,$attrvalue->attrvalueid));
	}

        function updateMultipleValues($values)
        {
            $status=array();
            $prepared = $this->db->prepare("UPDATE attrvalues SET value=? WHERE attrvalueid = ?;");
            foreach($values as $attrvalueid=>$val)
                $status[]=$prepared->execute(array($val,$attrvalueid));

            if(in_array('0',$status))
                return 0;
            else
                return 1;
        }

	function delete($attrvalueid)
	{
            $this->db->prepare("DELETE FROM attrvalues WHERE attrvalueid = ?;");
            $this->db->execute(array($attrvalueid));
	}

        function deleteMultiple($parentID,$parentType,$ids=0)
        {
            $status=array();
            if(is_array($ids))
            {
                $prepared = $this->db->prepare("DELETE FROM attrvalues WHERE attrnameid=? AND parentID=? and parentType=? LIMIT 1;");
                foreach($ids as $id)
                    $status[]=$prepared->execute(array($id,$parentID,$parentType));
            }
            else
            {
                $prepared = $this->db->prepare("DELETE FROM attrvalues WHERE parentID=? and parentType=? LIMIT 1;");
                $status[]=$prepared->execute(array($parentID,$parentType));
            }
            if(in_array('0',$status))
                return 0;
            else
                return 1;
        }
}

class attrvalue
{
	var $attrvalueid = 0;
	var $attrnameid = '';
        var $value='';
        var $parenttype = '';

	function __construct($itemID='0')
	{
		global $db;
		$this->attrvalueid = $itemID;

		if(is_numeric($this->attrvalueid) && $this->attrvalueid > 0)
		{
			$query = $db->prepare('SELECT * FROM attrvalues WHERE attrvalueid=?');
			$query->execute(array($this->attrvalueid));
			$result = $query->fetchAll();
			foreach($result as $attrvalue)
			{
				$this->attrnameid = $attrvalue['attrnameid'];
                                $this->value = $attrvalue['value'];
                                $this->parentid=$attrvalue['parentid'];
                                $this->parenttype=$attrvalue['parenttype'];
			}
		}
	}
};
?>
