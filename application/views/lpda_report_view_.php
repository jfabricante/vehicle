<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-8">
			<div class="box box-danger">
			<div id="overlay" class="overlay hide">
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
				<div class="box-header with-border">
					<h3 class="box-title">Report</h3>
				</div>
				<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="<?php echo base_url('delivery_advisory/delivery_advisory'); ?>" target="_blank">
				    <div class="box-body">
					    <div class="row">

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Report Type: </label>
									<div class="col-sm-3">
										<select class="form-control selectpicker" id = "report" name="report" data-live-search="true" required>
							                  <option value="99988744455212" <?php echo ($report == 1)? 'selected':'';?>>Nothing Selected</option>
							                  <?php
							                  foreach($report_type as $key){
							                    ?>
							                    <option 
							                    <?php echo ($report == $key['id'])? 'selected':'';?>
							                      data-subtext="- <?php echo $key['type'];?>" 
							                      data-content="<span class='label label-success'><?php echo $key['type'];?></span>" 
							                      value="<?php echo $key['id']; ?>" >
							                    </option>
							                    <?php 
							                  }
							                  ?>
							              </select>
									</div>
								</div>
					    	</div>

					    	 <div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">LPDA: </label>
									<div class="col-sm-8" id="po_list">
							          	<select class="form-control selectpicker" id = "lpda_no" name="lpda_no" data-live-search="true" required disabled>        
							            </select>  
									</div>
								</div>
					    	</div>
									<label class="col-sm-2 control-label hidden" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Forecast: </label>
									<div class="col-sm-3">
										<select class="form-control selectpicker hidden" id = "month" name="month" data-live-search="true"  readonly>
							                  <option value="99988744451" <?php echo ($month == 1)? 'selected':'';?>>Nothing Selected</option>
							                  <?php
							                  foreach($all_month as $key){
							                    ?>
							                    <option 
							                    <?php echo ($month == $key['id'])? 'selected':'';?>
							                      data-subtext="- <?php echo $key['month'];?>" 
							                      data-content="<span class='label label-success'><?php echo $key['month'];?></span>" 
							                      value="<?php echo $key['id']; ?>" >
							                    </option>
							                    <?php 
							                  }
							                  ?>
							              </select>
									</div>

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Remarks: </label>
									<div class="col-sm-6">
										<textarea class="form-control" id="remarks" name="remarks" disabled required></textarea>
									</div>
								</div>
					    	</div>

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Coordinator: </label>
									<div class="col-sm-6">
										<input type="text" id="coordinator" name="coordinator" class="form-control" readonly required>
									</div>
								</div>
					    	</div>

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Supervisor: </label>
									<div class="col-sm-6">
										<input type="text" id="supervisor" name="supervisor" class="form-control" readonly required>
									</div>
								</div>
					    	</div>

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">Start Date: </label>
									<div class="col-sm-3">
										<input type="text" id="start_date" name ="start_date" class="form-control" disabled required>
									</div>
								</div>
					    	</div>

					    	<div class="col-md-12">
					    		<div class="form-group">
									<label class="col-sm-2 control-label" style="margin-top: 5px; font-size: 15px; font-weight: normal !important;" for="unput1">With Forecast</label>
									<div class="col-sm-3">
										<select class="form-control selectpicker" id = "forecast_flag" name="forecast_flag" data-live-search="true" required>
							                    <option 
							                      data-subtext="Yes" 
							                      data-content="<span class='label label-success'><?php echo 'Yes';?></span>" 
							                      value="yes" >
							                    </option>
							                    <option 
							                      data-subtext="No" 
							                      data-content="<span class='label label-success'><?php echo 'No';?></span>" 
							                      value="no" >
							                    </option>
							              </select>
									</div>
								</div>
					    	</div>


					    </div>
				    </div>
					<div class="box-footer text-left">
						<input type="submit" id="submit" class="btn btn-danger" style="margin-left: 15px;" value="Generate Report" disabled>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script>
	$(document).ready(function() {

		$('.selectpicker').selectpicker();

		$("#start_date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});

		$('select[name=report]').change(function(){
			
			$('#remarks').val('');
			$('#coordinator').val('');
			$('#start_date').val('');
			var report = $(this).val();
			$('#overlay').removeClass();
      		$('#overlay').addClass('overlay');
      		if(report == '99988744455212')
      		{
      			$('#overlay').removeClass();

	            $('#overlay').addClass('overlay hide');
      			$("#lpda_no").attr("disabled", true);
      			$("#submit").attr("disabled", true);
				$("#remarks").attr("readonly", true);
				$("#remarks").attr("disabled", false);
				$("#start_date").attr("disabled", false);
				$("#start_date").attr("readonly", true);
				$("#coordinator").attr("readonly", true);
      		}
      		else
      		{
      			
      			$.ajax({
					type: 'POST',
					dataType: "text",
					data: {
						report : report
					},
					url: '<?php echo base_url();?>delivery_advisory/ajax_get_po_details',
					success: function(data){
						
						$('#overlay').removeClass();
	                    $('#overlay').addClass('overlay hide');
						$('#po_list').empty();

						$('#po_list').html(data);
						$('#lpda_no').selectpicker('refresh');

						$('select[name=lpda_no]').change(function(){

							var lpda_no = $(this).val();
							if(lpda_no == 1)
							{
								$("#remarks").attr("disabled", true);
								$("#start_date").attr("disabled", true);
								$("#submit").attr("disabled", true);
								$("#coordinator").attr("disabled", true);
								$("#supervisor").attr("disabled", true);
								$('#remarks').val('');
								$('#coordinator').val('');
								$('#supervisor').val('');
								$('#start_date').val('');
								$("#coordinator").attr("readonly", true);
								$("#supervisor").attr("readonly", true);
							}
							else
							{
								$('#overlay').removeClass();
				        		$('#overlay').addClass('overlay');

								$.ajax({
									type: 'POST',
									dataType: "json",
									data: {
										lpda_no : lpda_no
									},
									url: '<?php echo base_url();?>delivery_advisory/ajax_get_lpda_details',
									success: function(data){
										$('#overlay').removeClass();
					                    $('#overlay').addClass('overlay hide');
										$('#coordinator').val(data.BUYER_NAME);
										$('#supervisor').val('');
										$('#remarks').val(data.ERR_MSGS);

										$('#month').val(data.MONTH);
										$('.selectpicker').selectpicker('refresh')
										$("#month").attr("readonly", true);
										$("#remarks").attr("disabled", false);
										$("#coordinator").attr("disabled", false);
										$("#coordinator").attr("readonly", false);

										$("#supervisor").attr("disabled", false);
										$("#supervisor").attr("readonly", false);

										$("#start_date").attr("disabled", false);
										$("#submit").attr("disabled", false);

									}
								});
							}
							
						});
					}
				});


      		}
			
			
		});
	});
</script>

