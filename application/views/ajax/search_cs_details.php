<?php 
$this->load->helper('date_helper');
?>
<p class="lead"><small>Search Results <i class="fa fa-search"></i></small> </p>
<div class="col-md-10">
	<table class="table table-condensed">
		<thead>
			<th class="text-center">CS Number</th>
			<th class="text-center">Model</th>
			<th class="text-center">Lot</th>
			<th class="text-center">Engine No</th>
			<th class="text-center">Chassis No</th>
			<th class="text-center">Buyoff Date</th>
			<th class="text-center">MR Date</th>
		</thead>
		<tbody>
			<?php 
			$disabled = 0;
			$cnt = 0;
			foreach($result as $row){
				$mr_date = ($row->MR_DATE == NULL)? '' : date('m/d/Y', strtotime($row->MR_DATE));;
				$buyoff_date =  ($row->BUYOFF_DATE == NULL)? '' : date('m/d/Y', strtotime($row->BUYOFF_DATE))
			?>
			<tr>
				<td class="text-center"><?php echo $row->CS_NO; ?></td>
				<td class="text-center"><?php echo $row->SALES_MODEL; ?></td>
				<td class="text-center"><?php echo $row->LOT_NUMBER; ?></td>
				<td class="text-center"><?php echo $row->ENGINE_NO; ?></td>
				<td class="text-center"><?php echo $row->CHASSIS_NO; ?></td>
				<td class="text-center"><?php echo $buyoff_date; ?></td>
				<td class="text-center"><?php echo $mr_date; ?></td>
			</tr>
			<?php
				$cnt++; 
				if($row->MR_DATE != NULL AND $row->MR_DATE != '0000-00-00 00:00:00' AND $row->MR_DATE != ''){
					$disabled = 1;
				}
			}
			?>
		</tbody>
	</table>
</div>
<div class="row">
	<div class="col-lg-6">
		<form method="post" class="form-horizontal">
			<input type="hidden" value="<?php echo $cs_no; ?>" name="cs_no" />
			<fieldset <?php echo ($disabled == 1 OR $cnt == 0)? 'disabled':''; ?>>
				<p class="lead"><small>Update MR Date</small> </p>
				<div class="form-group">
					<label class="col-lg-3 control-label">MR Date</label>
					<div class="col-lg-4">
						<input autofocus="autofocus" type="text" class="datemask form-control input-sm" name="mr_date" value=""/>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-6 col-lg-offset-3">
						<button type="button" class="btn btn-danger btn-flat btn-sm" name="update">Submit</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script>
	$(document).ready(function() {
		
		$('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		$('button[name=update]').click(function(){
			
			var cs_no = $('input[name=cs_no]').val();
			var mr_date = $('input[name=mr_date]').val();
			
			if(mr_date){
				
				$.ajax({
					type:'POST',
					data:{
						cs_no : cs_no,
						mr_date : mr_date
					},
					url: '<?php echo base_url();?>mr/ajax_update_mr_date',
					success:function(data){
						$('#result').html(data);
						//~ alert(data);
					}
				});
			}
			else{
				alert('Invalid MR date!');
			}
		})
	});
</script>
