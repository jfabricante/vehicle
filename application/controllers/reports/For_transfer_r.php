<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class For_transfer_r extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('reports/for_transfer_model_r');
		session_check();
	}
	
	public function for_transfer_form(){
		
		$data = array(
				'content'     => 'report_form/for_transfer_form',
				'title'       => 'Motorpool Receiving Report'
			);

			$this->load->view('include/template', $data);
	}
	
	public function for_transfer_excel(){

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);

		//~ $from = '20-NOV-17';
		//~ $to = '23-NOV-17';
		$from = date('d-M-y', strtotime($this->input->post('from')));
		$to = date('d-M-y', strtotime($this->input->post('to')));

		$data = $this->for_transfer_model_r->get_received_units($from, $to);
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
		$objPHPExcel = PHPExcel_IOFactory::load(APPPATH."controllers/reports/excel_template/received_units.xlsx");
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.'H1')->applyFromArray($styleArray_header);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.'H'.$row)->applyFromArray($styleArray);
		
		$filename='received_units.xlsx';
		$this->excel($objPHPExcel,$filename);
	}
	
	public function excel($content, $filename){
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
		$objWriter->save('php://output');
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
