<?php

class Tagged_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		$this->uat = $this->load->database('uat', true);
	}
	
	public function get_tagged_headers(){
		
		$sql = "SELECT so.customer_name party_name,
					NVL(so.account_name, so.customer_name) account_name,
					so.serial_number cs_number,
					so.sales_model,
					so.body_color,
					so.order_number,
					so.line_number,
					so.reservation_date tagged_Date,
					NVL(SUBSTR(ORDER_TYPE_DESC, 0, INSTR(ORDER_TYPE_DESC, ' ')-1), ORDER_TYPE_DESC) order_type,
					 TRUNC(SYSDATE) - TRUNC(so.reservation_date) aging
				FROM IPC_SALES_ORDER_V so
				LEFT JOIN WSH_DELIVERY_DETAILS WDD
				ON so.so_line_id = wdd.SOURCE_LINE_ID
				--LEFT JOIN wsh_delivery_assignments wda
				--ON wdd.DELIVERY_DETAIL_ID = wda.DELIVERY_DETAIL_ID
				WHERE 1 = 1
				AND so.SERIAL_NUMBER IS NOT NULL
				--AND wda.delivery_id is null
				AND so.RELEASED_FLAG = 'N'
				--AND so.RELEASED_FLAG = 'N' -- OR (so.RELEASED_FLAG is null AND so.customer_id = 11085))
				";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_tagged_oc_headers(){
		
		$sql = "SELECT customer_name party_name,
					account_name,
					serial_number cs_number,
					sales_model,
					body_color,
					order_number,
					line_number
				FROM IPC_SALES_ORDER_V
				WHERE SERIAL_NUMBER IS NULL
				AND RELEASED_FLAG = 'N'
				AND reservation_date is null
				AND so_line_status  IN ('ENTERED','AWAITING_SHIPPING')
				ORDER BY customer_name, account_name, order_number, line_number";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_tagged_line($cs_no){
			
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
				   AND msn.current_organization_id = 212
				   AND msn.serial_number = ?";
	
		$data = $this->oracle->query($sql,$cs_no);
		$rows = $data->result();
		return $rows[0];
	}

	public function for_tagged_report(){
		
		$sql = "SELECT so.customer_name,
					so.account_name,
					so.fleet_name,
					so.serial_number,
					so.sales_model || ' ' || oola.attribute1,
					so.body_color,
					msn.attribute2                           chassis_number,
					msn.attribute3 engine_no,
					msn.attribute6 key_number,
					msn.attribute1 csr_number,
					msn.lot_number,
					msn.attribute5,
					msn.attribute14,
					ooha.ordered_date,
					so.order_number,
					ooha.quote_number						quote_number,
					ottl.description                         order_type,
					rtl.name                                 payment_terms,
					so.reservation_date,
					so.unit_selling_price + so.tax_value gross_amount,
					so.line_number,
					 TRUNC (SYSDATE) - TRUNC(so.reservation_date) aging,
					 so.RELEASED_FLAG
				FROM IPC_SALES_ORDER_V so
				LEFT JOIN mtl_serial_numbers msn
				ON so.serial_number = msn.serial_number
				 LEFT JOIN mtl_system_items_b msib
					          ON     msn.inventory_item_id = msib.inventory_item_id
					             AND msn.current_organization_id = msib.organization_id
					       LEFT JOIN mtl_parameters mt
					          ON msib.organization_id = mt.organization_id
					            LEFT JOIN oe_order_lines_all oola
					          ON so.so_line_id = oola.line_id
					       LEFT JOIN oe_order_headers_all ooha ON oola.header_id = ooha.header_id
					       LEFT JOIN RA_TERMS_TL rtl ON oola.payment_term_id = rtl.term_id
					       LEFT JOIN oe_transaction_types_tl ottl
					          ON ooha.order_type_id = ottl.transaction_type_id
				WHERE so.SERIAL_NUMBER IS NOT NULL
				";
		//~ 11085
		$data = $this->oracle->query($sql);
		return $data->result_array();
	}
	
	public function for_tagged_oc_report(){
		
		$sql = "SELECT so.customer_name,
						so.account_name,
						so.fleet_name,
						so.serial_number,
						so.sales_model  || ' ' || oola.attribute1 ,
						so.body_color,
						msn.attribute2 chassis_number,
						msn.attribute3 engine_no,
						msn.attribute6 key_number,
						msn.attribute1 csr_number,
						msn.lot_number,
						msn.attribute5,
						msn.attribute14,
						ooha.ordered_date,
						so.order_number,
						ooha.quote_number quote_number,
						ottl.description order_type,
						rtl.name payment_terms,
						so.reservation_date,
						so.unit_selling_price + so.tax_value gross_amount,
						so.line_number,
						TRUNC (SYSDATE) - TRUNC(so.reservation_date) aging
				FROM IPC_SALES_ORDER_V so
				LEFT JOIN mtl_serial_numbers msn
				ON so.serial_number = msn.serial_number
				 LEFT JOIN mtl_system_items_b msib
					          ON     msn.inventory_item_id = msib.inventory_item_id
					             AND msn.current_organization_id = msib.organization_id
					       LEFT JOIN mtl_parameters mt
					          ON msib.organization_id = mt.organization_id
					            LEFT JOIN oe_order_lines_all oola
					          ON so.so_line_id = oola.line_id
					       LEFT JOIN oe_order_headers_all ooha ON oola.header_id = ooha.header_id
					       LEFT JOIN RA_TERMS_TL rtl ON ooha.payment_term_id = rtl.term_id
					       LEFT JOIN oe_transaction_types_tl ottl
					          ON ooha.order_type_id = ottl.transaction_type_id
				WHERE so.SERIAL_NUMBER IS NULL
				AND so.RELEASED_FLAG = 'N'
				AND so.reservation_date is null";
		
		$data = $this->oracle->query($sql);
		return $data->result_array();
	}
	
	public function get_oc_balance_summary($from_date, $to_date){
		
		$sql = "SELECT * FROM (
				SELECT msib.attribute9 || ' ' ||  oola.attribute1 sales_model, msib.attribute8 body_color,
						 COUNT(CASE cust_account_id WHEN 14090  THEN 1 ELSE NULL END) BULACAN,  
						 COUNT(CASE cust_account_id WHEN 14085  THEN 1 ELSE NULL END) CABANATUAN,
						 COUNT(CASE cust_account_id WHEN 14088  THEN 1 ELSE NULL END) ISABELA,
						 COUNT(CASE cust_account_id WHEN 15086  THEN 1 ELSE NULL END) CAGAYAN,
						 COUNT(CASE cust_account_id WHEN 15118  THEN 1 ELSE NULL END) SAN_PABLO,
						 COUNT(CASE cust_account_id WHEN 15084  THEN 1 ELSE NULL END) MAKATI,
						 COUNT(CASE cust_account_id WHEN 15123  THEN 1 ELSE NULL END) BATANGAS,
						 COUNT(CASE cust_account_id WHEN 15089  THEN 1 ELSE NULL END) COMMONWEALTH,
						 COUNT(CASE cust_account_id WHEN 15099  THEN 1 ELSE NULL END) MANILA,
						 COUNT(CASE cust_account_id WHEN 15114  THEN 1 ELSE NULL END) EDSA,
						 COUNT(CASE cust_account_id WHEN 15126  THEN 1 ELSE NULL END) PAMPANGA,
						 COUNT(CASE cust_account_id WHEN 15129  THEN 1 ELSE NULL END) PANGASINAN,
						 COUNT(CASE cust_account_id WHEN 15121  THEN 1 ELSE NULL END) QA,
						 COUNT(CASE cust_account_id WHEN 15096  THEN 1 ELSE NULL END) ALABANG,
						 COUNT(CASE cust_account_id WHEN 15094  THEN 1 ELSE NULL END) CAVITE,
						 COUNT(CASE cust_account_id WHEN 15098  THEN 1 ELSE NULL END) PASIG,
						 COUNT(CASE cust_account_id WHEN 15141  THEN 1 ELSE NULL END) MANDAUE,
						 COUNT(CASE cust_account_id WHEN 15136  THEN 1 ELSE NULL END) ILOILO,
						 COUNT(CASE cust_account_id WHEN 15138  THEN 1 ELSE NULL END) GENSAN,
						 COUNT(CASE cust_account_id WHEN 15107  THEN 1 ELSE NULL END) DAVAO,
						 COUNT(CASE cust_account_id WHEN 17088  THEN 1 ELSE NULL END) BUTUAN,
						 COUNT(CASE cust_account_id WHEN 15105  THEN 1 ELSE NULL END) BACOLOD,
						 COUNT(CASE cust_account_id WHEN 11085  THEN 1 ELSE NULL END) IPC,
						 COUNT (CASE WHEN cust_account_id NOT IN (14090,
																14085,
																14088,
																15086,
																15118,
																15084,
																15123,
																15089,
																15099,
																15114,
																15126,
																15129,
																15121,
																15096,
																15094,
																15098,
																15141,
																15136, 
																15138, 
																15107,
																11085,
																17088,
																15105)  AND ottl.name = 'FLT.Sales Order' THEN 1 ELSE NULL END) flt,
						 COUNT (CASE WHEN cust_account_id NOT IN (14090,
																14085,
																14088,
																15086,
																15118,
																15084,
																15123,
																15089,
																15099,
																15114,
																15126,
																15129,
																15121,
																15096,
																15094,
																15098,
																15141,
																15136, 
																15138, 
																15107,
																11085,
																17088,
																15105) AND ottl.name != 'FLT.Sales Order' THEN 1 ELSE NULL END) OTHERS,
						COUNT(*) Total,
						COUNT(CASE WHEN ooha.attribute3 is not null then 1 else null end) fleet
						  FROM oe_order_headers_all ooha
							   LEFT JOIN oe_order_lines_all oola
									ON ooha.header_id = oola.header_id
							   LEFT JOIN mtl_reservations mr
								  ON oola.line_id = mr.demand_source_line_id
							   LEFT JOIN hz_cust_accounts_all hcca
								  ON oola.sold_to_org_id = hcca.cust_account_id
							   LEFT JOIN hz_parties hp ON hcca.party_id = hp.party_id
							   LEFT JOIN mtl_system_items_b msib
								ON oola.INVENTORY_ITEM_ID = msib.INVENTORY_ITEM_ID
									AND oola.SHIP_FROM_ORG_ID = msib.ORGANIZATION_ID
							   LEFT JOIN OE_TRANSACTION_TYPES_TL ottl
								  ON ooha.ORDER_TYPE_ID = ottl.TRANSACTION_TYPE_ID
						 WHERE     1 = 1
							   AND oola.flow_status_code IN ('ENTERED', 'AWAITING_SHIPPING')
							   AND oola.ship_from_org_id = 121
							   AND mr.reservation_id IS NULL
							   AND ooha.ordered_date BETWEEN ? AND ? 
							   GROUP BY ROLLUP (msib.attribute9 || ' ' ||  oola.attribute1,msib.attribute8)
							   )
							   ORDER BY sales_model, body_color";

		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result();
	}
	
	public function get_tagged_summary(){
		
		$sql = "SELECT msib.attribute9 || ' ' ||  oola.attribute1 sales_model, msib.attribute8 body_color,
					 COUNT(CASE cust_account_id WHEN 14090  THEN 1 ELSE NULL END) BULACAN,  
					 COUNT(CASE cust_account_id WHEN 14085  THEN 1 ELSE NULL END) CABANATUAN,
					 COUNT(CASE cust_account_id WHEN 14088  THEN 1 ELSE NULL END) ISABELA,
					 COUNT(CASE cust_account_id WHEN 15086  THEN 1 ELSE NULL END) CAGAYAN,
					 COUNT(CASE cust_account_id WHEN 15118  THEN 1 ELSE NULL END) SAN_PABLO,
					 COUNT(CASE cust_account_id WHEN 15084  THEN 1 ELSE NULL END) MAKATI,
					 COUNT(CASE cust_account_id WHEN 15123  THEN 1 ELSE NULL END) BATANGAS,
					 COUNT(CASE cust_account_id WHEN 15089  THEN 1 ELSE NULL END) COMMONWEALTH,
					 COUNT(CASE cust_account_id WHEN 15099  THEN 1 ELSE NULL END) MANILA,
					 COUNT(CASE cust_account_id WHEN 15114  THEN 1 ELSE NULL END) EDSA,
					 COUNT(CASE cust_account_id WHEN 15126  THEN 1 ELSE NULL END) PAMPANGA,
					 COUNT(CASE cust_account_id WHEN 15129  THEN 1 ELSE NULL END) PANGASINAN,
					 COUNT(CASE cust_account_id WHEN 15121  THEN 1 ELSE NULL END) QA,
					 COUNT(CASE cust_account_id WHEN 15096  THEN 1 ELSE NULL END) ALABANG,
					 COUNT(CASE cust_account_id WHEN 15094  THEN 1 ELSE NULL END) CAVITE,
					 COUNT(CASE cust_account_id WHEN 15098  THEN 1 ELSE NULL END) PASIG,
					 COUNT(CASE cust_account_id WHEN 15141  THEN 1 ELSE NULL END) MANDAUE,
					 COUNT(CASE cust_account_id WHEN 15136  THEN 1 ELSE NULL END) ILOILO,
					 COUNT(CASE cust_account_id WHEN 15138  THEN 1 ELSE NULL END) GENSAN,
					 COUNT(CASE cust_account_id WHEN 15107  THEN 1 ELSE NULL END) DAVAO,
					  COUNT(CASE cust_account_id WHEN 17088  THEN 1 ELSE NULL END) BUTUAN,
						COUNT(CASE cust_account_id WHEN 15105  THEN 1 ELSE NULL END) BACOLOD,
					 COUNT(CASE cust_account_id WHEN 11085  THEN 1 ELSE NULL END) IPC,
					 COUNT (CASE WHEN cust_account_id NOT IN (14090,
															14085,
															14088,
															15086,
															15118,
															15084,
															15123,
															15089,
															15099,
															15114,
															15126,
															15129,
															15121,
															15096,
															15094,
															15098,
															15141,
															15136, 
															15138, 
															15107,
															11085,
															17088,
															15105)  AND ottl.name = 'FLT.Sales Order' THEN 1 ELSE NULL END) FLT,
					 COUNT (CASE WHEN cust_account_id NOT IN (14090,
															14085,
															14088,
															15086,
															15118,
															15084,
															15123,
															15089,
															15099,
															15114,
															15126,
															15129,
															15121,
															15096,
															15094,
															15098,
															15141,
															15136, 
															15138, 
															15107,
															11085,
															17088,
															15105) AND ottl.name != 'FLT.Sales Order' THEN 1 ELSE NULL END) OTHERS,
					COUNT(*) Total,
					COUNT(CASE WHEN ooha.attribute3 is not null then 1 else null end) fleet
					  FROM oe_order_headers_all ooha
						   LEFT JOIN oe_order_lines_all oola
								ON ooha.header_id = oola.header_id
						   LEFT JOIN mtl_reservations mr
							  ON oola.line_id = mr.demand_source_line_id
							LEFT JOIN mtl_serial_numbers msn
								on mr.reservation_id = msn.reservation_id
						   LEFT JOIN hz_cust_accounts_all hcca
							  ON oola.sold_to_org_id = hcca.cust_account_id
						   LEFT JOIN hz_parties hp ON hcca.party_id = hp.party_id
						   LEFT JOIN mtl_system_items_b msib
							ON oola.INVENTORY_ITEM_ID = msib.INVENTORY_ITEM_ID
								AND oola.SHIP_FROM_ORG_ID = msib.ORGANIZATION_ID
						   LEFT JOIN OE_TRANSACTION_TYPES_TL ottl
							  ON ooha.ORDER_TYPE_ID = ottl.TRANSACTION_TYPE_ID
								LEFT JOIN OE_ORDER_HOLDS_ALL hold
                                        ON oola.line_id = hold.line_id
					 WHERE     1 = 1
--							   AND oola.flow_status_code IN ('ENTERED', 'AWAITING_SHIPPING')
						   AND oola.ship_from_org_id = 121
						     --and hold.released_flag = 'N'
						     and NVL (hold.RELEASED_FLAG, NVL (oola.ATTRIBUTE20, 'N')) = 'N'
						   AND msn.serial_number is not null
--							   AND ooha.ordered_date BETWEEN '01-OCT-17' AND '31-OCT-17'
						   GROUP BY ROLLUP (msib.attribute9 || ' ' ||  oola.attribute1,msib.attribute8)";

		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
}
