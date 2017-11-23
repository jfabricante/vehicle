<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wb extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		//~ $this->load->helper('date');

		$this->load->model('wb_model');
		$this->load->model('invoice_model');
		session_check();
	}
	
	public function entry(){
		
		if($this->input->post('customer_id') == NULL){
			$data['customer_id'] = 0;
		}
		else{
			$data['customer_id'] = $this->input->post('customer_id');
			$data['invoices'] = $this->invoice_model->get_unpulledout_per_customers($this->input->post('customer_id'));
			//~ $rows = $this->invoice_model->get_unpulledout_per_customers($this->input->post('customer_id'));
			
			//~ print_r($rows);
		}
		
		$data['content'] = 'wb_entry_view';
		$data['title'] = 'Invoice Printing and WB Number entry';
		$data['customers'] = $this->invoice_model->get_vehicle_customers();
		$this->load->view('include/template',$data);
	}
	
	public function ajax_search_trx_numbers()
	{
		$this->load->helper('format_helper');
		$form       = $this->input->post();
		$form       = (object)$form;
		$ctr        = 0;
		$array_data = array();
		$arr_ctr    = 0;

		$invoices = array();

		//~ print_r($form->data);
		
		$invoices = '0';
		foreach($form->data as $row)
		{
			if($row['name'] == 'invoice_id')
			{
				$invoices .= ','.$row['value'];
			}
		}
		
		$data['invoices'] = $invoices;
		$data['result'] = $this->wb_model->get_wb_by_trx_number_range($invoices);
		echo $this->load->view('ajax/search_trx_numbers_with_wb',$data,true);
	}
	
	public function save_wb(){
		
		$invoices = $this->input->post('invoices');
		
		if($this->input->post('trx_number') != NULL){
		
			$wb_numbers = $this->input->post('wb_number');
			$trx_numbers = $this->input->post('trx_number');
			$ctr = 0;
			$go_print = 1;
		
			foreach($trx_numbers as $trx_number){
				if(empty($wb_numbers[$ctr])){
					$go_print = 0;
				}
				else{
					$this->wb_model->update_wb_number($trx_number, $wb_numbers[$ctr]);
				}
				$ctr++;
			}
		}
		else{
			$go_print = 1;
		}
		
		if($go_print == 1){
			$this->print_invoice($invoices);
		}
		else{
			echo 'WB Number must not be empty!';
		}
		
	}
	
	//~ public function prepare_selected(){
		
		//~ $invoice_ids = $this->input->post('data');
		
		//~ print_r($invoice_ids);
		
		//~ $invoices = '0';
		//~ foreach($invoice_ids as $invoice_id){
			//~ $invoices .= ','.$invoice_id;
		//~ }
		
		//~ echo $invoices;die();
		
		//~ $data['invoices'] = $invoices;
		//~ $data['result'] = $this->wb_model->get_wb_by_trx_number_range($invoices);
		//~ echo $this->load->view('ajax/search_trx_numbers_with_wb',$data,true);
	//~ }
	
	public function print_invoice($invoices){
		
		//~ $trx_numbers = '40300009167';
		//~ $trx_numbers = '40300015727';
		$rows = $this->invoice_model->get_invoice_for_print_details_($invoices);
		
		//~ echo '<pre>';
		//~ print_r($row);
		//~ echo '</pre>';
		
		$content = array();
		$ctr = 0;
		foreach($rows as $row){
			if($row->FLEET_NAME != NULL){
				$fleet_name = '<tr>
									<td colspan="5" style="font-size: 10px;"><strong>Dealers Fleet Account :</strong></td>
								</tr>
								<tr>
									<td colspan="5" style="font-size: 12px;"><strong>'. $row->FLEET_NAME .'</strong></td>
								</tr>';
			}
			else{
				$fleet_name = '<tr>
									<td colspan="5" style="font-size: 10px;">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="5" style="font-size: 12px;">&nbsp;</td>
								</tr>';
			}
			
			$content[$ctr] = '';
			
			$content[$ctr] .= '<table border="0" style="padding: 1px;font-size: 12px;">
							<tr>
								<td colspan="5" style="text-align: right;font-size: 15px;"><strong>SALES INVOICE (VEHICLE)</strong></td>
							</tr>
							<tr>
								<td colspan="5" style="text-align: right;font-size: 16px;"><span style="font-size: 12px;">No.</span> &nbsp;&nbsp; <strong>'.$row->TRX_NUMBER.'</strong></td>
							</tr>
							<br />
							<br />
							<tr>
								<td colspan="3" style="font-size:10px;width:417px;"></td>
								<td colspan="1" style="width: 90px;">Date</td>
								<td colspan="1" style="width: 165px;"><strong>'. date('F j, Y', strtotime($row->TRX_DATE)) .'</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="font-size:10px;width:417px;">SOLD TO</td>
								<td colspan="1" style="width: 90px;">PO/SO Ref</td>
								<td colspan="1" style="width: 165px;"><strong>'. $row->SO_NUMBER .'</strong></td>
							</tr>
							<tr>
								<td colspan="1" style="width:17px;">&nbsp;</td>
								<td colspan="2" style="font-size: 12px;width:400px;"><strong>'. $row->PARTY_NAME .'</strong></td>
								<td colspan="1">DR Number</td>
								<td colspan="1"><strong><strong>'. $row->DR_NUMBER .'</strong></strong></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="font-size: 12px;">'. $row->ADDRESS .'</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="font-size: 12px;">TIN# : '. $row->TAX_REFERENCE .'</td>
								<td colspan="1" style="width:140px;">Terms of Payment</td>
								<td colspan="1" style="width:115px;">'.$row->PAYMENT_TERMS.'</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="font-size: 12px;">Business Style : '. $row->CLASS_CODE . ' - ' . $row->BUSINESS_STYLE .'</td>
								<td colspan="1">( &nbsp;&nbsp; ) Cash &nbsp; ( &nbsp;&nbsp; ) Check</td>
								<td colspan="1">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="3" style="font-size: 12px;">&nbsp;</td>
								<td colspan="1">With Orig copy of CSR</td>
								<td colspan="1">'.$row->CSR_NUMBER.'</td>
							</tr>
							<tr>
								<td colspan="3" style="font-size: 13px;">&nbsp;</td>
								<td colspan="1">CSR (OR)</td>
								<td colspan="1">'.$row->CSR_OR_NUMBER.'</td>
							</tr>
							<tr>
								<td colspan="5">
									<table border="0" style="padding: 0px;">
										<tr>
											<td style="width: 50px;text-align: center;"><strong>Item No</strong></td>
											<td style="width: 100px;text-align: center;"><strong>Reference</strong></td>
											<td colspan="2" style="width: 290px;text-align: center;"><strong>Description</strong></td>
											<td style="width: 50px;text-align: center;"><strong>Qty</strong></td>
											<td style="width: 90px;text-align: center;"><strong>Unit Price</strong></td>
											<td style="width: 90px;text-align: center;"><strong>Amount</strong></td>
										</tr>
										<tr>
											<td colspan="7"></td>
										</tr>
										<tr>
											<td style="text-align: center;"><strong>1</strong></td>
											<td style="text-align: left;"><strong>CS NO.:</strong></td>
											<td style="text-align: left;width: 95px;"><strong>Model</strong></td>
											<td style="text-align: left;width: 195px;"><strong>'.$row->SALES_MODEL.'</strong></td>
											<td style="text-align: center;"><strong>1</strong></td>
											<td style="text-align: right;"><strong>'. number_format($row->VATABLE_SALES,2) .'</strong></td>
											<td style="text-align: right;"><strong>'. number_format($row->VATABLE_SALES,2) .'</strong></td>
										</tr>
										<tr>
											<td style="text-align: center;">&nbsp;</td>
											<td rowspan="2" style="text-align: left;font-size: 20px;"><strong>'.$row->CS_NUMBER.'</strong></td>
											<td style="text-align: left;"><strong>Lot No</strong></td>
											<td style="text-align: left;"><strong>'.$row->LOT_NUMBER.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Serial No</strong></td>
											<td style="text-align: left;"><strong>'.$row->CHASSIS_NUMBER.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>WB No.:</strong></td>
											<td style="text-align: left;"><strong>Engine</strong></td>
											<td style="text-align: left;"><strong>'.$row->ENGINE_TYPE.'-'.$row->ENGINE_NO.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>'.$row->WB_NUMBER.'</strong></td>
											<td style="text-align: left;"><strong>Color</strong></td>
											<td style="text-align: left;"><strong>'.$row->BODY_COLOR.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>GVW</strong></td>
											<td style="text-align: left;"><strong>'.$row->GVW.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Fuel</strong></td>
											<td style="text-align: left;"><strong>'.$row->FUEL.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Key No</strong></td>
											<td style="text-align: left;"><strong>'.$row->KEY_NO.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Tire Specs</strong></td>
											<td style="text-align: left;"><strong>'.$row->TIRE_SPECS.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Battery</strong></td>
											<td style="text-align: left;"><strong>'.$row->BATTERY.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Displacement</strong></td>
											<td style="text-align: left;"><strong>'.$row->DISPLACEMENT.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">&nbsp;</td>
											<td style="text-align: left;"><strong>Year Model</strong></td>
											<td style="text-align: left;"><strong>'.$row->YEAR_MODEL.'</strong></td>
											<td colspan="3" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="7" style="text-align: center;">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="7" style="text-align: center;">&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="5">
									<table border="0" style="padding: 1px;font-size: 10px;font-weight: bold;">
										<tr>
											<td style="text-align: left;width: 245px;">'.nl2br($row->ITEMS1).'</td>
											<td style="text-align: left;width: 215px;">'.nl2br($row->ITEMS2).'</td>
											<td>
												<table border="0" style="padding: 1px;font-size: 12px;font-weight: bold;">
													<tr>
														<td style="text-align: left;width: 120px;">Vatables Sales</td>
														<td style="text-align: right;width: 90px;">'. number_format($row->VATABLE_SALES,2) .'</td>
													</tr>
													<tr>
														<td style="text-align: left;">Exempted Sales</td>
														<td style="text-align: right;">0.00</td>
													</tr>
													<tr>
														<td style="text-align: left;">Zero Rated Sales</td>
														<td style="text-align: right;">0.00</td>
													</tr>
													<tr>
														<td style="text-align: left;">Discount</td>
														<td style="text-align: right;">'. number_format($row->DISCOUNT,2) .'</td>
													</tr>
													<tr>
														<td style="text-align: left;">Amt. Net of Vat</td>
														<td style="text-align: right;">'. number_format($row->AMT_NET_OF_VAT,2) .'</td>
													</tr>
													<tr>
														<td style="text-align: left;">VAT Amount</td>
														<td style="text-align: right;">'. number_format($row->VAT_AMOUNT,2) .'</td>
													</tr>
													<tr>
														<td colspan="2">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="2">&nbsp;</td>
													</tr>
													<tr>
														<td style="text-align: left;font-size: 14px;">Total Sales PHP</td>
														<td style="text-align: right;font-size: 14px;">'. number_format($row->TOTAL_SALES,2) .'</td>
													</tr>
												</table>
											</td>
											
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="5">&nbsp;</td>
							</tr>
							'.$fleet_name.'
							<tr>
								<td colspan="5">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="5">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="5" style="font-size: 11px;text-align: justified">Purchaser hereby expressly agrees that any action arising out or in condition with this invoice shall be instituted in the proper court of Province of Laguna, Philippines, and that in case of litigation purchaser shall pay as attorney’s fees an amount equivalent to 25% of the total sum due which attorney’s fees shall however, in no case be less than P5.00. Any account not paid within 90 days from the due date shall bear interest at the rate of 4% per month except as otherwise expressly stipulated herein. This sale is governed by the agreement on basic terms and conditions of the sales contract between the parties.</td>
							</tr>
							<tr>
								<td colspan="5">
									<table border="0" style="padding: 3px;font-size: 11px;">
										<tr>
											<td>Prepared By</td>
											<td>Checked By</td>
											<td>Approved By</td>
											<td>Released By</td>
										</tr>
										<tr>
											<td colspan="4">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="4">&nbsp;</td>
										</tr>
										<tr style="font-size:9px;">
											<td><strong>BANAYBNAY, ROSELLE ANNE D.</strong></td>
											<td><strong>DE MATTA, MEDARDO A.</strong></td>
											<td><strong>MENDOZA, ROBERTO P.</strong></td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td colspan="4">Received the above merchandise in good order and condition, subject to the terms and conditions of the Sales Contract.</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="5"></td>
							</tr>
							<tr>
								<td colspan="5">
									<table border="0" style="font-size: 10px;">
										<tr>
											<td style="width: 170px;"></td>
											<td style="width: 85px;">&nbsp;</td>
											<td style="width: 110px;"></td>
											<td style="width: 40px;">&nbsp;</td>
											<td style="width: 130px;">Valid Until</td>
											<td style="width: 155px;">December 31, 2021</td>
										</tr>
										<tr>
											<td colspan="4"></td>
											<td>BIR PERMIT TO USE NO.:</td>
											<td>1701_0124_PTU_CAS_000056</td>
										</tr>
										<tr>
											<td colspan="4"></td>
											<td>Date issued:</td>
											<td>January 3, 2017</td>
										</tr>
										<tr>
											<td style="border-bottom: 1px solid black;"></td>
											<td>&nbsp;</td>
											<td style="border-bottom: 1px solid black;"></td>
											<td>&nbsp;</td>
											<td>Valid until</td>
											<td>December 31, 2021</td>
										</tr>
										<tr>
											<td>Dealer Representative</td>
											<td>&nbsp;</td>
											<td>Date</td>
											<td>&nbsp;</td>
											<td>Document series range:</td>
											<td>40300000001-40399999999</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="5" style="text-align: center;line-height: 120%">
									<strong>THIS SALES INVOICE (VEHICLE) SHALL BE VALID FOR FIVE (5) YEARS FROM THE DATE OF THE PERMIT TO USE</strong>
								</td>
							</tr>
						</table>';
			$ctr++;
		}
		$this->pdf($content);
	}
	
	public function pdf($contents){
		// generate pdf content
		$this->load->library('pdf_invoice');
		// create new PDF document
		$pdf = new PDF_INVOICE('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
		
		foreach($contents as $content){
			$pdf->AddPage();
		// output the HTML content
			$pdf->writeHTML($content, true, false, true, false, '');
		}
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		//~ $this->filename = 'C:/wamp64/www/parts_dbs/files/dr.pdf';
		$pdf->Output('Invoice','I');
	}
}
