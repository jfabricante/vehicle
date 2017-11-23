<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">IVP, NYK and PSI</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th width="20">View</th>
								<th width="50">Status</th>
								<th>CS Number</th>
								<th>Item ID</th>
								<th>Item Model</th>
								<th>Inventory<br />Organization</th>
								<th>Sub-Inventory<br />Organization</th>
								<th>Lot Number</th>
								<th>Buyoff Date</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo $count; ?></td>
								<td style="text-align: center;"><a href="#" class="btn_dr_modal btn btn-primary btn-xs" data-cs_number="<?php echo $row->CS_NUMBER; ?>" ><i class="fa fa-search"></i></a></td>
								<td style="text-align: center;">
									<?php 
										if($row->FOR_REPAIR != ''){echo 'For Repair';}
										else if($row->FOR_TRANSFER != ''){echo 'For Transfer';}
										else { ?>
									<div class="btn-group">
										
										<button disabled type="button" class="btn btn-xs btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											  Buyoff
											<span class="caret"></span>
										</button>
										<?php if($row->FOR_REPAIR == '' && $row->FOR_TRANSFER == '') {?>
										<ul class="dropdown-menu pull-left">
											
												<li><a class="btn_for_transfer" href="#" data-cs_number="<?php echo $row->CS_NUMBER; ?>">Transfer</a></li>
												<li><a class="btn_for_repair" href="#" data-cs_number="<?php echo $row->CS_NUMBER; ?>">Repair</a></li>
											
											<!-- <li><a class="btn_dr_modal" href="#" data-cs_number="<?php echo $row->CS_NUMBER; ?>">View</a></li> -->
											
										</ul>
										<?php }?>
									</div>
									<?php } ?>
								</td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->ITEM_ID; ?></td>
								<td><?php echo $row->ITEM_MODEL; ?></td>
								<td><?php echo $row->ORGANIZATION_CODE; ?></td>
								<td><?php echo $row->SUBINVENTORY_CODE; ?></td>
								<td><?php echo $row->LOT_NUMBER; ?></td>
								<td><?php echo date1($row->BUYOFF_DATE); ?></td>
							</tr>
						<?php 
						$count++;
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
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content">
		
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		$('#dr-list').DataTable({
			
		});
		
		$('body').on('click','a.btn_dr_modal',function(){
			var cs_number = $(this).data('cs_number');
			
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>history/ajax_get_vehicle_details',
				data: {
						cs_number: cs_number
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('.modal-content').html('');
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});

		$('body').on('click','a.btn_for_transfer',function(){
			var cs_number = $(this).data('cs_number');
			
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>buyoff/ajax_for_transfer',
				data: {
						cs_number: cs_number
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('.modal-content').html('');
					$('#myModal2').modal('show');
					$('.modal-content').html(data);
				}
			});
		});

		$('body').on('click','a.btn_for_repair',function(){
			var cs_number = $(this).data('cs_number');
			
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>buyoff/ajax_for_repair',
				data: {
						cs_number: cs_number
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('.modal-content').html('');
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});
	});
</script>

