<?php 
$this->load->helper('format_helper');
?>
<p class="lead"><small>Search Results <i class="fa fa-search"></i></small> </p>
<div class="col-md-12">
	<table class="table table-condensed">
		<thead>
			
			<th class="text-center">#</th>
			<th class="text-center">Cs Number</th>
			<th class="text-left">Lot Number</th>
				<th class="text-left">Sales Model</th>
			<th class="text-left">Body Color</th>
			<th class="text-center">Invoice Number</th>
			<th class="text-left">Customer Name</th>
			<th class="text-left">Account Name</th>
		
			
			
			<th class="text-center">Pullout Date</th>
		</thead>
		<tbody>
			<?php 
			$disabled = 0;
			$cnt = 0;
			foreach($result as $row){
			?>
			<tr>
				<td class="text-center"><?php echo $cnt + 1; ?></td>
				<td class="text-center"><?php echo $row->CS_NO; ?></td>
				<td class="text-left"><?php echo $row->LOT_NUMBER; ?></td>
				<td class="text-left"><?php echo $row->SALES_MODEL; ?></td>
				<td class="text-left"><?php echo $row->BODY_COLOR; ?></td>
				<td class="text-center"><?php echo $row->TRX_NUMBER; ?></td>
				<td class="text-left"><?php echo $row->PARTY_NAME; ?></td>
				<td class="text-left"><?php echo $row->ACCOUNT_NAME; ?></td>
				<td class="text-center"><?php echo ($row->PULLOUT_DATE != NULL)? date1($row->PULLOUT_DATE):'-'; ?></td>
			</tr>
			<?php
				$cnt++; 
				if($row->PULLOUT_DATE != NULL AND $row->PULLOUT_DATE != '0000-00-00'){
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
			<input type="hidden" value="<?php echo $cs_nos; ?>" name="cs_nos2" />
			<fieldset <?php echo ($disabled == 1 OR $cnt == 0)? 'disabled':''; ?>>
				<p class="lead"><small>Update Pullout Date</small> </p>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-3 control-label">Pullout Date</label>
					<div class="col-lg-4">
						<input autofocus="autofocus" type="text" class="datemask form-control input-sm" name="pullout_date" value=""/>
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
			
			var cs_nos = $('input[name=cs_nos2]').val();
			var pullout_date = $('input[name=pullout_date]').val();
			
			//~ alert(cs_nos);
			
			if(pullout_date){
				
				$.ajax({
					type:'POST',
					data:{
						cs_nos : cs_nos,
						pullout_date : pullout_date
					},
					url: '<?php echo base_url();?>pullout/ajax_update_pullout_date',
					success:function(data){
						$('#result').html(data);
						//~ alert(data);
					}
				});
			}
			else{
				alert('Invalid dispatch date!');
			}
		})
	});
</script>
