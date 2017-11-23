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
	
	public function search_result2($cs_no = NULL){
		
		$cs_no = ($cs_no == NULL)? 1:$cs_no;
		
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
					   last_update,
					   'IFS' source
					FROM ifs_vehicle_info
					WHERE cs_no = ? 
					LIMIT 1";
		
		$data = $this->mysqli->query($sql, $cs_no);
		if($data->num_rows() > 0){
			return $data->result();
		}
		else{
			return FALSE;
		}
	}
	
	public function search_tagged($cs_no){
		
		$sql = "SELECT msn.serial_number                         cs_number,
					   msn.attribute2                            chassis_number,
					   msib.segment1                             prod_model,
					   msib.description                          prod_model_desc,
					   msib.attribute9                           sales_model,
					   msib.attribute8                           body_color,
					   msn.attribute4                            body_number,
					   msn.lot_number,
					   we.wip_entity_name                        shop_order_number,
					   mis.serial_no                             serial_number,
					   msib.attribute11 || ' ' || msn.attribute3 engine_no,
					   msib.attribute19 || ' ' || msn.attribute7 aircon,
					   msib.attribute20 || ' ' || msn.attribute9 stereo,
					   msn.attribute6                            key_no,
					   msn.attribute5                            buyoff_date,
					   msn.attribute11                           fm_date,
					   msn.attribute1                            csr_number,
					   msn.attribute12                           csr_or_number,
					   msn.attribute14                           csr_date,
					   ooha.order_number,
					   ooha.ordered_date,
					   hp.party_name,
					   hcaa.account_name,
					   msn.current_organization_id
				  FROM mtl_serial_numbers msn
					   LEFT JOIN mtl_system_items_b msib
						  ON msn.inventory_item_id = msib.inventory_item_id
					      AND msn.current_organization_id = msib.organization_id
					   LEFT JOIN mtl_parameters mt
						  ON msib.organization_id = mt.organization_id
					   LEFT JOIN mtl_serial_numbers_temp msnt
						  ON msn.serial_number = msnt.to_serial_number
					   LEFT JOIN mtl_material_transactions_temp mmtt
						  ON msnt.group_header_id = mmtt.transaction_header_id
					   LEFT JOIN oe_order_lines_all oola
						  ON mmtt.trx_source_line_id = oola.line_id
					   LEFT JOIN oe_order_headers_all ooha 
					      ON oola.header_id = ooha.header_id
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
					   AND msnt.to_serial_number IS NOT NULL
					   AND msn.c_attribute30 IS NULL
					   AND msn.serial_number = ?";
					   
		$data = $this->oracle->query($sql, $cs_no);
		if($data->num_rows() > 0){
			return $data->result();
		}
		else{
			return FALSE;
		}
	}
	
	public function search_result($cs_no = NULL){
		
		$cs_no = ($cs_no == NULL)? 1:$cs_no;
		
		$sql = "SELECT msib.inventory_item_id,
					   msn.current_subinventory_code subinventory_code,
					   msn.serial_number cs_no,
					   msib.segment1 prod_model,
					   msn.attribute2 vin,
					   msn.attribute5 buyoff_date,
					   msn.attribute14 csr_date,
					   tab1.order_date,
					   tab1.order_number sales_order,
					   tab1.allocation_date tagged_date,
					   tab1.shipment_date,
					   msn.attribute1 csr_no,
					   msn.attribute12 csr_or_no,
					   tab1.party_name customer_name,
					   tab1.account_name,
					   tab1.account_number customer_id,
					   msn.lot_number lot_no,
					   msn.attribute4 body_no,
					   msn.attribute3 engine_no,
					   msib.attribute11 engine_type,
					   msn.attribute6 key_no,
					   msib.attribute8 body_color,
					   SUBSTR (msn.attribute7, 1, INSTR (msn.attribute7, '/') - 1) aircon_no,
					   SUBSTR (msn.attribute7, -INSTR (reverse (msn.attribute7), '/') + 1) aircon_brand,
					   SUBSTR (msn.attribute9, 1, INSTR (msn.attribute9, '/') - 1) stereo_no,
					   SUBSTR (msn.attribute9, -INSTR (reverse (msn.attribute9), '/') + 1) stereo_brand,
					   SUBSTR (msn.attribute11, -INSTR (reverse (msn.attribute11), '/') + 1) fm_date,
					   msib.item_type,
					   rcta.trx_number invoice_no,
					   rcta.trx_date invoice_date,
					   rcta.attribute4 wb_no,
					   rcta.attribute5 pullout_date,
					   rctla.net_amount,
					   rctla.vat_amount,
					   'ORACLE' source,
					   NULL sales_model,
					   NULL prod_order_no,
					   NULL serial_no,
					   NULL payment_date
				  FROM mtl_serial_numbers msn
					   LEFT JOIN
						  mtl_system_items_b msib
					   ON msn.inventory_item_id = msib.inventory_item_id
						  AND msn.current_organization_id = msib.organization_id
						 LEFT JOIN (
									SELECT 
									   mut.serial_number,
									   wdd.released_status,
									   mmt.creation_date allocation_date,
									   so.ordered_date order_date,
									   wnd.confirm_date shipment_date,
									   so.order_number,
									   wnd.delivery_id,
										hp.party_name,
										 hcaa.account_name,
										 hcaa.account_number
								   FROM   mtl_unit_transactions mut
							   LEFT JOIN mtl_transaction_lot_numbers mtln
								  ON mtln.serial_transaction_id = mut.transaction_id
							   LEFT JOIN mtl_material_transactions mmt
								  ON mmt.transaction_id = mtln.transaction_id
							   LEFT JOIN (SELECT oola.line_id,
												 ooha.ordered_date,
												 oola.line_id so_line_id,
												 ooha.order_number
											FROM oe_order_headers_all ooha, oe_order_lines_all oola
										   WHERE ooha.header_id = oola.header_id
										   AND oola.INVOICE_INTERFACE_STATUS_CODE != 'NOT_ELIGIBLE'
											AND oola.FLOW_STATUS_CODE != 'CANCELLED') so
								  ON mmt.trx_source_line_id = so.line_id
							   LEFT JOIN wsh_delivery_details wdd
								  ON wdd.source_line_id = so.so_line_id
							   LEFT JOIN wsh_delivery_assignments wda
								  ON wdd.delivery_detail_id = wda.delivery_detail_id
							   LEFT JOIN WSH_NEW_DELIVERIES wnd
								  ON wnd.delivery_id = wda.delivery_id
								 LEFT JOIN hz_cust_accounts_all hcaa
											ON wdd.CUSTOMER_ID = hcaa.CUST_ACCOUNT_ID
										 LEFT JOIN hz_parties hp
											ON hcaa.party_id = hp.party_id
						 WHERE 1 = 1 
							 AND mmt.transaction_type_id = 52
								  AND mmt.subinventory_code = 'VSS'
								  AND wdd.released_status <> 'D'  ) tab1
							ON msn.serial_number = tab1.serial_number
						   LEFT JOIN ra_customer_trx_all rcta
							 ON tab1.delivery_id = rcta.interface_header_attribute3
						   LEFT JOIN (  SELECT customer_trx_id,
														 SUM (line_recoverable) net_amount,
														 SUM (tax_recoverable) vat_amount
													FROM ra_customer_trx_lines_all
												   WHERE 1 = 1 AND line_type = 'LINE'
												GROUP BY inventory_item_id, customer_trx_id) rctla
							  ON rcta.customer_trx_id = rctla.customer_trx_id
					   WHERE msib.item_type = 'FG'
					   AND msn.serial_number = ?";
		
		$data = $this->oracle->query($sql, $cs_no);
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
