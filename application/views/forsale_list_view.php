<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">List Per Model/Color</h3>
				</div>
<!-- 				<div class="form-group" style="margin-left: 10px; margin-top: 10px; margin-bottom: 50px;">
			        <a href="<?php echo base_url();?>forsale/report" class="btn btn-danger pull-left" type="button" style="margin-left: 10px;"><i class="fa fa-download"> Generate Report</i></a>
			    </div> -->
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th>View</th>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Body Color</th>
								<th>Chassis Number</th>
								<th>CSR Date</th>
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
								<td><a href="#" class="btn_dr_modal btn btn-primary btn-xs" data-cs_number="<?php echo $row->CS_NUMBER; ?>" ><i class="fa fa-search"></i></a></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->SALES_MODEL; ?></td>
								<td><?php echo $row->BODY_COLOR; ?></td>
								<td><?php echo $row->CHASSIS_NUMBER; ?></td>
								<td><?php echo date1($row->CSR_DATE); ?></td>
								<td><?php echo date1($row->BUYOFF_DATE); ?></td>
							</tr>
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
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});
	});
</script>

