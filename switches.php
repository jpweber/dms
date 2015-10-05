<!DOCTYPE html>
<html>
 <head>

 <link rel="stylesheet" href="../bootstrap_3/css/bootstrap.css" type="text/css">
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
 <script src="../bootstrap_3/js/bootstrap.min.js"></script>
 <script src="dms.js"></script>
 <script>
	 	$(document).ready(function() {
	    $("#switches .glyphicon-remove").on("click",function() {
		        var tr = $(this).closest('tr');
		        var switch_id = $(this).closest('td').prev().prev().prev().text();
		        
		        tr.css("background-color","#BA160C");
	
		        tr.fadeOut(300, function(){
		            tr.remove();
		        });
		        
		        $.get( "api/delete/"+switch_id );
		        
		      return false;
		    });
		    
		   $("#switches .glyphicon-book").on("click",function() 
			{   
				console.log('modal function running');
			    var switch_id = $(this).closest('td').prev().prev().text();
			
			    $.ajax({
			        cache: false,
			        type: 'GET',
			        url: 'switch_codesnips.php',
			        data: 'switch_id='+switch_id,
			        success: function(data) 
			        {
			            $('#myModal').show();
			            $('#modal-body').show().html(data);
			        }
			    });
			});
 
		    
		});
		


			
     
</script>


 </head>
 <body>
	 <div class="container">
	 	<?php 
	 		require_once('header.php');
	 		$switches = json_decode(file_get_contents('http://<hostname>/dms/api/list/switches')); 
	 	?>
		 <div class="page-header">
			 <h2>Switches<small> lists of configured dead man switches</small></h2>
		</div>
		
		<div class="row">
			<div class="col-md-12" id="switches">
				<table class="table table-striped" id="switches">
	            	<thead>
						<tr>
							<th>Switch #</th>
		                    <th>Switch Name</th>
		                    <th>Description</th>
		                    <th>Server</th>
		                    <th>Interval</th>
		                    <th>Switch ID</th>
		                    <th>Edit</th>
		                    <th>Code</th>
		                    <th>Delete</th>
						</tr>
	            	</thead>
	            	<tbody>
	            		<?php
	            			foreach ($switches as $switch){
	            				if ($switch->enabled){
		            				echo "<tr>";	
	            				}else{
		            				echo "<tr class='danger'>";
	            				}
		            			
			            			echo "<td>";
				            			echo $switch->jobnumber;
			            			echo "</td>";
			            			echo "<td>";
											echo $switch->jobname;
			            			echo "</td>";
			            			echo "<td>";
				            			echo $switch->jobdescription;
			            			echo "</td>";			            			
			            			echo "<td>";
				            			echo $switch->jobserver;
			            			echo "</td>";		       
			            			echo "<td>";
				            			echo $switch->jobinterval;
			            			echo "</td>";			     
			            			echo "<td id='jobid'>";				            			
				            			echo '<a href="http://<hostname>/dms/checkin_history.php?switch_id='.$switch->jobid.'">'.$switch->jobid.'</a>';
			            			echo "</td>";
			            			echo "<td>";
					            		echo '<a href="#" class="btn btn-default btn-sm disabled" role="button">
												  <span class="glyphicon glyphicon-cog"></span>
											  </a>';

			            			echo "</td>";
			            			echo "<td>";
/* 			            			switch_codesnips.php?switch_id='.$switch->jobid.' */
					            		echo '<a href="#" class="btn btn-default btn-sm" role="button" data-toggle="modal" data-target="#myModal" id="ZMFlKN1MGFP">
												  <span class="glyphicon glyphicon-book"></span>
											  </a>';

			            			echo "</td>";			            			
			            			echo "<td>";
			            				echo '<a href="#" class="btn btn-danger btn-sm" role="button">
												  <span class="glyphicon glyphicon-remove"></span>
											  </a>';
		            			echo "</tr>";            			       			     			
	            			}
	            			?>
	  	            	</tbody>
			</div>		
    </div> <!-- container -->
    
    <!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Code Snippets</h4>
	      </div>

	      <div class="modal-body" id="modal-body">
			  This should be removed by the ajax call
	        	
	      </div>
	      <div class="modal-footer">
<!-- 	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
	      </div>
	    </div>
	  </div>
	</div>


</body>
</html>