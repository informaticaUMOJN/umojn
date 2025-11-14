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

$Registro = fxVerificaUsuario();
	
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
	public $Carrera;
	public $FechaCorte;
	
	// Page header
	function Header()
	{
		// Logos
		$this->Image('imagenes/logoRep.jpg',15,8,0,16);
		$this->Image('imagenes/kanji.jpg',31,8,0,16);
		$mid_x = 210;
		// Title
		$this->SetFont('helvetica','B',15);
		$this->SetTextColor(0,0,0);

		$mnLinea = 18;
		$msTitulo = utf8_decode('ESTUDIANTES SOLVENTES');
		$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, $mnLinea, $msTitulo);

		$mnLinea += 5;
		$this->SetFont('helvetica','B',8);
		$this->Text(($mid_x - $this->GetStringWidth($this->Carrera)) / 2, $mnLinea, $this->Carrera);

		$mnLinea += 5;
		$this->SetFont('helvetica','B',8);
		$this->Text(($mid_x - $this->GetStringWidth($this->FechaCorte)) / 2, $mnLinea, $this->FechaCorte);
		
		$mnLinea += 10;
		$this->SetFillColor(0,100,255);
		$this->SetTextColor(255,255,255);
		$this->SetXY(10,$mnLinea);
		$this->Cell(20,7,utf8_decode('Carnet'),0,0,'L',true);
		$this->SetXY(30,$mnLinea);
		$this->Cell(70,7,utf8_decode('Nombre del Estudiante'),0,0,'L',true);
	}
	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('helvetica','I',8);
		// Page number
		$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'L');
		$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
	}
}

function fxFechaLarga($Fecha)
{
	$FechaDividida = explode("-", $Fecha);
	
	$Anno = $FechaDividida[0];
	$Mes = $FechaDividida[1];
	$Dia = $FechaDividida[2];
	
	switch ($Mes)
		{
			case "01":
				$NombreMes = "Enero";
				break;
			case "02":
				$NombreMes = "Febrero";
				break;
			case "03":
				$NombreMes = "Marzo";
				break;
			case "04":
				$NombreMes = "Abril";
				break;
			case "05":
				$NombreMes = "Mayo";
				break;
			case "06":
				$NombreMes = "Junio";
				break;
			case "07":
				$NombreMes = "Julio";
				break;
			case "08":
				$NombreMes = "Agosto";
				break;
			case "09":
				$NombreMes = "Septiembre";
				break;
			case "10":
				$NombreMes = "Octubre";
				break;
			case "11":
				$NombreMes = "Noviembre";
				break;
			case "12":
				$NombreMes = "Diciembre";
				break;
		}
	return ($Dia . " de " . $NombreMes . " de " . $Anno);
}

$mdFecha = trim($_POST["UMOJN"]);

//Obtención de datos
$msConsulta = "select KDSA030A.MATRICULA_REL, KDSA030A.ESTUDIANTE_REL, concat(trim(APELLIDOS_010), ', ', trim(NOMBRES_010)) as NOMBRECOMPLETO, MAXIMO_020, NOMBRE_020, FECHAINI_020, ";
$msConsulta .= "FECHAFIN_020, HORAINI_020, HORAFIN_020, fxDevuelveDias(KDSA020A.CURSO_REL) as DIASCLASE, CONVOCATORIA_020, CELULAR_010, CORREO_010, ESTADO_030, TIPOASISTENCIA_020, TIPOASISTENCIA_030 ";
$msConsulta .= "from KDSA030A, KDSA010A, KDSA020A where KDSA030A.ESTUDIANTE_REL = KDSA010A.ESTUDIANTE_REL and KDSA030A.CURSO_REL = KDSA020A.CURSO_REL ";
$msConsulta .= "and KDSA030A.ESTADO_030 <> 4 and KDSA030A.CURSO_REL = ?";

$m_cnx_MySQL = fxAbrirConexion();
$mDatos = $m_cnx_MySQL->prepare($msConsulta);
$mDatos->execute([$codCurso]);
$Registros = $mDatos->rowCount();
$Fila = $mDatos->fetch();

$TipoCurso = $Fila["TIPOASISTENCIA_020"];
switch ($TipoCurso)
{
	case 0:
		$Curso = $Fila["NOMBRE_020"] . " (Presencial)";
	break;

	case 1:
		$Curso = $Fila["NOMBRE_020"] . " (Virtual)";
	break;

	case 2:
		$Curso = $Fila["NOMBRE_020"] . " (On-line)";
}

$Maximo = $Fila["MAXIMO_020"];
$FechaIni = $Fila["FECHAINI_020"];
$FechaFin = $Fila["FECHAFIN_020"];
$HoraIni = date_create($Fila["HORAINI_020"]);
$HoraFin = date_create($Fila["HORAFIN_020"]);
$Horario = "De " . date_format($HoraIni, 'h:i a') . " a " . date_format($HoraFin, 'h:i a');
$DiasClase = $Fila["DIASCLASE"];
$Convocatoria = $Fila["CONVOCATORIA_020"];

$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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

if ($Registros > 0)
{	
	$pdf->Curso=$Curso;
	$pdf->Vigencia= "Del " . DevuelveFecha($FechaIni) . " al " . DevuelveFecha($FechaFin);
	$pdf->Horario=$Horario;
	$pdf->DiasClase=$DiasClase;
	$pdf->Convocatoria=$Convocatoria;
	
	$pdf->AddPage();
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',8);
	
	$Linea = 63;
	for ($i = 0; $i < $Registros; $i++)
	{
		$Matricula = $Fila["MATRICULA_REL"];
		$Celular = $Fila["CELULAR_010"];
		$Correo = $Fila["CORREO_010"];
		switch ($Fila["TIPOASISTENCIA_030"]) 
		{
			case 0:
				$TipoAsistencia = "Presencial";
				break;
			case 1:
				$TipoAsistencia = "Virtual";
				break;
			case 2:
				$TipoAsistencia = "On-line";
				break;
		}
		$NombreCompleto = utf8_decode(html_entity_decode($Fila["NOMBRECOMPLETO"]));
		
		$pdf->SetXY(10,$Linea);
		$pdf->Cell(20,5,$Matricula,0,0,'L',false);
		
		$pdf->SetXY(30,$Linea);
		$pdf->Cell(70,5,$NombreCompleto,0,0,'L',false);
		
		$pdf->SetXY(100,$Linea);
		$pdf->Cell(20,5,$Celular,0,0,'L',false);
		
		$pdf->SetXY(120,$Linea);
		$pdf->Cell(65,5,$Correo,0,0,'L',false);
		
		$pdf->SetXY(185,$Linea);
		$pdf->Cell(20,5,$TipoAsistencia,0,0,'L',false);
		
		$Linea += 5;
		
		if ($Linea >= 245)
		{
			$Linea=63;
			$pdf->AddPage();
		}
		$Fila = $mDatos->fetch();
	}
	
	$Linea += 5;
	$pdf->SetXY(140,$Linea);
	if ($Registros == 1)
		$pdf->Cell(65,5,$Registros . " matriculado",0,0,'R',false);
	else
		$pdf->Cell(65,5,$Registros . " matriculados",0,0,'R',false);

	$Linea += 5;
	$pdf->SetXY(140,$Linea);
	$pdf->Cell(65,5,utf8_decode(html_entity_decode("Máximo permitido: ")) . $Maximo,0,0,'R',false);
}
$pdf->Output();
}
?>