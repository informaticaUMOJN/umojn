<?php
    require_once ("funciones/fxGeneral.php");
    require_once ("tcpdf/tcpdf.php");
    $m_cnx_MySQL = fxAbrirConexion();

    class PDF extends TCPDF
	{
		// Page header
		function Header()
		{}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica','I',8);
			// Page number
			$this->Cell(0,10,'Página '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'L');
			$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
		}
	}

    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// remove default footer
	//$pdf->setPrintFooter(false);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
    }

    $pdf->AddPage();
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica','B',10);
    //Código QR
    $style = array(
        'border' => false,
        'vpadding' => 0,
        'hpadding' => 0,
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255)
    );
    /*
    $msCodigoQR = "https://umojn.edu.ni";
    $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 40, 30, 35, 35, $style, 'N');
    $pdf->setXY(40,65);
    $pdf->Cell(100,10,$msCodigoQR,0,0,'L');

    $msCodigoQR = "https://umojn.edu.ni/index.php/carreras/medicina-oriental/";
    $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 40, 30, 35, 35, $style, 'N');
    $pdf->setXY(42,65);
    $pdf->Cell(100,10,"Medicina oriental",0,0,'L');

    $msCodigoQR = "https://umojn.edu.ni/index.php/carreras/psicologia-clinica-con-mencion-en-medicina-oriental/";
    $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 40, 30, 35, 35, $style, 'N');
    $pdf->setXY(42,65);
    $pdf->Cell(100,10,"Psicología clínica",0,0,'L');
*/

    $msCodigoQR = "https://umojn.edu.ni/index.php/carreras/licenciatura-en-enfermeria-con-mencion-en-medicina-tradicional-y-complementaria/";
    $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 40, 30, 35, 35, $style, 'N');
    $pdf->setXY(47,65);
    $pdf->Cell(100,10,"Enfermería",0,0,'L');

    $pdf->Output();
?>