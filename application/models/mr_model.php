<?php

class Mr_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_search_cs_no($cs_no)
	{
		$sql = "SELECT 
					MSN.SERIAL_NUMBER cs_no,
					MSIB.ATTRIBUTE9 SALES_MODEL,
					MSN.LOT_NUMBER,
					MSN.ATTRIBUTE3 engine_no,
					MSN.ATTRIBUTE2 chassis_no,
					CASE WHEN REGEXP_LIKE(MSN.ATTRIBUTE5, '^[0-9]{2}-\w{3}-[0-9]{2}$') THEN TO_CHAR(MSN.ATTRIBUTE5,'YYYY-MM-DD')
                    WHEN REGEXP_LIKE(MSN.ATTRIBUTE5, '^[0-9]{2}-\w{3}-[0-9]{4}$') THEN TO_CHAR(MSN.ATTRIBUTE5,'YYYY-MM-DD')
                    WHEN REGEXP_LIKE(MSN.ATTRIBUTE5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}') THEN TO_CHAR(TO_DATE(MSN.ATTRIBUTE5,'YYYY/MM/DD HH24:MI:SS'),'YYYY-MM-DD')
                    ELSE NULL    
	                END
	                AS buyoff_date,
	                MSN.ATTRIBUTE15 mr_date
				FROM
					MTL_SYSTEM_ITEMS_B MSIB,
					MTL_SERIAL_NUMBERS MSN
				WHERE
					1=1
					AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
					AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
					AND MSN.CURRENT_STATUS in (3,4)
					AND MSN.SERIAL_NUMBER IN (".$cs_no.")";
		$data = $this->oracle->query($sql);
		return $data->result();
	}


	public function get_csr_without_mr_date($params)
	{

			if ($params['date_from'] && $params['date_to']) {
				$config = array(
						'date_from' => $params['date_from'],
						'date_to'   => $params['date_to']
					);

				$sql = "SELECT * FROM (SELECT MSN.LOT_NUMBER,
						       MSIB.ATTRIBUTE9   SALES_MODEL,
						       MSN.SERIAL_NUMBER cs_no,
						       MSIB.ATTRIBUTE11  series,
						       MSN.ATTRIBUTE3    engine_no,
						       MSIB.ATTRIBUTE17  fuel_type,
						       MSIB.ATTRIBUTE18  cylinder,
						       MSIB.ATTRIBUTE16  piston_disp,
						       MSN.ATTRIBUTE2    chassis_no,
						       MSIB.ATTRIBUTE14  gvw,
						       MSIB.ATTRIBUTE8   color,
						       MSN.ATTRIBUTE4    body_no,
						       CASE
						          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{2}$')
						          THEN
						             TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
						          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{4}$')
						          THEN
						             TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
						          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}')
						          THEN
						             TO_CHAR (TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS'),
						                      'MM/DD/YYYY')
						          ELSE
						             NULL
						       END
						          buyoff_date,
						       msn.attribute15   mr_date
						  FROM MTL_SYSTEM_ITEMS_B MSIB, MTL_SERIAL_NUMBERS MSN
						WHERE 1 = 1
						       AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
						       AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
						       AND msib.item_type = 'FG')
						      WHERE 1 = 1
						       AND to_date(buyoff_date, 'MM/DD/YYYY') BETWEEN ? AND ?
							   AND mr_date IS NULL
					";

				$data = $this->oracle->query($sql, $config);

				return $data->result();
			}
			else {
				$sql = "SELECT * FROM (SELECT MSN.LOT_NUMBER,
						       MSIB.ATTRIBUTE9   SALES_MODEL,
						       MSN.SERIAL_NUMBER cs_no,
						       MSIB.ATTRIBUTE11  series,
						       MSN.ATTRIBUTE3    engine_no,
						       MSIB.ATTRIBUTE17  fuel_type,
						       MSIB.ATTRIBUTE18  cylinder,
						       MSIB.ATTRIBUTE16  piston_disp,
						       MSN.ATTRIBUTE2    chassis_no,
						       MSIB.ATTRIBUTE14  gvw,
						       MSIB.ATTRIBUTE8   color,
						       MSN.ATTRIBUTE4    body_no,
						       CASE
						          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{2}$')
						          THEN
						             TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
						          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{2}-\w{3}-[0-9]{4}$')
						          THEN
						             TO_CHAR (msn.attribute5, 'MM/DD/YYYY')
						          WHEN REGEXP_LIKE (msn.attribute5, '^[0-9]{4}/[0-9]{2}/[0-9]{2}')
						          THEN
						             TO_CHAR (TO_DATE (msn.attribute5, 'YYYY/MM/DD HH24:MI:SS'),
						                      'MM/DD/YYYY')
						          ELSE
						             NULL
						       END
						          buyoff_date,
						       msn.attribute15   mr_date
						  FROM MTL_SYSTEM_ITEMS_B MSIB, MTL_SERIAL_NUMBERS MSN
						WHERE 1 = 1
						       AND MSN.INVENTORY_ITEM_ID = MSIB.INVENTORY_ITEM_ID
						       AND MSN.CURRENT_ORGANIZATION_ID = MSIB.ORGANIZATION_ID
						       AND msib.item_type = 'FG')
						      WHERE 1 = 1
						       AND to_date(buyoff_date, 'MM/DD/YYYY') > '01-JUN-17'
							   AND mr_date IS NULL
					";

				$data = $this->oracle->query($sql);

				return $data->result();
			}


	}

	public function update_mr_date($cs_no,$mr_date)
	{
		$sql = "UPDATE MTL_SERIAL_NUMBERS 
				SET ATTRIBUTE15 = ?
				where SERIAL_NUMBER IN (".$cs_no.")
				";
		$this->oracle->query($sql, $mr_date);
	}

}
