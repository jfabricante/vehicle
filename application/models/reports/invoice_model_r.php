<?php

class Invoice_Model_r extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		$this->uat = $this->load->database('uat', true);
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
