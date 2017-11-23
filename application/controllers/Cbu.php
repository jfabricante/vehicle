<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cbu extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('cbu_model');
		session_check();
	}
	
	public function pullout_entry(){
		//~ echo date('d-M-Y');
		$data['content'] = 'cbu_pullout_entry_view';
		$data['title'] = 'NYK CBU Units';
		$this->load->view('include/template',$data);
	}
	
	public function pulledout(){
		//~ echo date('d-M-Y');
		
		//~ $date_from = date('d-M-y', strtotime('7/01/2017'));
		//~ $date_to = date('d-M-y', strtotime('7/19/2017'));
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');

		$data['result'] = $this->cbu_model->get_cbu_pulledout( $data['from_date'] ,$data['to_date'] );
		$data['content'] = 'cbu_pulledout_view';
		$data['title'] = 'NYK CBU Units';
		$this->load->view('include/template',$data);
	}
	
	public function unpulledout(){
		//~ echo date('d-M-Y');
		$data['result'] = $this->cbu_model->get_cbu_unpulledout();
		$data['content'] = 'cbu_unpulledout_view';
		$data['title'] = 'NYK CBU Units';
		$this->load->view('include/template',$data);
	}
	
	public function ajax_search_cbu_cs_number(){
		
		$cs_nos = explode(',', $this->input->post('cs_nos'));
		$cs_nos = '\''.implode('\',\'', str_replace(' ', '', $cs_nos)).'\'';
		
		$cs_nos = STRTOUPPER($cs_nos);
		
		$data['result'] = $this->cbu_model->get_search_cbu_cs_nos($cs_nos);
		$data['cs_nos'] = $cs_nos;
		echo $this->load->view('ajax/searched_cs_nos',$data,true);
		
		//~ $this->dr_model->get_search_dr_number($drs);

	}
	
}
