<link href="<?php echo base_url('resources/plugins/tokenfield/css/bootstrap-tokenfield.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div id="overlay" class="overlay hide">
		            <i class="fa fa-refresh fa-spin"></i>
		        </div>

				<div class="box-header with-border">
					<h3 class="box-title"><?php echo $title_view; ?></h3>
					<?php if($type == 'new_units') { ?>
						<a href="#" id="insert_units" class="btn btn-flat btn-danger btn-sm pull-right">Insert New Units</a>
					<?php } else if($type == 'new_csr') { ?>
						<a href="#" id="update_csr" class="btn btn-flat btn-danger btn-sm pull-right">Update CSR</a>
					<?php } else if($type == 'new_invoice') { ?>
						<a href="#" id="update_invoice" class="btn btn-flat btn-danger btn-sm pull-right">Update Invoice</a>
					<?php } else if($type == 'new_so') { ?>
						<a href="#" id="update_so" class="btn btn-flat btn-danger btn-sm pull-right">Update Sales Order</a>
					<?php } else if($type == 'new_pullout') { ?>
						<a href="#" id="update_pullout" class="btn btn-flat btn-danger btn-sm pull-right">Update Pullout Date</a>
					<?php } ?>
					
				</div>

				<div id="result" class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="pending_list" class="display" cellspacing="0" width="100%" style="font-size: 85%;">
						<thead>
							<tr>
								<th>#</th>
								<?php if($type == 'new_units') { ?>
									<th>VIN</th>
									<th>Engine #</th>
									<th>CS #</th>
									<th>Engine Type</th>
									<th>Body #</th>
									<th>Lot #</th>
									<th>Key #</th>
									<th>Model</th>
									<th>Prod Model</th>
									<th>Body Color</th>
									<th>Production Date</th>
								<?php } else if($type == 'new_csr') { ?>
									<th>CS #</th>
									<th>CSR #</th>
									<th>CSR Date</th>
									<th>OR #</th>
									<th>MR Process Date</th>
								<?php } else if($type == 'new_invoice') { ?>
									<th>CS #</th>
									<th>Invoice #</th>
									<th>Invoice Price</th>
									<th>WB #</th>
									<th>Invoice Date</th>
								<?php } else if($type == 'new_so') { ?>
									<th>CS #</th>
									<th>Order #</th>
									<th>Order Code</th>
									<th>Pullout Dealer ID</th>
								<?php } else if($type == 'new_pullout') { ?>
									<th>CS #</th>
									<th>Pullout Date</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo $count; ?></td>
								<?php if($type == 'new_units') { ?>
									<td><?php echo $row->vin; ?></td>
									<td><?php echo $row->engine_no; ?></td>
									<td><?php echo $row->cs_no; ?></td>
									<td><?php echo $row->engine_type; ?></td>
									<td><?php echo $row->body_no; ?></td>
									<td><?php echo $row->lot_no; ?></td>
									<td><?php echo $row->key_no; ?></td>
									<td><?php echo $row->model; ?></td>
									<td><?php echo $row->prod_model; ?></td>
									<td><?php echo $row->body_color; ?></td>
									<td><?php echo $row->production_date; ?></td>
								<?php } else if($type == 'new_csr') { ?>
									<td><?php echo $row->cs_no; ?></td>
									<td><?php echo $row->csr_no; ?></td>
									<td><?php echo $row->csr_date; ?></td>
									<td><?php echo $row->or_no; ?></td>
									<td><?php echo $row->mr_process_date; ?></td>
								<?php } else if($type == 'new_invoice') {?>
									<td><?php echo $row->cs_no; ?></td>
									<td><?php echo $row->invoice_no; ?></td>
									<td><?php echo number_format($row->invoice_price,2); ?></td>
									<td><?php echo $row->wb_no; ?></td>
									<td><?php echo $row->invoice_date; ?></td>
								<?php } else if($type == 'new_so') { ?>
									<td><?php echo $row->cs_no; ?></td>
									<td><?php echo $row->order_no; ?></td>
									<td><?php echo $row->order_code; ?></td>
									<td><?php echo $row->pullout_dealer_id; ?></td>
								<?php } else if($type == 'new_pullout') { ?>
									<td><?php echo $row->cs_no; ?></td>
									<td><?php echo $row->pullout_date; ?></td>
								<?php } ?>
							</tr>
						<?php $count++; } ?>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/tokenfield/bootstrap-tokenfield.min.js');?>"></script>
<script>
	$(document).ready(function() {
		$('#pending_list').DataTable({});

		$('#insert_units').click(function(){
			$('#overlay').removeClass();
            $('#overlay').addClass('overlay');
			$.ajax({
				url: "<?php echo base_url();?>crms/insert_new_vehicle",
				method: 'POST',
				dataType: 'text',
				data: {},  
				success: function(){
					window.location.href = "<?php echo base_url(); ?>crms/new_units";
				},
			});
		});

		$('#update_csr').click(function(){
			$('#overlay').removeClass();
            $('#overlay').addClass('overlay');
			$.ajax({
				url: "<?php echo base_url();?>crms/updateCSR",
				method: 'POST',
				dataType: 'text',
				data: {},  
				success: function(){
					window.location.href = "<?php echo base_url(); ?>crms/csr";
				},
			});
		});

		$('#update_invoice').click(function(){
			$('#overlay').removeClass();
            $('#overlay').addClass('overlay');
			$.ajax({
				url: "<?php echo base_url();?>crms/updateInvoice",
				method: 'POST',
				dataType: 'text',
				data: {},  
				success: function(){
					window.location.href = "<?php echo base_url(); ?>crms/invoice";
				},
			});
		});

		$('#update_so').click(function(){
			$('#overlay').removeClass();
            $('#overlay').addClass('overlay');
			$.ajax({
				url: "<?php echo base_url();?>crms/updateSO",
				method: 'POST',
				dataType: 'text',
				data: {},  
				success: function(){
					window.location.href = "<?php echo base_url(); ?>crms/so";
				},
			});
		});

		$('#update_pullout').click(function(){
			$('#overlay').removeClass();
            $('#overlay').addClass('overlay');
			$.ajax({
				url: "<?php echo base_url();?>crms/updatePullout",
				method: 'POST',
				dataType: 'text',
				data: {},  
				success: function(){
					window.location.href = "<?php echo base_url(); ?>crms/pullout";
				},
			});
		});

	});
</script>
