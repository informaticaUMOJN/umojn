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
$mnRegistro = fxVerificaUsuario();
$mnAdministrador = fxVerificaAdministrador();
$mnSupervisor = fxVerificaSupervisor();

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
		public $msAsignatura;
		public $msTurno;
		public $msDocente;
		public $mFechas;

		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,12,0,16);
			// Title
			$mid_x = 278; // width of the "PDF screen", fixed by now.
			// Helvetica bold 18
			$this->SetFont('helvetica','B',14);
			$Titulo = 'ASISTENCIA ESTUDIANTIL';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 13, $Titulo);
			// Helvetica normal 18
			$this->SetFont('helvetica','',11);
			$Titulo = "Asignatura: " . $this->msAsignatura;
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 18, $Titulo);
			$Titulo = "Turno: " . $this->msTurno;
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 22, $Titulo);
			$Titulo = "Docente: " . $this->msDocente;
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 26, $Titulo);

			$this->SetFont('helvetica','',8);
			$this->SetFillColor(0,0,255);
			$this->SetTextColor(255,255,255);
			
			$this->setXY(15, 35);
			$this->cell(31, 20, 'ESTUDIANTE', 0, 0, 'C', true, '', 0, false, 'T', 'M');
			
			$mnY = 52; //Y se convierte en X durante la rotación de Cell
			foreach($this->mFechas as $mFecha)
			{
				$this->StartTransform();
				$this->Rotate(270, $mnY, 35);
				$this->setXY($mnY, 35);
				$this->cell(20, 5.4, $mFecha, 0, 0, 'C', true);
				$this->StopTransform();
				$mnY += 6.03;
			}
			$this->SetTextColor(0,0,0);
		}
		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Helvetica italic 8
			$this->SetFont('helvetica','I',8);
			// Page number
			$this->Cell(0,10,'Página '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'L');
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

	function escribeFechas($msCodAsignatura, $mnTurno)
	{
		//Escribe las FECHAS
		$msConsulta = "select FECHA_073 from UMO070A join UMO073A on UMO070A.SYLLABUS_REL = UMO073A.SYLLABUS_REL ";
		$msConsulta .= "where UMO070A.ASIGNATURA_REL = ? and TURNO_070 = ? order by FECHA_073";
		$mConexion = fxAbrirConexion();
		$mFechas = $mConexion->prepare($msConsulta);
		$mFechas->execute([$msCodAsignatura, $mnTurno]);
		
		$arrFechas = [];
		while ($mFilaFechas = $mFechas->fetch())
		{
			$msFecha = date("Y-m-d", strtotime($mFilaFechas["FECHA_073"]));
			$msFechaRep = DevuelveFecha($msFecha);
			array_push($arrFechas, $msFechaRep);
		}
		return $arrFechas;
	}

	$msCodAsignatura = $_POST["msAsignatura"];
	$mnTurno = $_POST["mnTurno"];
	$mnAnno = $_POST["mnAnno"];
	$mnSemestre = $_POST["mnSemestre"];
	$msCodDocente = $_POST["msDocente"];

	$msConsulta = "select distinct NOMBRE_100, NOMBRE_060, NOMBRE_040, TURNO_150 ";
	$msConsulta .= "from UMO150A, UMO100A, UMO060A, UMO040A ";
	$msConsulta .= "where UMO150A.DOCENTE_REL = UMO100A.DOCENTE_REL ";
	$msConsulta .= "and UMO150A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
	$msConsulta .= "and UMO150A.CARRERA_REL = UMO040A.CARRERA_REL ";
	$msConsulta .= "and UMO150A.ASIGNATURA_REL = ? and TURNO_150 = ? and ANNO_150 = ? and SEMESTRE_150 = ? and UMO150A.DOCENTE_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodAsignatura, $mnTurno, $mnAnno, $mnSemestre, $msCodDocente]);
	$mRegistros = $mDatos->rowCount();

	if ($mRegistros > 0)
	{
		$mFila = $mDatos->fetch();
		$msDocente = $mFila["NOMBRE_100"];
		$msAsignatura = $mFila["NOMBRE_060"];
		$msCarrera = $mFila["NOMBRE_040"];
	}
	else
	{
		$msConsulta = "select NOMBRE_100 from UMO100A where DOCENTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodDocente]);
		$mFila = $mDatos->fetch();
		$msDocente = $mFila["NOMBRE_100"];

		$msConsulta = "select CARRERA_REL, NOMBRE_060 from UMO060A where ASIGNATURA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodAsignatura]);
		$mFila = $mDatos->fetch();
		$msAsignatura = $mFila["NOMBRE_060"];
		$msCodCarrera = $mFila["CARRERA_REL"];

		$msConsulta = "select NOMBRE_040 from UMO040A where CARRERA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodCarrera]);
		$mFila = $mDatos->fetch();
		$msCarrera = $mFila["NOMBRE_040"];
	}

	$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}
	
	$pdf->msAsignatura=$msAsignatura;
	$pdf->msTurno=DevuelveTurno($mnTurno);
	$pdf->msDocente=$msDocente;
	$pdf->mFechas=escribeFechas($msCodAsignatura, $mnTurno);
	$pdf->AddPage();

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',8);

	$mbRelleno = true;
	$mbEscribeRotulo = true;
	$mnLinea = 35;
	$msHTML = '<style>';
	$msHTML .= ".ancho{";
	$msHTML .= "width: 17;";
	$msHTML .= "}";
	$msHTML .= ".celdaTh{";
	$msHTML .= "width: 17; height: 60;";
	$msHTML .= "}";
	$msHTML .= ".anchoE{";
	$msHTML .= "width: 90;";
	$msHTML .= "}";
	$msHTML .= ".relleno{";
	$msHTML .= "background-color: rgb(230,230,230);";
	$msHTML .= "}";
	$msHTML .= ".centro{";
	$msHTML .= "text-align: center;";
	$msHTML .= "}";
	$msHTML .= ".bordeSuperior{";
	$msHTML .= "border-top: 2px solid black; border-right: none; border-bottom: none; border-left: none;";
	$msHTML .= "}";
	$msHTML .= '</style>';
	$msHTML .= '<table style="width: 100%">';

	//Escribe las FECHAS
	$msConsulta = "select FECHA_073 from UMO070A join UMO073A on UMO070A.SYLLABUS_REL = UMO073A.SYLLABUS_REL ";
	$msConsulta .= "where UMO070A.ASIGNATURA_REL = ? and TURNO_070 = ? order by FECHA_073";
	$mFechas = $m_cnx_MySQL->prepare($msConsulta);
	$mFechas->execute([$msCodAsignatura, $mnTurno]);
	
	$msHTML .= '<thead><tr><th class="anchoE">&nbsp;</th>';
	while ($mFilaFechas = $mFechas->fetch())
	{
		$msFecha = date("Y-m-d", strtotime($mFilaFechas["FECHA_073"]));
		$msFechaRep = DevuelveFecha($msFecha);
		$msHTML .= '<th class="celdaTh">';
		$msCelda = '<div style="color: white">&nbsp;</div>';
		$msHTML .= '</th>';
	}
	$msHTML .= '</tr></thead>';

	$msConsulta = "select UMO030A.MATRICULA_REL, APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010 from UMO050A, UMO030A, UMO031A, UMO010A ";
	$msConsulta .= "where UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and UMO030A.MATRICULA_REL = UMO031A.MATRICULA_REL ";
	$msConsulta .= "and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and ASIGNATURA_REL = ? and TURNO_050 = ?";
	$mEstudiantes = $m_cnx_MySQL->prepare($msConsulta);
	$mEstudiantes->execute([$msCodAsignatura, $mnTurno]);
	
	
	$msHTML .= '<tbody>';
	
	while ($mFilaEstudiantes = $mEstudiantes->fetch())
	{
		$msMatricula = $mFilaEstudiantes["MATRICULA_REL"];
		$msEstudiante = $mFilaEstudiantes["APELLIDO1_010"];
		if (trim($mFilaEstudiantes["APELLIDO2_010"]) != "")
			$msEstudiante .= " " . $mFilaEstudiantes["APELLIDO2_010"];
		
		$msEstudiante .= ", " . $mFilaEstudiantes["NOMBRE1_010"];
		if (trim($mFilaEstudiantes["NOMBRE2_010"]) != "")
			$msEstudiante .= " " . $mFilaEstudiantes["NOMBRE2_010"];
		
		$msHTML .= '<tr>';
		if ($mbRelleno)
			$msHTML .= '<td class="anchoE relleno">' . $msEstudiante . '</td>';
		else
			$msHTML .= '<td class="anchoE">' . $msEstudiante . '</td>';
		
		$msConsulta = "select FECHA_073 from UMO070A join UMO073A on UMO070A.SYLLABUS_REL = UMO073A.SYLLABUS_REL ";
		$msConsulta .= "where UMO070A.ASIGNATURA_REL = ? and TURNO_070 = ? order by FECHA_073";
		$mFechas = $m_cnx_MySQL->prepare($msConsulta);
		$mFechas->execute([$msCodAsignatura, $mnTurno]);
		while ($mFilaFechas = $mFechas->fetch())
		{
			$msFecha = date("Y-m-d", strtotime($mFilaFechas["FECHA_073"]));
			
			$msConsulta = "select ESTADO_151 from UMO150A, UMO151A where UMO150A.ASISTENCIA_REL = UMO151A.ASISTENCIA_REL and ";
			$msConsulta .= "MATRICULA_REL = ? and FECHA_150 = ?";
			$mAsistencia = $m_cnx_MySQL->prepare($msConsulta);
			$mAsistencia->execute([$msMatricula, $msFecha]);
			$mnRegistros = $mAsistencia->rowCount();
			if ($mnRegistros > 0)
			{
				$mFilaAsistencia = $mAsistencia->fetch();
				$mnEstado = $mFilaAsistencia["ESTADO_151"];
				switch ($mnEstado)
				{
					case 0:
						if ($mbRelleno)
							$msHTML .= '<td class="centro ancho relleno">P</td>';
						else
							$msHTML .= '<td class="centro ancho">P</td>';
					break;
					case 1:
						if ($mbRelleno)
							$msHTML .= '<td class="centro ancho relleno">A</td>';
						else
							$msHTML .= '<td class="centro ancho">A</td>';
					break;
					default:
						if ($mbRelleno)
							$msHTML .= '<td class="centro ancho relleno">J</td>';
						else
							$msHTML .= '<td class="centro ancho">J</td>';
				}
			}
			else
				if ($mbRelleno)
					$msHTML .= '<td class="centro ancho relleno">&nbsp;</td>';
				else
					$msHTML .= '<td class="centro ancho">&nbsp;</td>';
		}
		$msHTML .= '</tr>';
		$mbRelleno = !$mbRelleno;
	}

	$msHTML .= '</tbody></table>';
	$msHTML .= '<br><br><br><br><br><br>';
	$msHTML .= '<table>';
	$msHTML .= '<tr>';
	$msHTML .= '<td style="width: 20%;">&nbsp;</td>';
	$msHTML .= '<td style="width: 20%;" class="bordeSuperior centro">' . $msDocente . '<br>Docente</td>';
	$msHTML .= '<td style="width: 20%;">&nbsp;</td>';
	$msHTML .= '<td style="width: 20%;" class="bordeSuperior centro">Firma y sello<br>Registro académico</td>';
	$msHTML .= '<td style="width: 20%;">&nbsp;</td>';
	$msHTML .= '</tr>';
	$msHTML .= '</table>';
	$pdf->SetXY(15,$mnLinea);
	$pdf->writeHTML($msHTML);
	$pdf->Output();
}
?>