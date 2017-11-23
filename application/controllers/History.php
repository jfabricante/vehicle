<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('history_model');
		session_check();
	}
	
	public function ajax_get_vehicle_details(){
		
		if(!empty($this->input->post('cs_number'))){
			
			$q = STRTOUPPER($this->input->post('cs_number'));
			
			$result = $this->history_model->search_tagged($q);

			if($result != FALSE){
				
				$data['header'] = $result[0];
				echo $this->load->view('ajax/vehicle_details',$data,true);
			}
			else{
				$result = $this->history_model->search_serial_numbers($q);
				
				if($result != FALSE){
					$data['header'] = $result[0];
					echo $this->load->view('ajax/vehicle_details',$data,true);
				}
				else{
					
					$result = $this->history_model->search_ifs_vehicle_info($q);
				
					if($result != FALSE){
						$data['header'] = $result[0];
						echo $this->load->view('ajax/vehicle_details',$data,true);
					}
				}
			}
		}
		
		
		
		//~ $data['header'] = $this->history_model->search_result($this->input->post('cs_number'));
		//~ echo $this->load->view('ajax/vehicle_details',$data,true);
		
		//~ $rows = $this->history_model->search_result($this->input->post('cs_number'));
		//~ $data['header'] = $rows[0];
		//~ echo $this->load->view('ajax/vehicle_details',$data,true);
		
	}
	
	public function search(){
		
		if(!empty($this->input->get('q'))){
			
			$q = STRTOUPPER($this->input->get('q'));
			
			$result = $this->history_model->search_tagged($q);
			
			if($result != FALSE){
				$data['header'] = $result[0];
				$data['search_key'] = $q;
				$data['content'] = 'search_view';
				$data['title'] = 'Search Results . . .';
				$this->load->view('include/template',$data);
			}
			else{
				$result = $this->history_model->search_serial_numbers($q);
				
				if($result != FALSE){
					$data['header'] = $result[0];
					$data['search_key'] = $q;
					$data['content'] = 'search_view';
					$data['title'] = 'Search Results . . .';
					$this->load->view('include/template',$data);
				}
				else{
					
					$result = $this->history_model->search_ifs_vehicle_info($q);
				
					if($result != FALSE){
						$data['header'] = $result[0];
						$data['search_key'] = $q;
						$data['content'] = 'search_view2';
						$data['title'] = 'Search Results . . .';
						$this->load->view('include/template',$data);
					}
					else{
						$data['content'] = 'search_view3';
						$data['title'] = 'Search Results . . .';
						$this->load->view('include/template',$data);
					}
				}
			}
		}
		else{
			$data['content'] = 'search_view3';
			$data['title'] = 'Search Results . . .';
			$this->load->view('include/template',$data);
		}
	}
	
	public function generator(){
		
		ini_set('max_execution_time', 36000);
		ini_set('memory_limit', '4000M');
		
		//~ get update start 
		$update_start = date('Y-m-d H:i:s');
		
		//~ get last update
		$row = $this->history_model->get_last_update();
		$last_update = $row[0]->last_update;
		
		//~ update last update
		$this->history_model->update_last_update($update_start);
		
		//~ get all updated lines
		$results = $this->history_model->get_ifs_headers($last_update);
		
		$cnt = 0;
		
		foreach($results as $row){
			
			$cnt++;
			
			//~ echo '<pre>';
			//~ print_r($row);
			//~ echo '</pre>';
			
			$buyoff_date = ($row->BUYOFF_DATE != NULL)? date('Y-m-d', strtotime($row->BUYOFF_DATE)):NULL;
			$fm_date = ($row->FM_DATE != NULL)? date('Y-m-d', strtotime($row->FM_DATE)):NULL;
			$tagged_date = ($row->TAGGED_DATE != NULL)? date('Y-m-d', strtotime($row->TAGGED_DATE)):NULL;
			$payment_date = ($row->PAYMENT_DATE != NULL)? date('Y-m-d', strtotime($row->PAYMENT_DATE)):NULL;
			$pullout_date = ($row->PULLOUT_DATE != NULL)? date('Y-m-d', strtotime($row->PULLOUT_DATE)):NULL;
			$invoice_date = ($row->INVOICE_DATE != NULL)? date('Y-m-d', strtotime($row->INVOICE_DATE)):NULL;
			$csr_date = ($row->CSR_DATE != NULL)? date('Y-m-d', strtotime($row->CSR_DATE)):NULL;
			
			
			$params = array(
						$row->PROD_ORDER_NO,	
						$row->SERIAL_NO,
						$row->CS_NO,
						$row->PROD_MODEL,
						$row->SALES_MODEL,
						$buyoff_date,
						$fm_date,
						$row->BODY_COLOR,
						$row->VIN,
						$row->BODY_NO,
						$row->ENGINE_TYPE,
						$row->ENGINE_NO,
						$row->KEY_NO,
						$row->WB_NO,
						$row->AIRCON_BRAND,
						$row->AIRCON_NO,
						$row->STEREO_NO,
						$row->STEREO_BRAND,
						$row->SALES_ORDER,
						$tagged_date,
						$payment_date,
						$pullout_date,
						$row->INVOICE_NO,
						$invoice_date,
						$row->CSR_NO,
						$row->CSR_OR_NO,
						$row->LOT_NO,
						$csr_date,
						$row->NET_AMOUNT,
						$row->VAT_AMOUNT,
						$row->CUSTOMER_ID,
						$row->CUSTOMER_ID_2,
						$row->CUSTOMER_NAME,
						$row->CUSTOMER_NAME_2,
						$row->LAST_UPDATE);
			
			if($this->history_model->check_cs_no($row->CS_NO) > 0){
				array_push($params,$row->CS_NO);
				$this->history_model->update_ifs_headers($params);
			}
			else{
				$this->history_model->insert_ifs_headers($params);
			}
			
			//~ echo '<pre>';
			//~ print_r($params);
			//~ echo '</pre>';
			
			
		}
		
		//~ echo $cnt;
	}

	public function history_log()
	{
		$data['content'] = 'history_log';
		$data['title'] = 'History';
		$data['result'] = $result = $this->history_model->view_history_log();

		$this->load->view('include/template',$data);
	}
}
