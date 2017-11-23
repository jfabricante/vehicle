<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/select2/css/select2.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-8">
			<div class="box box-danger">
			<div id="overlay" class="overlay hide">
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
				<div class="box-header with-border">
					<h3 class="box-title">Report</h3>
					<h3 class="box-title pull-right">Current Year - <?php echo $year; ?></h3>
				</div>
				<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="search">
				    <div class="box-body">
					    <div class="row">
					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Month: </label>
									<div class="col-sm-8">
										<select class="form-control select2 input-sm" id="month" name="month" data-live-search="true">
											<option value="0">Nothing Selected</option>
											<?php foreach($all_month as $m) {?>
												<option value="<?php echo $m['1']; ?>"><?php echo $m['0']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
					    	</div>
					    	<br></br>
					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">LPDA: </label>
									<div class="col-sm-8">
										<select class="form-control select2 input-sm" id="lpda" name="lpda" data-live-search="true">
											<?php echo ($options != NULL)? $options:'';?>
										</select>
									</div>
								</div>
					    	</div>
					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Remarks: </label>
									<div class="col-sm-8">
										<textarea class="form-control" placeholder="Remarks" id="remarks"></textarea>
									<!-- 	<input type="text" id="remarks" class="form-control" placeholder="Remarks"> -->
									</div>
								</div>
					    	</div>

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Coordinator: </label>
									<div class="col-sm-8">
										<input type="text" id="coordinator" class="form-control" placeholder="Coordinator">
									</div>
								</div>
					    	</div>

					    </div>
				    </div>
					<div class="box-footer text-left">
						<input type="button" id="submit" class="btn btn-danger" style="margin-left: 15px;" value="Generate Report">
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

		$('select[name=month]').change(function(){
			$('select[name=lpda]').val('').trigger('change');
			$('#overlay').removeClass();
        	$('#overlay').addClass('overlay');
			var month = $(this).val();
			$.ajax({
				type: 'POST',
				data: {
					month : month
				},
				url: '<?php echo base_url();?>titan/ajax_get_lpda_details',
				success: function(data){
					$('#overlay').removeClass();
                    $('#overlay').addClass('overlay hide');
					//alert(data);
					$('select[name=lpda]').html(data);
				}
			});
		});
		
		$('#submit').click(function(){
			var po_number = $('#lpda').val();
			var month = $('#month').val();
			var remarks = $('#remarks').val();
			var coordinator = $('#coordinator').val();
			//alert(po_number);
			window.open('<?php echo base_url(); ?>titan/lpda/'+po_number+'/'+month+'/'+remarks+'/'+coordinator); 
		});

	});
</script>

