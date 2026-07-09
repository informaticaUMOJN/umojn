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
		public $msCarrera;
		public $msCohorte;

		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,12,0,18);
			// Title
			$mid_x = 278; // width of the "PDF screen", fixed by now.

			$this->SetFont('helvetica','B',13);
			$Titulo = 'ESTUDIANTES MATRICULADOS';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 13, $Titulo);
			$this->SetFont('helvetica','',11);
			$this->Text(($mid_x - $this->GetStringWidth($this->msCarrera)) / 2, 18, $this->msCarrera);
			$this->Text(($mid_x - $this->GetStringWidth($this->msCohorte)) / 2, 22, $this->msCohorte);
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

	function fxDevuelveEstado($mnEstado)
	{
		switch(intval($mnEstado))
		{
			case 0:
				$msEstado = "Activo";
				break;
			case 1:
				$msEstado = "Inactivo";
				break;
			case 2:
				$msEstado = "Pre-matriculado";
				break;
		}

		return $msEstado;
	}

	$msCodCarrera = $_POST["msCarrera"];
	$mnCohorte = $_POST["mnCohorte"];

	$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 33, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}

	$msConsulta = "select MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, CARNET_250, CORREOI_250, ";
	$msConsulta .= "FECHA_260, ANNOINGRESO_260, ESTADO_260 ";
	$msConsulta .= "from UMO260A, UMO250A ";
	$msConsulta .= "where UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL and COHORTE_260 = ? and UMO260A.CARRERA_REL = ? ";
	$msConsulta .= "order by ANNOINGRESO_260, APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250";

	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$mnCohorte, $msCodCarrera]);
	$mnRegistros = $mDatos->rowCount();

	if ($mnRegistros > 0)
	{
		$msHTML = '<style>';
		$msHTML .= "th{";
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

		$msHTML .= '<table cellpadding="2">';
		$msHTML .= '<thead><tr>';
		$msHTML .= '<th width="10%">MATRICULA</th>';
		$msHTML .= '<th width="10%">CARNET</th>';
		$msHTML .= '<th width="10%">FECHA DE MATRICULA</th>';
		$msHTML .= '<th width="8%" class="centro">AÑO DE INGRESO</th>';
		$msHTML .= '<th width="36%">ESTUDIANTE</th>';
		$msHTML .= '<th width="18%">CORREO</th>';
		$msHTML .= '<th width="8%">ESTADO</th>';
		$msHTML .= '</tr></thead>';

		$msHTML .= '<tbody>';

		$msConsulta = "select NOMBRE_040 from UMO040A where CARRERA_REL = ?";
		$mAux = $m_cnx_MySQL->prepare($msConsulta);
		$mAux->execute([$msCodCarrera]);
		$fAux = $mAux->fetch();
		$msCarrera = $fAux["NOMBRE_040"];

		$pdf->msCarrera = $msCarrera;
		$pdf->msCohorte = "Cohorte " . $mnCohorte;
		$pdf->addPage();

		$mbColorea = 0;
		while ($mFila = $mDatos->fetch())
		{
			$msCarnet = $mFila["CARNET_250"];
			$msEstudiante = trim($mFila["NOMBRE1_250"]);
			if (trim($mFila["NOMBRE2_250"]) != "")
				$msEstudiante .= ' ' . trim($mFila["NOMBRE2_250"]);
				
			$msEstudiante .= ' ' . trim($mFila["APELLIDO1_250"]);
			if (trim($mFila["APELLIDO2_250"]) != "")
				$msEstudiante .= ' ' . trim($mFila["APELLIDO2_250"]);
			
			$msMatricula = $mFila["MATRICULAPOS_REL"];
			$mnAnnoIngreso = $mFila["ANNOINGRESO_260"];
			$msCorreo = $mFila["CORREOI_250"];
			$msEstado = fxDevuelveEstado($mFila["ESTADO_260"]);

			$pdf->setFontSize(8);
		
			$msHTML .= '<tr>';

			if ($mbColorea == 0)
			{
				$msHTML .= '<td width="10%">' . $msMatricula . '</td>';
				$msHTML .= '<td width="10%">' . $msCarnet . '</td>';
				$msHTML .= '<td width="10%">' . fxFechaCorta($mFila["FECHA_260"]) . '</td>';
				$msHTML .= '<td width="8%" class="centro">' . $mnAnnoIngreso . '</td>';
				$msHTML .= '<td width="36%">' . $msEstudiante . '</td>';
				$msHTML .= '<td width="18%">' . $msCorreo . '</td>';
				$msHTML .= '<td width="8%">' . $msEstado . '</td>';
			}
			else
			{
				$msHTML .= '<td width="10%" class="fondo">' . $msMatricula . '</td>';
				$msHTML .= '<td width="10%" class="fondo">' . $msCarnet . '</td>';
				$msHTML .= '<td width="10%" class="fondo">' . fxFechaCorta($mFila["FECHA_260"]) . '</td>';
				$msHTML .= '<td width="8%" class="fondo centro">' . $mnAnnoIngreso . '</td>';
				$msHTML .= '<td width="36%" class="fondo">' . $msEstudiante . '</td>';
				$msHTML .= '<td width="18%" class="fondo">' . $msCorreo . '</td>';
				$msHTML .= '<td width="8%" class="fondo">' . $msEstado . '</td>';
			}

			$msHTML .= '</tr>';

			if ($mbColorea == 0)
				$mbColorea = 1;
			else
				$mbColorea = 0;
		}
		
		$msHTML .= '</tbody>';
		$msHTML .= '</table>';
		$pdf->SetY(33);
		$pdf->writeHTML($msHTML);
	}
	$pdf->Output();
}
?>