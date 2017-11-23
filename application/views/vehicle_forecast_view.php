<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/select2/css/select2.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-danger">
			<div id="overlay" class="overlay hide">
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
				<div class="box-header with-border">
					<h3 class="box-title">Details</h3>
				</div>
				<form target="_blank" id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="<?php echo base_url(); ?>report/vehicle_forecast">
				    <div class="box-body">
					    <div class="row">
							<div class="col-md-8" style="padding: 10px;margin-left: 20px;">
								<div class="form-group">
									<label class="col-sm-2 control-label" for="unput1">Year</label>
									<div class="col-sm-8">
										<select class="form-control select2 input-sm" id="year" name="year">
											<?php
											foreach($year as $row => $value){
												?>
												<option value="<?php echo $value; ?>"><?php echo $value;?></option>
												<?php 
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="unput1">Month</label>
									<div class="col-sm-8">
										<select class="form-control select2 input-sm" id="month" name="month">
											<option value="01">January</option>
											<option value="02">February</option>
											<option value="03">March</option>
											<option value="04">April</option>
											<option value="05">May</option>
											<option value="06">June</option>
											<option value="07">July</option>
											<option value="08">August</option>
											<option value="09">September</option>
											<option value="10">October</option>
											<option value="11">November</option>
											<option value="12">December</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-2">
										<button style="margin-left: 14px;" class="btn btn-flat btn-danger btn-sm" type="submit">Generate Report</button>
									</div>
								</div>
							</div>	
						</div>
				    </div>
				</form>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/select2/js/select2.full.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/floatThead/floatThead.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/filestyle/bootstrap-filestyle.min.js'); ?>"></script>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: 'Nothing selected'
		});
	});
</script>

