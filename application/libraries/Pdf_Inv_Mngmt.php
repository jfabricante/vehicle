<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_Inv_Mngmt extends TCPDF {

	public $date;

	public function setDate($date)
    {
    	$this->date = $date;
    }

	public function __construct() {
		parent::__construct();
	}

	//Page header
    public function Header() {

    	$image_file = base_url() . 'resources/images/isuzu_logo.png';
		$this->Image($image_file, 160, 5, 27, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = base_url() . 'resources/images/oracle_logo.png';
		$this->Image($image_file, 160, 10, 19, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B' , 17);
		$html = "Inventory Management Report (Summary)";
		$this->writeHTMLCell($w = 0, $h = 10, $x = 5, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'N' , 9);
		$html = 'As of '.$this->date;
		$this->writeHTMLCell($w = 0, $h = 10, $x = 5, $y = 13, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->writeHTMLCell($w = 0, $h = 5, $x = 3, $y = 22, "<hr>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
    }

    // Page footer
    public function Footer() {
  		
  		$this->SetY(17);
        $this->SetX(145);
        $this->SetFont('helvetica', '', 7);
        $this->Cell('', '', 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), '', false, 'C', 0, '', 0, false, 'T', 'C');  

        $this->SetY(14);
        $this->SetX(147);
        $this->SetFont('helvetica', '', 7); 
        $this->Cell('', '', strtoupper(date('m/d/Y h:i:s a')), '', false, 'C', 0, '', 0, false, 'T', 'C'); 
    }

    // public function Output($name = 'doc.pdf', $dest = 'I')
    // {
    //     $this->tcpdflink = false;
    //     return parent::Output($name, $dest);
    // }
}
