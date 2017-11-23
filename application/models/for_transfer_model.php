<?php

class For_transfer_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_for_transfer($lot_number, $org){
		
		if($lot_number == 1){
			$lot_number = NULL;
		}
		
		$sql = "SELECT msib.inventory_item_id        item_id,
					   msib.segment1                 item_model,
					   mp.organization_code,
					   msn.current_subinventory_code subinventory_code,
					   msn.serial_number             cs_number,
					   msn.attribute2                chassis_number,
					   msn.attribute4                body_number,
					   msn.lot_number,
					   msn.attribute3                engine_number,
					   msn.attribute7                aircon_number,
					   msn.attribute9                stereo_number,
					   msn.attribute6                key_number,
					   TO_CHAR (TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24/MI/SS'),'MM/DD/YYYY') buyoff_date,
					   n_attribute30 transfer_flag
				  FROM mtl_system_items_b msib
					   LEFT JOIN mtl_serial_numbers msn
						  ON     msib.inventory_item_id = msn.inventory_item_id
							 AND msib.organization_id = msn.current_organization_id
					   LEFT JOIN mtl_parameters mp
						  ON msib.organization_id = mp.organization_id
				 WHERE     1 = 1
					   AND mp.organization_code = ?
					   AND msib.item_type = 'FG'
					   AND msn.current_status = 3
					   AND c_attribute30 IS NULL
					   AND n_attribute30 is null
					   AND msn.lot_number = NVL(?, msn.lot_number)";

		$data = $this->oracle->query($sql, array($org, $lot_number));
		return $data->result();
	}
	
	public function get_for_transfer_lot($org){
		
		$sql = "SELECT msn.lot_number
						FROM mtl_system_items_b msib
						   LEFT JOIN mtl_serial_numbers msn
							  ON     msib.inventory_item_id = msn.inventory_item_id
								 AND msib.organization_id = msn.current_organization_id
						   LEFT JOIN mtl_parameters mp
							  ON msib.organization_id = mp.organization_id
					 WHERE     1 = 1
						   AND mp.organization_code = ?
						   AND msib.item_type = 'FG'
						   AND msn.current_status = 3
						   AND c_attribute30 IS NULL
						   AND n_attribute30 IS NULL
					GROUP BY msn.lot_number
					ORDER BY msn.lot_number";

		$data = $this->oracle->query($sql, $org);
		return $data->result();
	}
	
	public function update_for_transfer_nyk($cs_numbers){
		
		$sql = "UPDATE mtl_serial_numbers
				   SET n_attribute30 = 1
				 WHERE serial_number IN (".$cs_numbers.")";

		$this->oracle->query($sql);
	}
}
