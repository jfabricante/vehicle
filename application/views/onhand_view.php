<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Isuzu Vehicle Sales (IVS)</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th></th>
								<th>Item ID</th>
								<th>Production Model</th>
								<th>Sales Model</th>
								<th>Total Quantity</th>
								<th>Available To Reserve</th>
								<th>Available To Tag</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result as $row){
						?>
							<tr>
								<td><a target="_blank" href="onhand_details/<?php echo $row->INVENTORY_ITEM_ID; ?>" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a></td>
								<td><?php echo $row->INVENTORY_ITEM_ID; ?></td>
								<td><?php echo $row->PROD_DESCRIPTION; ?></td>
								<td><?php echo $row->SALES_MODEL; ?></td>
								<td class="text-center"><?php echo $row->ONHAND; ?></td>
								<td class="text-center"><?php echo $row->AVAILABLE_TO_RESERVE; ?></td>
								<td class="text-center"><?php echo $row->AVAILABLE_TO_TAG; ?></td>
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

		$('#dr-list').DataTable();
		
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

