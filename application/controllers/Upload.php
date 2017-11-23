<?php

class Upload extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}
	
	public function do_upload(){
		
		$this->load->library('Excel');
		$this->load->library('IOFactory');
		
		$storagename = time();
		move_uploaded_file($_FILES['excel_file']['tmp_name'],  '../../upload' . $storagename . '.xlsx');
	}
}

