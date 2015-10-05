<?php

// url options
// /new - create new switch
// /delete/<id> - delete switch
// /checkin/<id> - task checking in, "pressing the switch"
// /getinfo/<id> - get task info, basically select * where id = blah
require '../lib/Hashids/Hashids.php';
require_once '../lib/Cron/CronExpression.php';
require_once '../lib/Cron/FieldInterface.php';
require_once '../lib/Cron/AbstractField.php';
require_once '../lib/Cron/DayOfMonthField.php';
require_once '../lib/Cron/DayOfWeekField.php';
require_once '../lib/Cron/FieldFactory.php';
require_once '../lib/Cron/HoursField.php';
require_once '../lib/Cron/MinutesField.php';
require_once '../lib/Cron/MonthField.php';
require_once '../lib/Cron/YearField.php';

 

$requestURI = explode('/', $_SERVER['REQUEST_URI']);
$command = $requestURI[3];
$switch_id = $requestURI[4];

/*
var_dump($requestURI);
var_dump($command);
var_dump($switch_id);
*/

//function to connect to mysql database
function db_connect($hostname, $user, $pass, $database){
	$mysqli = mysqli_connect($hostname, $user, $pass, $database);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
	    return false;
	}
	
	return $mysqli;
}

function generate_switchid($switch_name, $timestamp){
	$hashids = new Hashids\Hashids('embiggen');
	$hash = $hashids->encrypt(strlen($switch_name), $timestamp, 6);
	$numbers = $hashids->decrypt($hash);
	//!debug
	//var_dump($hash, $numbers);
	return $hash;
}

function new_switch($args){
	$switch_name = $args['jobname'];
	$description = $args['jobdescription'];
	$server = $args['jobserver'];
	$interval = $args['jobinterval'];
	//$scale = $args['intervalScale'];
	
	//$intervalSeconds = ($interval * $scale);
	
	//parse cron string
	$cron = Cron\CronExpression::factory($interval);
	//echo $cron->isDue();
	$next = $cron->getNextRunDate()->format('U');
	$previous = $cron->getPreviousRunDate()->format('U');
	$diff = $next - $previous;
	//$intervalFromCron = "";

	$switch_id = generate_switchid($switch_name, date('U'));
	$dbclient = db_connect("hostname", "username", "password", "dms");
	$query = "INSERT INTO `switches` (`id`, `switch_name`, `description`, `server`, `interval`, `switch_id`) VALUES ('', '$switch_name', '$description', '$server', '$diff', '$switch_id')";
		
	$results = $dbclient->query($query);
	
	if($results){
		$response['switch_id'] = $switch_id;
		return $response;
	}else{
		return false;
	}
}

function delete_switch($switch_id){
	$dbclient = db_connect("hostname", "username", "password", "dms");
	$query = "DELETE FROM `switches` WHERE `switch_id` = '$switch_id'  ";
	
	//!debug
	//var_dump($query);
		
	$results = $dbclient->query($query);
	
	if($results){
		return true;
	}else{
		return false;
	}

}

function switch_checkin($switch_id){
	$dbclient = db_connect("hostname", "username", "password", "dms");
	
	//setup timezones
	$utc = new DateTimeZone('UTC');
	$amny = new DateTimeZone('America/New_York');
	
	//capture now in new york timezone, server is set to new york timezone
	$now = new DateTime('now',$amny);
	//echo "AMNY time: ".$now->format('Y-m-d h:i');
	
	//convert date to UTC	
	$now->setTimeZone($utc);
	//echo "UTC time: ".$now->format('Y-m-d H:i');
	
	//set checking time in unix timestamp format
	$checkin_time = $now->format('U');
	
	$query = "INSERT INTO `switch_checkins` (`id`, `switch_id`, `checkin_time`) VALUES ('', '$switch_id', '$checkin_time')";
		
	$results = $dbclient->query($query);
	
	if($results){
		//query to make sure enabled status is set to true
		$query = "UPDATE `switches` SET `enabled` = 1 WHERE `switch_id` = '$switch_id'";
		//execute enabled query
		$results = $dbclient->query($query);
		
		//return true because of checkin success we don't care as much if the update fails.
		return true;
	}else{
		//return false;
		$dbclient->error;
		
	}
}

function get_switch_info($id){

	$dbclient = db_connect("hostname", "username", "password", "dms");
	$query = "SELECT * from `switches` WHERE `switch_id`= '$id' ";
	//!debug
	//echo $query;
	$results = $dbclient->query($query);
	
	if($results){
		$response = array();
		while ($row = $results->fetch_assoc()) {
			//!debug
			//var_dump($row);
			$response['jobid'] = $row['switch_id']; //unique app generated hash
        	$response['jobname'] = $row['switch_name'];
        	$response['jobdescription'] = $row['description'];
			$response['jobserver'] = $row['server'];
			$response['jobinterval'] = $row['interval'];
			$response['jobnumber'] = $row['id']; //the id column in the database

		}
		//!debug
		//var_dump($response);
		return $response;
	}else{
		return false;
	}
}

function get_switches($status){
	$dbclient = db_connect("hostname", "username", "password", "dms");
	$query = "SELECT * FROM `switches`";
	
	//add switch status where clause if status parameter is present
	if ($status == 'enabled'){
		$query .= 'WHERE enabled = 1  ORDER BY `server` ASC';
	}
	if ($status == 'disabled'){
		$query .= 'WHERE enabled = 0  ORDER BY `server` ASC';
	}
	
	
	//!debug
	//echo $query;
	$results = $dbclient->query($query);
	
	if($results){
		$response = array();
		$responses = array();
		while ($row = $results->fetch_assoc()) {
			//!debug
			//var_dump($row);
			$response['jobid'] = $row['switch_id']; //unique app generated hash
        	$response['jobname'] = $row['switch_name'];
        	$response['jobdescription'] = $row['description'];
			$response['jobserver'] = $row['server'];
			$response['jobinterval'] = $row['interval'];
			$response['jobnumber'] = $row['id']; //the id column in the database
			$response['enabled'] = $row['enabled'];
			$responses[] = $response;
		}
		//!debug
		//var_dump($response);
		return $responses;
	}else{
		return false;
	}

}

function get_checkins($switch_id=NULL, $limit){
	$dbclient = db_connect("hostname", "username", "password", "dms");
	
	if (is_null($switch_id)){
		// list all checkins
		$query = "SELECT  t1.id, t1.`switch_id`, t1.`checkin_time`, t2.`switch_name`, t2.`description`, t2.`server`
				FROM `switch_checkins` t1 
				INNER JOIN switches t2
				ON t1.`switch_id` = t2.`switch_id`
				ORDER BY t1.`checkin_time` DESC LIMIT 100";
	}else{
		// list only the specified checkin
		if($limit == ''){
			$limit = 100;
		}
		$query = "SELECT  t1.id, t1.`switch_id`, t1.`checkin_time`, t2.`switch_name`, t2.`description`, t2.`server`
				FROM `switch_checkins` t1 
				INNER JOIN switches t2
				ON t1.`switch_id` = t2.`switch_id`
				WHERE t1.`switch_id` = '$switch_id' ORDER BY t1.`checkin_time` DESC LIMIT $limit";
	} 
	
	
				
	//!debug
	//echo $query;
	$results = $dbclient->query($query);
	
	if($results){
		$response = array();
		$responses = array();
		while ($row = $results->fetch_assoc()) {
			//!debug
			//var_dump($row);
			$response['id'] = $row['id']; //the id column in the database
        	$response['switch_id'] = $row['switch_id'];
        	$response['checkin_time'] = $row['checkin_time'];
        	$response['switch_name'] = $row['switch_name'];
			$response['description'] = $row['description'];
			$response['server'] = $row['server'];     	

			$responses[] = $response;
		}
		//!debug
		//var_dump($response);
		return $responses;
	}else{
		return false;
	}

}

function set_checkin_failed($switch_id){
	$dbclient = db_connect("hostname", "username", "password", "dms");
	$query = "UPDATE `switches` SET `enabled` = 0 WHERE `switch_id` = '$switch_id'";
	
	$results = $dbclient->query($query);
	if($results){
		return true;
	}else{
		//return false;
		$dbclient->error;
		
	}
}




//run commands
switch ($command) {
	case 'new':
		//create new dead man swtich
		$args = $_POST;
		//!debug
		//var_dump($args);
		$response = new_switch($args);
		break;
	case 'delete':
		//delete dead man swtich
		$response = delete_switch($switch_id);
		break;
	case 'checkin':
		//task checking in
		$response = switch_checkin($switch_id);
		break;
	case 'getinfo':
		//task checking in
		$response = get_switch_info($switch_id);
		break;
	case 'list':
		//look for the type of objects to list
		if ($requestURI[4] == "switches"){
			//list all switches
			$response = get_switches($requestURI[5]);
		}
		if ($requestURI[4] == "checkins"){
			//list all switch checkins
			$response = get_checkins($requestURI[5],$requestURI[6]);
		}		
		break;
	case 'checkin_failed':
		$response = set_checkin_failed($switch_id);
		break;
			
	default:
		break;
}

// json encode the response and return it back to client
header('Content-type: application/json');
echo json_encode($response);



?>
