<?php
/*
 * Attachements class
 * manages file uploads which link back to assets within the system
 * stores basic meta data in the DB for easier management
 *
 * last updated 20100902 V0.9
 */
class attachments {
    var $db;
    var $enabled;
    var $basePath;
    var $acceptedAssetTypes;
    var $maxUploadSize;         // retrieved from config, size in MB
    var $config;                // stores the config object for use later

    private $users;             // internal val to store user details when calling getObjects

    // prepare DB, check if uploads r enabled & get other values
    function __construct()
    {
        global $db;
        $this->db=$db;
        $this->users=array();

        // retrieve state of uploads and if enabled find the storage path
        $this->config = new config;
        $this->enabled = $this->config->returnItem("attachments_enabled");
        if($this->enabled)
        {
            $this->basePath=$this->config->returnItem("attachment_path");
            $this->acceptedAssetTypes=array('device','rack','room');
        }
    }
    

    // Uploads an object to the system and places an entry in the DB
    function placeObject($postedObject,$assetID,$assetType,$meta)
    {
        if($this->enabled && is_numeric($assetID) && in_array($assetType, $this->acceptedAssetTypes))
        {
            // Check to see if the file is lower than our limit from config
            $this->maxUploadSize=$this->config->returnItem("attachment_maxUpload");
            $filesize=$postedObject['file']['size'];
            $filename=preg_replace('/[^0-9A-Za-z]_-/','', $postedObject['file']['name']);
            if($postedObject['file']['size']<($this->maxUploadSize*1024*1024))
            {
                if(!is_dir($this->basePath.$assetID.$assetType."/"))
                    mkdir($this->basePath.$assetID.$assetType."/");

                    if(!file_exists($this->basePath.$assetID.$assetType."/".$filename))
                    {
                        if(is_uploaded_file($postedObject['file']['tmp_name']))
                        {
                            if(move_uploaded_file($postedObject['file']['tmp_name'], $this->basePath.$assetID.$assetType."/".$filename))
                            {
                                $query = $this->db->prepare('INSERT INTO `attachments` VALUES("",?,?,?,?,?,?);');
                                $query->execute(array($assetID,$assetType,$meta['name'],$filename,$_SESSION['userid'],date("Y-m-d")));

                                $this->db->query("SELECT LAST_INSERT_ID()");
                                $result = $this->db->fetchAll();
                                return array(array(1,$result[0]['LAST_INSERT_ID()']));
                            }
                            else
                                return array(array(0,'unable_to_upload_file'));
                        }
                        else
                            return array(array(0,'invalid_upload'));
                    }
                    else
                        return array(array(0,'duplicate_name_exists'));
            }
            else
                return array(array(0,'exceeds_file_size'));
        }
        else
            return array(array(0,'invalid_asset'));
    }
    

    function getObjects($assetID,$assetType)
    {
        // get and cache a list of users
        // means we dont need individual lookups for each file later on
        if(count($this->users)==0)
        {
            $users = new users;
            foreach($users->getAll() as $user)
                $this->users[$user->userID]=$user->UserName;
        }

        // check uploads are enabled and the asset is valid
        if($this->enabled && is_numeric($assetID) && in_array($assetType, $this->acceptedAssetTypes))
        {
            $return=array();
            $query = $this->db->prepare('SELECT * FROM `attachments` WHERE assetID=? AND assetType=?;');
            $query->execute(array($assetID,$assetType));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if($result)
            {
                // fix up the data by resolving the userID and checking for a valid date
                foreach($result as $item)
                {
                    $return[$item['objectID']]=$item;
                    $return[$item['objectID']]['user']=$this->users[$item['userID']];
                    if($item['date']=="0000-00-00")
                        $return[$item['objectID']]['date']="unknown";
                }
            }
            return $return;
        }
        else
            return 0;
    }

    // retrieves an object 
    function getObject($objectID)
    {
        // check uploads are enabled and the asset is valid
        if($this->enabled && is_numeric($objectID))
        {
            $query = $this->db->prepare('SELECT * FROM attachments WHERE objectID=? LIMIT 1;');
            $query->execute(array($objectID));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $result['filepath']=$this->basePath.$result['assetID'].$result['assetType']."/".$result['filename'];
            return $result;
        }
        else
            return 0;
    }

    
    function delObject($objectID)
    {
        // check uploads are enabled and the asset is valid
        if($this->enabled && is_numeric($objectID))
        {
            // get details on the file as we were only given ID
            $query = $this->db->prepare('SELECT * FROM attachments WHERE objectID=? LIMIT 1;');
            $query->execute(array($objectID));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            if($result['0']['objectID'])
            {
                // check it exists and we can delete it
                if(file_exists($this->basePath.$result[0]['assetID'].$result[0]['assetType']."/".$result[0]['filename']))
                {
                    if(unlink($this->basePath.$result[0]['assetID'].$result[0]['assetType']."/".$result[0]['filename']))
                    {
                        // once del'd from filesys we can clear the DB
                        $query = $this->db->prepare('DELETE FROM attachments WHERE objectID=? LIMIT 1;');
                        return $query->execute(array($result['0']['objectID']));
                    }
                    else
                        return array(0,'unable_to_delete');
                }
                else
                    return array(0,'file_doesnt_exist');
            }
            else
                return array(0,'cannot_find_in_db');
        }
        else
            return array(0,'uploads_disabled_badID');
    }
}
?>