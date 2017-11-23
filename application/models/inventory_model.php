<?php

class Inventory_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		//~ $this->uat = $this->load->database('uat', true);
	}
	
	public function get_on_hand_availability(){
		
		$sql = "SELECT *
				FROM (SELECT msib.inventory_item_id,
					   msib.segment1 prod_model,
					   msib.description prod_description,
					   msib.attribute9 sales_model,
					   moq.onhand,
					   moq.onhand - NVL (mr.reserve, 0) available_to_reserve,
					   NVL (msn.tagged, 0) available_to_tag
				  FROM mtl_system_items_b msib
					LEFT JOIN (SELECT inventory_item_id, SUM (TRANSACTION_QUANTITY) onhand
										FROM mtl_onhand_quantities
									   WHERE     1 = 1
											 AND organization_id = 121
									GROUP BY inventory_item_id) moq
					ON msib.inventory_item_id = moq.inventory_item_id
					LEFT JOIN (SELECT inventory_item_id, COUNT (inventory_item_id) reserve
										FROM mtl_reservations
									   WHERE 1 = 1 AND organization_id = 121
									GROUP BY inventory_item_id) mr
					ON msib.inventory_item_id = mr.inventory_item_id
					LEFT JOIN ( SELECT inventory_item_id, COUNT (inventory_item_id) tagged
											FROM mtl_serial_numbers
										   WHERE     1 = 1
												 AND current_subinventory_code IN ('VSS')
												 AND current_organization_id = 121
												 AND c_attribute30 IS NULL
												 AND reservation_id IS NULL
												 AND current_status = 3
										GROUP BY inventory_item_id ) msn
					ON msib.inventory_item_id = msn.inventory_item_id
					WHERE 1 = 1
					AND msib.organization_id = 121
					AND moq.onhand IS NOT NULL)
					ORDER BY CASE WHEN available_to_reserve <> available_to_tag then 1 else 2 end, prod_model";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_on_hand_availability_per_model($item_id){
		
		$sql = "SELECT DISTINCT
					 NVL (hcaa.account_name, hp.party_name)         account_name,
					 msn.serial_number,
					 moq.lot_number,
					 moq.subinventory_code,
					 NVL (ooha.order_number, wd.source_header_number) order_number,
					 NVL (oola.line_number, wd.source_line_number)  line_number,
					 mr.creation_date                               reservation_date,
					 msn.d_attribute20                              tagged_date
				FROM mtl_onhand_quantities moq
					 INNER JOIN mtl_system_items_b msib
						ON     moq.organization_id = msib.organization_id
						   AND moq.inventory_item_id = msib.inventory_item_id
					 LEFT JOIN mtl_serial_numbers msn
						ON     msib.inventory_item_id = msn.inventory_item_id
						   AND msib.organization_id = msn.current_organization_id
						   AND moq.lot_number = msn.lot_number
						   AND moq.subinventory_code = msn.current_subinventory_code
					 LEFT JOIN mtl_reservations mr
						ON     msn.reservation_id = mr.reservation_id
						   AND msn.inventory_item_id = mr.inventory_item_id
					 LEFT JOIN wsh_deliverables_v wd
						ON msn.serial_number = wd.serial_number
					 LEFT JOIN oe_order_lines_all oola
						ON mr.demand_source_line_id = oola.line_id
					 LEFT JOIN oe_order_headers_all ooha ON oola.header_id = ooha.header_id
					 LEFT JOIN hz_cust_accounts_all hcaa
						ON oola.sold_to_org_id = hcaa.cust_account_id
					 LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
			   WHERE     1 = 1
					 AND msn.current_status = 3
					 AND msn.c_attribute30 IS NULL
					 AND moq.inventory_item_id = ?
					 AND moq.organization_id = 121
			ORDER BY order_number";

		$data = $this->oracle->query($sql, $item_id);
		return $data->result();
	}
	
	public function get_reserved_wout_tagged($item_id){
		
		$sql = "SELECT 
					NVL(hcaa.account_name,   hp.party_name) account_name,
					   ooha.order_number,
					   oola.line_number,
							  mr.creation_date reservation_date
				  FROM mtl_reservations mr
					   LEFT JOIN oe_order_lines_all oola
						  ON mr.demand_source_line_id = oola.line_id
					   LEFT JOIN oe_order_headers_all ooha ON oola.header_id = ooha.header_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON oola.sold_to_org_id = hcaa.cust_account_id
					   LEFT JOIN hz_parties hp 
						 ON hcaa.party_id = hp.party_id
				 WHERE mr.inventory_item_id = ? AND mr.lot_number IS NULL";

		$data = $this->oracle->query($sql, $item_id);
		return $data->result();
	}
	
	public function get_model_details($item_id){
		
		$sql = "SELECT msib.segment1    prod_model,
					   msib.description prod_model_desc,
					   msib.attribute9  sales_model
				  FROM mtl_system_items_b msib
				 WHERE organization_id = 121 AND inventory_item_id = ?";

		$data = $this->oracle->query($sql, $item_id);
		$rows = $data->result();
		return $rows[0];
	}
}
