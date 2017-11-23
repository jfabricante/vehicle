<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css');?>" rel="stylesheet" >
<form action="<?php echo base_url('index.php/sales/apply'); ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>

	<div class="modal-body">
		<div class="form-group hidden">
			<input type="text" class="form-control" id="item_id" name="item_id" value="<?php echo isset($entity->item_id) ? $entity->item_id : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="prod_model" name="prod_model" value="<?php echo isset($entity->prod_model) ? $entity->prod_model : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="description" name="description" value="<?php echo isset($entity->description) ? $entity->description : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="type" name="type" value="<?php echo isset($entity->type) ? $entity->type : ''; ?>">
		</div>

		<div class="form-group">
			<label for="sales_model">Sales Model</label>
			<input type="text" class="form-control" id="sales_model" name="sales_model" value="<?php echo isset($entity->sales_model) ? $entity->sales_model : ''; ?>" >
		</div>

		<div class="form-group">
			<label for="status">Status</label>
			<select name="status" id="status" class="form-control selectpicker" data-live-search="true" required>
				<option></option>
				<?php $status = array('Active', 'Inactive'); ?>
				<?php foreach($status as $item): ?>
					<option value="<?php echo $item; ?>" <?php echo isset($entity->status) ? $item == $entity->status ? 'selected' : '' : ''; ?> ><?php echo $item; ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="emp_no" name="emp_no" value="<?php echo $this->session->userdata('employee_number'); ?>">
		</div>
	</div>
	
	<div class="modal-footer">
		<div class="form-group">
			<button type="button" class="btn btn-flat btn-info pull-left" data-dismiss="modal">Close</button>
			<input type="submit" value="Apply Changes" class="btn btn-flat btn-danger">
		</div>
	</div>
	
</form><!-- End Form -->
<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.selectpicker').selectpicker({});
	});
</script>
					