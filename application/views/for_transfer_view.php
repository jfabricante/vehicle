<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/sweetalert/sweetalert.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Select units for receiving from IVP</h3>
				</div>
				<div class="row">
					<div class="col-sm-7" style="padding: 10px;margin-left: 20px;">
						<form id="myform" class="form-horizontal" method="POST" accept-charset="utf-8">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="unput1">Select Lot Number</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker" name="lot_number" onchange="this.form.submit()" data-live-search="true">
										<option value="0" <?php echo ($lot_number == 0)? 'selected':'';?>>Nothing Selected</option>
										<option value="1" <?php echo ($lot_number == 1)? 'selected':'';?>>Select All</option>
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
						<button id="btn-selected" type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModal">
							View Selected
						</button>
					</div>
				</div>
				<div class="box-body">
				
					<table id="myTable" class="display table table-bordered table-condensed nowrap" cellspacing="3" width="100%" style="font-size: 95%;">
						<thead>
							<tr>
								<th class="text-left">
<!--
									<input type="checkbox" name="select_all">
-->
								</th>
								<th class="text-left">Production<br />Model</th>
								<th class="text-left">CS<br />Number</th>
								<th class="text-left">Chassis<br />Number</th>
								<th class="text-left">Body<br />Number</th>
								<th class="text-left">Engine<br />Number</th>
								<th class="text-left">Key<br />Number</th>
								<th class="text-left">Aircon<br />Number</th>
								<th class="text-left">Stereo<br />Number</th>
								<th class="text-left">Buyoff<br />Date</th>
							</tr>
						</thead>
						<tbody>
					
						<?php
						$ctr = 0;
						foreach($result as $row){
						?>
							<tr>
								<td class="text-left">
									<input 	class="cb_cs_number" 
											type="checkbox" 
											data-prod_model="<?php echo $row->ITEM_MODEL; ?>" 
											data-cs_number="<?php echo $row->CS_NUMBER; ?>" 
											data-chassis_number="<?php echo $row->CHASSIS_NUMBER; ?>" 
											data-body_number="<?php echo $row->BODY_NUMBER; ?>" 
											data-engine_number="<?php echo $row->ENGINE_NUMBER; ?>" 
											data-key_number="<?php echo $row->KEY_NUMBER; ?>" 
											value="<?php echo $row->CS_NUMBER; ?>" 
											name="transfer[]"
									>
								</td>
								<td class="text-left"><?php echo $row->ITEM_MODEL; ?></td>
								<td class="text-left"><?php echo $row->CS_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->CHASSIS_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->BODY_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->ENGINE_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->KEY_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->AIRCON_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->STEREO_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->BUYOFF_DATE; ?></td>
							</tr>
						<?php
						$ctr++;
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

<form id="transfer_form" class="form-horizontal" method="POST" action="transfer_nyk">
					
</form>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">List of Units for Receiving</h4>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th class="text-left">Production<br />Model</th>
							<th class="text-left">CS<br />Number</th>
							<th class="text-left">Chassis<br />Number</th>
							<th class="text-left">Body<br />Number</th>
							<th class="text-left">Engine<br />Number</th>
							
							<th class="text-left">Key<br />Number</th>
						</tr>
					</thead>
					<tbody id="tbody">
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="btn-submit" type="button" class="btn btn-danger">Receive All</button>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/sweetalert/sweetalert.min.js');?>"></script>
<script>
	
$(document).ready(function() {
	$('.selectpicker').selectpicker();
	
	var mydataTable = $('#myTable').DataTable();
	
	var count = 0;
	
	$( "#btn-submit" ).click(function() {
		
		var $form = $("#transfer_form");
		swal({
				title:  count + ' unit(s)',
				text: 'Successfully marked as "Received Units".', 
				type: "success",
				confirmButtonColor: '#DD6B55'
			}).then(function() {
				$form.submit();
		});	
		
	});

	$( "#btn-selected" ).click(function() {
		
		$('table.table tbody#tbody').html('');
		$('#transfer_form').html('');
		$("#btn-submit").attr("disabled", "disabled");
		var $form = $("#transfer_form");
		var $tbody = $("tbody#tbody");
		count = 0;
		
		
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
				//create table row
				$('table.table > tbody#tbody:last-child').append('<tr>' +
																'<td>' + $(this).data('prod_model') + '</td>' +
																'<td>' + $(this).data('cs_number') + '</td>' +
																'<td>' + $(this).data('chassis_number') + '</td>' +
																'<td>' + $(this).data('body_number') + '</td>' +
																'<td>' + $(this).data('engine_number') + '</td>' +
																'<td>' + $(this).data('key_number') + '</td>' +
															'</tr>');
				count++;
			} 
		});
		if(count > 0){
			$("#btn-submit").removeAttr("disabled");
		}
	});
	
	$("input[name=select_all]").on("click", function() {
		var cells = mydataTable.cells( ).nodes();
		$( cells ).find('.cb_cs_number:checkbox').prop('checked', $(this).is(':checked'));
	});
	
});
</script>
