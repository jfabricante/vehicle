<?php 
$this->load->helper('format_helper');
//var_dump($sales_model); die;
?>
<section class="content">
	<div class="row">
		<div class="col-md-4">
			<div class="box box-danger">
			<div id="overlay" class="overlay hide">
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
				<div class="box-header with-border">
					<h3 class="box-title">Report Parameters</h3>
				</div>
				<form target="_blank" id="form_filters"  method="POST" accept-charset="utf-8" action="ws_executive_excel">
				    <div class="box-body">
						<div class="form-group">
							<label class="control-label">Previous Month Date Start</label>
							<input type="text" class="form-control date" name="prev_from" required>
						</div>
						<div class="form-group">
							<label class="control-label">Previous Month Date End</label>
								<input type="text" class="form-control date" name="prev_to" required>
						</div>
						<div class="form-group">
							<label class="control-label">Current Month Date Start</label>
							<input type="text" class="form-control date" name="curr_from" required>
						</div>
						<div class="form-group">
							<label class="control-label">Current Month Date End</label>
								<input type="text" class="form-control date" name="curr_to" required>
						</div>
					    
						<div class="form-group">
							<label class="control-label">Previous Month Working Days</label>
								<input type="text" class="form-control" name="prev_wd" required>
						</div>
					    
						<div class="form-group">
							<label class="control-label">Current Month Working Days</label>
								<input type="text" class="form-control" name="curr_wd" required>
						</div>
					    
				    </div>
					<div class="box-footer text-left">
						<input type="submit" id="submit" class="btn btn-danger" value="Generate Report">
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script>
	$(document).ready(function() {
		$(".date").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		//~ $("#date_to").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
	});
</script>

