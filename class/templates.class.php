<?php
class templates
{
    var $db;
	
    function __construct()
    {
        global $db;
        $this->db=$db;
    }

    /* quickSearch($vendor,$modelName,$deviceTypeID)
     * Performs a quick lookup to determine if a template exists for a given system, returns the templateID
     * this check is used on the template import tool so we dont get duplicates
     */
    function quickSearch($name,$deviceTypeID)
    {
        if($deviceTypeID==0)
            return false;

        $return = array();
        $this->db->prepare('SELECT templateID FROM templates WHERE name=? AND deleted=0 AND deviceTypeID=?');
        $this->db->execute(array($name,$deviceTypeID));
        $result = $this->db->fetch();

        if($result && $result['templateID'])
            return $result['templateID'];
        else
            return false;
    }
/*
    function getByTrait($filterCategories)
    {
        if($deviceTypeID==0)
            return false;

        $return = array();
        $this->db->prepare('SELECT * FROM templates deleted=0 AND deviceTypeID=?');
        $this->db->execute(array($deviceTypeID));
        $result = $this->db->fetchAll();

        if($result)
            return $result;
        else
            return false;
    }
*/
    function getAll($deviceTypeID=0)
    {
        $return = array();
        if($deviceTypeID==0)
        {
            $this->db->prepare('SELECT * FROM templates LEFT JOIN attrvalues ON (attrvalues.parentID=templates.templateID AND attrvalues.attrnameid=12) GROUP BY templates.templateID order by templates.deviceTypeID;');
            $this->db->execute(array());
        }
        else
        {
            $this->db->prepare('SELECT * FROM templates LEFT JOIN attrvalues ON (attrvalues.parentType="template" AND attrvalues.parentID=templates.templateID AND attrvalues.attrnameid=12) WHERE templates.deviceTypeID=? GROUP BY templates.templateID order by templates.deviceTypeID;');
            $this->db->execute(array($deviceTypeID));
        }
        $result = $this->db->fetchAll();
        foreach($result as $item)
        {
            //echo "<pre>"; print_r($item);
            $newItem=new template;
            $newItem->name=$item['name'];
            $newItem->vendor=$item['value'];
            $newItem->templateID=$item['templateID'];
            $newItem->deleted=0;
            $newItem->deviceTypeID=$item['deviceTypeID'];
            array_push($return, $newItem);
        }

        if($result)
            return $return;
        else
            return false;
    }
    
    function getByDeviceType($deviceTypeID,$filter=0)
    {
        if($deviceTypeID==0)
            return false;

        $return = array();
        $this->db->prepare('SELECT * FROM templates WHERE deleted=0 AND deviceTypeID=?');
        $this->db->execute(array($deviceTypeID));
        $result = $this->db->fetchAll();
        foreach($result as $item)
        {
            $newItem=new template;
            $newItem->name=$item['name'];
            $newItem->templateID=$item['templateID'];
            $newItem->deleted=0;
            $newItem->deviceTypeID=$item['deviceTypeID'];
            array_push($return, $newItem);
        }


        if($result)
            return $return;
        else
            return false;
    }

    function getByCategory($traitID,$showDeleted=0)
    {
        if($traitID==0)
            return false;

        $return = array();
        if($showDeleted)
            $prepared = $this->db->prepare('SELECT templates.* FROM templates LEFT JOIN attrcategoryvalues ON (attrcategoryvalues.parentID=templates.templateID) WHERE attrcategoryvalues.categoryID=? GROUP BY templates.templateID;');
        else
            $prepared = $this->db->prepare('SELECT templates.* FROM templates LEFT JOIN attrcategoryvalues ON (attrcategoryvalues.parentID=templates.templateID) WHERE templates.deleted=0 AND attrcategoryvalues.categoryID=? GROUP BY templates.templateID;');
        $prepared->execute(array($traitID));
        $result = $this->db->fetchAll();
        
        foreach($result as $item)
        {
            $newItem=new template;
            $newItem->name=$item['name'];
            $newItem->templateID=$item['templateID'];
            $newItem->deleted=$item['deleted'];
            $newItem->deviceTypeID=$item['deviceTypeID'];
            $return[$item['deviceTypeID']][]=$newItem;
        }

        if($result)
            return $return;
        else
            return false;
    }


    function insert($newTemplate)
    {
        $this->db->prepare("INSERT INTO templates (templateID,name,background,deleted,deviceTypeID)
        VALUES ('',?,?,0,?);");
        $created = $this->db->execute(array($newTemplate->name, $newTemplate->background,$newTemplate->deviceTypeID));

        $this->db->query("SELECT LAST_INSERT_ID()");
        $result = $this->db->fetchAll();
        $newTemplateID = $result[0]['LAST_INSERT_ID()'];
        $newTemplate->templateID = $newTemplateID;
        return $newTemplateID;
    }

    function createTemplatePorts($portsArray)
    {
        $result=array();
        // loop over each type of port and make an entry for it
        $prepared = $this->db->prepare("INSERT INTO templateports values('',?,?,?,?,?,?);");
        foreach($portsArray as $entry)
        {
            $result[]=$prepared->execute(array($entry['templateID'],$entry['portTypeID'],$entry['isJoin'],$entry['bandwidth'],$entry['count'],$entry['disporder']));
        }
        if(in_array("0",$result))
            return false;
        else
            return true;
    }

    function update($template)
    {
        $this->db->prepare("UPDATE templates SET name = ?,deviceTypeID = ?,background=? WHERE templateID = ?;");
        $updated = $this->db->execute(array($template->name,$template->deviceTypeID,$template->background,$template->templateID));
        
        return $updated;
    }

    function delete($TemplateID)
    {
        $this->db->prepare("UPDATE templates SET deleted=1 WHERE templateID = ?;");
        return $this->db->execute(array($TemplateID));
    }
};

class template
{
    var $db;
    var $templateID;
    var $deleted=0;
    var $deviceTypeID=0;
    var $name=0;
    var $background='';
    var $attributes;
    var $categories=array();

    // Device type can be defined as floor, rack, patch, switch.......
    // it includes basic options like (canTemplate, floorDevice, rackDevice)....
    // doubt we'll need to access this much
    function __construct($byTemplateID='0')
    {
        global $db;
        $this->db=$db;

        if(is_numeric($byTemplateID) && $byTemplateID > 0)
        {
            $query = $this->db->prepare('SELECT * FROM templates WHERE templateID = ? LIMIT 1;');
            $query->execute(array($byTemplateID));
            $template = $query->fetch(PDO::FETCH_ASSOC);

            $this->templateID = $template['templateID'];
            $this->name = $template['name'];
            $this->background=$template['background'];
            $this->deleted = $template['deleted'];
            $this->deviceTypeID = $template['deviceTypeID'];
        }
    }

    function fillMeta($categoryFilter=0)
    {
        if(!$categoryFilter)
            return false;

        $meta=new attrnames;
        $this->attributes = $meta->getByParent($this->templateID,'template',$categoryFilter,"1");
    }

    function getMeta($nameID,$categoryID)
    {
        if($categoryID)
        {
            if(isset($this->categories[$categoryID][$nameID]))
                return $this->categories[$categoryID][$nameID]->value;
        }
        else
        {
            if(isset($this->attributes[$nameID]))
                return $this->attributes[$nameID]->value;
        }
        return "";
    }

    function fillCategories($getNames=0,$getValues=0,$categoryFilter=0)
    {
        $cats=new attrcategoryvalues;
        $this->categories = $cats->getByParent($this->templateID,'template',$getNames,$getValues,$categoryFilter);
    }
    
    function numPorts()
    {
            $query = $this->db->prepare('SELECT sum(count) FROM templatePorts WHERE templateID=?;');
            $query->execute(array($this->templateID));
            return $query->fetch(PDO::FETCH_COLUMN,0);        
    }
    
    // a quick hack to speed up performance when listing a large number of templates
    function getBriefDetails()
    {
            $query = $this->db->prepare('SELECT attrnameid,value FROM attrvalues WHERE (attrnameid=5||attrnameid=12) AND parentID=? AND parentType="template" ORDER BY attrnameid;');
            $query->execute(array($this->templateID));
            return $query->fetchAll();
    }


    function hasCategory($categoryID)
    {
        if(isset($this->categories[$categoryID]))
            return true;
        else
            return false;
    }
};
?>