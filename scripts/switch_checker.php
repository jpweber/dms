<?php
require_once('../lib/dms/class.Switches.php');

// open db connection

//get list of switches 
$switches = json_decode(file_get_contents('http://<hostname>/dms/api/list/switches/enabled')); 


// loop through switches to determine if a checkin didn't happen
foreach ($switches as $switch){
	// var_dump($switch);
	//Current time
	date_default_timezone_set('America/New_York');
	$time_now = strtotime('now');
	// get individual switch check in info
	$checkin_info = json_decode(file_get_contents('http://<hostname>/dms/api/list/checkins/'.$switch->jobid.'/2'));
	//time frame to check for
	$check_interval = $switch->jobinterval * 2;
	//job server ID
	$job_server = $switch->jobserver;
	//difference betweeen 1st and 2nd check in time
	$difference =  $checkin_info[0]->checkin_time - $checkin_info[1]->checkin_time;
	// the furthest acceptable time
	$acceptable = $time_now - $checkin_info[0]->checkin_time;
	//now - time to check
	$time_to_check = $time_now - $check_interval;
	
	//var_dump($results);
	echo "Switch Name: ". $checkin_info[0]->switch_name."\n";
	echo "Switch ID: ". $checkin_info[0]->switch_id."\n";
	echo "Job Server: ". $job_server."\n";
	echo "check in time 0: ". $checkin_info[0]->checkin_time."\n";
	echo "check in time 1: ". $checkin_info[1]->checkin_time."\n";
	echo "acceptable: ". $acceptable."\n";
	echo "difference: ". $difference."\n";
//	echo "Switch Interval: ". $switch->jobinterval."\n";
	echo "Interval * two: ". $check_interval."\n";
	echo "now:". $time_now."\n";
	echo "nowcheck:". ($time_now - $check_interval)."\n";
	
	if (($checkin_info[0]->checkin_time < $time_now) && ($checkin_info[0]->checkin_time > $time_to_check)){
		echo "1 success\n";
	}
	elseif(($checkin_info[1]->checkin_time < $time_now) && ($checkin_info[1]->checkin_time > $time_to_check)){
		echo"2 success\n";
	}else{
		echo"failure";
		send_mail($checkin_info[0]->switch_name,$switch->jobdescription,$checkin_info[0]->switch_id,$job_server);
		file_get_contents('http://<hostname>/dms/api/checkin_failed/'.$switch->jobid);
		
	}
	
	echo "\n";
	}

function draw_body($name,$description,$switch_id,$job_name){
	$body_email = "$name - $description did not check in <br> ";
	$body_email .= "Server = $job_name <br> ";	
	$body_email .= "http://<hostname>/dms/checkin_history.php?switch_id=$switch_id";
	return $body_email;

}

//send mail to users
function send_mail($name,$description,$server_id,$job_name){
	echo "Sending Email\n";
	$to  =  "devs@yourdomain.com";
	$subject = 'DMS Notification';
	$message = draw_body($name,$description,$server_id,$job_name);
	$headers = "From: no-reply@yourdomain.net\r\n";
	$headers .= "Content-type: text/html\r\n";
	mail($to, $subject, $message, $headers);
}

// Check in with DMS
$results = file_get_contents('http://<hostname>/dms/api/checkin/zZuP1GL60sy');
?>
