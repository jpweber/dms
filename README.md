## Dead Man Switch

## Description  
Dead man switch is a cron job monitoring system. Cron jobs check in via a simple http api. if the job does not check in within a certain amount of time an email notification is sent out and is marked as red / disabled in the list of switches.

## Instalation 
All files in git repo just go in whatever directory you should to be installed and then create a cron job for the switch_checker.php script.
Two database tables are needed one for the switches and one for the switch checkins

## Usage  
Create a switch via the web interface. This is done by filling out the form on home page with name, description, server it runs on and the crontab entry for when it runs

This creates a _switch_ entry in the database and generates a unique hash for this switch to identify itself with. When a switch is created code snippets for how to add them to your scripts is provided in Python, PHP, Ruby and Bash.

There is also a cronjob component that analyzes the checkins database for anything that did not check in and then flips the _switch_ to disabled and then sends a notification that something did not checkin. 

### Example  
A basic example of a file a file rotation job.  
Name: file rotation  
description. Renames files to make room for new ones.  
server: app14  
Interval: */10 * * * *

This would then provide a snippet like this to add to your code. 
`curl -silent http://<hostname>/dms/api/checkin/rxHz20ppaF5 > /dev/null` 

## Documentation
The main parts are:  
* Web interface  
* web API  
* script to analyze checkins  

The web interfaces leverages the API for all CRUD operations. So the web interface is just a client to the API that anything could be made to use.
The _switches_ checkinvia the API as well.

The script to analyze checkins lives in the scripts dir and uses the following logic to determine if something has checked in or now.

```
	//Current time
	date_default_timezone_set('America/New_York');
	$time_now = strtotime('now');
	// get individual switch check in info
	$checkin_info = json_decode(file_get_contents('http://hostname/dms/api/list/checkins/'.$switch->jobid.'/2'));
	
	//time frame to check for
	$check_interval = $switch->jobinterval * 2;
	
	//difference betweeen 1st and 2nd check in time
	$difference =  $checkin_info[0]->checkin_time - $checkin_info[1]->checkin_time;
	
	// the furthest acceptable time
	$acceptable = $time_now - $checkin_info[0]->checkin_time;
	
	//now - time to check
	$time_to_check = $time_now - $check_interval;
```

### API

API is a RESTful JSON API.

####Endpoints
Base = $HOSTNAME/api/

* /new
	* new expects args to be passed via POST 
* /delete
	* 	delete expects switch_id to be passed
	* 	example: /delete/rxHz20ppaF5
* /checkin
	* 	checkin expects switch_id to be passed
	* 	example: /checkin/rxHz20ppaF5
* /getinfo
	*  	checkin expects switch_id to be passed
	* 	example: /checkin/rxHz20ppaF5
	
* /list
	*	takes two extra arguments
		*	/switches and /checkins
	* /list/switches
		* 	Takes an extra parameter to specify __enabled__ or __disabled__ . If no extra parameter is passed it will list all switches both enabled and disabled.
	* /list/checkins
		* Takes an extra paremeter of the switch id to return checkins for only one switc (`/list/checkins/rxHz20ppaF5`). if no switch id is passed it will return all switches