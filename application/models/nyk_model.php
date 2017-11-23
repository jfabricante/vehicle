<?php

class Nyk_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function update_cbu_mis_attributes($params){
		
		$sql = "UPDATE XXXIPC_MIS SET 
					CS_NO = ?,
					VIN_NO = ?,
					ENGINE_NO = ?,
					BODY_NO = ?,
					FM_OFF_DATE = ?,
					KEY_NO = ?,
					AC_NO = ?,
					STEREO_NO = ?,
					LAST_UPDATED_BY = ?
				WHERE shop_order = ?
				AND REPLACE(serial_no, ' ', '') = ?
				AND model_code = ?
				AND active = 1";
		$update = $this->oracle->query($sql, $params);
		
		if($update){
			return $this->oracle->affected_rows();
		}
		else{
			return 0;
		}
	}
	
	public function insert_cbu_details($params){
		
		$sql = "INSERT INTO IPC.IPC_NYK_TRANSMITTAL(
						CS_NUMBER,
						CHASSIS_NUMBER,
						ENGINE_NUMBER,
						BODY_NUMBER,
						KEY_NUMBER,
						AIRCON_NUMBER,
						STEREO_NUMBER,
						FM_DATE,
						FG_JO,
						SERIAL_NUMBER,
						FG_MODEL_CODE )
					VALUES(?,?,?,?,?,?,?,?,?,?,?)";
		return $this->oracle->query($sql, $params);
	}
	
	public function insert_transmittal_url($params){
		
		$sql = "INSERT INTO IPC.IPC_NYK_TRANSMITTAL_URI(
						URL,
						IS_SUCCESS )
					VALUES(?,?)";
		return $this->oracle->query($sql, $params);
	}
	
	public function get_all_buyoff_today(){
		
		$date = date('d-M-y');
		
		$sql = "SELECT serial_number,
						fg_model_code,
						fg_jo,
						cs_number,
						chassis_number,
						engine_number,
						body_number,
						key_number,
						aircon_number,
						stereo_number,
						fm_date,
						TO_CHAR(date_received, 'MM/DD/YYYY HH24:MI:SS') date_received
				  FROM IPC.IPC_NYK_TRANSMITTAL
				 WHERE TO_DATE(TO_CHAR(date_received, 'DD-MON-YY')) BETWEEN ? AND ?
				 ORDER BY date_received DESC";
		$data = $this->oracle->query($sql, array($date,$date));
		return $data->result_array();
	}
	
}
