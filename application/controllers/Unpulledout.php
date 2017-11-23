<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unpulledout extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('unpulledout_model');
		session_check();
	}
	
	public function list_(){
		$data['content'] = 'unpulledout_list_view';
		$data['title'] = 'Unpulled Out Units';
		$data['result'] = $this->unpulledout_model->get_unpulledout_headers();
		$this->load->view('include/template',$data);
	}
	
	public function ajax_get_unpulledout_line(){
		
		$data['row'] = $this->forsale_model->get_unpulledout_line($this->input->post('cs_number'));
		echo $this->load->view('ajax/forsale_line_view',$data,true);

		//~ $row = $this->forsale_model->get_forsale_line($this->input->post('cs_number'));
		//~ echo '<pre>';		
		//~ print_r($row);
		//~ echo '</pre>';
		
	}

	public function report()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		$data = $this->unpulledout_model->for_unpulledout_report();
		$this->load->library('excel');
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
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
      	$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/unpulledout_template.xlsx");
      	$objPHPExcel->setActiveSheetIndex(0);

      	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
      	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'S1')->applyFromArray($styleArray_header);
      	$objPHPExcel->getActiveSheet()->getStyle('A2:'.'S'.$row)->applyFromArray($styleArray);


      	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	$objWriter->save('././resources/report_template/tempfile.xls');

      	$filename='unpulledout_report.xls'; //save our workbook as this file name

      	header('Content-Type: application/vnd.ms-excel'); //mime type

      	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

      	header('Cache-Control: max-age=0'); //no cache

      	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      	$objWriter->save('php://output');
	}
}
