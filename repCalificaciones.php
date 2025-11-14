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
$Administrador = fxVerificaAdministrador();

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
		public $msAsignatura;
		public $msIdAsignatura;
		public $msTurno;
		public $msDocente;
		public $mnAnnoLectivo;
		public $msSemestre;
		public $msAnno;
		public $msSemestreCarrera;
		public $msCarrera;
		
		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,12,0,16);
			// Title
			$mid_x = 278; // width of the "PDF screen", fixed by now.

			$this->SetFont('helvetica','B',14);
			$Titulo = 'ACTA DE CALIFICACIONES';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 13, $Titulo);

			$this->SetFont('helvetica','B',11);
			$this->Text(($mid_x - $this->GetStringWidth($this->msCarrera)) / 2, 18, $this->msCarrera);

			$this->SetFont('helvetica','',11);
			$Titulo = $this->msAsignatura . " / " . $this->msIdAsignatura;
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 23, $Titulo);
			
			$Titulo = "Docente: " . $this->msDocente;
			$this->Text(35, 28, $Titulo);
			
			$Titulo = "Turno: " . $this->msTurno;
			$this->Text(110, 28, $Titulo);

			$Titulo = "Año lectivo: " . $this->mnAnnoLectivo;
			$this->Text(145, 28, $Titulo);

			$Titulo = "Semestre de la carrera: " . $this->msSemestreCarrera . " (" . $this->msAnno . " año / " . $this->msSemestre . " semestre)";
			$this->Text(185, 28, $Titulo);
		}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica','I',8);
			// Page number
			$this->Cell(0,10,'Página '.$this->PageNo().' de '.$this->getAliasNbPages(),0,0,'L');
			$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
		}
	}

	function DevuelveTurno($turno)
	{
		switch(intval($turno))
		{
			case 1:
				$resultado = "Diurno";
				break;
			case 2:
				$resultado = "Matutino";
				break;
			case 3:
				$resultado = "Vespertino";
				break;
			case 4:
				$resultado = "Nocturno";
				break;
			case 5:
				$resultado = "Sabatino";
				break;
			case 6:
				$resultado = "Dominical";
				break;
		}
		return $resultado;
	}

	function DevuelveFecha($Fecha)
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

	$msCodAsignatura = $_POST["msAsignatura"];
	$mnTurno = $_POST["mnTurno"];
	$msCodDocente = $_POST["msDocente"];
	$mnAnnoLectivo = $_POST["mnAnno"];
	$mnSemestreLectivo = $_POST["mnSemestre"];

	$msConsulta = "select NOMBRE_060, CODIGO_060, CARRERA_REL from UMO060A where ASIGNATURA_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodAsignatura]);
	$mFila = $mDatos->fetch();
	$msAsignatura = $mFila["NOMBRE_060"];
	$msIdAsignatura = $mFila["CODIGO_060"];
	$msCodCarrera = $mFila["CARRERA_REL"];

	$msConsulta = "select NOMBRE_100 from UMO100A where DOCENTE_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodDocente]);
	$mFila = $mDatos->fetch();
	$msDocente = $mFila["NOMBRE_100"];

	$msConsulta = "select NOMBRE_040 from UMO040A where CARRERA_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodCarrera]);
	$mFila = $mDatos->fetch();
	$msCarrera = $mFila["NOMBRE_040"];

	$msConsulta = "select SEMESTRE_051 from UMO051A, UMO050A where UMO051A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL ";
	$msConsulta .= "and ASIGNATURA_REL = ? and TURNO_050 = ? and ACTIVO_050 = 1";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodAsignatura, $mnTurno]);
	$mFila = $mDatos->fetch();
	$mnSemestreCarrera = intval($mFila["SEMESTRE_051"]);
	if ($mnSemestreCarrera % 2 == 0)
	{
		$msSemestre = "II";
		$mnAnno = intval($mnSemestreCarrera / 2);
	}
	else
	{
		$msSemestre = "I";
		$mnAnno = floor($mnSemestreCarrera / 2);
		if ($mnAnno == 0)
			$mnAnno = 1;
	}

	$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->msAsignatura=$msAsignatura;
	$pdf->msIdAsignatura=$msIdAsignatura;
	$pdf->msTurno=DevuelveTurno($mnTurno);
	$pdf->msDocente=$msDocente;
	$pdf->mnAnnoLectivo=$mnAnnoLectivo;
	$pdf->msSemestre=$msSemestre;
	$pdf->msSemestreCarrera=convertirARomanos($mnSemestreCarrera);
	$pdf->msAnno=convertirARomanos($mnAnno);
	$pdf->msCarrera=$msCarrera;

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}

	$pdf->setFontSize(8);
	$pdf->AddPage();

	$msHTML = '<style>';
	$msHTML .= "th{";
	$msHTML .= "background-color:rgb(0,0,255); color: white;";
	$msHTML .= "}";
	$msHTML .= ".fondo{";
	$msHTML .= "background-color: rgb(235,235,235);";
	$msHTML .= "}";
	$msHTML .= ".derecha{";
	$msHTML .= "text-align: right;";
	$msHTML .= "}";
	$msHTML .= ".centro{";
	$msHTML .= "text-align: center;";
	$msHTML .= "}";
	$msHTML .= ".bordeSuperior{";
	$msHTML .= "border-top: 2px solid black; border-right: none; border-bottom: none; border-left: none;";
	$msHTML .= "}";
	$msHTML .= '</style>';
	$msHTML .= '<table cellpadding="2">';
	$msHTML .= '<thead><tr>';
	$msHTML .= '<th>CARNET</th>';
	$msHTML .= '<th>ESTUDIANTE</th>';
	$msHTML .= '<th class="derecha">I PARCIAL</th>';
	$msHTML .= '<th class="derecha">II PARCIAL</th>';
	$msHTML .= '<th class="derecha">III PARCIAL</th>';
	$msHTML .= '<th class="derecha">NOTA FINAL</th>';
	$msHTML .= '<th class="centro">EXAMEN EXTRAORDINARIO</th>';
	$msHTML .= '<th class="centro">INTER SEMESTRAL</th>';
	$msHTML .= '<th class="centro">EXAMEN SUFICIENCIA</th>';
	$msHTML .= '<th class="centro">CONVALIDACION</th>';
	$msHTML .= '</tr></thead>';

	$msConsulta = "select UMO030A.MATRICULA_REL, CARNET_010, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 ";
	$msConsulta .= "from UMO030A, UMO010A, UMO031A, UMO050A ";
	$msConsulta .= "where UMO030A.MATRICULA_REL = UMO031A.MATRICULA_REL and UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL ";
	$msConsulta .= "and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL ";
	$msConsulta .= "and UMO031A.ASIGNATURA_REL = ? and TURNO_050 = ? and ANNOLECTIVO_030 = ? and SEMESTREACADEMICO_030 = ? ";
	$msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
	$mDatosEstudiante = $m_cnx_MySQL->prepare($msConsulta);
	$mDatosEstudiante->execute([$msCodAsignatura, $mnTurno, $mnAnnoLectivo, $mnSemestreLectivo]);

	$mbColorea = 0;
	while ($filaEstudiante = $mDatosEstudiante->fetch())
	{
		$msHTML .= '<tr>';
		$msMatricula = $filaEstudiante["MATRICULA_REL"];

		if ($mbColorea == 0)
			$msHTML .= '<td>' . $filaEstudiante["CARNET_010"] . '</td>';
		else
			$msHTML .= '<td class="fondo">' . $filaEstudiante["CARNET_010"] . '</td>';

		$msEstudiante = html_entity_decode($filaEstudiante["APELLIDO1_010"]);

		if (trim($filaEstudiante["APELLIDO2_010"]) != "")
			$msEstudiante .= ' ' . html_entity_decode($filaEstudiante["APELLIDO2_010"]);

		$msEstudiante .= ', ' . html_entity_decode($filaEstudiante["NOMBRE1_010"]);
		
		if (trim($filaEstudiante["NOMBRE2_010"]) != '')
			$msEstudiante .= ' ' . html_entity_decode($filaEstudiante["NOMBRE2_010"]);

		if ($mbColorea == 0)
			$msHTML .= '<td>' . trim($msEstudiante) . '</td>';
		else
			$msHTML .= '<td class="fondo">' . trim($msEstudiante) . '</td>';

		//***Primer parcial***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 0, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();
			$mnParcial1 = round(intval($mrNotas["NOTA_161"]) * 0.35, 2);
		}
		else
			$mnParcial1 = 0;

		if ($mbColorea == 0)
			$msHTML .= '<td class="derecha">' . number_format($mnParcial1, 2, ".", ",") . '</td>';
		else
			$msHTML .= '<td class="fondo derecha">' . number_format($mnParcial1, 2, ".", ",") . '</td>';

		//***Segundo parcial***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 1, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();
			$mnParcial2 = round(intval($mrNotas["NOTA_161"]) * 0.35, 2);
		}
		else
			$mnParcial2 = 0;

		if ($mbColorea == 0)
			$msHTML .= '<td class="derecha">' . number_format($mnParcial2, 2, ".", ",") . '</td>';
		else
			$msHTML .= '<td class="fondo derecha">' . number_format($mnParcial2, 2, ".", ",") . '</td>';
		
		//***Tercer parcial***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 2, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();
			$mnParcial3 = round(intval($mrNotas["NOTA_161"]) * 0.3, 2);
		}
		else
			$mnParcial3 = 0;

		if ($mbColorea == 0)
			$msHTML .= '<td class="derecha">' . number_format($mnParcial3, 2, ".", ",") . '</td>';
		else
			$msHTML .= '<td class="fondo derecha">' . number_format($mnParcial3, 2, ".", ",") . '</td>';

		//***Nota final***
		$mnNotaFinal = round(floatval($mnParcial1) + floatval($mnParcial2) + floatval($mnParcial3), 0);

		if ($mbColorea == 0)
			$msHTML .= '<td class="derecha">' . number_format($mnNotaFinal,0,'.',',') . '</td>';
		else
			$msHTML .= '<td class="fondo derecha">' . number_format($mnNotaFinal,0,'.',',') . '</td>';
		
		//***Examen extraordinario***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 3, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();

			switch(intval($mrNotas["NOTA_161"]))
			{
				case 0:
					$msExtraordinario = "";
					break;
				case 1:
					$msExtraordinario = "Reprobado";
					break;
				case 2:
					$msExtraordinario = "Aprobado";
					break;
			}
		}
		else
			$msExtraordinario = "";

		if ($mbColorea == 0)
			$msHTML .= '<td class="centro">' . trim($msExtraordinario) . '</td>';
		else
			$msHTML .= '<td class="fondo centro">' . trim($msExtraordinario) . '</td>';

		//***Curso intersemestral***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 4, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();

			switch(intval($mrNotas["NOTA_161"]))
			{
				case 0:
					$msIntersemestral = "";
					break;
				case 1:
					$msIntersemestral = "Reprobado";
					break;
				case 2:
					$msIntersemestral = "Aprobado";
					break;
			}
		}
		else
			$msIntersemestral = "";

		if ($mbColorea == 0)
			$msHTML .= '<td class="centro">' . trim($msIntersemestral) . '</td>';
		else
			$msHTML .= '<td class="fondo centro">' . trim($msIntersemestral) . '</td>';

		//***Examen de suficiencia***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 5, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();

			switch(intval($mrNotas["NOTA_161"]))
			{
				case 0:
					$msSuficiencia = "";
					break;
				case 1:
					$msSuficiencia = "Reprobado";
					break;
				case 2:
					$msSuficiencia = "Aprobado";
					break;
			}
		}
		else
			$msSuficiencia = "";

		if ($mbColorea == 0)
			$msHTML .= '<td class="centro">' . trim($msSuficiencia) . '</td>';
		else
			$msHTML .= '<td class="fondo centro">' . trim($msSuficiencia) . '</td>';

		//***Convalidación***
		$msConsulta = "Select NOTA_161 from UMO161A, UMO160A where UMO161A.CALIFICACION_REL = UMO160A.CALIFICACION_REL and ";
		$msConsulta .= "ANNO_160 = ? and SEMESTRE_160 = ? and MATRICULA_REL = ? and PARCIAL_160 = ? and ASIGNATURA_REL = ?";
		$mDatosNotas = $m_cnx_MySQL->prepare($msConsulta);
		$mDatosNotas->execute([$mnAnnoLectivo, $mnSemestreLectivo, $msMatricula, 6, $msCodAsignatura]);
		$mnRegistros = $mDatosNotas->rowCount();
		if ($mnRegistros > 0)
		{
			$mrNotas = $mDatosNotas->fetch();

			switch(intval($mrNotas["NOTA_161"]))
			{
				case 0:
					$msConvalidacion = "";
					break;
				case 1:
					$msConvalidacion = "Reprobado";
					break;
				case 2:
					$msConvalidacion = "Aprobado";
					break;
			}
		}
		else
			$msConvalidacion = "";

		if ($mbColorea == 0)
			$msHTML .= '<td class="centro">' . trim($msConvalidacion) . '</td>';
		else
			$msHTML .= '<td class="fondo centro">' . trim($msConvalidacion) . '</td>';

		$msHTML .= '</tr>';

		if ($mbColorea == 0)
			$mbColorea = 1;
		else
			$mbColorea = 0;
	}
	$msHTML .= '</table>';
	$msHTML .= '<table>';
	$msHTML .= '<br><br><br><br><br><br>';
	$msHTML .= '<tr>';
	$msHTML .= '<td style="width: 20%;">&nbsp;</td>';
	$msHTML .= '<td style="width: 20%;" class="bordeSuperior centro">' . $msDocente . '<br>Docente</td>';
	$msHTML .= '<td style="width: 20%;">&nbsp;</td>';
	$msHTML .= '<td style="width: 20%;" class="bordeSuperior centro">Firma y sello<br>Registro académico</td>';
	$msHTML .= '<td style="width: 20%;">&nbsp;</td>';
	$msHTML .= '</tr>';
	$msHTML .= '</table>';
	$pdf->SetY(35);
	$pdf->writeHTML($msHTML);
	$pdf->Output();
}
?>