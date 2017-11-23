<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Pullout Summary</h3>
				</div>
				<div class="row">
					<div class="col-sm-12" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8">
							<input type="hidden" name="from_date" value="<?php echo ($from_date == '')? date('Y-m-01'):date('Y-m-d', strtotime($from_date)); ?>"/>
							<input type="hidden" name="to_date" value="<?php echo ($to_date == '')? date('Y-m-01'):date('Y-m-d', strtotime($to_date)); ?>"/>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="unput1">Date Created</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" name="date_created" value="<?php echo ($from_date == '')? date('m/01/Y'):date('m/d/Y', strtotime($from_date)); ?> - <?php echo ($to_date == '')? date('m/d/Y'):date('m/d/Y', strtotime($to_date)); ?>" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="box-footer">
					<div class="text-right">
						<a href="<?php echo base_url(); ?>pullout/report/<?php echo $from_date; ?>/<?php echo $to_date; ?>" target="_blank"  class="btn btn-danger" >Generate PDF</a>
						<a href="<?php echo base_url(); ?>pullout/excel/<?php echo $from_date; ?>/<?php echo $to_date; ?>" target="_blank"  class="btn btn-danger" >Generate Excel</a>
					<div class="col-sm-8">
				</div>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/daterangepicker/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.js');?>"></script>
<script>
	$(document).ready(function() {
		
		$('input[name="date_created"]').daterangepicker();
		$('input[name="date_created"]').on('apply.daterangepicker', function(ev, picker) {
			$('input[name="from_date"]').val(picker.startDate.format('YYYY-MM-DD'));
			$('input[name="to_date"]').val(picker.endDate.format('YYYY-MM-DD'));
			form_filters.submit();
		});
	});
</script>
