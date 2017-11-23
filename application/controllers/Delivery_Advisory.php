<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delivery_Advisory extends CI_Controller {

	public function __construct() {
      	parent::__construct();
        
        $this->load->model('titan_model_');
        $this->load->helper('date');
        $this->load->helper('url');
        //$this->load->model('login_model');
        $this->load->library('session');
  	}

    public function delivery_advisory_report()
    {
        $post = $this->input->post();
        $month = array(
                  array("month"=>"January","id"=>"01"),
                  array("month"=>"February","id"=>"02"),
                  array("month"=>"March","id"=>"03"),
                  array("month"=>"April","id"=>"04"),
                  array("month"=>"May","id"=>"05"),
                  array("month"=>"June","id"=>"06"),
                  array("month"=>"July","id"=>"07"),
                  array("month"=>"August","id"=>"08"),
                  array("month"=>"September","id"=>"09"),
                  array("month"=>"October","id"=>"10"),          
                  array("month"=>"November","id"=>"11"),
                  array("month"=>"December","id"=>"12")
            );

        $data['all_month'] = $month;
        // echo "<pre>";
        // print_r($post);
        // echo "</pre>";
        // exit();
        $report_type = array(
                  array("type" => "LOCAL","id" => "LOCAL"),
                  array("type" => "SUBMATS","id" => "SM"),
                  array("type" => "CSO","id" => "CSO")
            );
        
        $report_type = $report_type;
        
        $data['report_type'] = $report_type;

        $data['report'] = 1;
        $data['year'] = date("Y",now('asia/manila'));
        $data['month'] = (!empty($post['month']))? $post['month'] : 01;
        //$data['lpda_list'] = $lpda_list;
        $data['content'] = 'lpda_report_view_';
        $data['title'] = 'Delivery Advisory';
        $this->load->view('include/template',$data);
        // $data['content'] = 'lpda_report_view';
        // $data['title'] = 'LPDA';
        // $data['user'] = $this->login_model->get_user_by_id($this->session->userdata('user_id_tax_system'));
        // $this->load->view('template/template',$data);
    }

    public function ajax_get_po_details()
    {
        $post = $this->input->post();
        $report = $post['report'];
        $lpda_list = $this->titan_model_->get_po($report);
        $data['lpda_list'] = $lpda_list;
        $data['lpda_no'] = 1;
        $ajax = $this->load->view('lpda_po_list',$data,true);
        //echo json_encode($lpda_list);
        echo $ajax;
    }
    public function ajax_get_lpda_details()
    {
        $post = $this->input->post();
        $details = $this->titan_model_->get_po_details($post['lpda_no']);

        $lpda_details = array(
            'BUYER_NAME' => $details[0]->BUYER_NAME, 
            'ERR_MSGS' => $details[0]->ERR_MSGS,
            'MONTH' => $details[0]->F_MONTH_SDESC
            );

        echo json_encode($lpda_details);
    }

    public function delivery_advisory()
    {
        $post = $this->input->post();

        ini_set('memory_limit', '-1');  
        ini_set('max_execution_time', 7200);
        set_time_limit(7200);

        $po_header = $this->titan_model_->get_po_header($post['lpda_no']);


        $data = $this->titan_model_->get_header_date($post['lpda_no'],$post['start_date']);
        $data = array_slice($data,0,27);

      
        
        $attribute_ctr = TRIM($data[0]['ATTRIBUTE_NO']);
        $ctr_attr = 1;
        $attribute_array = array();
        $attribute_array28 = array();

        while($ctr_attr <= 27)
        {
            array_push($attribute_array,'lpda_details.attribute_'.sprintf("%02d", $attribute_ctr).' as attribute_'.sprintf("%02d", $ctr_attr));
            $attribute_ctr++;
            $ctr_attr++;
        }

        while($attribute_ctr <= 60)
        {
            array_push($attribute_array28,'NVL (lpda_details.attribute_'.sprintf("%02d", $attribute_ctr).',0)');
            $attribute_ctr++;
        }
        $remarks = $post['remarks'];
        if($post['forecast_flag'] == 'no')
        {
            $remarks = $remarks . ' - WITHOUT FORECAST';
            $withforecast = 'AND lpda_details.total_qty IS NOT NULL';
        }
        else
        {
            $remarks = $remarks . ' - WITH FORECAST';
            $withforecast = '';
        }

        $attribute_array28 = implode("+", $attribute_array28);
        $attribute_array = implode(",", $attribute_array);
        $lpda_ = $this->titan_model_->get_lpda_data($post['lpda_no'],$attribute_array,$attribute_array28,$post['report'],$withforecast);
        //   echo "<pre>";
        // print_r($po_header[0]);
        // echo "</pre>";
        // exit();
        if(!empty($lpda_))
        {
            if($post['report'] != 'CSO')
            {
                
                $coordinator = $post['coordinator'];
                $supervisor = $post['supervisor'];

                $dateObj   = DateTime::createFromFormat('!m', $po_header[0]['NUM_MONTH_CREATED']);
                $monthName = $dateObj->format('F');
                
                $this->load->library('Pdf_titan_');

                

                $pdf = new Pdf_titan_(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->setData($po_header[0]);
                $pdf->setReport($post['report']);
                $pdf->setRemarks($remarks);
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Isuzu');
                $pdf->SetTitle('DA');
                $pdf->SetSubject('DA');
                $pdf->SetKeywords('DA, isuzu');

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
                
               // $month = $post['month'];
                // $time = strtotime('20'.$po_header[0]['NUM_YEAR_CREATED'].'-'.$po_header[0]['NUM_MONTH_CREATED'].'-1');
                $time = strtotime($po_header[0]['NUM_YEAR_CREATED'].'-'.$post['month']);

                $forecast_month1 = date("M Y", strtotime("+1 month", $time));
                $forecast_month2 = date("M Y", strtotime("+2 month", $time));
                $forecast_month3 = date("M Y", strtotime("+3 month", $time));

                $th1 = '';
                $th2 = '';
                $date_header = '';

                $prev_month = '';
                $cur_month = '';
                $ctr = 0 ;
                foreach($data as $key)
                {
                    if($key['MONTH_NEEDED'] != $prev_month )
                    {
                        $cur_month = $key['MONTH_NEEDED'];
                    }
                    else
                    {
                        $cur_month = '';
                    }
                    $th1 .= '<th style="width: 27.68px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$key['DAY_NEEDED'].'</th>';
                    $th2 .= '<th style="width: 27.68px; border-left: 1px solid gray; border-top: 1px solid gray;" bgcolor="#D9D9D9"></th>';
                    
                    if($cur_month != '')
                    {
                        $date_header .= '<th style="width: 27.68px; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; border-left: 1px solid gray;"><b>'.$cur_month.'</b></th>';
                        $prev_month = $cur_month;
                    }
                    else
                    {
                        $date_header .= '<th style="width: 27.68px; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; ">'.$cur_month.'</th>';
                    }
                    
                    $ctr++;
                }
                $ctr = count($data);
                while($ctr < 27)
                {
                    $th1 .= '<th style="width: 27.68px; border-left: 1px solid gray; border-top: 1px solid gray;"></th>';
                    $th2 .= '<th style="width: 27.68px; border-left: 1px solid gray; border-top: 1px solid gray;" bgcolor="#D9D9D9"></th>';
                    $date_header .= '<th style="width: 27.68px; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; "></th>';
                    $ctr++;
                }

                $ctr = 0;
                $ctr_pagebreak = ceil(count($lpda_)/18);

                foreach ($lpda_ as $row) {
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
                                        
                                        '.$date_header.'
                                        <th style="width: 27.68px; font-size: 7px; border-bottom: 1px solid gray; border-top: 1px solid gray; "></th>
                                        
                                        <th style="width: 33px; border-left: 1px solid gray; border-bottom: 1px solid gray; font-size: 8px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month1.'</th>
                                        <th style="width: 33px; border-bottom: 1px solid gray; font-size: 8px; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month2.'</th>
                                        <th style="width: 33px; border-bottom: 1px solid gray; font-size: 8px; border-right: 1px solid gray; border-left: 1px solid gray; border-top: 1px solid gray;">'.$forecast_month3.'</th>
                                    </tr></table><table border="0" style="padding: 1px 1px;">';

                        $html .='<tr style="text-align: center; font-size: 6px; font-weight: normal;">
                                        <th style="width: 100px; font-weight: bold; font-size: 10px; border-left: 1px solid gray;"></th>
                                        <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray;"></th>
                                        <th style="width: 30px; font-weight: bold; font-size: 6px;"></th>
                                        '.$th1.'
                                        <th style="width: 27.68px; border-left: 1px solid gray; border-top: 1px solid gray;"></th>
                                        <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                        <th style="width: 33px; border-right: 1px solid gray;"></th>
                                        <th style="width: 33px; border-right: 1px solid gray;"></th>
                                    </tr></table>';  

                    }

                    $template_ = '<th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_01.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_02.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_03.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_04.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_05.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_06.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_07.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_08.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_09.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_10.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_11.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_12.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_13.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_14.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_15.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_16.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_17.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_18.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_19.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_20.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_21.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_22.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_23.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_24.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_25.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_26.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_27.'</th>
                                        <th style="width: 27.68px; border-left: 1px solid gray;">'.$row->ATTRIBUTE_28.'</th>

                                        <th style="width: 33px; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                        <th style="width: 33px; border-right: 1px solid gray;"></th>
                                        <th style="width: 33px; border-right: 1px solid gray;"></th>';

                    $html .='<table border="0" style="padding: 0px 0px;">';
                    if($row->RID == 1)
                    {
                        if($post['forecast_flag'] == 'no')
                        {
                            $row->N1 = '';
                            $row->N2 = '';
                            $row->N3 = '';
                        }
                        $html .='   <tr style="text-align: center; font-size: 6px; font-weight: bold;">
                                        <th style="width: 100px; font-weight: normal; font-size: 7px; text-align: left; border-left: 1px solid gray; border-top: 1px solid gray;">&nbsp;&nbsp;'.$row->PART_NO.'</th>
                                        <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray; border-top: 1px solid gray;">1</th>
                                        <th style="width: 30px; font-weight: bold; font-size: 6px; border-left: 1px solid gray; border-top: 1px solid gray;" bgcolor="#D9D9D9"></th>
                                        '.$th2.'
                                        <th style="width: 27.68px; border-left: 1px solid gray; border-right: 1px solid gray; border-top: 1px solid gray;" bgcolor="#D9D9D9"></th>
                                        <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->N1.'</th>
                                        <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->N2.'</th>
                                        <th style="width: 33px; font-size: 8px; border-right: 1px solid gray; border-top: 1px solid gray;">'.$row->N3.'</th>
                                    </tr>';

                        $html .='
                                    <tr style="text-align: center; font-size: 7px; font-weight: bold;">
                                        <th style="width: 100px; font-weight: normal; font-size: 7px; text-align: left; border-left: 1px solid gray;">&nbsp;&nbsp;'.$row->PART_DESC.'</th>
                                        <th style="width: 35px; font-weight: normal; font-size: 7px; border-left: 1px solid gray;">'.$row->IUSAGE.'</th>
                                        <th style="width: 30px; font-weight: bold; font-size: 7px; border-left: 1px solid gray; text-align: left;">&nbsp;&nbsp;'.$row->TOTAL_QTY.'</th>
                                        '.$template_.'
                                    </tr>';
                                    
                    }
                    else
                    {
                        $html .='
                                    <tr style="text-align: center; font-size: 5px; font-weight: normal;">
                                        <th style="width: 100px; font-weight: normal; font-size: 7px;  height: 20px; border-left: 1px solid gray;"></th>
                                        <th style="width: 35px; font-weight: normal; font-size: 8px; border-left: 1px solid gray;"></th>
                                        <th style="width: 30px; font-weight: bold; font-size: 6px; border-left: 1px solid gray;"></th>
                                        '.$template_.'
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
                                        <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">'.$supervisor.'</th>
                                        <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; text-align: center; font-size: 10px;">'.$coordinator.'</th>
                                    </tr>
                                  </table>';
                        if($ctr_pagebreak-1 >= 0)
                        {
                             $html .= '<br pagebreak="true"/>';
                        }
                    } 
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
                                    <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; text-align: center; font-size: 10px;">'.$supervisor.'</th>
                                    <th style="width: 130px; border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; text-align: center; font-size: 10px;">'.$coordinator.'</th>
                                </tr>
                              </table>';


                    
                }

                $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                $pdf->writeHTMLCell(0, 0, '', 185, $footer, 0, 1, 0, true, '', true);
                $pdf->Output("Delivery Advisory-" . date('Ymdhis') . ".pdf",'I');
            }
            else
            {

                $cso = $this->titan_model_->get_cso_data($post['lpda_no'],$attribute_array,$attribute_array28,$post['report']);
                $starting_date =date_create($post['start_date']);
                $time = strtotime(date_format($starting_date,'Y-m'));

                $forecast_month1 = date("M", strtotime("+1 month", $time));
                $forecast_month2 = date("M", strtotime("+2 month", $time));
                $forecast_month3 = date("M", strtotime("+3 month", $time));

                $this->load->library('excel');
                // echo "<pre>";
                // print_r($po_header);
                // echo "</pre>";
                // exit();

                $objPHPExcel = PHPExcel_IOFactory::load("././resources/report_template/cso_template.xlsx");
                $objPHPExcel->setActiveSheetIndex(0);

                $objPHPExcel->getActiveSheet()->fromArray($cso,null, 'B10');

                $lpda_no = array($po_header[0]['LPDA_NO']);
                $vendor = array($po_header[0]['CONTACT_PERSON'] . ' ' . $po_header[0]['VENDOR_NAME']);
                //$contact_person = array($po_header[0]['CONTACT_PERSON']);

                $objPHPExcel->getActiveSheet()->fromArray($lpda_no,null, 'D6');
                $objPHPExcel->getActiveSheet()->fromArray($vendor,null, 'D1');

                $objPHPExcel->getActiveSheet()->fromArray(array($forecast_month1),null, 'G9');
                $objPHPExcel->getActiveSheet()->fromArray(array($forecast_month2),null, 'H9');
                $objPHPExcel->getActiveSheet()->fromArray(array($forecast_month3),null, 'I9');

                $objPHPExcel->getActiveSheet()->fromArray(array($po_header[0]['MIN_NEEDED_DATE']),null, 'D5');
                $objPHPExcel->getActiveSheet()->fromArray(array($po_header[0]['PO_CREATED_DATE']),null, 'F6');
                //$objPHPExcel->getActiveSheet()->fromArray($footer1,null, 'A');

                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save('././resources/report_template/tempfile.xls');

                $filename='cso_report.xls'; //save our workbook as this file name

                header('Content-Type: application/vnd.ms-excel'); //mime type

                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

                header('Cache-Control: max-age=0'); //no cache

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

                $objWriter->save('php://output');
            }
        }
        else
        {
            $data['heading'] = 'PO - '.$post['lpda_no'];
            $data['message'] = 'No Data';
            $this->load->view('errors/html/error_general',$data);
        }
    }

    // public function cso()
    // {
    //     $this->load->library('Pdf_cso');

    //     $pdf = new Pdf_cso(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //     $pdf->SetCreator(PDF_CREATOR);
    //     $pdf->SetAuthor('Isuzu');
    //     $pdf->SetTitle('DA');
    //     $pdf->SetSubject('DA');
    //     $pdf->SetKeywords('DA, isuzu');

    //     $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
    //     $pdf->setFooterData(array(0,64,0), array(0,64,128));

    //     $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    //     $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //     $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //     $pdf->SetMargins('5', '40', '2');
    //     $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
    //     $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //     $pdf->SetAutoPageBreak(TRUE, 0);
    //     $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //     if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    //         require_once(dirname(__FILE__).'/lang/eng.php');
    //         $pdf->setLanguageArray($l);
    //     }

    //     $pdf->setFontSubsetting(true);

    //     $pdf->SetFont('dejavusans', '', 8, '', true);

    //     $pdf->AddPage('L', 'A4');
    //     $html = '<style>
    //             table, td, th {
    //                 border: 1px solid #444;
    //             }

    //             table {
    //                 border-collapse: collapse;
    //                 width: 100%;
    //             }

    //             th {
    //                 height: 50px;
    //             }
    //             </style>';
       
    //     $html .= '<table>
    //                 <tr style="text-align: center; font-size: 10px; font-weight: bold;">
    //                     <th rowspan="2" style="width: 60px;">ITEM NO</th>
    //                     <th rowspan="2" style="width: 220px;">PART NO</th>
    //                     <th rowspan="2" style="width: 250px;">PART NAME</th>
    //                     <th rowspan="2" style="width: 60px;">Q`TY</th>
    //                     <th rowspan="2" style="width: 220px;">MODEL</th>
    //                     <th colspan="3" style="width: 200px;">FORECAST</th>

    //                 </tr>
    //                 <tr style="text-align: center; font-size: 10px; font-weight: bold;">
    //                     <th scope="col">SEP</th>
    //                     <th scope="col">OCT</th>
    //                     <th scope="col">NOV</th>
    //                 </tr>';
    //                 $ctr=0;
    //                 while($ctr < 10)
    //                 {
    //                 $html .= '
    //                         <tr style="text-align: center; font-size: 10px;">
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                             <td height="20"></td>
    //                         </tr>';
    //                      $ctr++;
    //                 }
    //                 $html .='
    //                     </table>';
    //     $footer = '';
    //     $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    //     $pdf->writeHTMLCell(0, 0, '', 185, $footer, 0, 1, 0, true, '', true);
    //     $pdf->Output("Delivery Advisory-" . date('Ymdhis') . ".pdf",'I');
    // }

}
