<?php

class Cbu_model extends CI_Model {
	
	private $mysqli = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle',true);
	}
	
	public function get_search_cbu_cs_nos($cs_nos){
		
		$sql = "SELECT rcta.customer_trx_id, 
						rcta.trx_number, 
						rcta.trx_date, 
						rcta.attribute3 cs_no, 
						rcta.attribute5 pullout_date,
						hp.party_name,
						 msib.attribute9 sales_model,
						 msib.attribute8 body_color,
						 msn.lot_number,
				   hcaa.account_name
				FROM ra_customer_trx_all rcta
				  LEFT JOIN ipc_ar_invoices_with_cm cm 
					  ON rcta.customer_trx_id = cm.orig_trx_id
				LEFT JOIN hz_cust_accounts_all hcaa
					  ON rcta.sold_to_customer_id = hcaa.cust_account_id
				   LEFT JOIN hz_parties hp 
					  ON hcaa.party_id = hp.party_id
				LEFT JOIN mtl_serial_numbers msn
					  ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
					  ON msn.inventory_item_id = msib.inventory_item_id
					  AND msn.current_organization_id = msib.organization_id
				WHERE 1 = 1
				and cm.orig_trx_id IS NULL
				 AND rcta.cust_trx_type_id = 1002
				  AND msn.c_attribute30 IS NULL
				  AND (msib.segment1 like '%UCR%' OR msib.segment1 like '%UCS%')
				AND rcta.attribute3 in (".$cs_nos.")";
		//~ echo $sql;
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_cbu_unpulledout(){
		
		$sql = "SELECT rcta.customer_trx_id, 
						rcta.trx_number, 
						rcta.trx_date, 
						rcta.attribute3 cs_number, 
						rcta.attribute5 pullout_date,
						hp.party_name,
						 msib.attribute9 sales_model,
						 msib.attribute8 body_color,
				   hcaa.account_name
				FROM ra_customer_trx_all rcta
				  LEFT JOIN ipc_ar_invoices_with_cm cm 
					  ON rcta.customer_trx_id = cm.orig_trx_id
				LEFT JOIN hz_cust_accounts_all hcaa
					  ON rcta.sold_to_customer_id = hcaa.cust_account_id
				   LEFT JOIN hz_parties hp 
					  ON hcaa.party_id = hp.party_id
				LEFT JOIN mtl_serial_numbers msn
					  ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
					  ON msn.inventory_item_id = msib.inventory_item_id
					  AND msn.current_organization_id = msib.organization_id
				WHERE 1 = 1
				and cm.orig_trx_id IS NULL
				 AND rcta.cust_trx_type_id = 1002
				  AND (msib.segment1 like '%UCR%' OR msib.segment1 like '%UCS%')
				 AND rcta.attribute5 IS NULL
				  AND msn.c_attribute30 IS NULL
				 ORDER BY hp.party_name, hcaa.account_name";
		//~ echo $sql;
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_cbu_pulledout($from_date = NULL, $to_date = NULL){
		
		$from_date = ($from_date == NULL)? date('01-M-y'):date('d-M-y', strtotime($from_date));
		$to_date = ($to_date == NULL)? date('d-M-y'):date('d-M-y', strtotime($to_date));
		
		$sql = "SELECT rcta.customer_trx_id, 
						rcta.trx_number, 
						rcta.trx_date, 
						rcta.attribute3 cs_number, 
						rcta.attribute5 pullout_date,
						hp.party_name,
						 msib.attribute9 sales_model,
						 msib.attribute8 body_color,
				   hcaa.account_name
				FROM ra_customer_trx_all rcta
				  LEFT JOIN ipc_ar_invoices_with_cm cm 
					  ON rcta.customer_trx_id = cm.orig_trx_id
				LEFT JOIN hz_cust_accounts_all hcaa
					  ON rcta.sold_to_customer_id = hcaa.cust_account_id
				   LEFT JOIN hz_parties hp 
					  ON hcaa.party_id = hp.party_id
				LEFT JOIN mtl_serial_numbers msn
					  ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
					  ON msn.inventory_item_id = msib.inventory_item_id
					  AND msn.current_organization_id = msib.organization_id
				WHERE 1 = 1
				and cm.orig_trx_id IS NULL
				 AND rcta.cust_trx_type_id = 1002
				  AND msn.c_attribute30 IS NULL
				  AND rcta.attribute5 is not null
				  AND (msib.segment1 like '%UCR%' OR msib.segment1 like '%UCS%')
				  AND TO_DATE(replace(rcta.attribute5,' ',''),'YYYY/MM/DD HH24:MI:SS') BETWEEN ? AND ?
				  ORDER BY hp.party_name, hcaa.account_name";
		//~ echo $sql;
		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result();
	}
}
