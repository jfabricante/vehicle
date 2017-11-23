<div class="box box-danger">
	<div class="box-header with-border">
		<h3 class="box-title">&nbsp;</h3>
		<div class="box-tools pull-right">
			<button name="btn-save" class="btn btn-danger btn-sm">Print & Save</button>
		</div>
	</div>
	<div class="box-body">
		<form id="myForm2" class="form-horizontal" action="save_wb" method="post" target="_blank">
			<table class="table table-condensed" style="padding: 10px;">
				<thead>
					
					<th class="text-center">#</th>
					<th class="text-center">Transaction Number</th>
					<th class="text-center">CS Number</th>
					<th class="text-center">WB Number</th>
				</thead>
				<tbody>
					<input type="hidden" name="invoices" value="<?php echo $invoices; ?>" />
					<?php 
					$disabled = 0;
					$cnt = 0;
					$ctr = 0;
					foreach($result as $row){
					?>
					<tr>
						<td class="text-center valign-center"><?php echo $cnt + 1; ?></td>
						<td class="text-center"><input <?php echo $row->WB_NUMBER == NULL ? 'readonly':'disabled';?> type="text" class="text-center form-control inputs-sm" name="trx_number[]" value="<?php echo $row->TRX_NUMBER; ?>"></td>
						<td class="text-center"><input <?php echo $row->WB_NUMBER == NULL ? 'readonly':'disabled';?> type="text" class="text-center form-control inputs-sm" name="cs_number[]"  value="<?php echo $row->CS_NUMBER; ?>"></td>
						<td class="text-center"><input <?php echo $row->WB_NUMBER == NULL ? '':'disabled';?> type="text" class="text-center form-control inputs-sm" name="wb_number[]"  value="<?php echo $row->WB_NUMBER; ?>"></td>
					</tr>
					<?php
						$cnt++; 
						if($row->WB_NUMBER == NULL){
							$ctr++;
						}
					}
					?>
				</tbody>
			</table>
		</form>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('button[name="btn-save"]').click(function(){
			$('#myForm2').submit();
		});
	});
</script>
