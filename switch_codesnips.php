<?php
$switch_id = $_GET['switch_id'];
//hard coded switch id 5 for testing
$json = file_get_contents('http://<hostname>/dms/api/getinfo/'.$switch_id);

$switch_info = json_decode($json);
//var_dump($switch_info);

?>

Your new Dead Man Switch for <strong><?php echo $switch_info->jobname; ?></strong> Has been created.
To use it in your cron job use the appropriate code snippet below. 

<h4>Shell:</h4>
<pre>curl -silent http://<hostname>/dms/api/checkin/<?php echo $switch_info->jobid; ?> > /dev/null </pre>

<h4>PHP:</h4>
<pre>
$results = file_get_contents('http://<hostname>/dms/api/checkin/<?php echo $switch_info->jobid; ?>');
</pre>

<h4>Python:</h4>
<pre>
import urllib2
url = 'http://<hostname>/dms/api/checkin/<?php echo $switch_info->jobid; ?>'
try:
    response = urllib2.urlopen(url).read()
except:
    print "Cannot connect to dms api"
</pre>

<h4>Ruby:</h4>
<pre>
require 'open-uri'
url = "http://<hostname>/dms/api/checkin/<?php echo $switch_info->jobid; ?>"
begin
    response = open(url).read
rescue
    puts "Cannot connect to DMS API"
end
</pre>

