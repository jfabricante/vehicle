<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/sweetalert/sweetalert.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Select units ready for buyoff</h3>
				</div>
				<div class="row">
					<div class="col-sm-7" style="padding: 10px;margin-left: 20px;">
						<form id="myform" class="form-horizontal" method="POST" accept-charset="utf-8">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="unput1">Select Lot Number</label>
								<div class="col-sm-7">
									<select class="form-control selectpicker" name="lot_number" onchange="this.form.submit()" data-live-search="true">
										<option value="0" <?php echo ($lot_number == 0)? 'selected':'';?>>Nothing Selected</option>
										<?php
										foreach($lots as $row){
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
						</form>
					</div>
					<div class="col-sm-12 text-right" style="padding-right: 27px;">
						<button id="btn-submit" type="button" class="btn btn-danger">Submit Selected</button>
					</div>
				</div>
				<div class="box-body">
					<form id="transfer_form" class="form-horizontal" method="POST" action="submit_selected_for_buyoff">
						<table id="myTable" class="display table table-bordered table-condensed nowrap" cellspacing="3" width="100%" style="font-size: 95%;">
							<thead>
								<tr>
									<th class="text-left">
<!--
										<input type="checkbox" name="select_all">
-->
									&nbsp;
									</th>
									<th class="text-left">Item<br />Model</th>
									<th class="text-left">CS<br />Number</th>
									<th class="text-left">Chassis<br />Number</th>
									<th class="text-left">Body<br />Number</th>
									<th class="text-left">Engine<br />Number</th>
									<th class="text-left">Aircon<br />Number</th>
									<th class="text-left">Stereo<br />Number</th>
									<th class="text-left">Key<br />Number</th>
								</tr>
							</thead>
							<tbody>
						
							<?php
							$ctr = 0;
							foreach($result as $row){
							?>
								<tr>
									<td class="text-center"><input class="cb_cs_number" type="checkbox" value="<?php echo $row->CS_NUMBER; ?>" name="for_buyoff[]"></td>
									<td class="text-left"><?php echo $row->MODEL_CODE; ?></td>
									<td class="text-left"><?php echo $row->CS_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->CHASSIS_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->BODY_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->ENGINE_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->AIRCON_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->STEREO_NUMBER; ?></td>
									<td class="text-left"><?php echo $row->KEY_NUMBER; ?></td>
								</tr>
							<?php
							$ctr++;
							}
							?>
							
							</tbody>
						</table>
					</form>
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
<script src="<?php echo base_url('resources/plugins/sweetalert/sweetalert.min.js');?>"></script>
<script>
	
$(document).ready(function() {
	$('.selectpicker').selectpicker();
	
	var mydataTable = $('#myTable').DataTable();
	
	$( "#btn-submit" ).click(function() {
		var $form = $("#transfer_form");
		var count = 0;
		mydataTable.$('input[type="checkbox"]').each(function(){
			// If checkbox is checked
			if(this.checked){
				// Create a hidden element 
				$form.append(
					$('<input>')
						.attr('type', 'hidden')
						.attr('name', this.name)
						.val(this.value)
				);
				count++;
			} 
		});
		
		if(count > 0){
			swal({
				title:  count + ' unit(s)',
				text: 'Successfully marked as "For Buyoff Units".', 
				type: "success",
				confirmButtonColor: '#DD6B55'
			}).then(function() {
				$form.submit();
			});
			
		}
		
	});
	
	$("input[name=select_all]").on("click", function() {
		var cells = mydataTable.cells( ).nodes();
		$( cells ).find('.cb_cs_number:checkbox').prop('checked', $(this).is(':checked'));
	});
	
});
</script>
