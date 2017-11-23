<?php 
$this->load->helper('format_helper');
?>
<div id="modalContent" class="well" style="padding: 20px;">
	<div class="box box-danger">
		<div class="box-header with-border">
			<h3 class="box-title show" style="font-size: 15px;"><strong>Vehicle Details</strong></h3>
		</div>
		<div class="box-body">
			<div class="col-sm-12">
				<div class="col-sm-4">
					<strong>CS Number</strong>
					<p class="text-muted">
						<?php echo $header->CS_NUMBER; ?>
					</p>
					<strong>Chassis Number</strong>
					<p class="text-muted">
						<?php echo $header->CHASSIS_NUMBER; ?>
					</p>
					<strong>Engine</strong>
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
				 <div class="col-sm-4">
					
					<strong>Body Color</strong>
					<p class="text-muted">
						<?php echo ($header->BODY_COLOR == NULL)? '-':$header->BODY_COLOR; ?>
					</p>
					
					<strong>Aircon</strong>
					<p class="text-muted">
						<?php echo ($header->AIRCON == NULL OR $header->AIRCON == ' ')? '-':$header->AIRCON; ?>
					</p>
					<strong>Stereo</strong>
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
					<strong>MR Date</strong>
					<p class="text-muted">
						<?php echo ($header->MR_DATE == NULL)? '-':date('m/d/Y', strtotime(str_replace($header->MR_DATE))); ?>
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
				<div class="col-sm-4">
					<strong>Order Number</strong>
					<p class="text-muted">
						<?php echo ($header->ORDER_NUMBER == NULL)? '-':$header->ORDER_NUMBER; ?>
					</p>
					<strong>Allocation Date</strong>
					<p class="text-muted">
						<?php echo ($header->TAGGED_DATE == NULL)? '-':date('m/d/Y', strtotime($header->TAGGED_DATE)); ?>
					</p>
					<strong>Invoice Number</strong>
					<p class="text-muted">
						<?php echo ($header->TRX_NUMBER == NULL)? '-':$header->TRX_NUMBER; ?>
					</p>
					<strong>Invoice Date</strong>
					<p class="text-muted">
						<?php echo ($header->TRX_DATE == NULL)? '-':date('m/d/Y', strtotime($header->TRX_DATE)); ?>
					</p>
					<strong>Pullout Date</strong>
					<p class="text-muted">
						<?php echo ($header->PULLOUT_DATE == NULL)? '-':date('m/d/Y', strtotime($header->PULLOUT_DATE)); ?>
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
					<strong>WB Number</strong>
					<p class="text-muted">
						<?php echo ($header->WB_NUMBER == NULL)? '-':$header->WB_NUMBER; ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
