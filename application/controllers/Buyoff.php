<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Buyoff extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('date');

		$this->load->model('buyoff_model');
		session_check();
	}
	
	public function for_buyoff(){
		$lot_number = ($this->input->post('lot_number') == NULL)? '0':$this->input->post('lot_number');
		$data['lot_number'] = $lot_number;
		$data['content'] = 'for_buyoff_view';
		$data['title'] = 'For Buyoff Units';
		$data['lots'] = $this->buyoff_model->get_for_buyoff_lot_numbers();
		$data['result'] = $this->buyoff_model->get_for_buyoff_headers($lot_number);
		$this->load->view('include/template',$data);
	}
	
	public function submit_selected_for_buyoff(){
		$cs_numbers = "'0'";
		//~ $ctr = 1;
		foreach($this->input->post('for_buyoff') as $cs_number){
			$cs_numbers .= ",'".$cs_number."'";
			//~ echo $ctr . ' ' . $cs_number . '<br />';
			//~ $ctr++;
		}
		//~ echo $cs_numbers;
		$this->buyoff_model->update_for_for_buyoff($this->session->get_userdata()['user_id'], $cs_numbers);
		
		redirect('buyoff/for_buyoff');
	}
	
	public function list_(){
		$data['content'] = 'buyoff_list_view';
		$data['title'] = 'Buyoff Units';
		$data['result'] = $this->buyoff_model->get_buyoff_headers();
		$this->load->view('include/template',$data);
	}
	
	public function ajax_get_buyoff_line(){
		
		$data['row'] = $this->buyoff_model->get_buyoff_line($this->input->post('cs_number'));
		echo $this->load->view('ajax/buyoff_line_view',$data,true);

		//~ $row = $this->buyoff_model->get_buyoff_line('CR9299');
		//~ echo '<pre>';		
		//~ print_r($row);
		//~ echo '</pre>';
		
	}

	public function ajax_for_transfer()
	{
		$data['cs_no'] = $this->input->post('cs_number');
		echo $this->load->view('ajax/for_transfer',$data,true);
	}

	public function ajax_for_repair()
	{
		$data['cs_no'] = $this->input->post('cs_number');
		echo $this->load->view('ajax/for_repair',$data,true);
	}

	public function transfer()
	{
		$post = $this->input->post();

		$params_transfer = array(
				'cs_no' => $post['cs_no'],
				'last_update' => date("d-M-y",now('asia/manila'))
			);

		$this->buyoff_model->for_transfer($params_transfer);

		$params_history = array(
				'cs_no' => $post['cs_no'],
				'status' => 'FOR TRANSFER',
				'description' => '',
				'date_log' => date("d-M-y",now('asia/manila'))
			);

		$this->buyoff_model->history_log($params_history);

		redirect('buyoff/list_');
	}

	public function repair()
	{
		$post = $this->input->post();

		$params_repair = array(
				'cs_no' => $post['cs_no'],
				'description' => $post['desc'],
				'problem_created_date' => date("d-M-y",now('asia/manila'))
			);

		$this->buyoff_model->for_repair($params_repair);

		$params_history = array(
				'cs_no' => $post['cs_no'],
				'status' => 'FOR REPAIR',
				'description' => $post['desc'],
				'date_log' => date("d-M-y",now('asia/manila'))
			);

		$this->buyoff_model->history_log($params_history);

		redirect('buyoff/list_');
		
	}

	public function view_for_repair()
	{
		$data['content'] = 'for_repair';
		$data['title'] = 'For Repair';
		$data['result'] = $result = $this->buyoff_model->get_for_repair();

		$this->load->view('include/template',$data);
	}

	public function ajax_return_repair()
	{
		$data['cs_no'] = $this->input->post('cs_number');
		echo $this->load->view('ajax/return_repair',$data,true);
	}

	public function return_repair()
	{
		$post = $this->input->post();

		$params_history = array(
				'cs_no' => $post['cs_no'],
				'status' => 'RETURN TO BUYOFF',
				'description' => $post['action_taken'],
				'date_log' => date("d-M-y",now('asia/manila'))
			);

		$this->buyoff_model->return_repair($params_history);

		redirect('buyoff/list_');
	}

	public function buyoff_summary()
	{
		//$data['sales_model'] = $this->buyoff_model->get_sales_model();

		$data = array(
				'content'     => 'buyoff_summary_view',
				'title'       => 'MAIDD STOCK/SALES REPORT',
				'sales_model' => $this->buyoff_model->get_sales_model(),
				'lot_number' => $this->buyoff_model->get_buyoff_lot_number()
			);

        $this->load->view('include/template',$data);
	}

	public function ajax_get_buyoff_report_details()
	{
		$post = $this->input->post();
		$result = $this->buyoff_model->get_buyoff_report_details($post['lot_no']);
		//print_r($result[0]);
		echo json_encode($result);
		//exit();
		//exit();
	}

	public function generate_excel_report() 
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);

		// Load Excel library
		$this->load->library('excel');

		$cp_no       = $this->input->post('cp_no') ? $this->input->post('cp_no') : '';
		$cp_date     = $this->input->post('cp_date') ? $this->input->post('cp_date') : '';
		$cp_date     = $cp_date ? date('d-M-y', strtotime($cp_date)) : '';
		$entry_no    = $this->input->post('entry_no');
		$date_from   = date('d-M-y', strtotime($this->input->post('date_from')));
		$date_to     = date('d-M-y', strtotime($this->input->post('date_to')));
		$sales_model = $this->input->post('sales_model') ? $this->input->post('sales_model') : '';
		$lot_number = $this->input->post('lot_number') ? $this->input->post('lot_number') : '';
		//$date_from = date('Y/m/d', strtotime($this->input->post('date_from')));
		//$date_to   = date('Y/m/d', strtotime($this->input->post('date_to')));

		//var_dump($this->input->post()); die;
		$config = array(
				'date_from'   => $date_from,
				'date_to'     => $date_to,
				'sales_model' => $sales_model,
				'lot_number' => $lot_number,
			);

		// Get the data
		$data = $this->buyoff_model->get_buyoff_filter_by_date($config);

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
		//var_dump($data); die;

		$config = array();

		foreach ($data as $row) {
			$config[] = array(
					"cs_no"                  => $row->CS_NO,
					"engine_no"              => $row->ENGINE,
					"fuel_type"              => $row->FUEL_TYPE,
					"cylinder"               => $row->CYLINDER,
					"piston_ disp"           => $row->PISTON_DISP,
					"boc_cp_number"          => $cp_no,
					"cp_date"                => $cp_date,
					"informal_entry_number"  => $entry_no,
					"chasis_no"              => $row->CHASSIS_NO,
					"boc_cp_number1"         => $cp_no,
					"cp_date1"               => $cp_date,
					"informal_entry_number1" => $entry_no,
					"body_id_number"         => $row->BODY_NO,
					"make"                   => "Isuzu",
					"series"                 => $row->SALES_MODEL ? $row->SALES_MODEL : '',
					"body_type"              => '',
					"year"                   => '',
					"color"                  => $row->COLOR,
					'gvw'                    => $row->GVW,
					'aircon'                 => '',
					'submission_date'        => '',
					'tiresize_front'         => '',
					'tiresize_back'          => '',
					'coc_no'                 => '',
					'lot_no'                 => $row->LOT_NUMBER,
					'buyoff_date'            => $row->BUYOFF_DATE,
					'csr_number'            => $row->CSR_NUMBER
				);
		}

		//var_dump($config); die;

		$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/buyoff_template.xlsx");
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->fromArray($config, null, 'A2');
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
		$objWriter->save('example.html');

		$filename='buyoff_report.xls'; //save our workbook as this file name

		header('Content-Type: application/vnd.ms-excel'); //mime type

		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save('php://output');
	}


	public function generate_report()
	{
		$post = $this->input->post();

		$result = $this->buyoff_model->get_buyoff_generate_report($post['lot_no']);
		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";
		// exit();
		$data = '';
		foreach ($result as $row) {
			$data .= '<tr style="text-align: center; font-size: 8px; font-weight: normal;">
						  <th>'.$row->CS_NO.'</th>
						  <th>'.$row->SERIES.'</th>
						  <th>'.$row->ENGINE_NO.'</th>
						  <th>'.$row->FUEL_TYPE.'</th>
						  <th>'.$row->CYLINDER.'</th>
						  <th>'.$row->PISTON_DISP.'</th>
						  <th>'.$post['cp_no'].'</th>
						  <th>'.$post['cp_date'].'</th>
						  <th>'.$post['entry_no'].'</th>
						  <th>'.$row->CHASSIS_NO.'</th>
						  <th>'.$row->BODY_NO .'</th>
						  <th>ISUZU</th>
						  <th>'.$row->GVW.'</th>
						  <th>'.$row->COLOR.'</th>
					  </tr>';
		}

		$html = '<table border="0" style="padding: 4px 2px;">
                    <tr style="text-align: left; font-size: 12px; font-weight: normal;">
                        <th style="width: 150px;">Control Number :</th>
                        <th style="width: 300px;">'.$post['lot_no'].'</th>
                    </tr>
                    <tr style="text-align: left; font-size: 12px; font-weight: normal;">
                        <th style="width: 150px;">Accredited Operator :</th>
                        <th style="width: 100px;"></th>
                    </tr>
                  </table>';

        $html .= '<br><br><table border="1" style="padding: 4px 2px;">
                    <tr style="text-align: center; font-size: 10px; font-weight: normal; background-color:#DCDBDC">
                        <th style="width: 70px;">CS Number</th>
                        <th style="width: 60px;">Engine Series</th>
                        <th style="width: 70px;">Engine Number</th>
                        <th style="width: 70px;">Fuel Type</th>
                        <th style="width: 50px;">Cylinder</th>
                        <th style="width: 50px;">Displacement</th>
                        <th style="width: 70px;">CP NO</th>
                        <th style="width: 70px;">CP Date</th>
                        <th style="width: 70px;">Informal Entry No.</th>
                        <th style="width: 125px;">Chassis Number</th>
                        <th style="width: 70px;">Body No</th>
                        <th style="width: 70px;">Make</th>
                        <th style="width: 70px;">GVW</th>
                        <th style="width: 100px;">Color</th>
                    </tr>
               
                    	'.$data.'	
                    
                  </table>';

		$this->load->library('Pdf_buyoff');

        $pdf = new Pdf_buyoff(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('Buyoff Report');
        $pdf->SetSubject('Buyoff Report');
        $pdf->SetKeywords('Buyoff Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '40', '2');
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

        $pdf->Output("buyoff-" . date('Ymdhis') . ".pdf",'I');
	}

	public function completion_report()
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

	public function generate_vehicle_completion_report()
	{
		$post = $this->input->post();
		

		$date = $post['month'].'/01/'.$post['year'];
		$from =  date("d/M/Y", strtotime($date));
		$to =  date("t/M/Y", strtotime($date));

		$params = array(
				'from' => $from,
				'to' => $to
			);
		// echo "<pre>";
		// print_r($params);
		// echo "</pre>";
		// exit();

		$data = $this->buyoff_model->get_vehicle_completion($params);

		$html .= '<br><br><table border="1" style="padding: 4px 2px;">
                    <tr style="text-align: center; font-size: 10px; font-weight: normal; background-color:#DCDBDC">
                        <th style="width: 70px;">CS Number</th>
                        <th style="width: 60px;">Engine Series</th>
                        <th style="width: 70px;">Engine Number</th>
                        <th style="width: 70px;">Fuel Type</th>
                        <th style="width: 50px;">Cylinder</th>
                        <th style="width: 50px;">Displacement</th>
                        <th style="width: 70px;">CP NO</th>
                        <th style="width: 70px;">CP Date</th>
                        <th style="width: 70px;">Informal Entry No.</th>
                        <th style="width: 125px;">Chassis Number</th>
                        <th style="width: 70px;">Body No</th>
                        <th style="width: 70px;">Make</th>
                        <th style="width: 70px;">GVW</th>
                        <th style="width: 100px;">Color</th>
                    </tr>
               
                    	'.$data.'	
                    
                  </table>';
		
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();

        $this->load->library('Pdf_buyoff');

        $pdf = new Pdf_buyoff(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('Buyoff Report');
        $pdf->SetSubject('Buyoff Report');
        $pdf->SetKeywords('Buyoff Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '40', '2');
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

        $pdf->Output("buyoff-" . date('Ymdhis') . ".pdf",'I');
		
	}

	public function display_prooflist_form(){
		$data = array(
				'content'     => 'buyoff_prooflist_view',
				'title'       => 'Buyoff Prooflist Report'
			);

        $this->load->view('include/template', $data);
	}
	public function generate_prooflist()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 7200);

		$this->load->library('Pdf_prooflist');

		$date_from = date('d-M-y', strtotime($this->input->post('date_from')));
		$date_to   = date('d-M-y', strtotime($this->input->post('date_to')));

		$prooflists = $this->buyoff_model->fetch_buyoff_prooflist(array($date_from, $date_to));

		$content = '';

		$model = '';

		$total = 0;

		$config = array();

		$count = 0;

		$s = 0;

		for($j = 0; $j < count($prooflists); $j++)
		{
			$content .= '<tr style="font-size: 7px; text-align: center;" >';

			if ($model != $prooflists[$j]['MODEL']  )
			{
				$model = $prooflists[$j]['MODEL'];

				// Get the model count
				for($i = $s; $i < count($prooflists); $i++)
				{
					if($model != $prooflists[$i]['MODEL'])
					{
						$s = $i;
						break;
					}
					$count++;
				}

				if ((floor($count / 2) - 1) == $total)
				{
					$content .= '<td style=" border-top: 1px thin black;width: 120px; border-left: 1px thin black; border-right: 1px thin black;">' .$prooflists[$j]['MODEL']. '</td>';
				}
				else if($count == 1)
				{
					$content .= '<td style=" border-top: 1px thin black;width: 120px; border-left: 1px thin black; border-right: 1px thin black;">' .$prooflists[$j]['MODEL']. '</td>';
				}
				else
				{
					$content .= '<td style=" width: 120px; border-left: 1px thin black; border-right: 1px thin black;"></td>';
				}

				$total = 1;
			}
			else {
				if ((floor($count / 2) - 1)  == $total)
				{
					$content .= '<td style=" border-top: 1px thin black;width: 120px; border-left: 1px thin black; border-right: 1px thin black;">' .$prooflists[$j]['MODEL']. '</td>';
				}
				else 
				{
					$content .= '<td style=" width: 120px; border-left: 1px thin black; border-right: 1px thin black;"></td>';
				}

				$total++;
			}

			
			$content .= '<td style="width: 50px; border-top: 1px thin black; border-left: 1px thin black;">' . $prooflists[$j]['JOB_NO'] .'</td>';
			$content .= '<td style="width: 50px; border-top: 1px thin black;">' . $prooflists[$j]['CS_NO'] .'</td>';
			$content .= '<td style="width: 50px; border-top: 1px thin black;">' . $prooflists[$j]['LOT_NUMBER'] .'</td>';
			$content .= '<td style="width: 110px; border-top: 1px thin black;">' . $prooflists[$j]['CHASSIS_NO'] .'</td>';
			$content .= '<td style="width: 50px; border-top: 1px thin black;">' . $prooflists[$j]['BODY_NO'] .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . $prooflists[$j]['ENGINE_NO'] .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . $prooflists[$j]['ENGINE_MODEL'] .'</td>';
			$content .= '<td style="width: 50px; border-top: 1px thin black;">' . $prooflists[$j]['KEY_NUMBER'] .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . $prooflists[$j]['COLOR'] .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . date('d-M-Y', strtotime($prooflists[$j]['BUYOFF_DATE'])) .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . $prooflists[$j]['AIRCON_NO'] .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . $prooflists[$j]['AIRCON_BRAND'] .'</td>';
			$content .= '<td style="border-top: 1px thin black;">' . $prooflists[$j]['STEREO_NO'] .'</td>';
			$content .= '<td style="border-top: 1px thin black; border-right: 1px thin black;">' . $prooflists[$j]['STEREO_BRAND'] .'</td>';
			$content .= '</tr>';

			if ($model != ($model1 = isset($prooflists[$j+1]['MODEL']) ? $prooflists[$j+1]['MODEL'] : ''))
			{
				array_push($config, array('model' => $model, 'total' => $total));

				$content .= '<tr style="text-align: right; padding-bottom: 1em;">';
					$content .= '<td  style="border-top: 1px thin black;border-top: 1px thin black;"></td>';
					$content .= '<td  style="border-top: 1px thin black;" colspan = "12"></td>';
					$content .= '<td style="border-top: 1px thin black;background-color: #ccc;font-size: 7px; font-weight: bold;">Total Count:</td>';
					$content .= '<td style="border-top: 1px thin black;background-color: #ccc;font-size: 7px; font-weight: bold; ">' . $total . '</td>';	
				$content .= '</tr>';
				$content .= '<tr>
								<td colspan="15">&nbsp;</td>
							</tr>';
				

				$total = 0;

				$count = 0;
			}
		}

		$html = '<table border="0" style="padding: 4px 2px;">
					<thead>
		                <tr style="font-size: 7px; font-weight: normal; text-align: center; font-weight: bold">
		                    <th border="1" style="width: 120px;">MODEL</th>
		                    <th border="1" style="width: 50px;">JOB NO</th>
		                    <th border="1" style="width: 50px;">CS NO</th>
		                    <th border="1" style="width: 50px;">LOT NO</th>
		                    <th border="1" style="width: 110px;">CHASIS NO</th>
		                    <th border="1" style="width: 50px;">BODY NO</th>
		                    <th border="1">ENGINE MODEL</th>
		                    <th border="1">ENGINE NO</th>
		                    <th border="1" style="width: 50px;">KEY NO</th>
		                    <th border="1">BODY COLOR</th>
		                    <th border="1">BUYOFF DATE</th>
		                    <th border="1">AIRCON NO</th>
		                    <th border="1">AIRCON BRAND</th>
		                    <th border="1">STEREO NO</th>
		                    <th border="1">STEREO BRAND</th>
		                </tr>
	                </thead>
	                <tbody style="text-align: center;">
	                	' . $content . '
	                </tbody>
	              </table>';

	    $count = array_column($config, 'total');
		$total = array_sum($count);

		$content1 = '';

		foreach($config as $row)
		{
			$content1 .= '<tr style="font-size: 7px; vertical-align: middle;">';
				$content1 .= '<td>' .$row['model']. '</td>';
				$content1 .= '<td>' .$row['total']. '</td>';
			$content1 .= '</tr>';
		}

		$content1 .= '<tr>';
			$content1 .= '<td> TOTAL BUY-OFF</td>';
			$content1 .= '<td>' . $total .'</td>';
		$content1 .= '</tr>';

		$html .= '<table border="1" style="padding: 4px 2px;">
					<tr style="font-size: 7px; font-weight: normal;">
						<td style="width: 150px;">MODEL</td>
						<td style="width: 150px;">NO OF UNITS</td>
					</tr>' 
					.$content1. 
				'</table>';

		$html .= '<p style="text-align: center">This is a system generated report.</p>';

		$pdf = new Pdf_prooflist(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('Buyoff Prooflist Report');
        $pdf->SetSubject('Buyoff Prooflist Report');
        $pdf->SetKeywords('Buyoff Prooflist Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '40', '2');
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

        $date = 'Buyoff Date from ' . date('M d, Y', strtotime($date_from)) . ' to ' . date('M d, Y', strtotime($date_to));
        $pdf->setBuyoffDate($date);

        $pdf->AddPage('L', 'A4');
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $pdf->Output("prooflist-" . date('m-d-Y') . ".pdf",'I');
	}

}
