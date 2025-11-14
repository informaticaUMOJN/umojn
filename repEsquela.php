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
require_once ("funciones/fxNumerosLetras.php");
require_once ("tcpdf/tcpdf.php");
$m_cnx_MySQL = fxAbrirConexion();
$mnRegistro = fxVerificaUsuario();
$mbAdministrador = fxVerificaAdministrador();

if ($mnRegistro == 0)
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
			$this->Image('imagenes/logoRep.jpg',15,12,0,18);
			// Title
			$mid_x = 210; // width of the "PDF screen", fixed by now.

			$this->SetFont('helvetica','B',13);
			$Titulo = 'ESQUELA DE CALIFICACIONES';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 13, $Titulo);
		}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica','I',8);
			// Page number
			//$this->Cell(0,10,'Página '.$this->PageNo().' de '.$this->getAliasNbPages(),0,0,'L');
			$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
		}
	}

	function convertirARomanos($numero) {
		$valores = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1];
		$simbolos = ["M", "CM", "D", "CD", "C", "XC", "L", "XL", "X", "IX", "V", "IV", "I"];
	
		$resultado = "";
	
		for ($i = 0; $i < count($valores); $i++) {
			while ($numero >= $valores[$i]) {
				$resultado .= $simbolos[$i];
				$numero -= $valores[$i];
			}
		}
	
		return $resultado;
	}

	function fxAnnoAcademico($mnAnno)
	{
		switch (intval($mnAnno))
		{
			case 1:
				$msAnno = "1er. AÑO";
				break;
			case 2:
				$msAnno = "2do. AÑO";
				break;
			case 3:
				$msAnno = "3er. AÑO";
				break;
			case 4:
				$msAnno = "4to. AÑO";
				break;
			case 5:
				$msAnno = "5to. AÑO";
				break;
		}

		return $msAnno;
	}

	$mMatricula = $_POST["msMatricula"];

	$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 18, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}

	foreach($mMatricula as $mRegistro)
	{
		$msMatricula = $mRegistro[0];
		$mnAnnoLectivo = $mRegistro[1];
		$mnSemestreLectivo = $mRegistro[2];

		$msConsulta = "SELECT NOMBRE_040, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, CARNET_010, ANNOACADEMICO_030 ";
		$msConsulta .= "FROM UMO030A, UMO040A, UMO010A ";
		$msConsulta .= "WHERE UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL AND UMO030A.CARRERA_REL = UMO040A.CARRERA_REL ";
		$msConsulta .= "AND UMO030A.MATRICULA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMatricula]);
		$mFila = $mDatos->fetch();
		$msAnnoAcademico = fxAnnoAcademico(intval($mFila["ANNOACADEMICO_030"]));
		$msCarrera = $mFila["NOMBRE_040"];
		$msCarnet = $mFila["CARNET_010"];
		$msEstudiante = trim($mFila["NOMBRE1_010"]);
		if (trim($mFila["NOMBRE2_010"]) != "")
			$msEstudiante .= ' ' . trim($mFila["NOMBRE2_010"]);
			
		$msEstudiante .= ' ' . trim($mFila["APELLIDO1_010"]);
		if (trim($mFila["APELLIDO2_010"]) != "")
			$msEstudiante .= ' ' . trim($mFila["APELLIDO2_010"]);

		
		$mid_x = 210;
		$pdf->AddPage();
		$pdf->setFontSize(8);

		$msHTML = '<style>';
		$msHTML .= "th{";
		$msHTML .= "background-color:rgb(0,0,255); color: white;";
		$msHTML .= "}";
		$msHTML .= ".italica{";
		$msHTML .= "font-size:6px; font-style: italic;";
		$msHTML .= "}";
		$msHTML .= ".resaltar{";
		$msHTML .= "font-size:12px; font-weight: bolder;";
		$msHTML .= "}";
		$msHTML .= ".azul{";
		$msHTML .= "background-color:rgb(0,0,255); color: white;";
		$msHTML .= "}";
		$msHTML .= ".fondo{";
		$msHTML .= "background-color: rgb(235,235,235);";
		$msHTML .= "}";
		$msHTML .= ".centro{";
		$msHTML .= "text-align: center;";
		$msHTML .= "}";
		$msHTML .= ".derecha{";
		$msHTML .= "text-align: right;";
		$msHTML .= "}";
		$msHTML .= ".bordeSuperior{";
		$msHTML .= "border-top: 2px solid black; border-right: none; border-bottom: none; border-left: none;";
		$msHTML .= "}";
		$msHTML .= '</style>';
		
		$msHTML .= '<table width="100%" cellpadding="1">';
		$msHTML .= '<tr><td class="centro">' . $msCarrera . '</td></tr>';
		$msHTML .= '<tr><td class="centro">' . $msEstudiante . '</td></tr>';
		$msHTML .= '<tr><td class="centro">' . $msCarnet . '</td></tr>';
		$msHTML .= '</table>';

		$msHTML .= '<table cellpadding="2">';
		if ($mnSemestreLectivo == 1)
			$msHTML .= '<tr><td class="resaltar">' . $msAnnoAcademico . '</td><td class="derecha">I SEMESTRE ' . $mnAnnoLectivo . '</td></tr>';
		else
			$msHTML .= '<tr><td class="resaltar">' . $msAnnoAcademico . '</td><td class="derecha">II SEMESTRE ' . $mnAnnoLectivo . '</td></tr>';
		$msHTML .= '</table>';
		$msHTML .= '<table cellpadding="2">';
		$msHTML .= '<thead><tr>';
		$msHTML .= '<th width="10%">CODIGO</th>';
		$msHTML .= '<th width="40%">ASIGNATURA</th>';
		$msHTML .= '<th width="10%" class="centro">NOTA</th>';
		$msHTML .= '<th width="40%">CALIFICACION EN LETRAS</th>';
		$msHTML .= '</tr></thead>';

		$msConsulta = "select UMO031A.ASIGNATURA_REL, CODIGO_060, NOMBRE_060 ";
		$msConsulta .= "from UMO031A join UMO060A on UMO031A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
		$msConsulta .= "where MATRICULA_REL = ?";
		$mDatosEstudiante = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosEstudiante->execute([$msMatricula]);

		$mbColorea = 0;
		$mnSuma = 0;
		$mnCuenta = 0;

		while ($filaEstudiante = $mDatosEstudiante->fetch())
		{
			$msAsignaturaRel = $filaEstudiante["ASIGNATURA_REL"];
			$msAsignaturaCod = $filaEstudiante["CODIGO_060"];
			$msAsignaturaNom = $filaEstudiante["NOMBRE_060"];

			$msConsulta = "select NOTA_161 from UMO161A join UMO160A on UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL where PARCIAL_160 = 0 and MATRICULA_REL = ? and ASIGNATURA_REL = ?";
			$mDatosNota = $m_cnx_MySQL->prepare($msConsulta);
			$mDatosNota->execute([$msMatricula, $msAsignaturaRel]);
			$mnRegistros = $mDatosNota->rowCount();
			if ($mnRegistros > 0)
			{
				$filaNota = $mDatosNota->fetch();
				$mnParcial1 = round(intval($filaNota["NOTA_161"]) * 0.35, 2);
			}
			else
				$mnParcial1 = 0;

			$msConsulta = "select NOTA_161 from UMO161A join UMO160A on UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL where PARCIAL_160 = 1 and MATRICULA_REL = ? and ASIGNATURA_REL = ?";
			$mDatosNota = $m_cnx_MySQL->prepare($msConsulta);
			$mDatosNota->execute([$msMatricula, $msAsignaturaRel]);
			$mnRegistros = $mDatosNota->rowCount();
			if ($mnRegistros > 0)
			{
				$filaNota = $mDatosNota->fetch();
				$mnParcial2 = round(intval($filaNota["NOTA_161"]) * 0.35, 2);
			}
			else
				$mnParcial2 = 0;
			
			$msConsulta = "select NOTA_161 from UMO161A join UMO160A on UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL where PARCIAL_160 = 2 and MATRICULA_REL = ? and ASIGNATURA_REL = ?";
			$mDatosNota = $m_cnx_MySQL->prepare($msConsulta);
			$mDatosNota->execute([$msMatricula, $msAsignaturaRel]);
			$mnRegistros = $mDatosNota->rowCount();
			if ($mnRegistros > 0)
			{
				$filaNota = $mDatosNota->fetch();
				$mnParcial3 = round(intval($filaNota["NOTA_161"]) * 0.3, 2);
			}
			else
				$mnParcial3 = 0;

			$mnNotaFinal = round(floatval($mnParcial1) + floatval($mnParcial2) + floatval($mnParcial3), 0);
			$msNotaLetras = fxNumerosLetras($mnNotaFinal);
			
			$msHTML .= '<tr>';

			if ($mbColorea == 0)
			{
				$msHTML .= '<td width="10%">' . $msAsignaturaCod . '</td>';
				$msHTML .= '<td width="40%">' . $msAsignaturaNom . '</td>';
				$msHTML .= '<td width="10%" class="centro">' . number_format($mnNotaFinal,0,'.',',') . '</td>';
				$msHTML .= '<td width="40%">' . $msNotaLetras . '</td>';
			}
			else
			{
				$msHTML .= '<td class="fondo" width="10%">' . $msAsignaturaCod . '</td>';
				$msHTML .= '<td class="fondo" width="40%">' . $msAsignaturaNom . '</td>';
				$msHTML .= '<td class="fondo centro" width="10%">' . number_format($mnNotaFinal,0,'.',',') . '</td>';
				$msHTML .= '<td class="fondo" width="40%">' . $msNotaLetras . '</td>';
			}

			$msHTML .= '</tr>';
			$mnSuma += $mnNotaFinal;
			$mnCuenta++;

			if ($mbColorea == 0)
				$mbColorea = 1;
			else
				$mbColorea = 0;
		}

		if ($mnCuenta != 0)
			$mnPromedio = round(floatval($mnSuma) / intval($mnCuenta), 0);
		else
			$mnPromedio = 0;

		$msPromedioLetras = fxNumerosLetras($mnPromedio);

		$msHTML .= '<tr>';
		$msHTML .= '<td class="azul" colspan="2">PROMEDIO SEMESTRAL ORDINARIO</td>';
		$msHTML .= '<td class="azul centro">' . number_format($mnPromedio,0,'.',',') . '</td>';
		$msHTML .= '<td class="azul">' . $msPromedioLetras . '</td>';
		$msHTML .= '</tr>';
		
		$msHTML .= '</table>';
		$msHTML .= '<label class="italica">La nota mínima para aprobar es 60 (sesenta)</label><br>';
		$msHTML .= '<label class="italica">Este documento es de uso interno</label><br>';
		$msHTML .= '<label class="italica">C.c. Expediente</label>';	
		$msHTML .= '<br><br><br><br><br><br>';
		$msHTML .= '<table>';
		$msHTML .= '<tr>';
		$msHTML .= '<td style="width: 30%;">&nbsp;</td>';
		$msHTML .= '<td style="width: 40%;" class="bordeSuperior centro">MSc. Xaviera Pérez Calero<br>Registro académico</td>';
		$msHTML .= '<td style="width: 30%;">&nbsp;</td>';
		$msHTML .= '</tr>';
		$msHTML .= '</table>';

		$pdf->writeHTML($msHTML);
	}
	$pdf->SetY(18);
	$pdf->Output();
}
?>