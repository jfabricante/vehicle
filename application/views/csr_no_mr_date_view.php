<?php 
$this->load->helper('format_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="row">
					<div class="col-sm-5" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" class="form-horizontal" method="POST" accept-charset="utf-8">
							<input type="hidden" name="date_from" value="<?php echo ($date_from == '')? date('Y-m-01'):date('Y-m-d', strtotime($date_from)); ?>"/>
							<input type="hidden" name="date_to" value="<?php echo ($date_to == '')? date('Y-m-01'):date('Y-m-d', strtotime($date_to)); ?>"/>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="unput1">Filter Date</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" name="date_filtered" value="<?php echo ($date_from == '')? date('m/01/Y'):date('m/d/Y', strtotime($date_from)); ?> - <?php echo ($date_to == '')? date('m/d/Y'):date('m/d/Y', strtotime($date_to)); ?>" />
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="mr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>CS Number</th>
								<th>Lot Number</th>
								<th>Sales Model</th>
								<th>Color</th>
								<th>Chassis No.</th>
								<th>Engine No.</th>
								<th>Buyoff Date</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($items as $item): ?>
							<tr>
								<td><?php echo $item->CS_NO; ?></td>
								<td><?php echo $item->LOT_NUMBER; ?></td>
								<td><?php echo $item->SALES_MODEL; ?></td>
								<td><?php echo $item->COLOR; ?></td>
								<td><?php echo $item->CHASSIS_NO; ?></td>
								<td><?php echo $item->ENGINE_NO; ?></td>
								<td><?php echo $item->BUYOFF_DATE; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/daterangepicker/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.js');?>"></script>
<script>
	$(document).ready(function() {

		$('#mr-list').DataTable({});
		
		/*$('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});*/
		
		$('input[name="date_filtered"]').daterangepicker();

		$('input[name="date_filtered"]').on('apply.daterangepicker', function(ev, picker) {
			$('input[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
			$('input[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
			form_filters.submit();
		});
	});
</script>
