<form action="<?php echo base_url('index.php/sales/apply_pricelist_patch'); ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>

	<div class="modal-body">
		<div class="form-group hidden">
			<input type="text" class="form-control" id="ITEM_ID" name="ITEM_ID" value="<?php echo isset($entity->ITEM_ID) ? $entity->ITEM_ID : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="PROD_MODEL" name="PROD_MODEL" value="<?php echo isset($entity->PROD_MODEL) ? $entity->PROD_MODEL : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="DESCRIPTION" name="DESCRIPTION" value="<?php echo isset($entity->DESCRIPTION) ? $entity->DESCRIPTION : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="TYPE" name="TYPE" value="<?php echo isset($entity->TYPE) ? $entity->TYPE : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="SALES_MODEL" name="SALES_MODEL" value="<?php echo isset($entity->SALES_MODEL) ? $entity->SALES_MODEL : ''; ?>">
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="STATUS" name="STATUS" value="<?php echo isset($entity->STATUS) ? $entity->STATUS : ''; ?>">
		</div>

		<div class="form-group">
			<label for="PRICELIST">Pricelist</label>
			<input type="text" class="form-control" id="PRICELIST" name="PRICELIST" value="<?php echo isset($entity->PRICELIST) ? $entity->PRICELIST : 'WSP-Vehicle'; ?>" readonly>
		</div>

		<div class="form-group">
			<label for="PRICE">Price</label>
			<input type="number" step="0.01" class="form-control" id="PRICE" name="PRICE" value="<?php echo isset($entity->PRICE) ? $entity->PRICE : ''; ?>" required>
		</div>

		<div class="form-group hidden">
			<input type="text" class="form-control" id="EMP_NO" name="EMP_NO" value="<?php echo $this->session->userdata('employee_number'); ?>">
		</div>
	</div>
	
	<div class="modal-footer">
		<div class="form-group">
			<button type="button" class="btn btn-flat btn-info pull-left" data-dismiss="modal">Close</button>
			<input type="submit" value="Apply Changes" class="btn btn-flat btn-danger">
		</div>
	</div>
	
</form><!-- End Form -->