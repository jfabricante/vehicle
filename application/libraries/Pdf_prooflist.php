<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_prooflist extends TCPDF {

	protected $_buyoff_date;

	public function __construct() {
		parent::__construct();

	}

	//Page header
    public function Header() {
		
		$image_file = base_url() . 'resources/images/isuzu_logo.png';
		$this->Image($image_file, 268, 12, 27, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = base_url() . 'resources/images/oracle_logo.png';
		$this->Image($image_file, 276, 17, 19, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$this->writeHTMLCell($w = 0, $h = 5, $x = 3, $y = 22, "<hr>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'B' , 16);
		$html = "IPC Buy-off Prooflist";
		$this->writeHTMLCell($w = 0, $h = 5, $x = 5, $y = 26, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'N' , 7);
		$html = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
		$this->writeHTMLCell($w = 0, $h = 5, $x = 271, $y = 30, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'N' , 7);
		$html = date('m/d/Y h:i:s a');
		$this->writeHTMLCell($w = 0, $h = 5, $x = 260, $y = 35, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

		$this->SetFont('helvetica', 'N' , 7);
		$html = $this->getBuyoffDate();
		$this->writeHTMLCell($w = 0, $h = 5, $x = 5, $y = 35, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

    }

    // Set buyoff date
    public function setBuyoffDate($params)
    {
    	$this->_buyoff_date = $params;

    	return $this;
    }

    public function getBuyoffDate()
    {
    	return $this->_buyoff_date;
    }
}
