<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title show" style="font-size: 15px;"><strong>Vehicle Details</strong> (<?php echo $header->SOURCE; ?>)</h3>
				</div>
				<div class="box-body">
					<div class="col-sm-12">
						<div class="col-sm-3">
							<strong>CS Number</strong>
							<p class="text-muted">
								<?php echo $header->CS_NUMBER; ?>
							</p>
							<strong>Chassis Number</strong>
							<p class="text-muted">
								<?php echo $header->CHASSIS_NUMBER; ?>
							</p>
							<strong>Engine Model / Number</strong>
							<p class="text-muted">
								<?php echo $header->ENGINE; ?>
							</p>
							<strong>Body Number</strong>
							<p class="text-muted">
								<?php echo ($header->BODY_NUMBER == NULL)? '-':$header->BODY_NUMBER; ?>
							</p>
							<strong>Lot Number</strong>
							<p class="text-muted">
								<?php echo $header->LOT_NUMBER; ?>
							</p>
							<strong>Production Model</strong>
							<p class="text-muted">
								<?php echo $header->PROD_MODEL; ?>
							</p>
							<strong>Production Model Description</strong>
							<p class="text-muted">
								<?php echo $header->PROD_MODEL_DESC; ?>
							</p>
							
							<strong>Sales Model</strong>
							<p class="text-muted">
								<?php echo ($header->SALES_MODEL == NULL)? '-':$header->SALES_MODEL; ?>
							</p>
							 <strong>Production Order No</strong>
							<p class="text-muted">
								<?php echo ($header->SHOP_ORDER_NUMBER == NULL)? '-':$header->SHOP_ORDER_NUMBER; ?>
							</p>
							<strong>Serial Number</strong>
							<p class="text-muted">
								<?php echo ($header->SERIAL_NUMBER == NULL)? '-':$header->SERIAL_NUMBER; ?>
							</p>
							
						</div>
						 <div class="col-sm-3">
							
							<strong>Inventory</strong>
							<p class="text-muted">
								<?php echo ($header->ORGANIZATION_CODE == NULL)? '-':$header->ORGANIZATION_CODE; ?>
							</p>
							<strong>Sub Inventory</strong>
							<p class="text-muted">
								<?php echo ($header->CURRENT_SUBINVENTORY_CODE == NULL)? '-':$header->CURRENT_SUBINVENTORY_CODE; ?>
							</p>
							<strong>Body Color</strong>
							<p class="text-muted">
								<?php echo ($header->BODY_COLOR == NULL)? '-':$header->BODY_COLOR; ?>
							</p>
							
							<strong>Aircon Brand / Number</strong>
							<p class="text-muted">
								<?php echo ($header->AIRCON == NULL OR $header->AIRCON == ' ')? '-':$header->AIRCON; ?>
							</p>
							<strong>Stereo Brand / Number</strong>
							<p class="text-muted">
								<?php echo ($header->STEREO == NULL OR $header->STEREO == ' ')? '-':$header->STEREO; ?>
							</p>
							<strong>Key Number</strong>
							<p class="text-muted">
								<?php echo ($header->KEY_NO == NULL)? '-':$header->KEY_NO; ?>
							</p>
							<strong>FM Date</strong>
							<p class="text-muted">
								<?php echo ($header->FM_DATE == NULL)? '-':date('m/d/Y', strtotime(str_replace('/', '', $header->FM_DATE))); ?>
							</p>
							<strong>Buyoff Date</strong>
							<p class="text-muted">
								<?php echo ($header->BUYOFF_DATE == NULL)? '-':date('m/d/Y', strtotime($header->BUYOFF_DATE)); ?>
							</p>
							<strong>CSR Number</strong>
							<p class="text-muted">
								<?php echo ($header->CSR_NUMBER == NULL)? '-':$header->CSR_NUMBER; ?>
							</p>
							<strong>CSR OR Number</strong>
							<p class="text-muted">
								<?php echo ($header->CSR_OR_NUMBER == NULL)? '-':$header->CSR_OR_NUMBER; ?>
							</p>
							<strong>CSR Date</strong>
							<p class="text-muted">
								<?php echo ($header->CSR_DATE == NULL)? '-':date('m/d/Y', strtotime($header->CSR_DATE)); ?>
							</p>
							
						</div>
						<div class="col-sm-3">
							<strong>Order Number</strong>
							<p class="text-muted">
								<?php echo ($header->ORDER_NUMBER == NULL)? '-':$header->ORDER_NUMBER; ?>
							</p>
							<strong>Order Date</strong>
							<p class="text-muted">
								<?php echo ($header->ORDERED_DATE == NULL)? '-':date('m/d/Y', strtotime($header->ORDERED_DATE)); ?>
							</p>
							<strong>Allocation Date</strong>
							<p class="text-muted">
								<?php echo ($header->TAGGED_DATE == NULL)? '-':date('m/d/Y', strtotime($header->TAGGED_DATE)); ?>
							</p>
							<strong>Sales Order Type</strong>
							<p class="text-muted">
								<?php echo $header->ORDER_TYPE; ?>
							</p>
							<strong>Customer ID</strong>
							<p class="text-muted">
								<?php echo ($header->CUSTOMER_ID == NULL)? '-':$header->CUSTOMER_ID; ?>
							</p>
							<strong>Customer Name</strong>
							<p class="text-muted">
								<?php echo ($header->PARTY_NAME == NULL)? '-':$header->PARTY_NAME; ?>
							</p>
							<strong>Account Name</strong>
							<p class="text-muted">
								<?php echo ($header->ACCOUNT_NAME == NULL)? '-':$header->ACCOUNT_NAME; ?>
							</p>
							<strong>Fleet Customer</strong>
							<p class="text-muted">
								<?php echo ($header->FLEET_NAME == NULL)? '-':$header->FLEET_NAME; ?>
							</p>
						</div>
						<div class="col-sm-3">
							<strong>Invoice Number</strong>
							<p class="text-muted">
								<?php echo ($header->TRX_NUMBER == NULL)? '-':$header->TRX_NUMBER; ?>
							</p>
							<strong>Invoice Date</strong>
							<p class="text-muted">
								<?php echo ($header->TRX_DATE == NULL)? '-':date('m/d/Y', strtotime($header->TRX_DATE)); ?>
							</p>
							<strong>WB Number</strong>
							<p class="text-muted">
								<?php echo ($header->WB_NUMBER == NULL)? '-':$header->WB_NUMBER; ?>
							</p>
							<strong>Pullout Date</strong>
							<p class="text-muted">
								<?php echo ($header->PULLOUT_DATE == NULL)? '-':date('m/d/Y', strtotime($header->PULLOUT_DATE)); ?>
							</p>
							<strong>Payment Status</strong>
							<p class="text-muted">
								<?php echo $header->STATUS; ?>
							</p>
							<strong>Payment Date</strong>
							<p class="text-muted">
								<?php echo ($header->PAID_DATE == NULL)? '-' : $header->PAID_DATE; ?>
							</p>
							<strong>Current Status</strong>
							<p class="text-muted">
								<?php echo $header->CURRENT_STATUS; ?>
							</p>
							<strong>Next Step</strong>
							<p class="text-muted">
								<?php echo $header->NEXT_STEP; ?>
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
