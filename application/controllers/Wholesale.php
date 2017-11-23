<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wholesale extends CI_Controller {
	
	public function __construct(){
		parent::__construct();

		$this->load->model('wholesale_model');
		session_check();
	}
	
	public function ws_executive_report_form(){
		$data['content'] = 'report_form/ws_executive_report_form';
		$data['title'] = 'Executive Report Form';
		$this->load->view('include/template',$data);
	}
	
	public function ws_executive_report(){
		//~ die();
		//~ $prev_start_date = '01-OCT-17';
		//~ $prev_end_date = '31-OCT-17';
		//~ $curr_start_date = '01-NOV-17';
		//~ $curr_end_date = '30-NOV-17';
		//~ $working_days1 = 10;
		//~ $working_days2 = 10;
		
		//~ echo $this->input->post('prev_from');die();
		
		$prev_start_date = date('d-M-y', strtotime($this->input->post('prev_from')));
		$prev_end_date = date('d-M-y', strtotime($this->input->post('prev_to')));
		$curr_start_date = date('d-M-y', strtotime($this->input->post('curr_from')));
		$curr_end_date = date('d-M-y', strtotime($this->input->post('curr_to')));
		$working_days1 = $this->input->post('prev_wd');
		$working_days2 = $this->input->post('curr_wd');
		
		
		
		$params = array(
						$prev_start_date,
						$prev_end_date,
						$curr_start_date,
						$curr_end_date
					);
		
		$rows = $this->wholesale_model->get_ws_executive_summary($params);
		$row_span = $this->wholesale_model->get_ws_executive_summary_count($params);
		$end_date_invoice = $this->wholesale_model->get_invoiced_for_curr_day($curr_end_date);
		
		$rs = array();
		$ctr = 0;
		foreach($row_span as $rowspan){
			$rs[$ctr] = $rowspan->CNT;
			$ctr++;
		}
		
		//~ echo '<pre>';
		//~ print_r($rs);
		//~ echo '</pre>';
		//~ die();
		
		$data = '';
		$variant = '';
		$ctr = 0;
		$cnt = 0;
		foreach($rows as $row){
			
			if($cnt == 35){
				//~ $data .= '<br pagebreak="true" />';
				$cnt = 0;
			}
			
			if(($variant == '' OR $variant != $row->MODEL_VARIANT) AND $row->MODEL_VARIANT != NULL){
				$span = $rs[$ctr] + 1;
				$data .= '<tr nobr="true">
							<td rowspan="'. $span .'" style="border: 1px solid #333;text-align: center;"><strong>'.$row->MODEL_VARIANT.'</strong></td>
							<td style="border: 1px solid #333;">'.$row->SALES_MODELO.'</td>
							<td style="text-align: center;border: 1px solid #333;">'.$row->PREV_INVOICED.'</td>
							<td style="text-align: center;border: 1px solid #333;">'.$row->CURR_INVOICED.'</td>
							<td style="text-align: center;border: 1px solid #333;">'. ($row->CURR_INVOICED - $row->PREV_INVOICED) .'</td>
							<td style="text-align: center;border: 1px solid #333;">'.$row->RESERVED.'</td>
							<td style="text-align: center;border: 1px solid #333;">'.$row->TAGGED.'</td>
							<td style="text-align: center;border: 1px solid #333;">'. ($row->CURR_INVOICED + $row->TAGGED) .'</td>
						</tr>';
				$variant = $row->MODEL_VARIANT;
				$ctr++;
			}
			else if($row->SALES_MODELO == NULL AND $row->MODEL_VARIANT == NULL){
				$data .= '<tr nobr="true" style="font-weight: bold;">
							<td></td>
							<td style="border: 1px solid #333;">Grand Total</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->PREV_INVOICED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->CURR_INVOICED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'. ($row->CURR_INVOICED - $row->PREV_INVOICED) .'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->RESERVED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->TAGGED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'. ($row->CURR_INVOICED + $row->TAGGED) .'</td>
						</tr>';
				$data .= '<tr nobr="true" style="font-weight: bold;">
							<td></td>
							<td style="border: 1px solid #333;">Average</td>
							<td style="border: 1px solid #333;text-align: center;">'. ROUND($row->PREV_INVOICED / $working_days1) .'</td>
							<td style="border: 1px solid #333;text-align: center;">'. ROUND($row->CURR_INVOICED / $working_days2) .'</td>
							<td style="border: 1px solid #333;text-align: center;">'. ROUND(($row->CURR_INVOICED - $row->PREV_INVOICED) / $working_days2).'</td>
							<td ></td>
							<td ></td>
							<td ></td>
						</tr>';
				$data .= '<tr nobr="true" style="font-weight: bold;">
							<td></td>
							<td style="border: 1px solid #333;">Invoiced of '.$curr_end_date.'</td>
							<td style="border: 1px solid #333;text-align: center;">'. $end_date_invoice->CNT .'</td>
							<td style="border: 1px solid #333;text-align: center;"></td>
							<td style="border: 1px solid #333;text-align: center;"></td>
							<td ></td>
							<td ></td>
							<td ></td>
						</tr>';
			}
			else{
				$style = $row->SALES_MODELO == NULL ? 'font-weight: bold;':'';
				$data .= '<tr nobr="true" style="'.$style.'">
							<td style="border: 1px solid #333;">'.$row->SALES_MODELO.'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->PREV_INVOICED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->CURR_INVOICED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'. ($row->CURR_INVOICED - $row->PREV_INVOICED) .'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->RESERVED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'.$row->TAGGED.'</td>
							<td style="border: 1px solid #333;text-align: center;">'. ($row->CURR_INVOICED + $row->TAGGED) .'</td>
						</tr>';
			}
			
			$cnt++;
		}
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 9px;">
						<tr>
							<td colspan="8" style="font-size: 12px;"><strong>Executive Report</strong></td>
						</tr>
						<tr>
							<td colspan="8" style="font-size: 12px;"><strong>Wholesale Comparison</strong></td>
						</tr>
						<tr>
							<td colspan="8" style="font-size: 12px;"><strong>All Dealer Summary</strong></td>
						</tr>
						<tr>
							<td width="270px" colspan="2"></td>
							<td width="200px" colspan="3" style="border: 1px solid #333;text-align: center"><strong>Working Days</strong></td>
							<td colspan="3"></td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td  width="70px" colspan="1" style="border: 1px solid #333;text-align: center;">'.$working_days1.'</td>
							<td  width="65px" colspan="1" style="border: 1px solid #333;text-align: center;">'.$working_days2.'</td>
							<td  width="65px" colspan="1" style="border: 1px solid #333;text-align: center;">'.$working_days2.'</td>
							<td colspan="3"></td>
						</tr>
						<tr style="font-weight: bold;">
							<td width="90px" style="text-align: center;border-top: 1px solid white;"></td>
							<td width="180px"></td>
							<td width="70px" style="text-align: center;border: 1px solid #333;">'. STRTOUPPER(date('M d',strtotime($prev_start_date)).'-'.date('d',strtotime($prev_end_date))) .' </td>
							<td width="65px" style="text-align: center;border: 1px solid #333;">'. STRTOUPPER(date('M d',strtotime($curr_start_date)).'-'.date('d',strtotime($curr_end_date))) .' </td>
							<td width="65px" style="text-align: center;border: 1px solid #333;">% (+ / -)</td>
							<td width="65px" style="text-align: center;border: 1px solid #333;">Reserved</td>
							<td width="65px" style="text-align: center;border: 1px solid #333;">Tagged</td>
							<td width="75px" style="text-align: center;border: 1px solid #333;">Projected WS</td>
						</tr>
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
					</table>';
		
		$this->pdf($content);
	}
	
	public function ws_summary_report(){

		$year = $this->uri->segment(3);
		
		$rows = $this->wholesale_model->get_ws_summary($year);
		//~ $rows = array();
		
		$data = '';
		$variant = '';
		$ctr = 0;
		$cnt = 0;
		
		$jan = 0;
		$feb = 0;
		$mar = 0;
		$apr = 0;
		$may = 0;
		$jun = 0;
		$jul = 0;
		$aug = 0;
		$sep = 0;
		$oct = 0;
		$nov = 0;
		$dec = 0;
		
		foreach($rows as $row){
			if($row->MODEL_VARIANT != NULL AND $row->SALES_MODEL == NULL){
				$data .= '<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td style="text-align: right;border: 0.1px solid #333;">'.$row->MODEL_VARIANT.' Total : </td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->JAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->FEB.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAR.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->APR.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAY.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->JUN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->JUL.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->AUG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->SEP.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->OCT.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->NOV.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->DEC.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->YTD.'</td>
						</tr>';
			}
			else if($row->MODEL_VARIANT == NULL AND $row->SALES_MODEL == NULL){
				$data .= '<tr>
							<td colspan="14" style="text-align: right;">&nbsp;</td>
						</tr>';
				$data .= '<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td style="text-align: right;border: 0.0.1px solid #333;">Grand Total : </td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->JAN.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->FEB.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->MAR.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->APR.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->MAY.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->JUN.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->JUL.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->AUG.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->SEP.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->OCT.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->NOV.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->DEC.'</td>
							<td style="text-align: center;border: 0.0.1px solid #333;">'.$row->YTD.'</td>
						</tr>';
				$jan = $row->JAN;
				$feb = $jan + $row->FEB;
				$mar = $feb + $row->MAR;
				$apr = $mar + $row->APR;
				$may = $apr + $row->MAY;
				$jun = $may + $row->JUN;
				$jul = $jun + $row->JUL;
				$aug = $jul + $row->AUG;
				$sep = $aug + $row->SEP;
				$oct = $sep + $row->OCT;
				$nov = $oct + $row->NOV;
				$dec = $nov + $row->DEC;
			}
			else if($row->MODEL_VARIANT != NULL AND $row->SALES_MODEL != NULL){
				$data .= '<tr>
							<td style="border: 0.1px solid #333;">'.$row->SALES_MODEL.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->JAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->FEB.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAR.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->APR.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAY.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->JUN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->JUL.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->AUG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->SEP.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->OCT.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->NOV.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->DEC.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->YTD.'</td>
						</tr>';
			}
		}
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 8px;">
						<tr>
							<td colspan="14" style="font-size: 12px;"><strong>Wholesale Summary Report</strong></td>
						</tr>
						<tr>
							<td colspan="14" style="font-size: 10px;">For the Year '.$year.' (Cumulative)</td>
						</tr>
						<tr>
							<td colspan="14" style="font-size: 12px;">&nbsp;</td>
						</tr>
						<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td width="180px" style="text-align: center;border: 0.1px solid #333;">Model</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Jan</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Feb</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Mar</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Apr</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">May</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Jun</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Jul</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Aug</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Sep</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Oct</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Nov</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">Dec</td>
							<td width="37px" style="text-align: center;border: 0.1px solid #333;">YTD</td>
						</tr>
						'.$data.'
						<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td style="text-align: right;border: 0.1px solid #333;">Grand Cumulative Total : </td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$jan.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$feb.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$mar.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$apr.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$may.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$jun.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$jul.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$aug.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$sep.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$oct.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$nov.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$dec.'</td>
						</tr>
						<tr>
							<td colspan="14" style="text-align: right;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="14" style="text-align: right;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="14" style="text-align: right;font-size: 12px;"><i>System Generated Report</i></td>
						</tr>
					</table>';
		
		$this->pdf($content);
	}
	
	public function pdf($content){
		// generate pdf content
		$this->load->library('pdf');
		// create new PDF document
		$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
		
		$pdf->AddPage();
		// output the HTML content
		$pdf->writeHTML($content, true, false, true, false, '');
		
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$this->filename = 'report.pdf';
		$pdf->Output($this->filename,'I');
	}
}
