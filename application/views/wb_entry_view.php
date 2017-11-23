<?php 
$this->load->helper('format_helper');

// echo $customer_id;
?>
<section class="content">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-danger">
				<div class="box-body">
					<div class="col-sm-12">
						<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8">
							<div class="form-group" style="margin-bottom: 0;">
								<div class="col-sm-12">
									<select class="form-control select2" name="customer_id" onchange="this.form.submit()" data-live-search="true">
										<option value="1">Select Customer</option>
										<?php
										foreach($customers as $customer){
										?>
										<option 
											<?php echo ($customer_id == $customer->CUSTOMER_ID)? 'selected':'';?>
											value="<?php echo $customer->CUSTOMER_ID; ?>" name="customer_id">
											<?php echo $customer->CUSTOMER_NAME; ?>
										</option>
										<?php 
										}
										?>
									</select>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php 
			if(isset($invoices)){
			?>
			<div class="box box-danger">
				<div class="box-header with-border">
					&nbsp;
					<div class="box-tools pull-right">
						<button name="btn-submit" class="btn btn-danger btn-sm">Submit Selected</button>
					</div>
				</div>
				<div class="box-body">
					<div class="col-sm-12">
						<form id="myForm" class="form-horizontal" action="prepare_selected" method="post">
							<table id="myTable" class="table table-hover">
								<thead>
									<tr>
										<th></th>
										<th>Account</th>
										<th>Transaction Number</th>
										<th>CS Number</th>
										<th>WB Number</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$cnt = 1;
									foreach($invoices as $row){
										
									?>
									<tr>
										<td><input type="checkbox" name="invoice_id" value="<?php echo $row->TRX_ID; ?>"/></td>
										<td><?php echo $row->ACCOUNT_NAME; ?></td>
										<td><?php echo $row->TRX_NUMBER; ?></td>
										<td><?php echo $row->CS_NUMBER	; ?></td>
										<td><?php echo $row->WB_NUMBER; ?></td>
									</tr>
									<?php 
									$cnt++;
									}
									?>
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>
			<?php 
			}
			?>
		</div>
		<div id="search_div" class="col-md-6">
			
		</div>
	</div>
</section>
<script>
	$(document).ready(function() {
		
		$('.select2').select2();
		
		 $('table').DataTable( {
		
		});
		
		$('button[name=btn-submit]').click(function(){
			
			$('#search_div').html('');
			var data = $("#myForm").serializeArray();
			
			$.ajax({
				type:'POST',
				data:{
					data : data
				},
				url: '<?php echo base_url();?>wb/ajax_search_trx_numbers',
				success:function(data){
					$('#search_div').html(data);
					//~ alert(data);
				}
			});
		});
		
		$('table#myTable tr').click(function(event) {
			if (event.target.type !== 'checkbox') {
			  $(':checkbox', this).trigger('click');
			}
		});
		
		$("input[type='checkbox']").change(function (e) {
			if ($(this).is(":checked")) { //If the checkbox is checked
				$(this).closest('tr').addClass("highlight_row"); 
				//Add class on checkbox checked
			} else {
				$(this).closest('tr').removeClass("highlight_row");
				//Remove class on checkbox uncheck
			}
		});
	});
</script>
