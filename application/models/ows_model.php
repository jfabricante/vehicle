<?php

class Ows_Model extends CI_Model {
	
	private $ows_db = NULL;
	private $oracle = NULL;

	public function __construct(){
		
		parent::__construct();
		$this->ows_db = $this->load->database('ows_db',true);
		$this->oracle = $this->load->database('oracle',true);
	}

	public function getVehicleDetails()
	{

		$sql = "SELECT 
                CASE WHEN msn.attribute2 IS NULL THEN '0' ELSE msn.attribute2 END vin,
                CASE WHEN msib.attribute9 IS NULL THEN '0' ELSE msib.attribute9 END  model,
                --NULL model_id,
                msib.description,
                msib.attribute8 body_color,
                rcta.attribute4 wb_no,
                msn.attribute4 body_no,
                CASE WHEN msn.attribute3 IS NULL THEN '0' ELSE msn.attribute3 END engine_no, 
                msib.attribute11 engine_type,
                CASE WHEN msib.attribute16 IS NULL THEN '0' ELSE msib.attribute16 END  cc,
                CASE WHEN msib.attribute18 IS NULL THEN '0' ELSE msib.attribute18 END  cylinder,
                CASE WHEN msib.attribute14 IS NULL THEN '0' ELSE msib.attribute14 END  max_gvw,
                msib.attribute17 fuel_type,
                CASE WHEN msn.serial_number IS NULL THEN '0' ELSE msn.serial_number END cs_no,
                msn.attribute6 key_no,
                msib.attribute19 ac_brand,
                msib.attribute20 stereo_brand,
                msib.attribute12 battery,
                msib.attribute13 tire_brand,
                
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
                           END AS ipc_pullout_date,
                --NULL tran_id,
                msn.attribute1 csr_no,
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
                    rcta.trx_number invoice_no,
                    TO_CHAR (rcta.trx_date, 'YYYY-MM-DD') invoice_date,
                    msn.attribute12 or_no,
                    rcta.bill_to_customer_id dealer_code,
                    --NULL dealer_id,
                    --0 rdr_flag,
                    --NULL product_family,
                    --NULL product_family_description,     
                    msn.lot_number lot_no,     
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
                    --NULL fleet_flag,
                    --NULL delivery_date,
                    --NULL vehicle_tran_id,
                    --NULL model_series,
                    --0 archived,
                    NULL entry_remarks
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
                        AND msn.current_organization_id NOT IN (88,141)
                        AND msn.serial_number != 'CS1887'";

		$data = $this->oracle->query($sql);
  		return $data->result();
	}

	public function insert_oracle_data_to_mysql_crms($data){

    	$sql = $this->ows_db->insert('t_ows_vehicle_oracle', $data);
        return true;
	}

	public function insert_new_vehicle($data){
		
    	$sql = $this->ows_db->insert('t_ows_vehicle', $data);
        return true;
	}

	public function get_new_units()
	{
		$sql = "SELECT 
                    tovo.vin,
                    tovo.engine_no,
                    tovo.cs_no,
                    tovo.engine_type,
                    tovo.body_no,
                    tovo.lot_no,
                    tovo.key_no,
                    tovo.model,
                    tovo.body_color,
                    tovo.cc,
                    tovo.cylinder,
                    tovo.max_gvw,
                    tovo.fuel_type,
                    tovo.ac_brand,
                    tovo.tire_brand,
                    tovo.stereo_brand,
                    tovo.battery,
                    tovo.production_date 
                FROM
                    t_ows_vehicle_oracle tovo
                WHERE 1 = 1 
                    AND TRIM(tovo.cs_no) NOT IN 
                    (SELECT TRIM(cs_no) FROM t_ows_vehicle 
                    WHERE cs_no != NULL OR cs_no != '')
                ORDER BY production_date DESC ";

		$data = $this->ows_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_invoice()
	{
		$sql = "SELECT 
                    tovo.cs_no,
                    tovo.invoice_no,
                    tovo.invoice_date,
                    tovo.wb_no
                FROM t_ows_vehicle tov
                LEFT JOIN t_ows_vehicle_oracle tovo
                    ON tov.cs_no = tovo.cs_no 
                WHERE 1=1
                    AND tov.invoice_no IS NULL
                    AND tovo.invoice_no IS NOT NULL";

		$data = $this->ows_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_csr()
	{
		$sql = "SELECT 
					tovo.cs_no,
					tovo.csr_no,
					tovo.csr_date,
					tovo.or_no,
					DATE_FORMAT(tovo.mr_process_date,'%Y-%m-%d') mr_process_date
				FROM t_ows_vehicle tov
				LEFT JOIN t_ows_vehicle_oracle tovo
					ON tov.cs_no = tovo.cs_no 
				WHERE 1 = 1 
					AND tov.csr_no IS NULL 
					AND tovo.csr_no IS NOT NULL";

		$data = $this->ows_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_so()
	{
		$sql = "SELECT tov.cs_no FROM t_ows_vehicle tov
                LEFT JOIN t_ows_vehicle_oracle tovo
                ON tov.cs_no = tovo.cs_no
                LEFT JOIN t_ows_customer_ifs_oracle tocio
                ON tovo.dealer_code = tocio.account_code
                WHERE 1=1
                AND tov.dealer_code IS NULL
                AND tovo.dealer_code IS NOT NULL";

		$data = $this->ows_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function get_new_pullout()
	{
		$sql = "SELECT 
                  tovo.cs_no,
                  DATE_FORMAT(tovo.ipc_pullout_date,'%Y-%m-%d') ipc_pullout_date
                FROM
                  t_ows_vehicle tov 
                  LEFT JOIN t_ows_vehicle_oracle tovo 
                    ON tov.cs_no = tovo.cs_no 
                WHERE 1 = 1 
                  AND tov.ipc_pullout_date IS NULL 
                  AND tovo.ipc_pullout_date IS NOT NULL";

		$data = $this->ows_db->query($sql);
  		$return =  $data->result();
		return $return;
	}

	public function updateInvoice()
	{
		$sql = "UPDATE t_ows_vehicle tov 
				LEFT JOIN t_ows_vehicle_oracle tovo 
					ON tov.cs_no = tovo.cs_no 
				SET 
					tov.invoice_no = tovo.invoice_no,
					tov.invoice_date = tovo.invoice_date,
					tov.wb_no = tovo.wb_no
				WHERE 1 = 1 
				  	AND tov.invoice_no IS NULL
				  	AND tovo.invoice_no IS NOT NULL";

		$return = $this->ows_db->query($sql);
		return $return;
	}

	public function updateSO()
	{
		$sql = "UPDATE t_ows_vehicle tov
                LEFT JOIN t_ows_vehicle_oracle tovo
                ON tov.cs_no = tovo.cs_no
                LEFT JOIN t_ows_customer_ifs_oracle tocio
                ON tovo.dealer_code = tocio.account_code
                SET tov.dealer_code = tocio.dealer_code
                WHERE 1=1
                AND tov.dealer_code IS NULL
                AND tovo.dealer_code IS NOT NULL";

		$return = $this->ows_db->query($sql);
		return $return;
	}

	public function updateCSR()
	{
		$sql = "UPDATE t_ows_vehicle tov 
				LEFT JOIN t_ows_vehicle_oracle tovo 
					ON tov.cs_no = tovo.cs_no 
				SET 
					tov.csr_no = tovo.csr_no,
				 	tov.csr_date = tovo.csr_date,
				 	tov.or_no = tovo.or_no,
				 	tov.mr_process_date = tovo.mr_process_date
				WHERE 1 = 1 
				  	AND tov.csr_no IS NULL 
				  	AND tovo.csr_no IS NOT NULL";
		$return = $this->ows_db->query($sql);
		return $return;
	}

	public function updatePullout()
	{
		$sql = "UPDATE t_ows_vehicle tov 
				LEFT JOIN t_ows_vehicle_oracle tovo 
				    ON tov.cs_no = tovo.cs_no 
				SET tov.ipc_pullout_date = DATE_FORMAT(tovo.ipc_pullout_date,'%Y-%m-%d')
				WHERE 1 = 1 
				  AND tov.ipc_pullout_date IS NULL 
				  AND tovo.ipc_pullout_date IS NOT NULL";
				  
		$return = $this->ows_db->query($sql);
		return $return;
	}

	public function updateWB()
	{
		$sql = "UPDATE
				  t_ows_vehicle tov 
				  LEFT JOIN t_ows_vehicle_oracle tovo 
				    ON tov.cs_no = tovo.cs_no 
				SET tov.wb_no = tovo.wb_no
				WHERE 1 = 1 
				  AND tov.wb_no IS NULL 
				  AND tovo.wb_no IS NOT NULL";
				  
		$return = $this->ows_db->query($sql);
		return $return;
	}

	public function truncate_t_crm_vehicle_oracle()
    {
        $this->ows_db->truncate('t_ows_vehicle_oracle');
        return true;
    }


}
