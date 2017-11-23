<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pullout extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('pullout_model');
		session_check();
	}
	
	public function new_(){
		//~ echo date('d-M-Y');
		$data['content'] = 'pullout_entry_view';
		$data['title'] = 'Vehicle Pullout Date';
		$this->load->view('include/template',$data);
	}
	
	public function cbu(){
		//~ echo date('d-M-Y');
		$data['content'] = 'cbu_pullout_entry_view';
		$data['title'] = 'NYK CBU Units';
		$this->load->view('include/template',$data);
	}
	
	public function cbu_pulledout(){
		//~ echo date('d-M-Y');
		
		//~ $date_from = date('d-M-y', strtotime('7/01/2017'));
		//~ $date_to = date('d-M-y', strtotime('7/19/2017'));
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');

		$data['result'] = $this->pullout_model->get_cbu_pulledout( $data['from_date'] ,$data['to_date'] );
		$data['content'] = 'cbu_pulledout_view';
		$data['title'] = 'NYK CBU Units';
		$this->load->view('include/template',$data);
	}
	
	public function cbu_unpulledout(){
		//~ echo date('d-M-Y');
		$data['result'] = $this->pullout_model->get_cbu_unpulledout();
		$data['content'] = 'cbu_unpulledout_view';
		$data['title'] = 'NYK CBU Units';
		$this->load->view('include/template',$data);
	}
	
	public function ajax_search_cs_number(){
		
		$cs_nos = explode(',', $this->input->post('cs_nos'));
		$cs_nos = '\''.implode('\',\'', str_replace(' ', '', $cs_nos)).'\'';
		
		$cs_nos = STRTOUPPER($cs_nos);
		
		$data['result'] = $this->pullout_model->get_search_cs_nos($cs_nos);
		$data['cs_nos'] = $cs_nos;
		echo $this->load->view('ajax/searched_cs_nos',$data,true);
		
		//~ $this->dr_model->get_search_dr_number($drs);

	}
	
	public function ajax_update_pullout_date(){
		
		$cs_nos = $this->input->post('cs_nos');
		$pullout_date = date('Y/m/d 00:00:00', strtotime($this->input->post('pullout_date')));
		
		//~ echo $cs_nos;die();
		
		$this->pullout_model->update_pullout_date($cs_nos, $pullout_date);
		$data['result'] = $this->pullout_model->get_search_cs_nos($cs_nos);
		$data['cs_nos'] = $cs_nos;
		echo $this->load->view('ajax/searched_cs_nos',$data,true);
	}

	public function view_report()
	{
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
  //       echo "<pre>";
		// echo ($data['from_date']);
		// echo "</pre>";
		$data['content'] = 'pullout_report';
		$data['title'] = 'Report';
		$this->load->view('include/template',$data);
	}
	
	public function excel($from_date = null,$to_date = null){
		
		//~ echo $_SERVER['REQUEST_URI'];
		
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
		
		$from_date = ($from_date == null)? date('Y-m-01') : $from_date;
		$to_date = ($to_date == null)? date('Y-m-01') : $to_date;

		$from_date = date_format(date_create($from_date),'d-M-y');
		$to_date = date_format(date_create($to_date),'d-M-y');

		$from_date2 = date_format(date_create($from_date),'m/d/Y');
		$to_date2 = date_format(date_create($to_date),'m/d/Y');

		$data = $this->pullout_model->pullout_report($from_date,$to_date);

		 //~ echo "<pre>";
		 //~ print_r($data);
		 //~ echo "</pre>";
		 //~ exit();
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
      	$objPHPExcel = PHPExcel_IOFactory::load('././resources/report_template/pulledout_template.xlsx');
      	$objPHPExcel->setActiveSheetIndex(0);

      	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
      	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'J1')->applyFromArray($styleArray_header);
      	$objPHPExcel->getActiveSheet()->getStyle('A2:'.'J'.$row)->applyFromArray($styleArray);
      	//~ $objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('00000000000000');
      	//~ $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$ctr, $row->part_no,PHPExcel_Cell_DataType::TYPE_STRING);
      	//~ $objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('0000');

      	//~ $objPHPExcel->getActiveSheet()->getStyle('S2:S'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	$objWriter->save('././resources/report_template/pullout_temp.xls');

      	//~ $filename = $sales_type . '-soa-'.$data[0]['account_number'].'.xls'; //save our workbook as this file name
      	$filename = 'pulledout.xls'; //save our workbook as this file name

      	header('Content-Type: application/vnd.ms-excel'); //mime type

      	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

      	header('Cache-Control: max-age=0'); //no cache

      	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      	$objWriter->save('php://output');
	}
	
	public function report($from_date = null,$to_date = null)
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 10000);
        
		$from_date = ($from_date == null)? date('Y-m-01') : $from_date;
		$to_date = ($to_date == null)? date('Y-m-01') : $to_date;

		$from_date = date_format(date_create($from_date),'d-M-y');
		$to_date = date_format(date_create($to_date),'d-M-y');

		$from_date2 = date_format(date_create($from_date),'m/d/Y');
		$to_date2 = date_format(date_create($to_date),'m/d/Y');

		$data = $this->pullout_model->pullout_report($from_date,$to_date);
		
		 //~ echo "<pre>";
		 //~ print_r($data);
		 //~ echo "</pre>";
		 //~ exit();
		
		$ctr = 0;

		$this->load->library('Pdf_pulledout');

        $pdf = new Pdf_pulledout(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setData($from_date2.' to '.$to_date2);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('IPC Portal');
        $pdf->SetSubject('IPC Portal');
        $pdf->SetKeywords('IPC Portal');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT - 5, 29, PDF_MARGIN_RIGHT - 5);
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);

        $pdf->AddPage('L', 'A4');

        $ctr = 0;
		$html = '<style>
                table, td, th {
                    border: 1px solid #444;
                    padding: 4px 2px;
                }

                table {
                    border-collapse: collapse;
                    width: 100%;
                }

                th {
                    height: 50px;
                }

                </style>';


                    foreach($data as $row)
						
                    {$row = (object)$row;
	                    $html .= '<table>
	                            <tr style="text-align: center; font-size: 7px;">
	                                <td height="10" style="width: 180px; text-align: left;">'.$row->PARTY_NAME.'</td>
	                                <td height="10" style="width: 100px; text-align: left;">'.$row->ACCOUNT_NAME.'</td>
	                                <td height="10" style="width: 80px;">'.$row->TRX_NUMBER.'</td>
	                                <td height="10" style="width: 60px;">'.$row->CS_NUMBER.'</td>
	                                <td height="10" style="width: 70px;">'.$row->PULLOUT_DATE.'</td>
	                                <td height="10" style="width: 120px; text-align: left;">'.$row->BODY_COLOR.'</td>
	                                <td height="10" style="width: 110px;">'.$row->CHASSIS_NUMBER.'</td>
	                                <td height="10" style="width: 70px;">'.$row->ENGINE_NO.'</td>
	                                <td height="10" style="width: 70px;">'.$row->KEY_NUMBER.'</td>
	                                <td height="10" style="width: 130px; text-align: left;">'.$row->SALES_MODEL.'</td>
	                            </tr></table>';
                        $ctr++;
                        if($ctr > 30)
                        {
                        	$html .= '<br pagebreak="true"/>';
                        	$ctr = 0;
                        }
                    }
					
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output("pulledout_report.pdf",'I');
	}
	
}
