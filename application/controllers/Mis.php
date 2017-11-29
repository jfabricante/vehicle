<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mis extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('mis_model');
		session_check();
	}
	
	public function upload_vin(){
		
		if($_FILES['excel_file']['error'] == 0){
			if (file_exists($_FILES['excel_file']['name'])) {
				unlink($_FILES['excel_file']['name']);
			}
			$file = 'upload//' . time() . '.xlsx';
			if(move_uploaded_file($_FILES['excel_file']['tmp_name'],  $file)){
				$error = $this->read_excel($file);
				redirect('mis/vins_list/'.$error);
				
			}
			else{
				echo 'Upload failed.';
			}
		}
	}
	
	public function vins_list(){
		$data['error'] =  (empty($this->uri->segment(3))? NULL:urldecode($this->uri->segment(3))) ;
		$data['content'] = 'vins_list_view';
		$data['title'] = 'Manufacturing Information Sheet';
		$data['result'] = $this->mis_model->get_vins_list();
		$this->load->view('include/template',$data);
	}
	
	public function search(){
		
		$lot_number = ($this->input->post('lot_number') != NULL)? $this->input->post('lot_number'):'';
		//~ $model_name = ($this->input->post('model_name') != NULL)? $this->input->post('model_name'):'';
		$data['lot_numbers'] = $this->mis_model->select_lot_number_dd($lot_number);
		//~ $data['model_names'] = $this->mis_model->select_model_name_dd($lot_number);
		$data['lot_number'] = $lot_number;
		//~ $data['model_name'] = $model_name;
		
		//print_r($data);
		$data['modelList'] = $this->mis_model->fetchModelList();
		
		$data['box_body'] = NULL;
		$data['content'] = 'mis_search_view';
		$data['title'] = 'Manufacturing Information Sheet';
		$this->load->view('include/template',$data);
	}
	
	public function ajax_get_model_names(){
		
		$model_names = $this->mis_model->select_model_names_dd($this->input->post('lot_number'));
		
		$options = "<option value='1'>Nothing Selected</option>";
		
		foreach($model_names as $row){
			$options .= "<option value='" . $row->MODEL_NAME . "'> " . $row->MODEL_NAME . "</option>";
		}
		
		echo $options;
	}
	
	public function ajax_get_mis_units(){
		
		$lot_number = $this->input->post('lot_number');
		$model_name = $this->input->post('model_name');
		
		$data['lot_number'] = $lot_number;
		$data['model_name'] = $model_name;
		$data['result'] = $this->mis_model->select_mis_units($lot_number, $model_name);
		
		//~ print_r($data);
		
		echo $this->load->view('ajax/mis_list',$data,true);
		
	}
	
	public function ajax_msn_details_form(){
		
		$data['mis_id'] = $this->input->post('mis_id');
		$data['serial_no'] = $this->input->post('serial_no');
		$data['lot_no'] = $this->input->post('lot_no');
		$data['model_name'] = $this->input->post('model_name');
		
		
		//~ $data['model_name'] = $model_name;
		$rows = $this->mis_model->select_mis_details($data['mis_id']);
		$data['row'] = $rows[0];
		
		$data['vins'] =  $this->mis_model->select_vin_per_lot($data['lot_no']);
		//~ print_r($data['row']);
		
		echo $this->load->view('ajax/msn_details_form',$data,true);
		
	}
	
	public function ajax_get_engine(){
		
		$row = $this->mis_model->select_engine($this->input->post('vin'));
		
		echo $row->ENGINE_NO;
	}
	
	public function ajax_msn_details_submit(){
		
		$post = (object)$this->input->post();
		
		//~ print_r($post);
		
		if($post->cs_no == '' OR $post->vin == ''){
			echo 'required';
		}
		else{
			$fm_date = $post->fm_date == NULL ? NULL : date('d-M-y', strtotime($post->fm_date));
			//~ $buyoff_date = $post->buyoff_date == NULL ? NULL : date('d-M-y', strtotime($post->buyoff_date));
			
			$data = array(
						strtoupper($post->cs_no), 
						strtoupper(str_replace(' ', '', $post->chassis_no)), 
						strtoupper($post->engine_no), 
						strtoupper($post->body_no), 
						$fm_date,
						$post->remarks,
						strtoupper($post->key_no),
						strtoupper($post->aircon_no), 
						strtoupper($post->stereo_no), 
						NULL,
						$post->last_updated_by,
						$post->mis_id
					);
					
			//~ echo '<pre>';
			//~ print_r($data);
			//~ echo '</pre>';
			//~ die();
			
			if($this->mis_model->update_mis_attributes($data) > 0){
				$this->mis_model->update_is_used_flag(str_replace(' ', '', $post->chassis_no));
				echo 'true';
			}
			else{
				echo 'false';
			}
		}
		
	}
	
	public function save_mis(){
		
		$lot_number = $this->input->post('lot_number');
		$model_name = $this->input->post('model_name');
		$mis = $this->input->post('mis');
		
		echo $lot_number . ' ' . $model_name;
		
		foreach($mis as $row ){
			$row = (object)$row;
			
			if($row->cs_number != "" && $row->vin != ""){
			//	var_dump($row);
				$this->mis_model->update_mis_unit(array($row->cs_number, $row->vin, $row->mis_id));
			}
		}
		
		$data['lot_numbers'] = $this->mis_model->select_lot_number_dd();
		$data['lot_number'] = $lot_number;
		$data['model_name'] = $model_name;
		
		$model_names = $this->mis_model->select_model_names_dd($this->input->post('lot_number'));
		
		if(1 == $model_name){
			$options = "<option selected value='1'>Nothing Selected</option>";
		}
		else{
			$options = "<option value='1'>Nothing Selected</option>";
		}
		
		foreach($model_names as $row){
			if($row->MODEL_NAME == $model_name){
				$options .= "<option selected value='" . $row->MODEL_NAME . "'> " . $row->MODEL_NAME . "</option>";
			}
			else{
				$options .= "<option value='" . $row->MODEL_NAME . "'> " . $row->MODEL_NAME . "</option>";
			}
		}
		
		$data['options'] = $options;
		$data['box_body'] = 1;
		
		$data['result'] = $this->mis_model->select_mis_units($lot_number, $model_name);
		
		$data['content'] = 'mis_search_view';
		$data['title'] = 'Manufacturing Information Sheet';
		$this->load->view('include/template',$data);	
	}
	
	public function print_(){
		
		// generate pdf content
		$this->load->library('Pdf_mis');
		// create new PDF document
		$pdf = new Pdf_mis(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Isuzu');
		$pdf->SetTitle('IPC Portal');
		$pdf->SetSubject('IPC Portal');
		$pdf->SetKeywords('IPC Portal');
		// set default header data
		$pdf->SetheaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->setFooterData(array(0,0,0), array(0,0,0));
		// set header and footer fonts
		$pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT - 5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT - 5);
		$pdf->SetheaderMargin(0);
		$pdf->SetFooterMargin(0);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 0);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		// Add a page
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$mis_id = ($this->uri->segment(3) == 0)? NULL:urldecode($this->uri->segment(3)) ;
		$lot_number = (empty($this->uri->segment(4))? NULL:urldecode($this->uri->segment(4))) ;
		$model_name = (empty($this->uri->segment(5))? NULL:urldecode($this->uri->segment(5))) ;
		
		//~ echo $mis_id . '<br />' . $lot_number . '<br />' . $model_name; //die();
		
		$rows = $this->mis_model->select_mis_details($mis_id, $lot_number, $model_name);
		
		//~ echo $lot_number;die();
		
		//~ foreach($rows as $row){
			//~ echo '<pre>';
			//~ print_r($row);
			//~ echo '</pre>';
		//~ }
		
		//~ die();
		
		$content = '';
		foreach($rows as $row){
			$content = '<table border="0" style="font-size: 30px;">
							<tr>
								<td colspan="5" style="border-bottom: 1px solid #000;"><span style="font-size: 15px;">MODEL NAME : </span> '. $row->MODEL_NAME .'</td>
							</tr>
							<tr>
								<td colspan="3">
									<table border="0" style="font-size: 26px;padding: 18px;padding-left:0;">
										<tr>
											<td colspan="2" style="font-size: 15px;">&nbsp;<br/>SERIAL NO : </td>
											<td colspan="6"><span style="font-size: 8px;">&nbsp;<br/></span>'. $row->SERIAL_NO .'</td>
										</tr>
										<tr>
											<td colspan="2" style="font-size: 15px;">&nbsp;<br/>MODEL CODE : </td>
											<td colspan="6"><span style="font-size: 8px;">&nbsp;<br/></span>'. $row->MODEL_CODE .'</td>
										</tr>
										<tr>
											<td colspan="2" style="font-size: 15px;">&nbsp;<br/>LOT NUMBER : </td>
											<td colspan="6"><span style="font-size: 8px;">&nbsp;<br/></span>'. $row->LOT_NUM .'</td>
										</tr>
										<tr>
											<td colspan="8">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="font-size: 15px;">&nbsp;<br/>START DATE : </td>
											<td colspan="6"><span style="font-size: 8px;">&nbsp;<br/></span>'. date('m/d/Y', strtotime($row->START_DATE)) .'</td>
										</tr>
										<tr>
											<td colspan="2" style="font-size: 15px;">&nbsp;<br/>SHOP ORDER : </td>
											<td colspan="6"><span style="font-size: 8px;">&nbsp;<br/></span>'. $row->SHOP_ORDER .'</td>
										</tr>
										<tr>
											<td colspan="2" style="font-size: 15px;">&nbsp;<br/>PLAN ID : </td>
											<td colspan="6"><span style="font-size: 8px;">&nbsp;<br/></span>'. $row->PLAN_ID .'</td>
										</tr>
									</table>
								</td>
								<td colspan="2">
									<table style="font-size: 13px;padding:8px;padding-bottom: 30px;" border="1">
										<tr>
											<td>CS NUMBER</td>
											<td>BODY-ON DATE</td>
										</tr>
										<tr>
											<td>VIN</td>
											<td>FM DATE</td>
										</tr>
										<tr>
											<td>ENGINE NUMBER</td>
											<td>PDI CONTROL NO</td>
										</tr>
										<tr>
											<td>BODY NUMBER</td>
											<td>PDI DATE</td>
										</tr>
										<tr>
											<td>BUYOFF DATE</td>
											<td>WB NUMBER</td>
										</tr>
										<tr>
											<td colspan="2">KEY NUMBER</td>
										</tr>
										<tr>
											<td colspan="2">AIRCON NUMBER / BRAND</td>
										</tr>
										<tr>
											<td colspan="2">BATTERY RATING / SPEC</td>
										</tr>
										<tr>
											<td colspan="2">STEREO NUMBER / BRAND</td>
										</tr>
										<tr>
											<td colspan="2">TIRE BRAND / SIZE</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
		
			// output the HTML content
			
			//~ echo $content;die();
			
			$pdf->AddPage('L', 'A4');
			
			$pdf->writeHTML($content, true, false, true, false, '');
		}
		
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$pdf->Output("MIS.pdf",'I');
	}
	
	public function print_so_report(){
		
		// generate pdf content
		$this->load->library('Pdf_so');
		// create new PDF document
		$pdf = new Pdf_so(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Isuzu');
		$pdf->SetTitle('IPC Portal');
		$pdf->SetSubject('IPC Portal');
		$pdf->SetKeywords('IPC Portal');
		// set default header data
		$pdf->SetheaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->setFooterData(array(0,0,0), array(0,0,0));
		// set header and footer fonts
		$pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT - 5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT - 5);
		$pdf->SetheaderMargin(0);
		$pdf->SetFooterMargin(0);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 0);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		// Add a page
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$lot_number = (empty($this->uri->segment(3))? NULL:urldecode($this->uri->segment(3))) ;
		$model_name = (empty($this->uri->segment(4))? NULL:urldecode($this->uri->segment(4))) ;
		
		//~ echo $mis_id . '<br />' . $lot_number . '<br />' . $model_name; //die();
		
		$rows = $this->mis_model->select_shop_order_report($lot_number, $model_name);
		
		//~ foreach($rows as $row){
			//~ echo '<pre>';
			//~ print_r($row);
			//~ echo '</pre>';
		//~ }
		
		//~ die();
		
		$content = '';
		$order_no = '';
		$lot_num = '';
		$due_date = '';
		$td = '';
		$ctr = 0;
		foreach($rows as $row){
			
			$ctr++;
			
			if($order_no == ''){
				$lot_num = $row->LOT_NUM;;
				$due_date = $row->DUE_DATE;
				$order_no = $row->SHOP_ORDER;
			}
			
			$td .=  '<tr>
						<td style="border-left: 1px solid #555;border-bottom: 1px solid #777;text-align: center;">'.$ctr.'</td>
						<td style="border-bottom: 1px solid #777;text-align: center;">'.$row->SERIAL_NO.'</td>
						<td style="border-bottom: 1px solid #777;text-align: center;">'.$row->SHOP_ORDER.'</td>
						<td style="border-bottom: 1px solid #777;text-align: center;">'.$row->MODEL_NAME.'</td>
						<td style="border-bottom: 1px solid #777;text-align: center;">&nbsp;</td>
						<td style="border-bottom: 1px solid #777;text-align: center;">&nbsp;</td>
						<td style="border-right: 1px solid #555;border-bottom: 1px solid #777;text-align: center;">&nbsp;</td>
					</tr>';
			
		}
		
		$content = '<table style="font-size: 12px;padding:2px;" border="0">
						<tr>
							<td colspan="4"><b>Lot Number</b> : '.$lot_num.'</td>
							<td style="text-align: right;"><b>Due Date : </b></td>
							<td style="text-align: left;">'.date('F m, Y', strtotime($due_date)).'</td>
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
							<td style="text-align: right;"><b>Quantity : </b></td>
							<td style="text-align: left;">'.$ctr.'</td>
						</tr>
						<tr>
							<td colspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6">
								<table style="font-size: 9px;padding:7px 5px;" border="0">
									<tr style="font-size: 11px;background-color: #ccc;font-weight: bold;">
										<td style="border-left: 2px solid #555;border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 30px;">#</td>
										<td style="border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 80px;">Serial Number</td>
										<td style="border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 80px;">Shop Order Number</td>
										<td style="border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 260px;">Model / Body Color</td>
										<td style="border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 70px;">CS Number</td>
										<td style="border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 80px;">Body Number</td>
										<td style="border-right: 2px solid #555;border-top: 2px solid #555;border-bottom: 2px solid #555;text-align: center;width: 80px;">Actual Prod Date</td>
									</tr>
									'.$td.'
								</table>
							</td>
						</tr>
					</table>';
						
		$pdf->AddPage('P', 'A4');
			
		$pdf->writeHTML($content, true, false, true, false, '');
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$pdf->Output("shop_order.pdf",'I');
	}
	
	public function read_excel($inputFileName){

		$this->load->library('excel');
		
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		
		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		$error = '';
		
		//~ echo $highestRow;die();

		for ($row = 2; $row <= $highestRow; $row++){
			
			
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
											NULL,
											TRUE,
											FALSE);

			$chassis_no = empty($rowData[0][0])? '':$rowData[0][0]; // VIN
			$engine_no = empty($rowData[0][2])? '':$rowData[0][1] . $rowData[0][2] . $rowData[0][3]; // ENGINE PREFIX / NUMBER / SUFFIX
			$lot_no = empty($rowData[0][4])? '':$rowData[0][4]; // LOT NUMBER
			
			
			//~ echo 'Vin or engine number already exist. ' . $chassis_no . ' - ' . $engine_no ;
			
			
			$params = array($chassis_no,
							$engine_no,
							$lot_no);
			
			$row_ = $this->mis_model->check_duplicate(array($chassis_no, $engine_no));
			
			//~ echo $row->CNT;
			
			
			if($row_->CNT == 0){
				if($chassis_no != '' AND $engine_no != '' AND $lot_no != ''){			
					$this->mis_model->insert_vin_engine($params);
				}
			}
			else{
				$error .= 'Vin or engine number already exist. ' . $chassis_no . ' - ' . $engine_no . '<br>';
				
			}
			
		}
		
		//~ echo $row;
		//~ die();
		
		return $error;
	}

	public function ajax_get_model_lot()
	{
		$model_lot = $this->mis_model->fetchDistinctLot($this->input->post());

		$config = array();

		foreach ($model_lot as $lot)
		{
			$config[] = array(
					'id'   => $lot->LOT_NO,
					'text' => $lot->LOT_NO
				);
		}

		echo json_encode($config);
	}

	protected function _showVars($var)
	{
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}

}
