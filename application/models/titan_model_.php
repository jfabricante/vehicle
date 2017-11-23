<?php

class Titan_Model_ extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

  public function get_header_date($lpda_no,$date_start){
      $sql = "SELECT   
                attribute_no, 
                month_needed, 
                day_needed, 
                needed_date,
                year_needed
              FROM ipc.ipc_lpda_columns
              WHERE 1=1
                AND lpda_no = '$lpda_no'
                AND TO_CHAR(needed_date,'YYYY-MM-DD') >= '$date_start'
                --AND ROWNUM <= 27
              ORDER BY attribute_no";
      $data = $this->oracle->query($sql);
      return $data->result_array();
  }

  public function get_po_header($lpda_no){

      $sql = "SELECT 
              *
              FROM ipc.ipc_lpda_headers where lpda_no = '$lpda_no'
              ";
      $data = $this->oracle->query($sql);
      return $data->result_array();
  }

	public function get_lpda_data($lpda_no,$attribute,$attribute28,$report_type,$withforecast){
		 
     ini_set('memory_limit', '-1');  
     ini_set('max_execution_time', 72000);

     $sql2 = "SELECT rid, lpda_headers.lpda_no, lpda_headers.vendor_name,
                 lpda_headers.contact_person, lpda_headers.contact_no,
                 lpda_headers.vendor_address, lpda_headers.buyer_name,
                 lpda_details.part_no, lpda_details.part_desc,
                 lpda_details.item_categories, lpda_details.iusage,
                 lpda_details.total_qty, 
                 $attribute,
                 CASE 
                    WHEN rid = 1 
                      THEN
                         (
                          CASE
                             WHEN lpda_details.total_qty IS NULL
                                THEN NULL
                             ELSE ( $attribute28 )
                          END
                         ) ELSE NULL END attribute_28,

                 lpda_details.n1, lpda_details.n2, lpda_details.n3
        FROM ipc.ipc_lpda lpda_details, ipc.ipc_lpda_headers lpda_headers
        WHERE 1=1
        AND lpda_headers.po_header_id = lpda_details.po_header_id
        AND lpda_headers.lpda_no = '$lpda_no'
        AND lpda_details.item_categories LIKE '%$report_type%'
        $withforecast
        ORDER BY lpda_no, part_no ASC, rid ASC";

    $data2 = $this->oracle->query($sql2);
		return $data2->result();
	}

  public function get_cso_data($lpda_no,$attribute,$attribute28,$report_type){
     ini_set('memory_limit', '-1');  
     ini_set('max_execution_time', 72000);

     $sql2 = "SELECT   ROWNUM item_no,lpda_details.part_no, lpda_details.part_desc, lpda_details.total_qty,
                       lpda_details.iusage, lpda_details.n1, lpda_details.n2,
                       lpda_details.n3
                  FROM ipc.ipc_lpda lpda_details, ipc.ipc_lpda_headers lpda_headers
                 WHERE 1 = 1
                   AND rid = 1
                   AND lpda_headers.po_header_id = lpda_details.po_header_id
                   AND lpda_headers.lpda_no = '$lpda_no'
                   AND lpda_details.item_categories LIKE '%CSO%'
                   AND TOTAL_QTY IS NOT NULL
              ORDER BY lpda_headers.lpda_no
              ";

    $data2 = $this->oracle->query($sql2);
    return $data2->result_array();
  }

  public function get_po($categories)
  {
      $sql = "SELECT 
                LPDA_NO, 
                VENDOR_NAME,  
                STR_MONTH_CREATED, 
                BUYER_NAME, 
                ERR_MSGS,
                f_month_desc  
              FROM IPC.IPC_LPDA_HEADERS
              WHERE CATEGORIES LIKE '%$categories%'
              and f_month_desc is not null
              ORDER BY VENDOR_NAME";
      $data = $this->oracle->query($sql);
      return $data->result();
  }

  public function get_po_details($lpda_no)
  {
      $sql = "SELECT 
                LPDA_NO, 
                VENDOR_NAME,  
                STR_MONTH_CREATED, 
                BUYER_NAME, 
                ERR_MSGS,
                F_MONTH_SDESC  
              FROM IPC.IPC_LPDA_HEADERS
              WHERE LPDA_NO = ?
              ";
      $data = $this->oracle->query($sql,$lpda_no);
      return $data->result();
  }
}
