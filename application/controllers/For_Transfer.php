<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class For_Transfer extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('for_transfer_model');
		session_check();
	}
	
	public function nyk(){
		
		$lot_number = ($this->input->post('lot_number') == NULL)? '0':$this->input->post('lot_number');
		$data['lot_number'] = $lot_number;
		$data['content'] = 'for_transfer_view';
		$data['title'] = 'For Inter-Organization Transfer (NYK to IVS)';
		$data['lots'] = $this->for_transfer_model->get_for_transfer_lot('NYK');
		$data['result'] = $this->for_transfer_model->get_for_transfer($lot_number, 'NYK');
		$this->load->view('include/template',$data);
	}
	
	public function ivp(){
		
		$lot_number = ($this->input->post('lot_number') == NULL)? '0':$this->input->post('lot_number');
		$data['lot_number'] = $lot_number;
		$data['content'] = 'for_transfer_view';
		$data['title'] = 'For Inter-Organization Transfer (IVP to IVS)';
		$data['lots'] = $this->for_transfer_model->get_for_transfer_lot('IVP');
		$data['result'] = $this->for_transfer_model->get_for_transfer($lot_number, 'IVP');
		$this->load->view('include/template',$data);
	}
	
	public function transfer_nyk(){
		
		$cs_numbers = "'0'";
		//~ $ctr = 1;
		foreach($this->input->post('transfer') as $cs_number){
			$cs_numbers .= ",'".$cs_number."'";
			//~ echo $ctr . ' ' . $cs_number . '<br />';
			//~ $ctr++;
		}
		//~ echo $cs_numbers;
		$this->for_transfer_model->update_for_transfer_nyk($cs_numbers);
		redirect('For_Transfer/ivp');
	}
}
