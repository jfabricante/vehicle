<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_cso extends TCPDF {

	public function __construct() {
		parent::__construct();
	}

	//Page header
    public function Header() {
		
    }

    // Page footer
    public function Footer() {
  
    }
}
