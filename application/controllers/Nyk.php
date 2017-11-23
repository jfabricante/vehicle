<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nyk extends CI_Controller {
	
	public function __construct(){
	
		parent::__construct();
		$this->load->model('nyk_model');
		$this->load->library('email'); 
	}
	
	public function buyoff_notifications(){
		
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 3600);
		
		$time = date("g:i a");
		
		$rows = $this->nyk_model->get_all_buyoff_today();
		$excel_rows = $rows;
		
		//~ EMAIL BODY
		
		$cnt = 0;
		$data = '';
		foreach($rows as $row){
			$row = (object)$row;
			$data .=  '<tr>
							<td style = "padding:8px;font-size: 11px;">'.$row->SERIAL_NUMBER.'</td>
							<td style = "padding:8px;font-size: 11px;text-align: left;">'.$row->FG_JO.'</td>
							<td style = "padding:8px;font-size: 11px;text-align: left;">'.$row->FG_MODEL_CODE.'</td>
							<td style = "padding:8px;font-size: 11px;text-align: left;">'.$row->CS_NUMBER.'</td>
							<td style = "padding:8px;font-size: 11px;">'.$row->CHASSIS_NUMBER.'</td>
							<td style = "padding:8px;font-size: 11px;">'.$row->DATE_RECEIVED.'</td>
						</tr>';
			$cnt++;
		}
		
		if($cnt == 0){
			file_put_contents('count.txt', $cnt);
		}
		
		$prev_count = file_get_contents('count.txt'); // foo
		
		if($cnt > $prev_count){
			
			file_put_contents('count.txt', $cnt);
			//~ EXCEL CREATION
			
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

			$row = count($excel_rows) + 1;
			$objPHPExcel = PHPExcel_IOFactory::load('././resources/report_template/nyk_transmittal_update.xlsx');
			$objPHPExcel->setActiveSheetIndex(0);

			$objPHPExcel->getActiveSheet()->fromArray($excel_rows,null, 'A2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'L1')->applyFromArray($styleArray_header);
			$objPHPExcel->getActiveSheet()->getStyle('A2:'.'L'.$row)->applyFromArray($styleArray);
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save('././resources/report_template/soa_temp.xls');

			$filename = 'NYKCBU-'.date('mdy-Hi').'.xls'; //save our workbook as this file name

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

			$objWriter->save($filename);
			
			//~ EMAIL
			
			$body = '<p style="text-color:black;font-size: 15px;font-family: arial,sans-serif;font-weight: bold;">NYK - Daily CBU details transmittal update as of '.$time.' today.</p>';
			$body .= '<p style="text-align: left;text-color:black;font-size: 12px;font-family: arial,sans-serif;">Count : '.$cnt.' Unit(s)</p>';
			$body .= '<table border="1" style="text-color:black;font-size: 11px;font-family: arial,sans-serif;border-color: #666;text-align: center;border-collapse: collapse;">';
			$body .= '<tr style="background: #eee;padding:10px;">
							<th>Serial Number</th>
							<th>FG JO</th>
							<th>FG Model Code</th>
							<th>CS Number</th>
							<th>Chassis Number</th>
							<th>Date Received</th>
						</tr>';
			$body .= $data;
			$body .= '</table>';
			$body .= '<p style="text-align: center;text-color:black;font-size: 13px;font-family: arial,sans-serif;">" This is an automated message. Please do not respond to this e-mail."</p>';
			$body .= '<p style="text-align: center;text-color:black;font-size: 13px;font-family: arial,sans-serif;">IPC Management Information System - Vehicle Portal Email Notification</p>';
			
			$this->load->library('emailerphp');
			$mail = new EmailerPHP;
			
			$mail->addAddress('lorie-valdomar@isuzuphil.com');
			$mail->addAddress('jonel-medina@isuzuphil.com');
			$mail->addAddress('joy-agustin@isuzuphil.com');
			$mail->addCC('arcadio-bugua@isuzuphil.com');
			$mail->addCC('crisanto-mina@isuzuphil.com');
			$mail->addCC('edgar-artista@isuzuphil.com');
			$mail->addCC('mike-bernas@isuzuphil.com');
			$mail->addBCC('christopher-desiderio@isuzuphil.com');
			$mail->addBCC('eric-alcones@isuzuphil.com');
			$mail->addBCC('bryan-briones@isuzuphil.com');
			$mail->addBCC('eva-lasay@isuzuphil.com');
			
			//~ $mail->addAddress('christopher-desiderio@isuzuphil.com');
			//~ $mail->addBCC('bryan-briones@isuzuphil.com');
			
			$mail->Subject = 'NYK - CBU Details Transmittal Update';
			$mail->AddAttachment($filename);
			$mail->Body = $body;
			$mail->isHTML(true);
		
			$mail->send();
			unlink($filename);
		}
	}
	
	public function buyoff(){
		
		
		$cs_number = $this->uri->segment(3);
		$chassis_number = $this->uri->segment(4);
		$engine_number = $this->uri->segment(5);
		$body_number = substr($this->uri->segment(4), -5);
		$key_number = $this->uri->segment(6);
		$aircon_number = $this->uri->segment(7);
		$stereo_number = $this->uri->segment(8);
		$fm_date = $this->uri->segment(9);
		$fg_jo = $this->uri->segment(10);
		$serial_number = $this->uri->segment(11);
		$fg_model_code = $this->uri->segment(12);
		
		$cs_number = (strtoupper($cs_number) == 'NULL')? NULL : strtoupper($cs_number);
		$chassis_number = (strtoupper($chassis_number) == 'NULL')? NULL : strtoupper($chassis_number);
		$engine_number = (strtoupper($engine_number) == 'NULL')? NULL : strtoupper($engine_number);
		$body_number = (strtoupper($body_number) == 'NULL')? NULL : $body_number;
		$fm_date = (!preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $fm_date))? NULL : date_format(date_create_from_format('m-d-Y', $fm_date), 'd-M-y');
		$key_number = (strtoupper($key_number) == 'NULL')? NULL : strtoupper($key_number);
		$aircon_number = (strtoupper($aircon_number) == 'NULL')? NULL : strtoupper($aircon_number);
		$stereo_number = (strtoupper($stereo_number) == 'NULL')? NULL : strtoupper($stereo_number);
		$buyoff_date = date('d-M-y');
		$fg_jo = (strtoupper($fg_jo) == 'NULL')? NULL : $fg_jo;
		$serial_number = (strtoupper($serial_number) == 'NULL')? NULL : $serial_number;
		$fg_model_code = (strtoupper($fg_model_code) == 'NULL')? NULL : $fg_model_code;
		$last_updated_by = 'NYK';
		
		//~ $cs_number = str_replace('%20', '', $cs_number);
		$cs_number = str_replace(' ', '', $cs_number);
		
		if( $cs_number      != NULL AND 
			$chassis_number != NULL AND 
			$fg_jo          != NULL AND 
			$serial_number  != NULL AND 
			$fg_model_code  != NULL ){
			
			$data = array(
						str_replace('%20', '', $cs_number),
						str_replace('%20', '', $chassis_number),
						str_replace('%20', '', $engine_number),
						str_replace('%20', '', $body_number),
						str_replace('%20', '', $fm_date),
						str_replace('%20', '', $key_number),
						str_replace('%20', '', $aircon_number),
						str_replace('%20', '', $stereo_number),
						str_replace('%20', '', $last_updated_by),
						str_replace('%20', '', $fg_jo),
						str_replace('%20', '', $serial_number),
						str_replace('%20', '', $fg_model_code)
					);
					
			$affected_rows = $this->nyk_model->update_cbu_mis_attributes($data);
			
			if($affected_rows > 0){
				
				$data = array(
						str_replace('%20', '', $cs_number),
						str_replace('%20', '', $chassis_number),
						str_replace('%20', '', $engine_number),
						str_replace('%20', '', $body_number),
						str_replace('%20', '', $key_number),
						str_replace('%20', '', $aircon_number),
						str_replace('%20', '', $stereo_number),
						str_replace('%20', '', $fm_date),
						str_replace('%20', '', $fg_jo),
						str_replace('%20', '', $serial_number),
						str_replace('%20', '', $fg_model_code)
					);
					
				$this->nyk_model->insert_cbu_details($data);
				$this->nyk_model->insert_transmittal_url(array(uri_string(), 1));
				echo '1';
			}
			else{
				$this->nyk_model->insert_transmittal_url(array(uri_string(), 0));
				echo '0';
			}
		}
		else{
			$this->nyk_model->insert_transmittal_url(array(uri_string(), 0));
			echo '0';
		}
	}
}
