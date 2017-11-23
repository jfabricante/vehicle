<?php

class Motorpool_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function report(){
		
		$sql = "SELECT msn.serial_number,
				       msib.attribute9 sales_model,
				       msn.attribute2                          vin,
				       msib.attribute8                         body_color,
				       msn.lot_number,
				       msn.attribute3                          engine_no,
				       msn.attribute6                          key_number,
				       msn.attribute1                          csr_number,
				       rcta.trx_number,
				       CASE WHEN REGEXP_LIKE(rcta.trx_date, '^[0-9]{2}-\w{3}-[0-9]{2}$') THEN TO_CHAR(rcta.trx_date,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(rcta.trx_date, '^[0-9]{2}-\w{3}-[0-9]{4}$') THEN TO_CHAR(rcta.trx_date,'MM/DD/YYYY')
		                     WHEN REGEXP_LIKE(rcta.trx_date, '^[0-9]{4}/[0-9]{2}/[0-9]{2}') THEN TO_CHAR(TO_DATE(rcta.trx_date,'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY')
		                     ELSE NULL
		               END
		               AS trx_date
				FROM mtl_serial_numbers msn
				       LEFT JOIN mtl_system_items_b msib
				          ON     msn.inventory_item_id = msib.inventory_item_id
				             AND msn.current_organization_id = msib.organization_id
				       LEFT JOIN ra_customer_trx_all rcta
				          ON msn.serial_number = rcta.attribute3
				       LEFT JOIN mtl_parameters mp
				          ON msn.current_organization_id = mp.organization_id
				       LEFT JOIN ipc_ar_invoices_with_cm cm
				           ON rcta.customer_trx_id = cm.orig_trx_id
				WHERE     1 = 1
				       AND msn.c_attribute30 IS NULL
				       AND mp.organization_code IN ('IVS')
				       AND rcta.attribute5 IS NULL
				       AND cm.orig_trx_id is null
					   ORDER BY serial_number";
			
		$data = $this->oracle->query($sql);
		return $data->result_array();
	}
	
}
