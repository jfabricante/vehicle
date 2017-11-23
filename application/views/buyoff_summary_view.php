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
					<h3 class="box-title">Details</h3>
				</div>
				<form target="_blank" id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="<?php echo base_url(); ?>buyoff/generate_excel_report">
				    <div class="box-body">
					    <div class="row">
								<div class="col-xs-5">
								  <label class="control-label">Date from</label>
								  <input type="text" class="form-control" id="date_from" name="date_from" required>
								</div>
								<div class="col-xs-5">
								  <label class="control-label">Date to</label>
								  <input type="text" class="form-control" id="date_to" name="date_to" required>
								</div>

		                        <!-- <div class="col-xs-5 hidden">
		                          <label class="control-label">Lot No</label>
		                          <input type="text" class="form-control" id="lot_no" name="lot_no">
		                        </div> -->
		                        <div class="col-xs-5">
		                          <label class="control-label">Lot Number</label>
		                          <!-- <input type="text" class="form-control" id="sales_model" name="sales_model"> -->
		                          <select name="sales_model" id="sales_model" class="form-control select2" data-live-search="true">
		                          	<option value="0"></option>
		                          	<?php foreach($lot_number as $lot): ?>
		                          		<option value="<?php echo $lot->LOT_NUMBER; ?>"><?php echo $lot->LOT_NUMBER; ?></option>
		                          	<?php endforeach; ?>
		                          </select>
		                        </div>
		                        <div class="col-xs-5">
		                          <label class="control-label">Sales Model</label>
		                          <!-- <input type="text" class="form-control" id="sales_model" name="sales_model"> -->
		                          <select name="sales_model" id="sales_model" class="form-control select2" data-live-search="true">
		                          	<option value="0"></option>
		                          	<?php foreach($sales_model as $sales): ?>
		                          		<option value="<?php echo $sales->MODEL; ?>"><?php echo $sales->MODEL; ?></option>
		                          	<?php endforeach; ?>
		                          </select>
		                        </div>
<!-- 		                        <div class="col-xs-5">
		                          <label class="control-label">Year Model</label>
		                          <input type="text" class="form-control" id="year_model" name="year_model" readonly>
		                        </div> -->
		                        <div class="col-xs-5">
		                          <label class="control-label">CP Number</label>
		                          <input type="text" class="form-control" id="cp_no" name="cp_no">
		                        </div>
		                        
		                        <div class="col-xs-5">
		                          <label class="control-label">CP Date</label>
		                          <input type="text" class="form-control" id="cp_date" name="cp_date">
		                        </div>
		                       
		                        <div class="col-xs-5">
		                          <label class="control-label">Entry No</label>
		                          <input type="text" class="form-control" id="entry_no" name="entry_no">
		                        </div>
<!-- 		                        <div class="col-xs-5">
		                          <label class="control-label">Body Type</label>
		                          <input type="text" class="form-control" id="body_type" name="body_type" readonly>
		                        </div>
		                        <div class="col-xs-5">
		                          <label class="control-label">Engine Series</label>
		                          <input type="text" class="form-control" id="engine_series" name="engine_series" readonly>
		                        </div> -->
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
<script src="<?php echo base_url('resources/plugins/select2/js/select2.full.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/select2/js/select2.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/floatThead/floatThead.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/filestyle/bootstrap-filestyle.min.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script>
	$(document).ready(function() {
		$("select").select2({ width: 'resolve' });
		$("#cp_date").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#date_from").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#date_to").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#lot_no").change(function(){
			//~ var sales_model = $('#sales_model').val();
			var lot_no = $('#lot_no').val();

			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>buyoff/ajax_get_buyoff_report_details',
				data: {
						lot_no: lot_no
					},
				success: function(data) 
				{
					var parsed = $.parseJSON(data);	

					$.each(parsed, function (i, jsondata) {
						$('#sales_model').val(jsondata.SALES_MODEL);
						//~ alert(jsondata.sales_model)
					});
					
					//$('#engine_se').val();
					//alert(dr_number);
					// $('.modal-content').html('');
					// $('#myModal').modal('show');
					// $('.modal-content').html(data);
				}
			});
		    //alert("The text has been changed.");
		}); 

	});
</script>

