<?php

class Report_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_invoiced_units_by_date($from, $to){
		
		$sql = "SELECT rcta.customer_trx_id,
					   hcaa.account_number,
					   hp.party_name customer_name,
					   hcaa.account_name,
					   hcpc.name profile_class,
					   ooha.attribute3                     fleet_name,
					   ottl.description                    sales_type,
					   rcta.attribute3 cs_number,
					   msib.attribute9 sales_model,
					   msib.attribute8 body_color,
					   rcta.trx_number,
					   rcta.trx_date,
					   rcta.purchase_order,
					   rtl.name                            payment_terms,
					   ooha.order_number,
					   ooha.ordered_date,
					   rcta.attribute5                     pullout_date,
					  CASE  WHEN rcta.attribute5 IS NOT NULL THEN to_date(rcta.attribute5, 'YYYY/MM/DD HH24:MI:SS')  + (NVL(SUBSTR( rtl.name, 0, INSTR( rtl.name, ' ')-1),  rtl.name) ) ELSE NULL END due_date,
						rcta.attribute4                     wb_number,
					   rcta.attribute8                     csr_number,
					   rcta.attribute11                    csr_date,
--					   rctla.net_amount,
--					   rctla.vat_amount,
--					   rctla.net_amount + rctla.vat_amount invoice_amount,
--					   ROUND (rctla.net_amount * .01, 2)   wht_amount,
--					   (rctla.net_amount + rctla.vat_amount) - (ROUND (rctla.net_amount * .01, 2)) amount_due,
--					    NVL(araa.amount_applied,0) paid_amount,
--					   (rctla.net_amount + rctla.vat_amount) - NVL(araa.amount_applied,0) balance,
					   CASE WHEN (NVL(araa.amount_applied,0) + 1) > ( (rctla.net_amount + rctla.vat_amount) - (ROUND (rctla.net_amount * .01, 2))) THEN 'PAID' ELSE 'UNPAID' END PAYMENT_STATUS,
					   CASE WHEN (NVL(araa.amount_applied,0) + 1) > ( (rctla.net_amount + rctla.vat_amount) - (ROUND (rctla.net_amount * .01, 2))) THEN  araa.apply_date ELSE NULL END paid_date
				 FROM ra_customer_trx_all rcta
					   LEFT JOIN ipc_ar_invoices_with_cm cm
						  ON rcta.customer_trx_id = cm.orig_trx_id
					   LEFT JOIN (SELECT customer_trx_id,
										 MAX(warehouse_id) warehouse_id,
										 MAX(inventory_item_id) inventory_item_id,
										 MAX(quantity_invoiced) quantity_invoiced,
										 SUM (LINE_RECOVERABLE) net_amount,
										 SUM (TAX_RECOVERABLE) vat_amount
									FROM ra_customer_trx_lines_all
								   WHERE line_type = 'LINE'
								GROUP BY customer_trx_id) rctla
						  ON rcta.customer_trx_id = rctla.customer_trx_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON rcta.sold_to_customer_id = hcaa.cust_account_id
					   LEFT JOIN hz_customer_profiles hzp
						  ON hcaa.cust_account_id = hzp.cust_account_id
						   AND rcta.bill_to_site_use_id = hzp.site_use_id
					   LEFT JOIN hz_cust_profile_classes hcpc
						   ON hzp.profile_class_id = hcpc.profile_class_id
					   LEFT JOIN hz_parties hp 
						   ON hcaa.party_id = hp.party_id
					   LEFT JOIN  mtl_system_items_b msib
					        ON rctla.warehouse_id = msib.organization_id
					        AND rctla.inventory_item_id = msib.inventory_item_id
					   LEFT JOIN
					     (SELECT applied_customer_trx_id,
								 SUM (amount_applied) amount_applied,
								 MAX (apply_date)   apply_date
							FROM ar_receivable_applications_all
						   WHERE display = 'Y'
						GROUP BY applied_customer_trx_id) araa
						  ON araa.applied_customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN oe_order_headers_all ooha
						  ON rcta.interface_header_attribute1 = ooha.order_number
					   LEFT JOIN ra_terms_tl rtl ON ooha.payment_term_id = rtl.term_id
					   LEFT JOIN oe_transaction_types_tl ottl
						  ON ooha.order_type_id = ottl.transaction_type_id
				  WHERE 1 = 1
				   AND rcta.cust_trx_type_id = 1002
				  AND cm.orig_trx_id IS NULL
				  AND rcta.trx_date between to_date(?, 'MM/DD/YYYY') AND to_date(?, 'MM/DD/YYYY')";
		$data = $this->oracle->query($sql, array($from, $to));
		return $data->result_array();
	}
	
	
	public function get_vehicle_details_by_lot($lot_number,$lot_number2, $cs_number, $chassis_number, $engine_number){
		
		if($cs_number != NULL){
			$and_cs_number = "AND mis.cs_no = '" . $cs_number . "' ";
		}
		else{
			$and_cs_number = "";
		}
		
		if($chassis_number != NULL){
			$and_chassis_number = "AND msn.attribute2 = '" . $chassis_number . "' ";
		}
		else{
			$and_chassis_number = "";
		}
		
		if($engine_number != NULL){
			$and_engine_number = "AND msn.attribute3 = '" . $engine_number . "' ";
		}
		else{
			$and_engine_number = "";
		}
		
		$and = $and_cs_number . $and_chassis_number . $and_engine_number;
		
		$sql = "  SELECT mis.serial_no,
						 mis.lot_num,
						 msib.segment1   prod_model,
						 msib.description prod_model_desc,
						 msib.attribute9 sales_model,
						 msib.attribute8 body_color,
						 CASE msn.current_status
							WHEN 1 THEN 'Defined but not used'
							WHEN 3 THEN 'Resides in stores'
							WHEN 4 THEN '	Issued out of stores'
							ELSE NULL
						 END
							status,
						 msn.serial_number cs_number,
						 msn.attribute2  chassis_number,
						 msn.attribute4  body_number,
						 mis.shop_order,
						 msib.attribute11  engine_type,
						 msn.attribute3  engine_no,
						 msn.attribute7  aircon_no,
						 msn.attribute9  stereo_no,
						 msn.attribute6  key_no,
						 to_char(to_date(msn.attribute11,'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY') fm_date,
						  to_char(to_date(msn.attribute5,'YYYY/MM/DD HH24:MI:SS'),'MM/DD/YYYY')  buyoff_date,
						 msn.attribute15 mr_date,
						 msn.attribute1  csr_number,
						 msn.attribute12 csr_or_number,
						 msn.attribute14 csr_date
					FROM ipc.xxxipc_mis mis
						 LEFT JOIN mtl_serial_numbers msn ON mis.cs_no = msn.serial_number
						 LEFT JOIN mtl_system_items_b msib
							ON     mis.item_id = msib.inventory_item_id
							   AND mis.org_id = msib.organization_id
				   WHERE mis.lot_num between ? and ?
					".$and."
				   AND mis.active = 1
				ORDER BY mis.lot_num";
		$data = $this->oracle->query($sql, array($lot_number, $lot_number2));
		return $data->result_array();
	}

	public function get_vehicle_forecast($month,$year){
      
      $sql = "SELECT a.forecast_set,
       a.lot_num,
       a.prod_model,
       a.uom,
       a.sales_model,
       a.body_color,
       TO_CHAR (a.forecast_date, 'MON') MONTH,
       TO_CHAR (a.forecast_date, 'YYYY') YEAR,
       a.forecast_quantity,
      case when to_char(a.forecast_date,'DD') = 1 then a.forecast_quantity else 0 end n1,
                case when to_char(a.forecast_date,'DD') = 2 then a.forecast_quantity else 0 end n2,
                case when to_char(a.forecast_date,'DD') = 3 then a.forecast_quantity else 0 end n3,
                case when to_char(a.forecast_date,'DD') = 4 then a.forecast_quantity else 0 end n4,
                case when to_char(a.forecast_date,'DD') = 5 then a.forecast_quantity else 0 end n5,
                case when to_char(a.forecast_date,'DD') = 6 then a.forecast_quantity else 0 end n6,
                case when to_char(a.forecast_date,'DD') = 7 then a.forecast_quantity else 0 end n7,
                case when to_char(a.forecast_date,'DD') = 8 then a.forecast_quantity else 0 end n8,
                case when to_char(a.forecast_date,'DD') = 9 then a.forecast_quantity else 0 end n9,
                case when to_char(a.forecast_date,'DD') = 10 then a.forecast_quantity else 0 end n10,
                case when to_char(a.forecast_date,'DD') = 11 then a.forecast_quantity else 0 end n11,
                case when to_char(a.forecast_date,'DD') = 12 then a.forecast_quantity else 0 end n12,
                case when to_char(a.forecast_date,'DD') = 13 then a.forecast_quantity else 0 end n13,
                case when to_char(a.forecast_date,'DD') = 14 then a.forecast_quantity else 0 end n14,
                case when to_char(a.forecast_date,'DD') = 15 then a.forecast_quantity else 0 end n15,
                case when to_char(a.forecast_date,'DD') = 16 then a.forecast_quantity else 0 end n16,
                case when to_char(a.forecast_date,'DD') = 17 then a.forecast_quantity else 0 end n17,
                case when to_char(a.forecast_date,'DD') = 18 then a.forecast_quantity else 0 end n18,
                case when to_char(a.forecast_date,'DD') = 19 then a.forecast_quantity else 0 end n19,
                case when to_char(a.forecast_date,'DD') = 20 then a.forecast_quantity else 0 end n20,
                case when to_char(a.forecast_date,'DD') = 21 then a.forecast_quantity else 0 end n21,
                case when to_char(a.forecast_date,'DD') = 22 then a.forecast_quantity else 0 end n22,
                case when to_char(a.forecast_date,'DD') = 23 then a.forecast_quantity else 0 end n23,
                case when to_char(a.forecast_date,'DD') = 24 then a.forecast_quantity else 0 end n24,
                case when to_char(a.forecast_date,'DD') = 25 then a.forecast_quantity else 0 end n25,
                case when to_char(a.forecast_date,'DD') = 26 then a.forecast_quantity else 0 end n26,
                case when to_char(a.forecast_date,'DD') = 27 then a.forecast_quantity else 0 end n27,
                case when to_char(a.forecast_date,'DD') = 28 then a.forecast_quantity else 0 end n28,
                case when to_char(a.forecast_date,'DD') = 29 then a.forecast_quantity else 0 end n29,
                case when to_char(a.forecast_date,'DD') = 30 then a.forecast_quantity else 0 end n30,
                case when to_char(a.forecast_date,'DD') = 31 then a.forecast_quantity else 0 end n31
  FROM (  SELECT mfd.forecast_set,
                 mfd.description lot_num,
                 mfdetails.prod_model,
                 mfdetails.forecast_quantity,
                 mfdetails.uom,
                 mfdetails.forecast_date,
                 mfdetails.sales_model,
                 mfdetails.body_color                --mfd.forecast_designator
            FROM mrp_forecast_designators mfd,
                 (SELECT mfi.forecast_designator,
                         mfi.organization_id,
                         msib.segment1 prod_model,
                         msib.attribute9 sales_model,
                         msib.attribute8 body_color,
                         mfi.primary_uom_code uom,
                         mfdq.forecast_date,
                         mfdq.original_forecast_quantity forecast_quantity
                    FROM mrp_forecast_items_v mfi,
                         mrp_forecast_dates_v mfdq,
                         mtl_system_items_b msib
                   WHERE     mfi.inventory_item_id = mfdq.inventory_item_id
                         AND mfi.forecast_designator = mfdq.forecast_designator
                         AND mfi.organization_id = mfdq.organization_id
                         AND mfi.inventory_item_id = msib.inventory_item_id
                         AND mfi.organization_id = msib.organization_id
                         AND msib.item_type = 'FG') mfdetails
           WHERE     1 = 1
                 AND mfd.forecast_designator = mfdetails.forecast_designator
                 AND mfd.organization_id = mfdetails.organization_id
                 AND TO_CHAR (mfdetails.forecast_date, 'Mon') =
                        NVL (?, TO_CHAR (mfdetails.forecast_date, 'Mon'))
                 AND TO_CHAR (mfdetails.forecast_date, 'YYYY') =
                        NVL (?, TO_CHAR (mfdetails.forecast_date, 'YYYY'))
        ORDER BY mfdetails.forecast_date, mfd.forecast_set) a

      ";

      $data = $this->oracle->query($sql,array($month,$year));
      return $data->result_array();
  }

  public function get_inventory_management_report($as_of){
  		$month = date('F', strtotime($as_of));
		$year = date('Y', strtotime($as_of));
  		$beg_stock_sql = "";

  		if($month == "July" && $year == "2017"){ // beginning balance of vehicle
  			$beg_stock_sql = "SELECT 
					            msib.inventory_item_id,
					            msib.segment1 part_no,
					            nvl(msib.attribute9,msib.segment1) sales_model,
					            msib.attribute8 color,
					            sum(mmt.transaction_quantity) beg_stock,
					            0 tagged,
					            0 buyoff,
					            0 invoiced
							FROM mtl_material_transactions mmt 
							        INNER JOIN mtl_transaction_types mtt
							            ON mtt.transaction_type_id = mmt.transaction_type_id
							        INNER JOIN mtl_system_items_b msib
							            ON msib.inventory_item_id = mmt.inventory_item_id
							            AND msib.organization_id = mmt.organization_id
							WHERE 1 = 1
							            and mmt.organization_id IN (121,107)
							            and mmt.SUBINVENTORY_code IN ('VSS','STG')
							            and UPPER(msib.inventory_item_status_code) = 'ACTIVE'
							            and trunc(mmt.transaction_date) <= add_months(TO_DATE('$as_of'), -1)
							GROUP BY 
							           msib.inventory_item_id,
							            msib.segment1,
							            msib.attribute9,
							            msib.segment1,
							            msib.attribute8";
  		}
  		else {
  			$beg_stock_sql = "SELECT inventory_item_id,
								            part_no,
								            sales_model,
								            color,
								            BEG_STOCK + BUYOFF - INVOICED beg_stock,
								            0 tagged,
								            0 buyoff,
								            0 invoiced
								FROM (SELECT
								                        inventory_item_id,
								                        part_no,
								                        sales_model,
								                        color,
								                        NVL(sum(beg_stock),0) beg_stock,
								                        0 tagged,
								                        NVL(sum(buyoff),0) buyoff,
								                        NVL(sum(invoiced),0) invoiced
								                FROM (
								                      
								                           SELECT 
								                                        msib.inventory_item_id,
								                                        msib.segment1 part_no,
								                                        nvl(msib.attribute9,msib.segment1) sales_model,
								                                        msib.attribute8 color,
								                                        sum(mmt.transaction_quantity) beg_stock,
								                                        0 tagged,
								                                        0 buyoff,
								                                        0 invoiced
								                            FROM mtl_material_transactions mmt 
								                                    INNER JOIN mtl_transaction_types mtt
								                                        ON mtt.transaction_type_id = mmt.transaction_type_id
								                                    INNER JOIN mtl_system_items_b msib
								                                        ON msib.inventory_item_id = mmt.inventory_item_id
								                                        AND msib.organization_id = mmt.organization_id
								                            WHERE 1 = 1
								                                        and mmt.organization_id IN (121,107)
								                                        and mmt.SUBINVENTORY_code IN ('VSS','STG')
								                                        and UPPER(msib.inventory_item_status_code) = 'ACTIVE'
								                                        and trunc(mmt.transaction_date) <= add_months(TO_DATE('$as_of'), -2)
								                            GROUP BY 
								                                       msib.inventory_item_id,
								                                        msib.segment1,
								                                        msib.attribute9,
								                                        msib.segment1,
								                                        msib.attribute8
								                           
								                            UNION ALL
								                         
								                            SELECT   inventory_item_id,
								                                     part_no,
								                                     nvl(sales_model,part_no) sales_model,
								                                     color,
								                                     0 beg_stock,
								                                     0 tagged,
								                                     count(cs_no) buyoff,
								                                     0 invoiced
								                            FROM (SELECT MSN.LOT_NUMBER,
								                                       MSIB.INVENTORY_ITEM_ID,
								                                       MSIB.SEGMENT1 PART_NO,
								                                       MSIB.ATTRIBUTE9   SALES_MODEL,
								                                       MSN.SERIAL_NUMBER cs_no,
								                                       MSIB.ATTRIBUTE11  series,
								                                       msib.attribute11 || ' ' ||msn.attribute3 engine,
								                                       msib.attribute19 || ' ' ||msn.attribute7 aircon,
								                                       MSIB.ATTRIBUTE17  fuel_type,
								                                       MSIB.ATTRIBUTE18  cylinder,
								                                       MSIB.ATTRIBUTE16  piston_disp,
								                                       MSN.ATTRIBUTE2    chassis_no,
								                                       MSIB.ATTRIBUTE14  gvw,
								                                       MSIB.ATTRIBUTE8   color,
								                                       MSN.ATTRIBUTE4    body_no,
								                                       MSN.ATTRIBUTE15,
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
								                                       AND msn.current_status in (3,4)    
								                                       AND msn.c_attribute30 is null
								                                       AND msib.item_type = 'FG')
								                            WHERE 1 = 1
								                                  AND TO_DATE (buyoff_date, 'MM/DD/YYYY') BETWEEN TO_DATE(ADD_MONTHS((LAST_DAY('$as_of')+1),-2)) AND TO_DATE(ADD_MONTHS('$as_of',-1))
								                                  AND attribute15 IS NULL
								                            GROUP BY
								                                inventory_item_id,
								                                part_no,
								                                sales_model,
								                                color
								                            UNION ALL
								                      
								                            SELECT inventory_item_id,
								                                    part_no,
								                                    sales_model,
								                                    color,
								                                    0 beg_stock,
								                                    0 tagged,
								                                    0 buyoff,
								                                    count(customer_trx_id) invoiced
								                            FROM (
								                                  SELECT  msib.inventory_item_id,
								                                          msib.segment1 part_no,
								                                          nvl(msib.attribute9,msib.segment1) sales_model,
								                                          msib.attribute8 color,
								                                          rctla.warehouse_id,
								                                          rctla.customer_trx_id
								                                  FROM ra_customer_trx_lines_all rctla
								                                        INNER JOIN ra_customer_trx_all rcta
								                                            ON rctla.customer_trx_id = rcta.customer_trx_id
								                                        INNER JOIN mtl_system_items_b msib
								                                            ON  rctla.inventory_item_id = msib.inventory_item_id
								                                            AND rctla.warehouse_id = msib.organization_id
								                                         LEFT  JOIN ipc_ar_invoices_with_cm cm
								                                            ON rcta.customer_trx_id = cm.orig_trx_id
								                                  WHERE 1 = 1
								                                              AND rcta.cust_trx_type_id = 1002
								                                              AND cm.orig_trx_id IS NULL
								                                              AND msib.organization_id IN (121,107)
								                                              AND msib.item_type = 'FG'
								                                              AND to_date(rcta.trx_date) BETWEEN TO_DATE(ADD_MONTHS((LAST_DAY('$as_of')+1),-2)) AND TO_DATE(ADD_MONTHS('$as_of',-1))
								                                  GROUP BY 
								                                      msib.inventory_item_id,
								                                      msib.segment1,
								                                      msib.attribute9,
								                                      msib.attribute8,
								                                      rctla.inventory_item_id,
								                                      rctla.warehouse_id,
								                                      rctla.customer_trx_id    
								                                )
								             GROUP BY
								              inventory_item_id,
								              part_no,
								              sales_model,
								              color             
								          ) 
								           GROUP BY
								              inventory_item_id,
								              part_no,
								              sales_model,
								              color                                  
								      )";
  			
  		}

  		$sql = "SELECT --inventory_item_id,
          			--   part_no,
			             sales_model,
			             color,
			             NVL(sum(beg_stock),0) beg_stock,
			             NVL(sum(tagged),0) tagged,
			             NVL(sum(buyoff),0) buyoff,
			             NVL(sum(invoiced),0) invoiced
				FROM (
				           $beg_stock_sql
				            UNION ALL
				            -- TAGGED UNITS
				            SELECT    msib_tagged.inventory_item_id,
				                            msib_tagged.segment1 part_no,
				                            nvl(msib_tagged.attribute9,msib_tagged.segment1) sales_model,
				                            msib_tagged.attribute8 color,
				                            0 beg_stock,
				                            count(msn_tagged.serial_number) tagged,
				                            0 buyoff,
				                            0 invoiced
				            FROM 
				                        mtl_system_items_b msib_tagged,
				                        mtl_serial_numbers msn_tagged 
				            WHERE 1 = 1
				                        AND msn_tagged.current_organization_id = msib_tagged.organization_id
				                        AND msn_tagged.inventory_item_id = msib_tagged.inventory_item_id             
				                        AND msn_tagged.reservation_id IS NOT NULL
				                        AND msib_tagged.organization_id IN (121,107) 
				                        AND msib_tagged.item_type = 'FG'
				                        AND msn_tagged.current_subinventory_code IN ('VSS')
				                       -- AND TO_DATE(msn_tagged.d_attribute20) BETWEEN TO_DATE(ADD_MONTHS((LAST_DAY(?)+1),-1)) AND TO_DATE(?)
				            --            AND msib_tagged.inventory_item_id = 144139
				                        AND UPPER(msib_tagged.inventory_item_status_code) = 'ACTIVE'
				            GROUP BY 
				                        msib_tagged.inventory_item_id,
				                        msib_tagged.segment1,
				                        msib_tagged.attribute9,
				                        msib_tagged.attribute8,
				                        msib_tagged.inventory_item_id
				            UNION ALL
				            -- BUY OFF
				          	SELECT   inventory_item_id,
						             part_no,
						             nvl(sales_model,part_no) sales_model,
						             color,
						             0 beg_stock,
						             0 tagged,
						             count(cs_no) buyoff,
						             0 invoiced
							FROM 
			        			(SELECT MSN.LOT_NUMBER,
			                           MSIB.INVENTORY_ITEM_ID,
			                           MSIB.SEGMENT1 PART_NO,
			                           MSIB.ATTRIBUTE9   SALES_MODEL,
			                           MSN.SERIAL_NUMBER cs_no,
			                           MSIB.ATTRIBUTE11  series,
			                           msib.attribute11 || ' ' ||msn.attribute3 engine,
			                           msib.attribute19 || ' ' ||msn.attribute7 aircon,
			                           MSIB.ATTRIBUTE17  fuel_type,
			                           MSIB.ATTRIBUTE18  cylinder,
			                           MSIB.ATTRIBUTE16  piston_disp,
			                           MSN.ATTRIBUTE2    chassis_no,
			                           MSIB.ATTRIBUTE14  gvw,
			                           MSIB.ATTRIBUTE8   color,
			                           MSN.ATTRIBUTE4    body_no,
			                           MSN.ATTRIBUTE15,
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
			                           AND msn.current_status in (3,4)    
			                           AND msn.c_attribute30 is null
			                           AND msib.item_type = 'FG')
			                          WHERE 1 = 1
			                           AND TO_DATE (buyoff_date, 'MM/DD/YYYY') BETWEEN TO_DATE(ADD_MONTHS((LAST_DAY(?)+1),-1)) AND TO_DATE(?)
			                            AND attribute15 IS NULL
							GROUP BY
										inventory_item_id,
										part_no,
										sales_model,
										color
				            UNION ALL
				            -- INVOICED
				             SELECT inventory_item_id,
                                            part_no,
                                            sales_model,
                                            color,
                                            0 beg_stock,
                                            0 tagged,
                                            0 buyoff,
                                            count(customer_trx_id) invoiced
                                FROM (
                                SELECT msib.inventory_item_id,
                                            msib.segment1 part_no,
                                            nvl(msib.attribute9,msib.segment1) sales_model,
                                            msib.attribute8 color,
                --                            rctla.inventory_item_id,
                                            rctla.warehouse_id,
                                            rctla.customer_trx_id
                                      FROM ra_customer_trx_lines_all rctla
                                                    INNER JOIN ra_customer_trx_all rcta
                                                        ON rctla.customer_trx_id = rcta.customer_trx_id
                                                    INNER JOIN mtl_system_items_b msib
                                                        ON  rctla.inventory_item_id = msib.inventory_item_id
                                                        AND rctla.warehouse_id = msib.organization_id
                                                     LEFT  JOIN ipc_ar_invoices_with_cm cm
                                                        ON rcta.customer_trx_id = cm.orig_trx_id
                                                WHERE 1 = 1
                                                            AND rcta.cust_trx_type_id = 1002
                                                            AND cm.orig_trx_id IS NULL
                                                            AND msib.organization_id IN (121,107)
                                                            AND msib.item_type = 'FG'
                                                            AND to_date(rcta.trx_date) BETWEEN TO_DATE(ADD_MONTHS((LAST_DAY(?)+1),-1)) AND TO_DATE(?)
                                                GROUP BY 
                                                                msib.inventory_item_id,
                                                                msib.segment1,
                                                                msib.attribute9,
                                                                msib.attribute8,
                                                                rctla.inventory_item_id,
                                                                rctla.warehouse_id,
                                                                rctla.customer_trx_id    
                                    )
                                    GROUP BY  inventory_item_id,
                                                        part_no,
                                                        sales_model,
                                                        color
				            )
				GROUP BY ROLLUP
				          ( 
				            sales_model,
				            color) 
            ";
	  $data = $this->oracle->query($sql,array($as_of,$as_of,$as_of,$as_of,$as_of,$as_of));
      return $data->result();
  }

}
