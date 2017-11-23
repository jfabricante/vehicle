<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagged_r extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('reports/tagged_model_r');
		session_check();
	}
	
	public function tagged_units_detailed_form(){
		
		$data = array(
				'content'     => 'report_form/tagged_units_detailed_form',
				'title'       => 'Tagged Units Detailed Report',
				'dealers'	  => $this->tagged_model_r->get_vehicle_dealers()
			);

			$this->load->view('include/template', $data);
	}
	
	public function OC_detailed_form(){
		
		$data = array(
				'content'     => 'report_form/oc_detailed_form',
				'title'       => 'OC Detailed Report',
				'dealers'	  => $this->tagged_model_r->get_vehicle_dealers()
			);

			$this->load->view('include/template', $data);
	}
	
	public function tagged_units_detailed_pdf(){
		
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);
		
		$rows = $this->input->post('customer_id');
		
		if($rows[0] == 1){
			$customer_id = NULL;
		}
		else{
			$customer_id =  implode( ", ", $rows);
		}
		
		$rows = $this->tagged_model_r->get_tagged_units_detailed($customer_id);
		
		$data = '';
		$ctr = 0;
		foreach($rows as $row){
			
			if($ctr == 0 AND $row->CUST_ACCOUNT_ID != NULL AND $row->CS_NUMBER != NULL){
				$data .= '<tr>
							<td colspan="13" style="font-size: 9px;"><strong>'.$row->ACCOUNT_NAME.'</strong></td>
						</tr>
						<tr style="font-weight: bold;background-color: #D3D3D3;text-align: center;">
								<th width="50px" style="border: 0.1px solid #333;">CS Number</th>
								<th width="60px" style="border: 0.1px solid #333;">Order Number</th>
								<th width="40px" style="border: 0.1px solid #333;">Line Number</th>
								<th width="130px" style="border: 0.1px solid #333;">Sales Model</th>
								<th width="110px" style="border: 0.1px solid #333;">Body Color</th>
								<th width="90px" style="border: 0.1px solid #333;">Chassis Number</th>
								<th width="60px" style="border: 0.1px solid #333;">Engine Number</th>
								<th width="100px" style="border: 0.1px solid #333;">Lot Number</th>
								<th width="80px" style="border: 0.1px solid #333;">Tagged Date</th>
								<th width="70px" style="border: 0.1px solid #333;">Amount</th>
								<th width="50px" style="border: 0.1px solid #333;">Paymet Terms</th>
								<th width="50px" style="border: 0.1px solid #333;">Aging</th>
								<th width="90px" style="border: 0.1px solid #333;">CSR Number</th>
							</tr>';
				$ctr++;
			}
			
			if($row->CUST_ACCOUNT_ID != NULL AND $row->CS_NUMBER != NULL){
				$data .= '<tr>
							<td align="center" width="50px" style="border: 0.1px solid #333;">'.$row->CS_NUMBER.'</td>
							<td align="center" width="60px" style="border: 0.1px solid #333;">'.$row->ORDER_NUMBER.'</td>
							<td align="center"  width="40px" style="border: 0.1px solid #333;">'.$row->LINE_NUMBER.'</td>
							<td width="130px" style="border: 0.1px solid #333;">'.$row->SALES_MODEL.'</td>
							<td width="110px" style="border: 0.1px solid #333;">'.$row->BODY_COLOR.'</td>
							<td width="90px" style="border: 0.1px solid #333;">'.$row->CHASSIS_NUMBER.'</td>
							<td width="60px" style="border: 0.1px solid #333;">'.$row->ENGINE_NUMBER.'</td>
							<td width="100px" style="border: 0.1px solid #333;">'.$row->LOT_NUMBER.'</td>
							<td align="center" width="80px" style="border: 0.1px solid #333;">'.date('m/d/Y', strtotime($row->TAGGED_DATE)).'</td>
							<td width="70px" style="border: 0.1px solid #333;">'.$row->AMOUNT.'</td>
							<td align="center" width="50px" style="border: 0.1px solid #333;">'.$row->PAYMENT_TERMS.'</td>
							<td align="center" width="50px" style="border: 0.1px solid #333;">'.$row->AGING.'</td>
							<td align="center" width="90px" style="border: 0.1px solid #333;">'.$row->CSR_NUMBER.'</td>
						</tr>';
			}
			else if($row->CUST_ACCOUNT_ID != NULL AND $row->CS_NUMBER == NULL){
				$data .= '<tr style="font-weight: bold;">
							<td width="780px" colspan="9" align="center" >&nbsp;</td>
							<td width="100px" colspan="2" align="center" >Total : '.$row->CNT.'</td>
							<td width="100px" colspan="2" align="center" >Units with CSR : '.$row->CNT_CSR.'</td>
						</tr>
						<tr>
							<td colspan="13">&nbsp;</td>
						</tr>';
				$ctr = 0;
			}
		}
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 7px;">
						<thead>
							<tr>
								<td colspan="13" style="font-size: 12px;"><strong>Tagged Units Detailed Report</strong></td>
							</tr>
							<tr>
								<td colspan="13" style="font-size: 10px;">As of '.date('F d, Y').'</td>
							</tr>
							<tr>
								<td colspan="13" style="font-size: 12px;">&nbsp;</td>
							</tr>
						</thead>
						<tbody>
							'.$data.'
							<tr>
								<td colspan="13" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="13" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="13" style="text-align: right;font-size: 12px;"><i>System Generated Report</i></td>
							</tr>
						</tbody>
					</table>';
		
		$filename = 'tagged_units_detailed';
		$this->pdf($content,$filename,'L');
	
	}
	
	public function oc_detailed_pdf(){
		
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);
		
		//~ $rows = $this->input->post('customer_id');
		
		//~ if($rows[0] == 1){
			//~ $customer_id = NULL;
		//~ }
		//~ else{
			//~ $customer_id =  implode( ", ", $rows);
		//~ }
		
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);
		
		$rows = $this->input->post('customer_id');
		
		if($rows[0] == 1){
			$customer_id = NULL;
		}
		else{
			$customer_id =  implode( ", ", $rows);
		}
		
		$rows = $this->tagged_model_r->get_tagged_units_detailed($customer_id);
		
		$from = date('d-M-y', strtotime($this->input->post('from')));
		$to = date('d-M-y', strtotime($this->input->post('to')));
		
		$rows = $this->tagged_model_r->get_oc_detailed($from, $to, $customer_id);
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		//~ die();
		
		$data = '';
		$ctr = 0;
		foreach($rows as $row){
			
			if($ctr == 0  AND $row->ACCOUNT_NAME != NULL){
				$data .= '<tr>
							<td colspan="13" style="font-size: 9px;"><strong>'.$row->ACCOUNT_NAME.'</strong></td>
						</tr>
						<tr style="font-weight: bold;background-color: #D3D3D3;text-align: center;">
								<th width="160px" style="border: 0.1px solid #333;">Model</th>
								<th width="110px" style="border: 0.1px solid #333;">Body Color</th>
								<th width="70px" style="border: 0.1px solid #333;">Order Number</th>
								<th width="55px" style="border: 0.1px solid #333;">Line Number</th>
								<th width="65px" style="border: 0.1px solid #333;">Invoice Number</th>
								<th width="55px" style="border: 0.1px solid #333;">CS Number</th>
								<th width="75px" style="border: 0.1px solid #333;">Tagged Date</th>
								<th width="75px" style="border: 0.1px solid #333;">OC Date</th>
							</tr>';
				$ctr++;
			}
			
			if($row->ORDER_NUMBER != NULL AND $row->LINE_NUMBER != NULL){
				$data .= '<tr>
							<td align="left" width="160px" style="border: 0.1px solid #333;">'.$row->SALES_MODEL.'</td>
							<td align="left" width="110px" style="border: 0.1px solid #333;">'.$row->BODY_COLOR.'</td>
							<td align="center" width="70px" style="border: 0.1px solid #333;">'.$row->ORDER_NUMBER.'</td>
							<td align="center" width="55px" style="border: 0.1px solid #333;">'.$row->LINE_NUMBER.'</td>
							<td align="center" width="65px" style="border: 0.1px solid #333;">'.$row->TRX_NUMBER.'</td>
							<td align="center" width="55px" style="border: 0.1px solid #333;">'.$row->CS_NUMBER.'</td>
							<td align="center" width="75px" style="border: 0.1px solid #333;">'.date('m/d/Y', strtotime($row->TAGGED_DATE)).'</td>
							<td align="center" width="75px" style="border: 0.1px solid #333;">'.date('m/d/Y', strtotime($row->OC_DATE)).'</td>
						</tr>';
			}
			else if($row->ORDER_NUMBER == NULL AND $row->LINE_NUMBER == NULL AND $row->BODY_COLOR == NULL AND $row->SALES_MODEL != NULL AND $row->MODEL_VARIANT != NULL){
				$data .= '<tr>
							<td width="100px" align="left" >Count : '.$row->CNT.'</td>
							<td width="100px" align="left" >w/ Allocation : '.$row->CNT_W_TAGGED.'</td>
							<td width="100px" align="left" >w/o Allocation : '.$row->CNT_WO_TAGGED.'</td>
						</tr>
						<tr>
							<td colspan=""></td>
						</tr>';
			}
			else if($row->ORDER_NUMBER == NULL AND $row->LINE_NUMBER == NULL AND $row->BODY_COLOR == NULL AND $row->SALES_MODEL == NULL AND $row->MODEL_VARIANT == NULL AND $row->ACCOUNT_NAME != NULL ){
				$data .= '<tr>
							<td width="100px" align="left" >Total Count : '.$row->CNT.'</td>
							<td width="100px" align="left" >Total w/ Allocation : '.$row->CNT_W_TAGGED.'</td>
							<td width="100px" align="left" >Total w/o Allocation : '.$row->CNT_WO_TAGGED.'</td>
						</tr>
						<tr>
							<td colspan=""></td>
						</tr>';
				$ctr = 0;
			}
			
			else if($row->ORDER_NUMBER == NULL AND $row->LINE_NUMBER == NULL AND $row->BODY_COLOR == NULL AND $row->SALES_MODEL == NULL AND $row->MODEL_VARIANT == NULL AND $row->ACCOUNT_NAME == NULL ){
				$data .= '<tr>
							<td width="120px" align="left" >Grand Total Count : '.$row->CNT.'</td>
							<td width="120px" align="left" >Grand Total w/ Allocation : '.$row->CNT_W_TAGGED.'</td>
							<td width="120px" align="left" >Grand Total w/o Allocation : '.$row->CNT_WO_TAGGED.'</td>
						</tr>
						<tr>
							<td colspan=""></td>
						</tr>';
			}
			
		}
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 7px;">
						<thead>
							<tr>
								<td colspan="8" style="font-size: 12px;"><strong>Order Confirmation Detailed Report</strong></td>
							</tr>
							<tr>
								<td colspan="8" style="font-size: 10px;">OC Date From '.date('m/d/Y', strtotime($from)).' To '.date('m/d/Y', strtotime($to)).'</td>
							</tr>
							<tr>
								<td colspan="8" style="font-size: 12px;">&nbsp;</td>
							</tr>
						</thead>
						<tbody>
							'.$data.'
							<tr>
								<td colspan="8" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="8" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="8" style="text-align: right;font-size: 12px;"><i>System Generated Report</i></td>
							</tr>
						</tbody>
					</table>';
		
		$filename = 'oc_detailed_report';
		$this->pdf($content, $filename);
	
	}
	
	public function excel($content, $filename){
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	
	public function pdf_f($content, $filename, $orientation = 'P'){
		
		// generate pdf content
		$this->load->library('pdf');
		// create new PDF document
		$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Isuzu');
		$pdf->SetTitle('IPC Vehicle Portal');
		$pdf->SetSubject('IPC Vehicle Portal');
		$pdf->SetKeywords('IPC Vehicle Portal');
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
		$pdf->SetheaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
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
		
		if($orientation == 'L'){
			$pdf->AddPage('L');
		}
		else{
			$pdf->AddPage();
		}
		// output the HTML content
		$pdf->writeHTML($content, true, false, true, false, '');
		
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$file = APPPATH."controllers/reports/saved_pdf/".$filename;
		$pdf->Output($file, 'F');
		
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="oc_detailed.pdf"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header('Accept-Ranges: bytes');
		@readfile($file);

	}
	
	public function pdf($content, $filename, $orientation = 'P'){
		
		// generate pdf content
		$this->load->library('pdf');
		// create new PDF document
		$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Isuzu');
		$pdf->SetTitle('IPC Vehicle Portal');
		$pdf->SetSubject('IPC Vehicle Portal');
		$pdf->SetKeywords('IPC Vehicle Portal');
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
		$pdf->SetheaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
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
		
		if($orientation == 'L'){
			$pdf->AddPage('L');
		}
		else{
			$pdf->AddPage();
		}
		// output the HTML content
		$pdf->writeHTML($content, true, false, true, false, '');
		
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$pdf->Output($filename, 'I');
	}
}
