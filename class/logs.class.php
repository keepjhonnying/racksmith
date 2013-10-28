<?php
class logs
{
	var $db;
	
	function __construct()
	{
		global $db;
		$this->db=$db;		
	}

	var $rows='0';

	function getAll($limit=25,$offset=0)
	{
		$return = array();

		$a = $this->db->prepare('SELECT * FROM `logs` ORDER BY eventTime DESC LIMIT :limit OFFSET :offset');
		$a->bindParam(':limit', $limit, PDO::PARAM_INT);
		$a->bindParam(':offset', $offset, PDO::PARAM_INT);
		$a->execute();
		$result = $a->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $log)
		{
			$newLog = new log;
			$newLog->logID = $log['logID'];
			$newLog->event = $log['event'];
			$newLog->eventType = $log['eventType'];
			$newLog->itemID = $log['itemID'];
			$newLog->previous = $log['previous'];
			$newLog->comment = $log['comment'];
			$newLog->userID = $log['userID'];
                        $newLog->timestamp = strtotime($log['eventTime']);
			$newLog->eventTime = date('h:ia '.$_SESSION['dateFormat'], $newLog->timestamp);
                        
                       // print_r($log);
			array_push($return, $newLog);
		}
		$this->rows = $return;
		return $return;		
	}
        
	function getByUser($userID,$limit=25,$offset=0)
	{
		$return = array();

		$a = $this->db->prepare('SELECT * FROM `logs` WHERE userID=:userID ORDER BY eventTime DESC LIMIT :limit OFFSET :offset');
                $a->bindParam(':userID', $userID, PDO::PARAM_INT);
		$a->bindParam(':limit', $limit, PDO::PARAM_INT);
		$a->bindParam(':offset', $offset, PDO::PARAM_INT);
		$a->execute();
		$result = $a->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $log)
		{
			$newLog = new log;
			$newLog->logID = $log['logID'];
			$newLog->event = $log['event'];
			$newLog->eventType = $log['eventType'];
			$newLog->itemID = $log['itemID'];
			$newLog->previous = $log['previous'];
			$newLog->comment = $log['comment'];
			$newLog->userID = $log['userID'];
                        $newLog->timestamp = strtotime($log['eventTime']);
			$newLog->eventTime = date('h:ia '.$_SESSION['dateFormat'], $newLog->timestamp);
                        
                       // print_r($log);
			array_push($return, $newLog);
		}
		$this->rows = $return;
		return $return;		
	}
	
	
	function getEvent($eventID)
	{
		$this->db->prepare("SELECT * FROM savedevents WHERE eventID=? LIMIT 1;");
		$this->db->execute(array($eventID));
		$value = $this->db->fetchAll();
		
		if(!$value[0]['logs'])
			return false;
		
		
		$string = $value[0]['logs'];
		

		$return = array();
		$this->db->query("SELECT * FROM logs where logID in ($string)");
		$result = $this->db->fetchAll();
		
		foreach($result as $log)
		{
			$newLog = new log;
			$newLog->logID = $log['logID'];
			$newLog->event = $log['event'];
			$newLog->eventType = $log['eventType'];
			$newLog->itemID = $log['itemID'];
			$newLog->previous = $log['previous'];
			$newLog->comment = $log['comment'];
			$newLog->userID = $log['userID'];
                        $newLog->timestamp = strtotime($log['eventTime']);
			$newLog->eventTime = date($_SESSION['dateFormat'].' H:i', $newLog->timestamp);
			array_push($return, $newLog);
		}
		$this->rows = $return;
		return $return;	
	}
	
	function getEventName($eventID)
	{
		$this->db->prepare("SELECT * FROM savedevents WHERE eventID=? LIMIT 1;");
		$this->db->execute(array($eventID));
		$value = $this->db->fetchAll();
		return stripslashes($value[0]['name']);
	}
	
	function deleteSavedEvent($eventID)
	{
		$this->db->prepare("DELETE FROM savedevents WHERE eventID=? LIMIT 1;");
		return $this->db->execute(array($eventID));
	}
	
	
	// When performing a filter this should always be completed first
	function filterByHour($hour)
	{
		$return = array();

		$this->db->prepare('SELECT * FROM logs where eventTime between CURRENT_TIMESTAMP()-interval ? HOUR and CURRENT_TIMESTAMP() ORDER BY eventTime DESC');
		$this->db->execute(array($hour));
		$result = $this->db->fetchAll();
		
		foreach($result as $log)
		{
			$newLog = new log;
			$newLog->logID = $log['logID'];
			$newLog->event = $log['event'];
			$newLog->eventType = $log['eventType'];
			$newLog->itemID = $log['itemID'];
			$newLog->previous = $log['previous'];
			$newLog->comment = $log['comment'];
			$newLog->userID = $log['userID'];
                        $newLog->timestamp = strtotime($log['eventTime']);
			$newLog->eventTime = date($_SESSION['dateFormat'].' H:i', $newLog->timestamp);
			array_push($return, $newLog);
		}
		$this->rows = $return;
		return $return;		
	}
	
	
	// Filters the $this->rows 
	// results are saved back to rows and also returned as array of instances
 	function filterByUser($userID)
	{
		$return=array();
		// No previous filter was in use so querythe DB and filter in the SQL
		if(!$this->rows)
		{
			$this->db->prepare('SELECT * FROM logs where userID=? ORDER BY eventTime DESC');
			$this->db->execute(array($userID));
			$result = $this->db->fetchAll();

			foreach($result as $log)
			{
				$newLog = new log;
			$newLog->logID = $log['logID'];
			$newLog->event = $log['event'];
			$newLog->eventType = $log['eventType'];
			$newLog->itemID = $log['itemID'];
			$newLog->previous = $log['previous'];
			$newLog->comment = $log['comment'];
			$newLog->userID = $log['userID'];
                        $newLog->timestamp = strtotime($log['eventTime']);
			$newLog->eventTime = date($_SESSION['dateFormat'].' H:i', $newLog->timestamp);
				array_push($return, $newLog);
			}
			$this->rows = $return;
			return $return;	
		}
		// Another filter must have been used, loop over its results and filter these
		else
		{
			foreach($this->rows as $log)
				if($log->userID == $userID)
					array_push($return,$log);	
			
			$this->rows = $return;
			return $return;
		}
      
	}
	
	// SEARCH
	// Filters the $this->rows or makes a query to generate it
	// results are saved back to rows and also returned as array of instances
 	function filterByTerm($searchTerm)
	{
            $return=array();
            // No previous filter was in use so querythe DB and filter in the SQL
            if(!$this->rows)
            {
                // split the searchterm up into keywords
                $keywords = preg_split("/[\s,]+/",$searchTerm);
                // take the first term for use later
                $initialTerm=$keywords[0];

                // take all terms after the first one
                $keywords = array_slice($keywords,1); //to limit add ,5?

                // for each term add an extra section to the SQL
                // also rewrite the value so that it is surrounded with % for SQL
                $addition='';
                foreach($keywords as $val=>$keyword)
                {
                        $addition .= " or event LIKE ?";
                        $keywords[$val]='%'.$keyword.'%';
                }

                // put the first term back into the query
                array_unshift($keywords,'%'.$initialTerm.'%');

                $this->db->prepare("SELECT * FROM logs where event LIKE ? $addition ORDER BY eventTime DESC");
                $this->db->execute($keywords);
                $result = $this->db->fetchAll();


                foreach($result as $log)
                {
                        $newLog = new log;
                        $newLog->logID = $log['logID'];
                        $newLog->event = $log['event'];
                        $newLog->eventType = $log['eventType'];
                        $newLog->itemID = $log['itemID'];
                        $newLog->previous = $log['previous'];
                        $newLog->comment = $log['comment'];
                        $newLog->userID = $log['userID'];
                        $newLog->timestamp = strtotime($log['eventTime']);
                        $newLog->eventTime = date($_SESSION['dateFormat'].' H:i', $newLog->timestamp);
                        array_push($return, $newLog);
                }
                $this->rows = $return;
                return $return;
        }
        // Another filter must have been used, loop over its results and filter these
        else
        {
                foreach($this->rows as $log)
                        if(preg_match("/$searchTerm/",$log->event))
                                array_push($return,$log);

                $this->rows = $return;
                return $return;
        }
      
	}


	// Saves an array of logIDs to the database used as work orders
	function saveEvent($name,$arrayOfIDs)
	{
		//$IDs=serialize($arrayOfIDs);
		$IDs=implode(",",$arrayOfIDs);
		$this->db->prepare("INSERT INTO savedevents (eventID,name,logs) VALUES('',?,?);");
		if($this->db->execute(array($name,$IDs)))
			return 1;
		else
			return 0;		
	}


	function listSavedEvents()
	{
            $this->db->prepare("select eventID,name from savedevents");
            $this->db->execute(array());
            return  $this->db->fetchAll();
	}
	
	// -------------------------------------new insert log
	function insert($newLog)
	{
            $this->db->prepare("INSERT INTO logs (logID,event,eventType,itemID,previous,comment,userID,eventTime) VALUES ('',?,?,?,?,?,?,now());");
            $this->db->execute(array($newLog->event,$newLog->eventType,$newLog->itemID,$newLog->previous,$newLog->comment,$_SESSION['userid']));
	}



//-------------------------------------------

	function addLog($event)
	{
		$this->db->prepare("INSERT INTO logs (event,userID) VALUES (?,?);");
		$this->db->execute(array($event,));
	}


};

class log
{
	var $logID;
	var $event='';
	var $eventType='';
	var $itemID='';
	var $previous='';
	var $comment='';
	var $userID='';
        var $timestamp;
	var $eventTime='';

	function user()
	{
		return new user($this->userID);
	}
};
?>