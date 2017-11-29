<?php

class Tagged_Model_r extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		$this->uat = $this->load->database('uat', true);
	}
	
	public function get_tagged_units_detailed($customer_ids){
		
		if($customer_ids != NULL){
			$and = "AND ooha.sold_to_org_id IN (".$customer_ids.")";
		}
		else{
			$and = "";
		}
		
		$sql = "SELECT hcca.cust_account_id,
					 MAX(NVL (hp.party_name || ' - ' || hcca.account_name, hp.party_name))    account_name,
					 MAX(msib.attribute9 || ' ' || oola.attribute1) sales_model,
					 MAX(ooha.order_number) order_number,
					 MAX(oola.line_number) line_number,
					 MAX(msn.attribute2)                            chassis_number,
					 MAX(msn.attribute3)                            engine_number,
					 msn.serial_number                        cs_number,
					 MAX(msn.attribute6)                            key_number,
					 MAX(msn.lot_number) lot_number,
					 MAX(msib.attribute8)                           body_color,
					 MAX(msn.d_attribute20)                         tagged_date,
					 MAX(ooha.attribute3)                           fleet_name,
					 MAX(oola.unit_selling_price + oola.tax_value)  amount,
					 MAX(rt.name)                                   payment_terms,
					 MAX(TRUNC (SYSDATE) - TRUNC (msn.d_attribute20)) aging,
					 MAX(msn.attribute1)                            csr_number,
					 COUNT(msn.serial_number) cnt,
					 COUNT(CASE WHEN msn.attribute1 IS NULL THEN NULL ELSE 1 END) cnt_csr
				FROM oe_order_headers_all ooha
					 LEFT JOIN oe_order_lines_all oola ON ooha.header_id = oola.header_id
					 LEFT JOIN mtl_reservations mr
						ON oola.line_id = mr.demand_source_line_id
					 LEFT JOIN mtl_serial_numbers msn
						ON mr.reservation_id = msn.reservation_id
					 LEFT JOIN hz_cust_accounts_all hcca
						ON oola.sold_to_org_id = hcca.cust_account_id
					 LEFT JOIN hz_parties hp ON hcca.party_id = hp.party_id
					 LEFT JOIN mtl_system_items_b msib
						ON oola.inventory_item_id = msib.inventory_item_id
						   AND oola.ship_from_org_id = msib.organization_id
					 LEFT JOIN oe_transaction_types_tl ottl
						ON ooha.order_type_id = ottl.transaction_type_id
					 LEFT JOIN ra_terms_tl rt ON oola.payment_term_id = rt.term_id
					 LEFT JOIN oe_order_holds_all hold ON oola.line_id = hold.line_id
			   WHERE     1 = 1
					 AND oola.ship_from_org_id = 121
					 AND NVL (hold.RELEASED_FLAG, NVL (oola.ATTRIBUTE20, 'N')) = 'N'
					 AND msn.serial_number IS NOT NULL
					 " . $and . "
			GROUP BY ROLLUP (hcca.cust_account_id, msn.serial_number  )";
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_oc_detailed($from, $to, $customer_ids){
		
		if($customer_ids != NULL){
			$and = "AND ooha.sold_to_org_id IN (".$customer_ids.")";
		}
		else{
			$and = "AND hcca.cust_account_id != 11085";
			//~ $and = "AND hcca.cust_account_id in (15121,15096)";
		}
		
		$sql = "SELECT hp.party_name || ' - ' || hcca.account_name account_name,
					   msib.model_variant,
					   msib.sales_model || ' ' || oola.attribute1   sales_model,
					   msib.body_color,
					   ooha.order_number,
					  oola.line_number,
					   MAX(ooha.ordered_date) ordered_Date,
					   MAX(NVL (msn.serial_number, rcta.attribute3))    cs_number,
					   MAX(rcta.trx_number) trx_number,
					   MAX(NVL (msn.d_attribute20, msn2.d_attribute20)) tagged_date,
					   MAX(NVL (to_date(ooha.attribute12), ooha.ordered_date))   oc_date,
					   -- MAX(ooha.attribute12 )                           oc_date,
					   MAX(mr.creation_date)                            reservation_date,
					   COUNT(*) cnt,
					   COUNT(CASE WHEN NVL (msn.serial_number, rcta.attribute3) IS NOT NULL THEN 1 ELSE NULL END ) cnt_w_tagged,
					   COUNT(CASE WHEN NVL (msn.serial_number, rcta.attribute3) IS NULL THEN 1 ELSE NULL END ) cnt_wo_tagged
				  FROM oe_order_headers_all ooha
					   LEFT JOIN oe_order_lines_all oola ON ooha.header_id = oola.header_id
					   LEFT JOIN mtl_reservations mr
						  ON oola.line_id = mr.demand_source_line_id
					   LEFT JOIN mtl_serial_numbers msn
						  ON mr.reservation_id = msn.reservation_id
					   LEFT JOIN ra_customer_trx_lines_all rctla
						  ON     rctla.interface_line_attribute1 = TO_CHAR (ooha.order_number)
							 AND rctla.interface_line_attribute6 = TO_CHAR (oola.line_id)
					   LEFT JOIN ra_customer_trx_all rcta
						  ON rctla.customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN mtl_serial_numbers msn2
						  ON rcta.attribute3 = msn2.serial_number
					   LEFT JOIN hz_cust_accounts_all hcca
						  ON oola.sold_to_org_id = hcca.cust_account_id
					   LEFT JOIN hz_parties hp ON hcca.party_id = hp.party_id
					   LEFT JOIN ipc_vehicle_models msib
						  ON     oola.INVENTORY_ITEM_ID = msib.INVENTORY_ITEM_ID
							 AND oola.SHIP_FROM_ORG_ID = msib.ORGANIZATION_ID
							 AND NVL (msib.sales_model_remarks, 1) =  NVL (oola.attribute1, 1)
					   LEFT JOIN OE_TRANSACTION_TYPES_TL ottl
						  ON ooha.ORDER_TYPE_ID = ottl.TRANSACTION_TYPE_ID
				 WHERE     1 = 1
					   AND oola.ship_from_org_id = 121
					   AND msn.c_Attribute30 IS NULL
					   AND oola.flow_status_code != 'CANCELLED'
					   AND ooha.ordered_date BETWEEN ? AND ?
				--       AND ooha.order_number = '3010026292'
					   ".$and."
					   AND rctla.interface_line_attribute11 = 0
					   GROUP BY ROLLUP (hp.party_name || ' - ' || hcca.account_name, msib.model_variant, msib.sales_model || ' ' || oola.attribute1,  msib.body_color,ooha.order_number,oola.line_number)
					   ORDER BY hp.party_name || ' - ' || hcca.account_name, msib.model_variant, msib.sales_model || ' ' || oola.attribute1,  msib.body_color,ooha.order_number,oola.line_number";
		
		$data = $this->oracle->query($sql, array($from, $to));
		return $data->result();
	}
	
	public function get_vehicle_dealers()
	{
		$sql = " SELECT hcp.site_use_id,
					  hca.cust_account_id customer_id,
					  hp.party_id,
					  hps.party_site_id,
					  hp.party_number,
					  hp.party_name,
					  hca.account_number,
					  hca.account_name,
					  hcpc.name profile_class_name
				 FROM apps.hz_customer_profiles hcp
					  INNER JOIN apps.hz_cust_profile_classes hcpc
						 ON hcp.profile_class_id = hcpc.profile_class_id
					  INNER JOIN apps.hz_cust_site_uses_all hcsua
						 ON hcsua.site_use_id = hcp.site_use_id
					  INNER JOIN apps.hz_cust_acct_sites_all hcasa
						 ON hcasa.cust_acct_site_id = hcsua.cust_acct_site_id
					  INNER JOIN apps.hz_cust_accounts_all hca
						 ON hca.cust_account_id = hcasa.cust_account_id
					  INNER JOIN apps.hz_party_sites hps
						 ON hps.party_site_id = hcasa.party_site_id
					  INNER JOIN apps.hz_parties hp ON hp.party_id = hps.party_id
				WHERE     hcp.profile_class_id = 1043                   -- Dealers-Vehicle
					  AND hcp.status = 'A'
					  AND hcsua.status = 'A'
					  AND hca.account_name is not null";
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
}
