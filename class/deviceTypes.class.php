<?php
class deviceTypes
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

        $this->db->query('SELECT * FROM devicetypes ORDER BY name');
        $result = $this->db->fetchAll();

        foreach($result as $deviceType)
        {
            $newDeviceType = new deviceType;
            $newDeviceType->deviceTypeID = $deviceType['deviceTypeID'];
            $newDeviceType->name = $deviceType['name'];
            $return[$newDeviceType->deviceTypeID]=$newDeviceType;
        }
        $this->rows = $return;
        return $this->rows;
    }

    function insert($newDeviceType)
    {
        $this->db->prepare("INSERT INTO devicetypes (name) VALUES (?);");
        $this->db->execute(array($newDeviceType->name));
    }

    function returnForDevice()
    {
        

    }
};

class deviceType
{
    var $deviceTypeID;
    var $name;

    var $db;

    function __construct($ByDeviceTypeID='0')
    {
        global $db;
        $this->db=$db;

        $this->deviceTypeID = $ByDeviceTypeID;
        $this->name = "";

        if (is_numeric($this->deviceTypeID) && $this->deviceTypeID > 0)
        {
            $query = $this->db->prepare('SELECT * FROM devicetypes WHERE deviceTypeID = ?');
            $query->execute(array($this->deviceTypeID));
            $result = $query->fetchAll();

            foreach($result as $deviceType)
            {
                $this->deviceTypeID = $deviceType['deviceTypeID'];
                $this->name = $deviceType['name'];
            }
        }
    }
};
?>