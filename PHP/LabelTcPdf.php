<?php

	require_once('tcpdf/config/lang/eng.php');
	require_once('tcpdf/tcpdf.php');

	// Extend the TCPDF class to create custom Header and Footer 
class LabelTcPdf extends TCPDF { 
		    //Page header 
public function Header() { 
		        // Logo
		        // function Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false) {
	$this->Image('tcpdf/images/abelei_logo.png', 15, 8, 27);
		        // Set font 
    $this->SetFont('helvetica', 'B', 10);
		        // Move to the right 
		        //$this->Cell(80);
		        // Title
		        // LINE BREAK
    $this->Cell(0, 0, '', 0, 1);
		        // LINE BREAK
    $this->Cell(0, 0, '', 0, 1);
		        // function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false) {
		        // $this->Cell(0, 0, 'clever able capable', 0, 0, 'C', 2, 0, 0, 1);
    $this->Image('tcpdf/images/abelei_font.png', 21, 40, 15, 0, 0, 0, 'C', 0, 300, 'L');
		        // Line break 
    $this->Ln(50);
} 
		     
		    // Page footer 
public function Footer() { 
		        // Position at 1.5 cm from bottom 
	$this->SetY(-20);
		        // Set font 
	$this->SetFont('helvetica', 8);
		        // Page number 
	$this->Cell(0, 10, '194 Alder Drive | North Aurora, Illinois 60542 | t-630.859.1410 | f-630.859.1448 | toll-free 866-4-abelei', 0, 0, 'C');
		        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
} 
} 

	
?>