<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_smda extends TCPDF {

	public $template;
	public $_date;
     
    public function setData($template)
    {
    	$this->template = $template;
    	//$this->_date = $_date;
    }

    public function setRemarks($remarks)
    {
    	$this->remarks = $remarks;
    	//$this->_date = $_date;
    }

	public function __construct() {
		parent::__construct();
	}

	//Page header
    public function Header() {

    	$dateObj   = DateTime::createFromFormat('!m', $this->template->MONTH_FN);
        $monthName = $dateObj->format('F');

        $image_file = base_url() . 'resources/images/isuzu_logo_black.png';
		$this->Image($image_file, 1, 7, 38, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		// $this->SetFont($fontname, 'B' , 30);
		// $html = "ISUZU";
		// $this->writeHTMLCell($w = 0, $h = 0, $x = 3, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'B' , 13);
		$html = "PHILIPPINES CORPORATION";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 3, $y = 17, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'B' , 15);
		$html = "LPDA (Local Parts Delivery Advisory)";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 3, $y = 22, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = $monthName . ' ' .$this->template->IYEAR . ' ' . $this->remarks;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 3, $y = 29, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);


		$this->SetFont('helvetica', '' , 7);
		$html = "114 Technology Avenue Phase II, Laguna Technopark";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 40, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 7);
		$html = "Biñan, Laguna 4024";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 40, $y = 8, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 7);
		$html = "Tel No : 842-0256/57 loc 201 or 207";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 40, $y = 11, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 7);
		$html = "Fax No: 842-0202";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 40, $y = 14, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);


		$this->SetFont('helvetica', '' , 9);
		$html = "TO";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 120, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = "ADDRESS";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 120, $y = 10, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = "TELEPHONE NO";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 120, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = "ATTENTION";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 120, $y = 20, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);


		$this->SetFont('helvetica', '' , 9);
		$html = $this->template->VENDOR_NAME;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 147, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = $this->template->ADDRESS;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 147, $y = 10, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = $this->template->TEL_NUM;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 147, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = $this->template->CONTACT_PERSON;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 147, $y = 20, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = "REFERENCE NO";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 240, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', '' , 9);
		$html = $this->template->LPDA_NO;
		$this->writeHTMLCell($w = 0, $h = 0, $x = 270, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
    }

    // Page footer
    public function Footer() {

 		$this->SetY(5);
        $this->SetX(280);
        $this->SetFont('helvetica', '', 5);
        $this->Cell('', '', 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), '', false, 'C', 0, '', 0, false, 'T', 'C');  

        $this->SetY(5);
        $this->SetX(190);
        $this->SetFont('helvetica', '', 5); 
        $this->Cell('', '', 'SMDA', '', false, 'C', 0, '', 0, false, 'T', 'C');  

        $this->SetY(7);
        $this->SetX(205);
        $this->SetFont('helvetica', '', 5); 
        $this->Cell('', '', strtoupper(date('m/d/Y h:i:s a')), '', false, 'C', 0, '', 0, false, 'T', 'C');   
    }
}
