
// function to draw the new switch form
function drawNewSwitch(){
      $("#newSwitch").load('new_switch.php');
}

//draw checkins table for main page
function drawCheckins(){
      $("#checkins").load('recent_checkins.php');
}


function showCodeSnips(data){
	var switch_id = data['switch_id']
	//console.log(switch_id);
	$("#newSwitch").load('switch_codesnips.php?switch_id='+switch_id);
}

function deleteSwitch(switch_id){
	$.ajax({
	    url: "api/delete/"+switch_id,
	});
}