<?php

class Crms_Model extends CI_Model {
	
	private $crms_db = NULL;
	private $oracle = NULL;

	public function __construct(){
		
		parent::__construct();
		$this->crms_db = $this->load->database('crms_db',true);
		$this->oracle = $this->load->database('oracle',true);
	}

	public function getVehicleDetails()
	{

		$sql = "SELECT 
				CASE WHEN msn.attribute2 IS NULL THEN '0' ELSE msn.attribute2 END vin,
				CASE WHEN msn.attribute3 IS NULL THEN '0' ELSE msn.attribute3 END engine_no, 
				CASE WHEN msn.serial_number IS NULL THEN '0' ELSE msn.serial_number END cs_no,
				msib.attribute11 engine_type,
				msn.attribute4 body_no,
				msn.lot_number lot_no,
				msn.attribute6 key_no,
				msib.attribute21 year,
				CASE WHEN msib.attribute9 IS NULL THEN '0' ELSE msib.attribute9 END  model,
				msib.segment1 prod_model,
				msib.attribute8 body_color, 
				CASE WHEN msib.attribute16 IS NULL THEN '0' ELSE msib.attribute16 END  cc,
				CASE WHEN msib.attribute18 IS NULL THEN '0' ELSE msib.attribute18 END  cylinder,
				CASE WHEN msib.attribute14 IS NULL THEN '0' ELSE msib.attribute14 END  max_gvw,
				msib.attribute17 fuel_type,
				msib.attribute19 aircon,
				msib.attribute13 tire_brand_size,
				msib.attribute20 stereo,
				msib.attribute12 battery,
				msib.attribute14 weight,
									CASE
									          WHEN REGEXP_LIKE
									                     (msn.attribute5,
									                      '^[0-9]{2}-\w{3}-[0-9]{2}$'             --     DD-MON-YY
									                     )
									             THEN TO_CHAR (msn.attribute5, 'YYYY-MM-DD')
									          WHEN REGEXP_LIKE (msn.attribute5,
									                            '^[0-9]{2}-\w{3}-[0-9]{4}$'         -- DD-MON-YYYY
									                           )
									             THEN TO_CHAR (msn.attribute5, 'YYYY-MM-DD')
									          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}'
									                                                                         -- YYYY/MM/DD
									              )
									             THEN TO_CHAR (TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS'),
									                           'YYYY-MM-DD'
									                          )
									          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}/[0-9]{2}/[0-9]{4}'
									                                                                         -- MM/DD/YYYY
									              )
									             THEN TO_CHAR (TO_DATE (msn.attribute5, 'MM/DD/YYYY'),
									                           'YYYY-MM-DD'
									                          )
									          ELSE TO_CHAR (TO_DATE (msn.attribute5, 'MM/DD/YYYY'), 'YYYY-MM-DD')
									       END production_date,

					msn.attribute1 csr_no, msn.attribute12 or_no,
					       CASE
					          WHEN REGEXP_LIKE
					                        (msn.attribute14,
					                         '^[0-9]{2}-\w{3}-[0-9]{2}$'          --     DD-MON-YY
					                        )
					             THEN TO_CHAR (msn.attribute14, 'YYYY-MM-DD')
					          WHEN REGEXP_LIKE (msn.attribute14,
					                            '^[0-9]{2}-\w{3}-[0-9]{4}$'         -- DD-MON-YYYY
					                           )
					             THEN TO_CHAR (msn.attribute14, 'YYYY-MM-DD')
					          WHEN REGEXP_LIKE (msn.attribute14, '^[0-9]{4}/[0-9]{2}/[0-9]{2}'
					                                                                          -- YYYY/MM/DD
					              )
					             THEN TO_CHAR (TO_DATE (msn.attribute14, 'YYYY/MM/DD HH24:MI:SS'),
					                           'YYYY-MM-DD'
					                          )
					          WHEN REGEXP_LIKE (msn.attribute14, '^[0-9]{2}/[0-9]{2}/[0-9]{4}'
					                                                                          -- MM/DD/YYYY
					              )
					             THEN TO_CHAR (TO_DATE (msn.attribute14, 'MM/DD/YYYY'),
					                           'YYYY-MM-DD'
					                          )
					          ELSE NULL
					       END csr_date,
					       CASE
					          WHEN REGEXP_LIKE (rcta.attribute5,
					                            '^[0-9]{2}-\w{3}-[0-9]{2}$'
					                           )
					             THEN TO_CHAR (rcta.attribute5, 'YYYY-MM-DD')
					          WHEN REGEXP_LIKE (rcta.attribute5, '^[0-9]{2}-\w{3}-[0-9]{4}$')
					             THEN TO_CHAR (rcta.attribute5, 'YYYY-MM-DD')
					          WHEN REGEXP_LIKE (rcta.attribute5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}')
					             THEN TO_CHAR (TO_DATE (rcta.attribute5, 'YYYY/MM/DD HH24:MI:SS'),
					                           'YYYY-MM-DD'
					                          )
					          ELSE NULL
					       END AS pullout_date,
						    rcta.attribute4 wb_no,
					       rcta.bill_to_customer_id pullout_dealer_id, 
						   rcta.trx_number invoice_no,
					       TO_CHAR (rcta.trx_date, 'YYYY-MM-DD') invoice_date,
					       CASE WHEN rctlgda.amount IS NULL THEN 0 ELSE rctlgda.amount END invoice_price,
					       CASE
					          WHEN REGEXP_LIKE (msn.attribute15,
					                            '^[0-9]{2}-\w{3}-[0-9]{2}$'
					                           )
					             THEN TO_CHAR (msn.attribute15, 'YYYY-MM-DD')
					          WHEN REGEXP_LIKE (msn.attribute15, '^[0-9]{2}-\w{3}-[0-9]{4}$')
					             THEN TO_CHAR (msn.attribute15, 'YYYY-MM-DD')
					          WHEN REGEXP_LIKE (msn.attribute15, '^[0-9]{4}/[0-9]{2}/[0-9]{2}')
					             THEN TO_CHAR (TO_DATE (msn.attribute15, 'YYYY/MM/DD HH24:MI:SS'),
					                           'YYYY-MM-DD'
					                          )
					          ELSE NULL
					       END mr_process_date,
					       msib.attribute9 catalog_desc,
						   ooha.order_number order_no,
						   CASE WHEN ooha.order_type_id = '1124' THEN 'FS' WHEN ooha.order_type_id IN ('1121','1122','1043') THEN 'VS' ELSE NULL END order_code
					  FROM mtl_system_items_b msib
					  LEFT JOIN mtl_serial_numbers msn
					  ON msn.inventory_item_id = msib.inventory_item_id
					   AND msn.current_organization_id = msib.organization_id
					  LEFT JOIN ra_customer_trx_all rcta
					       ON msn.serial_number = rcta.attribute3
					       LEFT JOIN ipc_ar_invoices_with_cm cm
					       ON rcta.customer_trx_id = cm.orig_trx_id
					       LEFT JOIN ra_cust_trx_line_gl_dist_all rctlgda
					       ON rcta.customer_trx_id = rctlgda.customer_trx_id
					     AND rctlgda.account_class = 'REC'
						left join oe_order_headers_all ooha
						 on rcta.INTERFACE_HEADER_ATTRIBUTE1 = ooha.order_number
					WHERE  1 = 1
					   AND msib.item_type = 'FG'
					   AND msn.c_attribute30 IS NULL
					   AND (   (msn.current_organization_id = 88 AND msn.current_status != 4)
					        OR msn.current_organization_id != 88
					       )
					   AND rcta.previous_customer_trx_id IS NULL
					   AND cm.orig_trx_id IS NULL
					   AND msn.current_status IN ('3', '4')
					   AND (   rcta.trx_number NOT IN ('40300001798', '40300013052')
					        OR rcta.trx_number IS NULL
					       )
					    AND msn.current_organization_id NOT IN (88,141)";

		$data = $this->oracle->query($sql);
  		return $data->result();
	}

	public function insert_oracle_data_to_mysql_crms($data){

    	$sql = $this->crms_db->insert('t_crm_vehicle_oracle', $data);
        return true;
	}

	public function insert_new_vehicle($data){
		
    	$sql = $this->crms_db->insert('t_crm_vehicle', $data);
        return true;
	}

	public function get_new_units()
	{
		$sql = "SELECT 
					vin,
					engine_no,
					cs_no,
					engine_type,
					body_no,
					lot_no,
					key_no,
					YEAR,
					model,
					prod_model,
					body_color,
					cc,
					cylinder,
					max_gvw,
					fuel_type,
					aircon,
					tire_brand_size,
					stereo,
					battery,
					weight,
					invoice_price,
					catalog_desc,
					production_date
				FROM
					  t_crm_vehicle_oracle tcvo 
				WHERE 1=1
					AND tcvo.cs_no NOT IN (SELECT cs_no FROM t_crm_vehicle)
				ORDER BY production_date DESC";

		$data = $this->crms_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_invoice()
	{
		$sql = "SELECT 
					tcvo.cs_no,
					tcvo.invoice_no,
					tcvo.invoice_date,
					tcvo.invoice_price,
					tcvo.wb_no
				FROM t_crm_vehicle tcv
				LEFT JOIN t_crm_vehicle_oracle tcvo
					ON tcv.cs_no = tcvo.cs_no 
				WHERE 1=1
					AND tcv.invoice_no IS NULL
					AND tcvo.invoice_no IS NOT NULL";

		$data = $this->crms_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_csr()
	{
		$sql = "SELECT 
					tcvo.cs_no,
					tcvo.csr_no,
					tcvo.csr_date,
					tcvo.or_no,
					DATE_FORMAT(tcvo.mr_process_date,'%Y-%m-%d') mr_process_date
				FROM t_crm_vehicle tcv
				LEFT JOIN t_crm_vehicle_oracle tcvo
					ON tcv.cs_no = tcvo.cs_no 
				WHERE 1 = 1 
					AND tcv.csr_no IS NULL 
					AND tcvo.csr_no IS NOT NULL";

		$data = $this->crms_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_so()
	{
		$sql = "SELECT 
				  tcvo.cs_no,
				  tcvo.order_no,
				  CASE WHEN tccio.dealer_code IS NULL THEN tcvo.pullout_dealer_id ELSE tccio.dealer_code END pullout_dealer_id,
				  tcvo.order_code
				FROM
				  t_crm_vehicle tcv 
				  LEFT JOIN t_crm_vehicle_oracle tcvo 
				    ON tcv.cs_no = tcvo.cs_no 
				  LEFT JOIN t_crm_customer_ifs_oracle tccio
				    ON tcvo.pullout_dealer_id = tccio.account_code
				WHERE 1 = 1 
				  AND tcv.order_no IS NULL 
				  AND tcv.pullout_dealer_id IS NULL 
				  AND tcv.order_code IS NULL 
				  AND tcvo.order_no IS NOT NULL 
				  AND tcvo.pullout_dealer_id IS NOT NULL 
				  AND tcvo.order_code IS NOT NULL";

		$data = $this->crms_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_pullout()
	{
		$sql = "SELECT 
				  tcvo.cs_no,
				  DATE_FORMAT(tcvo.pullout_date,'%Y-%m-%d') pullout_date
				FROM
				  t_crm_vehicle tcv 
				  LEFT JOIN t_crm_vehicle_oracle tcvo 
				    ON tcv.cs_no = tcvo.cs_no 
				WHERE 1 = 1 
				  AND tcv.pullout_date IS NULL 
				  AND tcvo.pullout_date IS NOT NULL ";

		$data = $this->crms_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function updateInvoice()
	{
		$sql = "UPDATE t_crm_vehicle tcv 
				LEFT JOIN t_crm_vehicle_oracle tcvo 
					ON tcv.cs_no = tcvo.cs_no 
				SET 
					tcv.invoice_no = tcvo.invoice_no,
					tcv.invoice_date = tcvo.invoice_date,
					tcv.wb_no = tcvo.wb_no
				WHERE 1 = 1 
				  	AND tcv.invoice_no IS NULL
				  	AND tcvo.invoice_no IS NOT NULL";

		$return = $this->crms_db->query($sql);
		return $return;
	}

	public function updateSO()
	{
		$sql = "UPDATE t_crm_vehicle tcv 
				LEFT JOIN t_crm_vehicle_oracle tcvo 
					ON tcv.cs_no = tcvo.cs_no 
				LEFT JOIN t_crm_customer_ifs_oracle tccio
				    ON tcvo.pullout_dealer_id = tccio.account_code
				SET 
					tcv.order_no = tcvo.order_no,
					tcv.pullout_dealer_id = CASE WHEN tccio.dealer_code IS NULL THEN tcvo.pullout_dealer_id ELSE tccio.dealer_code END,
					tcv.order_code = tcvo.order_code
				WHERE 1 = 1 
				  	AND tcv.order_no IS NULL 
				  	AND tcv.pullout_dealer_id IS NULL 
				  	AND tcv.order_code IS NULL 
				  	AND tcvo.order_no IS NOT NULL 
				  	AND tcvo.pullout_dealer_id IS NOT NULL 
				  	AND tcvo.order_code IS NOT NULL";

		$return = $this->crms_db->query($sql);
		return $return;
	}

	public function updateCSR()
	{
		$sql = "UPDATE t_crm_vehicle tcv 
				LEFT JOIN t_crm_vehicle_oracle tcvo 
					ON tcv.cs_no = tcvo.cs_no 
				SET 
					tcv.csr_no = tcvo.csr_no,
				 	tcv.csr_date = tcvo.csr_date,
				 	tcv.or_no = tcvo.or_no,
				 	tcv.mr_process_date = tcvo.mr_process_date
				WHERE 1 = 1 
				  	AND tcv.csr_no IS NULL 
				  	AND tcvo.csr_no IS NOT NULL";
		$return = $this->crms_db->query($sql);
		return $return;
	}

	public function updatePullout()
	{
		$sql = "UPDATE t_crm_vehicle tcv 
				LEFT JOIN t_crm_vehicle_oracle tcvo 
				    ON tcv.cs_no = tcvo.cs_no 
				SET tcv.pullout_date = DATE_FORMAT(tcvo.pullout_date,'%Y-%m-%d')
				WHERE 1 = 1 
				  AND tcv.pullout_date IS NULL 
				  AND tcvo.pullout_date IS NOT NULL";
				  
		$return = $this->crms_db->query($sql);
		return $return;
	}

	public function updateWB()
	{
		$sql = "UPDATE
				  t_crm_vehicle tcv 
				  LEFT JOIN t_crm_vehicle_oracle tcvo 
				    ON tcv.cs_no = tcvo.cs_no 
				SET tcv.wb_no = tcvo.wb_no
				WHERE 1 = 1 
				  AND tcv.wb_no IS NULL 
				  AND tcvo.wb_no IS NOT NULL";
				  
		$return = $this->crms_db->query($sql);
		return $return;
	}

	public function truncate_t_crm_vehicle_oracle()
    {
        $this->crms_db->truncate('t_crm_vehicle_oracle');
        return true;
    }


}
