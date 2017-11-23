<?php

class Invoice_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		$this->uat = $this->load->database('uat', true);
	}
	
	public function get_daily_invoiced_units(){
		
		$sql = "SELECT rcta.customer_trx_id,
					   hcaa.account_number,
					   hp.party_name customer_name,
					   NVL(hcaa.account_name,  hp.party_name) account_name,
					   hcpc.name profile_class,
					   ooha.attribute3                     fleet_name,
					   ottl.description                    sales_type,
					   rcta.attribute3 cs_number,
					   msib.attribute9 sales_model,
					   msib.attribute8 body_color,
					   rcta.trx_number,
					   rcta.trx_date,
					   to_char(rcta.creation_date,'MM/DD/YYYY HH:MI:SS AM') creation_date,
					   rcta.purchase_order,
					   rtl.name                            payment_terms,
					   ooha.order_number,
					   ooha.ordered_date,
					   rcta.attribute5                     pullout_date,
					  CASE  WHEN rcta.attribute5 IS NOT NULL THEN to_date(rcta.attribute5, 'YYYY/MM/DD HH24:MI:SS')  + (NVL(SUBSTR( rtl.name, 0, INSTR( rtl.name, ' ')-1),  rtl.name) ) ELSE NULL END due_date,
						rcta.attribute4                     wb_number,
					   rcta.attribute8                     csr_number,
					   rcta.attribute11                    csr_date,
--					   rctla.net_amount,
--					   rctla.vat_amount,
--					   rctla.net_amount + rctla.vat_amount invoice_amount,
--					   ROUND (rctla.net_amount * .01, 2)   wht_amount,
--					   (rctla.net_amount + rctla.vat_amount) - (ROUND (rctla.net_amount * .01, 2)) amount_due,
--					    NVL(araa.amount_applied,0) paid_amount,
--					   (rctla.net_amount + rctla.vat_amount) - NVL(araa.amount_applied,0) balance,
					   CASE WHEN (NVL(araa.amount_applied,0) + 1) > ( (rctla.net_amount + rctla.vat_amount) - (ROUND (rctla.net_amount * .01, 2))) THEN 'PAID' ELSE 'UNPAID' END PAYMENT_STATUS,
					   CASE WHEN (NVL(araa.amount_applied,0) + 1) > ( (rctla.net_amount + rctla.vat_amount) - (ROUND (rctla.net_amount * .01, 2))) THEN  araa.apply_date ELSE NULL END paid_date
				 FROM ra_customer_trx_all rcta
					   LEFT JOIN ipc_ar_invoices_with_cm cm
						  ON rcta.customer_trx_id = cm.orig_trx_id
					   LEFT JOIN (SELECT customer_trx_id,
										 MAX(warehouse_id) warehouse_id,
										 MAX(inventory_item_id) inventory_item_id,
										 MAX(quantity_invoiced) quantity_invoiced,
										 SUM (LINE_RECOVERABLE) net_amount,
										 SUM (TAX_RECOVERABLE) vat_amount
									FROM ra_customer_trx_lines_all
								   WHERE line_type = 'LINE'
								GROUP BY customer_trx_id) rctla
						  ON rcta.customer_trx_id = rctla.customer_trx_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON rcta.sold_to_customer_id = hcaa.cust_account_id
					   LEFT JOIN hz_customer_profiles hzp
						  ON hcaa.cust_account_id = hzp.cust_account_id
						   AND rcta.bill_to_site_use_id = hzp.site_use_id
					   LEFT JOIN hz_cust_profile_classes hcpc
						   ON hzp.profile_class_id = hcpc.profile_class_id
					   LEFT JOIN hz_parties hp 
						   ON hcaa.party_id = hp.party_id
					   LEFT JOIN  mtl_system_items_b msib
					        ON rctla.warehouse_id = msib.organization_id
					        AND rctla.inventory_item_id = msib.inventory_item_id
					   LEFT JOIN
					     (SELECT applied_customer_trx_id,
								 SUM (amount_applied) amount_applied,
								 MAX (apply_date)   apply_date
							FROM ar_receivable_applications_all
						   WHERE display = 'Y'
						GROUP BY applied_customer_trx_id) araa
						  ON araa.applied_customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN oe_order_headers_all ooha
						  ON rcta.interface_header_attribute1 = ooha.order_number
					   LEFT JOIN ra_terms_tl rtl ON ooha.payment_term_id = rtl.term_id
					   LEFT JOIN oe_transaction_types_tl ottl
						  ON ooha.order_type_id = ottl.transaction_type_id
				  WHERE 1 = 1
				   AND rcta.cust_trx_type_id = 1002
				  AND cm.orig_trx_id IS NULL
				  AND rcta.trx_date = trunc(sysdate)";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	
	public function get_released_units_per_customer($customer_id){
		
		$customer_id = ($customer_id == 1)? NULL:$customer_id;
		
		$sql = "SELECT 
					so.customer_name,
					nvl(so.account_name,so.customer_name) account_name,
					so.fleet_name,
					so.serial_number cs_number,
					so.body_color,
					so.ORDERED_ITEM_DESC sales_model,
					so.order_number,
					so.line_number,
					SUBSTR (so.order_type_desc, 0, INSTR (so.order_type_desc, ' ') - 1) order_type,
					CASE wdd.released_status
						WHEN 'R' THEN 'Ready to Release'
						WHEN 'S' THEN 'Released to Warehouse'
						WHEN 'Y' THEN 'Staged'
						WHEN 'C' THEN 'Shipped'
						ELSE 'Error'
					END current_status,
					CASE wdd.released_status
						WHEN 'R' THEN 'Pick Release'
						WHEN 'S' THEN 'Transact Move Order'
						WHEN 'Y' THEN 'Ship Confirm'
						WHEN 'C' THEN 'Invoice'
						ELSE 'Error'
					END next_step,
					so.unit_selling_price + so.tax_value gross_amount,
					so.RELEASED_FLAG,
					so.released_date
					--pdc.check_number
				FROM IPC_SALES_ORDER_V so
					-- LEFT JOIN (SELECT *
					--	  FROM (SELECT check_number,
					--					cs_number,
					--				   RANK ()
					--					  OVER (PARTITION BY cs_number ORDER BY unit.check_id DESC)
					--					  rnk
					--			  FROM ipc.ipc_treasury_pdc_units unit
					--				   LEFT JOIN ipc.ipc_treasury_pdc pdcu
					--					  ON unit.check_id = pdcu.check_id
					--			 WHERE 1 = 1 AND pdcu.batch_id IS NOT NULL)
					--	 WHERE rnk = 1) pdc
					--	  ON so.serial_number = pdc.cs_number
				LEFT JOIN WSH_DELIVERY_DETAILS WDD
				ON so.so_line_id = wdd.SOURCE_LINE_ID
				LEFT JOIN wsh_delivery_assignments wda
				ON wdd.DELIVERY_DETAIL_ID = wda.DELIVERY_DETAIL_ID
				WHERE 1 = 1 
				AND so.SERIAL_NUMBER IS NOT NULL 
				AND (so.RELEASED_FLAG = 'Y' OR (so.RELEASED_FLAG is null AND so.customer_id != 11085))
				AND so.customer_id = NVL(?, so.customer_id)
				AND wdd.released_status IN ('R','S','Y')
				--AND wda.delivery_id is not null
				ORDER BY so.account_name
				";
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_released_units_customers(){
		$sql = "SELECT 
					so.customer_id,
					so.customer_name,
					so.account_name
				FROM IPC_SALES_ORDER_V so
				LEFT JOIN WSH_DELIVERY_DETAILS WDD
					ON so.so_line_id = wdd.SOURCE_LINE_ID
				LEFT JOIN wsh_delivery_assignments wda
				ON wdd.DELIVERY_DETAIL_ID = wda.DELIVERY_DETAIL_ID
				WHERE 1 = 1 
					AND so.SERIAL_NUMBER IS NOT NULL 
					AND so.RELEASED_FLAG = 'Y' -- OR (so.RELEASED_FLAG is null AND so.customer_id != 11085))
					AND wdd.released_status IN ('R','S','Y')
						--AND wda.delivery_id is not null
				GROUP BY so.customer_id,
						so.customer_name,
						so.account_name";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_vehicle_customers(){
		$sql = "SELECT DISTINCT hp.party_id customer_id, hp.party_name customer_name
				  FROM ra_customer_trx_all rcta
					   LEFT JOIN ipc_ar_invoices_with_cm cm
						  ON rcta.customer_trx_id = cm.orig_trx_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON rcta.sold_to_customer_id = hcaa.cust_account_id
					   LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
				 WHERE     rcta.cust_trx_type_id = 1002
					   AND rcta.attribute5 IS NULL
					   AND rcta.attribute3 IS NOT NULL 
					   ORDER BY hp.party_name";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_unpulledout_per_customers($party_id){
		$sql = "SELECT hp.party_name     customer_name,
					   hcaa.account_name account_name,
					   rcta.customer_trx_id trx_id,
					   rcta.trx_number,
					   rcta.attribute3   cs_number,
					   rcta.attribute4   wb_number,
					   rcta.attribute5   pullout_date
				  FROM ra_customer_trx_all rcta
					   LEFT JOIN ipc_ar_invoices_with_cm cm
						  ON rcta.customer_trx_id = cm.orig_trx_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON rcta.sold_to_customer_id = hcaa.cust_account_id
					   LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
				 WHERE     rcta.cust_trx_type_id = 1002
					   AND rcta.attribute5 IS NULL
					   AND rcta.attribute3 IS NOT NULL 
					   AND hp.party_id = ?
					   ORDER BY hcaa.account_name, rcta.trx_number";
		$data = $this->oracle->query($sql,$party_id);
		return $data->result();
	}
	
	
	public function get_invoice_details($invoices)
	{
		$sql = "SELECT rcta.customer_trx_id,
					   rcta.sold_to_customer_id,
					   cust.cust_account_id,
					   rcta.trx_number,
					   rcta.trx_date,
					   rtl.name                            payment_terms,
					   rcta.interface_header_attribute1                        so_number,
					   rcta.attribute4                                         wb_number,
					   rcta.attribute9 dr_number,
					   msn.inventory_item_id,                                   
					   msn.attribute1                                          csr_number,
					   msn.attribute12                                         csr_or_number,
					   msib.segment1                                           model_code,
					   msib.attribute9                                         sales_model,
					   msn.lot_number                                          lot_number,
					   msn.serial_number                                       cs_number,
					   msn.attribute2                                          chassis_number,
					   msib.attribute11                                        engine_type,
					   msn.attribute3                                          engine_no,
					   msib.attribute8                                         body_color,
					   msib.attribute17                                        fuel,
					   msib.attribute14                                        gvw,
					   msn.attribute6                                          key_no,
					   msib.attribute13                                        tire_specs,
					   msn.attribute8                                          battery,
					   msib.attribute16                                        displacement,
					   msib.attribute21                                        year_model,
					   msit.items1,
					   msit.items2,
					   cust.party_name,
					   cust.account_name,
					   cust.tax_reference,
					   cust.address,
					   ooha.attribute3                     						fleet_name,
					   cust.business_style,
					   cust.class_code,
					   rctla.vatable_sales,
					   rctla.discount,
					   rctla.vatable_sales + rctla.discount                    amt_net_of_vat,
					   rctla.vat_amount,
					   rctla.vatable_sales + rctla.discount + rctla.vat_amount total_sales
				  FROM ra_customer_trx_all rcta
					   INNER JOIN
					   (  SELECT customer_trx_id,
								 SUM (
									CASE
									   WHEN LINE_RECOVERABLE > 0 THEN LINE_RECOVERABLE
									   ELSE 0
									END)
									vatable_sales,
								 SUM (
									CASE
									   WHEN LINE_RECOVERABLE < 0 THEN LINE_RECOVERABLE
									   ELSE 0
									END)
									discount,
								 SUM (TAX_RECOVERABLE) vat_amount
							FROM ra_customer_trx_lines_all
						   WHERE line_type = 'LINE'
						GROUP BY customer_trx_id) rctla
						  ON rcta.customer_trx_id = rctla.customer_trx_id
						  INNER JOIN oe_order_headers_all ooha
							ON rcta.interface_header_attribute1 = ooha.order_number
						 INNER JOIN ra_terms_tl rtl ON ooha.payment_term_id = rtl.term_id
					   INNER JOIN mtl_serial_numbers msn
						  ON rcta.attribute3 = msn.serial_number
					   INNER JOIN mtl_system_items_b msib
						  ON     msib.inventory_item_id = msn.inventory_item_id
							 AND msib.organization_id = msn.current_organization_id
					   LEFT  JOIN
						  (SELECT HCAA.cust_account_id,
								 MAX (hp.party_name)             party_name,
								 MAX (hcaa.account_name)         account_name,
								 DECODE(regexp_replace(MAX(hl.address1),'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET|FLEET-PARTS|FLEET'), '', MAX(hl.address2) || ' ' || MAX(hl.address3), regexp_replace(MAX(hl.address1),'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET|FLEET-PARTS|FLEET') || ' ' || MAX(hl.address2) || ' ' || MAX(hl.address3)) address,
								 MAX (hccd.class_code_description) business_style,
								 MAX (hca.class_code)            class_code,
								 MAX (hcsua.tax_reference)       tax_reference
							FROM hz_cust_accounts_all hcaa
								 LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
								 LEFT JOIN HZ_CODE_ASSIGNMENTS hca
									ON hca.owner_table_id = hp.party_id AND hca.end_date_active IS NULL
								 LEFT JOIN HZ_CLASS_CODE_DENORM hccd
									ON     hca.class_code = hccd.class_code
									   AND hca.class_category = hccd.class_category
								 LEFT JOIN hz_cust_acct_sites_all hcasa
									ON hcaa.cust_account_id = hcasa.cust_account_id
								 LEFT JOIN hz_cust_site_uses_all hcsua
									ON hcasa.cust_acct_site_id = hcsua.cust_acct_site_id
								 LEFT JOIN hz_party_sites hps
									ON hcasa.party_site_id = hps.party_site_id
								 LEFT JOIN hz_locations hl
									ON hps.location_id = hl.location_id
						GROUP BY HCAA.cust_account_id ) cust
						  ON rcta.sold_to_customer_id = cust.cust_account_id 
					   LEFT JOIN (SELECT inventory_item_id, 
										organization_id,
										MAX(SUBSTR(LONG_DESCRIPTION, 1, INSTR(LONG_DESCRIPTION, CHR(10),1,12)-1)) items1,
										MAX(SUBSTR(LONG_DESCRIPTION,INSTR(TRIM(LONG_DESCRIPTION), CHR(10),1,12)+1,3000)) items2
							   FROM MTL_SYSTEM_ITEMS_TL
							 GROUP BY inventory_item_id, organization_id) msit
						   ON msib.inventory_item_id = msit.inventory_item_id
						   AND msit.organization_id = 121
						WHERE 1 = 1   
							--AND rcta.customer_trx_id IN (".$invoices.")
							AND rcta.trx_number IN (".$invoices.")"
							;
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_sales_daily_summary_by_dealer($from_date, $to_date)
	{
		$sql = "SELECT msi.attribute9 || ' ' ||  oola.attribute1 sales_model, msi.attribute8 body_color,
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
																15105)  AND ottl.name = 'FLT.Sales Order' THEN 1 ELSE NULL END) FLEET,
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
											COUNT(*) Total
						FROM ra_customer_trx_all rcta
							 LEFT JOIN
							 (SELECT DISTINCT
									 customer_trx_id,
									 inventory_item_id,
									 warehouse_id,
									 interface_line_attribute6 line_id
								FROM ra_customer_trx_lines_all
							   WHERE     1 = 1
									 AND line_type = 'LINE'
									 AND interface_line_attribute6 IS NOT NULL) rctla
								ON rcta.customer_trx_id = rctla.customer_trx_id
							 LEFT JOIN oe_order_lines_all oola 
								  ON rctla.line_id = oola.line_id
							   LEFT JOIN oe_order_headers_all ooha
									ON oola.header_id = ooha.header_id
							 LEFT JOIN ipc_ar_invoices_with_cm cm
								ON rctla.customer_trx_id = cm.orig_trx_id
								LEFT JOIN hz_cust_accounts_all hcca
							  ON rcta.sold_to_customer_id = hcca.cust_account_id
						   LEFT JOIN hz_parties hp 
							  ON hcca.party_id = hp.party_id
							LEFT JOIN mtl_system_items msi
							  ON rctla.inventory_item_id = msi.inventory_item_id
							  and rctla.warehouse_id = msi.organization_id
							 LEFT JOIN OE_TRANSACTION_TYPES_TL ottl
								 ON ooha.ORDER_TYPE_ID = ottl.TRANSACTION_TYPE_ID
					   WHERE     1 = 1
							 AND rcta.trx_Date BETWEEN ? AND ?
							 AND rcta.cust_trx_type_id = 1002
							 AND cm.orig_trx_id IS NULL
							-- AND msi.attribute9 = '180 mu-X 4x2 LS-A AT 3.0'
							  GROUP BY ROLLUP (msi.attribute9 || ' ' ||  oola.attribute1,msi.attribute8)";
		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result();
	}
}
