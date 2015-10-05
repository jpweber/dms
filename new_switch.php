<h3>Create New Switch<small></small></h3>
<form role="form" action="api/new" method="post" id="formNewSwitch">
  <div class="form-group">
    <label for="jobname">Job Name</label>
    <input type="text" class="form-control" name="jobname" id="jobname" placeholder="Short Name or Script">
  </div>
  <div class="form-group">
    <label for="jobdescription">Description</label>
    <input type="text" class="form-control" name="jobdescription" id="jobdescription" placeholder="Description">
  </div>
	  <div class="form-group">
    <label for="jobserver">Server</label>
    <input type="text" class="form-control" name="jobserver" id="jobserver" placeholder="What server does this run on">
  </div>
  <div class="form-group">
    <label for="jobinterval">Interval</label>
    <input type="time" class="form-control" name="jobinterval" id="jobinterval" placeholder="How Often does this run?">
  </div>
  
  <button type="submit" class="btn btn-default">Submit</button>
</form>

<script type="text/javascript">
    var frm = $('#formNewSwitch');
    frm.submit(function (ev) {
        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),
            success: function(data){
	            //console.log(data);
	            showCodeSnips(data)
            }
        });

        ev.preventDefault();
    });
</script>