<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('inventory_model');
		session_check();
	}
	
	public function onhand(){
		$data['content'] = 'onhand_view';
		$data['title'] = 'On-hand Availability';
		$data['result'] = $this->inventory_model->get_on_hand_availability();
		$this->load->view('include/template',$data);
	}
	
	public function onhand_details(){
		$model = $this->inventory_model->get_model_details($this->uri->segment(3));
		$data['title'] = $model->PROD_MODEL . '<small>' . $model->SALES_MODEL.'</small>';
		$data['content'] = 'onhand_details_view';
		
		$data['result'] = $this->inventory_model->get_on_hand_availability_per_model($this->uri->segment(3));
		$data['result2'] = $this->inventory_model->get_reserved_wout_tagged($this->uri->segment(3));
		$this->load->view('include/template',$data);
	}
}
