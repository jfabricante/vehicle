<?php 
$this->load->helper('format_helper');
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">History Log</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="history_log_table" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th>CS NUMBER</th>
								<th>STATUS</th>
								<th>DESCRIPTION</th>
								<th>DATE LOG</th>
							</tr>
						</thead>
						<tbody>
							<?php $ctr=1;
							foreach($result as $row) {?>
							<tr>
								<td><?php echo $ctr; ?></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->STATUS; ?></td>
								<td><?php echo $row->DESCRIPTION; ?></td>
								<td><?php echo $row->DATE_LOG; ?></td>
							</tr>
							<?php $ctr++; } ?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	$(document).ready(function() {
		$('#history_log_table').DataTable();
	});
</script>

