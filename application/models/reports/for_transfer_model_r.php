<?php

class For_transfer_Model_r extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		$this->uat = $this->load->database('uat', true);
	}
	
	public function get_received_units($from, $to){
		
		$sql = "select msib.segment1, msn.serial_number, msn.attribute2 chassis_number, msn.attribute3 engine_number, msn.attribute6 key_number, msn.attribute4 body_number,  msn.attribute5 buyoff_date, d_attribute19 received_date
				from mtl_Serial_numbers msn
				left join mtl_system_items_b msib
				on msn.current_organization_id = msib.organization_id
				and msn.inventory_item_id = msib.inventory_item_id
				where 1 = 1
				AND trunc(d_attribute19) between ? and ?
				AND msib.segment1 not like '%UC%'";
		
		$data = $this->oracle->query($sql, array($from, $to));
		return $data->result_array();
	}
}
