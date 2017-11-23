<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Crms extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('crms_model');
	}

	public function new_units()
	{
		$data['content'] = 'crms_view';
		$data['title'] = 'CRMS';
		$data['type'] = 'new_units';
		$data['title_view'] = 'New Units';
		$data['result'] = $this->crms_model->get_new_units();
		$this->load->view('include/template',$data);
	}

	public function csr()
	{
		$data['content'] = 'crms_view';
		$data['title'] = 'CRMS';
		$data['type'] = 'new_csr';
		$data['title_view'] = 'New CSR';
		$data['result'] = $this->crms_model->get_new_csr();
		$this->load->view('include/template',$data);
	}

	public function so()
	{
		$data['content'] = 'crms_view';
		$data['title'] = 'CRMS';
		$data['type'] = 'new_so';
		$data['title_view'] = 'New Order';
		$data['result'] = $this->crms_model->get_new_so();
		$this->load->view('include/template',$data);
	}

	public function invoice()
	{
		$data['content'] = 'crms_view';
		$data['title'] = 'CRMS';
		$data['type'] = 'new_invoice';
		$data['title_view'] = 'New Invoice';
		$data['result'] = $this->crms_model->get_new_invoice();
		$this->load->view('include/template',$data);
	}

	public function pullout()
	{
		$data['content'] = 'crms_view';
		$data['title'] = 'CRMS';
		$data['type'] = 'new_pullout';
		$data['title_view'] = 'New Pullout Date';
		$data['result'] = $this->crms_model->get_new_pullout();
		$this->load->view('include/template',$data);
	}

	public function fetch_vehicle_data()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		$data = $this->crms_model->getVehicleDetails();
		$this->crms_model->truncate_t_crm_vehicle_oracle();

		foreach ($data as $key) {
			$this->crms_model->insert_oracle_data_to_mysql_crms($key);
        }
        redirect('crms/stop');
        echo "Done";
        exit();
        break;
        die();
	}

    public function stop()
    {
        echo "Done";
        exit();
        break;
        die();
    }

	public function insert_new_vehicle()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 7200);
		$data = $this->crms_model->get_new_units();

		foreach ($data as $key) {
            $this->crms_model->insert_new_vehicle($key);
        }

        redirect('crms/new_units');
	}

	public function updateInvoice()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->crms_model->updateInvoice();
        redirect('crms/invoice');
	}

	public function updateSO()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->crms_model->updateSO();
        redirect('crms/so');
	}

	public function updateCSR()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->crms_model->updateCSR();
        redirect('crms/csr');
	}

	public function updatePullout()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->crms_model->updatePullout();
        redirect('crms/pullout');
	}

	public function updateWB()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->crms_model->updateWB();
        redirect('crms/csr');
	}
	
}
