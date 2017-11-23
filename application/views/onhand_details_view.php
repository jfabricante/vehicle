<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php 
			if(!empty($result2)){
			?>
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Reserved w/out Tagged</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>Account Name</th>
								<th>Order Number</th>
								<th>Line Number</th>
								<th>Reservation Date</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result2 as $row){
						?>
							<tr>
								
								<td><?php echo $row->ACCOUNT_NAME; ?></td>
								<td><?php echo $row->ORDER_NUMBER; ?></td>
								<td><?php echo $row->LINE_NUMBER; ?></td>
								<td><?php echo date1($row->RESERVATION_DATE); ?></td>
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
			<?php 
			}
			?>
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Reserved w/ Tagged and Available to Tag Units</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								
								<th>CS Number</th>
								<th>Lot Number</th>
								<th>Subinventory Code</th>
								<th>Account Name</th>
								<th>Order Number</th>
								<th>Line Number</th>
								<th>Reservation Date</th>
								<th>Tagged Date</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo $row->SERIAL_NUMBER; ?></td>
								<td><?php echo $row->LOT_NUMBER; ?></td>
								<td><?php echo $row->SUBINVENTORY_CODE; ?></td>
								<td><?php echo $row->ACCOUNT_NAME; ?></td>
								<td><?php echo $row->ORDER_NUMBER; ?></td>
								<td><?php echo $row->LINE_NUMBER; ?></td>
								<td><?php echo date1($row->RESERVATION_DATE); ?></td>
								<td><?php echo date1($row->TAGGED_DATE); ?></td>
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

<script>
	$(document).ready(function() {

		$('#dr-list').DataTable({
			 "order": [[ 5, "desc" ]]
		});
		
		$('body').on('click','a.btn_dr_modal',function(){
			var item_id = $(this).data('item_id');
			
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>inventory/ajax_onhand_details',
				data: {
						item_id: item_id
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});
		
	});
</script>

