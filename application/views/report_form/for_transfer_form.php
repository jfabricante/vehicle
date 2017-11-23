<?php 
$this->load->helper('format_helper');
//var_dump($sales_model); die;
?>
<link href="<?php echo base_url('resources/plugins/select2/css/select2.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css') ?>" rel="stylesheet">
<section class="content">
	<div class="row">
		<div class="col-md-8">
			<div class="box box-danger">
				<div id="overlay" class="overlay hide">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
				<div class="box-header with-border">
					<h3 class="box-title">Received Date Range</h3>
				</div>
				<form target="_blank" id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="for_transfer_excel">
				    <div class="box-body">
					    <div class="row">
							<div class="col-xs-5">
								<label class="control-label">Date from</label>
								<input type="text" class="form-control" id="date_from" name="from" required>
							</div>
							<div class="col-xs-5">
								<label class="control-label">Date to</label>
								<input type="text" class="form-control" id="date_to" name="to" required>
							</div>
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
		$("#date_from").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#date_to").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
	});
</script>

