<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">CSR Entry</h3>
					<div class="box-tools pull-right">
						<button name="btn-save" class="btn btn-danger btn-sm">Save</button>
					</div>
			        <div id = "msg" class="alert alert-danger hidden" style="margin-top: 10px;">
			            <a href="#" class="close" data-dismiss="alert">&times;</a>
			            <p id="display_error"></p>
			        </div>
				</div>
				<div class="box-body">
					<table class="table" id="table">
						<thead style="background-color: #ffffff";>
							<tr>
								<th style="width:10px;">#</th>
								<th style="width:150px;">CS Number</th>
								<th style="width:250px;">CSR Number</th>
								<th style="width:250px;">CSR OR Number</th>
								<th style="width:150px;">CSR Date</th>
								<th style="width:200px;">Transaction ID</th>
							</tr>
						</thead>
						<form id="myForm" role="form" class="form-horizontal" method="POST" action="new_csr" enctype="multipart/form-data" >
							<tbody>
								<tr>
									<td></td>
									<td class="cs_number_primary"><input placeholder="CS Number" type="text" class="form-control input-sm"></td>
									<td class="csr_number_primary"><input placeholder="CSR Number"  type="text" class="form-control input-sm"></td>
									<td class="csr_or_number_primary"><input placeholder="CSR OR Number"  type="text" class="form-control input-sm"></td>
									<td class="csr_date_primary"><input placeholder="CSR Date"  type="text" class="form-control input-sm"></td>
									<td class="transaction_id_primary"><input placeholder="Transaction ID"  type="text" class="form-control input-sm"></td>
								</tr>
								<?php
								$ctr = 0; 
								$ctr1 = 1; 
								while($ctr1 <= 30){
								?>
								<tr>
									<td><?php echo $ctr1; ?></td>
									<td class="cs_number" >
										<div class="has-feedback">
											<input placeholder="CS Number" data-inputmask="'mask': 'a**999', 'placeholder':'######'" type="text" id="cs_no" name="cs_no" class="form-control input-sm text-uppercase" value="">
											<span class="hidden text-green fa fa-check form-control-feedback" aria-hidden="true"></span>
											<span class="hidden text-red fa fa-remove form-control-feedback" aria-hidden="true"></span>
										</div>
									</td>

									<td class="csr_number">
										<div class="has-feedback">
											<input placeholder="CSR Number" data-inputmask="'mask': '**************', 'placeholder':'#############'" type="text" id="csr_number" name="csr_number" class="form-control input-sm" value="">
											<span class="hidden text-green fa fa-check form-control-feedback" aria-hidden="true"></span>
											<span class="hidden text-red fa fa-remove form-control-feedback" aria-hidden="true"></span>
										</div>
									</td>

									<td class="csr_or_number <?php echo $ctr == 0 ? 'csr_or_number_primary':'';?>"><input placeholder="CSR OR Number" data-inputmask="'mask': '9999999999', 'placeholder':'##########'"  type="text" name="csr_or_number" class="form-control input-sm" value=""></td>
									<td class="csr_date <?php echo $ctr == 0 ? 'csr_date_primary':'';?>"><input placeholder="CSR Date" type="text" name="csr_date" class="form-control input-sm" value=""></td>
									<td class="transaction_id <?php echo $ctr == 0 ? 'transaction_id_primary':'';?>"><input placeholder="Transaction ID" data-inputmask="'mask': '******************', 'placeholder':'##################'" type="text" name="transaction_id" class="form-control input-sm" value=""></td>

									
								</tr>
								<?php 
								$ctr1++;
								$ctr++;
								}
								?>
							</tbody>
						</form>

					</table>
				</div>	
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/floatThead/floatThead.min.js'); ?>"></script>
<script>
	
	$(document).ready(function() {

		$(".msg").addClass("hidden");

		$('td.cs_number input').focus(function(){
			$(this).val($('td.cs_number_primary input').val());
		});
		
		$('td.csr_number input').focus(function(){
			$(this).val($('td.csr_number_primary input').val());
		});
		
		$('td.csr_or_number input').focus(function(){
			$(this).val($('td.csr_or_number_primary input').val());
		});
		
		$('td.csr_date input').focus(function(){
			$(this).val($('td.csr_date_primary input').val());
		});
		
		$('td.transaction_id input').focus(function(){
			$(this).val($('td.transaction_id_primary input').val());
		});
		
		$('td.cs_number input').blur(function(){
			
			var arr = [];
			$(this).each(function(){
				var value = $(this).val();
				if (arr.indexOf(value) == -1)
					arr.push(value);
				else
					$(this).addClass("duplicate");
			});
		
			//alert(arr);
			var cs_number = $(this).val();
			if (cs_number.replace(/^\s+|\s+$/g, "").length > 0){
				var cs_number_elem = $(this);
				cs_number_elem.next().addClass('hidden');
				cs_number_elem.next().next().addClass('hidden');
				$.ajax({
					type:'POST',
					data:{
						cs_number : cs_number
					},
					url: '<?php echo base_url();?>csr/check_cs',
					success:function(data){
						if(data){
							//~ alert('true');
							cs_number_elem.next().removeClass('hidden');
						}
						else{
							cs_number_elem.next().next().removeClass('hidden');
						}
					}
				});
			}
		});

		$('td.csr_number input').blur(function(){
			
			var arr = [];
			$(this).each(function(){
				var value = $(this).val();
				if (arr.indexOf(value) == -1)
					arr.push(value);
				else
					$(this).addClass("duplicate");
			});
		

			var csr_number = $(this).val();
			if (csr_number.replace(/^\s+|\s+$/g, "").length > 0){
				var csr_number_elem = $(this);
				csr_number_elem.next().addClass('hidden');
				csr_number_elem.next().next().addClass('hidden');
				$.ajax({
					type:'POST',
					data:{
						csr_number : csr_number
					},
					url: '<?php echo base_url();?>csr/check_csr_no',
					success:function(data){
						if(data){
							//~ alert('true');
							csr_number_elem.next().removeClass('hidden');
						}
						else{
							csr_number_elem.next().next().removeClass('hidden');
						}
					}
				});
			}
		});
		
		var $table = $('table.table');
		//$table.floatThead();
		
		$('td.cs_number input, td.csr_number input, td.csr_or_number input, td.transaction_id input').inputmask();
		$('td.csr_date input, td.csr_date_primary input').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		$('button[name="btn-save"]').click(function(){
			//$('#myForm').submit();
			var data = $("#myForm").serializeArray();
			var valid_dupe = $('#valid_dupe').val();
			$.ajax({
				type:'POST',
				data:{
					data : data,
					valid_dupe : valid_dupe
				},
				url: '<?php echo base_url();?>csr/new_csr',
				success:function(data){
					if(data != ''){
						// alert(data);
						$('#display_error').empty();
						$('#display_error').append('<span>'+data+'</span>');
						//$('#display_error').val(data);
						$("#msg").removeClass("hidden");
					}
					else{
						$("#msg").addClass("hidden");
						window.location.href = "<?php echo base_url(); ?>csr/list_";
					}
				}
			});
		});

		
	});
</script>
