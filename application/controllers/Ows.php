<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Ows extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('ows_model');
	}

	public function new_units()
	{
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		$data['content'] = 'ows_view';
		$data['title'] = 'OWS';
		$data['type'] = 'new_units';
		$data['title_view'] = 'New Units';
		$data['result'] = $this->ows_model->get_new_units();
	}

	public function csr()
	{
		$data['content'] = 'ows_view';
		$data['title'] = 'OWS';
		$data['type'] = 'new_csr';
		$data['title_view'] = 'New CSR';
		$data['result'] = $this->ows_model->get_new_csr();
		$this->load->view('include/template',$data);
	}

	public function so()
	{
		$data['content'] = 'ows_view';
		$data['title'] = 'OWS';
		$data['type'] = 'new_so';
		$data['title_view'] = 'New Order';
		$data['result'] = $this->ows_model->get_new_so();
		$this->load->view('include/template',$data);
	}

	public function invoice()
	{
		$data['content'] = 'ows_view';
		$data['title'] = 'OWS';
		$data['type'] = 'new_invoice';
		$data['title_view'] = 'New Invoice';
		$data['result'] = $this->ows_model->get_new_invoice();
		$this->load->view('include/template',$data);
	}

	public function pullout()
	{
		$data['content'] = 'ows_view';
		$data['title'] = 'OWS';
		$data['type'] = 'new_pullout';
		$data['title_view'] = 'New Pullout Date';
		$data['result'] = $this->ows_model->get_new_pullout();
		$this->load->view('include/template',$data);
	}

	public function fetch_vehicle_data()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		$data = $this->ows_model->getVehicleDetails();
		$this->ows_model->truncate_t_crm_vehicle_oracle();

		foreach ($data as $key) {
			$this->ows_model->insert_oracle_data_to_mysql_crms($key);
        }
        redirect('ows/stop');
        echo "Done";
        exit();
        break;
        die();
	}

	public function insert_new_vehicle()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 7200);
		$data = $this->ows_model->get_new_units();

		foreach ($data as $key) {
            $this->ows_model->insert_new_vehicle($key);
        }

        //redirect('crms/new_units');
	}
    public function stop()
    {
        echo "Done";
        exit();
        break;
        die();
    }

	public function updateInvoice()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->ows_model->updateInvoice();
        redirect('ows/stop');
	}

	public function updateSO()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->ows_model->updateSO();
        redirect('ows/so');
	}

	public function updateCSR()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->ows_model->updateCSR();
        redirect('ows/csr');
	}

	public function updatePullout()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->ows_model->updatePullout();
        redirect('ows/pullout');
	}

	public function updateWB()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

        $result = $this->ows_model->updateWB();
        redirect('ows/csr');
	}
	
}
