<?php

class Wholesale_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		//~ $this->uat = $this->load->database('uat', true);
	}
	
	public function get_ws_executive_summary($params){
		
		$sql = "  SELECT model_variant,
						 sales_model || ' ' || sales_model_remarks sales_modelo,
						 SUM (previous_invoiced)                 prev_invoiced,
						 SUM (current_invoiced)                  curr_invoiced,
						 SUM (reserved)                          reserved,
						 SUM (tagged)                            tagged
					FROM (SELECT DISTINCT
								 CASE
									WHEN (msib.prod_model) LIKE '%TB%' THEN 'CROSSWIND'
									WHEN (msib.prod_model) LIKE '%UC%' THEN 'MU-X'
									WHEN (msib.prod_model) LIKE '%TF%' THEN 'DMAX'
									WHEN (msib.prod_model) LIKE '%N%R%' THEN 'N-SERIES'
									WHEN (msib.prod_model) LIKE '%FV%' THEN 'F-SERIES'
									WHEN (msib.prod_model) LIKE '%F%R%' THEN 'F-SERIES'
									ELSE 'TRUCKS'
								 END
									model_variant,
								 msib.inventory_item_id,
								 msib.prod_model,
								 msib.sales_model,
								 msib.sales_model_remarks,
								 NVL (prev.previous_invoiced, 0) previous_invoiced,
								 NVL (curr.current_invoiced, 0) current_invoiced,
								 NVL (mr.reserved, 0)          reserved,
								 NVL (mr.tagged, 0)            tagged
							FROM (  SELECT msib.inventory_item_id,
										   msib.organization_id,
										   msib.segment1      prod_model,
										   msib.attribute9    sales_model,
										   TRIM (oola.attribute1) sales_model_remarks
									  FROM mtl_system_items msib
										   LEFT JOIN oe_order_lines_all oola
											  ON msib.inventory_item_id = oola.inventory_item_id
									 WHERE     1 = 1
										   AND msib.organization_id = 121
										   AND msib.inventory_item_status_code = 'Active'
										   AND msib.attribute9 IS NOT NULL
										   AND msib.item_type = 'FG'
								  GROUP BY msib.inventory_item_id,
										   msib.organization_id,
										   msib.segment1,
										   msib.attribute9,
										   TRIM (oola.attribute1)) msib ------------------------------------MSIB
								 LEFT JOIN
								 (  SELECT rctla.inventory_item_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (rcta.customer_trx_id) previous_invoiced
									  FROM ra_customer_trx_all rcta
										   LEFT JOIN
										   (SELECT DISTINCT
												   customer_trx_id,
												   inventory_item_id,
												   interface_line_attribute6 line_id
											  FROM ra_customer_trx_lines_all
											 WHERE     1 = 1
												   AND line_type = 'LINE'
												   AND interface_line_attribute6 IS NOT NULL) rctla
											  ON rcta.customer_trx_id = rctla.customer_trx_id
										   LEFT JOIN oe_order_lines_all oola
											  ON rctla.line_id = oola.line_id
										   LEFT JOIN ipc_ar_invoices_with_cm cm
											  ON rctla.customer_trx_id = cm.orig_trx_id
									 WHERE     1 = 1
										   AND rcta.trx_Date BETWEEN ? AND ?
										   AND rcta.cust_trx_type_id = 1002
										   AND cm.orig_trx_id IS NULL
								  GROUP BY rctla.inventory_item_id, TRIM (oola.attribute1)) prev  -------------------PREVIOUS
									ON     msib.inventory_item_id = prev.inventory_item_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (prev.sales_model_remarks, 1)
								 LEFT JOIN
								 (  SELECT rctla.inventory_item_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (rcta.customer_trx_id) current_invoiced
									  FROM ra_customer_trx_all rcta
										   LEFT JOIN
										   (SELECT DISTINCT
												   customer_trx_id,
												   inventory_item_id,
												   interface_line_attribute6 line_id
											  FROM ra_customer_trx_lines_all
											 WHERE     1 = 1
												   AND line_type = 'LINE'
												   AND interface_line_attribute6 IS NOT NULL)  rctla
											  ON rcta.customer_trx_id = rctla.customer_trx_id
										   LEFT JOIN oe_order_lines_all oola
											  ON rctla.line_id = oola.line_id
										   LEFT JOIN ipc_ar_invoices_with_cm cm
											  ON rctla.customer_trx_id = cm.orig_trx_id
									 WHERE     1 = 1
										   AND rcta.trx_Date BETWEEN ? AND ?
										   AND rcta.cust_trx_type_id = 1002
										   AND cm.orig_trx_id IS NULL
								  GROUP BY rctla.inventory_item_id, TRIM (oola.attribute1))curr  ---------------------CURRENT
									ON     msib.inventory_item_id = curr.inventory_item_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (curr.sales_model_remarks, 1)
								 LEFT JOIN
								 (  SELECT mr.inventory_item_id,
										   mr.organization_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (mr.inventory_item_id) reserved,
										   COUNT (
											  CASE
												 WHEN mr.lot_number IS NOT NULL THEN 1
												 ELSE NULL
											  END)
											  tagged
									  FROM mtl_reservations mr
										   LEFT JOIN oe_order_lines_all oola
											  ON mr.demand_source_line_id = oola.line_id
									 WHERE     1 = 1
										   AND sold_to_org_id <> 11085
									GROUP BY mr.inventory_item_id,
										   mr.organization_id,
										   TRIM (oola.attribute1)) mr ---------------------------------------MR
									ON     msib.inventory_item_id = mr.inventory_item_id
									   AND msib.organization_id = mr.organization_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (mr.sales_model_remarks, 1)
						   WHERE     1 = 1
								 --                 AND msib.inventory_item_id = 373236
								 AND (   prev.previous_invoiced IS NOT NULL
									  OR curr.current_invoiced IS NOT NULL
									  OR mr.reserved IS NOT NULL
									  OR mr.tagged IS NOT NULL))
				GROUP BY ROLLUP (model_variant, sales_model || ' ' || sales_model_remarks)
				ORDER BY model_variant, sales_model || ' ' || sales_model_remarks";
				
				  //~ -- AND mr.creation_date BETWEEN ? AND ?
				
		//~ params 
		//~ prev_start_date, 
		//~ prev_end_date, 
		//~ curr_start_date, 
		//~ curr_end_date, 
		
		
		$data = $this->oracle->query($sql, $params);
		return $data->result_array();
	}
	
	public function get_ws_executive_summary_excel($params){
		
		$sql = "  SELECT model_variant,
						 sales_model || ' ' || sales_model_remarks sales_modelo,
						 SUM (previous_invoiced)                 prev_invoiced,
						 SUM (current_invoiced)                  curr_invoiced,
						  SUM (current_invoiced)  -  SUM (previous_invoiced) diff,
						 SUM (reserved)                          reserved,
						 SUM (tagged)                            tagged,
						  SUM (current_invoiced) + SUM (tagged)   projected
					FROM (SELECT DISTINCT
								 CASE
									WHEN (msib.prod_model) LIKE '%TB%' THEN 'CROSSWIND'
									WHEN (msib.prod_model) LIKE '%UC%' THEN 'MU-X'
									WHEN (msib.prod_model) LIKE '%TF%' THEN 'DMAX'
									WHEN (msib.prod_model) LIKE '%N%R%' THEN 'N-SERIES'
									WHEN (msib.prod_model) LIKE '%FV%' THEN 'F-SERIES'
									WHEN (msib.prod_model) LIKE '%F%R%' THEN 'F-SERIES'
									ELSE 'TRUCKS'
								 END
									model_variant,
								 msib.inventory_item_id,
								 msib.prod_model,
								 msib.sales_model,
								 msib.sales_model_remarks,
								 NVL (prev.previous_invoiced, 0) previous_invoiced,
								 NVL (curr.current_invoiced, 0) current_invoiced,
								 NVL (mr.reserved, 0)          reserved,
								 NVL (mr.tagged, 0)            tagged
							FROM (  SELECT msib.inventory_item_id,
										   msib.organization_id,
										   msib.segment1      prod_model,
										   msib.attribute9    sales_model,
										   TRIM (oola.attribute1) sales_model_remarks
									  FROM mtl_system_items msib
										   LEFT JOIN oe_order_lines_all oola
											  ON msib.inventory_item_id = oola.inventory_item_id
									 WHERE     1 = 1
										   AND msib.organization_id = 121
										   AND msib.inventory_item_status_code = 'Active'
										   AND msib.attribute9 IS NOT NULL
										   AND msib.item_type = 'FG'
								  GROUP BY msib.inventory_item_id,
										   msib.organization_id,
										   msib.segment1,
										   msib.attribute9,
										   TRIM (oola.attribute1)) msib ------------------------------------MSIB
								 LEFT JOIN
								 (  SELECT rctla.inventory_item_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (rcta.customer_trx_id) previous_invoiced
									  FROM ra_customer_trx_all rcta
										   LEFT JOIN
										   (SELECT DISTINCT
												   customer_trx_id,
												   inventory_item_id,
												   interface_line_attribute6 line_id
											  FROM ra_customer_trx_lines_all
											 WHERE     1 = 1
												   AND line_type = 'LINE'
												   AND interface_line_attribute6 IS NOT NULL) rctla
											  ON rcta.customer_trx_id = rctla.customer_trx_id
										   LEFT JOIN oe_order_lines_all oola
											  ON rctla.line_id = oola.line_id
										   LEFT JOIN ipc_ar_invoices_with_cm cm
											  ON rctla.customer_trx_id = cm.orig_trx_id
									 WHERE     1 = 1
										   AND rcta.trx_Date BETWEEN ? AND ?
										   AND rcta.cust_trx_type_id = 1002
										   AND cm.orig_trx_id IS NULL
								  GROUP BY rctla.inventory_item_id, TRIM (oola.attribute1)) prev  -------------------PREVIOUS
									ON     msib.inventory_item_id = prev.inventory_item_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (prev.sales_model_remarks, 1)
								 LEFT JOIN
								 (  SELECT rctla.inventory_item_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (rcta.customer_trx_id) current_invoiced
									  FROM ra_customer_trx_all rcta
										   LEFT JOIN
										   (SELECT DISTINCT
												   customer_trx_id,
												   inventory_item_id,
												   interface_line_attribute6 line_id
											  FROM ra_customer_trx_lines_all
											 WHERE     1 = 1
												   AND line_type = 'LINE'
												   AND interface_line_attribute6 IS NOT NULL)  rctla
											  ON rcta.customer_trx_id = rctla.customer_trx_id
										   LEFT JOIN oe_order_lines_all oola
											  ON rctla.line_id = oola.line_id
										   LEFT JOIN ipc_ar_invoices_with_cm cm
											  ON rctla.customer_trx_id = cm.orig_trx_id
									 WHERE     1 = 1
										   AND rcta.trx_Date BETWEEN ? AND ?
										   AND rcta.cust_trx_type_id = 1002
										   AND cm.orig_trx_id IS NULL
								  GROUP BY rctla.inventory_item_id, TRIM (oola.attribute1))curr  ---------------------CURRENT
									ON     msib.inventory_item_id = curr.inventory_item_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (curr.sales_model_remarks, 1)
								 LEFT JOIN
								 (  SELECT mr.inventory_item_id,
										   mr.organization_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (mr.inventory_item_id) reserved,
										   COUNT (
											  CASE
												 WHEN mr.lot_number IS NOT NULL THEN 1
												 ELSE NULL
											  END)
											  tagged
									  FROM mtl_reservations mr
										   LEFT JOIN oe_order_lines_all oola
											  ON mr.demand_source_line_id = oola.line_id
									 WHERE     1 = 1
										   AND sold_to_org_id <> 11085
									GROUP BY mr.inventory_item_id,
										   mr.organization_id,
										   TRIM (oola.attribute1)) mr ---------------------------------------MR
									ON     msib.inventory_item_id = mr.inventory_item_id
									   AND msib.organization_id = mr.organization_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (mr.sales_model_remarks, 1)
						   WHERE     1 = 1
								 --                 AND msib.inventory_item_id = 373236
								 AND (   prev.previous_invoiced IS NOT NULL
									  OR curr.current_invoiced IS NOT NULL
									  OR mr.reserved IS NOT NULL
									  OR mr.tagged IS NOT NULL))
				GROUP BY ROLLUP (model_variant, sales_model || ' ' || sales_model_remarks)
				ORDER BY model_variant, sales_model || ' ' || sales_model_remarks";
				
				  //~ -- AND mr.creation_date BETWEEN ? AND ?
				
		//~ params 
		//~ prev_start_date, 
		//~ prev_end_date, 
		//~ curr_start_date, 
		//~ curr_end_date, 
		
		
		$data = $this->oracle->query($sql, $params);
		return $data->result_array();
	}
	
	public function get_invoiced_for_curr_day($curr_end_date){
		
		$sql = "select count(*) cnt
				from ra_customer_trx_all rcta
				left join ipc_ar_invoices_with_cm cm
				on rcta.customer_trx_id = cm.orig_trx_id
				where 1 = 1
				and rcta.cust_trx_type_id = 1002
				and cm.orig_trx_id is null
				and rcta.trx_date = ?";
						
		$data = $this->oracle->query($sql, $curr_end_date);
		$rows = $data->result();
		return $rows[0];
	}
	
	public function get_ws_executive_summary_count($params){
		
		$sql = "SELECT COUNT(DISTINCT  sales_model || ' ' || sales_model_remarks) cnt
					FROM (SELECT DISTINCT
								 CASE
									WHEN (msib.prod_model) LIKE '%TB%' THEN 'CROSSWIND'
									WHEN (msib.prod_model) LIKE '%UC%' THEN 'MU-X'
									WHEN (msib.prod_model) LIKE '%TF%' THEN 'DMAX'
									WHEN (msib.prod_model) LIKE '%N%R%' THEN 'N-SERIES'
									WHEN (msib.prod_model) LIKE '%FV%' THEN 'F-SERIES'
									WHEN (msib.prod_model) LIKE '%F%R%' THEN 'F-SERIES'
									ELSE 'TRUCKS'
								 END
									model_variant,
								 msib.inventory_item_id,
								 msib.prod_model,
								 msib.sales_model,
								 msib.sales_model_remarks,
								 NVL (prev.previous_invoiced, 0) previous_invoiced,
								 NVL (curr.current_invoiced, 0) current_invoiced,
								 NVL (mr.reserved, 0)          reserved,
								 NVL (mr.tagged, 0)            tagged
							FROM (  SELECT msib.inventory_item_id,
										   msib.organization_id,
										   msib.segment1      prod_model,
										   msib.attribute9    sales_model,
										   TRIM (oola.attribute1) sales_model_remarks
									  FROM mtl_system_items msib
										   LEFT JOIN oe_order_lines_all oola
											  ON msib.inventory_item_id = oola.inventory_item_id
									 WHERE     1 = 1
										   AND msib.organization_id = 121
										   AND msib.inventory_item_status_code = 'Active'
										   AND msib.attribute9 IS NOT NULL
										   AND msib.item_type = 'FG'
								  GROUP BY msib.inventory_item_id,
										   msib.organization_id,
										   msib.segment1,
										   msib.attribute9,
										   TRIM (oola.attribute1)) msib ------------------------------------MSIB
								 LEFT JOIN
								 (  SELECT rctla.inventory_item_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (rcta.customer_trx_id) previous_invoiced
									  FROM ra_customer_trx_all rcta
										   LEFT JOIN
										   (SELECT DISTINCT
												   customer_trx_id,
												   inventory_item_id,
												   interface_line_attribute6 line_id
											  FROM ra_customer_trx_lines_all
											 WHERE     1 = 1
												   AND line_type = 'LINE'
												   AND interface_line_attribute6 IS NOT NULL) rctla
											  ON rcta.customer_trx_id = rctla.customer_trx_id
										   LEFT JOIN oe_order_lines_all oola
											  ON rctla.line_id = oola.line_id
										   LEFT JOIN ipc_ar_invoices_with_cm cm
											  ON rctla.customer_trx_id = cm.orig_trx_id
									 WHERE     1 = 1
										   AND rcta.trx_Date BETWEEN ? AND ?
										   AND rcta.cust_trx_type_id = 1002
										   AND cm.orig_trx_id IS NULL
								  GROUP BY rctla.inventory_item_id, TRIM (oola.attribute1)) prev  -------------------PREVIOUS
									ON     msib.inventory_item_id = prev.inventory_item_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (prev.sales_model_remarks, 1)
								 LEFT JOIN
								 (  SELECT rctla.inventory_item_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (rcta.customer_trx_id) current_invoiced
									  FROM ra_customer_trx_all rcta
										   LEFT JOIN
										   (SELECT DISTINCT
												   customer_trx_id,
												   inventory_item_id,
												   interface_line_attribute6 line_id
											  FROM ra_customer_trx_lines_all
											 WHERE     1 = 1
												   AND line_type = 'LINE'
												   AND interface_line_attribute6 IS NOT NULL)  rctla
											  ON rcta.customer_trx_id = rctla.customer_trx_id
										   LEFT JOIN oe_order_lines_all oola
											  ON rctla.line_id = oola.line_id
										   LEFT JOIN ipc_ar_invoices_with_cm cm
											  ON rctla.customer_trx_id = cm.orig_trx_id
									 WHERE     1 = 1
										   AND rcta.trx_Date BETWEEN ? AND ?
										   AND rcta.cust_trx_type_id = 1002
										   AND cm.orig_trx_id IS NULL
								  GROUP BY rctla.inventory_item_id, TRIM (oola.attribute1))curr  ---------------------CURRENT
									ON     msib.inventory_item_id = curr.inventory_item_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (curr.sales_model_remarks, 1)
								 LEFT JOIN
								 (  SELECT mr.inventory_item_id,
										   mr.organization_id,
										   TRIM (oola.attribute1)   sales_model_remarks,
										   COUNT (mr.inventory_item_id) reserved,
										   COUNT (
											  CASE
												 WHEN mr.lot_number IS NOT NULL THEN 1
												 ELSE NULL
											  END)
											  tagged
									  FROM mtl_reservations mr
										   LEFT JOIN oe_order_lines_all oola
											  ON mr.demand_source_line_id = oola.line_id
									 WHERE     1 = 1
										   AND sold_to_org_id <> 11085
									GROUP BY mr.inventory_item_id,
										   mr.organization_id,
										   TRIM (oola.attribute1)) mr ---------------------------------------MR
									ON     msib.inventory_item_id = mr.inventory_item_id
									   AND msib.organization_id = mr.organization_id
									   AND NVL (msib.sales_model_remarks, 1) =
											  NVL (mr.sales_model_remarks, 1)
						   WHERE     1 = 1
								 --                 AND msib.inventory_item_id = 373236
								 AND (   prev.previous_invoiced IS NOT NULL
									  OR curr.current_invoiced IS NOT NULL
									  OR mr.reserved IS NOT NULL
									  OR mr.tagged IS NOT NULL))
									  group by model_variant
									ORDER BY model_variant";
				
		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}
	
	public function get_ws_summary($year){
		
		$sql = "SELECT model_variant,
						 sales_model || '  ' || sales_model_remarks         sales_model,
						 COUNT (CASE WHEN ar_month = 1 THEN 1 ELSE NULL END) Jan,
						 COUNT (CASE WHEN ar_month = 2 THEN 1 ELSE NULL END) Feb,
						 COUNT (CASE WHEN ar_month = 3 THEN 1 ELSE NULL END) Mar,
						 COUNT (CASE WHEN ar_month = 4 THEN 1 ELSE NULL END) Apr,
						 COUNT (CASE WHEN ar_month = 5 THEN 1 ELSE NULL END) May,
						 COUNT (CASE WHEN ar_month = 6 THEN 1 ELSE NULL END) Jun,
						 COUNT (CASE WHEN ar_month = 7 THEN 1 ELSE NULL END) Jul,
						 COUNT (CASE WHEN ar_month = 8 THEN 1 ELSE NULL END) Aug,
						 COUNT (CASE WHEN ar_month = 9 THEN 1 ELSE NULL END) Sep,
						 COUNT (CASE WHEN ar_month = 10 THEN 1 ELSE NULL END) Oct,
						 COUNT (CASE WHEN ar_month = 11 THEN 1 ELSE NULL END) Nov,
						 COUNT (CASE WHEN ar_month = 12 THEN 1 ELSE NULL END) Dec,
						 COUNT (*)                                          ytd
					FROM (SELECT vm.model_variant,
								 vm.sales_model,
								 vm.sales_model_remarks,
								 EXTRACT (MONTH FROM trx_date) ar_month
							FROM ipc_vehicle_models vm
								 LEFT JOIN
								 (SELECT rctla.inventory_item_id,
										 TRIM (oola.attribute1) sales_model_remarks,
										 rcta.trx_date,
										 rcta.trx_number,
										 rcta.customer_trx_id,
										 rcta.attribute3      cs_number
									FROM ra_customer_trx_all rcta
										 LEFT JOIN
										 (SELECT DISTINCT
												 customer_trx_id,
												 inventory_item_id,
												 interface_line_attribute6 line_id
											FROM ra_customer_trx_lines_all
										   WHERE     1 = 1
												 AND line_type = 'LINE'
												 AND interface_line_attribute6
														IS NOT NULL) rctla
											ON rcta.customer_trx_id =
												  rctla.customer_trx_id
										 LEFT JOIN oe_order_lines_all oola
											ON rctla.line_id = oola.line_id
										 LEFT JOIN ipc_ar_invoices_with_cm cm
											ON rctla.customer_trx_id = cm.orig_trx_id
								   WHERE     1 = 1
										 AND EXTRACT (YEAR FROM rcta.trx_Date) = ?
										 AND rcta.cust_trx_type_id = 1002
										 AND cm.orig_trx_id IS NULL) ar
									ON     vm.inventory_item_id = ar.inventory_item_id
									   AND NVL (vm.sales_model_remarks, 1) =
											  NVL (ar.sales_model_remarks, 1)
						   WHERE ar.trx_number IS NOT NULL)
				GROUP BY ROLLUP (model_variant, sales_model || '  ' || sales_model_remarks)";
				
		$data = $this->oracle->query($sql, $year);
		return $data->result();
	}
	
}
