<?php

class Buyoff_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_for_buyoff_headers($lot_number){
		
		$sql = "SELECT model_code,
					   cs_no     cs_number,
					   vin_no    chassis_number,
					   body_no   body_number,
					   engine_no engine_number,
					   lot_num   lot_number,
					   ac_no     aircon_number,
					   stereo_no stereo_number,
					   key_no    key_number
				FROM xxxipc_mis
				   WHERE active = 1 
				   AND for_buyoff = 1 
				   AND completion_status = 0
				   AND lot_num = ?";
			
			//current status 3 => reside in store 
			//current status 4 => issue out of store
		
		$data = $this->oracle->query($sql, $lot_number);
		return $data->result();
	}
	 
	public function get_for_buyoff_lot_numbers(){
		
		$sql = "SELECT lot_num lot_number
				FROM xxxipc_mis
				   WHERE active = 1 
				   AND for_buyoff = 1 
				   AND completion_status = 0
				   GROUP BY lot_num";
			
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	 
	public function update_for_for_buyoff($user_id, $cs_numbers){
		
		$sql = "UPDATE xxxipc_mis SET completion_status  = 1, for_compxn_date = sysdate, check_complete_by = ?
				WHERE cs_no IN (".$cs_numbers.")";
		
		$this->oracle->query($sql, $user_id);
	}
	 
	public function get_buyoff_headers(){
		
		$sql = "SELECT msn.serial_number           cs_number,
						 msi.inventory_item_id       item_id,
						 msi.segment1                item_model,
						 mp.organization_code,
						 msn.current_subinventory_code subinventory_code,
						 msn.lot_number,
						 msn.attribute5              buyoff_date,
						 ippd.cs_number              for_repair,     -- not null => for repair
						 ipvs.cs_number              for_transfer  -- not null => for transfer
					FROM mtl_system_items_b msi
						 LEFT JOIN mtl_serial_numbers msn
							ON msi.inventory_item_id = msn.inventory_item_id
							AND msi.organization_id = msn.current_organization_id
						 LEFT JOIN mtl_parameters mp
							ON msi.organization_id = mp.organization_id
						 LEFT JOIN ipc_pdi_problem_details ippd
							ON msn.serial_number = ippd.cs_number
						 LEFT JOIN ipc_pdi_vehicle_status ipvs
							ON msn.serial_number = ipvs.cs_number
				   WHERE     1 = 1
						 AND mp.organization_code IN ('IVP', 'NYK', 'PSI')
						 AND msi.item_type = 'FG'
						 AND msn.current_status = 3
						 and c_attribute30 IS NULL
				ORDER BY msn.serial_number DESC";
			
			//current status 3 => reside in store 
			//current status 4 => issue out of store
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	 
	public function get_buyoff_line($cs_no){
		
		$sql = "SELECT 
				    msn.current_subinventory_code subinventory_code
				   ,msi.inventory_item_id
				   ,mp.organization_code 
				   ,msi.segment1 item_model    
				   ,msn.serial_number cs_number
				   ,msn.lot_number lot_number
				   ,msn.attribute2 chassis_no
				   ,msn.attribute4 body_number
				   ,msn.attribute3 engine_no
				   ,msi.attribute11 engine_model
				   ,msn.attribute6 key_number
				   ,msi.attribute8  body_color
				   ,SUBSTR(msn.attribute7, 1,instr(msn.attribute7,'/') - 1) ac_no
				   ,substr(msn.attribute7, - instr(reverse(msn.attribute7), '/') + 1) ac_brand
				   ,SUBSTR(msn.attribute9, 1,instr(msn.attribute9,'/') - 1) stereo_no
				   ,substr(msn.attribute9, - instr(reverse(msn.attribute9), '/') + 1) stereo_brand
				   ,substr(msn.attribute11, - instr(reverse(msn.attribute11), '/') + 1) fm_date
				   ,msn.attribute5 as buyoff_date
				   ,msi.item_type
			FROM mtl_system_items_b msi
				 LEFT JOIN mtl_serial_numbers msn
					 ON msi.inventory_item_id = msn.inventory_item_id
					 AND msi.organization_id = msn.current_organization_id
				 LEFT JOIN mtl_parameters mp
					ON msi.organization_id = mp.organization_id
			WHERE 1 = 1
				  AND mp.organization_code IN ('IVP','NYK','PSI')
				  AND msi.item_type = 'FG'
				  AND msn.current_status = 3
				  AND msn.serial_number = ?
			ORDER BY msn.serial_number DESC";
		
		$data = $this->oracle->query($sql,$cs_no);
		$rows = $data->result();
		return $rows[0];
	}

	public function for_repair($params)
	{
		$sql = "INSERT INTO IPC.IPC_PDI_PROBLEM_DETAILS (
					CS_NUMBER,
					DESCRIPTION,
					PROBLEM_CREATED_DATE)
				VALUES(?,?,?)";
		
		$this->oracle->query($sql,$params); 

		$sql2 = "DELETE FROM IPC.IPC_PDI_VEHICLE_STATUS WHERE CS_NUMBER = ?";
		
		$this->oracle->query($sql2,$params['cs_no']); 

		return true;
	} 

	public function for_transfer($params)
	{
		$sql = "INSERT INTO IPC.IPC_PDI_VEHICLE_STATUS (
					CS_NUMBER,
					LAST_UPDATE)
				VALUES(?,?)";

		$this->oracle->query($sql,$params); 

		$sql2 = "DELETE FROM IPC.IPC_PDI_PROBLEM_DETAILS WHERE CS_NUMBER = ?";
		
		$this->oracle->query($sql2,$params['cs_no']); 

		return true;
	}

	public function history_log($params)
	{
		$sql = "INSERT INTO IPC.IPC_PDI_HISTORY (
					CS_NUMBER,
					STATUS,
					DESCRIPTION,
					DATE_LOG)
				VALUES(?,?,?,?)";
		return $this->oracle->query($sql,$params); 
	}

	public function get_for_repair()
	{
		$sql = "SELECT
					CS_NUMBER,
					DESCRIPTION,
					TO_CHAR(PROBLEM_CREATED_DATE, 'mm/dd/YYYY') PROBLEM_CREATED_DATE
				FROM 
					IPC.IPC_PDI_PROBLEM_DETAILS";
		$data = $this->oracle->query($sql);
		return $data->result();
	}

	public function return_repair($params)
	{
		$sql = "INSERT INTO IPC.IPC_PDI_HISTORY (
					CS_NUMBER,
					STATUS,
					DESCRIPTION,
					DATE_LOG)
				VALUES(?,?,?,?)";
		$this->oracle->query($sql,$params); 

		$sql2 = "DELETE FROM IPC.IPC_PDI_PROBLEM_DETAILS WHERE CS_NUMBER = ?";
		
		$this->oracle->query($sql2,$params['cs_no']); 

		return true;
	}

	public function get_sales_model()
	{
		$sql = "SELECT DISTINCT(MSIB.ATTRIBUTE9) AS model 
				FROM MTL_SYSTEM_ITEMS_B MSIB 
				WHERE ORGANIZATION_ID = 107 
				ORDER BY MSIB.ATTRIBUTE9 ASC";
		$data = $this->oracle->query($sql);
		return $data->result();
	}

	public function get_buyoff_report_details($lot_no)
	{
		//~ $where = array($lot_no,$sales_model);
		$sql = "SELECT 
					MSN.LOT_NUMBER,
					NVL(MSIB.ATTRIBUTE9,'SALES MODEL IS NULL') SALES_MODEL,
					MSN.SERIAL_NUMBER cs_no,
					MSIB.ATTRIBUTE11 series,
					MSN.ATTRIBUTE3 engine_no,
					MSIB.ATTRIBUTE17 fuel_type,
					MSIB.ATTRIBUTE18 cylinder,
					MSIB.ATTRIBUTE16 piston_disp,
					MSN.ATTRIBUTE2 chassis_no,
					MSIB.ATTRIBUTE14 gvw,
					MSIB.ATTRIBUTE8 color
				FROM
					MTL_SYSTEM_ITEMS_B MSIB,
					MTL_SERIAL_NUMBERS MSN
				WHERE
					1=1
					AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
					AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
					AND MSN.current_subinventory_code IN ('VSS','FG')
					AND MSN.LOT_NUMBER = ?
					AND ROWNUM = 1
		";
		$data = $this->oracle->query($sql,$lot_no);
		return $data->result();
	}



	// Reference lot number
	public function get_buyoff_generate_report($params)
	{
		//~ $where = array($lot_no,$sales_model);
		$sql = "SELECT 
					MSN.LOT_NUMBER,
					MSIB.ATTRIBUTE9   SALES_MODEL,
					MSN.SERIAL_NUMBER cs_no,
					MSIB.ATTRIBUTE11  series,
					msib.attribute11 || ' ' ||msn.attribute3 engine_no,
					MSIB.ATTRIBUTE17  fuel_type,
					MSIB.ATTRIBUTE18  cylinder,
					MSIB.ATTRIBUTE16  piston_disp,
					MSN.ATTRIBUTE2    chassis_no,
					MSIB.ATTRIBUTE14  gvw,
					MSIB.ATTRIBUTE8   color,
					MSN.ATTRIBUTE4    body_no,
					msn.attribute5    buyoff_date,
					msn.attribute15   mr_date
				FROM
					MTL_SYSTEM_ITEMS_B MSIB,
					MTL_SERIAL_NUMBERS MSN
				WHERE
					1 = 1
					AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
					AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
					AND MSN.current_subinventory_code IN ('VSS','FG')
					AND MSN.LOT_NUMBER = ?

		";

		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}

	// Get sales model
	public function get_buyoff_sales_model()
	{
		$sql = "SELECT DISTINCT(MSIB.ATTRIBUTE9) AS model
				FROM MTL_SYSTEM_ITEMS_B MSIB
	   			WHERE MSIB.ATTRIBUTE9 IS NOT NULL
	   			ORDER BY MSIB.ATTRIBUTE9 ASC";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_buyoff_lot_number()
	{
		$sql = "SELECT DISTINCT(msn.lot_number) AS lot_number
				FROM mtl_serial_numbers msn
	   			WHERE 1 = 1
	   			AND msn.current_status IN (3, 4)
				AND msn.c_attribute30 IS NULL
				--AND msn.attribute1 is null
	   			ORDER BY msn.lot_number ASC";

		$data = $this->oracle->query($sql);
		return $data->result();
	}


	public function fetch_model()
	{
		$query = $this->oracle
				->distinct()
				->select('SEGMENT1')
				->from('MTL_SYSTEM_ITEMS_B MSIB')
				->get();

		return $query->result_array();
	}

	public function fetch_buyoff_prooflist($params)
	{		
		

		$sql = "SELECT * FROM
					(SELECT MSN.LOT_NUMBER,
						WE.WIP_ENTITY_NAME JOB_NO,	
						MSIB.SEGMENT1   MODEL,
						MSN.SERIAL_NUMBER cs_no,
						MSIB.ATTRIBUTE11  series,
						msib.attribute11  engine_no,
						msn.attribute3   engine_model,
						MSIB.ATTRIBUTE17  fuel_type,
						MSIB.ATTRIBUTE18  cylinder,
						MSIB.ATTRIBUTE16  piston_disp,
						MSN.ATTRIBUTE2    chassis_no,
						MSIB.ATTRIBUTE14  gvw,
						MSIB.ATTRIBUTE8   color,
						MSN.ATTRIBUTE4    body_no,
						msn.attribute6    key_number,
						msn.attribute7    AIRCON_NO,
						msib.attribute19  AIRCON_BRAND,
						msn.attribute9 stereo_no,
						msib.attribute20 stereo_brand,
						MSN.attribute15,
						CASE
							WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{2}$')
								THEN
								TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
							WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{4}$')
								THEN
								TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
							WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}')
								THEN
								TO_CHAR (TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS'),
								'MM/DD/YYYY')
							ELSE
								NULL
							END
							buyoff_date,
						msn.attribute15   mr_date
					FROM MTL_SYSTEM_ITEMS_B MSIB, MTL_SERIAL_NUMBERS MSN
					LEFT JOIN wip_entities we
					ON msn.original_wip_entity_id = we.wip_entity_id
					WHERE 1 = 1
					AND msn.c_attribute30 is null
					AND msn.current_status IN (3,4)
					AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
					AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
					AND msib.item_type = 'FG'),
					(SELECT DISTINCT(SEGMENT1) FROM MTL_SYSTEM_ITEMS_B MSIB) LIST
				WHERE 1 = 1
					AND TO_DATE(buyoff_date,'MM/DD/YYYY') BETWEEN ? AND ?
					AND MODEL = LIST.SEGMENT1";

		$data = $this->oracle->query($sql, $params);

		return $data->result_array();
	}


	// Filtered by date from and date to
	public function get_buyoff_filter_by_date($params) 
	{
		//~ $sql = '';

		//~ $config = array(
				//~ 'date_from' => $params['date_from'],
				//~ 'date_to' => $params['date_to']
			//~ );



			$sql = "/* Formatted on 10/27/2017 9:44:49 AM (QP5 v5.294) */
					SELECT MSN.LOT_NUMBER,
						   MSIB.ATTRIBUTE9                           SALES_MODEL,
						   MSN.SERIAL_NUMBER                         cs_no,
						   MSIB.ATTRIBUTE11                          series,
						   msib.attribute11 || ' ' || msn.attribute3 engine,
						   MSIB.ATTRIBUTE17                          fuel_type,
						   MSIB.ATTRIBUTE18                          cylinder,
						   MSIB.ATTRIBUTE16                          piston_disp,
						   MSN.ATTRIBUTE2                            chassis_no,
						   MSIB.ATTRIBUTE14                          gvw,
						   MSIB.ATTRIBUTE8                           color,
						   MSN.ATTRIBUTE4                            body_no,
						   MSN.attribute15,
						   msn.attribute5                            buyoff_date,
						   msn.attribute1                            csr_number,
						   msn.attribute5                            buyoff_date,
						   msn.lot_number,
						   msn.attribute15                           mr_date
					  FROM MTL_SYSTEM_ITEMS_B MSIB, MTL_SERIAL_NUMBERS MSN
					 WHERE     1 = 1
						   AND msn.current_status IN (3, 4)
						   AND msn.c_attribute30 IS NULL
						   AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
						   AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
						   AND msib.item_type = 'FG'
						   AND TRUNC (TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS')) BETWEEN ?  AND ?
						   AND MSIB.ATTRIBUTE9  = NVL(?, MSIB.ATTRIBUTE9 )
						   AND msn.lot_number  = NVL(?, msn.lot_number )";	

		

		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}

	public function get_vehicle_completion($params)
	{
		$sql = "SELECT 
				msib.segment1 assembly_model,
				msib.attribute9 sales_model,
				MAX(msib.attribute8) body_color,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 1 THEN 1 ELSE NULL END) day_1,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 2 THEN 1 ELSE NULL END) day_2,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 3 THEN 1 ELSE NULL END) day_3,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 4 THEN 1 ELSE NULL END) day_4,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 5 THEN 1 ELSE NULL END) day_5,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 6 THEN 1 ELSE NULL END) day_6,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 7 THEN 1 ELSE NULL END) day_7,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 8 THEN 1 ELSE NULL END) day_8,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 9 THEN 1 ELSE NULL END) day_9,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 10 THEN 1 ELSE NULL END) day_10,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 11 THEN 1 ELSE NULL END) day_11,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 12 THEN 1 ELSE NULL END) day_12,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 13 THEN 1 ELSE NULL END) day_13,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 14 THEN 1 ELSE NULL END) day_14,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 15 THEN 1 ELSE NULL END) day_15,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 16 THEN 1 ELSE NULL END) day_16,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 17 THEN 1 ELSE NULL END) day_17,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 18 THEN 1 ELSE NULL END) day_18,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 19 THEN 1 ELSE NULL END) day_19,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 20 THEN 1 ELSE NULL END) day_20,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 21 THEN 1 ELSE NULL END) day_21,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 22 THEN 1 ELSE NULL END) day_22,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 23 THEN 1 ELSE NULL END) day_23,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 24 THEN 1 ELSE NULL END) day_24,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 25 THEN 1 ELSE NULL END) day_25,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 26 THEN 1 ELSE NULL END) day_26,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 27 THEN 1 ELSE NULL END) day_27,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 28 THEN 1 ELSE NULL END) day_28,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 29 THEN 1 ELSE NULL END) day_29,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 30 THEN 1 ELSE NULL END) day_30,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 31 THEN 1 ELSE NULL END) day_31,
				COUNT(*) total
			 FROM mtl_system_items_b msib, mtl_serial_numbers msn
					   WHERE 1 = 1
						 AND msn.inventory_item_id = msib.inventory_item_id
						 AND msn.current_organization_id = msib.organization_id
						 AND msib.item_type = 'FG'
						 AND msn.c_attribute30 IS NULL
						 AND msn.current_status IN (3,4)
				 AND TO_DATE(TO_CHAR(TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY'),'MM/DD/YYYY') BETWEEN ? AND ?
				 and msib.attribute9 is not null
				 -- AND ROWNUM <= 20
				 GROUP BY ROLLUP (msib.attribute9,msib.segment1)
				 ORDER BY msib.attribute9";

		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}
	
	public function get_vehicle_completion_2($params)
	{
		$sql = "SELECT 
				msib.segment1 assembly_model,
				'NO SALES MODEL' sales_model,
				MAX(msib.attribute8) body_color,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 1 THEN 1 ELSE NULL END) day_1,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 2 THEN 1 ELSE NULL END) day_2,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 3 THEN 1 ELSE NULL END) day_3,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 4 THEN 1 ELSE NULL END) day_4,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 5 THEN 1 ELSE NULL END) day_5,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 6 THEN 1 ELSE NULL END) day_6,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 7 THEN 1 ELSE NULL END) day_7,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 8 THEN 1 ELSE NULL END) day_8,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 9 THEN 1 ELSE NULL END) day_9,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 10 THEN 1 ELSE NULL END) day_10,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 11 THEN 1 ELSE NULL END) day_11,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 12 THEN 1 ELSE NULL END) day_12,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 13 THEN 1 ELSE NULL END) day_13,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 14 THEN 1 ELSE NULL END) day_14,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 15 THEN 1 ELSE NULL END) day_15,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 16 THEN 1 ELSE NULL END) day_16,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 17 THEN 1 ELSE NULL END) day_17,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 18 THEN 1 ELSE NULL END) day_18,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 19 THEN 1 ELSE NULL END) day_19,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 20 THEN 1 ELSE NULL END) day_20,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 21 THEN 1 ELSE NULL END) day_21,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 22 THEN 1 ELSE NULL END) day_22,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 23 THEN 1 ELSE NULL END) day_23,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 24 THEN 1 ELSE NULL END) day_24,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 25 THEN 1 ELSE NULL END) day_25,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 26 THEN 1 ELSE NULL END) day_26,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 27 THEN 1 ELSE NULL END) day_27,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 28 THEN 1 ELSE NULL END) day_28,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 29 THEN 1 ELSE NULL END) day_29,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 30 THEN 1 ELSE NULL END) day_30,
				COUNT(CASE WHEN EXTRACT (day from to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS')) = 31 THEN 1 ELSE NULL END) day_31,
				COUNT(*) total
			 FROM mtl_system_items_b msib, mtl_serial_numbers msn
					   WHERE 1 = 1
						 AND msn.inventory_item_id = msib.inventory_item_id
						 AND msn.current_organization_id = msib.organization_id
						 AND msib.item_type = 'FG'
						 AND msn.c_attribute30 IS NULL
						 AND msn.current_status IN (3,4)
				 AND TO_DATE(TO_CHAR(TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY'),'MM/DD/YYYY') BETWEEN ? AND ?
				 AND msib.attribute9 IS NULL
				 GROUP BY ROLLUP (msib.attribute9,msib.segment1)
				 ORDER BY msib.attribute9";

		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}

	public function get_vehicle_completion_($params)
	{
		$sql = "SELECT 
				assembly_model,
				sales_model,
				MAX(body_color) body_color,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 1 THEN 1 ELSE NULL END) day_1,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 2 THEN 1 ELSE NULL END) day_2,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 3 THEN 1 ELSE NULL END) day_3,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 4 THEN 1 ELSE NULL END) day_4,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 5 THEN 1 ELSE NULL END) day_5,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 6 THEN 1 ELSE NULL END) day_6,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 7 THEN 1 ELSE NULL END) day_7,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 8 THEN 1 ELSE NULL END) day_8,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 9 THEN 1 ELSE NULL END) day_9,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 10 THEN 1 ELSE NULL END) day_10,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 11 THEN 1 ELSE NULL END) day_11,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 12 THEN 1 ELSE NULL END) day_12,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 13 THEN 1 ELSE NULL END) day_13,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 14 THEN 1 ELSE NULL END) day_14,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 15 THEN 1 ELSE NULL END) day_15,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 16 THEN 1 ELSE NULL END) day_16,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 17 THEN 1 ELSE NULL END) day_17,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 18 THEN 1 ELSE NULL END) day_18,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 19 THEN 1 ELSE NULL END) day_19,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 20 THEN 1 ELSE NULL END) day_20,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 21 THEN 1 ELSE NULL END) day_21,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 22 THEN 1 ELSE NULL END) day_22,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 23 THEN 1 ELSE NULL END) day_23,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 24 THEN 1 ELSE NULL END) day_24,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 25 THEN 1 ELSE NULL END) day_25,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 26 THEN 1 ELSE NULL END) day_26,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 27 THEN 1 ELSE NULL END) day_27,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 28 THEN 1 ELSE NULL END) day_28,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 29 THEN 1 ELSE NULL END) day_29,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 30 THEN 1 ELSE NULL END) day_30,
				COUNT(CASE WHEN EXTRACT (day from to_date(buyoff_date,'MM/DD/YYYY')) = 31 THEN 1 ELSE NULL END) day_31,
				COUNT(*) total
				FROM (
				SELECT msn.serial_number, msib.segment1 assembly_model, msib.attribute9 sales_model,
				                 msib.attribute8 body_color, 
				                 CASE
				                    WHEN REGEXP_LIKE (msn.attribute5,
				                                      '^[0-9]{2}-\w{3}-[0-9]{2}$' --     DD-MON-YY
				                                     )
				                       THEN TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
				                    WHEN REGEXP_LIKE (msn.attribute5,
				                                      '^[0-9]{2}-\w{3}-[0-9]{4}$'  -- DD-MON-YYYY
				                                     )
				                       THEN TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
				                    WHEN REGEXP_LIKE (msn.attribute5,
				                                      '^[0-9]{4}/[0-9]{2}/[0-9]{2}' -- YYYY/MM/DD
				                                     )
				                       THEN TO_CHAR (TO_DATE (msn.attribute5,
				                                              'YYYY/MM/DD HH24:MI:SS'
				                                             ),
				                                     'MM/DD/YYYY'
				                                    )
				                        WHEN REGEXP_LIKE (msn.attribute5,
				                                      '^[0-9]{2}/[0-9]{2}/[0-9]{4}' -- MM/DD/YYYY
				                                    )
				                       THEN TO_CHAR (TO_DATE (msn.attribute5,
				                                              'MM/DD/YYYY'
				                                             ),
				                                     'MM/DD/YYYY'
				                                    )
				                    ELSE TO_CHAR(to_Date(msn.attribute5, 'MM/DD/YYYY'),'MM/DD/YYYY')
				                 END buyoff_date
				            FROM mtl_system_items_b msib, mtl_serial_numbers msn
				           WHERE 1 = 1
				             AND msn.inventory_item_id = msib.inventory_item_id
				             AND msn.current_organization_id = msib.organization_id
				             AND msib.item_type = 'FG'
				             AND msn.c_attribute30 is null
				             and msn.current_status IN (3,4))
				               WHERE 1 = 1
				     AND TO_DATE (buyoff_date, 'MM/DD/YYYY') BETWEEN ? AND ?
				     --AND ROWNUM <= 100
				     GROUP BY rollup (sales_model, assembly_model)
				     ORDER BY sales_model
";

		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}
}
