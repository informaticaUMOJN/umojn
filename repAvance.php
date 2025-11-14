<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}

require_once ("funciones/fxGeneral.php");
require_once ("funciones/fxUsuarios.php");
require_once ("tcpdf/tcpdf.php");

$m_cnx_MySQL = fxAbrirConexion();
$Registro = fxVerificaUsuario();
$mbAdministrador = fxVerificaAdministrador();

if ($Registro == 0)
{
?>
	<div class="container text-center">
    	<div id="DivContenido">
        	<img src="imagenes/errordeacceso.png"/>
        </div>
    </div>
<?php }
else
{
	class PDF extends TCPDF
	{
		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,6,0,16);
			// Title
			$mid_x = 278; // width of the "PDF screen", fixed by now.

			$this->SetFont('helvetica','',11);
			$Titulo = 'UNIVERSIDAD DE MEDICINA ORIENTAL JAPON-NICARAGUA';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 10, $Titulo);
			$Titulo = 'Dirección Académica';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 15, $Titulo);
			
			$this->SetFont('helvetica','B',11);
			$Titulo = 'AVANCE PROGRAMATICO';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 20, $Titulo);
		}

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

	function devuelveFecha($Fecha)
	{
		$FechaDividida = explode("-", $Fecha);
		
		$Anno = $FechaDividida[0];
		$Mes = $FechaDividida[1];
		$Dia = $FechaDividida[2];
		
		switch ($Mes)
			{
				case "01":
					$NombreMes = "Ene";
					break;
				case "02":
					$NombreMes = "Feb";
					break;
				case "03":
					$NombreMes = "Mar";
					break;
				case "04":
					$NombreMes = "Abr";
					break;
				case "05":
					$NombreMes = "May";
					break;
				case "06":
					$NombreMes = "Jun";
					break;
				case "07":
					$NombreMes = "Jul";
					break;
				case "08":
					$NombreMes = "Ago";
					break;
				case "09":
					$NombreMes = "Sep";
					break;
				case "10":
					$NombreMes = "Oct";
					break;
				case "11":
					$NombreMes = "Nov";
					break;
				case "12":
					$NombreMes = "Dic";
					break;
			}
		return ($Dia . "-" . $NombreMes . "-" . $Anno);
	}

	function devuelveSemestre($mnSemestre)
	{
		switch(intval($mnSemestre))
		{
			case 1:
				$msResultado = "Primero";
				break;
			case 2:
				$msResultado = "Segundo";
				break;
			case 3:
				$msResultado = "Tercero";
				break;
			case 4:
				$msResultado = "Cuarto";
				break;
			case 5:
				$msResultado = "Quinto";
				break;
			case 6:
				$msResultado = "Sexto";
				break;
			case 7:
				$msResultado = "Séptimo";
				break;
			case 8:
				$msResultado = "Octavo";
				break;
			case 9:
				$msResultado = "Noveno";
				break;
			case 10:
				$msResultado = "Décimo";
				break;
		}
		return $msResultado;
	}

	function devuelveTurno($mnTurno)
	{
		switch($mnTurno)
		{
			case 1:
				$msResultado = "Diurno";
				break;
			case 2:
				$msResultado = "Matutino";
				break;
			case 3:
				$msResultado = "Vespertino";
				break;
			case 4:
				$msResultado = "Nocturno";
				break;
			case 5:
				$msResultado = "Sabatino";
				break;
			case 6:
				$msResultado = "Dominical";
				break;
		}
		return $msResultado;
	}

	$mAvance = $_POST["UMOJN"];

	$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}

	$pdf->AddPage();

	$msConsulta = "select UMO170A.AVANCE_REL, SYLLABUS_REL, NOMBRE_040, NOMBRE_100, NOMBRE_060, CODIGO_060, SEMESTRE_170, TURNO_170 ";
	$msConsulta .= "from UMO170A, UMO040A, UMO100A, UMO060A ";
	$msConsulta .= "where UMO170A.CARRERA_REL = UMO040A.CARRERA_REL and ";
	$msConsulta .= "UMO170A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and ";
	$msConsulta .= "UMO170A.DOCENTE_REL = UMO100A.DOCENTE_REL and ";
	$msConsulta .= "UMO170A.AVANCE_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$mAvance]);
	$mFila = $mDatos->fetch();

	/***PRIMERA PAGINA***/
	$msSyllabus = $mFila["SYLLABUS_REL"];
	$msCarrera = $mFila["NOMBRE_040"];
	$msDocente = $mFila["NOMBRE_100"];
	$msAsignatura = $mFila["NOMBRE_060"];
	$msSemestre = devuelveSemestre($mFila["SEMESTRE_170"]);
	$msTurno = devuelveTurno($mFila["TURNO_170"]);

	//Primera columna
	$mnLinea = 30;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',11);
	$pdf->Cell(25,0,"Carrera");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',11);
	$pdf->Cell(20,0,$msCarrera);
	
	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',11);
	$pdf->Cell(25,0,"Turno");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',11);
	$pdf->Cell(20,0,$msTurno);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',11);
	$pdf->Cell(25,0,"Asignatura");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',11);
	$pdf->Cell(20,0,$msAsignatura);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',11);
	$pdf->Cell(25,0,"Docente");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',11);
	$pdf->Cell(20,0,$msDocente);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',11);
	$pdf->Cell(25,0,"Semestre");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',11);
	$pdf->Cell(20,0,$msSemestre);

	$msConsulta = "select DETSYLLABUS_REL, FECHA_073, UNIDAD_073, CONTENIDO_073 from UMO073A where SYLLABUS_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msSyllabus]);

	$mbColorea = 0;
	$mnLinea += 6;
	$pdf->SetXY(15,$mnLinea);
	$pdf->SetFont('helvetica','',10);

	$msHTML = "<style>";
	$msHTML .= "td";
	$msHTML .= "{text-align: justify;}";
	$msHTML .= ".fondo";
	$msHTML .= "{background-color:rgb(230,230,230);}";
	$msHTML .= ".bordeDer";
	$msHTML .= "{border-right: 1px solid black;}";
	$msHTML .= "</style>";

	$msHTML .= '<table cellpadding="5px">';
	$msHTML .= '<thead>';
	$msHTML .= '<tr style="color:rgb(255,255,255); background-color:rgb(0,0,230); text-align: center;">';
	$msHTML .= '<th colspan="3" width="44%">PROGRAMADO</th>';
	$msHTML .= '<th colspan="3" width="44%">IMPARTIDO</th>';
	$msHTML .= '<th rowspan="2" width="13%">Observaciones, adecuaciones y/o sugerencias</th>';
	$msHTML .= '</tr>';
	$msHTML .= '<tr style="color:rgb(255,255,255); background-color:rgb(0,0,230); text-align: center;">';
	$msHTML .= '<th width="15%">Unidad</th>';
	$msHTML .= '<th width="9%">Fecha</th>';
	$msHTML .= '<th width="20%">Tema</th>';
	$msHTML .= '<th width="15%">Unidad</th>';
	$msHTML .= '<th width="9%">Fecha</th>';
	$msHTML .= '<th width="20%">Tema</th>';
	$msHTML .= '</tr></thead>';
	while ($mFila = $mDatos->fetch())
	{
		$mdFechaP = $mFila["FECHA_073"];
		$msConsulta = "select FECHAE_171, UNIDADE_171, CONTENIDOE_171, OBSERVACIONES_171 ";
		$msConsulta .= "from UMO171A where AVANCE_REL = ? and FECHAP_171 = ?";
		$mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
		$mAuxiliar->execute([$mAvance, $mdFechaP]);
		if ($mAuxiliar->rowCount() > 0)
		{
			$mrAux = $mAuxiliar->fetch();
			$mdFecha = devuelveFecha($mrAux["FECHAE_171"]);
			$msUnidad = $mrAux["UNIDADE_171"];
			$msContenido = $mrAux["CONTENIDOE_171"];
			$msObservaciones = $mrAux["OBSERVACIONES_171"];
		}
		else
		{
			$mdFecha = "";
			$msUnidad = "";
			$msContenido = "";
			$msObservaciones = "";
		}

		$msHTML .= "<tr>";
		if ($mbColorea == 1)
		{
			$msHTML .= '<td width="15%" class="fondo bordeDer">' . $mFila["UNIDAD_073"] . '</td>';
			$msHTML .= '<td width="9%" class="fondo bordeDer">' . devuelveFecha($mFila["FECHA_073"]) . '</td>';
			$msHTML .= '<td width="20%" class="fondo bordeDer">' . $mFila["CONTENIDO_073"] . '</td>';
			$msHTML .= '<td width="15%" class="fondo bordeDer">' . $msUnidad . '</td>';
			$msHTML .= '<td width="9%" class="fondo bordeDer">' . $mdFecha . '</td>';
			$msHTML .= '<td width="20%" class="fondo bordeDer">' . $msContenido . '</td>';
			$msHTML .= '<td width="13%" class="fondo">' . $msObservaciones . '</td>';
		}
		else
		{
			$msHTML .= '<td width="15%" class="bordeDer">' . $mFila["UNIDAD_073"] . '</td>';
			$msHTML .= '<td width="9%" class="bordeDer">' . devuelveFecha($mFila["FECHA_073"]) . '</td>';
			$msHTML .= '<td width="20%" class="bordeDer">' . $mFila["CONTENIDO_073"] . '</td>';
			$msHTML .= '<td width="15%" class="bordeDer">' . $msUnidad . '</td>';
			$msHTML .= '<td width="9%" class="bordeDer">' . $mdFecha . '</td>';
			$msHTML .= '<td width="20%" class="bordeDer">' . $msContenido . '</td>';
			$msHTML .= '<td width="13%">' . $msObservaciones . '</td>';
		}
		$msHTML .= "</tr>";

		if ($mbColorea == 0)
			$mbColorea = 1;
		else
			$mbColorea = 0;
	}
	$msHTML .= "</table>";
	$pdf->writeHTML($msHTML);
	$pdf->Output();
}
?>