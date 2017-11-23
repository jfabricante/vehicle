<?php 
$this->load->helper('format_helper');
?>
<div id="modalContent" class="well" style="padding: 20px;">
	<div class="box box-danger">
		<div class="box-header with-border">
			<h3 class="box-title show" style="font-size: 15px;"><strong>Unit Details</strong></h3>
		</div>
		<div class="box-body">
			<div class="col-sm-12">
				<div class="col-sm-4">
					<strong>CS Number</strong>
					<p class="text-muted">
						<?php echo $row->CS_NUMBER; ?>
					</p>
					<strong>Item ID</strong>
					<p class="text-muted">
						<?php echo $row->INVENTORY_ITEM_ID; ?>
					</p>
					<strong>Item Model</strong>
					<p class="text-muted">
						<?php echo $row->ITEM_MODEL; ?>
					</p>
					<strong>Chassis Number</strong>
					<p class="text-muted">
						<?php echo $row->CHASSIS_NO; ?>
					</p>
					<strong>Lot Number</strong>
					<p class="text-muted">
						<?php echo $row->LOT_NUMBER; ?>
					</p>
				 </div>
				 <div class="col-sm-4">
					<strong>Engine Model</strong>
					<p class="text-muted">
						<?php echo $row->ENGINE_MODEL; ?>
					</p>
					<strong>Engine Number</strong>
					<p class="text-muted">
						<?php echo $row->ENGINE_NO; ?>
					</p>
					<strong>Aircon Model</strong>
					<p class="text-muted">
						<?php echo $row->AC_BRAND; ?>
					</p>
					<strong>Aircon Number</strong>
					<p class="text-muted">
						<?php echo $row->AC_NO; ?>
					</p>
					<strong>Stereo Model</strong>
					<p class="text-muted">
						<?php echo $row->STEREO_BRAND; ?>
					</p>
					<strong>Stereo Number</strong>
					<p class="text-muted">
						<?php echo $row->STEREO_NO; ?>
					</p>
				</div>
				<div class="col-sm-4">
					<strong>Buyoff Date</strong>
					<p class="text-muted">
						<?php echo date1($row->BUYOFF_DATE); ?>
					</p>
					<strong>FM Date</strong>
					<p class="text-muted">
						<?php echo date1($row->FM_DATE); ?>
					</p>
					<strong>Inventory Organization</strong>
					<p class="text-muted">
						<?php echo $row->ORGANIZATION_CODE; ?>
					</p>
					<strong>Sub-Inventory Organization</strong>
					<p class="text-muted">
						<?php echo $row->SUBINVENTORY_CODE; ?>
					</p>
					<strong>Body Number</strong>
					<p class="text-muted">
						<?php echo $row->BODY_NUMBER; ?>
					</p>
					<strong>Key Number</strong>
					<p class="text-muted">
						<?php echo $row->KEY_NUMBER; ?>
					</p>
				 </div>	
			</div>
		</div>
	</div>
</div>
