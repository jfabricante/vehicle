<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_buyoff extends TCPDF {

	public function __construct() {
		parent::__construct();
	}

	//Page header
    public function Header() {
		
		$image_file = base_url() . 'resources/images/isuzu_logo.png';
		$this->Image($image_file, 258, 12, 27, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = base_url() . 'resources/images/oracle_logo.png';
		$this->Image($image_file, 266, 17, 19, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//~ $this->SetFont('helvetica', 'B' , 30);
		//~ $html = "ISUZU";
		//~ $this->writeHTMLCell($w = 0, $h = 0, $x = 3, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		//~ $this->SetFont('helvetica', 'B' , 13);
		//~ $html = "PHILIPPINES CORPORATION";
		//~ $this->writeHTMLCell($w = 0, $h = 10, $x = 40, $y = 10, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->writeHTMLCell($w = 0, $h = 5, $x = 3, $y = 22, "<hr>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);


		$this->SetFont('helvetica', 'N' , 9);
		$html = "Buyoff Summary Report Form";
		$this->writeHTMLCell($w = 0, $h = 5, $x = 3, $y = 23, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'B' , 16);
		$html = "BUYOFF SUMMARY REPORT";
		$this->writeHTMLCell($w = 0, $h = 5, $x = 100, $y = 26, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
    }

    // Page footer
    public function Footer() {
  
    }
}
