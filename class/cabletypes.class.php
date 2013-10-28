<?php
class cableTypes
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
        if(count($this->rows) <= 0)
        {
            $query = $this->db->prepare('SELECT * FROM cabletypes ORDER BY cableTypeID');
            $query->execute();
            $result = $query->fetchAll(); //PDO::FETCH_NAMED

            $return = array();
            foreach($result as $cableType)
            {
                $newType = new cableType;
                $newType->cableTypeID = $cableType['cableTypeID'];
                $newType->name = $cableType['name'];
                $newType->isPower = $cableType['isPower'];
                $return[$cableType['cableTypeID']]=$newType;
            }
            $this->rows = $return;
            return $return;
        }
        else
            return $this->rows;
    }

    /*
    function getByCategory($categoryID)
    {
        if(count($this->rows) <= 0)
        {
            $query = $this->db->prepare('SELECT * FROM cabletypes WHERE category=? ORDER BY cableTypeID');
            $query->execute($groupID);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            $return = array();
            foreach($result as $cableType)
            {
                // use the primary key as our array index
                // we dont need to worry about sequence with these
                $newType = new cableType;
                $newType->cableTypeID = $cableType['cableTypeID'];
                $newType->name = $cableType['name'];
                $newType->isPower = $cableType['isPower'];
                $return[$cableType['cableTypeID']]=$newType;
            }
            $this->rows = $return;
            return $return;
        }
        else
            return $this->rows;
    }*/

    /* check if the category exists and enable it or just crete from scratch */
    function newCategory($name,$type)
    {
        $name = preg_replace('/[^0-9a-zA-z-_ ]/','', $name);
        $type = preg_replace('/[^0-9]/','', $type);
        
        $query = $this->db->prepare('SELECT * FROM cablecategories WHERE name=? LIMIT 1');
        $query->execute(array($name));
        $result = $query->fetch();
        if($result)
        {
           if($this->reactivateCategory($result['categoryID']))
               return 1;
           else
               return 0;

        }
        else
        {
            $query = $this->db->prepare('INSERT INTO cablecategories VALUES("",?,?,1);');
            if($query->execute(array($name,$type)))
                return 1;
            else
                return 0;
        }
    }

    /* check if the category exists and enable it or just crete from scratch */
    function newCableType($name,$type)
    {
        $name = preg_replace('/[^0-9a-zA-z-_ ]/','', $name);
        $type = preg_replace('/[^0-9]/','', $type);

        /* for now we only have power and data items
         * this just sets a isPower flag in the DB
         */
        if($type==2)
            $type=1;
        else
            $type=0;

        $query = $this->db->prepare('INSERT INTO cabletypes VALUES("",?,?);');
        if($query->execute(array($name,$type)))
            return 1;
        else
            return 0;
    }

    /* toggle the enable ID for a category */
    function deactivateCategory($catID)
    {
        $catID = preg_replace('/[^0-9]/','', $catID);
        $query = $this->db->prepare('UPDATE cablecategories SET enabled=0 WHERE categoryID=? LIMIT 1;');
        if($query->execute(array($catID)))
            return 1;
        else
            return 0;
    }

    /* toggle the enable ID for a category */
    function reactivateCategory($catID)
    {
        $catID = preg_replace('/[^0-9]/','', $catID);
        $query = $this->db->prepare('UPDATE cablecategories SET enabled=1 WHERE categoryID=? LIMIT 1;');
        if($query->execute(array($catID)))
            return 1;
        else
            return 0;
    }

    /* toggle the enable ID for a category */
    function addCableTypeToCategory($catID,$cableTypeID)
    {
        $catID = preg_replace('/[^0-9]/','', $catID);
        $cableTypeID = preg_replace('/[^0-9]/','', $cableTypeID);
        $query = $this->db->prepare('INSERT INTO cabletypejoins VALUES ("",?,?);');
        $created = $query->execute(array($catID,$cableTypeID));
        
        $this->db->query("SELECT LAST_INSERT_ID()");
        $result = $this->db->fetch();

        if ($created && $result['LAST_INSERT_ID()'])
            return $result['LAST_INSERT_ID()'];
        else
            return 0;
    }

    /* toggle the enable ID for a category */
    function removeCable($entryID)
    {
        $entryID = preg_replace('/[^0-9]/','', $entryID);
        $query = $this->db->prepare('DELETE FROM cabletypejoins WHERE entryID=? LIMIT 1;');
        if($query->execute(array($entryID)))
            return 1;
        else
            return 0;
    }


    function getCategories()
    {
        $query = $this->db->prepare('SELECT * FROM cablecategories');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function getCategoryEntries($catID)
    {
        $query = $this->db->prepare('SELECT * FROM cabletypejoins WHERE categoryID=?;');
        $query->execute(array($catID));
        $result = $query->fetchAll();
        return $result;
    }
}

class cableType
{
    var $db;
    var $cableTypeID='';
    var $name='';
    var $isPower='';
    var $group='';

    function __construct($cableTypeID='0')
    {
        global $db;
        $this->db=$db;
        $this->floorID = $cableTypeID;

        if (is_numeric($this->cableTypeID) && $this->cableTypeID > 0)
        {
            $query = $this->db->prepare('SELECT * FROM cabletypes WHERE cableTypeID = ?');
            $query->execute(array($this->cableTypeID));
            $result = $query->fetchAll();

            foreach($result as $cable)
            {
                $this->cableTypeID = $cable['cableTypeID'];
                $this->name = $cable['name'];
                $this->isPower = $cable['isPower'];
            }
        }
    }
};
?>