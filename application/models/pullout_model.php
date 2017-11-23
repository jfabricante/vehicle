<?php

class Pullout_model extends CI_Model {
	
	private $mysqli = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle',true);
	}
	
	public function get_search_cs_nos($cs_nos){
		
		$sql = "SELECT rcta.customer_trx_id, 
						rcta.trx_number, 
						rcta.trx_date, 
						rcta.attribute3 cs_no, 
						rcta.attribute5 pullout_date,
						hp.party_name,
						msn.lot_number,
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
				AND msn.c_attribute30 is null
				AND rcta.cust_trx_type_id = 1002
				AND rcta.attribute3 in (".$cs_nos.")";
		//~ echo $sql;
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function update_pullout_date($cs_nos, $pullout_date){
		
		$sql = "UPDATE ra_customer_trx_all SET attribute5 = ?
				WHERE attribute3 in (".$cs_nos.")";
		$this->oracle->query($sql, $pullout_date);
	}

	public function pullout_report($from_date,$to_date)
	{
		$where = array($from_date,$to_date);

		$sql = "SELECT hp.party_name,
				       hcaa.account_name,
				       rcta.trx_number,
				       rcta.attribute3 cs_number,
				       CASE WHEN REGEXP_LIKE(rcta.attribute5 , '^[0-9]{2}-\w{3}-[0-9]{2}$') THEN TO_CHAR(rcta.attribute5 ,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(rcta.attribute5 , '^[0-9]{2}-\w{3}-[0-9]{4}$') THEN TO_CHAR(rcta.attribute5 ,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(rcta.attribute5 , '^[0-9]{4}/[0-9]{2}/[0-9]{2}') THEN TO_CHAR(TO_DATE(rcta.attribute5 ,'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY')
		                     ELSE NULL
			               END
			               AS pullout_date,
				       msib.attribute8 body_color,
				       msib.attribute9 sales_model,
				       msn.attribute2  chassis_number,
				       msn.attribute3  engine_no,
				       msn.attribute6  key_number
				FROM ra_customer_trx_all rcta
				       LEFT JOIN ipc_ar_invoices_with_cm cm
				          ON rcta.customer_trx_id = cm.orig_trx_id
				       LEFT JOIN mtl_serial_numbers msn
				          ON rcta.attribute3 = msn.serial_number
				       LEFT JOIN mtl_system_items_b msib
				          ON     msn.inventory_item_id = msib.inventory_item_id
				             AND msn.current_organization_id = msib.organization_id
				       LEFT JOIN hz_cust_accounts_all hcaa
				          ON rcta.sold_to_customer_id = hcaa.cust_account_id
				       LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
				WHERE     1 = 1
				       AND cm.orig_trx_id IS NULL
				       AND rcta.attribute5 IS NOT NULL
				       AND rcta.cust_trx_type_id = 1002
				       AND rcta.attribute3 IS NOT NULL
				       AND msn.c_attribute30 IS NULL
				       AND TO_DATE (rcta.attribute5, 'YYYY/MM/DD HH24:MI:SS') BETWEEN ? AND ? ";
		$data = $this->oracle->query($sql,$where);
		return $data->result_array();
	}
	
	
}
