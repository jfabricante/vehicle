<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Vehicle | Login</title>
		
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		
		<!-- Bootstrap 3.3.6 -->
		<link rel="stylesheet" href="<?php echo base_url('resources/templates/bootstrap-3.3.7/css/bootstrap.min.css');?>">
		
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo base_url('resources/fonts/font-awesome-4.6.3/css/font-awesome.min.css');?>">
		
		<!-- Theme style -->
		<link rel="stylesheet" href="<?php echo base_url('resources/templates/adminlte-2.3.5/dist/css/AdminLTE.min.css');?>">
		<link rel="stylesheet" href="<?php echo base_url('resources/templates/adminlte-2.3.5/dist/css/skins/skin-red.min.css');?>">
		
		<!-- Custom Style for CMS -->
		<link rel="stylesheet" href="<?php echo base_url('resources/css/custom.css');?>">
		<link rel="shortcut icon" href="<?php echo base_url('resources/images/favicon.ico');?>" type="image/x-icon"/ >
		
		<!-- iCheck -->
		<!--   <link rel="stylesheet" href="../../plugins/iCheck/square/blue.css"> -->

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<b>IPC</b>Portal
			</div>
			<div class="login-box-body">
				<?php 
					if($this->session->flashdata('login_error') != NULL){
					?>
					<div class="col-sm-12 alert alert-warning alert-dismissible">
						Error : Login Required!
					</div>
					<?php 
					}
				?>
				<?php echo $message;?>
				<h4 class="text-center">Vehicle and Fleet System</h4>
				<p class="login-box-msg">Sign in to start your session</p>
				<?php echo form_open('login/check_login');?>
					
					<div class="form-group has-feedback">
						<input type="text" class="form-control" placeholder="Username" name="username" id="username" value="<?php echo set_value('username');?>"/>
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
						<span class="help-block login-error-msg text-red"><?php echo form_error('username');?></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" class="form-control" placeholder="Password" name="password" id="password" value="<?php echo set_value('password');?>"/>
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
						<span class="help-block login-error-msg text-red"><?php echo form_error('password');?></span>
					</div>
					<div class="row">
						<div class="col-xs-8"></div>
						<div class="col-xs-4">
							<button type="submit" class="btn btn-danger btn-block btn-flat">Sign In</button>
						</div>
					</div>
				<?php echo form_close();?>
			</div>
		</div>

		<!-- jQuery 2.2.3 -->
		<script src="<?php echo base_url('resources/js/jquery-3.0.0/jquery.min.js');?>"></script>

		<!-- Bootstrap 3.3.6 -->
		<script src="<?php echo base_url('resources/bootstrap-3.3.7/js/bootstrap.min.js');?>"></script>
	</body>
</html>
