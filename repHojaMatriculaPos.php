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
		public $msMatricula;

		// Page header
		function Header()
		{
			$mid_x = 210; // width of the "PDF screen", fixed by now.

			// Logos
			$this->Image('imagenes/logoRep.jpg',15,8,0,16);
			$this->Image('imagenes/kanji.jpg',31,8,0,16);

			// Title
			$this->SetFont('helvetica','B',12);
			$msTitulo = 'UNIVERSIDAD DE MEDICINA';
			$this->Text(41, 8, $msTitulo);
			$msTitulo = 'ORIENTAL JAPON-NICARAGUA';
			$this->Text(41, 13, $msTitulo);
			$msTitulo = 'Excelencia académica con espíritu humanista';
			$this->SetFont('helvetica','I',9);
			$this->Text(41, 20, $msTitulo);
			
			$this->Line(120,10,120,22);
			$this->SetFont('helvetica','',7);
			$msTitulo = 'Puente del paso a desnivel de Rubenia 7c. al oeste';
			$this->Text(128, 10, $msTitulo);
			$msTitulo = 'Barrio Venezuela. Managua, Nicaragua';
			$this->Text(128, 13, $msTitulo);
			$msTitulo = 'registro.academico@umojn.edu.ni';
			$this->Text(128, 16, $msTitulo);
			$msTitulo = '2253-0340 / 2253-0344';
			$this->Text(128, 19, $msTitulo);

			$this->SetTextColor(0,0,250);
			$this->SetFont('helvetica','B',15);
			$msTitulo = 'HOJA DE MATRICULA ' . $this->msMatricula;
			$this->Text(($mid_x - $this->GetStringWidth($msTitulo)) / 2, 25, $msTitulo);
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

	function fxFechaCorta($Fecha)
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

	function fxCalculaEdad($Fecha)
	{
		//fecha actual
		$annoHoy=date("Y");
		$mesHoy=date("n");
		$diaHoy=date("j");

		//fecha de nacimiento
		$FechaDividida = explode("-", $Fecha);
		$annoNac = $FechaDividida[0];
		$mesNac = $FechaDividida[1];
		$diaNac = $FechaDividida[2];
		
		$edad= $annoHoy-$annoNac;

		if ($mesHoy < ($mesNac - 1))
			$edad -= 1;
			
		if (($mesNac - 1) == $mesHoy and $diaHoy < $diaNac)
			$edad -= 1;	
		
		return $edad;
	}

	function fxAnnoAcademico($mnAnno)
	{
		switch (intval($mnAnno))
		{
			case 1:
				$msAnno = "1er. año";
				break;
			case 2:
				$msAnno = "2do. año";
				break;
			case 3:
				$msAnno = "3er. año";
				break;
			case 4:
				$msAnno = "4to. año";
				break;
			case 5:
				$msAnno = "5to. año";
				break;
		}

		return $msAnno;
	}

	$codMatricula = trim($_POST["UMOJN"]);

	//Obtención de datos
	$msConsulta = "select UMO260A.MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, SEXO_250, COHORTE_260, CARNET_250, ";
	$msConsulta .= "FECHA_260, RECIBO_260, NACIONALIDAD_250, FECHANAC_250, CEDULA_250, TELEFONO_250, CELULAR_250, ";
	$msConsulta .= "DIRECCION_250, CORREOE_250, CORREOI_250, EMERGENCIA_250, TEL_EMERGENCIA_250, CEL_EMERGENCIA_250, MEDIO_250, PERIODO_230, ";
	$msConsulta .= "NOMBRE_040, NOMBRE_180, IFNULL((select RUTA_251 from UMO251A where UMO251A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL and TIPO_REL = 4), '') as FOTO, ";
	$msConsulta .= "TITULO_260, NOTAS_260, CEDULA_260, CURRICULUM_260, TURNO_230 ";
	$msConsulta .= "from UMO260A, UMO250A, UMO040A, UMO230A, UMO180A ";
	$msConsulta .= "where UMO260A.CARRERA_REL = UMO040A.CARRERA_REL and UMO260A.PLANPOSGRADO_REL = UMO230A.PLANPOSGRADO_REL and ";
	$msConsulta .= "UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL and UMO250A.UNIVERSIDAD_REL = UMO180A.UNIVERSIDAD_REL and ";
	$msConsulta .= "MATRICULAPOS_REL = ?";

	$m_cnx_MySQL = fxAbrirConexion();
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$codMatricula]);

	$mFila = $mDatos->fetch();
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
	
	$msMatricula = $mFila["MATRICULAPOS_REL"];
	$msFoto = $mFila["FOTO"];
	$msNombre1 = $mFila["NOMBRE1_250"];
	$msNombre2 = $mFila["NOMBRE2_250"];
	$msApellido1 = $mFila["APELLIDO1_250"];
	$msApellido2 = $mFila["APELLIDO2_250"];
	$msSexo = $mFila["SEXO_250"];
	$msCohorte = $mFila["COHORTE_260"];
	$msCarnet = $mFila["CARNET_250"];
	$msFechaMat = $mFila["FECHA_260"];
	$msRecibo = $mFila["RECIBO_260"];
	$msNacinalidad = $mFila["NACIONALIDAD_250"];
	$msFechaNac = $mFila["FECHANAC_250"];
	$msCedula = $mFila["CEDULA_250"];
	$msTelefono = $mFila["TELEFONO_250"];
	$msCelular = $mFila["CELULAR_250"];
	$msDireccion = $mFila["DIRECCION_250"];
	$msCorreoE = $mFila["CORREOE_250"];
	$msCorreoI = $mFila["CORREOI_250"];
	$msEmergencia = $mFila["EMERGENCIA_250"];
	$msTelEmergencia = $mFila["TEL_EMERGENCIA_250"];
	$msCelEmergencia = $mFila["CEL_EMERGENCIA_250"];
	$msMedio = $mFila["MEDIO_250"];
	$msPlanEstudio = $mFila["PERIODO_230"];
	$mnTurno = $mFila["TURNO_230"];
	$msCarrera = $mFila["NOMBRE_040"];
	$msUniversidad = $mFila["NOMBRE_180"];
	$mbDiploma = $mFila["TITULO_260"];
	$mbNotas = $mFila["NOTAS_260"];
	$mbCedula = $mFila["CEDULA_260"];
	$mbCurriculum = $mFila["CURRICULUM_260"];
	
	$pdf->msMatricula=$msMatricula;
	
	$pdf->AddPage();
	$pdf->SetTextColor(0,0,0);

	if ($msFoto != '')
		$pdf->Image($msFoto,15,30,0,25);
	else{
		$pdf->Rect(15,35,18,20);
		$pdf->SetFont('helvetica','',10);
		$pdf->Text(18, 43, 'FOTO');
	}
	
	//Generales del estudiante
	$msEstudiante = $msNombre1;

	if (trim($msNombre2) != "")
		$msEstudiante .= ' ' . $msNombre2;
	
	$msEstudiante .= ' ' . $msApellido1;

	if (trim($msApellido2) != "")
		$msEstudiante .= ' ' . $msApellido2;
	
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(35, 35, $msEstudiante);
	
	$pdf->SetFont('helvetica','',10);
	if ($msSexo == "M")
		$msRotulo = "Nacido el " . fxFechaLarga($msFechaNac) . " (" . fxCalculaEdad($msFechaNac) . " años)";
	else
		$msRotulo = "Nacida el " . fxFechaLarga($msFechaNac) . " (" . fxCalculaEdad($msFechaNac) . " años)";
	$pdf->Text(35, 41, $msRotulo);

	$msRotulo = "Cédula " . $msCedula;
	$pdf->Text(35, 46, $msRotulo);

	//Generales de la matrícula
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 35, 'Fecha de la matrícula');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 35, fxFechaCorta($msFechaMat));

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 40, 'Recibo');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 40, $msRecibo);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 45, 'Carnet');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 45, $msCarnet);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 50, 'Cohorte');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 50, $msCohorte);

	//Datos académicos
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 60, 'Datos académicos');

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 65, 'Carrera');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(65, 65, $msCarrera);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 70, 'Turno');

	$pdf->SetFont('helvetica','',10);
	if ($mnTurno == 1)
		$pdf->Image('imagenes/check.jpg', 65, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg',65, 70, 5);
	$pdf->Text(70,70,"Diurno");

	if ($mnTurno == 2)
		$pdf->Image('imagenes/check.jpg', 83, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg', 83, 70, 5);
	$pdf->Text(87,70,"Matutino");

	if ($mnTurno == 3)
		$pdf->Image('imagenes/check.jpg', 103, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg', 103, 70, 5);
	$pdf->Text(107,70,"Vespertino");

	if ($mnTurno == 4)
		$pdf->Image('imagenes/check.jpg', 127, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg',127, 70, 5);
	$pdf->Text(131,70,"Nocturno");

	if ($mnTurno == 5)
		$pdf->Image('imagenes/check.jpg', 148, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg',148, 70, 5);
	$pdf->Text(152,70,"Sabatino");

	if ($mnTurno == 6)
		$pdf->Image('imagenes/check.jpg', 169, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg',169, 70, 5);
	$pdf->Text(173,70,"Dominical");

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 75, 'Plan de estudio');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(65, 75, $msPlanEstudio);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 80, 'Universidad de procedencia');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(65, 80, $msUniversidad);

	//Datos de contacto
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 90, 'Datos de contacto');

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 95, 'Teléfono');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 95, $msTelefono);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 100, 'Celular');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 100, $msCelular);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(100, 95, 'Correo particular');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(140, 95, $msCorreoE);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(100, 100, 'Correo institucional');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(140, 100, $msCorreoI);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 105, 'Dirección domiciliar');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 105, $msDireccion);

	//Contacto de emergencia
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 115, 'Contacto de emergencia');

	if ($msTelEmergencia != ''){
		$msTelsEmergencia = $msTelEmergencia;
		if ($msCelEmergencia != '')
			$msTelsEmergencia .= ' / ' . $msCelEmergencia;
	}
	else
		$msTelsEmergencia = $msCelEmergencia;

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 120, 'Nombre');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 120, $msEmergencia);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 125, 'Teléfono(s)');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 125, $msTelsEmergencia);

	//Documentos entregados
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 135, 'Documentos entregados');

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',10);

	if ($mbCedula == 0)
		$pdf->Image('imagenes/uncheck.jpg', 30, 140, 5);
	else
		$pdf->Image('imagenes/check.jpg',30, 140, 5);
	$pdf->Text(35,140,"Cédula de identidad");

	if ($mbDiploma == 0)
		$pdf->Image('imagenes/uncheck.jpg', 80, 140, 5);
	else
		$pdf->Image('imagenes/check.jpg',80, 140, 5);
	$pdf->Text(85,140,"Diploma de bachiller");

	if ($mbCurriculum == 0)
		$pdf->Image('imagenes/uncheck.jpg', 30, 145, 5);
	else
		$pdf->Image('imagenes/check.jpg',30, 145, 5);
	$pdf->Text(35,145,"Acta de nacimiento");

	if ($mbNotas == 0)
		$pdf->Image('imagenes/uncheck.jpg', 80, 145, 5);
	else
		$pdf->Image('imagenes/check.jpg',80, 145, 5);
	$pdf->Text(85,145,"Calificaciones de secundaria");

	/*Cursos inscritos*/
	$msConsulta = "select UMO261A.CURSOPOSGRADO_REL, NOMBRE_240, CODIGO_240, CREDITOS_231 from UMO261A, UMO240A, UMO231A, UMO260A ";
	$msConsulta .= "where UMO261A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL and UMO261A.CURSOPOSGRADO_REL = UMO231A.CURSOPOSGRADO_REL ";
	$msConsulta .= "and UMO261A.MATRICULAPOS_REL = ? and UMO260A.MATRICULAPOS_REL = UMO261A.MATRICULAPOS_REL and ";
	$msConsulta .= "UMO260A.PLANPOSGRADO_REL = UMO231A.PLANPOSGRADO_REL";
	$m_cnx_MySQL = fxAbrirConexion();
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$codMatricula]);
	$mRegistros = $mDatos->rowCount();

	$mnLinea = 155;

	$pdf->SetFont('helvetica','',10);
	if ($mRegistros > 0)
	{
		//Cursos inscritos
		$pdf->SetTextColor(0,0,250);
		$pdf->SetFont('helvetica','B',12);
		$pdf->Text(15, $mnLinea, 'Cursos inscritos');

		$msHTML = '<table>';
		$msHTML .= '<tr>';
		$msHTML .= '<th style="text-align: left;background-color:rgb(0,0,255);color: white; width: 20%;"><strong>Código</strong></th>';
		$msHTML .= '<th style="text-align: left;background-color:rgb(0,0,255);color: white; width: 60%;"><strong>Asignatura</strong></th>';
		$msHTML .= '<th style="text-align: center;background-color:rgb(0,0,255);color: white; width: 20%;"><strong>Créditos</strong></th>';
		$msHTML .= '</tr>';

		while ($mFila = $mDatos->fetch())
		{
			$msHTML .= '<tr>';
			$msHTML .= '<td>' . $mFila["CODIGO_240"] . '</td>';
			$msHTML .= '<td>' . $mFila["NOMBRE_240"] . '</td>';
			$msHTML .= '<td style="text-align: center">' . $mFila["CREDITOS_231"] . '</td>';
			$msHTML .= '</tr>';
		}
		$msHTML .= '</table>';
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('helvetica','',10);
		$mnLinea += 5;
		$pdf->SetY($mnLinea);
		$pdf->writeHTML($msHTML);
		$mnLinea += 5 * $mRegistros;
		$mnLinea += 5;
	}

	//Mensaje final
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->SetXY(15, $mnLinea);
	$pdf->Cell(30, 0, 'IMPORTANTE', 0, 0, 0, true);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',12);
	$mnLinea += 5;
	$pdf->Text(15,$mnLinea,"1.-Los pagos de los aranceles comprenden desde Enero hasta Diciembre del año lectivo.");
	$mnLinea += 5;
	$pdf->Text(15,$mnLinea,"2.-No se realizan reembolsos o transferencias del monto pagado de Matrícula y/o Mensualidad.");

	$mnLinea += 10;
	$pdf->Image('imagenes/firmaRegistro.jpg', 85, $mnLinea, 40);
	$pdf->SetFont('helvetica','B',8);
	$mnLinea += 27;
	$pdf->Line(85,$mnLinea,120,$mnLinea);
	$mnLinea += 2;
	$pdf->Text(93,$mnLinea,"Firma y Sello");
	$mnLinea += 4;
	$pdf->Text(88,$mnLinea,"Registro Académico");
	$pdf->Output();
}
?>