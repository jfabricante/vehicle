<?php 
$this->load->helper('format_helper');
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Search Lot Number and Model Name</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-toggle="modal" data-target="#myModal2"><i class="fa fa-upload"></i></button>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="search">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="unput1">Lot Number</label>
								<div class="col-sm-10">
									<select class="form-control select2" id="lot_number" name="lot_number">
										<option value="1" <?php echo ($lot_number == 1)? 'selected':'';?>>Nothing Selected</option>
										<?php
										foreach($lot_numbers as $row){
											?>
											<option value="<?php echo $row->LOT_NUMBER; ?>" <?php echo ($lot_number == $row->LOT_NUMBER)? 'selected':'';?> >
												<?php echo $row->LOT_NUMBER;?>
											</option>
											<?php 
										}
										?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="unput1">Model Name</label>
								<div class="col-sm-10">
									<select class="form-control select2" id="model_name" name="model_name" data-live-search="true">
										<?php echo ($options != NULL)? $options:'';?>
									</select>
								</div>
							</div>

							<!-- <div class="form-group">
								<label class="col-sm-2 control-label" for="vin_model">Vin Model</label>
								<div class="col-sm-10">
									<select class="form-control select2" id="vin_model" name="vin_model" data-live-search="true">
										<option selected>Nothing Selected</option>
										<?php foreach ($modelList as $vinModel): ?>
											<option value="<?php echo $vinModel->PRODUCT_MODEL ?>"><?php echo $vinModel->PRODUCT_MODEL ?></option>
										<?php endforeach ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="model_lot">Model Lot</label>
								<div class="col-sm-10">
									<select class="form-control select2" id="model_lot" name="model_lot" data-live-search="true">
										<option selected>Nothing Selected</option>
									</select>
								</div>
							</div> -->

							<div class="form-group">
								<div class="col-sm-offset-2">
									<button style="margin-left: 14px;" class="btn btn-flat btn-danger btn-sm" type="button" name="submit">Search</button>
								</div>
							</div>
						</form>
					</div>

						
				</div>
				<div class="box-body">
					
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content modal-contente">
			
		</div>
	</div>
</div>

<!-- Modal2 -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload chassis number and engine number matching.</h4>
			</div>
			<div class="modal-body">
				<form id="my-form" class="form-horizontal" method="POST" action="upload_vin" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="exampleInputFile">Excel File</label>
						<div class="col-sm-10">
							<input name="excel_file" type="file" id="exampleInputFile" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="exampleInputFile"></label>
						<div class="col-sm-10">
							<button class="btn btn-danger btn-flat" type="submit">Upload</button>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/floatThead/floatThead.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/filestyle/bootstrap-filestyle.min.js'); ?>"></script>
<script>
	$(document).ready(function() {

		const appUrl = "<?php echo base_url()?>";
		
		 $(document).on("focus", ".datemask", function() { 
			$('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		  });
		  
		$(':file').filestyle({
			buttonName: 'btn-danger btn-flat',
			placeholder: 'No file selected',
			icon: false
		});
		
		//~ $('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		$('body table#mis-table').floatThead();
		
		$('.select2').select2({
			placeholder: 'Nothing selected'
		});
		//~ $($('body .select2').data('select2').$container).addClass('form-control')
		
		$('select[name=lot_number]').change(function(){
			$('select[name=model_name]').val('').trigger('change');
			var lot_number = $(this).val();
			$.ajax({
				type: 'POST',
				data: {
					lot_number : lot_number
				},
				url: '<?php echo base_url();?>mis/ajax_get_model_names',
				success: function(data){
					//alert(data);
					$('select[name=model_name]').html(data);
				}
			});
		});
		
		$('button[name="submit"]').click(function(){

			var lot_number = $('#lot_number').val();
			var model_name = $('#model_name').val();
			
			if(lot_number != 1){
				$.ajax({
					type: 'POST',
					data: {
						lot_number : lot_number,
						model_name : model_name
					},
					url: '<?php echo base_url();?>mis/ajax_get_mis_units',
					success: function(data){
					
						$('.box-body').html(data);
					}
				});
			}
			else{
				alert('Invalid Lot Number.');			
			}
		});
		
		$('body').on('blur', '.cs_number', function(){
			 if( $(this).val() ) {
				 $(this).parent().next().children(0).attr('required', true);
			}
			else{
				 $(this).parent().next().children(0).attr('required', false);
			}
		});
		
		$('body').on('blur', '.vin', function(){
			 if( $(this).val() ) {
				 $(this).parent().prev().children(0).attr('required', true);
			}
			else{
				 $(this).parent().prev().children(0).attr('required', false);
			}
		});
		
		
		$('body').on('click', '.modal-trigger', function(){
			 
			 var mis_id = $(this).data('mis_id');
			 var serial_no = $(this).data('serial_no');
			 var lot_no = $(this).data('lot_no');
			 var model_name = $(this).data('model_name');
			 
			 $.ajax({
				type: 'POST',
				data: {
					mis_id : mis_id,
					serial_no : serial_no,
					lot_no : lot_no,
					model_name : model_name
				},
				url: '<?php echo base_url();?>mis/ajax_msn_details_form',
				success: function(data){
					$('.modal-contente').html(data);
					$('#myModal').show();
					
				}
			});
		});
		
		$('body').on('click', '#save-msn-details', function(){
			 
			 $('#saved-alert').addClass('hidden');
			 $('#required-alert').addClass('hidden');
			 $('#error-alert').addClass('hidden');
			 
			 //~ var serial = $("input[name=serial_no]").val();
			 //~ $(".serial").each(function(){
				//~ if( $(this).attr("id") == serial){
					//~ alert("yes");
					//~ var tr = $(this);
					//~ var cs_no = tr.find("td:nth-child(7)").text("Bryan");
					//~ var chassis_number = tr.find("td:nth-child(8)").text("Bryan CH	");
				//~ }
			//~ });
			
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>mis/ajax_msn_details_submit',
				data: $('#msn-details-form').serialize(),
				success: function (data) {
					if(data == 'true'){
						$('#saved-alert').removeClass('hidden');
						
						var id = $('input[name=serial_no]').val();
						id = '#serial-'+(id.replace(/\s+/g, ''));

						var tr = $(id);
						tr.find("td:nth-child(8)").text($('input[name=cs_no]').val());
						tr.find("td:nth-child(9)").text($('input[name=chassis_no]').val());
					}
					else if(data == 'required'){
						$('#required-alert').removeClass('hidden');
					}
					else{
						$('#error-alert').removeClass('hidden');
					}
				}
			});
		});

		/*const $vin_model = $('#vin_model');
		const $model_lot = $("#model_lot");*/

		// Load the last value
		/*$vin_model.val('QKR77EE1AY 17').trigger('change');*/

		/*$vin_model.on('change', function() {
			const $self  = $(this);
			const $model = $self.val();

			if ($model != 'undefined' && $model != '')
			{
				$.post(appUrl + 'mis/ajax_get_model_lot', {PRODUCT_MODEL: $model})
				.done(function(data) {
					let result = JSON.parse(data);

					$model_lot.select2({
						data: result
					})
				});

			}
		});*/

	});
</script>

