<?php
class attrnames
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
                $query = $this->db->prepare('SELECT * FROM attrnames ORDER BY sort desc;');
                $query->execute();
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $name)
                {
                    $attrname = new attrname;
                    foreach($name as $key=>$val)
                        $attrname->$key=$val;
                    array_push($return, $attrname);
                }
                $this->rows = $return;
                return $return;
            }
            else
                    return $this->rows;
	}


	function getByParent($attrparentid, $attrparenttype,$categoryFilter=0,$getValues=0)
	{
            $categorySQL="";
            // customise SQL to filter categories, cant do this in the execute
            if(is_array($categoryFilter))
            {
                // for each parentType we want to loop the filter
                $categorySections=array();
                foreach($categoryFilter as $parentType=>$IDs)
                {
                    // check requirements for individual items in this type
                    $categorySections[]=' (attrnames.parentType="'.$parentType.'" AND (attrnames.parentID="'.implode("\" || attrnames.parentID=\"",$IDs).'"))';
                }
                $categorySQL="(".implode(" || ",$categorySections).") AND ";
            }

            // if we want to join with the values as well
            if($getValues)
                $query = $this->db->prepare('SELECT attrnames.*, attrvalues.value FROM attrnames LEFT JOIN attrvalues ON attrvalues.attrnameid=attrnames.attrnameid WHERE '.$categorySQL.' attrvalues.parentid=? AND attrvalues.parenttype=? ORDER BY attrnames.parentID,attrnames.sort;');
            else
                $query = $this->db->prepare('SELECT * FROM attrnames WHERE '.$categorySQL.' parentid = ? AND parenttype = ? ORDER BY sort desc;');

            $query->execute(array($attrparentid,$attrparenttype));
            return $query->fetchAll(PDO::FETCH_CLASS);
	}

	function insert($attrname)
	{
            $this->db->prepare("INSERT INTO attrnames VALUES ('',?,?,?,?,?,?,?,?,?,?,?);");
            $created = $this->db->execute(array($attrname->parentid,$attrname->parenttype,$attrname->name,$attrname->type,$attrname->default,$attrname->units,$attrname->options,$attrname->desc,$attrname->static,$attrname->control,$attrname->sort));

            $this->db->query("SELECT LAST_INSERT_ID()");
            $result = $this->db->fetchAll();

            if ($created)
                return $result[0]['LAST_INSERT_ID()'];
            else
                return $created;
	}

	function update($attrname)
	{
		$this->db->prepare("UPDATE attrnames SET parentid=?,parenttype=?,name=?,type=?,default=?,desc=?,units=?,options=?,control=?,sort=? WHERE attrnameid = ?;");
		return $this->db->execute(array($attrname->parentid,$attrname->parenttype,$attrname->type,$attrname->type,$attrname->default,$attrname->desc,$attrname->units,$attrname->options,$attrname->control,$attrname->sort,$attrname->attrnameid));
	}

	function delete($attrnameID)
	{
		$this->db->prepare("DELETE FROM attrnames WHERE attrnameid = ? AND static != 1;");
		$this->db->execute(array($attrnameID));
		
		//$attrvalues = new attrvalues;
		//$attrvalues->deleteItems($attrnameID);
	}
        
	function update_sort($data)
	{
            $saveStatus=array();
            $data = preg_replace("/[^0-9,]/","",$data);
            $data = explode(",",$data);
            $data = array_reverse($data);
            $update = $this->db->prepare("UPDATE attrnames SET sort=? WHERE attrnameid=?;");
            
            foreach($data as $key=>$currentID)
            {
                if(is_numeric($currentID))
                {        
                    if($update->execute(array(($key+1),$currentID)))
                        $saveStatus[]=1;
                    else
                        $saveStatus[]=0;
                }
            }

            if(in_array(0, $saveStatus))
                return 0;
            else
                return 1;
	}
}

class attrname
{
	var $attrnameid = 0;
	var $parentid = 0;
        var $parenttype = "";
        var $name = "";
        var $type = "";
        var $default = "";
        var $units = "";
        var $options='';
        var $desc = "";
        var $static = 0;
        var $control=0;         // 0 - editable for both templates and devices
        var $sort='0';          // 1 - read only within devices

	function __construct($ByNameID="0")
	{
		global $db;
		if (is_numeric($ByNameID) && $ByNameID > 0)
		{
                    $this->attrnameid = $ByNameID;
                    $query = $db->prepare('SELECT * FROM attnames WHERE attrnameid=?');
                    $query->execute(array($this->attrnameid));
                    $result = $query->fetchAll();
                    foreach($result as $attrname)
                    {
                        foreach($attrname as $key=>$val)
                            $this->$key=$val;
                    }
		}
	}
};
?>