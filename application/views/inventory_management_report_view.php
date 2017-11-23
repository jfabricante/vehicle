<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-danger">
			<div id="overlay" class="overlay hide">
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
				<div class="box-header with-border">
					<h3 class="box-title">Search</h3>
				</div>
				<form target="_blank" id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8" action="pdf_inventory_management">
				    <div class="box-body">
						<div class="form-group">
							<label class="col-sm-3 control-label">As of</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="txt_as_of" name="txt_as_of"/>
							</div>
							<input type="hidden" id="txt_as_of_date" name="txt_as_of_date"/>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Output type</label>
							<div class="col-sm-8">
								<select id="sel_output_type" class="form-control">
									<option value="pdf">PDF</option>
									<option value="excel">Excel</option>
								</select>
							</div>
							
						</div>
					
						<div class="form-group">
							<label class="col-sm-3 control-label">&nbsp;</label>
							<div class="col-sm-8">
								<button type="submit" id="btn_generate" class="btn btn-sm btn-flat btn-danger">Generate</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/daterangepicker/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.js');?>"></script>
<script>
	$(document).ready(function() {
		$('#txt_as_of').daterangepicker({
			singleDatePicker: true,
			showDropdowns: true
		});

		$("#btn_generate").click(function(){
			//var as_of = $("#txt_as_of").val();
			var as_of = moment(new Date($("#txt_as_of").val())).format("DD-MMM-YYYY");
			$("#txt_as_of_date").val(as_of);
			$("#form_filters").submit();
		});

		$("#sel_output_type").change(function(){
			if($(this).val() == "pdf"){
				$("#form_filters").attr('action','pdf_inventory_management');
			}
			else if($(this).val() == "excel"){
				$("#form_filters").attr('action','excel_inventory_management');
			}
			else {
				$("#form_filters").attr('action','pdf_inventory_management');
			}
		});
	});
</script>

