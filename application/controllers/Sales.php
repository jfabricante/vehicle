<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('date');

		$this->load->library('user_agent');

		$this->load->model('sales_model');
		session_check();
	}

	public function model_list()
	{
		$data = array(
				'title'   => 'Vehicle Sales Model',
				'content' => 'sales_model_list_view',
				'items'   => $this->_merge_sales_model()
		);
			
		$this->load->view('include/template', $data);
	}

	public function form()
	{
		$item_id = $this->uri->segment(3);

		$entity = '';

		if ($this->sales_model->exist_patch($item_id))
		{
			$entity = $this->sales_model->read_patch($item_id);
		}
		else
		{
			$entity = $this->sales_model->read_model_item($item_id);
		}

		$data = array(
				'title'  => 'Vehicle Sales Model Form',
				'entity' => $entity
			);

		$this->load->view('sales_model_form_view', $data);
	}

	public function apply()
	{
		$datetime = date('Y-m-d H:i:s');
		$config   = $this->input->post();
		
		$config['datetime'] = $datetime;


		$this->sales_model->store_patch($config);

		$this->session->set_flashdata('message', '<div class="alert alert-success">Vehicle sales model has been updated!</div>');

		redirect($this->agent->referrer());
	}

	public function pricelist()
	{
		$data = array(
				'title'   => 'Vehicle Sales Price',
				'content' => 'sales_pricelist_list_view',
				'items'   => $this->_merge_sales_pricelist()
		);
			
		$this->load->view('include/template', $data);
	}

	public function pricelist_form()
	{
		$item_id = $this->uri->segment(3);

		$entity = '';

		if ($this->sales_model->exist_pricelist_patch($item_id))
		{
			$entity = $this->sales_model->read_pricelist_patch($item_id);
		}
		else
		{
			$entity = $this->sales_model->read_pricelist_item($item_id);
		}

		$data = array(
				'title'  => 'Vehicle Sales Price Form',
				'entity' => $entity
			);

		$this->load->view('sales_pricelist_form_view', $data);
	}

	public function apply_pricelist_patch()
	{
		$config = $this->input->post(); 
		$datetime = date('Y-m-d H:i:s');

		$config['datetime'] = $datetime;
		$config['EMP_NO'] = $this->session->userdata('employee_number');

		$this->sales_model->store_pricelist_patch($config);

		$this->session->set_flashdata('message', '<div class="alert alert-success">Vehicle sales price has been updated!</div>');

		redirect($this->agent->referrer());
	}

	protected function _merge_sales_model()
	{
		$sales_model = $this->sales_model->fetch_model_items('array');

		$sales_patch = $this->sales_model->fetch_model_patch('array');

		for ($i = 0; $i < count($sales_model); $i++)
		{
			for($j = 0; $j < count($sales_patch); $j++)
			{
				if ($sales_model[$i]['item_id'] == $sales_patch[$j]['item_id'])
				{
					$sales_model[$i] = $sales_patch[$j];
				}
			}
		}

		return $sales_model;
	}

	protected function _merge_sales_pricelist()
	{
		$pricelist = $this->sales_model->fetch_price_items('array');

		$pricelist_patch = $this->sales_model->fetch_pricelist_patch('array');

		for ($i = 0; $i < count($pricelist); $i++)
		{
			for($j = 0; $j < count($pricelist_patch); $j++)
			{
				if ($pricelist[$i]['ITEM_ID'] == $pricelist_patch[$j]['ITEM_ID'])
				{
					$pricelist[$i] = $pricelist_patch[$j];
				}
			}
		}

		return $pricelist;
	}

	public function test()
	{
		$pricelist = $this->sales_model->fetch_price_items('array');
		$pricelist_patch = $this->sales_model->fetch_pricelist_patch('array');

		echo '<pre>';
		var_dump($pricelist_patch);
		echo '</pre>';
	}
}
	