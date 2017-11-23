<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
<!--
				<div class="box-header with-border">
					<h3 class="box-title">Credit Released Units</h3>
				</div>
-->
				<div class="row">
					<div class="col-sm-6" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="unput1">Select Customer</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker" name="customer_id" onchange="this.form.submit()" data-live-search="true">
										<option value="1" <?php echo ($customer_id == 1)? 'selected':'';?>>Select All Customer</option>
										<?php
										foreach($customers as $customer){
										?>
										<option data-subtext="<?php echo $customer->ACCOUNT_NAME;?>" value="<?php echo $customer->CUSTOMER_ID; ?>" <?php echo ($customer_id == $customer->CUSTOMER_ID)? 'selected':'';?> >
											<?php echo $customer->CUSTOMER_NAME;?>
										</option>
										<?php 
										}
										?>
									</select>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="box-body">
					<table id="myTable" class="display table table-bordered table-condensed nowrap" cellspacing="3" width="100%" style="font-size: 95%;">
						<thead>
							<tr>
								<th class="text-left">Account Name</th>
<!--
								<th class="text-left">Fleet Name</th>
-->
								<th class="text-center">CS Number</th>
								<th class="text-left">Sales Model</th>
								<th class="text-left">Body Color</th>
								<th class="text-left">Order Number</th>
								<th class="text-left">Line Number</th>
								<th class="text-left">Released Date</th>
								<th class="text-left">Current Status</th>
								<th class="text-left">Next Step</th>
<!--
								<th class="text-right">Gross Amount</th>
-->
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($result as $row){
							?>
								<tr class="<?php echo $row->RELEASED_FLAG != NULL ? 'success':''; ?>">
									<td class="text-left"><?php echo $row->ACCOUNT_NAME; ?></td>
<!--
									<td class="text-left"><?php echo $row->FLEET_NAME; ?></td>
-->
									<td class="text-center"><?php echo $row->CS_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->SALES_MODEL; ?></td>
									<td class="text-left"><?php echo $row->BODY_COLOR; ?></td>
									<td class="text-center"><?php echo $row->ORDER_NUMBER; ?></td>
									<td class="text-center"><?php echo $row->LINE_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->RELEASED_DATE; ?></td>
									<td class="text-left"><?php echo $row->CURRENT_STATUS; ?></td>
									<td class="text-left"><?php echo $row->NEXT_STEP; ?></td>
<!--
									<td class="text-right"><?php echo number_format($row->GROSS_AMOUNT,2); ?></td>
-->
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
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
		<div class="modal-content">
		
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal_cancel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Picklist Cancellation</h4>
      </div>
      <div class="modal-body" id="modal_cancel_body">
    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btn_cancel_yes">Yes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal" id="btn_cancel_close">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script>
	
$(document).ready(function() {
	$('.selectpicker').selectpicker();
	
	$('#myTable').DataTable({
		'order' : [[ 4, 'desc' ]],
		'scrollX' : true
	});
});
</script>
