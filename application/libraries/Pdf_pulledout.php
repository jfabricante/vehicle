<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_pulledout extends TCPDF {

	public $date;
	public function setData($date)
    {
    	$this->date = $date;
    	//$this->_date = $_date;
    }

	public function __construct() {
		parent::__construct();
	}



	//Page header
    public function Header() {
        //~ Logo
		$image_file = base_url() . 'resources/images/isuzu_logo.png';
		$this->Image($image_file, 258, 12, 27, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = base_url() . 'resources/images/oracle_logo.png';
		$this->Image($image_file, 266, 17, 19, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//~ IPC
		$this->SetFont('helvetica', 'B' , 20);
		$html = "PULLOUT SUMMARY REPORT";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 10, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		$this->SetFont('helvetica', 'B' , 10);
		$html = "Date: ".$this->date;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 18, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$header = '<style>
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
		$header .= '<table>
                    <tr style="text-align: center; font-size: 8px; font-weight: bold;" bgcolor="#C0C5CE">
                        <th style="width: 180px;">PARTY NAME</th>
                        <th style="width: 100px;">ACCOUNT NAME</th>
                        <th style="width: 80px;">TRX NUMBER</th>
                        <th style="width: 60px;">CS NUMBER</th>
                        <th style="width: 70px;">PULLOUT DATE</th>
                        <th style="width: 120px;">BODY COLOR</th>
                        <th style="width: 110px;">CHASSIS NUMBER</th>
                        <th style="width: 70px;">ENGINE NO</th>
                        <th style="width: 70px;">KEY NUMBER</th>
                        <th style="width: 130px;">SALES MODEL</th>
                    </tr></table>';

        $this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 24, $header, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
    }

    // Page footer
   public function Footer() {
        $this->SetY(-10);

     	$this->SetFont('helvetica', 'I', 8);

     	$this->Cell('', '', 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), '', false, 'C', 0, '', 0, false, 'T', 'C'); 
    }

}
