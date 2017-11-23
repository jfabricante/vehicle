<?php

class Csr_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->mysqli = $this->load->database('mysqli', true);
		$this->oracle = $this->load->database('oracle', true);
		$this->ifs = $this->load->database('ifs', true);
	}

	public function new_csr($params){
			
		$sql = "INSERT INTO IPC.IPC_VEHICLE_CSR (
					cs_number,
					csr_number,
					csr_or_number,
					csr_date,
					transaction_id,
					created_by_id,
					created_date)
				VALUES(?,?,?,?,?,?,sysdate)";
				
		return $this->oracle->query($sql,$params); 

	}
	
	public function update_serial_number($params){
		
		$sql = "UPDATE mtl_serial_numbers SET
				attribute1 = ?,
				attribute12 = ?,
				attribute14 = ?
				WHERE 1 = 1 
				AND serial_number = ?";
		$this->oracle->query($sql, $params);
	}
	
	public function update_vehicle_master($params){
		
		$sql = "UPDATE vehicle_master_tab SET
					csr_number = ?,
					csrdate = ?,
					or_no = ?,
					tran_id = ?
				   WHERE cs_no = ?";
		$this->ifs->query($sql, $params);
	}
	
	public function get_csr_all($from_date = NULL, $to_date = NULL){
		
		$from_date = ($from_date == NULL)? date('01-M-y'):date('d-M-y', strtotime($from_date));
		$to_date = ($to_date == NULL)? date('d-M-y'):date('d-M-y', strtotime($to_date));
		$params = array($from_date, $to_date);
		
		//~ echo $from_date .' - '. $to_date;
		
		$sql = "SELECT 
					cs_number,
					csr_number,
					csr_or_number,
					csr_date,
					transaction_id,
					created_date
				FROM IPC.IPC_VEHICLE_CSR
				WHERE to_date(to_char(created_date, 'DD-MON-YY')) BETWEEN ? AND ?";
				
		$data = $this->oracle->query($sql, $params);
		return $data->result();
	} 
	
	public function check_cs_if_exist($cs_number){
		
		$sql = "SELECT
					attribute1 csr_number, 
					attribute12 csr_or_number,
					attribute14 csr_date
				FROM mtl_serial_numbers
				WHERE 1 = 1
					AND serial_number = ?";
		
		$rows = $this->oracle->query($sql,$cs_number);
		if($rows->num_rows() > 0){
			return true;
		}
		else {
			return false;
		}
	}

	public function check_csr_no_if_exist($csr_number){
		
		$sql = "SELECT
					attribute1 csr_number, 
					attribute12 csr_or_number,
					attribute14 csr_date
				FROM mtl_serial_numbers
				WHERE 1 = 1 
					AND attribute1 = ? ";
		
		$rows = $this->oracle->query($sql,$csr_number);
		if($rows->num_rows() > 0){
			return false;
		}
		else {
			return true;
		}
	}
}
