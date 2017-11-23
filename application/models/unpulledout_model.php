<?php

class Unpulledout_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_unpulledout_headers(){
		
		$sql = "SELECT rcta.customer_trx_id,
				   rcta.attribute3 cs_number,
				   rcta.trx_date,
				   rcta.attribute11 csr_date,
				   hp.party_name,
				   hcaa.account_name,
				   ooha.order_number,
					msib.description prod_model,
					msib.attribute8 body_color,
					msib.attribute9 sales_model
			  FROM ra_customer_trx_all rcta
				   LEFT JOIN oe_order_headers_all ooha
					  ON rcta.interface_header_attribute1 = ooha.order_number
				   LEFT JOIN hz_cust_accounts_all hcaa
					  ON rcta.sold_to_customer_id = hcaa.cust_account_id
				   LEFT JOIN hz_parties hp 
					  ON hcaa.party_id = hp.party_id
				   LEFT JOIN ipc_ar_invoices_with_cm cm 
					  ON rcta.customer_trx_id = cm.orig_trx_id
				   LEFT JOIN mtl_serial_numbers msn
					  ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
					  ON msn.inventory_item_id = msib.inventory_item_id
					  AND msn.current_organization_id = msib.organization_id
			 WHERE cm.orig_trx_id IS NULL
			      AND msn.c_attribute30 IS NULL
				  AND rcta.cust_trx_type_id = 1002
				  AND rcta.attribute5 IS NULL
				   ORDER BY hp.party_name, hcaa.account_name, rcta.trx_date DESC";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_unpulledout_line($cs_no){
			
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
				   LEFT JOIN ra_customer_trx_all rcta
					  ON wnd.delivery_id = rcta.interface_header_attribute3
				   LEFT JOIN mtl_parameters mp
					   ON msib.organization_id = mp.organization_id
			 WHERE     1 = 1
				   AND rcta.trx_number IS NULL
				   AND mmt.transaction_type_id <> 100
				   AND msn.current_organization_id = 121
				   AND msn.serial_number = ?";
	
		$data = $this->oracle->query($sql,$cs_no);
		$rows = $data->result();
		return $rows[0];
	}

	public function for_unpulledout_report(){
		
		$sql = "SELECT 
					   hp.party_name customer_name,
					   hcaa.account_name,
					   ooha.attribute3,
					   msib.segment1 model,
					   NVL(msib.attribute9, msib.description) sales_model,
					   msn.serial_number cs_number,
					   msn.attribute2 vin,
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
		               CASE WHEN REGEXP_LIKE(msn.attribute14, '^[0-9]{2}-\w{3}-[0-9]{2}$') THEN TO_CHAR(msn.attribute14,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(msn.attribute14, '^[0-9]{2}-\w{3}-[0-9]{4}$') THEN TO_CHAR(msn.attribute14,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(msn.attribute14, '^[0-9]{4}/[0-9]{2}/[0-9]{2}') THEN TO_CHAR(TO_DATE(msn.attribute14,'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY')
		                     ELSE NULL
		               END
		               AS csr_date,
					   TO_CHAR(mmt.creation_date,'MM/DD/YYYY') allocation_date,
					   rctla.sales_type,
					   TO_CHAR(rcta.trx_date,'MM/DD/YYYY') trx_date,
					   rcta.trx_number invoice_no,
					   CASE WHEN NVL(araa.amount_applied, 0) + 1 >= round((rctla.net_amount - rctla.net_amount * 0.01),2) + rctla.vat_amount THEN 'Paid'
					   ELSE 'Unpaid'
					   END status,
					   to_char(araa.apply_date,'MM/DD/YYYY') paid_date,
					   rcta.attribute4 wb_number
					 FROM ra_customer_trx_all rcta
					 LEFT JOIN (SELECT customer_trx_id,
										 MAX(warehouse_id) warehouse_id,
										 MAX(INTERFACE_LINE_ATTRIBUTE2) sales_type,
										 MAX(inventory_item_id) inventory_item_id,
										 MAX(quantity_invoiced) quantity_invoiced,
										 SUM (LINE_RECOVERABLE) net_amount,
										 SUM (TAX_RECOVERABLE) vat_amount
									FROM ra_customer_trx_lines_all
								   WHERE line_type = 'LINE'
								GROUP BY customer_trx_id) rctla
						  ON rcta.customer_trx_id = rctla.customer_trx_id
					 LEFT JOIN (SELECT applied_customer_trx_id,
								 SUM (amount_applied) amount_applied,
								 MAX (apply_date)   apply_date
							FROM ar_receivable_applications_all
						   WHERE display = 'Y'
						GROUP BY applied_customer_trx_id) araa
						  ON araa.applied_customer_trx_id = rcta.customer_trx_id
				   LEFT JOIN oe_order_headers_all ooha
					  ON rcta.interface_header_attribute1 = ooha.order_number
				   LEFT JOIN hz_cust_accounts_all hcaa
					  ON rcta.sold_to_customer_id = hcaa.cust_account_id
				   LEFT JOIN hz_parties hp 
					  ON hcaa.party_id = hp.party_id
				   LEFT JOIN ipc_ar_invoices_with_cm cm 
					  ON rcta.customer_trx_id = cm.orig_trx_id
				   LEFT JOIN mtl_serial_numbers msn
					  ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
					  ON msn.inventory_item_id = msib.inventory_item_id
					  AND msn.current_organization_id = msib.organization_id
					LEFT JOIN mtl_material_transactions mmt
					  ON     msn.last_transaction_id = mmt.transaction_id
						 AND msn.inventory_item_id = mmt.inventory_item_id
						 AND msib.organization_id = mmt.organization_id
					LEFT JOIN WSH_NEW_DELIVERIES wnd
					  ON mmt.shipment_number = wnd.delivery_id
			 WHERE cm.orig_trx_id IS NULL 
				  AND rcta.cust_trx_type_id = 1002
				  AND rcta.attribute5 IS NULL
				  AND msn.c_attribute30 IS NULL
				 AND rcta.attribute3 IS NOT NULL
				 ORDER BY hp.party_name, hcaa.account_name, rcta.trx_date DESC";

		$data = $this->oracle->query($sql);
		return $data->result_array();
	}

}
