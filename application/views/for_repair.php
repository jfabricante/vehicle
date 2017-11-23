<?php 
$this->load->helper('format_helper');
?>
<section class="content">
	<div class="row">
		<div class="col-md-10">
			<div class="box box-danger">
				<div class="box-header with-border">
				<h3 class="box-title">Quality Control</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="for_repair_table" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th>CS Number</th>
								<th>Description</th>
								<th>Date Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $ctr=1;
							foreach($result as $row) {?>
							<tr>
								<td><?php echo $ctr; ?></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->DESCRIPTION; ?></td>
								<td><?php echo $row->PROBLEM_CREATED_DATE; ?></td>
								<td style="text-align: center;"><input type="button" name="btn_return" id="btn_return" class="btn btn-danger btn-xs" data-cs_number="<?php echo $row->CS_NUMBER;?>" value="Return to PDI"></td>
							</tr>
							<?php $ctr++; } ?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#for_repair_table').DataTable();

		$('body').on('click','#btn_return',function(){
			var cs_number = $(this).data('cs_number');

			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>buyoff/ajax_return_repair',
				data: {
						cs_number: cs_number
					},
				success: function(data) 
				{
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});

	});
</script>

