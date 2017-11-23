<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="../../favicon.ico">
		<title>IPC Portal</title>
		<!-- Bootstrap core CSS -->
		<link href="<?php echo base_url('resources/templates/bootstrap-3.3.7/css/bootstrap.min.css');?>" rel="stylesheet" >
		<!-- Admin LTE core CSS -->
		<link href="<?php echo base_url('resources/templates/AdminLTE-2.3.5/dist/css/AdminLTE.min.css');?>" rel="stylesheet" >
		<link href="<?php echo base_url('resources/templates/AdminLTE-2.3.5/dist/css/skins/_all-skins.min.css');?>" rel="stylesheet" >
		<!-- Select2 -->
		<link href="<?php echo base_url('resources/plugins/select2/css/select2.css') ?>" rel="stylesheet" >
		<!-- Data Tables -->
		<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
		<!-- Font Awesome -->
		<link href="<?php echo base_url('resources/fonts/font-awesome-4.6.3/css/font-awesome.min.css');?>" rel="stylesheet" >
		<!-- Custom CSS -->
		<link href="<?php echo base_url('resources/css/custom.css');?>" rel="stylesheet" >
			<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<!-- jQuery 3.0.0 -->
		<script src="<?php echo base_url('resources/js/jquery-3.0.0/jquery.min.js');?>"></script>
		<!-- Bootstrap 3.3.6 -->
		<script src="<?php echo base_url('resources/templates/bootstrap-3.3.7/js/bootstrap.min.js');?>"></script>
		<!-- Data Tables -->
		<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
		<!-- Select2 -->
		<script src="<?php echo base_url('resources/plugins/select2/js/select2.full.min.js');?>"></script>
		<!-- Input Mask -->
		<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
		<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
		<!-- Admin LTE app js -->
		<script src="<?php echo base_url('resources/templates/AdminLTE-2.3.5/dist/js/app.min.js');?>"></script>
	</head>
	<body class="hold-transition skin-red sidebar-mini">
		<div class="wrapper">
			<?php $this->load->view('include/header.php'); ?>
			<?php $this->load->view('include/sidebar-menu.php'); ?>
			<div class="content-wrapper">
				<section class="content-header">
					<h1><?php echo $title; ?></h1>
				</section>
				<?php $this->load->view($content); ?>
			</div>
			<?php $this->load->view('include/footer.php'); ?>
			<?php $this->load->view('include/control-sidebar.php'); ?>
		</div>
	
	
	</body>
</html>
