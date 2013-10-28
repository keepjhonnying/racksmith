<?php
//require "config.php";
if(!defined('RACKSMITH_PATH'))
    define("RACKSMITH_PATH", "./");

require RACKSMITH_PATH."class/pdo.php";

$theme='default';
global $db;
$db = new DB;

include RACKSMITH_PATH."class/deviceTypes.class.php";
include RACKSMITH_PATH."class/floors.class.php";
include RACKSMITH_PATH."class/buildings.class.php";
include RACKSMITH_PATH."class/owners.class.php";
include RACKSMITH_PATH."class/users.class.php";
include RACKSMITH_PATH."class/logs.class.php";
include RACKSMITH_PATH."class/cabinets.class.php";
include RACKSMITH_PATH."class/shelves.class.php";
include RACKSMITH_PATH."class/rooms.class.php";
include RACKSMITH_PATH."class/layoutItems.class.php";
include RACKSMITH_PATH."class/racks.class.php";
include RACKSMITH_PATH."class/templates.class.php";
include RACKSMITH_PATH."class/devices.class.php";
include RACKSMITH_PATH."class/ports.class.php";
include RACKSMITH_PATH."class/joins.class.php";
include RACKSMITH_PATH."class/cabletypes.class.php";
include RACKSMITH_PATH."class/cables.class.php";
include RACKSMITH_PATH."class/config.class.php";
include RACKSMITH_PATH."class/licences.class.php";
include RACKSMITH_PATH."class/attachments.class.php";
include RACKSMITH_PATH."class/attrcategory.class.php";
include RACKSMITH_PATH."class/attrnames.class.php";
include RACKSMITH_PATH."class/attroptions.class.php";
include RACKSMITH_PATH."class/attrvalues.class.php";
include RACKSMITH_PATH."class/attrcategoryvalues.class.php";

if (!isset($_SESSION['userid']) && !strstr($_SERVER['PHP_SELF'],'login.php'))
{
	$users = new users;

	// If a cookie is set and it is a valid session
	if(isset($_COOKIE['racksmith']))
	{
		$details=$users->checkSessionKey($_COOKIE['racksmith']);
		if($details)
		{
			foreach($details as $item)
			{
				$_SESSION['userid']=$item->userID;
				$_SESSION['username']=$item->UserName;
			}
		}
		else
		{
			session_destroy();
			header("Location: login.php");
		}
	}
	else
		header("Location: login.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
}


// json replacement function thanks to
// http://www.php.net/manual/en/function.json-encode.php#82904
if (!function_exists('json_encode'))
{
function json_encode($a=false,$option=false)
{
  if (is_null($a)) return 'null';
  if ($a === false) return 'false';
  if ($a === true) return 'true';
  if (is_scalar($a))
  {
    if (is_float($a))
    {
      // Always use "." for floats.
      $a = str_replace(",", ".", strval($a));
    }

    // All scalars are converted to strings to avoid indeterminism.
    // PHP's "1" and 1 are equal for all PHP operators, but
    // JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
    // we should get the same result in the JS frontend (string).
    // Character replacements for JSON.
    static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
    array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
    return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
  }
  $isList = true;
  for ($i = 0, reset($a); $i < count($a); $i++, next($a))
  {
    if (key($a) !== $i)
    {
      $isList = false;
      break;
    }
  }
  $result = array();
  if ($isList)
  {
    foreach ($a as $v) $result[] = json_encode($v);
    return '[ ' . join(', ', $result) . ' ]';
  }
  else
  {
    foreach ($a as $k => $v) $result[] = json_encode($k).': '.json_encode($v);
    return '{ ' . join(', ', $result) . ' }';
  }
}
}

function object_to_array($var) {
    $result = array();
    $references = array();

    // loop over elements/properties
    foreach ($var as $key => $value) {
        // recursively convert objects
        if (is_object($value) || is_array($value)) {
            // but prevent cycles
            if (!in_array($value, $references)) {
                $result[$key] = object_to_array($value);
                $references[] = $value;
            }
        } else {
            // simple values are untouched
            $result[$key] = $value;
        }
    }
    return $result;
}


?>