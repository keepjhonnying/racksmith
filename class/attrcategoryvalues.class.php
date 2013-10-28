<?php

class attrcategoryvalues
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
                $query = $this->db->prepare('SELECT * FROM attrcategoryvalues');
                $query->execute();
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $item)
                {
                    $newItem = new attrcategoryvalue();
                    $newItem->attcatid = $item['attcatid'];
                    $newItem->parentID = $item['parentID'];
                    $newItem->parentType = $item['parentType'];
                    $newItem->categoryID = $item['categoryID'];
                    array_push($return, $newItem);
                }
                $this->rows = $return;
                return $return;
            }
            else
                    return $this->rows;
	}


        function getByParent($parentID,$parentType,$getNames=0,$getValues=0,$categoryFilter=0)
	{
            $filterSelection="";
            $return = array();
            // Generate some custom SQL so we can restrict the number of categories we return
            // we do this as we may retreive values from within and want to reduce the resultset
            if(is_array($categoryFilter))
            {
                $categorySections=array();
                foreach($categoryFilter as $IDs)
                    $categorySections[]=' attrcategoryvalues.categoryID="'.$IDs.'"';
                $filterSelection="(".implode(" || ",$categorySections).") AND ";
            }
            
            if($getNames==0)
            {
                $query = $this->db->prepare('SELECT * FROM attrcategoryvalues WHERE attrcategoryvalues.parentID=? AND attrcategoryvalues.parentType=?;');
                $query->execute(array($parentID,$parentType));

                $lines=$query->fetchAll(PDO::FETCH_CLASS, 'attrcategoryvalue');
                foreach($lines as &$result)
                {
                    $return[$result->categoryID]="";
                }
                $query->closeCursor();

                return $return;
            }
            else
            {
                // check if we want to grab values while we get this information, if so its an extra join
                if(!$getValues)
                {
                    $query = $this->db->prepare('SELECT * FROM attrcategoryvalues LEFT JOIN attrnames ON attrnames.parenttype = "attrcategory" AND attrnames.parentid = attrcategoryvalues.categoryID WHERE '.$filterSelection.' attrcategoryvalues.parentID=? AND attrcategoryvalues.parentType=?;');
                    $query->execute(array($parentID,$parentType));
                }
                else
                {
                    
                    // changed 'AND attrvalues.parentID=? AND attrvalues.parentType=? ' from being part of the on join to the where clause
                    // this way we detect any template changes
                    // we should review this // AND (attrvalues.parentID=null || (attrvalues.parentID=? AND attrvalues.parentType=?))
                    $query = $this->db->prepare('SELECT * FROM attrcategoryvalues LEFT JOIN attrnames ON attrnames.parenttype = "attrcategory" AND attrnames.parentid = attrcategoryvalues.categoryID LEFT JOIN attrvalues ON (attrvalues.attrnameid = attrnames.attrnameid AND attrvalues.parentID=? AND attrvalues.parentType=?) WHERE '.$filterSelection.' (attrcategoryvalues.parentID=? AND attrcategoryvalues.parentType=?);');
                    $query->execute(array($parentID,$parentType,$parentID,$parentType));
                }

                // loop over the results and generate array[<categoryID>][<nameID>]->(object)
                // this object is attrcategoryvalue but includes values from attrnames and if getValues==1 the attrvalues object
                
                //$lines=$query->fetchAll(PDO::FETCH_CLASS, 'attrcategoryvalue');
                //foreach($lines as &$result)
                
                while($result=$query->fetchObject('attrcategoryvalue'))
                {
                    if(!$result->attrnameid)
                    {
                        $return[$result->categoryID]="";
                        continue;
                    }
                    $return[$result->categoryID][$result->attrnameid]=$result;
                }
                $query->closeCursor();
                
                return $return;
            }
	}


	function insert($item)
	{
            $this->db->prepare("INSERT INTO attrcategoryvalues (attrcatvalid,parentID,parentType,categoryID) VALUES ('',?,?,?);");
            $created = $this->db->execute(array($item->parentID,$item->parentType,$item->categoryID));

            $this->db->query("SELECT LAST_INSERT_ID()");
            $result = $this->db->fetchAll();

            if ($created)
                return $result[0]['LAST_INSERT_ID()'];
            else
                return $created;
	}

	function update($attrcatvalue)
	{
            $this->db->prepare("UPDATE attrcategoryvalues SET parentID=?,parentType,categoryID=? WHERE attcatid = ?;");
            return $this->db->execute(array($attrcatvalue->parentID,$attrcatvalue->parentType,$attrcatvalue->categoryID,$attrcatvalue->attcatid));
	}

	function delete($attrcatvalue)
	{
            $this->db->prepare("DELETE FROM attrcategoryvalues WHERE attcatid = ?;");
            $this->db->execute(array($attrcatvalid));
	}

        function insertMultiple($entries)
        {
            $status=array();
            $prepared = $this->db->prepare("INSERT INTO attrcategoryvalues (attrcatvalid,parentID,parentType,categoryID) VALUES ('',?,?,?);");

            foreach($entries as &$entry)
                $status[]=$prepared->execute(array($entry->parentID,$entry->parentType,$entry->categoryID));
            
            if(in_array('0',$status))
                return 0;
            else
                return 1;
        }

        function deleteMultiple($parentID,$parentType,$ids=0)
        {
            $status=array();
            if(is_array($ids))
            {
                $prepared = $this->db->prepare("DELETE FROM attrcategoryvalues WHERE categoryID=? AND parentID=? and parentType=? LIMIT 1;");

                foreach($ids as $id)
                    $status[]=$prepared->execute(array($id,$parentID,$parentType));
            }
            else
            {
                $prepared = $this->db->prepare("DELETE FROM attrcategoryvalues WHERE parentID=? and parentType=? LIMIT 1;");
                $status[]=$prepared->execute(array($parentID,$parentType));
            }
            if(in_array('0',$status))
                return 0;
            else
                return 1;
        }
}

class attrcategoryvalue
{
	var $attrcatvalid = 0;
        var $parentID = "";
        var $parentType = "";
        var $categoryID = "";

	function __construct($ByCategoryID='0')
	{
		global $db;
		

		
		if (is_numeric($ByCategoryID) && $ByCategoryID > 0)
		{
                    $this->attcatid = $ByCategoryID;
                    $query = $db->prepare('SELECT * FROM attrcategoryvalues WHERE attrcatvalid=?');
                    $query->execute(array($this->attcatid));
                    $result = $query->fetchAll();
                    foreach($result as $attrcategoryvalues)
                    {
                        $this->parentID = $attrcategoryvalues['parentID'];
                        $this->parentType = $attrcategoryvalues['parentType'];
                        $this->categoryID = $attrcategoryvalues['categoryID'];
                    }
		}
	}
};
?>
