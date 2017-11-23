<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">CBU Pulledout Units</h3>
				</div>
				<div class="row">
					<div class="col-sm-5" style="padding: 10px;margin-left: 20px;">

						<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8">
							<input type="hidden" name="from_date" value="<?php echo ($from_date == '')? date('Y-m-01'):date('Y-m-d', strtotime($from_date)); ?>"/>
							<input type="hidden" name="to_date" value="<?php echo ($to_date == '')? date('Y-m-01'):date('Y-m-d', strtotime($to_date)); ?>"/>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="unput1">Date Created</label>
								<div class="col-sm-9">
									<input class="form-control text-center" type="text" name="date_created" value="<?php echo ($from_date == '')? date('m/01/Y'):date('m/d/Y', strtotime($from_date)); ?> - <?php echo ($to_date == '')? date('m/d/Y'):date('m/d/Y', strtotime($to_date)); ?>" />
								</div>
							</div>
						</form>

					</div>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th>CS Number</th>
								<th>Invoice Number</th>
								<th>Invoice Date</th>
								<th>Customer Name</th>
								<th>Account Name</th>
								<th>Sales Model</th>
								<th>Body Color</th>
								<th>Pullout Date</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo $count; ?></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->TRX_NUMBER; ?></td>
								<td><?php echo date('m/d/Y', strtotime($row->TRX_DATE)); ?></td>
								<td><?php echo $row->PARTY_NAME; ?></td>
								<td><?php echo $row->ACCOUNT_NAME; ?></td>
								<td><?php echo $row->SALES_MODEL; ?></td>
								<td><?php echo $row->BODY_COLOR; ?></td>
								<td><?php echo date('m/d/Y', strtotime($row->PULLOUT_DATE)); ?></td>
							</tr>
							</tr>
						<?php 
						$count++;
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/daterangepicker/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.js');?>"></script>
<script>
	$(document).ready(function() {

		$('#dr-list').DataTable({
			
		});
		
		$('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		$('input[name="date_created"]').daterangepicker();
		$('input[name="date_created"]').on('apply.daterangepicker', function(ev, picker) {
			$('input[name="from_date"]').val(picker.startDate.format('YYYY-MM-DD'));
			$('input[name="to_date"]').val(picker.endDate.format('YYYY-MM-DD'));
			form_filters.submit();
		});
	});
</script>
