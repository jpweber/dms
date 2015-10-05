<!DOCTYPE html>
<html>
 <head>

 <link rel="stylesheet" href="../bootstrap_3/css/bootstrap.css" type="text/css">
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
 <script src="dms.js"></script>


 </head>
 <body>
	 <div class="container">
	 	<?php 
	 		require_once('header.php');
	 		$switch_id = $_GET['switch_id'];
    		if(is_null($switch_id) ){
        		$checkins = json_decode(file_get_contents('http://<hostname>/dms/api/list/checkins'));
    		}else{
        		$checkins = json_decode(file_get_contents('http://<hostname>/dms/api/list/checkins/'.$switch_id));
    		}
	 		  
	 	?>
	 	
		<div class="page-header">
			 <h2>CheckIns<small> Switch CheckIn History</small></h2>
		</div>
		
		<div class="row">
			<div class="col-md-12" id="switches">
				<table class="table table-striped table-hover">
	            	<thead>
						<tr>
<!-- 							<th>Switch #</th> -->
		                    <th>Switch Name</th>
		                    <th>Description</th>
		                    <th>Server</th>
			                <th>Check In Time</th>
		                    <th>Switch ID</th>
						</tr>
	            	</thead>
	            	<tbody>
	            	
	            		<?php
	            		
	            		
	            		
	            		
	            			foreach ($checkins as $checkin){
		            			echo "<tr>";
			            			/*
echo "<td>";
				            			echo $checkin->id;
			            			echo "</td>";
*/
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
			            			echo "<td>";
				            			echo $checkin->switch_id;
			            			echo "</td>";
		            			echo "</tr>";            			       			     			
	            			}
            			?>

	            	</tbody>
	            				
	            		

			</div>
			
    </div> <!-- container -->
</body>
</html>