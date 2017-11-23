<section class="content">

	<div class="row">

		<div class="col-md-12">
			<?php echo $this->session->flashdata('message');  ?>
			<div class="box box-danger">
				
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th></th>
								<th>Product Model</th>
								<th>Description</th>
								<th>Type</th>
								<th>Sales Model</th>
								<th>Status</th>
								<th>Pricelist</th>
								<th>Price</th>
								<th></th>
							</tr>
						</thead>

						<tbody>
							<?php $counter = 1; ?>
							<?php foreach($items as $item): ?>
								<tr>
									<td><?php echo $counter ?></td>
									<td><?php echo $item['PROD_MODEL'] ?></td>
									<td><?php echo $item['DESCRIPTION'] ?></td>
									<td><?php echo $item['TYPE'] ?></td>
									<td><?php echo $item['SALES_MODEL'] ?></td>
									<td><?php echo $item['STATUS'] ?></td>
									<td><?php echo $item['PRICELIST'] ?></td>
									<td><?php echo $item['PRICE'] ?></td>
									<td>
										<a href="<?php echo base_url('index.php/sales/pricelist_form/' . $item['ITEM_ID']); ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
											<i class="fa fa-edit" aria-hidden="true"></i>
										</a>
									</td>
								</tr>
								<?php $counter++ ?>
							<?php endforeach; ?>
						</tbody>
					</table>

				</div>

			</div>
			
		</div>
	</div>
</section>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      ...
    </div>
  </div>
</div>

<script>
	$(document).ready(function() {
		$('.table').DataTable({
			
		});

		// Detroy modal
		$('body').on('hidden.bs.modal', '.modal', function () {
			$(this).removeData('bs.modal');
		}); 
	});
</script>