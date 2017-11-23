<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Available_units_r extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('reports/available_units_model_r');
		session_check();
	}
	
	public function available_to_tag_excel(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);

		$data = $this->available_units_model_r->get_available_to_tag();
		$this->load->library('excel');

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
		$objPHPExcel = PHPExcel_IOFactory::load(APPPATH."controllers/reports/excel_template/available_to_tag.xlsx");
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.'J1')->applyFromArray($styleArray_header);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.'J'.$row)->applyFromArray($styleArray);
		
		$filename='available_to_tag.xlsx';
		$this->excel($objPHPExcel,$filename);
	}
	
	public function available_units_excel(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);

		$data = $this->available_units_model_r->get_available_units();
		$this->load->library('excel');

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
		$objPHPExcel = PHPExcel_IOFactory::load(APPPATH."controllers/reports/excel_template/available_to_tag.xlsx");
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.'J1')->applyFromArray($styleArray_header);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.'J'.$row)->applyFromArray($styleArray);
		
		$filename='available_units.xlsx';
		$this->excel($objPHPExcel,$filename);
	}
	
	public function available_units_summary_pdf(){
		
		//~ ini_set('memory_limit', '-1');
		//~ ini_set('max_execution_time', 3600);
		
		$rows = $this->available_units_model_r->get_available_summary();
		//~ $rows = array();
		
		$data = '';
		$sales_model = '';
		foreach($rows as $row){
			
			if($row->SALES_MODEL != NULL AND $row->BODY_COLOR != NULL){
				$data .= '<tr>
							<td width="230px" style="border: 0.1px solid #333;">'.$row->SALES_MODEL.'</td>
							<td width="140px" style="border: 0.1px solid #333;">'.$row->BODY_COLOR.'</td>
							<td width="100px" align="center" style="border: 0.1px solid #333;">'.$row->IVP_QTY.'</td>
							<td width="100px" align="center" style="border: 0.1px solid #333;">'.$row->IVS_QTY.'</td>
							<td width="100px" align="center" style="border: 0.1px solid #333;">'.$row->QTY.'</td>
						  </tr>';
			}
			else if($row->SALES_MODEL != NULL AND $row->BODY_COLOR == NULL){
				$data .= '<tr style="font-weight: bold;">
							<td width="370px" colspan="2" align="right" >&nbsp;</td>
								<td width="100px" colspan="1" align="center" >'.$row->IVP_QTY.'</td>
								<td width="100px" colspan="1" align="center" >'.$row->IVS_QTY.'</td>
								<td width="100px" colspan="1" align="center" >'.$row->QTY.'</td>
						  </tr>
						  <tr>
								<td colspan="3" style="font-size: 10px;">&nbsp;</td>
						</tr>';
			}
			else if($row->SALES_MODEL == NULL AND $row->BODY_COLOR == NULL){
				$data .= '<tr>
								<td colspan="3" style="font-size: 10px;">&nbsp;</td>
							</tr>
							<tr style="font-weight: bold;background-color: #CCC;">
								<td width="370px" colspan="2" align="right" >Grand Total Count</td>
								<td width="100px" colspan="1" align="center" >'.$row->IVP_QTY.'</td>
								<td width="100px" colspan="1" align="center" >'.$row->IVS_QTY.'</td>
								<td width="100px" colspan="1" align="center" >'.$row->QTY.'</td>
							</tr>';
			}
			
		}
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 9px;">
						<thead>
							<tr>
								<td colspan="5" style="font-size: 12px;"><strong>Available Units Summary</strong></td>
							</tr>
							<tr>
								<td colspan="5" style="font-size: 10px;">As of '.date('F d, Y').'</td>
							</tr>
							<tr>
								<td colspan="5" style="font-size: 12px;">&nbsp;</td>
							</tr>
							<tr style="font-weight: bold;background-color: #D3D3D3;">
								<th width="230px" style="border: 0.1px solid #333;">Model</th>
								<th width="140px" style="border: 0.1px solid #333;">Body Color</th>
								<th width="100px" align="center" style="border: 0.1px solid #333;">IVP</th>
								<th width="100px" align="center" style="border: 0.1px solid #333;">IVS</th>
								<th width="100px" align="center" style="border: 0.1px solid #333;">Total Qty</th>
							</tr>
						</thead>
						<tbody>
							'.$data.'
							<tr>
								<td colspan="5" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="5" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="5" style="text-align: right;font-size: 12px;"><i>System Generated Report</i></td>
							</tr>
						</tbody>
					</table>';
		
		$this->pdf($content);
	}
	
	public function excel($content, $filename){
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	
	public function pdf($content){
		
		// generate pdf content
		$this->load->library('pdf');
		// create new PDF document
		$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
		
		$pdf->AddPage();
		// output the HTML content
		$pdf->writeHTML($content, true, false, true, false, '');
		
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$this->filename = 'Available-Units.pdf';
		$pdf->Output($this->filename,'I');
	}
}
