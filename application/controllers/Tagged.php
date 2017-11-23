<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagged extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('tagged_model');
		session_check();
	}
	
	public function list_(){
		$data['content'] = 'tagged_list_view';
		$data['title'] = 'Tagged Units';
		$data['result'] = $this->tagged_model->get_tagged_headers();
		$this->load->view('include/template',$data);
	}
	
	public function oc(){
		$data['content'] = 'tagged_oc_view';
		$data['title'] = 'Order Confirmed';
		$data['result'] = $this->tagged_model->get_tagged_oc_headers();
		$this->load->view('include/template',$data);
	}
	
	public function ajax_get_tagged_line(){
		
		$data['row'] = $this->forsale_model->get_forsale_line($this->input->post('cs_number'));
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

		$data = $this->tagged_model->for_tagged_report();

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
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
      	$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/tagged_template.xlsx");
      	$objPHPExcel->setActiveSheetIndex(0);

      	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
      	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'U1')->applyFromArray($styleArray_header);
      	$objPHPExcel->getActiveSheet()->getStyle('A2:'.'U'.$row)->applyFromArray($styleArray);
      	$objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('00000000000000');
      	//~ $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$ctr, $row->part_no,PHPExcel_Cell_DataType::TYPE_STRING);
      	//~ $objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('0000');

      	$objPHPExcel->getActiveSheet()->getStyle('S2:S'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	$objWriter->save('././resources/report_template/tempfile.xls');

      	$filename='tagged_report.xls'; //save our workbook as this file name

      	header('Content-Type: application/vnd.ms-excel'); //mime type

      	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

      	header('Cache-Control: max-age=0'); //no cache

      	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      	$objWriter->save('php://output');
	}
	
	public function report_oc()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		$data = $this->tagged_model->for_tagged_oc_report();

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
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
      	$objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/tagged_template.xlsx");
      	$objPHPExcel->setActiveSheetIndex(0);

      	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
      	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'U1')->applyFromArray($styleArray_header);
      	$objPHPExcel->getActiveSheet()->getStyle('A2:'.'U'.$row)->applyFromArray($styleArray);
      	$objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('00000000000000');
      	//~ $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$ctr, $row->part_no,PHPExcel_Cell_DataType::TYPE_STRING);
      	//~ $objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('0000');

      	$objPHPExcel->getActiveSheet()->getStyle('S2:S'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	$objWriter->save('././resources/report_template/tempfile.xls');

      	$filename='oc.xls'; //save our workbook as this file name

      	header('Content-Type: application/vnd.ms-excel'); //mime type

      	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

      	header('Cache-Control: max-age=0'); //no cache

      	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      	$objWriter->save('php://output');
	}
	
	public function oc_balance_summary_form(){
		
		$data = array(
				'content'     => 'report_form/oc_balance_summary_report_form',
				'title'       => 'OC Balance Summary Report'
			);

			$this->load->view('include/template', $data);
	}
	
	public function oc_balance_summary_report(){

		$from_date = date('d-M-y', strtotime($this->input->post('from')));
		$to_date = date('d-M-y', strtotime($this->input->post('to')));
		
		//~ echo $from_date;die();
		
		$rows = $this->tagged_model->get_oc_balance_summary($from_date, $to_date);
		//~ $rows = array();
		$data = '';
		$sales_model = '';
		foreach($rows as $row){
			
			$row->BULACAN = ($row->BULACAN == 0) ? '':$row->BULACAN;
			$row->CABANATUAN = ($row->CABANATUAN == 0) ? '':$row->CABANATUAN;
			$row->ISABELA = ($row->ISABELA == 0) ? '':$row->ISABELA;
			$row->CAGAYAN = ($row->CAGAYAN == 0) ? '':$row->CAGAYAN;
			$row->SAN_PABLO = ($row->SAN_PABLO == 0) ? '':$row->SAN_PABLO;
			$row->MAKATI = ($row->MAKATI == 0) ? '':$row->MAKATI;
			$row->BATANGAS = ($row->BATANGAS == 0) ? '':$row->BATANGAS;
			$row->COMMONWEALTH = ($row->COMMONWEALTH == 0) ? '':$row->COMMONWEALTH;
			$row->MANILA = ($row->MANILA == 0) ? '':$row->MANILA;
			$row->EDSA = ($row->EDSA == 0) ? '':$row->EDSA;
			$row->PAMPANGA = ($row->PAMPANGA == 0) ? '':$row->PAMPANGA;
			$row->PANGASINAN = ($row->PANGASINAN == 0) ? '':$row->PANGASINAN;
			$row->QA = ($row->QA == 0) ? '':$row->QA;
			$row->ALABANG = ($row->ALABANG == 0) ? '':$row->ALABANG;
			$row->CAVITE = ($row->CAVITE == 0) ? '':$row->CAVITE;
			$row->PASIG = ($row->PASIG == 0) ? '':$row->PASIG;
			$row->MANDAUE = ($row->MANDAUE == 0) ? '':$row->MANDAUE;
			$row->ILOILO = ($row->ILOILO == 0) ? '':$row->ILOILO;
			$row->GENSAN = ($row->GENSAN == 0) ? '':$row->GENSAN;
			$row->DAVAO = ($row->DAVAO == 0) ? '':$row->DAVAO;
			$row->BUTUAN = ($row->BUTUAN == 0) ? '':$row->BUTUAN;
			$row->BACOLOD = ($row->BACOLOD == 0) ? '':$row->BACOLOD;
			$row->IPC = ($row->IPC == 0) ? '':$row->IPC;
			$row->FLEET = ($row->FLEET == 0) ? '':$row->FLEET;
			$row->OTHERS = ($row->OTHERS == 0) ? '':$row->OTHERS;
			$row->TOTAL = ($row->TOTAL == 0) ? '':$row->TOTAL;
			
			if($row->SALES_MODEL != NULL AND $row->BODY_COLOR != NULL){
				$data .= '<tr>
							<td width="160px" style="text-align: left;border: 0.1px solid #333;">'.$row->SALES_MODEL.' </td>
							<td width="80px" style="text-align: left;border: 0.1px solid #333;">'.$row->BODY_COLOR.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BULACAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->CABANATUAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->ISABELA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->CAGAYAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->SAN_PABLO.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->MAKATI.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BATANGAS.'</td>
							<td width="30px" style="text-align: center;border: 0.1px solid #333;">'.$row->COMMONWEALTH.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->MANILA.'</td>
							<td width="35px" style="text-align: center;border: 0.1px solid #333;">'.$row->EDSA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->PAMPANGA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->PANGASINAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->QA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->ALABANG.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->CAVITE.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->PASIG.'</td>
							<td width="30px" style="text-align: center;border: 0.1px solid #333;">'.$row->MANDAUE.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->ILOILO.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->GENSAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->DAVAO.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BUTUAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BACOLOD.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->IPC.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->FLEET.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->OTHERS.'</td>
							<td width="35px" style="text-align: center;border: 0.1px solid #333;">'.$row->TOTAL.'</td>
						</tr>';
			}
			else if($row->SALES_MODEL != NULL AND $row->BODY_COLOR == NULL){
			
				$data .= '<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td colspan="2" style="text-align: left;border: 0.1px solid #333;">Total </td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BULACAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CABANATUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ISABELA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAGAYAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->SAN_PABLO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAKATI.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BATANGAS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->COMMONWEALTH.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANILA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->EDSA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PAMPANGA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PANGASINAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->QA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ALABANG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAVITE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PASIG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANDAUE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ILOILO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->GENSAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->DAVAO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BUTUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BACOLOD.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->IPC.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->FLEET.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->OTHERS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->TOTAL.'</td>
						</tr>';
				
			}
			else if($row->SALES_MODEL == NULL AND $row->BODY_COLOR == NULL){
			
				$data .= '<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td colspan="2" style="text-align: left;border: 0.1px solid #333;">Grand Total </td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BULACAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CABANATUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ISABELA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAGAYAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->SAN_PABLO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAKATI.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BATANGAS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->COMMONWEALTH.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANILA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->EDSA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PAMPANGA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PANGASINAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->QA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ALABANG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAVITE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PASIG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANDAUE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ILOILO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->GENSAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->DAVAO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BUTUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BACOLOD.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->IPC.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->FLEET.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->OTHERS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->TOTAL.'</td>
						</tr>';
				
			}
		}
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 8px;">
						<thead>
							<tr>
								<td colspan="28" style="font-size: 12px;"><strong>Order Confirmation Balance Summary</strong></td>
							</tr>
							<tr>
								<td colspan="28" style="font-size: 10px;">From '.date('m/d/Y',strtotime($from_date)).' to '.date('m/d/Y',strtotime($to_date)).'</td>
							</tr>
							<tr>
								<td colspan="28" style="font-size: 12px;">&nbsp;</td>
							</tr>
							<tr style="font-weight: bold;background-color: #D3D3D3;">
								<th width="160px" style="text-align: center;border: 0.1px solid #333;">MODEL</th>
								<th width="80px" style="text-align: center;border: 0.1px solid #303;">BODY COLOR</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BUL</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">CAB</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">ISA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">CAG</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">SNP</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">MKT</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BAT</th>
								<th width="30px" style="text-align: center;border: 0.1px solid #283;">CMW</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">MNL</th>
								<th width="35px" style="text-align: center;border: 0.1px solid #283;">EDSA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">PAM</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">DAG</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">QA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">ALA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">CAV</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">PAS</th>
								<th width="30px" style="text-align: center;border: 0.1px solid #283;">MAN</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">ILO</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">GEN</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">DAV</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BUT</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BAC</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">IPC</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">FLT</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">OTH</th>
								<th width="35px" style="text-align: center;border: 0.1px solid #333;">Total</th>
							</tr>
						</thead>
							<tbody>
							'.$data.'
							<tr>
								<td colspan="28" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="28" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="28" style="text-align: right;font-size: 12px;"><i>System Generated Report</i></td>
							</tr>
						</tbody>
					</table>';
		
		$this->pdf($content,'L');
	}
	
	public function tagged_summary_report(){

		//~ $from_date = date('d-M-y', strtotime($this->input->post('from')));
		//~ $to_date = date('d-M-y', strtotime($this->input->post('to')));
		
		//~ echo $from_date;die();
		
		$rows = $this->tagged_model->get_tagged_summary();
		//~ $rows = array();
		$data = '';
		$sales_model = '';
		foreach($rows as $row){
			
			$row->BULACAN = ($row->BULACAN == 0) ? '':$row->BULACAN;
			$row->CABANATUAN = ($row->CABANATUAN == 0) ? '':$row->CABANATUAN;
			$row->ISABELA = ($row->ISABELA == 0) ? '':$row->ISABELA;
			$row->CAGAYAN = ($row->CAGAYAN == 0) ? '':$row->CAGAYAN;
			$row->SAN_PABLO = ($row->SAN_PABLO == 0) ? '':$row->SAN_PABLO;
			$row->MAKATI = ($row->MAKATI == 0) ? '':$row->MAKATI;
			$row->BATANGAS = ($row->BATANGAS == 0) ? '':$row->BATANGAS;
			$row->COMMONWEALTH = ($row->COMMONWEALTH == 0) ? '':$row->COMMONWEALTH;
			$row->MANILA = ($row->MANILA == 0) ? '':$row->MANILA;
			$row->EDSA = ($row->EDSA == 0) ? '':$row->EDSA;
			$row->PAMPANGA = ($row->PAMPANGA == 0) ? '':$row->PAMPANGA;
			$row->PANGASINAN = ($row->PANGASINAN == 0) ? '':$row->PANGASINAN;
			$row->QA = ($row->QA == 0) ? '':$row->QA;
			$row->ALABANG = ($row->ALABANG == 0) ? '':$row->ALABANG;
			$row->CAVITE = ($row->CAVITE == 0) ? '':$row->CAVITE;
			$row->PASIG = ($row->PASIG == 0) ? '':$row->PASIG;
			$row->MANDAUE = ($row->MANDAUE == 0) ? '':$row->MANDAUE;
			$row->ILOILO = ($row->ILOILO == 0) ? '':$row->ILOILO;
			$row->GENSAN = ($row->GENSAN == 0) ? '':$row->GENSAN;
			$row->DAVAO = ($row->DAVAO == 0) ? '':$row->DAVAO;
			$row->BUTUAN = ($row->BUTUAN == 0) ? '':$row->BUTUAN;
			$row->BACOLOD = ($row->BACOLOD == 0) ? '':$row->BACOLOD;
			$row->IPC = ($row->IPC == 0) ? '':$row->IPC;
			$row->FLEET = ($row->FLEET == 0) ? '':$row->FLEET;
			$row->OTHERS = ($row->OTHERS == 0) ? '':$row->OTHERS;
			$row->TOTAL = ($row->TOTAL == 0) ? '':$row->TOTAL;
			
			
			if($row->SALES_MODEL != NULL AND $row->BODY_COLOR != NULL){
				
				$data .= '<tr>
							<td width="160px" style="text-align: left;border: 0.1px solid #333;">'.$row->SALES_MODEL.' </td>
							<td width="80px" style="text-align: left;border: 0.1px solid #333;">'.$row->BODY_COLOR.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BULACAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->CABANATUAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->ISABELA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->CAGAYAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->SAN_PABLO.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->MAKATI.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BATANGAS.'</td>
							<td width="30px" style="text-align: center;border: 0.1px solid #333;">'.$row->COMMONWEALTH.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->MANILA.'</td>
							<td width="35px" style="text-align: center;border: 0.1px solid #333;">'.$row->EDSA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->PAMPANGA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->PANGASINAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->QA.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->ALABANG.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->CAVITE.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->PASIG.'</td>
							<td width="30px" style="text-align: center;border: 0.1px solid #333;">'.$row->MANDAUE.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->ILOILO.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->GENSAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->DAVAO.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BUTUAN.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->BACOLOD.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->IPC.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->FLEET.'</td>
							<td width="28px" style="text-align: center;border: 0.1px solid #333;">'.$row->OTHERS.'</td>
							<td width="35px" style="text-align: center;border: 0.1px solid #333;">'.$row->TOTAL.'</td>
						</tr>';
			}
			else if($row->SALES_MODEL != NULL AND $row->BODY_COLOR == NULL){
			
				$data .= '<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td colspan="2" style="text-align: left;border: 0.1px solid #333;">Total </td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BULACAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CABANATUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ISABELA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAGAYAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->SAN_PABLO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAKATI.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BATANGAS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->COMMONWEALTH.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANILA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->EDSA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PAMPANGA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PANGASINAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->QA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ALABANG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAVITE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PASIG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANDAUE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ILOILO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->GENSAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->DAVAO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BUTUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BACOLOD.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->IPC.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->FLEET.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->OTHERS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->TOTAL.'</td>
						</tr>';
				
			}
			else if($row->SALES_MODEL == NULL AND $row->BODY_COLOR == NULL){
			
				$data .= '<tr style="font-weight: bold;background-color: #D3D3D3;">
							<td colspan="2" style="text-align: left;border: 0.1px solid #333;">Grand Total </td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BULACAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CABANATUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ISABELA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAGAYAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->SAN_PABLO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MAKATI.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BATANGAS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->COMMONWEALTH.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANILA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->EDSA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PAMPANGA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PANGASINAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->QA.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ALABANG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->CAVITE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->PASIG.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->MANDAUE.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->ILOILO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->GENSAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->DAVAO.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BUTUAN.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->BACOLOD.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->IPC.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->FLEET.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->OTHERS.'</td>
							<td style="text-align: center;border: 0.1px solid #333;">'.$row->TOTAL.'</td>
						</tr>';
				
			}
		}
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		
		$content = '<table nobr="true" border="0" style="padding: 3px 5px;font-size: 8px;">
						<thead>
							<tr>
								<td colspan="28" style="font-size: 12px;"><strong>Tagged Summary</strong></td>
							</tr>
							<tr>
								<td colspan="28" style="font-size: 10px;">As of '.date('m/d/Y').'</td>
							</tr>
							<tr>
								<td colspan="28" style="font-size: 12px;">&nbsp;</td>
							</tr>
							<tr style="font-weight: bold;background-color: #D3D3D3;">
								<th width="160px" style="text-align: center;border: 0.1px solid #333;">MODEL</th>
								<th width="80px" style="text-align: center;border: 0.1px solid #303;">BODY COLOR</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BUL</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">CAB</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">ISA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">CAG</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">SNP</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">MKT</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BAT</th>
								<th width="30px" style="text-align: center;border: 0.1px solid #283;">CMW</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">MNL</th>
								<th width="35px" style="text-align: center;border: 0.1px solid #283;">EDSA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">PAM</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">DAG</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">QA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">ALA</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">CAV</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">PAS</th>
								<th width="30px" style="text-align: center;border: 0.1px solid #283;">MAN</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">ILO</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">GEN</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">DAV</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BUT</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">BAC</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">IPC</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">FLT</th>
								<th width="28px" style="text-align: center;border: 0.1px solid #283;">OTH</th>
								<th width="35px" style="text-align: center;border: 0.1px solid #333;">Total</th>
							</tr>
						</thead>
							<tbody>
							'.$data.'
							<tr>
								<td colspan="28" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="28" style="text-align: right;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="28" style="text-align: right;font-size: 12px;"><i>System Generated Report</i></td>
							</tr>
						</tbody>
					</table>';
		
		$this->pdf($content,'L');
	}
	
	public function pdf($content, $orientation = 'P'){
		// generate pdf content
		$this->load->library('pdf');
		// create new PDF document
		$pdf = new PDF($orientation , PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
		
		$this->filename = 'report.pdf';
		$pdf->Output($this->filename,'I');
	}
}
