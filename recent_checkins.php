<?php 
	$checkins = json_decode(file_get_contents('http://<hostname>/dms/api/list/checkins')); 
?>
	<h3>Checkin History<small> last 10</small></h3>
	<table class="table table-striped table-hover">
    	<thead>
			<tr>
<!-- 							<th>Switch #</th> -->
                <th>Switch Name</th>
                <th>Description</th>
                <th>Server</th>
                <th>Check In Time</th>
			</tr>
    	</thead>
    	<tbody>
    	
    		<?php
    			$i = 0;
    			foreach ($checkins as $checkin){
        			echo "<tr>";

            			echo "<td>";
	            			echo $checkin->switch_name;
            			echo "</td>";
            			echo "<td>";
	            			echo $checkin->description;
            			echo "</td>";			            			
            			echo "<td>";
	            			echo $checkin->server;
            			echo "</td>";		       
            			echo "<td>";
	            			echo date('Y-m-d h:i:s',$checkin->checkin_time);
            			echo "</td>";			     

        			echo "</tr>";            			       			     			
        			
        			$i++;
        			if ($i == 9){
	        			break;
        			}
    			}
			?>

    	</tbody>
