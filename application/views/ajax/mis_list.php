<form id="my_form" class="form-horizontal" method="POST" action="save_mis">
	<input type="hidden" name="lot_number" value="<?php echo $lot_number; ?>">
	<input type="hidden" name="model_name" value="<?php echo $model_name; ?>">
	<table class="table table-condensed table-hover" id="mis-table" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
		<thead style="background-color: #ffffff;">
			<tr>
				
				<th>#</th>
				<th>Print</th>
				<th>Update</th>
				<th>Serial Number</th>
				<th>Model Name</th>
				<th>Model Code</th>
				<th>Lot Number</th>
				<th>CS Number</th>
				<th>Chassis Number</th>
				
			</tr>
		</thead>
		<tbody>
		<?php 
		$cnt = 0;
		$first_model_code = '';
		$same = 0;
		foreach($result as $row){
			if($first_model_code == ''){
				$first_model_code =  $row->MODEL_CODE;
			}
			if($first_model_code != $row->MODEL_CODE){
				$same++;;
			}
		?>
			<input type="hidden" name="mis[<?php echo $cnt; ?>][mis_id]" class="input-sm form-control" value="<?php echo $row->MIS_ID;?>"/>
			<tr id="serial-<?php echo str_replace(' ', '', $row->SERIAL_NO); ?>">
				<td><?php echo $cnt + 1; ?></td>
				
				<td><a class="text-primary" target="_blank" href="print_/<?php echo $row->MIS_ID; ?>"><i class="fa fa-print"></i></a></td>
				<td><a class="text-primary modal-trigger" href="#" data-toggle="modal" data-target="#myModal" data-mis_id="<?php echo $row->MIS_ID; ?>" data-serial_no="<?php echo $row->SERIAL_NO; ?>" data-lot_no="<?php echo $row->LOT_NUM; ?>" data-model_name="<?php echo $row->MODEL_NAME; ?>" ><i class="fa fa-edit"></i></a></td>
				<td class="check_trigger"><?php echo $row->SERIAL_NO; ?></td>
				<td class="check_trigger"><?php echo $row->MODEL_NAME; ?></td>
				<td class="check_trigger"><?php echo $row->MODEL_CODE; ?></td>
				<td class="check_trigger"><?php echo $row->LOT_NUM; ?></td>
				<td class="check_trigger"><?php echo $row->CS_NO; ?></td>
				<td class="check_trigger"><?php echo $row->VIN_NO; ?></td>
			</tr>
		<?php 
		$cnt++;
		$lot_number = $row->LOT_NUM;
		$model_code = $row->MODEL_CODE;
		}
		if($same > 0){
			$model_code = '';
		}
		?>
		</tbody>
	</table>
	<a target="_blank" href="print_/0/<?php echo $lot_number. '/' . $model_code; ?>" class="btn btn-flat btn-primary btn-sm"><i class="fa fa-print"></i> &nbsp;MIS</a>
	<a target="_blank" href="print_so_report/<?php echo $lot_number. '/' . $model_code; ?>" class="btn btn-flat btn-primary btn-sm"><i class="fa fa-print"></i> &nbsp;SO</a>
	<button type="submit" class="hidden btn btn-flat btn-danger btn-sm" name="save" >Save</button>
</form>
<script>
	$(document).ready(function() {
		$('body table#mis-table').floatThead();
		
		$('table#mis-table tr td.check_trigger').click(function(event) {
			
			if (event.target.type !== 'checkbox') {
			  $(':checkbox', $(this).parent()).trigger('click');
			}
		});
	});
</script>
