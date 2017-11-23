<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Csr extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('csr_model');
		$this->load->library('form_validation');
		session_check();
	}
	
	public function new_(){
		//~ echo date('d-M-Y');
		$data['content'] = 'csr_entry_view';
		$data['title'] = 'Certificate of Stock Report';
		$this->load->view('include/template',$data);
	}
	
	public function list_(){
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
		$data['content'] = 'csr_list_view';
		$data['title'] = 'Certificate of Stock Report';
		$data['result'] = $this->csr_model->get_csr_all($data['from_date'], $data['to_date']);
		$this->load->view('include/template',$data);
	}
	
	public function new_csr(){

		$this->load->helper('format_helper');
		$data       = $this->input->post();
		$data       = (object)$data;
		$ctr        = 0;
		$array_data = array();
		$arr_ctr    = 0;

		$cs_number_array = array();
		$csr_number_array = array();

		foreach($data->data as $row)
		{
			if($row['name'] == 'cs_no')
			{
				$array_data[$arr_ctr]['cs_number'] = $row['value'];
				array_push($cs_number_array, $row['value']);
			}
			if($row['name'] == 'csr_number')
			{
				$array_data[$arr_ctr]['csr_number'] = $row['value'];
				array_push($csr_number_array, $row['value']);
			}
			if($row['name'] == 'csr_or_number')
			{
				$array_data[$arr_ctr]['csr_or_number'] = $row['value'];
			}
			if($row['name'] == 'csr_date')
			{
				$array_data[$arr_ctr]['csr_date'] = $row['value'];
			}
			if($row['name'] == 'transaction_id')
			{
				$array_data[$arr_ctr]['transaction_id'] = $row['value'];
			}

			$ctr++;
			if($ctr == 5)
			{
				$arr_ctr++;
				$ctr = 0;
			}
		}

		$postArr = array_filter(array_map('array_filter',$array_data));
		// $cs_number_unique = $this->unique_multidim_array($postArr,'cs_number'); 
		// $csr_number_unique = $this->unique_multidim_array($postArr,'csr_number'); 

		$arr_cs = array_filter($cs_number_array);
		$arr_unique_cs = array_unique($arr_cs);
		$arr_duplicates_cs = array_diff_assoc($arr_cs, $arr_unique_cs);

		$arr_csr = array_filter($csr_number_array);
		$arr_unique_csr = array_unique($arr_csr);
		$arr_duplicates_csr = array_diff_assoc($arr_csr, $arr_unique_csr);

		$keys_cs = array_keys($arr_duplicates_cs);
		$keys_csr = array_keys($arr_duplicates_csr);
		// echo "<pre>";
		// print_r(array_filter($postArr));
		// echo "</pre>";
		// exit();
		if(!empty($arr_duplicates_cs))
		{
			echo '<strong>Duplicate CS Number</strong> - ' . STRTOUPPER($arr_duplicates_cs[$keys_cs[0]]);
		}
		else if(!empty($arr_duplicates_csr))
		{
			echo '<strong>Duplicate CSR Number</strong> - ' . STRTOUPPER($arr_duplicates_csr[$keys_csr[0]]);
		}
		else
		{

			foreach($postArr as $line){
				$row = (object)$line;

				// if($row->cs_number != NULL){
					

					if(!empty($row->cs_number))
					{
						if(!empty($row->csr_number))
						{
							if(!$this->csr_model->check_cs_if_exist(STRTOUPPER($row->cs_number)))
							{
								echo 'Invalid CS Number';
								break;
							}
							else if(!$this->csr_model->check_csr_no_if_exist(STRTOUPPER($row->csr_number)))
							{
								echo 'Invalid CSR Number';
								break;
							}
							else if(empty($row->csr_or_number))
							{
								echo 'Empty CSR OR Number';
								break;
							}
							else if(empty($row->csr_date))
							{
								echo 'Empty CSR Date';
								break;
							}
							else if(empty($row->transaction_id))
							{
								echo 'Empty Transaction ID';
								break;
							}
							else
							{
								$data = array(
									STRTOUPPER($row->cs_number),
									STRTOUPPER($row->csr_number),
									STRTOUPPER($row->csr_or_number),
									oracle_date($row->csr_date),
									STRTOUPPER($row->transaction_id),
									$this->session->userdata('user_id')
									);
								$data1 = array(
											STRTOUPPER($row->csr_number),
											oracle_date($row->csr_date),
											STRTOUPPER($row->csr_or_number),
											STRTOUPPER($row->transaction_id),
											STRTOUPPER($row->cs_number)
										);
								$data2 = array(
											STRTOUPPER($row->csr_number),
											STRTOUPPER($row->csr_or_number),
											oracle_dff_date($row->csr_date),
											STRTOUPPER($row->cs_number)
										);

								//~ if($this->csr_model->new_csr($data)){
									//~ //$this->csr_model->update_vehicle_master($data1);
									//~ $this->csr_model->update_serial_number($data2);
								//~ }
								$this->csr_model->new_csr($data);
								$this->csr_model->update_serial_number($data2);
							}			
						}
						else
						{
							echo 'Empty CSR Number';
							break;
						}
					}
					else 
					{
						echo 'Empty CS Number';
						break;
					}
							
				// }
				// else{
				// 	 echo 'Empty CS Number'
				// 	 break;
				// }
			}
			echo '';
		}
	}
	
	public function check_cs(){
		
		$cs_number = $this->input->post('cs_number');
		echo $this->csr_model->check_cs_if_exist(STRTOUPPER($cs_number));
	}

	public function check_csr_no(){
		
		$csr_number = $this->input->post('csr_number');
		echo $this->csr_model->check_csr_no_if_exist(($csr_number));
	}

	// public function unique_multidim_array($array, $key) {
	//     $temp_array = array();
	//     $i = 0;
	//     $key_array = array();
	   
	//     foreach($array as $val) {
	//         if (!in_array($val[$key], $key_array)) {
	//             $key_array[$i] = $val[$key];
	//             $temp_array[$i] = $val;
	//         }
	//         $i++;
	//     }
	//     return $temp_array;
	// } 
}
