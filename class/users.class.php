<?php

class users
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
                $this->db->prepare('SELECT * FROM users');
                $this->db->execute(array());
                $result = $this->db->fetchAll();

                $return = array();
		
                foreach($result as $user)
                {
                    $newUser = new user;
                    $newUser->userID = $user['userID'];
                    $newUser->UserName = $user['userName'];
                    $newUser->external = $user['external'];
                    $newUser->Password = $user['password'];
                    $newUser->Email = $user['email'];
                    $newUser->Phone = $user['phone'];
                    $newUser->metric=$user['metric'];
                    $newUser->dateformat=$user['dateformat'];

                    array_push($return, $newUser);
                }
                $this->rows = $return;
                return $return;
            }
            else
                return $this->rows;
	}

	function getUser($UserID)
	{
            $return = false;
            if (count($this->rows) > 0)
            {
                foreach($this->rows as $user)
                {
                    if ($user->UserID == $UserID)
                    {
                        $newUser = new user;
                        $newUser->userID = $user['userID'];
                        $newUser->UserName = $user['userName'];
                        $newUser->external = $user['external'];
                        $newUser->Password = $user['password'];
                        $newUser->Email = $user['email'];
                        $newUser->Phone = $user['phone'];
                        $newUser->metric=$user['metric'];
                        $newUser->dateformat=$user['dateformat'];
                        array_push($return, $newUser);
                    }
                }
                return $return;
            }
            else
            {
                $this->db->prepare('SELECT userID,userName,external,password,email,phone,metric,dateformat FROM users WHERE userID = ?');
                $this->db->execute(array($UserID));
                $result = $this->db->fetchAll();

                $return = array();

                foreach($result as $user)
                {
                    $newUser = new user;
                    $newUser->userID = $user['userID'];
                    $newUser->UserName = $user['userName'];
                    $newUser->external = $user['external'];
                    $newUser->Password = $user['password'];
                    $newUser->Email = $user['email'];
                    $newUser->Phone = $user['phone'];
                    $newUser->metric=$user['metric'];
                    $newUser->dateformat=$user['dateformat'];

                    array_push($return, $newUser);
                }
                return $return;
            }
            return $return;
	}

	function insert($newUser)
	{
            $this->db->prepare("INSERT INTO users (userID,userName,external,password,email,phone,sessionKey,resetRequestKey,metric,dateformat) VALUES ('',?,?,?,?,?,'','',?,?);");
            if($this->db->execute(array($newUser->UserName,$newUser->external,$newUser->Password,$newUser->Email,$newUser->Phone,$newUser->metric,$newUser->dateformat)))
            {
                $this->db->query("SELECT LAST_INSERT_ID()");
                $result = $this->db->fetch();
                return $result['LAST_INSERT_ID()'];
            }
            else
                return 0;
	}

	function update($newUser)
	{
            $this->db->prepare("UPDATE users SET userName = ?,external=?,password = ?,email = ?,phone = ?,metric=?,dateformat=? WHERE userID = ?;");
            return $this->db->execute(array($newUser->UserName,$newUser->external,$newUser->Password,$newUser->Email,$newUser->Phone,$newUser->metric,$newUser->dateformat,$newUser->userID));
	}

	function delete($UserID)
	{
            $this->db->prepare("DELETE FROM users WHERE userID = ?;");
            return $this->db->execute(array($UserID));
	}

	function invalidateAccount($UserID)
	{
            $this->db->prepare("UPDATE users SET password='' WHERE userID = ?;");
            return $this->db->execute(array($UserID));
	}

	function login($username, $password)
	{
            $userID = 0;
            $this->db->prepare('SELECT * FROM users WHERE userName = ? AND password = ? AND external=0 LIMIT 1');
            $this->db->execute(array($username,md5($password)));

            $result = $this->db->fetch();
            if($result['userID'])
                return $result['userID'];
            else
                return false;
	}
	
	
	function login_ldap($username, $password)
	{
            // bypass AD for the root user
            if($username == 'root')
                return $this->login($username,$password);
			
            $config = new config;

            // get connection values from DB
            $basedn         = $config->returnItem("ldap_basedn");
            $prefix         = $config->returnItem("ldap_prefix");
            $postfix        = $config->returnItem('ldap_postfix');
            $requiredGroup	= $config->returnItem("ldap_group");
            $ldap_server	= $config->returnItem("ldap_server");
            $ldap_field     = $config->returnItem("ldap_field");

            // connect but detect ldap/s
            if($config->returnItem("ldaps_enabled"))
                $adcon = ldap_connect("ldaps://".$ldap_server);
            else
                $adcon = ldap_connect("ldap://".$ldap_server);

            ldap_set_option($adcon, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($adcon, LDAP_OPT_REFERRALS, 0);

            // Bind to the directory server.
            if($prefix && $basedn)
                $bind_rdn=$prefix."=".$username.",".$basedn;
            else
                $bind_rdn=$username.$postfix;

            if(@!ldap_bind($adcon,$bind_rdn,$password))
                return false;

            // Perform the search and grab the users groups
            $searchres = @ldap_search($adcon,$basedn,"($ldap_field=$username)",array("memberof"));
            $entries = ldap_get_entries($adcon, $searchres);

            // loop over the returned groups and find our required
            if($entries[0]['memberof']['count']>0)
            {
                for($i=0;$i<$entries[0]['memberof']['count'];$i++)
		{
                    // we dont want the full DN so strip out just the group name
                    $groupComponents=explode(',',$entries[0]['memberof'][$i]);
                    $group=substr($groupComponents[0],3,strlen($groupComponents[0]));

                    if(preg_match('/^'.$requiredGroup.'/i',$group))
                    {
                        // We have a match so check if an LDAP user already exists
                        $this->db->prepare('SELECT userID FROM users WHERE userName = ? AND external="ldap" LIMIT 1');
                        $this->db->execute(array($username));

                        $result = $this->db->fetch();
                        if($result)
                        {
                            ldap_unbind($adcon);
                            return $result['userID'];
                        }
                        // no ldap user exists so create one
                        else
                        {
                            $user = new user;
                            $user->userID = '';
                            $user->UserName = $username;
                            $user->external = 'ldap';
                            $user->Password = '';
                            $user->Email = '';
                            $user->Phone = '';
                            $user->metric=1;
                            $user->dateformat="d-m-Y";

                            $userID=$this->insert($user);
                            if(is_numeric($userID) && $userID>0)
                            {
                                ldap_unbind($adcon);
                                return $userID;
                            }
                            else
                            {
                                ldap_unbind($adcon);
                                return false;
                            }
                        }
                    }
		}
            }
            else
            {
                ldap_unbind($adcon);
                return false;
            }
            
            return false;
	}


	function checkSessionKey($key)
	{
            // ensure we have a valid md5 key
            $key = preg_replace('/^[A-Za-z0-9]/', '', $key);
            if(strlen($key)==32)
            {
                $this->db->prepare('SELECT userID,userName,sessionKey FROM users WHERE sessionKey = ? LIMIT 1');
                $this->db->execute(array($key));

                $result = $this->db->fetch();
                if($result)
                    return $this->getUser($result['userID']);
            }
            return 0;
	}
	
	function searchUser($searchString, $key='username',$ldap=0)
	{
            // Check if the user is searching a valid item else err
            $acceptedKeys=array("username","email","requestKey");
            if(!(in_array($key,$acceptedKeys)))
                    return 0;

            switch($key) {
            case "email":
                $this->db->prepare("SELECT userID,external FROM users WHERE email=? LIMIT 1");
                break;
            case "username":
                $this->db->prepare("SELECT userID,external FROM users WHERE userName=? LIMIT 1");
                break;
            case "requestKey":
                $this->db->prepare("SELECT userID,external FROM users WHERE resetRequestKey=? LIMIT 1");
                break;
            }
            $this->db->execute(array($searchString));

            // Retreieve the userID of the returned user
            $result = $this->db->fetch();

            // if LDAP is set we want to eliminate results that come from ldap users
            // mainly used to check when creating users

		if($result)
                    if(!$ldap)
                        $userID = $result['userID'];
                    else
                    {
                        if($result['external']==0)
                            $userID = $result['userID'];
                        else
                            return 0;
                    }
		else
			return 0;

		// It means another query but generate an object for this user and return it
		$user = new user($userID);
		return $user;
	}
	
	function setSessionKey($userID,$key)
	{
            $this->db->prepare("UPDATE users SET sessionKey = ? WHERE userID = ? LIMIT 1;");
            $this->db->execute(array($key,$userID));
	}
	
	
}

class user
{
	var $db;

	var $userID;
	var $UserName;
	var $external;
	var $Password;
	var $Email;
	var $Phone;
        var $metric;
        var $dateformat;

	function __construct($ByUserID='0')
	{
            global $db;
            $this->db=$db;

            $this->userID = $ByUserID;
            if (is_numeric($this->userID) && $this->userID > 0)
            {
                $this->db->prepare('SELECT userID,userName,external,password,email,phone,metric,dateformat FROM users WHERE userID = ? LIMIT 1');
                $this->db->execute(array($ByUserID));
                $result = $this->db->fetch();

                if(count($result) > 0)
                {
                    $this->UserName = $result['userName'];
                    $this->external = $result['external'];
                    $this->Password = $result['password'];
                    $this->Email = $result['email'];
                    $this->Phone = $result['phone'];
                    $this->metric = $result['metric'];
                    $this->dateformat = $result['dateformat'];
                }
            }
	}
	
	function setPasswordRequestKey($key)
	{
            $this->db->prepare("UPDATE users SET resetRequestKey = ? WHERE userID=? LIMIT 1;");
            $this->db->execute(array($key,$this->userID));
	}
	
	
	function setSessionKey($key)
	{
            $this->db->prepare("UPDATE users SET sessionKey = ? WHERE userID = ? LIMIT 1;");
            $this->db->execute(array($key,$this->userID));
	}
	
	function addRackToSession($rackID,$userID=0)
	{
            if(!$userID)
                $userID=$_SESSION['userid'];
			
            // check to see if an item is already in a session
            // optimise this but make sure we maintain sqlite/misc support
            $this->db->prepare("select * from sessionitems WHERE type='rack' AND itemID=? AND userID=?");
            $this->db->execute(array($rackID,$userID));
            $result = $this->db->fetchAll();
            if(!$result)
            {
                $this->db->prepare("INSERT INTO sessionitems VALUES('',?,?,'rack');");
                $this->db->execute(array($userID,$rackID));
            }
	}
	
	function listSessionItems($type=0)
	{
            if($type)
            {
                $this->db->prepare('SELECT * FROM sessionitems WHERE userID = ? AND type=? order by itemID;');
                $this->db->execute(array($this->userID,$type));
            }
            else
            {
                $this->db->prepare('SELECT * FROM sessionitems WHERE userID = ? order by itemID;');
                $this->db->execute(array($this->userID));
            }
            $result = $this->db->fetchAll();
            return $result;
	}
	
	function removeRackFromSession($rackID,$userID=0)
	{
            if(!$userID)
                $userID=$_SESSION['userid'];
            $this->db->prepare("DELETE FROM sessionitems WHERE userID=? AND itemID=?;");
            $this->db->execute(array($userID,$rackID));
	}
};
?>