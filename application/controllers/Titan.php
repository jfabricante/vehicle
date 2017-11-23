<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Titan extends CI_Controller {

	public function __construct() {
      	parent::__construct();
        
        $this->load->model('titan_model');
        $this->load->helper('date');
        //session_start();
  	}
    // function isWeekend() {
    //     $date = date('2017-07-02');
    //         if(date('w', strtotime($date)) == 6 || date('w', strtotime($date)) == 0) {
    //             echo '1';
    //         } else {
    //             echo '0'; 
    //         }
    // }
    public function lpda_report()
    {
        $month = array(
                  array("January","01"),
                  array("February","02"),
                  array("March","03"),
                  array("April","04"),
                  array("May","05"),
                  array("June","06"),
                  array("July","07"),
                  array("August","08"),
                  array("September","09"),
                  array("October","10"),          
                  array("November","11"),
                  array("December","12")
            );

        $data['all_month'] = $month;
        $data['year'] = date("Y",now('asia/manila'));

        $data['content'] = 'lpda_report_view';
        $data['title'] = 'LPDA';
        $this->load->view('include/template',$data);
    }

    public function smda_report()
    {
        $month = array(
                  array("January","01"),
                  array("February","02"),
                  array("March","03"),
                  array("April","04"),
                  array("May","05"),
                  array("June","06"),
                  array("July","07"),
                  array("August","08"),
                  array("September","09"),
                  array("October","10"),          
                  array("November","11"),
                  array("December","12")
            );

        $data['all_month'] = $month;
        $data['year'] = date("Y",now('asia/manila'));

        $data['content'] = 'smda_report_view';
        $data['title'] = 'SMDA';
        $this->load->view('include/template',$data);
    }

    public function ajax_get_lpda_details()
    {
        $post = $this->input->post();
        $need_by_date = date("Y",now('asia/manila')) . '-' . $post['month'];
        $po = $this->titan_model->get_po($need_by_date);

        $options = "<option value='1'>Nothing Selected</option>";
        
        foreach($po as $row){
            $options .= "<option value='" . $row->PO_NUM . "'> " . $row->PO_NUM . ' - ' . $row->VENDOR_NAME . "</option>";
        }
        
        echo $options;
    }

    public function lpda($lpda_no,$month,$remarks,$coordinator)
    {
        
        
        ini_set('memory_limit', '-1');  
        ini_set('max_execution_time', 7200);
        set_time_limit(7200);

        

        $data = $this->titan_model->get_lpda_data($lpda_no,$month);
        if(!empty($data))
        {


        $remarks = str_replace("%20"," ",$remarks);
        $coordinator = str_replace("%20"," ",$coordinator);
        //$part_info_data = $this->titan_model->get_part_info('20100003515','1266','02');
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit();
        $dateObj   = DateTime::createFromFormat('!m', $data[0]->MONTH_FN);
        $monthName = $dateObj->format('F');
        
        $this->load->library('Pdf_titan');

        $pdf = new Pdf_titan(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setData($data[0]);
        $pdf->setRemarks($remarks);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('LPDA');
        $pdf->SetSubject('LPDA');
        $pdf->SetKeywords('Titan, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '40', '2');
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, 0);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);

        $pdf->AddPage('L', 'A4');
        $html = '';
        
        // $part_info = array();
        // $part_info_ctr = 0;     
        // foreach ($part_info_data as $key) {
        //     $part_info[$part_info_ctr] = $key->PART_INFO;
        //     $part_info_ctr++;
        // }
        // $part_info = array_filter($part_info);

        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit();
        $time = strtotime($data[0]->IYEAR.'-'.$data[0]->MONTH_FN.'-1');
        $forecast_month1 = date("M Y", strtotime("+1 month", $time));
        $forecast_month2 = date("M Y", strtotime("+2 month", $time));
        $forecast_month3 = date("M Y", strtotime("+3 month", $time));
        // echo $forecast_month1;
        //  exit();

        $number_month = cal_days_in_month(CAL_GREGORIAN,$data[0]->MONTH_FN,$data[0]->IYEAR);
        $day = 1;
        $day_dif = (int)(31 - $number_month);
        $day_ctr = (int)(31 - $day_dif);
        $th1 = '';
        $th2 = '';

        

        while($day <= 31)
        {
            if($day <= $day_ctr)
            {
                $th1 .= '<th style="width: 25px; border-left: 1px solid gray;">'.$day.'</th>';
            }
            else
            {
                $th1 .= '<th style="width: 25px; border-left: 1px solid gray;"></th>';
            }

            if($day <= 30)
            {
                $th2 .='<th style="width: 25px; border-top: 1px solid gray; border-left: 1px solid gray;" bgcolor="#D9D9D9"></th>';
            }
            else
            {
                $th2 .='<th style="width: 25px; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray;" bgcolor="#D9D9D9"></th>';
            }
            $day++;
        }

        $ctr = 0;
        $ctr_pagebreak = ceil(count($data)/18);
        $part_info_ctr = 1; 

        foreach ($data as $row) {
            if($ctr == 0)
            {
                $html .= '<table border="0" style="padding: 1px 1px;">
                            <tr style="text-align: center; font-size: 12px; font-weight: bold;">
                                <th style="width: 100px; font-weight: bold; border-left: 1px solid gray; border-top: 1px solid gray;">PART NO</th>
                                <th style="width: 840px; border-left: 1px solid gray; border-top: 1px solid gray;">FIRMED ORDER</th>
                                <th style="width: 99px; border-left: 1px solid gray; border-top: 1px solid gray; border-right: 1px solid gray;">FORECAST</th>
                            </tr>
                         </table>';
                $html .='<table border="0" style="padding: 1px 1px;"><tr style="text-align: center; font-size: 10px; font-weight: normal;">
                                <th style="width: 100px; font-weight: bold; font-size: 11px; border-bottom: 1px solid gray; border-left: 1px solid gray;">(DESCRIPTION)</th>
                                <th style="width: 35px; font-weight: bold; font-size: 7px; border-left: 1px solid gray; border-bottom: 1px solid gray; border-top: 1px solid gray; border-right: 1px solid gray;" >USAGE</th>
                                <th style="width: 30px; font-weight: bold; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray;border-right: 1px solid gray; ">TOTAL</th>
                                
                                <th style="width: 38px; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; "></th>
                                <th style="width: 737px; text-align: left; border-bottom: 1px solid gray; border-top: 1px solid gray;">'.$monthName.' '.$row->IYEAR.'</th>
                                <th style="width: 33px; border-left: 1px solid gray; border-bottom: 1px solid gray; font-size: 8px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month1.'</th>
                                <th style="width: 33px; border-bottom: 1px solid gray; font-size: 8px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month2.'</th>
                                <th style="width: 33px; border-bottom: 1px solid gray; font-size: 8px; border-right: 1px solid gray; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month3.'</th>
                            </tr></table><table border="0" style="padding: 1px 1px;">';

                $html .='<tr style="text-align: center; font-size: 6px; font-weight: normal;">
                                <th style="width: 100px; font-weight: bold; font-size: 10px; border-left: 1px solid gray;"></th>
                                <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray;"></th>
                                <th style="width: 30px; font-weight: bold; font-size: 6px;"></th>
                                '.$th1.'
                                <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                            </tr></table>';  
            }
            $html .='<table border="0" style="padding: 0px 0px;">';
            if($row->RID == 1)
            {
                $html .='   <tr style="text-align: center; font-size: 6px; font-weight: bold;">
                                <th style="width: 100px; font-weight: normal; font-size: 7px; text-align: left; border-left: 1px solid gray; border-top: 1px solid gray;">&nbsp;&nbsp;'.$row->PART_NO.'</th>
                                <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray; border-top: 1px solid gray;">1</th>
                                <th style="width: 30px; font-weight: bold; font-size: 6px; border-left: 1px solid gray; border-top: 1px solid gray;" bgcolor="#D9D9D9"></th>
                                '.$th2.'
                                <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->M1.'</th>
                                <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->M2.'</th>
                                <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->M3.'</th>
                            </tr>';

                $html .='
                            <tr style="text-align: center; font-size: 7px; font-weight: bold;">
                                <th style="width: 100px; font-weight: normal; font-size: 7px; text-align: left; border-left: 1px solid gray;">&nbsp;&nbsp;'.$row->PART_INFO.'</th>
                                <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray;">'.$row->I_IUSAGE.'</th>
                                <th style="width: 30px; font-weight: bold; font-size: 7px; border-left: 1px solid gray; text-align: left;">&nbsp;&nbsp;'.$row->TOT_QTY.'</th>
                                
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_01.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_02.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_03.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_04.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_05.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_06.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_07.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_08.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_09.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_10.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_11.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_12.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_13.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_14.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_15.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_16.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_17.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_18.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_19.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_20.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_21.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_22.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_23.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_24.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_25.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_26.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_27.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_28.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_29.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_30.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_31.'</th>

                                <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                            </tr>';
                            
            }
            else
            {
                $html .='
                            <tr style="text-align: center; font-size: 5px; font-weight: normal;">
                                <th style="width: 100px; font-weight: normal; font-size: 7px;  height: 20px; border-left: 1px solid gray;"></th>
                                <th style="width: 35px; font-weight: normal; font-size: 8px; border-left: 1px solid gray;"></th>
                                <th style="width: 30px; font-weight: bold; font-size: 6px; border-left: 1px solid gray;"></th>
                                
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_01.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_02.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_03.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_04.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_05.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_06.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_07.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_08.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_09.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_10.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_11.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_12.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_13.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_14.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_15.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_16.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_17.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_18.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_19.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_20.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_21.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_22.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_23.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_24.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_25.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_26.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_27.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_28.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_29.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_30.'</th>
                                <th style="width: 25px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_31.'</th>

                                <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                            </tr>';
            }
            $html .= '</table>';

            $ctr++;
            if($ctr > 17)
            {
                $ctr = 0;
                    
                $ctr_pagebreak--;
                $html .= '<table border="0" style="padding: 1px 1px;">
                        <tr>
                            <th style="width: 1038px; border-top: 1px solid gray;"></th>
                        </tr>
                        <tr>
                            <th style="width: 1038px; "></th>
                        </tr>
                      </table>';    
                $html .= '<table border="0" style="padding: 1px 1px;">
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 388px; border-top: 1px solid gray; border-left: 1px solid gray;">NOTE: Two (2) days prior to schedule reflected in LPDA, Suppliers shall inform and give delivery commitment to IPC if required delivery date will not be met, otherwise Suppliers will be charged to all the cost incurred by production line-stop due to unavailabity of parts.</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">RECEIVED BY</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">PCD-PRODUCTION PLANNING AND CONTROL SECTION</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; font-size: 6px;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                            </tr>
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 388px; border-left: 1px solid gray; font-weight: bold;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                            </tr>
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 388px; border-left: 1px solid gray; font-weight: bold;">DELIVERY TIME FRAME:</th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                            </tr>
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 388px; border-bottom: 1px solid gray; border-left: 1px solid gray;">2:45 PM - 4:00 PM</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">ANTON MINA</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">MARIVIC GOTIONGCO</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">REINA VERGARA</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; text-align: center; font-size: 10px;">'.$coordinator.'</th>
                            </tr>
                          </table>';
                if($ctr_pagebreak-1 >= 0)
                {
                     $html .= '<br pagebreak="true"/>';
                }
            } 
            $part_info_ctr++;
        }      
        $footer = '';   
        if($ctr_pagebreak-1 == 0) 
        {
            $html .= '<table border="0" style="padding: 1px 1px;">
                    <tr>
                        <th style="width: 1038px; border-top: 1px solid gray;"></th>
                    </tr>
                  </table>';    
            $footer .= '<table border="0" style="padding: 1px 1px;">
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 388px; border-top: 1px solid gray; border-left: 1px solid gray;">NOTE: Two (2) days prior to schedule reflected in LPDA, Suppliers shall inform and give delivery commitment to IPC if required delivery date will not be met, otherwise Suppliers will be charged to all the cost incurred by production line-stop due to unavailabity of parts.</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">RECEIVED BY</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">PCD-PRODUCTION PLANNING AND CONTROL SECTION</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; font-size: 6px;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; font-size: 6px;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                        </tr>
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 388px; border-left: 1px solid gray; font-weight: bold;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                        </tr>
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 388px; border-left: 1px solid gray; font-weight: bold;">DELIVERY TIME FRAME:</th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                        </tr>
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 388px; border-bottom: 1px solid gray; border-left: 1px solid gray;">2:45 PM - 4:00 PM</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">ANTON MINA</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">MARIVIC GOTIONGCO</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">REINA VERGARA</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; text-align: center; font-size: 10px;">'.$coordinator.'</th>
                        </tr>
                      </table>';

        }

            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
            $pdf->writeHTMLCell(0, 0, '', 185, $footer, 0, 1, 0, true, '', true);
            $pdf->Output("lpda-" . date('Ymdhis') . ".pdf",'I');
        }
        else
        {
            $data['heading'] = 'LPDA No - '.$lpda_no;
            $data['message'] = 'No Data';
            $this->load->view('errors/html/error_general',$data);
        }   
       
       
    }

    // public function smda($lpda_no,$month)
    public function smda($lpda_no,$month,$remarks)
    {
        
        
        ini_set('memory_limit', '-1');  
        ini_set('max_execution_time', 7200);
        set_time_limit(7200);
        $data = $this->titan_model->get_lpda_data($lpda_no,$month);

        $remarks = str_replace("%20"," ",$remarks);

        //$part_info_data = $this->titan_model->get_part_info('20100003515','1266','02');
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit();
        $dateObj   = DateTime::createFromFormat('!m', $data[0]->MONTH_FN);
        $monthName = $dateObj->format('F');
        
        $this->load->library('Pdf_smda');

        $pdf = new Pdf_smda(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setData($data[0]);
        $pdf->setRemarks($remarks);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('SMDA');
        $pdf->SetSubject('SMDA');
        $pdf->SetKeywords('Titan, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('2', '40', '2');
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, 0);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);

        $pdf->AddPage('L', 'A4');
        $html = '';
        
        // $part_info = array();
        // $part_info_ctr = 0;     
        // foreach ($part_info_data as $key) {
        //     $part_info[$part_info_ctr] = $key->PART_INFO;
        //     $part_info_ctr++;
        // }
        // $part_info = array_filter($part_info);

        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit();
        $time = strtotime($data[0]->IYEAR.'-'.$data[0]->MONTH_FN.'-1');
        $forecast_month1 = date("M Y", strtotime("+1 month", $time));
        $forecast_month2 = date("M Y", strtotime("+2 month", $time));
        $forecast_month3 = date("M Y", strtotime("+3 month", $time));
        // echo $forecast_month1;
        //  exit();

        $number_month = cal_days_in_month(CAL_GREGORIAN,$data[0]->MONTH_FN,$data[0]->IYEAR);
        $day = 1;
        $day_dif = (int)(31 - $number_month);
        $day_ctr = (int)(31 - $day_dif);
        $th1 = '';
        $th2 = '';

        

        while($day <= 31)
        {
            if($day <= $day_ctr)
            {
                $th1 .= '<th style="width: 23px; border-left: 1px solid gray;">'.$day.'</th>';
            }
            else
            {
                $th1 .= '<th style="width: 23px; border-left: 1px solid gray;"></th>';
            }

            if($day <= 30)
            {
                $th2 .='<th style="width: 23px; border-top: 1px solid gray; border-left: 1px solid gray;" bgcolor="#D9D9D9"></th>';
            }
            else
            {
                $th2 .='<th style="width: 23px; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray;" bgcolor="#D9D9D9"></th>';
            }
            $day++;
        }

        $ctr = 0;
        $ctr_pagebreak = ceil(count($data)/20);
        $part_info_ctr = 1; 

        foreach ($data as $row) {
            if($ctr == 0)
            {
                $html .= '<table border="0" style="padding: 1px 1px;">
                            <tr style="text-align: center; font-size: 12px; font-weight: bold;">
                                <th style="width: 100px; font-weight: bold; border-left: 1px solid gray; border-top: 1px solid gray;">PART NO</th>
                                <th style="width: 828px; border-left: 1px solid gray; border-top: 1px solid gray;">FIRMED ORDER</th>
                                <th style="width: 99px; border-left: 1px solid gray; border-top: 1px solid gray; border-right: 1px solid gray;">FORECAST</th>
                            </tr>
                         </table>';
                $html .='<table border="0" style="padding: 1px 1px;"><tr style="text-align: center; font-size: 10px; font-weight: normal;">
                                <th style="width: 100px; font-weight: bold; font-size: 11px; border-bottom: 1px solid gray; border-left: 1px solid gray;">(DESCRIPTION)</th>
                                <th style="width: 35px; font-weight: bold; font-size: 7px; border-left: 1px solid gray; border-bottom: 1px solid gray; border-top: 1px solid gray; border-right: 1px solid gray;" >UOM</th>
                                <th style="width: 33px; font-weight: bold; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; ">TOTAL</th>
                                <th style="width: 15px; border-bottom: 1px solid gray; border-top: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 38px; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; "></th>
                                <th style="width: 707px; text-align: left; border-bottom: 1px solid gray; border-top: 1px solid gray;">'.$monthName.' '.$row->IYEAR.'</th>
                                <th style="width: 33px; border-left: 1px solid gray; border-bottom: 1px solid gray; font-size: 8px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month1.'</th>
                                <th style="width: 33px; border-bottom: 1px solid gray; font-size: 8px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month2.'</th>
                                <th style="width: 33px; border-bottom: 1px solid gray; font-size: 8px; border-right: 1px solid gray; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month3.'</th>
                            </tr></table><table border="0" style="padding: 1px 1px;">';

                $html .='<tr style="text-align: center; font-size: 6px; font-weight: normal;">
                                <th style="width: 100px; font-weight: bold; font-size: 10px; border-left: 1px solid gray;"></th>
                                <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray;"></th>
                                <th style="width: 80px; font-weight: bold; font-size: 6px;"></th>
                                '.$th1.'
                                <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                            </tr></table>';  
            }
            $html .='<table border="0" style="padding: 0px 0px;">';
            if($row->RID == 1)
            {
                $html .='   <tr style="text-align: center; font-size: 6px; font-weight: bold;">
                                <th style="width: 100px; font-weight: normal; font-size: 7px; text-align: left; border-left: 1px solid gray; border-top: 1px solid gray;">&nbsp;&nbsp;'.$row->PART_NO.'</th>
                                <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray; border-top: 1px solid gray;">1</th>
                                <th style="width: 80px; font-weight: bold; font-size: 6px; border-left: 1px solid gray; border-top: 1px solid gray;" bgcolor="#D9D9D9"></th>
                                '.$th2.'
                                <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->M1.'</th>
                                <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->M2.'</th>
                                <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->M3.'</th>
                            </tr>';

                $html .='
                            <tr style="text-align: center; font-size: 6px; font-weight: bold;">
                                <th style="width: 100px; font-weight: normal; font-size: 7px; text-align: left; border-left: 1px solid gray;">&nbsp;&nbsp;'.$row->PART_INFO.'</th>
                                <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray;">'.$row->I_IUSAGE.'</th>
                                <th style="width: 80px; font-weight: bold; font-size: 6px; border-left: 1px solid gray; text-align: left;">&nbsp;&nbsp;'.$row->TOT_QTY.'</th>
                                
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_01.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_02.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_03.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_04.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_05.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_06.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_07.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_08.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_09.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_10.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_11.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_12.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_13.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_14.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_15.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_16.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_17.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_18.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_19.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_20.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_21.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_22.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_23.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_24.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_25.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_26.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_27.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_28.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_29.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_30.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_31.'</th>

                                <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                            </tr>';
                            
            }
            else
            {
                $html .='
                            <tr style="text-align: center; font-size: 4px; font-weight: normal;">
                                <th style="width: 100px; font-weight: normal; font-size: 7px;  height: 20px; border-left: 1px solid gray;"></th>
                                <th style="width: 35px; font-weight: normal; font-size: 8px; border-left: 1px solid gray;"></th>
                                <th style="width: 80px; font-weight: bold; font-size: 6px; border-left: 1px solid gray;"></th>
                                
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_01.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_02.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_03.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_04.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_05.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_06.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_07.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_08.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_09.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_10.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_11.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_12.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_13.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_14.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_15.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_16.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_17.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_18.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_19.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_20.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_21.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_22.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_23.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_24.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_25.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_26.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_27.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_28.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_29.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_30.'</th>
                                <th style="width: 23px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_31.'</th>

                                <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                                <th style="width: 33px; border-right: 1px solid gray;"></th>
                            </tr>';
            }
            $html .= '</table>';

            $ctr++;
            if($ctr > 19)
            {
                $ctr = 0;
                    
                $ctr_pagebreak--;
                $html .= '<table border="0" style="padding: 1px 1px;">
                        <tr>
                            <th style="width: 1027px; border-top: 1px solid gray;"></th>
                        </tr>
                      </table>';  
                $html .= '<table border="0" style="padding: 1px 1px;">
                    <tr>
                        <th style="width: 1027px;">Please enter our order as stated subject to our terms and conditions herein defined</th>
                    </tr>
                    <tr>
                        <th style="width: 1027px; font-size: 8px;"></th>
                    </tr>
                  </table>';   
                $html .= '<table border="0" style="padding: 1px 1px;">
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 377px; border-top: 1px solid white; border-left: 1px solid white;"></th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">RECEIVED BY</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">PCD-PRODUCTION PLANNING AND CONTROL SECTION</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                                <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                            </tr>
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 377px; border-left: 1px solid white; font-weight: bold;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                            </tr>
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 377px; border-left: 1px solid white; font-weight: bold;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                            </tr>
                            <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                                <th style="width: 377px; border-bottom: 1px solid white; border-left: 1px solid white;"></th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray;"></th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center;">ANTON MINA</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center;">MARIVIC GOTIONGCO</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center;">REINA VERGARA</th>
                                <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; text-align: center;">KATRINA SAFLOR</th>
                            </tr>
                      </table>';
                if($ctr_pagebreak-1 >= 0)
                {
                     $html .= '<br pagebreak="true"/>';
                }
            } 
            $part_info_ctr++;
        }         
        if($ctr_pagebreak-1 == 0) 
        {
            $html .= '<table border="0" style="padding: 1px 1px;">
                    <tr>
                        <th style="width: 1027px; border-top: 1px solid gray;"></th>
                    </tr>
                  </table>';    
            $footer = '<table border="0" style="padding: 1px 1px;">
                    <tr>
                        <th style="width: 1027px; font-size: 8px;">Please enter our order as stated subject to our terms and conditions herein defined</th>
                    </tr>
                    <tr>
                        <th style="width: 1027px; font-size: 8px;"></th>
                    </tr>
                  </table>';  
            $footer .= '<table border="0" style="padding: 1px 1px;">
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 377px; border-top: 1px solid white; border-left: 1px solid white;"></th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">RECEIVED BY</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">PCD-PRODUCTION PLANNING AND CONTROL SECTION</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                            <th style="width: 130px; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray;">PRODUCTION PLANNING AND CONTROL SECTION</th>
                        </tr>
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 377px; border-left: 1px solid white; font-weight: bold;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                        </tr>
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 377px; border-left: 1px solid white; font-weight: bold;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                        </tr>
                        <tr style="text-align: left; font-size: 8px; font-weight: normal;">
                            <th style="width: 377px; border-bottom: 1px solid white; border-left: 1px solid white;"></th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray;"></th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center;">ANTON MINA</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center;">MARIVIC GOTIONGCO</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center;">REINA VERGARA</th>
                            <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; text-align: center;">KATRINA SAFLOR</th>
                        </tr>
                      </table>';

        }

        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $pdf->writeHTMLCell(0, 0, '', 185, $footer, 0, 1, 0, true, '', true);
        $pdf->Output("smda-" . date('Ymdhis') . ".pdf",'I');
    }

    public function display_data()
    {
        $data = $this->titan_model->get_lpda_data('20100003204','05');
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit();
    }

}
