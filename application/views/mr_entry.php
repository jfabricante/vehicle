<link href="<?php echo base_url('resources/plugins/tokenfield/css/bootstrap-tokenfield.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Details</h3>
				</div>
				<div class="box-body">
					<form class="form-horizontal">
						<div class="col-lg-8">
							<div class="form-group">
								<label class="col-lg-3 control-label">Search CS No : </label>
								<div class="col-lg-9">
									<input type="text" id="tokenfield" class="form-control input-sm" name="cs_no" value="" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label"></label>
								<div class="col-lg-9">
									<button id="cs_submit" type="button" class="btn btn-flat btn-danger btn-sm">Search</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div id="result" class="box-body">
					
				</div>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/tokenfield/bootstrap-tokenfield.min.js');?>"></script>
<script>
	$(document).ready(function() {
		$('#tokenfield').tokenfield();
		
		$('button#cs_submit').click(function(){
			
			var cs_no = $('input[name=cs_no]').val();
			
			$.ajax({
				type:'POST',
				data:{
					cs_no : cs_no
				},
				url: '<?php echo base_url();?>mr/ajax_search_cs_no',
				success:function(data){
					$('#result').html(data);
					//~ alert(data);
				}
			});
		});
	});
</script>
