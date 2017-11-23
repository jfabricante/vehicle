<?php

class Available_units_model_r extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_available_to_tag(){

		$sql = "SELECT msn.serial_number cs_number,
					   msn.attribute2 chassis_number,
					   msib.description prod_model,
					   msib.attribute9 sales_model,
					   msib.attribute8 body_color,
					   msn.attribute4 body_no,
					   msn.lot_number,
					   msn.attribute3 engine_no,
					   CASE WHEN REGEXP_LIKE(msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{2}$') THEN TO_CHAR(msn.attribute5,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{4}$') THEN TO_CHAR(msn.attribute5,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(msn.attribute5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}') THEN TO_CHAR(TO_DATE(msn.attribute5,'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY')
		                     WHEN msn.attribute5 IS NULL THEN ''
		                     ELSE TO_CHAR(TO_DATE(msn.attribute5,'MM/DD/YYYY'),'MM/DD/YYYY')
		               END
		               AS buyoff_date,
		               msn.attribute1 csr_no
				  FROM mtl_serial_numbers msn
					   LEFT JOIN mtl_system_items_b msib
						  ON msn.inventory_item_id = msib.inventory_item_id
						  AND msn.current_organization_id = msib.organization_id
					   LEFT JOIN mtl_parameters mt
						  ON msib.organization_id = mt.organization_id
				 WHERE 1 = 1
					   AND mt.organization_code = 'IVS'
					   AND msn.current_subinventory_code = 'VSS' 
					   AND msn.reservation_id IS NULL";

		$data = $this->oracle->query($sql);
		return $data->result_array();
	}
	
	public function get_available_units()
	{

		$sql = "SELECT msn.serial_number cs_number,
					   msn.attribute2    chassis_number,
					   msi.description   prod_model,
					   msi.attribute9    sales_model,
					   msi.attribute8    body_color,
					   msn.attribute4    body_no,
					   msn.lot_number,
					   msn.attribute3    engine_no,
					   msn.attribute5    buyoff_date,
					   msn.attribute1    csr_no
				  FROM mtl_serial_numbers msn
					   LEFT JOIN mtl_system_items msi
						  ON     msn.inventory_item_id = msi.inventory_item_id
							 AND msn.current_organization_id = msi.organization_id
				 WHERE     1 = 1
					   AND msn.current_status = 3
					   AND msn.c_attribute30 IS NULL
					   AND msn.reservation_id IS NULL
					   AND msi.item_type = 'FG'";

		$data = $this->oracle->query($sql);
		return $data->result_array();
	}
	
	public function get_available_summary()
	{

		$sql = "SELECT * FROM (
					SELECT NVL (msi.attribute9, msi.description) sales_model,
							 msi.attribute8 body_color,
							 COUNT (CASE WHEN msi.organization_id IN (88,141) THEN 1 ELSE NULL END)
								ivp_qty,
							 COUNT (CASE WHEN msi.organization_id = 121 THEN 1 ELSE NULL END)
								ivs_qty,
							 COUNT (msn.serial_number) qty
						FROM mtl_serial_numbers msn
							 LEFT JOIN mtl_system_items msi
								ON     msn.inventory_item_id = msi.inventory_item_id
								   AND msn.current_organization_id = msi.organization_id
					   WHERE     1 = 1
							 AND msn.current_status = 3
							 AND msn.c_attribute30 IS NULL
							 AND msi.item_type = 'FG'
							 AND msn.reservation_id IS NULL
					--       and  msn.current_organization_id = 121
					--         AND msi.attribute9 in ('mu-X 4x2 LS-A (8N) AT 3.0','180 mu-X 4x4 LS-A AT 3.0')
					GROUP BY ROLLUP (NVL (msi.attribute9, msi.description), msi.attribute8)
				)
				ORDER BY sales_model, body_color";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
}
