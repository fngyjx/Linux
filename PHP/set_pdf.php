<?php

/**
 * @author 
 * @copyright 2009
 */
function set_pdf($author,$title,$subject,$keyword) {
    
include('NewTcPdf.php');
	// create new PDF document 
		$pdf = new NewTcPdf('L', PDF_UNIT, 'A5', true, 'UTF-8', false);  

		// set document information 
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($author);
		$pdf->SetTitle($title);
		$pdf->SetSubject($subject);
		$pdf->SetKeywords($keyword);

		// set default header data 
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts 
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins 
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks 
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor 
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  

		//set some language-dependent strings 
		$pdf->setLanguageArray($l);  

		// --------------------------------------------------------- 

		// set font 
		$pdf->SetFont('helvetica', '', 10);

		// add a page 
		$pdf->AddPage();
        
        return $pdf;
  }

function set_abelei_pdf($author,$title,$subject,$keyword,$num_ordered_prds) {

include('AbeleiTcPdf.php');
	// create new PDF document 
		$pdf = new AbeleiTcPdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  

		// set document information 
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($author);
		$pdf->SetTitle($title);
		$pdf->SetSubject($subject);
		$pdf->SetKeywords($keyword);

		// set default header data 
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts 
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins 
		if ( $num_ordered_prds > 3 )
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+20, PDF_MARGIN_RIGHT);
		else
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks 
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor 
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  

		//set some language-dependent strings 
		$pdf->setLanguageArray($l);  

		// --------------------------------------------------------- 

		// set font 
		$pdf->SetFont('helvetica', '', 10);

		// add a page 
		$pdf->AddPage();
        
        return $pdf;
  }
  
function set_label_pdf($author,$title,$subject,$keyword,$num_ordered_prds) {

include('LabelTcPdf.php');
	// create new PDF document 
		$pdf = new LabelTcPdf('L', PDF_UNIT, 'A5', true, 'UTF-8', false);  

		// set document information 
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($author);
		$pdf->SetTitle($title);
		$pdf->SetSubject($subject);
		$pdf->SetKeywords($keyword);

		// set default header data 
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts 
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins 
		if ( $num_ordered_prds > 3 )
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+20, PDF_MARGIN_RIGHT);
		else
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks 
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor 
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  

		//set some language-dependent strings 
		$pdf->setLanguageArray($l);  

		// --------------------------------------------------------- 

		// set font 
		$pdf->SetFont('helvetica', '', 10);

		// add a page 
		$pdf->AddPage();
        
        return $pdf;
  }
  
?>