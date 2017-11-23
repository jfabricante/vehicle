<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/select2/css/select2.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-danger">
			<div id="overlay" class="overlay hide">
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
				<div class="box-header with-border">
					<h3 class="box-title">Search</h3>
				</div>
				<form target="_blank" id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="vehicle_information">
				    <div class="box-body">
						<div class="form-group">
							<label class="col-sm-3 control-label">From Lot Number</label>
							<div class="col-sm-8">
								<select class="form-control" name="lot_number">
									<option value="" selected="selected">Select From Lot Number</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">To Lot Number</label>
							<div class="col-sm-8">
								<select class="form-control" name="lot_number2">
									<option value="" selected="selected">Select To Lot Number</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">CS Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="cs_number" >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Chassis Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="chassis_number" >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Engine Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="engine_number" >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">&nbsp;</label>
							<div class="col-sm-8">
								<button type="submit" class="btn btn-sm btn-flat btn-danger">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/select2/js/select2.full.min.js');?>"></script>
<script>
	$(document).ready(function() {
		$("select").select2({
			  ajax: {
				url: "ajax_search_lot_number",
				dataType: 'json',
				type: 'GET',
				delay: 250,
				data: function (params) {
				  return {
					q: params.term // search term
				  };
				},
				processResults: function (data, page) {
				  return {
					results: data  
				  };
				},
				cache: true
			  },
			  minimumInputLength: 2
			});
	});
</script>

