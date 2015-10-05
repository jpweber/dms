<!DOCTYPE html>
<html>
 <head>

 <link rel="stylesheet" href="../bootstrap_3/css/bootstrap.css" type="text/css">
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
 <script src="dms.js"></script>
 <script>
	function load(){
		drawNewSwitch();
		drawCheckins();
	}
	
	window.onload = load
 </script>

 </head>
 <body>
	 <div class="container">
	 	<?php require_once('header.php'); ?>
		 <div class="page-header">
			 <h2>DMS<small> dead man switch cron monitoring</small></h2>
		</div>
		
		<div class="row">
			<div class="col-md-6" id="newSwitch">
				<!-- form to create new switches -->
			</div>
			
			<div class="col-md-6" id="checkins">
				<!-- table of recent checkings -->
			</div>
        
    </div> <!-- container -->
</body>
</html>