<?php
// Variables names provide a neater way of writing code
// Below are some static variables used for the default categories
define("GENERIC","1");
define("DRAWS_POWER","7");
define("GENERATES_POWER","8");
define("RACK_MOUNTABLE","2");
define("FLOOR_DEVICE","3");
define("PROVIDES_COOLING","10");
define("IS_UPS","9");
define("HAS_SOFTWARE","12");
define("HAS_OS","13");
define("IS_PATCH","14");
define("HAS_NETWORK_PORTS","15");
define("PROVIDES_DATA_STORAGE","17");
define("IS_SHELF","5");
define("HAS_LOM","16");
define("REQUIRES_SERVICING","18");
define("OUTDOOR_ITEM","4");
define("IS_CHASSIS","6");


class attrcategories
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
                    $query = $this->db->prepare('SELECT * FROM attrcategory ORDER BY sort DESC');
                    $query->execute();
                    $result = $query->fetchAll();

                    $return = array();
                    foreach($result as $category)
                    {
                        $attrcategory = new attrcategory();
                        $attrcategory->attrcategoryid = $category['attrcategoryid'];
                        $attrcategory->name = $category['name'];
                        $attrcategory->static = $category['static'];
                        $attrcategory->sort = $category['sort'];
                        //array_push($return, $attrcategory);
                        $return[$category['attrcategoryid']]=$attrcategory;
                    }
                    $this->rows = $return;
                    return $return;
            }
            else
                    return $this->rows;
	}


	function getByID($attrcategoryid)
	{
            if (count($this->rows) > 0)
            {
                $return = array();
                foreach($this->rows as $attrcategory)
                {
                    if ($attrcategory->attrcategoryid == $attrcategoryid)
                    {
                        array_push($return, $attrcategory);
                    }
                }
                return $return;
            }
            else
            {
                $query = $this->db->prepare('SELECT * FROM attrcategory WHERE attrcategoryid = ?');
                $query->execute(array($attrcategoryid));
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $category)
                {
                    $attrcategory = new attrcategory;
                    $attrcategory->attrcategoryid = $category['attrcategoryid'];
                    $attrcategory->name = $category['name'];
                    $attrcategory->static = $category['static'];
                    $attrcategory->sort = $category['sort'];
                    array_push($return, $attrcategory);
                }
                return $return;
            }
	}


        function getByParent($parentID,$parentType,$categoryFilter=0,$getValues=0)
	{
            if (count($this->rows) > 0)
            {
                $return = array();
                foreach($this->rows as $attrcategory)
                {
                    if ($attrcategory->parentID == $parentID && $attrcategory->parentType == $parentType)
                    {
                        array_push($return, $attrcategory);
                    }
                }
                return $return;
            }
            else
            {
                $query = $this->db->prepare('SELECT * FROM attrcategory WHERE parentID=? AND parentType=?;');
                $query->execute(array($parentID,$parentType));
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $category)
                {
                    $attrcategory = new attrcategory;
                    $attrcategory->attrcategoryid = $category['attrcategoryid'];
                    $attrcategory->name = $category['name'];
                    $attrcategory->static = $category['static'];
                    $attrcategory->sort = $category['sort'];
                    array_push($return, $attrcategory);
                }
                return $return;
            }
	}


	function insert($attrcategory)
	{
            $this->db->prepare("INSERT INTO attrcategory (attrcategoryid,name,static,sort) VALUES ('',?,0,?);");
            $created = $this->db->execute(array($attrcategory->name,$attrcategory->sort));

            $this->db->query("SELECT LAST_INSERT_ID()");
            $result = $this->db->fetchAll();

            if ($created)
                return $result[0]['LAST_INSERT_ID()'];
            else
                return $created;
	}

	function update($attrcategory)
	{
            $this->db->prepare("UPDATE attrcategory SET name = ?,sort=? WHERE attrcategoryid = ?;");
            return $this->db->execute(array($attrcategory->name,$attrcategory->sort,$attrcategory->attrcategoryid));
	}

	function delete($attrcategoryID)
	{
            $this->db->prepare("DELETE FROM attrcategory WHERE attrcategoryid = ? AND static != 1;");
            $this->db->execute(array($attrcategoryID));
		
            //$attrnames = new attrnames;
            //$attrnames->deleteItems($attrcategoryID);
	}

	function update_sort($data)
	{
            $saveStatus=array();
            $data = preg_replace("/[^0-9,]/","",$data);
            $data = explode(",",$data);
            $currentCount=count($data)-1;

            foreach($data as $currentID)
            {
                if(is_numeric($currentID))
                {
                    $update = $this->db->prepare("UPDATE attrcategory SET sort=? WHERE attrcategoryid=?");
                    if($update->execute(array($currentCount,$currentID)))
                        $saveStatus[]=1;
                    else
                        $saveStatus[]=0;
                }
                $currentCount--;
            }

            if(in_array(0, $saveStatus))
                return 0;
            else
                return 1;
	}

}

class attrcategory
{
	var $attrcategoryid = 0;
	var $name = '';
        var $sort='0';

	function __construct($ByCategoryID='0')
	{
		global $db;
		$this->attrcategoryid = $ByCategoryID;
		$this->name = "";
                $this->static = "";
		
		if (is_numeric($this->attrcategoryid) && $this->attrcategoryid > 0)
		{		
			$query = $db->prepare('SELECT * FROM attcategory WHERE attrcategoryid=?');
			$query->execute(array($this->attrcategoryid));
			$result = $query->fetchAll();
			foreach($result as $attrcategory)
			{
				$this->name = $attrcategory['name'];
                                $this->static = $attrcategory['static'];
                                $this->sort = $attrcategory['sort'];
			}
		}
	}
};
?>
