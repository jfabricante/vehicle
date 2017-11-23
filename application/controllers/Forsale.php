<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forsale extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('forsale_model');
		session_check();
	}
	
	public function per_model(){
		$data['content'] = 'forsale_list_view';
		$data['title'] = 'Available Units To Reserve';
		$data['result'] = $this->forsale_model->get_forsale_headers($this->uri->segment(3));
		$this->load->view('include/template',$data);
	}
	
	public function summary(){
		$data['content'] = 'forsale_summary_view';
		$data['title'] = 'Available Units To Reserve';
		$data['result'] = $this->forsale_model->get_forsale_per_model();
		$this->load->view('include/template',$data);
	}
	
	public function ajax_get_forsale_line(){
		
		$data['row'] = $this->forsale_model->get_forsale_line($this->input->post('cs_number'));
		echo $this->load->view('ajax/forsale_line_view',$data,true);

		//~ $row = $this->forsale_model->get_forsale_line($this->input->post('cs_number'));
		//~ echo '<pre>';		
		//~ print_r($row);
		//~ echo '</pre>';
		
	}
}
