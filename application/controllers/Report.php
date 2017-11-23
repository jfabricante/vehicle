<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Report extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('date');

		$this->load->model('buyoff_model');
		$this->load->model('report_form_model');
		$this->load->model('report_model');
		session_check();
	}

	public function ajax_search_lot_number(){
		
		$q = strtolower('%'.$this->input->get('q').'%');
		//~ $results = $this->report_form_model->get_lot_num_by_name($q);
		
		$return_arr = array();
		$data =  $this->report_form_model->get_lot_num_by_name($q);
		foreach($data as $row){
			$row_array = array(
							'id'=>$row->LOT_NUM,
							 'text' => $row->LOT_NUM
						);
			array_push($return_arr,$row_array);
		}
		echo json_encode($return_arr);
	}
	
	public function invoiced_units_form(){
		
		$data = array(
				'content'     => 'invoiced_units_form_view',
				'title'       => 'Invoiced Units Report'
			);

        $this->load->view('include/template', $data);
	}
	
	public function invoiced_units(){
		
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);

		$from = $this->input->post('from');
		$to = $this->input->post('to');
		
		//~ echo $from;die();

		$data = $this->report_model->get_invoiced_units_by_date($from, $to);

		//~ print_r($data)

		$this->load->library('excel');
		//~ echo "<pre>";
		//~ print_r($data);
		//~ echo "</pre>";
		//~ exit();
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),

			'font'  => array(
				  'bold'  => false,
				  'size'  => 8,
				  'name'  => 'Calibri'
			),

			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)

		);

		$styleArray_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),

			'font'  => array(
				  'bold'  => true,
				  'size'  => 10,
				  'name'  => 'Calibri'
			),

			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)

		);

		$row = count($data) + 1;
		$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/invoiced_units_template.xlsx");
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.'U1')->applyFromArray($styleArray_header);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.'U'.$row)->applyFromArray($styleArray);


		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('././resources/report_template/tempfile.xls');

		$filename='invoiced-units.xls'; //save our workbook as this file name

		header('Content-Type: application/vnd.ms-excel'); //mime type

		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save('php://output');
	}
	
	
	public function vehicle_information_form(){
		$data['content'] = 'vehicle_information_form_view';
		$data['title'] = 'Vehicle Information Report';
		$this->load->view('include/template',$data);
	}
	
	public function vehicle_information(){
		
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        
        $lot_number = $this->input->post('lot_number');
        $lot_number2 = $this->input->post('lot_number2');
        $cs_number = $this->input->post('cs_number');
        $chassis_number = $this->input->post('chassis_number');
        $engine_number = $this->input->post('engine_number');
        
		$data = $this->report_model->get_vehicle_details_by_lot($lot_number, $lot_number2, $cs_number, $chassis_number, $engine_number);
		
		//~ print_r($data)
		
		$this->load->library('excel');
		//~ echo "<pre>";
		//~ print_r($data);
		//~ echo "</pre>";
		//~ exit();
      	$styleArray = array(
        	'borders' => array(
	            'allborders' => array(
	                'style' => PHPExcel_Style_Border::BORDER_THIN
	            )
	        ),

	        'font'  => array(
	              'bold'  => false,
	              'size'  => 8,
	              'name'  => 'Calibri'
	        ),

	        'alignment' => array(
	          	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	      	)

    	);

    	$styleArray_header = array(
        	'borders' => array(
	            'allborders' => array(
	                'style' => PHPExcel_Style_Border::BORDER_THIN
	            )
	        ),

	        'font'  => array(
	              'bold'  => true,
	              'size'  => 10,
	              'name'  => 'Calibri'
	        ),

	        'alignment' => array(
	          	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	      	)

    	);

      	$row = count($data) + 1;
      	$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/vehicle_info_template.xlsx");
      	$objPHPExcel->setActiveSheetIndex(0);

      	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
      	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'U1')->applyFromArray($styleArray_header);
      	$objPHPExcel->getActiveSheet()->getStyle('A2:'.'U'.$row)->applyFromArray($styleArray);


      	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	$objWriter->save('././resources/report_template/tempfile.xls');

      	$filename='vehicle-information.xls'; //save our workbook as this file name

      	header('Content-Type: application/vnd.ms-excel'); //mime type

      	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

      	header('Cache-Control: max-age=0'); //no cache

      	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      	$objWriter->save('php://output');
	}


	public function vehicle_completion_form()
	{
		$post = $this->input->post();

		$year = date('Y');
		$array_year = array();

		while($year >= 2016)
		{
			array_push($array_year,$year);
			$year--;
		}

		$data['year'] = $array_year;
		$data['content'] = 'vehicle_completion_report_view';
		$data['title'] = 'Vehicle Completion Report';
		$this->load->view('include/template',$data);
	}

	public function vehicle_completion()
	{
		ini_set('memory_limit', '-1');  
        ini_set('max_execution_time', 7200);

		$post = $this->input->post();
		
		$this->load->library('Pdf_VCR');

        $pdf = new Pdf_VCR(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$date = $post['month'].'/01/'.$post['year'];
		$from =  date("d/M/Y", strtotime($date));
		$to =  date("t/M/Y", strtotime($date));
		$days_of_month =  date("t", strtotime($date));
		$month =  date("F", strtotime($date));
		$date_ =  date("F Y", strtotime($date));
		$params = array(
				'from' => $from,
				'to' => $to
			);
		$pdf->setDate($date_);
		$data = $this->buyoff_model->get_vehicle_completion($params);
		$data_2 = $this->buyoff_model->get_vehicle_completion_2($params);
		$total_day = (array)end($data);
		$total_day_2 = (array)end($data_2);
		$total_footer = array();

		$total_day = array_slice($total_day, 3); 
		$total_day_2 = array_slice($total_day_2, 3); 
		if($total_day_2)
		{
			foreach (array_keys($total_day + $total_day_2) as $key) {
		    	$total_footer[$key] = $total_day[$key] + $total_day_2[$key];
			}
		}
		else
		{
			$total_footer = $total_day;
		}
		
		//exit();
		
		$ctr_pagebreak = ceil(count($data)/22);

		$ctr = 1;
		$day_header = "";
		$sales_header = '';
		while($ctr <= $days_of_month)
		{
			$day_header .= '<th scope="col" style="color:white;">'.$ctr.'</th>';
			$sales_header .= '<td height="10"></td>';
			$ctr++;
		}
		$sales_header .= '<td height="10"></td><td height="10"></td><td height="10"></td><td height="10"></td>';

     	$html = '';
     	$flag_header = 1;
     	$pagebreak_ctr = 0;
     	$summary_details = array();

	     $html .= '<table style="padding: 4px 2px; width: 100%; margin-bottom: 0px;">
	                <tr style="text-align: center; font-size: 10px; font-weight: normal; background-color:#434343">
	                    <th rowspan="2" style="width: 200px; color:white;">ASSEMBLY MODEL</th>
	                    <th rowspan="2" style="width: 150px; color:white;">SALES MODEL</th>
	                    <th rowspan="2" style="width: 110px; color:white;">COLOR</th>
	                    <th colspan="'.$days_of_month.'" style="width: 530; color:white;">'.$month.' '.$post['year'].'</th>
	                    <th rowspan="2" style="width: 5px; background-color:#FFFFFF"></th>
	                    <th rowspan="2" style="width: 40px; color:white;">Total</th>
	                </tr> 
	                <tr style="text-align: center; font-size: 7px; font-weight: bold; background-color:#434343;">   
	                '.$day_header.'  
	                </tr><br>';
	     // print_r(end($data));
	     // exit();

    	foreach($data as $key)
        {
        	
        	if($days_of_month == 31){
	        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_28.'</td>
			                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_29.'</td>
			                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_30.'</td>
			                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$key->DAY_31.'</td>
			                    <td height="10"></td>
		                    	<td height="10" style="border: 1px thin black;">'.$key->TOTAL.'</td>';
			}
	        else if($days_of_month == 30){
	        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_28.'</td>
				                   <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_29.'</td>
				                   <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$key->DAY_30.'</td>
				                   <td height="10"></td>
			                       <td height="10" style="border: 1px thin black;">'.$key->TOTAL.'</td>';
			}   
	        else{$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$key->DAY_28.'</td>
	        						<td height="10"></td>
		                    		<td height="10" style="border: 1px thin black;">'.$key->TOTAL.'</td>';
	    	}

	    	$ddata_edit = '<td height="10" style="text-align: left;">'.$key->ASSEMBLY_MODEL.'</td>
		                    <td height="10" style="text-align: left;">'.$key->SALES_MODEL.'</td>
		                    <td height="10" style="border-right: 1px thin black; text-align: left;">'.$key->BODY_COLOR.'</td>';
		    $ddata_edit2 = '<td height="10" style="text-align: right;"><b>TOTAL</b></td>
		                    <td height="10" style="text-align: left;">( '.$key->SALES_MODEL.' )</td>
		                    <td height="10" style="border-right: 1px thin black;"></td>';

		    $ddata_value = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_1.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_2.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_3.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_4.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_5.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_6.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_7.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_8.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_9.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_10.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_11.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_12.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_13.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_14.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_15.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_16.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_17.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_18.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_19.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_20.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_21.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_22.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_23.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_24.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_25.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_26.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_27.'</td>';     

	    	$ddata = '<tr style="text-align: center; font-size: 6px;">
		                    '.$ddata_edit.'
		                    '.$ddata_value.'
		                    '.$add_day_header.'
		                    
		                </tr>';

		    $ddata2 = '<tr style="text-align: center; font-size: 6px;">
		                    '.$ddata_edit2.'
		                    '.$ddata_value.'
		                    '.$add_day_header.'
		                </tr><br>';
	        	
	        if($pagebreak_ctr <= 20)
	        {
        		if($key->ASSEMBLY_MODEL != NULL OR $key->ASSEMBLY_MODEL != '')
	        	{
	        		if($flag_header == 1)
		        	{
		        		$html .= '
			                <tr style="text-align: center; font-size: 8px; background-color:#D9D9D9">
			                    <td height="10" style="text-align: left;">'.$key->SALES_MODEL.'</td>
			                    '.$sales_header.'
			                </tr>';
			                $pagebreak_ctr++;
		        	}

        			$html .= $ddata;
	                $pagebreak_ctr++;
	                $flag_header = 0;
	        	}
	        	else if(($key->ASSEMBLY_MODEL == NULL OR $key->ASSEMBLY_MODEL == '') AND ($key->SALES_MODEL != NULL OR $key->SALES_MODEL != ''))
	        	{
	        		$html .= $ddata2;
	                $details =  array (
				      'sales_model' => $key->SALES_MODEL,
				      'total' => $key->TOTAL
				    );
					
					array_push($summary_details,$details);
	                $pagebreak_ctr++;
	                $flag_header = 1;
	        	}
	        	
        	}
        	else
        	{
        		$pagebreak_ctr=0;
        		$html .='</table>';
        		$html .= '<br pagebreak="true"/>';
	        	$html .= '<table style="padding: 4px 2px; width: 100%; margin-bottom: 0px;">
                    <tr style="text-align: center; font-size: 10px; font-weight: normal; background-color:#434343">
                        <th rowspan="2" style="width: 200px; color:white;">ASSEMBLY MODEL</th>
                        <th rowspan="2" style="width: 150px; color:white;">SALES MODEL</th>
                        <th rowspan="2" style="width: 110px; color:white;">COLOR</th>
                        <th colspan="'.$days_of_month.'" style="width: 530; color:white;">'.$month.' '.$post['year'].'</th>
                        <th rowspan="2" style="width: 5px; background-color:#FFFFFF"></th>
                        <th rowspan="2" style="width: 40px; color:white;">Total</th>
                    </tr> 
                    <tr style="text-align: center; font-size: 7px; font-weight: bold; background-color:#434343;">   
                    '.$day_header.'  
                    </tr><br>';

                if($key->ASSEMBLY_MODEL != NULL OR $key->ASSEMBLY_MODEL != '')
	        	{
	        		if($flag_header == 1)
		        	{
		        		$html .= '
			                <tr style="text-align: center; font-size: 8px; background-color:#D9D9D9">
			                    <td height="10" style="text-align: left;">'.$key->SALES_MODEL.'</td>
			                    '.$sales_header.'
			                </tr><br>';
			                $pagebreak_ctr++;
		        	}

	        		$html .= $ddata;
	                $pagebreak_ctr++;
	                $flag_header = 0;
	        	}
	        	else if(($key->ASSEMBLY_MODEL == NULL OR $key->ASSEMBLY_MODEL == '') AND ($key->SALES_MODEL != NULL OR $key->SALES_MODEL != ''))
	        	{
	        		$html .= $ddata2;
	                $details =  array (
				      'sales_model' => $key->SALES_MODEL,
				      'total' => $key->TOTAL
				    );
					
					array_push($summary_details,$details);
	                $pagebreak_ctr++;
	                $flag_header = 1;
	        	}
        	}   		
        }
        array_pop($data_2);
        foreach ($data_2 as $key) {

        	if($days_of_month == 31){
	        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_28.'</td>
			                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_29.'</td>
			                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_30.'</td>
			                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$key->DAY_31.'</td>
			                    <td height="10"></td>
		                    	<td height="10" style="border: 1px thin black;">'.$key->TOTAL.'</td>';
			}
	        else if($days_of_month == 30){
	        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_28.'</td>
				                   <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_29.'</td>
				                   <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$key->DAY_30.'</td>
				                   <td height="10"></td>
			                       <td height="10" style="border: 1px thin black;">'.$key->TOTAL.'</td>';
			}   
	        else{$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$key->DAY_28.'</td>
	        						<td height="10"></td>
		                    		<td height="10" style="border: 1px thin black;">'.$key->TOTAL.'</td>';
	    	}

	    	$ddata_edit = '<td height="10" style="text-align: left;">'.$key->ASSEMBLY_MODEL.'</td>
		                    <td height="10" style="text-align: left;">'.$key->SALES_MODEL.'</td>
		                    <td height="10" style="border-right: 1px thin black; text-align: left;">'.$key->BODY_COLOR.'</td>';
		    $ddata_edit2 = '<td height="10" style="text-align: right;"><b>TOTAL</b></td>
		                    <td height="10" style="text-align: left;">( '.$key->SALES_MODEL.' )</td>
		                    <td height="10" style="border-right: 1px thin black;"></td>';
		    $ddata_value = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_1.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_2.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_3.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_4.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_5.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_6.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_7.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_8.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_9.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_10.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_11.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_12.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_13.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_14.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_15.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_16.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_17.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_18.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_19.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_20.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_21.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_22.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_23.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_24.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_25.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_26.'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$key->DAY_27.'</td>';     

	    	$ddata = '<tr style="text-align: center; font-size: 6px;">
		                    '.$ddata_edit.'
		                    '.$ddata_value.'
		                    '.$add_day_header.'
		                    
		                </tr>';

		    $ddata2 = '<tr style="text-align: center; font-size: 6px;">
		                    '.$ddata_edit2.'
		                    '.$ddata_value.'
		                    '.$add_day_header.'
		                </tr><br>';

        	if($key->ASSEMBLY_MODEL != NULL OR $key->ASSEMBLY_MODEL != '')
	        	{
	        		if($flag_header == 1)
		        	{
		        		$html .= '
			                <tr style="text-align: center; font-size: 8px; background-color:#D9D9D9">
			                    <td height="10" style="text-align: left;">'.$key->SALES_MODEL.'</td>
			                    '.$sales_header.'
			                </tr><br>';
			                $pagebreak_ctr++;
		        	}

        			$html .= $ddata;
	                $pagebreak_ctr++;
	                $flag_header = 0;
	        	}
	        	else if(($key->ASSEMBLY_MODEL == NULL OR $key->ASSEMBLY_MODEL == '') AND ($key->SALES_MODEL != NULL OR $key->SALES_MODEL != ''))
	        	{
	        		$html .= $ddata2;
	                $details =  array (
				      'sales_model' => $key->SALES_MODEL,
				      'total' => $key->TOTAL
				    );
					
					array_push($summary_details,$details);
	                $pagebreak_ctr++;
	                $flag_header = 1;
	        	}
        }
        

       	if($days_of_month == 31){
        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_28'].'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_29'].'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_30'].'</td>
		                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$total_footer['DAY_31'].'</td>
		                    <td height="10"></td>
	                    	<td height="10" style="border: 1px thin black;">'.$total_footer['TOTAL'].'</td>';
		}
        else if($days_of_month == 30){
        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_28'].'</td>
			                   <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_29'].'</td>
			                   <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$total_footer['DAY_30'].'</td>
			                   <td height="10"></td>
		                       <td height="10" style="border: 1px thin black;">'.$total_footer['TOTAL'].'</td>';
		}   
        else{
        	$add_day_header = '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; border-right: 1px thin black;">'.$total_footer['DAY_28'].'</td>
        						<td height="10"></td>
	                    		<td height="10" style="border: 1px thin black;">'.$total_footer['TOTAL'].'</td>';
    	}

         $ddata_edit2 ='<td height="10" style="text-align: right;"><b>Grand Total</b></td>
	                    <td height="10" style="text-align: left;"></td>
	                    <td height="10" style="border-right: 1px thin black;"></td>';
	     $ddata_value ='<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_1'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_2'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_3'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_4'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_5'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_6'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_7'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_8'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_9'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_10'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_11'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_12'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_13'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_14'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_15'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_16'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_17'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_18'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_19'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_20'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_21'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_22'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_23'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_24'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_25'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_26'].'</td>
	                    <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black;">'.$total_footer['DAY_27'].'</td>';     

    	 $ddata = '<tr style="text-align: center; font-size: 6px; background-color:#D9D9D9">
	                    '.$ddata_edit2.'
	                    '.$ddata_value.'
	                    '.$add_day_header.'
	                </tr>';
	    $html .= $ddata;
	    $html .='</table>';
        // echo $total_per_day5;
        // exit();
	    $footer = '';
	    $total_buyoff = 0;
	    $footer .= '<br pagebreak="true"/>';
	    $footer .='<br></br><table border="1" style="padding: 4px 2px;">
                        <tr style="text-align: center; font-size: 10px; font-weight: bold;">
                            <th style="width: 300px;">Sales Model</th>
                            <th style="width: 120px;">Total</th>
                        </tr>';
        
	    foreach($summary_details as $key)
	    {
	    	$footer .= '
                    <tr style="text-align: center; font-size: 8px;">
                        <td height="10">'.$key['sales_model'].'</td>
                        <td height="10">'.$key['total'].'</td>
                    </tr>';
	        $total_buyoff += $key['total'];
	    }

	    $footer .= '
                    <tr style="text-align: center; font-size: 8px;">
                        <td height="10"><b>Total</b></td>
                        <td height="10"><b>'.$total_buyoff.'</b></td>
                    </tr></table><br></br>';

        $footer .='<br></br><table border="0" style="padding: 4px 2px;">
                        <tr style="text-align: center; font-size: 10px; font-weight: bold;">
                            <th style="width: 1035px;">This is system generated.</th>
                        </tr></table>';

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('Vehicle Completion Report');
        $pdf->SetSubject('Vehicle Completion Report');
        $pdf->SetKeywords('Vehicle Completion Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '25', '2');
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);

        $pdf->AddPage('L', 'A4');
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$pdf->writeHTMLCell(0, 0, '', '', $footer, 0, 1, 0, true, '', true);
        $pdf->Output("Vehicle Completion Report-" . date('Ymdhis') . ".pdf",'I');
		
	}

	public function vehicle_forecast_form()
	{
		$post = $this->input->post();

		$year = date('Y');
		$array_year = array();

		while($year >= 2016)
		{
			array_push($array_year,$year);
			$year--;
		}

		$data['year'] = $array_year;
		$data['content'] = 'vehicle_forecast_view';
		$data['title'] = 'Vehicle Forecast Report';
		$this->load->view('include/template',$data);
	}

	public function vehicle_forecast()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		$post = $this->input->post();

		$dateObj   = DateTime::createFromFormat('!m', $post['month']);
		$monthName = $dateObj->format('F');
		$date = $post['month'].'/01/'.$post['year'];
		
		$days_of_month =  date("t", strtotime($date));
		$data = $this->report_model->get_vehicle_forecast(substr($monthName, 0, 3),$post['year']);

		$ctr = 1;
		$day_header = '';
		$sales_header = '';
		$html = '';
		while($ctr <= $days_of_month)
		{
			$day_header .= '<th scope="col" style="color:white;">'.$ctr.'</th>';
			$sales_header .= '<td height="10"></td>';
			$ctr++;
		}

		$html .= '<table style="padding: 4px 2px; width: 100%; margin-bottom: 0px;">
	                <tr style="text-align: center; font-size: 8px; font-weight: normal; background-color:#434343">
	                    <th rowspan="2" style="width: 100px; color:white;">LOT #</th>
	                    <th rowspan="2" style="width: 120px; color:white;">PROD MODEL</th>
	                    <th rowspan="2" style="width: 120px; color:white;">SALES MODEL</th>
	                    <th rowspan="2" style="width: 80px; color:white;">BODY COLOR</th>
	                    <th rowspan="2" style="width: 30px; color:white;">UOM</th>

	                    <th colspan="'.$days_of_month.'" style="width: 530; color:white;">'.$monthName.' '.$post['year'].'</th>
	                    <th rowspan="2" style="width: 5px; background-color:#FFFFFF"></th>
	                    <th rowspan="2" style="width: 40px; color:white;">Total</th>
	                </tr> 
	                <tr style="text-align: center; font-size: 7px; font-weight: bold; background-color:#434343;">   
	                '.$day_header.'  
	                </tr><br>';

	    $prev_forecast = '';
		$flag = 0;

		$sales_counter = '';
		$days_ctr = 1;
	    foreach($data as $row) {

	    	if($flag == 0 || $prev_forecast != $row['FORECAST_SET'])
	    	{
	    		if($flag != 0 )
	    		{
	    			$html .= '<br>';
	    		}
	    		$html .=	'<tr style="text-align: center; font-size: 8px; background-color:#D9D9D9">
					    <td height="10" style="text-align: right;">FORECAST SET: </td>
					    <td height="10" style="text-align: left;"><b>'.$row['FORECAST_SET'].'</b></td>
					    <td height="10"></td>
					    <td height="10"></td>
					    <td height="10"></td>
				            '.$sales_header.'
				        <td height="10"></td><td height="10"></td></tr><br>';  
				$flag = 1;

	    	}

	    	if($days_of_month == '28')
	    	{
	    		while($days_ctr < $days_of_month)
	    		{
	    			$sales_counter .= '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; text-align: center;">'.$row['N'.$days_ctr].'</td>';
	    			$days_ctr++;
	    		}
	    		
	    	}
	    	else if($days_of_month == '30')
	    	{
	    		while($days_ctr < $days_of_month)
	    		{
	    			$sales_counter .= '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; text-align: center;">'.$row['N'.$days_ctr].'</td>';
	    			$days_ctr++;
	    		}
	    	}   
	    	else if($days_of_month == '31')
	    	{
	    		while($days_ctr < $days_of_month)
	    		{
	    			$sales_counter .= '<td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; text-align: center;">'.$row['N'.$days_ctr].'</td>';
	    			$days_ctr++;
	    		}
	    	}   

	        $html .= '
	        	<tr style="text-align: center; font-size: 6px;">
	        		<td height="10" style="text-align: left;">'.$row['LOT_NUM'].'</td>
			        <td height="10" style="text-align: left;">'.$row['PROD_MODEL'].'</td>
			        <td height="10" style="text-align: left;">'.$row['SALES_MODEL'].'</td>
			        <td height="10" style="text-align: left;">'.$row['BODY_COLOR'].'</td>
			        <td height="10" style="border-right: 1px thin black; text-align: center;">'.$row['UOM'].'</td>
			        '.$sales_counter.'
	                <td height="10" style="border-bottom: 1px thin black; border-top: 1px thin black; text-align: center; border-right: 1px thin black;">'.$row['N'.$days_of_month].'</td>
	                <td height="10"> </td>
	                <td height="10" style="border: 1px thin black;">'.$row['FORECAST_QUANTITY'].'</td></tr>';

	         $prev_forecast = $row['FORECAST_SET'];
        }
        $html .='</table>';
		$this->load->library('Pdf_VFR');

        $pdf = new Pdf_VFR(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $date_ =  date("F Y", strtotime($date));
        $pdf->setDate($date_);

		$pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('Vehicle Forecast Report');
        $pdf->SetSubject('Vehicle Forecast Report');
        $pdf->SetKeywords('Vehicle Forecast Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '25', '2');
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);

        $pdf->AddPage('L', 'A4');
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		//$pdf->writeHTMLCell(0, 0, '', '', $footer, 0, 1, 0, true, '', true);
        $pdf->Output("Vehicle Forecast Report-" . date('Ymdhis') . ".pdf",'I');
	}

	public function inventory_management(){
		//echo "inventory management";
		$data['content'] = 'inventory_management_report_view';
		$data['title'] = 'Inventory Management Report';
		$this->load->view('include/template',$data);
	}

	public function pdf_inventory_management(){
		$as_of_date = $this->input->post('txt_as_of_date');
		
		$report = $this->report_model->get_inventory_management_report($as_of_date);

		$this->load->library('Pdf_Inv_Mngmt');

        $pdf = new Pdf_Inv_Mngmt(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $date_ =  date("F d, Y", strtotime($as_of_date));
        $pdf->setDate($date_);

        $html = "";

		$pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('Inventory Management Report');
        $pdf->SetSubject('Inventory Management Report');
        $pdf->SetKeywords('Inventory Management Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '25', '2');
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);

        $pdf->AddPage('P', 'A4');

        $body = '';

        $ctr = 0;
        $last_row = count($report);
        foreach($report as $row){
        	$ctr++;
        	/*   sales_model,
			             color,
			             sum(beg_stock) beg_stock,
			             sum(tagged) tagged,
			             sum(buyoff) buyoff,
			             sum(invoiced) invoiced*/
			$inventory_total = ($row->BEG_STOCK + $row->BUYOFF) - $row->INVOICED;

			if($ctr < $last_row){
				if ($row->COLOR == ""){
					$body .= '<tr style="background-color:#ccc;font-weight:bold;" nobr="true">
		        				<td border="none" width="210">'.$row->SALES_MODEL.'</td>
		        				<td align="right" width="130">Total: </td>
		        				<td width="75">'.$row->BEG_STOCK.'</td>
		        				<td width="75">'.$row->BUYOFF.'</td>
		        				<td width="75">'.$row->TAGGED.'</td>
		        				<td width="75">'.$row->INVOICED.'</td>
		        				<td width="75">'.$inventory_total.'</td>
		        			  </tr>';
	        	}
	        
	        	else {
	    			$body .= '<tr nobr="true">
		        				<td width="210">'.$row->SALES_MODEL.'</td>
		        				<td width="130">'.$row->COLOR.'</td>
		        				<td width="75">'.$row->BEG_STOCK.'</td>
		        				<td width="75">'.$row->BUYOFF.'</td>
		        				<td width="75">'.$row->TAGGED.'</td>
		        				<td width="75">'.$row->INVOICED.'</td>
		        				<td width="75">'.$inventory_total.'</td>
		        			  </tr>';
	        	}
        	
        	}
        	else {

	        	$body .= '<tr style="background-color:#ccc;font-weight:bold;" nobr="true">
	        				<td width="340" colspan="2" align="right">Grand Total : </td>
	        				<td width="75">'.$row->BEG_STOCK.'</td>
	        				<td width="75">'.$row->BUYOFF.'</td>
	        				<td width="75">'.$row->TAGGED.'</td>
	        				<td width="75">'.$row->INVOICED.'</td>
	        				<td width="75">'.$inventory_total.'</td>
	        			  </tr>';
	      
        	}

        }
        $html = '<table cellpadding="5" border="1" border-collapse="collapse">
        			<thead>
        				<tr style="background-color:ccc;">
        					<th width="210">Model</th>
        					<th width="130">Color</th>
        					<th width="75">Beginning Stock</th>
        					<th width="75">Buy-Off</th>
        					<th width="75">Tagged Units</th>
        					<th width="75">Invoiced</th>
        					<th width="75">Inventory</th>
        				</tr>
        			</thead>
        			<tbody>
        			'.$body.'
        			</tbody>
        		 </table>';
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		//$pdf->writeHTMLCell(0, 0, '', '', $footer, 0, 1, 0, true, '', true);
        $pdf->Output("Inventory Management-" . date('Ymdhis') . ".pdf",'I');

	}

	public function excel_inventory_management(){


		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);


		$as_of_date = $this->input->post('txt_as_of_date');
		
		$data = $this->report_model->get_inventory_management_report($as_of_date);

		//~ print_r($data)

		$this->load->library('excel');
		//~ echo "<pre>";
		//~ print_r($data);
		//~ echo "</pre>";
		//~ exit();
/*		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),

			'font'  => array(
				  'bold'  => false,
				  'size'  => 8,
				  'name'  => 'Calibri'
			),

			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)

		);*/

/*		$styleArray_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),

			'font'  => array(
				  'bold'  => true,
				  'size'  => 10,
				  'name'  => 'Calibri'
			),

			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)

		);*/

	
		$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/inventory_management_template.xlsx");
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('B2', $as_of_date);
		
		$textFormat='@';//'General','0.00','@'
	/*	var_dump($objPHPExcel);
		die();*/
	//	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A4');
		$ctr = 4;
		$last_row = count($data);
		foreach($data as $row){
			
			$inventory = ($row->BEG_STOCK + $row->BUYOFF) - $row->INVOICED;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ctr, $row->SALES_MODEL);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$ctr, $row->COLOR);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$ctr, $row->BEG_STOCK);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$ctr, $row->BUYOFF);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ctr, $row->TAGGED);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$ctr, $row->INVOICED);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$ctr, $inventory);
		

			
			if ($row->COLOR == ""){
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$ctr,"Total");
					$objPHPExcel->getActiveSheet()
				    ->getStyle('A'.$ctr.':G'.$ctr)
				    ->applyFromArray(
				        array(
				            'fill' => array(
				                'type' => PHPExcel_Style_Fill::FILL_SOLID,
				                'color' => array('rgb' => 'C3C3C3')
				            )
				        )
				    );
			}
			
			if($row->SALES_MODEL == ""){
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$ctr, "Grand Total");
				$objPHPExcel->getActiveSheet()
				    ->getStyle('A'.$ctr.':G'.$ctr)
				    ->applyFromArray(
				        array(
				            'fill' => array(
				                'type' => PHPExcel_Style_Fill::FILL_SOLID,
				                'color' => array('rgb' => 'C3C3C3')
				            )
				        )
				    );
			}
			
			
   			$ctr++;
		}
		//$objPHPExcel->getActiveSheet()->getStyle('A3:'.'G3')->applyFromArray($styleArray_header);
		//$objPHPExcel->getActiveSheet()->getStyle('A2:'.'G'.$ctr)->applyFromArray($styleArray);


	
	//	$objPHPExcel->getActiveSheet()->getStyle('A3:'.'G3')->applyFromArray($styleArray_header);
	//	$objPHPExcel->getActiveSheet()->getStyle('A3:'.'G'.$ctr)->applyFromArray($styleArray);


	/*	PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		foreach(range('A','G') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}*/
    
		/*$objPHPExcel->getActiveSheet()->freezePane('A2');
		$objPHPExcel->getActiveSheet()->setTitle('Line Items');
		$sheet = $objPHPExcel->getActiveSheet();*/
		//$sheet->getSheetView()->setZoomScale(80);
		
		//$objPHPExcel->setActiveSheetIndex(0);
		
	//	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//ob_end_clean();

	/*	header('Content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Inventory-Management-Report.xlsx"');
		$objWriter->save('php://output');*/

		/*$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('././resources/report_template/tempfile.xls');

		$filename='Inventory-Management.xls'; //save our workbook as this file name

		header('Content-Type: application/vnd.ms-excel'); //mime type

		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save('php://output');
*/

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('././resources/report_template/tempfile001.xls');

		$filename='Inventory-Management-Report.xls'; //save our workbook as this file name

		header('Content-Type: application/vnd.ms-excel'); //mime type

		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save('php://output');

	}

}
