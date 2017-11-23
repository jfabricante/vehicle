<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title show" style="font-size: 15px;"><strong>Vehicle Details</strong></h3>
				</div>
				<div class="box-body">
					<div class="col-sm-12">
						<div class="col-sm-3">
							<strong>CS Number</strong>
							<p class="text-muted">
								<?php echo $header->cs_no; ?>
							</p>
							<strong>V.I.N.</strong>
							<p class="text-muted">
								<?php echo $header->vin; ?>
							</p>
							<strong>Prod Model</strong>
							<p class="text-muted">
								<?php echo $header->prod_model; ?>
							</p>
							<strong>Sales Model</strong>
							<p class="text-muted">
								<?php echo $header->sales_model; ?>
							</p>
							<strong>Body Color</strong>
							<p class="text-muted">
								<?php echo $header->body_color; ?>
							</p>
							<strong>Body Number</strong>
							<p class="text-muted">
								<?php echo $header->body_no; ?>
							</p>
							<strong>Lot Number</strong>
							<p class="text-muted">
								<?php echo $header->lot_no; ?>
							</p>
							<strong>Production Order No</strong>
							<p class="text-muted">
								<?php echo $header->prod_order_no; ?>
							</p>
							<strong>Serial Number</strong>
							<p class="text-muted">
								<?php echo $header->serial_no; ?>
							</p>
						 </div>
						 <div class="col-sm-3">
							<strong>Engine Type</strong>
							<p class="text-muted">
								<?php echo $header->engine_type; ?>
							</p>
							<strong>Engine Number</strong>
							<p class="text-muted">
								<?php echo $header->engine_no; ?>
							</p>
							<strong>Aircon Brand</strong>
							<p class="text-muted">
								<?php echo ($header->aircon_brand == NULL)? '-':$header->aircon_brand; ?><br/>
							</p>
							<strong>Aircon Number</strong>
							<p class="text-muted">
								<?php echo ($header->aircon_no == NULL)? '-':$header->aircon_no; ?><br/>
							</p>
							<strong>Stereo Brand</strong>
							<p class="text-muted">
								<?php echo ($header->stereo_brand == NULL)? '-':$header->stereo_brand; ?><br/>
							</p>
							<strong>Stereo Number</strong>
							<p class="text-muted">
								<?php echo ($header->stereo_no == NULL)? '-':$header->stereo_no; ?><br/>
							</p>
							<strong>Key Number</strong>
							<p class="text-muted">
								<?php echo ($header->key_no == NULL)? '-':$header->key_no; ?><br/>
							</p>
							<strong>Buyoff Date</strong>
							<p class="text-muted">
								<?php echo ($header->buyoff_date == NULL)? '-':date('m/d/Y', strtotime($header->buyoff_date)); ?>
							</p>
							<strong>FM Date</strong>
							<p class="text-muted">
								<?php echo ($header->fm_date == NULL)? '-':date('m/d/Y', strtotime($header->fm_date)); ?>
							</p>
						</div>
						<div class="col-sm-3">
							<strong>CSR Number</strong>
							<p class="text-muted">
								<?php echo ($header->csr_no == NULL)? '-':$header->csr_no; ?><br/>
							</p>
							<strong>CSR OR Number</strong>
							<p class="text-muted">
								<?php echo ($header->csr_or_no == NULL)? '-':$header->csr_or_no; ?><br/>
							</p>
							<strong>CSR Date</strong>
							<p class="text-muted">
								<?php echo ($header->csr_date == NULL)? '-':date('m/d/Y', strtotime($header->csr_date)); ?>
							</p>
							<strong>Sales Order</strong>
							<p class="text-muted">
								<?php echo ($header->sales_order == NULL)? '-':$header->sales_order; ?><br/>
							</p>
							<strong>Allocation Date</strong>
							<p class="text-muted">
								<?php echo ($header->tagged_date == NULL)? '-':date('m/d/Y', strtotime($header->tagged_date)); ?>
							</p>
							<strong>Invoice Number</strong>
							<p class="text-muted">
								<?php echo ($header->invoice_no == NULL)? '-':$header->invoice_no; ?><br/>
							</p>
							<strong>Invoice Date</strong>
							<p class="text-muted">
								<?php echo ($header->invoice_date == NULL)? '-':date('m/d/Y', strtotime($header->invoice_date)); ?>
							</p>
							<strong>Delivery Date</strong>
							<p class="text-muted">
								<?php echo ($header->pullout_date == NULL)? '-':date('m/d/Y', strtotime($header->pullout_date)); ?>
							</p>
							<strong>Payment Date</strong>
							<p class="text-muted">
								<?php echo ($header->payment_date == NULL)? '-':date('m/d/Y', strtotime($header->payment_date)); ?>
							</p>
						</div>
						<div class="col-sm-3">
							<strong>Customer ID</strong>
							<p class="text-muted">
								<?php echo ($header->customer_id == NULL)? '-':$header->customer_id; ?><br/>
							</p>
							<strong>Customer Name</strong>
							<p class="text-muted">
								<?php echo ($header->customer_name == NULL)? '-':$header->customer_name; ?><br/>
							</p>
							<strong>WB Number</strong>
							<p class="text-muted">
								<?php echo $header->wb_no; ?>
							</p>
							<?php 
							if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1','sales 2') )){
							?>
							<strong>Net Amount</strong>
							<p class="text-muted">
								<?php echo ($header->net_amount == NULL)? '-':number_format($header->net_amount,2); ?><br/>
							</p>
							<strong>VAT Amount</strong>
							<p class="text-muted">
								<?php echo ($header->vat_amount == NULL)? '-':number_format($header->vat_amount,2); ?><br/>
							</p>
							<strong>Invoice Amount</strong>
							<p class="text-muted">
								<?php echo ($header->net_amount == NULL)? '-':number_format($header->vat_amount + $header->net_amount,2); ?><br/>
							</p>
							<?php 
							}
							?>
							<strong>Last Update</strong>
							<p class="text-muted">
								<?php echo date('m/d/Y', strtotime($header->last_update)); ?>
							</p>
							<strong>Source</strong>
							<p class="text-muted">
								<?php echo $header->source; ?>
							</p>
						</div>
					</div>
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

	});
</script>
