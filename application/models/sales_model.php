<?php

class Sales_model extends CI_Model {
	
	public function __construct(){	
		parent::__construct();

		// Set the default timezone
		date_default_timezone_set('Asia/Manila');

		// Load database
		$this->oracle = $this->load->database('oracle', true);
		$this->load->database();
	}

	public function fetch_model_items($type = 'object')
	{
		$this->oracle->cache_on();

		$fields = array(
				'INVENTORY_ITEM_ID item_id',
				'SEGMENT1 prod_model',
				'DESCRIPTION description',
				'ITEM_TYPE type',
				'ATTRIBUTE9 sales_model',
				'INVENTORY_ITEM_STATUS_CODE status'
			);

		$clause = array(
				'ITEM_TYPE'                  => 'FG',
				'ORGANIZATION_ID'            => 121,
				'INVENTORY_ITEM_STATUS_CODE' => 'Active'
			);

		$query = $this->oracle->select($fields)
				->from('MTL_SYSTEM_ITEMS_B')
				->where($clause)
				->order_by('SEGMENT1')
				->get();

		if ($type == 'object'){
			return $query->result();
		}

		return $query->result_array();
		
	}

	public function read_model_item($id)
	{
		$fields = array(
				'ORGANIZATION_ID',
				'INVENTORY_ITEM_ID item_id',
				'SEGMENT1 prod_model',
				'DESCRIPTION description',
				'ITEM_TYPE type',
				'ATTRIBUTE9 sales_model',
				'INVENTORY_ITEM_STATUS_CODE status'
			);

		$clause = array(
				'ITEM_TYPE'                  => 'FG',
				'ORGANIZATION_ID'            => 121,
				'INVENTORY_ITEM_STATUS_CODE' => 'Active',
				'INVENTORY_ITEM_ID'          => $id
			);

		$query = $this->oracle->select($fields)
				->from('MTL_SYSTEM_ITEMS_B')
				->where($clause)
				->get();

		return $query->row();
	}

	public function store_patch(array $params)
	{
		$this->db->insert('production_model_tbl', $params);

		return $this;
	}

	public function exist_patch($id)
	{

		$query = $this->db->get_where('production_model_tbl', array('item_id' => $id));

		if ($query->num_rows() > 0)
		{
			return 1;
		}

		return 0;
	}

	public function fetch_model_patch($type = 'object')
	{
		$fields = array(
				'item_id',
				'prod_model',
				'description',
				'type',
				'sales_model',
				'status',
				'id'
			);

		$result = $this->db->select_max('id')
				->from('production_model_tbl')
				->group_by('item_id')
				->get();

		$ids = array_column($result->result_array(), 'id');

		if ($result->num_rows() > 0)
		{
			$query = $this->db->select($fields)
					->from('production_model_tbl')
					->where_in('id', $ids)
					->get();

			if ($type == 'object')
			{
				return $query->result();
			}

			return $query->result_array();
		}
		
	}

	public function read_patch($id)
	{
		$query = $this->db->select('*')
					->from('production_model_tbl')
					->where(array('item_id' => $id))
					->order_by('id', 'desc')
					->get();

		return $query->row();
	}

	public function fetch_price_items($type = 'object')
	{
		$this->oracle->cache_on();
		
		// Due to some restrictions can't use query builder
		$sql = "SELECT  MST.INVENTORY_ITEM_ID item_id,
				        MST.SEGMENT1 prod_model,
				        MST.DESCRIPTION description,
				        MST.ITEM_TYPE type, 
				        MST.ATTRIBUTE9 sales_model,
				        MST.INVENTORY_ITEM_STATUS_CODE status,
				        IPP.NAME pricelist,
				        IPP.PRICE price
				FROM MTL_SYSTEM_ITEMS_B MST 
				LEFT JOIN IPC_PROD_PRICELIST IPP
				ON MST.INVENTORY_ITEM_ID = IPP.INVENTORY_ITEM_ID
				AND IPP.NAME = 'WSP-Vehicle'
				AND IPP.END_DATE_ACTIVE IS NULL
				WHERE MST.ORGANIZATION_ID = 121
				AND MST.INVENTORY_ITEM_STATUS_CODE = 'Active'
				AND MST.ITEM_TYPE = 'FG'
				ORDER BY MST.SEGMENT1";

		$query = $this->oracle->query($sql);

		if ($type == 'object')
		{
			return $query->result();
		}

		return $query->result_array();
	}

	public function read_pricelist_item($id)
	{
		// Due to some restrictions can't use query builder
		$sql = "SELECT MST.INVENTORY_ITEM_ID item_id,
				        MST.SEGMENT1 prod_model,
				        MST.DESCRIPTION description,
				        MST.ITEM_TYPE type, 
				        MST.ATTRIBUTE9 sales_model,
				        MST.INVENTORY_ITEM_STATUS_CODE status,
				        IPP.NAME pricelist,
				        IPP.PRICE price
				FROM MTL_SYSTEM_ITEMS_B MST 
				LEFT JOIN IPC_PROD_PRICELIST IPP
				ON MST.INVENTORY_ITEM_ID = IPP.INVENTORY_ITEM_ID
				AND IPP.NAME = 'WSP-Vehicle'
				AND IPP.END_DATE_ACTIVE IS NULL
				WHERE MST.ORGANIZATION_ID = 121
				AND MST.INVENTORY_ITEM_STATUS_CODE = 'Active'
				AND MST.ITEM_TYPE = 'FG'
				AND MST.INVENTORY_ITEM_ID = ?";

		$query = $this->oracle->query($sql, $id);

		return $query->row();
	}

	public function store_pricelist_patch(array $params)
	{
		$this->db->insert('pricelist_model_tbl', $params);

		return $this;
	}

	public function fetch_pricelist_patch($type = 'object')
	{
		$result = $this->db->select_max('id')
				->from('pricelist_model_tbl')
				->group_by('item_id')
				->get();

		$ids = array_column($result->result_array(), 'id');

		if ($result->num_rows() > 0)
		{
			$query = $this->db->select('*')
				->from('pricelist_model_tbl')
				->where_in('id', $ids)
				->get();

			if ($type == 'object')
			{
				return $query->result();
			}

			return $query->result_array();
		}
	}

	public function read_pricelist_patch($id)
	{
		$query = $this->db->select('*')
					->from('pricelist_model_tbl')
					->where(array('item_id' => $id))
					->order_by('id', 'desc')
					->get();

		return $query->row();
	}

	public function exist_pricelist_patch($id)
	{
		$query = $this->db->get_where('pricelist_model_tbl', array('item_id' => $id));

		if ($query->num_rows() > 0)
		{
			return 1;
		}

		return 0;
	}
}
	