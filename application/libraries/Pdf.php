<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf extends TCPDF {

	public function __construct() {
		parent::__construct();
	}

	//Page header
    public function Header() {
        //~ Logo
        $this->SetY(0,true,true);
		$image_file = base_url() . 'resources/images/isuzu_logo.jpg';
		$this->Image($image_file, 10, 10, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//~ IPC
		$this->SetFont('helvetica', 'B', 12);
		$html = "Philippines Corporation";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 41, $y = 11, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		$this->SetFont('helvetica', 'N', 9);
		$html = "114 Technology Avenue, Laguna Technopark Phase II, Biñan, Laguna 4024 Philippines";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 16, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		//~ $html = "Tel. No. (049) 541-0224 to 26	|	Fax No. (+632) 842-0202	| VAT Reg. TIN : 004-834-871-00000";
		//~ $this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		//~ line
		$style = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->Line(10, 21, 200, 21, $style);

    }

    // Page footer
   public function Footer() {
  //       Position at 15 mm from bottom
  //       ~ $this->SetY(-15);
  //       Set font
  //       ~ $this->SetFont('helvetica', 'I', 8);
  //       Page number
  //       ~ echo $this->PageNo();die();
  //       ~ echo $this->getAliasNumPage();die();
        
  //       ~ if()
  //       ~ $this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        
  //       ~ if($this->getAliasNumPage() == 1){
		// 	~ $this->Cell(0, 10, 'IPC Copy', false, 'C', 0, '', 0, false, 'T', 'M');
		// ~ }
   		 $this->SetY(-15);
   		 // $this->SetX(-25);
            // Set font
            $this->SetFont('helvetica', 'I', 8);
            // Page number
         $this->Cell('', '', 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), '', false, 'C', 0, '', 0, false, 'T', 'C');  
        
    }

}
