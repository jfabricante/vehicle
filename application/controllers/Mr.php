<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mr extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('date');

		$this->load->model('mr_model');
		session_check();
	}
	
	public function entry(){
		$data['content'] = 'mr_entry';
		$data['title'] = 'MR Entry';
		//$data['result'] = $this->buyoff_model->get_buyoff_headers();
		$this->load->view('include/template',$data);
	}

	public function ajax_search_cs_no()
	{
		$cs_no = explode(',', $this->input->post('cs_no'));
		$cs_no = '\''.implode('\',\'', str_replace(' ', '', $cs_no)).'\'';

		$data['result'] = $this->mr_model->get_search_cs_no($cs_no);
		$data['cs_no'] = $cs_no;

		// echo "<pre>";
		// print_r($data['result']);
		// echo "</pre>";
		// exit();
		echo $this->load->view('ajax/search_cs_details',$data,true);
	}

	public function ajax_update_mr_date()
	{
		$post = $this->input->post();
		
		$cs_no = explode(',', $this->input->post('cs_no'));
		$cs_no = '\''.implode('\',\'', str_replace(' ', '', $cs_no)).'\'';
		$mr_date = date('Y/m/d 00:00:00', strtotime($this->input->post('mr_date')));
		
		// echo "<pre>";
		// print_r($cs_no);
		// echo "</pre>";
		// exit();

		$this->mr_model->update_mr_date($cs_no, $mr_date);
		$data['result'] = $this->mr_model->get_search_cs_no($cs_no);
		$data['cs_no'] = $cs_no;
		echo $this->load->view('ajax/search_cs_details',$data,true);
	}

	public function display_csr_without_mr_date()
	{
		$date_from = $this->input->post('date_from') ? date('d-M-y', strtotime($this->input->post('date_from'))) : '';
		$date_to   = $this->input->post('date_to') ? date('d-M-y', strtotime($this->input->post('date_to'))) : '';

		$config = array(
				'date_from' => $date_from,
				'date_to'   => $date_to
			);

		//var_dump($config); die;
		$data = array(
				'content'   => 'csr_no_mr_date_view',
				'title'     => 'List of CSR without MR Date',
				'items'     => $this->mr_model->get_csr_without_mr_date($config),
				'date_from' => $date_from,
				'date_to'   => $date_to
			);

		//var_dump($data['items']); die;
		$this->load->view('include/template', $data);
	}
	
}
