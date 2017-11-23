<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Motorpool extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('date');

		$this->load->model('motorpool_model');
		session_check();
	}
	
	public function report()
	{
	      ini_set('memory_limit', '-1');
	      ini_set('max_execution_time', 3600);
	      // ini_set("precision", "15");
	      
		  $data = $this->motorpool_model->report();
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
	      $objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/motorpool_template.xlsx");
	      $objPHPExcel->setActiveSheetIndex(0);

	      $objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
	      $objPHPExcel->getActiveSheet()->getStyle('A1:'.'J1')->applyFromArray($styleArray_header);
	      $objPHPExcel->getActiveSheet()->getStyle('A2:'.'J'.$row)->applyFromArray($styleArray);

	     //  $objPHPExcel->getActiveSheet()
		    // ->getStyle('H2:'.'H'.$row)
		    // ->getNumberFormat()
		    // ->setFormatCode( PHPExcel_Style_NumberFormat::TYPE_STRING );

	      // $objPHPExcel->getActiveSheet()
       //      ->getCell('H2:'.'H'.$row)
       //      ->setValueExplicit($cellDate, PHPExcel_Cell_DataType::TYPE_STRING);
		  //$objPHPExcel->getActiveSheet()->setCellValueExplicit('H2:'.'H'.$row, PHPExcel_Cell_DataType::TYPE_STRING);



	      $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	      $objWriter->save('././resources/report_template/tempfile.xls');

	      $filename='motorpool_report.xls'; //save our workbook as this file name

	      header('Content-Type: application/vnd.ms-excel'); //mime type

	      header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

	      header('Cache-Control: max-age=0'); //no cache

	      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	      $objWriter->save('php://output');
	}
	
}
