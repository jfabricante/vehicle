<div class="modal-header">
	<h4 class="modal-title">Serial Number Details <span class="pull-right">MIS ID : <?php echo sprintf('%05d', $mis_id); ?></span></h4>
</div>
<div id="saved-alert" class="hidden alert alert-success alert-dismissable">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<strong>Serial number details </strong> saved successfully!
</div>
<div id="required-alert" class="hidden alert alert-warning alert-dismissable">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	Fields mark with an asterisk (*) are required and cannot be left blank.
</div>
<div id="error-alert" class="hidden alert alert-warning alert-dismissable">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	There was an error saving serial number details. Please make sure your cs number and chassis number is unused. Thank you.
</div>
<form id="msn-details-form" class="form-horizontal" method="POST">
	<input type="hidden" class="form-control" name="mis_id" value="<?php echo $mis_id; ?>">
	<input type="hidden" class="form-control" name="last_updated_by" value="<?php echo $this->session->userdata('employee_number'); ?>">
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="col-sm-2 control-label">Serial Number </label>
					<div class="col-sm-4">
						<input readonly type="text" class="form-control" name="serial_no" value="<?php echo $serial_no; ?>">
					</div>
					<label class="col-sm-2 control-label">Lot Number </label>
					<div class="col-sm-4">
						<input readonly type="text" class="form-control" name="lot_no" value="<?php echo $lot_no; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Model Name</label>
					<div class="col-sm-10">
						<input readonly type="text" class="form-control" name="model_name" value="<?php echo $model_name; ?>">
					</div>
				</div>
				<hr />
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="col-sm-5 control-label">CS Number <span class="text-red">*</span></label>
					<div class="col-sm-7">
						<input <?php echo ($this->session->userdata('user_type') == 'manufacturing 2') ? 'readonly':''; ?> <?php echo ($row->CS_NO == NULL)? 'required':'readonly'; ?> type="text" placeholder="CS Number" class="form-control text-uppercase" name="cs_no" value="<?php echo $row->CS_NO; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Chassis Number <span class="text-red">*</span></label>
					<div class="col-sm-7">
						<?php 
						if($row->VIN_NO == NULL){
						?>
						<select class="select2 form-control" <?php //echo ($row->VIN_NO == NULL)? 'required':'disabled'; ?> name="vin">
							<option value="">NOTHING SELECTED</option>
							<?php 
							foreach($vins as $vin){
							?>
							<option <?php echo ($row->VIN_NO == $vin->VIN) ? 'selected':''; ?> value="<?php echo $vin->VIN; ?>"><?php echo $vin->VIN; ?></option>
							<?php 
							}
							?>
						</select>
						<input type="hidden"  name="chassis_no" value=" <?php echo $row->VIN_NO; ?>" />
						<?php 
						}
						else{
						?>
						<input readonly type="text" class="form-control"  name="chassis_no" value=" <?php echo $row->VIN_NO; ?>" />
						<input type="hidden" class="form-control"  name="vin" value=" <?php echo $row->VIN_NO; ?>" />
						<?php 
						}
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Engine Number</label>
					<div class="col-sm-7">
						<input readonly type="text" placeholder="Engine Number" class="form-control text-uppercase" name="engine_no" value="<?php echo $row->ENGINE_NO; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Body Number</label>
					<div class="col-sm-7">
						<input <?php echo ($this->session->userdata('user_type') == 'manufacturing 2') ? 'readonly':''; ?> type="text" placeholder="Body Number" class="form-control text-uppercase" name="body_no" value="<?php echo $row->BODY_NO; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">FM Date</label>
					<div class="col-sm-7">
						<input <?php echo ($this->session->userdata('user_type') == 'manufacturing 2') ? 'readonly':''; ?> type="text" placeholder="FM Date" class="form-control datemask" name="fm_date" value="<?php echo ($row->FM_OFF_DATE == NULL)? '': date('m/d/Y', strtotime($row->FM_OFF_DATE)); ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Remarks</label>
					<div class="col-sm-7">
						<textarea class="form-control"  name="remarks" rows="4" <?php echo ($this->session->userdata('user_type') == 'manufacturing 2') ? 'hidden':''; ?>><?php echo $row->REMARKS; ?></textarea>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="col-sm-5 control-label">Key Number</label>
					<div class="col-sm-7">
						<input <?php echo ($this->session->userdata('user_type') == 'manufacturing 3') ? 'readonly':''; ?> type="text" placeholder="Key Number" class="form-control text-uppercase" name="key_no" value="<?php echo $row->KEY_NO; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Aircon Number</label>
					<div class="col-sm-7">
						<input <?php echo ($this->session->userdata('user_type') == 'manufacturing 3') ? 'readonly':''; ?> type="text" placeholder="Aircon Number" class="form-control text-uppercase" name="aircon_no" value="<?php echo $row->AC_NO; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Stereo Number</label>
					<div class="col-sm-7">
						<input <?php echo ($this->session->userdata('user_type') == 'manufacturing 3') ? 'readonly':''; ?> type="text" placeholder="Stereo Number" class="form-control text-uppercase" name="stereo_no" value="<?php echo $row->STEREO_NO; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-5 control-label">Buyoff Date</label>
					<div class="col-sm-7">
						<input disabled type="text" placeholder="Buyoff Date" class="form-control datemask" name="buyoff_date" value="<?php echo ($row->BUY_OFF_DATE == NULL)? '':date('m/d/Y', strtotime($row->BUY_OFF_DATE)); ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<?php 
		if($row->LAST_UPDATED_BY != 'NYK' AND $row->BUY_OFF_DATE == NULL){
		?>
		<button id="save-msn-details" type="button" class="btn btn-flat btn-danger ">Save</button>
		<?php 
		}
		?>
		<button data-dismiss="modal" class="btn btn-flat btn-default" type="button">Cancel</button>
	</div>
</form>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.regex.extensions.js'); ?>"></script>

<script>
	$(document).ready(function(){
		
		$('.select2').select2();
		
		//~ $('input[name=cs_no]').inputmask();
		$('input[name=cs_no]').inputmask('a**999');
		
		$('select[name=vin]').change(function(){
			var vin = $(this).val();
			$.ajax({
				type: 'POST',
				data: {
					vin : vin
				},
				url: '<?php echo base_url();?>mis/ajax_get_engine',
				success: function(data){
					$('input[name=engine_no]').val(data);
					$('input[name=chassis_no]').val(vin);
				}
			});
		});
	});
</script>
