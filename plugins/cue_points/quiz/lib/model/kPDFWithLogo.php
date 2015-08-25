<?php
require('vendor/fpdf/fpdf.php');
class PDF extends FPDF {

	function Header() {
		$this->SetFont('Times','',12);
		$this->SetY(0.25);
		$this->Cell(0, .25, "Professors Name ".$this->PageNo(), 'T', 2, "R");
		//reset Y
		$this->SetY(1);
	}

	function Footer() {
//This is the footer; it's repeated on each page.
//enter filename: phpjabber logo, x position: (page width/2)-half the picture size,
//y position: rough estimate, width, height, filetype, link: click it!
		$this->Image("/opt/kaltura/app/logo.jpg", (8.5/2)-1.5, 9.8, 3, 1, "JPG", null);
	}

}