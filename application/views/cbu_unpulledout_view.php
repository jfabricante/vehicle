<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">CBU Pulledout Units</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th>CS Number</th>
								<th>Invoice Number</th>
								<th>Invoice Date</th>
								<th>Customer Name</th>
								<th>Account Name</th>
								<th>Sales Model</th>
								<th>Body Color</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo $count; ?></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->TRX_NUMBER; ?></td>
								<td><?php echo date('m/d/Y', strtotime($row->TRX_DATE)); ?></td>
								<td><?php echo $row->PARTY_NAME; ?></td>
								<td><?php echo $row->ACCOUNT_NAME; ?></td>
								<td><?php echo $row->SALES_MODEL; ?></td>
								<td><?php echo $row->BODY_COLOR; ?></td>
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

<script>
	$(document).ready(function() {

		$('#dr-list').DataTable({
			
		});
		
		
	});
</script>
