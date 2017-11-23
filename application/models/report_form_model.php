<?php

class Report_form_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_lot_num_by_name($key)
	{
		$sql = "SELECT DISTINCT lot_num
				  FROM IPC.XXXIPC_MIS
				 WHERE lower(lot_num) like ?";
		$data = $this->oracle->query($sql, $key);
		return $data->result();
	}

}
