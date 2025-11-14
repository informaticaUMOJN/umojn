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
$m_cnx_MySQL = fxAbrirConexion();
	
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
			$this->Image('imagenes/logoRep.jpg',15,12,0,18);

			// Title
			$this->SetFont('helvetica','B',13);
			$Titulo = 'ASIGNATURAS INSCRITAS';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 13, $Titulo);
		}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica','I',8);
			// Page number
			//$this->Cell(0,10,'Página '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'L');
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

	$mMatricula = $_POST["msMatricula"];

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
		
		$msConsulta = "select UMO031A.ASIGNATURA_REL, NOMBRE_060, CODIGO_060, CREDITOS_051 from UMO031A, UMO060A, UMO051A, UMO030A ";
		$msConsulta .= "where UMO031A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and UMO031A.ASIGNATURA_REL = UMO051A.ASIGNATURA_REL ";
		$msConsulta .= "and UMO031A.MATRICULA_REL = ? and UMO030A.MATRICULA_REL = UMO031A.MATRICULA_REL and ";
		$msConsulta .= "UMO030A.PLANESTUDIO_REL = UMO051A.PLANESTUDIO_REL";
		
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMatricula]);
		$mRegistros = $mDatos->rowCount();

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

		if ($mRegistros > 0)
		{
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

			$pdf->SetTextColor(0,0,250);
			$pdf->SetFont('helvetica','B',12);

			$msHTML .= '<table>';
			$msHTML .= '<tr>';
			$msHTML .= '<th style="width: 20%;"><strong>Código</strong></th>';
			$msHTML .= '<th style="width: 60%;"><strong>Asignatura</strong></th>';
			$msHTML .= '<th style="width: 20%;" class="centro"><strong>Créditos</strong></th>';
			$msHTML .= '</tr>';

			while ($mFila = $mDatos->fetch())
			{
				$msHTML .= '<tr>';
				$msHTML .= '<td>' . $mFila["CODIGO_060"] . '</td>';
				$msHTML .= '<td>' . $mFila["NOMBRE_060"] . '</td>';
				$msHTML .= '<td class="centro">' . $mFila["CREDITOS_051"] . '</td>';
				$msHTML .= '</tr>';
			}
			$msHTML .= '</table>';
			$msHTML .= '<br><br><br><br><br><br>';
			$msHTML .= '<table>';
			$msHTML .= '<tr>';
			$msHTML .= '<td style="width: 30%;">&nbsp;</td>';
			$msHTML .= '<td style="width: 40%;" class="bordeSuperior centro">MSc. Xaviera Pérez Calero<br>Registro académico</td>';
			$msHTML .= '<td style="width: 30%;">&nbsp;</td>';
			$msHTML .= '</tr>';
			$msHTML .= '</table>';
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('helvetica','',10);
			$pdf->SetY(18);
			$pdf->writeHTML($msHTML);
		}
	}
	$pdf->Output();
}
?>