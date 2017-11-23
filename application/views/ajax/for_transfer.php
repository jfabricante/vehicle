
<div class="modal-content">
	<div class="modal-header">
		<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
		<h4 class="modal-title">Are you sure that <?php echo $cs_no; ?> is good and for transfer to IVS?</h4>
	</div>
	<div class="modal-footer">
		<form id="myForm" role="form" class="form-horizontal" method="POST" action="<?php echo base_url(); ?>buyoff/transfer" enctype="multipart/form-data">
				<div class="text-right">
					<button type="submit" class="btn btn-danger">Yes</button>
					<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">No</button>
					<input type="hidden" name="cs_no" value="<?php echo $cs_no; ?>">
				</div> 
		</form>
	</div>
</div><!-- /.modal-content -->
