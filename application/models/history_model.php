<?php

class History_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
		$this->ifs = $this->load->database('ifs', true);
		$this->mysqli = $this->load->database('mysqli', true);
	}
	
	public function check_cs_no($cs_no){
		
		$sql = "SELECT id FROM ifs_vehicle_info WHERE cs_no = ?";
		$data = $this->mysqli->query($sql,$cs_no);
		return $data->num_rows();
	}
	
	public function get_ifs_headers($last_update){
		
		$sql = "SELECT DISTINCT vmt.prod_order_no,
					   vmt.serial_no,
					   vmt.cs_no,
					   vmt.prod_model,
					   spt.catalog_desc sales_model,
					   vmt.act_prod_n_end buyoff_date,
					   vmt.fm_date,
					   vmt.body_color,
					   vmt.vin_no vin,
					   vmt.body_no,
					   vmt.engine_type,
					   vmt.engine_no,
					   vmt.key_no,
					   vmt.wb_no,
					   vmt.aircon_brand,
					   vmt.aircon_no,
					   vmt.stereo_no,
					   vmt.stereo_brand,
					   vmt.sales_order,
					   vmt.reservation_date tagged_date,
					   vmt.payment payment_date,
					   vmt.pullout pullout_date,
					   it.invoice_no,
					   it.invoice_date,
					   vmt.csr_number csr_no,
					   vmt.or_no csr_or_no,
					   vmt.dop_description lot_no,
					   vmt.csrdate csr_date,
					   it.vat_dom_amount net_amount,
					   it.net_dom_amount vat_amount,
					   cit.customer_id customer_id,
					   cit2.customer_id customer_id_2,
					   cit.name customer_name,
					   cit2.name customer_name_2,
					   TO_CHAR(vmt.last_update, 'YYYY-MM-DD HH24:MI:SS') last_update
				  FROM vehicle_master_tab vmt
					   LEFT JOIN invoice_tab it
						  ON vmt.invoice_no = it.js_invoice_no
							 AND NVL (vmt.payer, vmt.dealer_code) = it.identity
							 AND vmt.sales_order = it.creators_reference
					   LEFT JOIN sales_part_tab spt
							ON vmt.model = spt.catalog_no
					   LEFT JOIN customer_info_tab cit
						  ON it.identity = cit.customer_id
					   LEFT JOIN customer_info_tab cit2
						  ON NVL (vmt.dealer_code, vmt.payer) = cit2.customer_id
						WHERE vmt.cs_no IS NOT NULL
						AND TO_CHAR(vmt.last_update, 'YYYY-MM-DD HH24:MI:SS') > ?
				   ORDER BY vmt.act_prod_n_end DESC";
		
		$data = $this->ifs->query($sql, $last_update);
		return $data->result();
	}
	
	public function insert_ifs_headers($params){
			
		$sql = "INSERT INTO ifs_vehicle_info (
					   prod_order_no,
					   serial_no,
					   cs_no,
					   prod_model,
					   sales_model,
					   buyoff_date,
					   fm_date,
					   body_color,
					   vin,
					   body_no,
					   engine_type,
					   engine_no,
					   key_no,
					   wb_no,
					   aircon_brand,
					   aircon_no,
					   stereo_no,
					   stereo_brand,
					   sales_order,
					   tagged_date,
					   payment_date,
					   pullout_date,
					   invoice_no,
					   invoice_date,
					   csr_no,
					   csr_or_no,
					   lot_no,
					   csr_date,
					   net_amount,
					   vat_amount,
					   customer_id,
					   customer_id_2,
					   customer_name,
					   customer_name_2,
					   last_update)
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	
		$this->mysqli->query($sql,$params);
	}
	
	public function update_ifs_headers($params){
			
		$sql = "UPDATE ifs_vehicle_info SET 
					   prod_order_no = ?,
					   serial_no = ?,
					   cs_no = ?,
					   prod_model = ?,
					   sales_model = ?,
					   buyoff_date = ?,
					   fm_date  = ?,
					   body_color  = ?,
					   vin = ?,
					   body_no = ?,
					   engine_type = ?,
					   engine_no = ?,
					   key_no = ?,
					   wb_no = ?,
					   aircon_brand = ?,
					   aircon_no = ?,
					   stereo_no = ?,
					   stereo_brand = ?,
					   sales_order = ?,
					   tagged_date = ?,
					   payment_date = ?,
					   pullout_date = ?,
					   invoice_no = ?,
					   invoice_date = ?,
					   csr_no = ?,
					   csr_or_no = ?,
					   lot_no = ?,
					   csr_date = ?,
					   net_amount = ?,
					   vat_amount = ?,
					   customer_id = ?,
					   customer_id_2 = ?,
					   customer_name = ?,
					   customer_name_2 = ?,
					   last_update = ?
					   WHERE cs_no = ?";
	
		$this->mysqli->query($sql,$params);
	}
	
	public function get_last_update(){
		
		$sql = "SELECT ifs_vehicle_info_last_update last_update FROM maintenance LIMIT 1";
		$data = $this->mysqli->query($sql);
		return $data->result();
	}
	
	public function update_last_update($last_update){
		
		$sql = "UPDATE maintenance SET ifs_vehicle_info_last_update = ?";
		$this->mysqli->query($sql,$last_update);
	}
	
	public function search_ifs_vehicle_info($q = NULL){
		
		$q = ($q == NULL)? 1:$q;
		
		$sql = "SELECT prod_order_no,
					   serial_no,
					   cs_no,
					   prod_model,
					   sales_model,
					   buyoff_date,
					   fm_date,
					   body_color,
					   vin,
					   body_no,
					   engine_type,
					   engine_no,
					   key_no,
					   wb_no,
					   aircon_brand,
					   aircon_no,
					   stereo_no,
					   stereo_brand,
					   sales_order,
					   tagged_date,
					   payment_date,
					   pullout_date,
					   invoice_no,
					   invoice_date,
					   csr_no,
					   csr_or_no,
					   lot_no,
					   csr_date,
					   net_amount,
					   vat_amount,
					   customer_id,
					   customer_id_2,
					   customer_name,
					   customer_name_2,
					   '' fleet_name,
					   last_update,
					   'IFS' source
					FROM ifs_vehicle_info
					WHERE cs_no = ? or vin = ?
					LIMIT 1";
		
		$data = $this->mysqli->query($sql, array($q, $q));
		if($data->num_rows() > 0){
			return $data->result();
		}
		else{
			return FALSE;
		}
	}
	
	public function search_tagged($q){
		
		$sql = "SELECT msn.serial_number                         cs_number,
					   msn.attribute2                            chassis_number,
					   msib.segment1                             prod_model,
					   msib.description                          prod_model_desc,
					   msib.attribute9  sales_model,
					   msib.attribute8                           body_color,
					   msn.attribute4                            body_number,
					   msn.lot_number,
					   we.wip_entity_name                        shop_order_number,
					   mis.serial_no                             serial_number,
					   msib.attribute11 || ' / ' || msn.attribute3 engine,
					   msib.attribute19 || ' / ' || msn.attribute7 aircon,
					   msib.attribute20 || ' / ' || msn.attribute9 stereo,
					   msn.attribute6                            key_no,
					   msn.attribute5                            buyoff_date,
					   msn.attribute11                           fm_date,
					   msn.attribute1                            csr_number,
					   msn.attribute12                           csr_or_number,
					   msn.attribute14                           csr_date,
					   msn.d_attribute20                        tagged_date,
					   msn.attribute15                           mr_date,
					   ottl.description order_type,
					   ooha.order_number,
					   ooha.ordered_date,
					   hp.party_name,
					   hcaa.account_name,
					   ooha.attribute3 fleet_name,
					   '' trx_number,
					   '' trx_date,
					   '' pullout_date,
					   '' wb_number,
					   hcaa.cust_account_id customer_id,
					   mt.organization_code,
						   msn.current_subinventory_code,
						   'Oracle' source,
						'-' status,
						'-' paid_date,
						CASE 
						    WHEN ooha.booked_flag = 'N' THEN 'Entered'
						    WHEN wdd.released_status = 'R' AND ooha.booked_flag = 'Y' AND hold.released_flag = 'N' THEN 'Booked / Credit Hold'
						    WHEN wdd.released_status = 'R' AND ooha.booked_flag = 'Y' AND hold.released_flag = 'Y' THEN 'Booked / Credit Hold Released'
						    WHEN wdd.released_status = 'R' AND ooha.booked_flag = 'Y' AND hold.released_flag IS NULL THEN 'Booked / Credit Hold Not Applied'
							WHEN wdd.released_status = 'S' THEN 'Released to Warehouse'
							WHEN wdd.released_status = 'Y' THEN 'Staged'
							WHEN wdd.released_status = 'C' THEN 'Shipped'
							ELSE NULL
						END current_status,
						CASE 
						    WHEN ooha.booked_flag = 'N' THEN 'Book Order'
						    WHEN wdd.released_status = 'R' AND ooha.booked_flag = 'Y' AND hold.released_flag = 'N' THEN 'Release Credit Hold'
						    WHEN wdd.released_status = 'R' AND ooha.booked_flag = 'Y' AND hold.released_flag = 'Y' THEN 'Launch Pick Release'
						    WHEN wdd.released_status = 'R' AND ooha.booked_flag = 'Y' AND hold.released_flag IS NULL THEN 'Launch Pick Release'
							WHEN wdd.released_status = 'S' THEN 'Transact Move Order / Allocate and Transact'
							WHEN wdd.released_status = 'Y' THEN 'Ship Confirm'
							WHEN wdd.released_status = 'C' THEN 'Invoice'
							ELSE NULL
						END next_step
				  FROM mtl_serial_numbers msn
					   LEFT JOIN mtl_system_items_b msib
						  ON msn.inventory_item_id = msib.inventory_item_id
					      AND msn.current_organization_id = msib.organization_id
					   LEFT JOIN mtl_parameters mt
						  ON msib.organization_id = mt.organization_id
					   LEFT JOIN mtl_reservations res
						  ON msn.reservation_id = res.reservation_id
					   LEFT JOIN oe_order_lines_all oola
						  ON res.demand_source_line_id = oola.line_id
					   LEFT JOIN oe_order_headers_all ooha 
					      ON oola.header_id = ooha.header_id
					    LEFT JOIN oe_order_holds_all hold
						  ON oola.header_id = hold.header_id
						AND oola.line_id = hold.line_id
					   LEFT JOIN oe_transaction_types_tl ottl
						ON ooha.order_type_id = ottl.transaction_type_id
					   LEFT JOIN WSH_DELIVERY_DETAILS WDD
						ON oola.line_id = wdd.SOURCE_LINE_ID
						LEFT JOIN wsh_delivery_assignments wda
						ON wdd.DELIVERY_DETAIL_ID = wda.DELIVERY_DETAIL_ID
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON oola.sold_to_org_id = hcaa.cust_account_id
					   LEFT JOIN hz_parties hp 
					      ON hcaa.party_id = hp.party_id
					   LEFT JOIN xxxipc_mis mis 
						  ON msn.serial_number = mis.cs_no
					   LEFT JOIN wip_entities we
						  ON msn.original_wip_entity_id = we.wip_entity_id
				 WHERE     1 = 1
					   AND mt.organization_code = 'IVS'
					   AND msn.current_subinventory_code = 'VSS'
					   AND msn.reservation_id IS NOT NULL
					   AND msn.c_attribute30 IS NULL
					   AND (msn.serial_number = ? OR msn.attribute2 = ?)";
					   
		$data = $this->oracle->query($sql, array($q,$q));
		if($data->num_rows() > 0){
			return $data->result();
		}
		else{
			return FALSE;
		}
	}
	
	public function search_serial_numbers($q = NULL){
		
		$q = ($q == NULL)? 1:$q;
		
		$sql = "SELECT msn.serial_number                         cs_number,
					   msn.attribute2                            chassis_number,
					   msib.segment1                             prod_model,
					   msib.description                          prod_model_desc,
					   msib.attribute9 sales_model,
					   msib.attribute8                           body_color,
					   msn.attribute4                            body_number,
					   ooha.attribute3 fleet_name,
					   msn.lot_number,
					   we.wip_entity_name                             shop_order_number,
					   mis.serial_no                                         serial_number,
					   msib.attribute11 || ' / ' || msn.attribute3 engine,
					   msib.attribute19 || ' / ' || msn.attribute7 aircon,
					   msib.attribute20 || ' / ' || msn.attribute9 stereo,
					   msn.attribute6                            key_no,
					   msn.attribute5                            buyoff_date,
					   msn.attribute11                           fm_date,
					   msn.attribute15                           mr_date,
					   msn.attribute1                            csr_number,
					   msn.attribute12                           csr_or_number,
					   msn.attribute14                           csr_date,
					   msn.d_attribute20                         tagged_date,
					   ooha.order_number,
					   ooha.ordered_date,
					   rcta.trx_number,
					   rcta.trx_date,
					   rcta.attribute5 pullout_date,
						rcta.attribute4 wb_number,
						ottl.description order_type,
					  hp.party_name,
					   hcaa.account_name,
					   hcaa.cust_account_id customer_id,
					   mt.organization_code,
						   msn.current_subinventory_code,
						   'Oracle' source,
						   CASE WHEN NVL(araa.amount_applied, 0) + 1 >= round((rctla.net_amount - rctla.net_amount * 0.01),2) + rctla.vat_amount THEN 'Paid'
					   ELSE 'Unpaid'
					   END status,
					   to_char(araa.apply_date,'MM/DD/YYYY') paid_date,
					  CASE 
							WHEN rcta.trx_number is not null THEN 'Invoiced'
						    WHEN wdd.released_status = 'C' THEN 'Shipped'
							ELSE NULL
						END current_status,
						CASE 
						   WHEN rcta.trx_number is not null THEN '-'
						    WHEN wdd.released_status = 'C' THEN 'Invoice'
							ELSE NULL
						END next_step
				  FROM mtl_serial_numbers msn
						 LEFT JOIN mtl_system_items_b msib
								ON msn.inventory_item_id = msib.inventory_item_id
								and msn.current_organization_id = msib.organization_id
						   LEFT JOIN mtl_parameters mt
						  ON msib.organization_id = mt.organization_id
					   LEFT JOIN (SELECT mmts.*
								  FROM mtl_material_transactions mmts
									   LEFT JOIN mtl_transaction_types mtt
										  ON mmts.transaction_type_id = mtt.transaction_type_id
								 WHERE  1 = 1 and mtt.transaction_type_name IN ('Sales order issue', 'Sales Order Pick')
								 ) mmt
						   ON msn.last_transaction_id = mmt.transaction_id
						LEFT JOIN oe_order_lines_all oola
						   ON mmt.trx_source_line_id = oola.line_id
					   LEFT JOIN oe_order_headers_all ooha
						   ON oola.header_id = ooha.header_id
					   LEFT JOIN oe_transaction_types_tl ottl
						   ON ooha.order_type_id = ottl.transaction_type_id
					   LEFT JOIN wsh_delivery_details wdd
						   ON oola.line_id = wdd.source_line_id
					   LEFT JOIN wsh_delivery_assignments wda
						   ON wdd.delivery_detail_id = wda.delivery_detail_id
						LEFT JOIN wsh_new_deliveries wnd
						 ON wda.delivery_id = wnd.delivery_id
						 LEFT JOIN hz_cust_accounts_all hcaa
						  ON oola.sold_to_org_id = hcaa.cust_account_id
					   LEFT JOIN hz_parties hp 
						  ON hcaa.party_id = hp.party_id
					   LEFT JOIN xxxipc_mis mis 
						  ON msn.serial_number = mis.cs_no
					   LEFT JOIN wip_entities we
						  ON msn.original_wip_entity_id = we.wip_entity_id
					   LEFT JOIN ra_customer_trx_all rcta
						  ON ooha.order_number = rcta.interface_header_attribute1
						  AND wnd.delivery_id = rcta.interface_header_attribute3
						LEFT JOIN (SELECT customer_trx_id,
										 MAX(warehouse_id) warehouse_id,
										 MAX(inventory_item_id) inventory_item_id,
										 MAX(quantity_invoiced) quantity_invoiced,
										 MAX (INTERFACE_LINE_ATTRIBUTE2) order_type,
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
				 WHERE 1 = 1 
					AND msn.c_attribute30 IS NULL
					AND msib.item_type = 'FG'
					AND (msn.serial_number = ? OR msn.attribute2 = ? OR msn.attribute3 = ?)";
							
		$data = $this->oracle->query($sql, array($q,$q,$q));
		if($data->num_rows() > 0){
			return $data->result();
		}
		else{
			return FALSE;
		}
	}
	
	public function view_history_log()
	{
		$sql = "SELECT
					CS_NUMBER,
					STATUS,
					DESCRIPTION,
					TO_CHAR(DATE_LOG,'mm/dd/YYYY') DATE_LOG
				FROM 
					IPC.IPC_PDI_HISTORY";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
}
