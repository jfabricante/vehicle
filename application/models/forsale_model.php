<?php

class Forsale_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_forsale_headers($item_id){
		
		$sql = "SELECT msn.serial_number cs_number,
					   msib.attribute9 sales_model,
					   msn.attribute2 chassis_number,
					   msn.attribute14 csr_date,
					   msn.attribute5 buyoff_date,
					   msib.description prod_model,
					   msib.attribute8 body_color
				  FROM mtl_serial_numbers msn
					   LEFT JOIN mtl_system_items_b msib
						  ON msn.inventory_item_id = msib.inventory_item_id
						  AND msn.current_organization_id = msib.organization_id
					   LEFT JOIN mtl_parameters mt
						  ON msib.organization_id = mt.organization_id
				 WHERE 1 = 1
					   AND mt.organization_code IN ('IVS')
					   AND msn.current_subinventory_code = 'VSS' 
					   AND msn.reservation_id IS NULL
					   AND msn.inventory_item_id = ?
					   AND msn.c_attribute30 is null
					 ORDER BY msib.attribute9, msn.attribute5 ASC";

		$data = $this->oracle->query($sql, $item_id);
		return $data->result();
	}
	
	public function get_forsale_per_model(){
		
		$sql = "SELECT msib.attribute9         sales_model,
					 msib.description        prod_model,
					 msib.attribute8         body_color,
					 msn.inventory_item_id,
					 COUNT (msn.serial_number) cnt
				FROM mtl_serial_numbers msn
					 LEFT JOIN mtl_system_items_b msib
						ON     msn.inventory_item_id = msib.inventory_item_id
						   AND msn.current_organization_id = msib.organization_id
					 LEFT JOIN mtl_parameters mt
						ON msib.organization_id = mt.organization_id
			   WHERE     1 = 1
					 AND mt.organization_code IN ('IVS')
					 AND msn.current_subinventory_code = 'VSS'
					 AND msn.reservation_id IS NULL
					  AND msn.c_attribute30 is null
			GROUP BY msib.attribute9, msib.attribute8, msib.description, msn.inventory_item_id
			ORDER BY msib.attribute9";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_forsale_line($cs_no){
			
		$sql = "SELECT
					msib.inventory_item_id,
					msn.current_subinventory_code subinventory_code,
					mp.organization_code,
					msn.serial_number cs_number,
					msib.segment1 item_model,
					msn.attribute2 vin,
					msn.attribute5 buyoff_date,
					msn.attribute14 csr_date,
					ooha.ordered_date,
					ooha.order_number,
					mmt.creation_date allocation_date,
					wnd.confirm_date shipment_date,
					msn.attribute1 csr_number,
					hp.party_name customer_name,
					hca.account_name,
					hca.cust_account_id customer_id,
					msn.lot_number lot_number,
					msn.attribute2 chassis_no,
					msn.attribute4 body_number,
					msn.attribute3 engine_no,
					msib.attribute11 engine_model,
					msn.attribute6 key_number,
					msib.attribute8 body_color,
					SUBSTR (msn.attribute7, 1, INSTR (msn.attribute7, '/') - 1) ac_no,
					SUBSTR (msn.attribute7, -INSTR (reverse (msn.attribute7), '/') + 1) ac_brand,
					SUBSTR (msn.attribute9, 1, INSTR (msn.attribute9, '/') - 1) stereo_no,
					SUBSTR (msn.attribute9, -INSTR (reverse (msn.attribute9), '/') + 1) stereo_brand,
					SUBSTR (msn.attribute11, -INSTR (reverse (msn.attribute11), '/') + 1) fm_date,
					msib.item_type
			  FROM mtl_serial_numbers msn
				   LEFT JOIN mtl_system_items_b msib
					  ON msn.inventory_item_id = msib.inventory_item_id
						 AND msn.current_organization_id = msib.organization_id
				   LEFT JOIN mtl_material_transactions mmt
					  ON     msn.last_transaction_id = mmt.transaction_id
						 AND msn.inventory_item_id = mmt.inventory_item_id
						 AND msib.organization_id = mmt.organization_id
				   LEFT JOIN oe_order_lines_all oola
					  ON mmt.TRX_SOURCE_LINE_ID = oola.line_id
				   LEFT JOIN oe_order_headers_all ooha
					  ON oola.header_id = ooha.header_id
				   LEFT JOIN hz_cust_accounts hca
					  ON ooha.sold_to_org_id = hca.cust_account_id
				   LEFT JOIN hz_parties hp
					  ON hca.party_id = hp.party_id
				   LEFT JOIN WSH_NEW_DELIVERIES wnd
					  ON mmt.shipment_number = wnd.delivery_id AND wnd.status_code = 'CL'
				   LEFT JOIN (SELECT a.customer_trx_id, a.trx_number, a.trx_date, a.interface_header_attribute3
							  FROM ra_customer_trx_all a
								   LEFT JOIN ipc_ar_invoices_with_cm b ON a.customer_trx_id = orig_trx_id
							 WHERE b.cm_trx_number is null AND a.cust_trx_type_id = 1002) rcta
					  ON wnd.delivery_id = rcta.interface_header_attribute3
				   LEFT JOIN mtl_parameters mp
					   ON msib.organization_id = mp.organization_id
			 WHERE     1 = 1
				   AND rcta.trx_number IS NULL
				   --AND mmt.transaction_type_id <> 100
				   AND msn.current_organization_id = 121
			   AND mmt.transaction_id =
					  (SELECT MAX (mmt.TRANSACTION_ID)
						 FROM mtl_unit_transactions mut
							  LEFT JOIN mtl_transaction_lot_numbers mtln
								 ON mtln.serial_transaction_id = mut.transaction_id
							  LEFT JOIN mtl_material_transactions mmt
								 ON mmt.transaction_id = mtln.transaction_id
						WHERE mut.serial_number = msn.serial_number)
						   AND msn.serial_number = ?";
	
		$data = $this->oracle->query($sql,$cs_no);
		$rows = $data->result();
		return $rows[0];
	}
}
