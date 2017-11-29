<?php

class Mis_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	 
	public function select_lot_number_dd(){
		
		$sql = "SELECT DISTINCT lot_num lot_number FROM XXXIPC_MIS WHERE active = 1 ORDER BY lot_num";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function select_model_names_dd($lot_number){
		
		$sql = "SELECT DISTINCT model_name FROM XXXIPC_MIS WHERE lot_num = ?";
		$data = $this->oracle->query($sql, $lot_number);
		return $data->result();
		
	}
	
	public function select_mis_units($lot_number, $model_name){
		
		if($model_name == 1 OR $model_name == NULL){
			$model_name = NULL;
		}
		
		$sql = "SELECT mis_id, serial_no, model_name, model_code, lot_num, cs_no, vin_no
				FROM XXXIPC_MIS WHERE lot_num = ? AND model_name = NVL(?, model_name) AND active = 1 ORDER BY serial_no";
		$data = $this->oracle->query($sql, array($lot_number, $model_name));
		return $data->result();
	}
	
	public function update_mis_unit($params){
		
		$sql = "UPDATE XXXIPC_MIS 
		        SET cs_no = ?, 
		            vin_no = ? 
		        WHERE mis_id = ?";
		$this->oracle->query($sql, $params);
		//return $this->oracle->_error_message();
		
	}
	
	public function select_mis_details($mis_id = NULL, $lot_number = NULL, $model_code = NULL){
		
		
		$sql = "SELECT mis_id, serial_no, model_name, model_code, lot_num, start_date, shop_order, plan_id, CS_NO,
					VIN_NO,
					ENGINE_NO,
					BODY_NO,
					FM_OFF_DATE,
					REMARKS,
					KEY_NO,
					AC_NO,
					STEREO_NO,
					BUY_OFF_DATE,
					LAST_UPDATED_BY
				FROM XXXIPC_MIS 
				WHERE lot_num = NVL(?,lot_num) 
				AND model_code = NVL(?,model_code) 
				AND mis_id = NVL(?,mis_id)
				AND active = 1";
		$data = $this->oracle->query($sql, array($lot_number, $model_code, $mis_id));
		return $data->result();
		
	}
	
	public function select_shop_order_report($lot_number = NULL, $model_code = NULL){
		
		
		$sql = "SELECT mis.mis_id, mis.serial_no, mis.model_name, mis.lot_num, mis.shop_order, wdj.scheduled_completion_date due_date
				FROM XXXIPC_MIS mis
				LEFT JOIN wip_discrete_jobs wdj
				ON 	mis.fg_wip_entity_id = wdj.wip_entity_id
				WHERE mis.lot_num = NVL(?,lot_num) 
				AND mis.model_code = NVL(?,model_code)
				AND mis.active = 1
				ORDER BY mis.serial_no";
		$data = $this->oracle->query($sql, array($lot_number, $model_code));
		return $data->result();
		
	}
	
	public function update_mis_attributes($params){
		
		$sql = "UPDATE XXXIPC_MIS SET 
					CS_NO = ?,
					VIN_NO = ?,
					ENGINE_NO = ?,
					BODY_NO = ?,
					FM_OFF_DATE = ?,
					REMARKS = ?,
					KEY_NO = ?,
					AC_NO = ?,
					STEREO_NO = ?,
					BUY_OFF_DATE = ?,
					LAST_UPDATED_BY = ?
				WHERE MIS_ID = ?";
		$stid = $this->oracle->query($sql, $params);
				
		return $this->oracle->affected_rows();
	}
	
	public function insert_vin_engine($params){
		
		$sql = "INSERT INTO XXXIPC_VIN_ENGINE (VIN, ENGINE_NO, LOT_NO) VALUES (?,?,?)";
		$this->oracle->query($sql, $params);
		
	}
	
	public function select_vin_per_lot($lot_no){
		$sql = "SELECT VIN_ID, VIN, ENGINE_NO
				FROM XXXIPC_VIN_ENGINE
				WHERE TRIM(LOT_NO) = TRIM(?)
				AND IS_USED = 0";
		$data = $this->oracle->query($sql, $lot_no);
		return $data->result();
	}
	
	public function select_engine($vin){
		$sql = "SELECT ENGINE_NO
				FROM XXXIPC_VIN_ENGINE
				WHERE VIN = ?";
		$data = $this->oracle->query($sql, $vin);
		$rows = $data->result();
		return $rows[0];
	}
	
	public function update_is_used_flag($vin){
		$sql = "UPDATE XXXIPC_VIN_ENGINE SET IS_USED = 1 WHERE VIN = ?";
		$this->oracle->query($sql, $vin);
		
	}
	
	public function get_vins_list(){
		$sql = "SELECT a.vin, a.engine_no, b.cs_no, a.lot_no
				FROM XXXIPC_VIN_ENGINE a
				LEFT JOIN XXXIPC_MIS b
				ON a.VIN = b.VIN_NO
				AND b.active = 1
				WHERE a.vin IS NOT NULL
				ORDER BY a.VIN_id DESC, a.lot_no";
				
		$data = $this->oracle->query($sql);
		return $data->result();
				
		
	}
	
	public function check_duplicate($params){
		
		$sql = "SELECT COUNT(VIN) CNT
				FROM XXXIPC_VIN_ENGINE
				WHERE VIN = NVL(?, VIN) OR ENGINE_NO = NVL(?, ENGINE_NO)";
		$data = $this->oracle->query($sql,$params);
		$rows = $data->result();
		return $rows[0];
	}

	public function fetchModelList()
	{
		$query = $this->oracle->select('PRODUCT_MODEL')
				->from('IPC.IPC_VE_VIN_MODEL')
				->order_by('PRODUCT_MODEL')
				->where('STATUS = 1')
				->get();

		return $query->result();
	}

	public function fetchDistinctLot($params)
	{
		$query = $this->oracle->distinct('LOT_NO')
				->select('LOT_NO')
				->order_by('LOT_NO', 'ASC')
				->where($params)
				->get('IPC.IPC_VE_VIN_ENGINE');

		return $query->result();
	}
}
