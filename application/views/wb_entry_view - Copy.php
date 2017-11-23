<section class="content">
	<div class="row">
		<div class="col-md-8">
			<div class="box box-danger">
				<div id="overlay" class="overlay hide">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
				<div class="box-header with-border">
					<h3 class="box-title">Search Transaction Numbers</h3>
				</div>
				<form class="form-horizontal">
				    <div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">From</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="from_trx_number" >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">To</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="to_trx_number" >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">&nbsp;</label>
							<div class="col-sm-10">
								<button type="button" class="btn btn-sm btn-flat btn-danger" name="btn-1">Search</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div id="search_div" class="col-md-4">
			
		</div>
	</div>
</section>
<script>
	$(document).ready(function(){
		
		$('button[name=btn-1]').click(function(){
			
			var from_trx_number = $('input[name=from_trx_number]').val();
			var to_trx_number   = $('input[name=to_trx_number]').val();
			
			$.ajax({
				type:'POST',
				data:{
					from_trx_number : from_trx_number,
					to_trx_number : to_trx_number
				},
				url: '<?php echo base_url();?>wb/ajax_search_trx_numbers',
				success:function(data){
					$('#search_div').html(data);
					//~ alert(data);
				}
			});
		});
	});
</script>

