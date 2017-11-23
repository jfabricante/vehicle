<?php

class Wb_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_wb_by_trx_number_range($invoices)
	{
		$sql = "SELECT customer_trx_id, trx_number, attribute3 cs_number, attribute4 wb_number
				FROM ra_customer_trx_all
				WHERE customer_trx_id in (".$invoices.")";
		$data = $this->oracle->query($sql);
		return $data->result();
	}

	public function update_wb_number($trx_number, $wb_number)
	{
		$sql = "UPDATE ra_customer_trx_all SET attribute4 = ? WHERE trx_number = ?";
		$this->oracle->query($sql, array($wb_number, $trx_number));
	}


	

}
