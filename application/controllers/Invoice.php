<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {
	
	public function __construct(){
		parent::__construct();

		$this->load->model('invoice_model');
		session_check();
	}
	
	public function list_(){
		
		$data['result'] = $this->invoice_model->get_daily_invoiced_units();
		$data['content'] = 'invoice_list_view';
		$data['title'] = 'Invoiced Units';
		
		//~ echo $data['customer_id'] . ' ' . $data['from_date'] . ' ' . $data['to_date'];
		
		$this->load->view('include/template',$data);
		
	}
	
	public function for_invoice(){
		
		$customer_id = $this->input->post('customer_id') == NULL ? 1:$this->input->post('customer_id');
		
		$data['result'] = $this->invoice_model->get_released_units_per_customer($customer_id);
		$data['customers'] = $this->invoice_model->get_released_units_customers();
		$data['customer_id'] = $customer_id;
		$data['content'] = 'for_invoice_view';
		$data['title'] = 'For Invoice Units';
		
		//~ echo $data['customer_id'] . ' ' . $data['from_date'] . ' ' . $data['to_date'];
		
		$this->load->view('include/template',$data);
		
	}
	
	public function print_invoice(){
		
		//~ $trx_numbers = '40300009167';
		$trx_numbers = '40300015727';
		$row = $this->invoice_model->get_invoice_details($trx_numbers);
		$row = $row[0];
		
		//~ echo '<pre>';
		//~ print_r($row);
		//~ echo '</pre>';
		
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
		
		$content = '';
		
		$content .= '<table border="0" style="padding: 1px;font-size: 12px;">
						<tr>
							<td colspan="5" style="text-align: right;font-size: 15px;"><strong>SALES INVOICE (VEHICLE)</strong></td>
						</tr>
						<tr>
							<td colspan="5" style="text-align: right;font-size: 16px;"><span style="font-size: 12px;">No.</span> &nbsp;&nbsp; <strong>'.$row->TRX_NUMBER.'</strong></td>
						</tr>
						<br />
						<br />
						<tr>
							<td colspan="3" style="font-size:10px;width:417px;">SOLD TO</td>
							<td colspan="1" style="width: 90px;">Date</td>
							<td colspan="1" style="width: 165px;"><strong>'. date('F j, Y', strtotime($row->TRX_DATE)) .'</strong></td>
						</tr>
						<tr>
							<td colspan="1" style="width:17px;">&nbsp;</td>
							<td colspan="2" style="font-size: 12px;width:400px;"><strong>'. $row->PARTY_NAME .'</strong></td>
							<td colspan="1">PO/SO Ref</td>
							<td colspan="1"><strong>'. $row->SO_NUMBER .'</strong></td>
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
		$this->pdf($content);
	}
	
	public function sales_daily_summary_by_dealer_form(){
		
		$data = array(
				'content'     => 'report_form/sales_daily_summary_by_dealer_form',
				'title'       => 'Sales Daily Range Summary By Dealer Report'
			);

			$this->load->view('include/template', $data);
	}
	
	public function sales_daily_summary_by_dealer_report(){

		//~ $from_date = '01-OCT-17';
		//~ $to_date = '31-OCT-17';
		$from_date = date('d-M-y', strtotime($this->input->post('from')));
		$to_date = date('d-M-y', strtotime($this->input->post('to')));
		
		//~ echo $from_date;die();
		
		$rows = $this->invoice_model->get_sales_daily_summary_by_dealer($from_date, $to_date);
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
								<td colspan="28" style="font-size: 12px;"><strong>Sales Daily Range Summary By Dealer</strong></td>
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
		
		$this->pdf2($content,'L');
	}
	
	public function pdf($content){
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
		
		$pdf->AddPage();
		// output the HTML content
		$pdf->writeHTML($content, true, false, true, false, '');
		
		// ---------------------------------------------------------
		// Close and output PDF document
		// tdis metdod has several options, check tde source code documentation for more information.
		
		$this->filename = 'report.pdf';
		$pdf->Output($this->filename,'I');
	}
	
	public function pdf2($content, $orientation = 'P'){
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
