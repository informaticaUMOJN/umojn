<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}
    
    include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
    require_once ("funciones/fxUsuarios.php");
    require_once ("funciones/fxCarreras.php");
    require_once ("funciones/fxSyllabusPosgrado.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$mnRegistro = fxVerificaUsuario();
	
	if ($mnRegistro == 0)
	{
?>
    <div class="container text-center">
        <div id="DivContenido">
            <img src="imagenes/errordeacceso.png" />
        </div>
    </div>
<?php 
    }
	else
	{
        $mbAdministrador = fxVerificaAdministrador();
        $mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("procSyllabusPos");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor)
		{
?>
        <div class="container text-center">
            <div id="DivContenido">
                <img src="imagenes/errordeacceso.png" />
            </div>
        </div>
<?php 
        }
		else
		{
			if (isset($_POST["txtCodSyllabus"]))
			{
				$mnOperacion = $_POST["Operacion"];
				$msCodigo = $_POST["txtCodSyllabus"];
                $msPlanPosgrado = $_POST["cboPlanPosgrado"];
                $msDocente = $_POST["cboDocente"];
                $msCurso = $_POST["cboCurso"];
                $mdFecha = $_POST["dtpFecha"];
                $mnTurno = $_POST["optTurno"];
                $msCohorte = $_POST["txtCohorte"];
                $mnRegimen = $_POST["optRegimen"];
                $msMediacion = $_POST["txtMediacion"];
                $msEjesValores = $_POST["txtEjesValores"];

                $gridObjetivoGrl = $_POST["gridObjGral"];
                $gridObjetivoUnd = $_POST["gridObjMod"];
                $gridDetalle = $_POST["gridDetalle"];

                if (isset($_POST["gridObsDocente"]))
                    $gridObsDocente = $_POST["gridObsDocente"];

                if (isset($_POST["gridObsAcademica"]))
                    $gridObsAcademica = $_POST["gridObsAcademica"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarSyllabusPos($msPlanPosgrado, $msDocente, $msPlanPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msMediacion, $msEjesValores);
                    $msBitacora = $msCodigo . "; " . $msPlanPosgrado . "; " . $msDocente . "; " . $msCurso . "; " . $mdFecha . "; " . $msCohorte . "; " . $mnTurno . "; " . $mnRegimen;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO290A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarSyllabus($msCodigo, $msPlanPosgrado, $msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msMediacion, $msEjesValores);
                    fxBorrarDetObjGral($msCodigo);
                    fxBorrarDetObjMod($msCodigo);
                    fxBorrarDetObsAcademica($msCodigo);
                    fxBorrarDetObsDocente($msCodigo);
                    fxBorrarDetSyllabus($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msPlanPosgrado . "; " . $msDocente . "; " . $msCurso . "; " . $mdFecha . "; " . $msCohorte . "; " . $mnTurno . "; " . $mnRegimen;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO290A", $msCodigo, "", "Modificar", $msBitacora);
                }
                
                $itemId = 1;
				foreach($gridObjetivoGrl as $Registro)
				{
					$msObjetivo = $Registro['objGral'];
                    fxGuardarDetObjGral($msCodigo, $itemId, $msObjetivo);
                    $itemId++;
				}
				
				$itemId = 1;
				foreach($gridObjetivoUnd as $Registro)
				{
                    $msObjetivo = $Registro['ObjMod'];
                    $msModulo = $Registro['modulo'];
					fxGuardarDetObjMod($msCodigo, $itemId, $msModulo, $msObjetivo);
					$itemId++;
                }
                
                if (isset($_POST["gridObsDocente"]))
                {
                    $itemId = 1;
                    foreach($gridObsDocente as $Registro)
                    {
                        $msObservacion = $Registro['obsDocente'];
                        fxGuardarDetObsDocente($msCodigo, $itemId, $msObservacion);
                        $itemId++;
                    }
                }

                if (isset($_POST["gridObsAcademica"]))
                {
                    $itemId = 1;
                    foreach($gridObsAcademica as $Registro)
                    {
                        $msObservacion = $Registro['obsAcademica'];
                        fxGuardarDetObsAcademica($msCodigo, $itemId, $msObservacion);
                        $itemId++;
                    }
                }

                $itemId = 1;
				foreach($gridDetalle as $Registro)
				{
                    $mdFecha = $Registro['fecha'];
                    $msModulo = $Registro['modulo'];
                    $msContenido = $Registro['contenido'];
                    $msObjetivoEsp = $Registro['objEsp'];
                    $msForma = $Registro['forma'];
                    $msMedios = $Registro['medios'];
                    $msEvaluacion = $Registro['evaluacion'];
					fxGuardarDetSyllabus($msCodigo, $itemId, $mdFecha, $msModulo, $msContenido, $msObjetivoEsp, $msForma, $msMedios, $msEvaluacion);
					$itemId++;
				}
				?><meta http-equiv="Refresh" content="0;url=gridSyllabus.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = $_POST["UMOJN"];
                else
                    $msCodigo = "";

				$mRecordSet = fxDevuelveSyllabusPos(0, "", $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msPlanPosgrado = $mFila["PLANESTUDIO_REL"];
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msCurso = $mFila["Curso_REL"];
                    $mdFecha = $mFila["FECHA_070"];
                    $msCohorte = $mFila["ANNO_070"];
                    $mnRegimen = $mFila["SEMESTRE_070"];
                    $mnTurno = $mFila["TURNO_070"];
                    $msGrupo = $mFila["GRUPO_070"];
                    $msMediacion = $mFila["RECOMENDACIONES_070"];
                    $msEjesValores = $mFila["EJESVALORES_070"];
                }
                else 
                {
                    $msPlanPosgrado = "";
                    $msDocente = "";
                    $msCarrera = "";
                    $msCurso = "";
                    $mdFecha = date('Y-m-d');
                    $msCohorte = "";
                    $mnRegimen = 4;
                    $mnTurno = 6;
                    $msGrupo = "";
                    $msMediacion = "";
                    $msEjesValores = "";
                }
	?>

<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
			<div class="col-xs-12 col-md-11">
				<div class="degradado"><strong>Syllabus de posgrado</strong></div>
			</div>
		</div>

        <div class="row">
            <div class="col-xs-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procSyllabus" name="procSyllabus" action="procSyllabus.php">
                    <div class="form-group row">
                        <label for="txtCodSyllabus" class="col-sm-12 col-md-2 col-form-label">Código del Syllabus</label>
                        <div class="col-sm-12 col-md-2">
                            <?php echo('<input type="text" class="form-control" id="txtCodSyllabus" name="txtCodSyllabus" value="' . $msCodigo . '" readonly />'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFecha" class="col-sm-12 col-md-2 col-form-label">Fecha</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
								echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />');
						    ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente">');
                                    
                                    if (trim($_SESSION["gsDocente"]) != "" and $mbAdministrador == 0 and $mbSupervisor == 0)
                                    {
                                        $mDocente = $_SESSION["gsDocente"];
                                        $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A where ACTIVO_100 = 1 and DOCENTE_REL = ? order by NOMBRE_100";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
					                    $mDatos->execute([$mDocente]);
                                    }
                                    else
                                    {
                                        $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A where ACTIVO_100 = 1 order by NOMBRE_100";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
					                    $mDatos->execute();
                                    }
                                }
                                else
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente" disabled>');

                                    $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A order by NOMBRE_100 desc";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
					                $mDatos->execute();
                                }

                                while ($mFila = $mDatos->fetch())
                                {
                                    $Docente = rtrim($mFila["DOCENTE_REL"]);
                                    $Texto = rtrim($mFila["NOMBRE_100"]);
                                    
                                    if ($msDocente == "")
                                        $msDocente = $Docente;
                                    
                                    if ($msDocente == $Docente)
                                        echo("<option value='" . $Docente . "' selected>" . $Texto . "</option>");
                                    else
                                        echo("<option value='" . $Docente . "'>" . $Texto . "</option>");
                                }
                            ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboCarrera" class="col-sm-auto col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaPlanPosgrado()">');
                                else
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" disabled>');

                                if ($msCurso != ""){
                                    $msConsulta = "select CARRERA_REL from UMO240A where CURSOPOSGRADO_REL = ?";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute([$msCurso]);
                                    $mFila = $mDatos->fetch();
                                    $msCarrera = $mFila["CARRERA_REL"];
                                }

                                $mDatos = fxDevuelveCarrera(1);
                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = rtrim($mFila["CARRERA_REL"]);
                                    $msTexto = rtrim($mFila["NOMBRE_040"]);
                                    $mbPosgrado = $mFila["POSGRADO_040"];

                                    if ($mbPosgrado == 1)
                                    {
                                        if ($msCarrera == "")
                                        {
                                            echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                            $msCarrera = $msValor;
                                        }
                                        else
                                        {
                                            if ($msCarrera == $msValor)
                                                echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
                                            else
                                                echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                        }
                                    }
                                }
                                echo('</select>');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboPlanPosgrado" class="col-sm-auto col-md-2 col-form-label">Plan de estudio</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboPlanPosgrado" name="cboPlanPosgrado" onchange="llenaCurso()">');
                                else
                                    echo('<select class="form-control" id="cboPlanPosgrado" name="cboPlanPosgrado" disabled>');
                                
                                $msConsulta = "Select PLANPOSGRADO_REL, PERIODO_230, ACTIVO_230 from UMO230A where CARRERA_REL = ?";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msCarrera]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = trim($mFila["PLANPOSGRADO_REL"]);
                                    $msTexto = "Período " . trim($mFila["PERIODO_240"]);
                                    $mbActivo = $mFila["ACTIVO_240"];

                                    if ($msPlanPosgrado == "")
                                    {
                                        if ($mbActivo == 1)
                                        {
                                            echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                            $msPlanPosgrado = $msValor;
                                        }
                                    }
                                    else
                                    {
                                        if ($msPlanPosgrado == $msValor)
                                            echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
                                        else
                                            echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                    }
                                }
                                echo('</select>');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboCurso" class="col-sm-auto col-md-2 col-form-label">Curso</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboCurso" name="cboCurso">');
                                else
                                    echo('<select class="form-control" id="cboCurso" name="cboCurso" disabled>');
                                
                                $msConsulta = "Select UMO240A.CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A, UMO231A where UMO231A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL and PLANPOSGRADO_REL = ? order by NOMBRE_240";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msPlanPosgrado]);
                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = rtrim($mFila["CURSOPOSGRADO_REL"]);
                                    $msTexto = rtrim($mFila["NOMBRE_240"]);
                                    if ($msCurso == "")
                                    {
                                        echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                        $msCurso = $msValor;
                                    }
                                    else
                                    {
                                        if ($msCurso == $msValor)
                                            echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
                                        else
                                            echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                    }
                                }
                                echo('</select>');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtCohorte" class="col-sm-12 col-md-2 col-form-label">Cohorte</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="text" class="form-control" id="txtCohorte" name="txtCohorte" value="' . $msCohorte . '" />');
                                else
                                    echo('<input type="text" class="form-control" id="txtCohorte" name="txtCohorte" value="' . $msCohorte . '" disabled />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="optRegimen" class="col-sm-12 col-md-2 col-form-label">Régimen</label>
                        <div class="col-sm-12 col-md-7">
                            <?php
                                if ($msCodigo == "")
                                {
                                    if ($mnRegimen == 1)
                                        echo('<input type="radio" id="optRegimen1" name="optRegimen" value="1" checked /> Mensual');
                                    else
                                        echo('<input type="radio" id="optRegimen1" name="optRegimen" value="1" /> Mensual');

                                    if ($mnRegimen == 2)
                                        echo('&emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="2" checked /> Bimestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="2" /> Bimestral');

                                    if ($mnRegimen == 3)
                                        echo('&emsp;<input type="radio" id="optRegimen3" name="optRegimen" value="3" checked /> Trimestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen3" name="optRegimen" value="3" /> Trimestral');

                                    if ($mnRegimen == 4)
                                        echo('&emsp;<input type="radio" id="optRegimen4" name="optRegimen" value="4" checked /> Cuatrimestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen4" name="optRegimen" value="4" /> Cuatrimestral');

                                    if ($mnRegimen == 5)
                                        echo('&emsp;<input type="radio" id="optRegimen5" name="optRegimen" value="5" checked /> Semestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen5" name="optRegimen" value="5" /> Semestral');

                                    if ($mnRegimen == 6)
                                        echo('&emsp;<input type="radio" id="optRegimen6" name="optRegimen" value="6" checked /> Intensivo');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen6" name="optRegimen" value="6" /> Intensivo');
                                }
                                else
                                {
                                    if ($mnRegimen == 1)
                                        echo('<input type="radio" id="optRegimen1" name="optRegimen" value="1" checked disabled /> Mensual');
                                    else
                                        echo('<input type="radio" id="optRegimen1" name="optRegimen" value="1" disabled /> Mensual');

                                    if ($mnRegimen == 2)
                                        echo('&emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="2" checked disabled /> Bimestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="2" disabled /> Bimestral');

                                    if ($mnRegimen == 3)
                                        echo('&emsp;<input type="radio" id="optRegimen3" name="optRegimen" value="3" checked disabled /> Trimestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen3" name="optRegimen" value="3" disabled /> Trimestral');

                                    if ($mnRegimen == 4)
                                        echo('&emsp;<input type="radio" id="optRegimen4" name="optRegimen" value="4" checked disabled /> Cuatrimestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen4" name="optRegimen" value="4" disabled /> Cuatrimestral');

                                    if ($mnRegimen == 5)
                                        echo('&emsp;<input type="radio" id="optRegimen5" name="optRegimen" value="5" checked disabled /> Semestral');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen5" name="optRegimen" value="5" disabled /> Semestral');

                                    if ($mnRegimen == 6)
                                        echo('&emsp;<input type="radio" id="optRegimen6" name="optRegimen" value="6" disabled checked /> Intensivo');
                                    else
                                        echo('&emsp;<input type="radio" id="optRegimen6" name="optRegimen" value="6" disabled /> Intensivo');
                                }
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                        <div class="col-sm-12 col-md-7">
                            <div class="radio">
                                <?php
                                    if ($msCodigo == "")
                                    {
                                        if ($mnTurno == 1)
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked /> Diurno');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" /> Diurno');

                                        if ($mnTurno == 2)
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" checked /> Matutino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino');

                                        if ($mnTurno == 3)
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" checked /> Vespertino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" /> Vespertino');

                                        if ($mnTurno == 4)
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" checked /> Nocturno');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" /> Nocturno');

                                        if ($mnTurno == 5)
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" checked /> Sabatino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" /> Sabatino');

                                        if ($mnTurno == 6)
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" checked /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" /> Dominical');
                                    }
                                    else
                                    {
                                        if ($mnTurno == 1)
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked disabled /> Diurno');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" disabled /> Diurno');

                                        if ($mnTurno == 2)
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" checked disabled /> Matutino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" disabled /> Matutino');

                                        if ($mnTurno == 3)
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" checked disabled /> Vespertino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" disabled /> Vespertino');

                                        if ($mnTurno == 4)
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" checked disabled /> Nocturno');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" disabled /> Nocturno');

                                        if ($mnTurno == 5)
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" checked disabled /> Sabatino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" disabled /> Sabatino');

                                        if ($mnTurno == 6)
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" disabled checked /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" disabled /> Dominical');
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtMediacion" class="col-sm-12 col-md-2 col-form-label">Mediación pedagógica</label>
                        <div class="col-sm-12 col-md-8">
                            <?php echo('<textarea class="form-control" id="txtMediacion" name="txtMediacion" rows="3" maxlength="300">' . $msMediacion . '</textarea>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtEjesValores" class="col-sm-12 col-md-2 col-form-label">Ejes transversales y valores</label>
                        <div class="col-sm-12 col-md-8">
                            <?php echo('<textarea class="form-control" id="txtEjesValores" name="txtEjesValores" rows="3" maxlength="300">' . $msEjesValores . '</textarea>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObjGral" class="col-sm-12 col-md-8 form-label">Objetivos generales del Curso</label>
                        </div>
                        <div class="col-sm-12 col-md-10">
                            <div id="dvObjGral">
                                <table id="dgObjGral" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObjGral', hidden:'true'">codObjGral</th>
                                            <th data-options="field:'objGral',width:'100%',align:'left'">Objetivo general</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObjGralPos($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUSPOS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBJETIVOGPOS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_291"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObjGral" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObjGral').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObjGral()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObjGral()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObjGral()">Deshacer</a>
                    </div>

                    <div id="dlgObjGral" class="easyui-dialog" title="Objetivo general" data-options="closed:true, modal:true, buttons: '#btnObjGral'" style="top:10%; height:15%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtObjGral" class="col-sm-12 col-md-3 form-label">Objetivo general</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObjGral" name="txtObjGral" rows="5" maxlength="400"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObjGral">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObjGral()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObjGral').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObjMod" class="col-sm-12 col-md-4 form-label">Objetivos por módulo</label>
                        </div>
                        <div class="col-sm-auto col-md-10">
                            <div id="dvObjMod">
                                <table id="dgObjMod">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObjMod', hidden:'true'">codObjMod</th>
                                            <th data-options="field:'modulo',width:'25%',align:'left'">Módulo</th>
                                            <th data-options="field:'ObjMod',width:'75%',align:'left'">Objetivo de módulo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObjModPos($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBJETIVOM_REL"] . "</td>");
                                            echo ("<td>" . $mFila["MODULO_292"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_292"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObjMod" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObjMod').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObjMod()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObjMod()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObjMod()">Deshacer</a>
                    </div>

                    <div id="dlgObjMod" class="easyui-dialog" title="Objetivos por módulos" data-options="closed:true, modal:true, buttons: '#btnObjMod'" style="top:10%; height:20%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtModulo" class="col-sm-12 col-md-3 form-label">Módulo</label>
                            <div class="col-sm-12 col-md-8">
                                <td><input id="txtModulo" name="txtModulo" class="form-control" value="" maxlength="100"></td>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtObjMod" class="col-sm-12 col-md-3 form-label">Objetivo del Módulo</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObjMod" name="txtObjMod" rows="5" maxlength="500"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObjMod">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObjMod()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObjMod').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label class="col-sm-12 col-md-3 form-label">Detalle del syllabus</label>
                        </div>
                        <div class="col-sm-auto col-md-10">
                            <div id="dvDetalle">
                                <table id="dgDetalle" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codDetalle', hidden:'true'">codDetalle</th>
                                            <th data-options="field:'fecha', align:'left'">Fecha</th>
                                            <th data-options="field:'modulo', editor:'text', align:'left'">Módulo</th>
                                            <th data-options="field:'contenido', editor:'text', align:'left'">Contenido</th>
                                            <th data-options="field:'objEsp', editor:'text', align:'left'">Objetivo específico</th>
                                            <th data-options="field:'forma', editor:'text', align:'left'">Mediación pedagógica y ejes</th>
                                            <th data-options="field:'medios', editor:'text', align:'left'">Recursos didácticos</th>
                                            <th data-options="field:'evaluacion', editor:'text', align:'left'">Evaluación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetSyllabusPos($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["DETSYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["FECHA_293"] . "</td>");
                                            echo ("<td>" . $mFila["MODULO_293"] . "</td>");
                                            echo ("<td>" . $mFila["CONTENIDO_293"] . "</td>");
                                            echo ("<td>" . $mFila["OBJETIVOESP_293"] . "</td>");
                                            echo ("<td>" . $mFila["FORMA_293"] . "</td>");
                                            echo ("<td>" . $mFila["MEDIOS_293"] . "</td>");
                                            echo ("<td>" . $mFila["EVALUACION_293"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftDetalle" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgDetalle').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitDetalle()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitDetalle()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectDetalle()">Deshacer</a>
                    </div>

                    <div id="dlgDetalle" class="easyui-dialog" title="Detalle del syllabus" data-options="closed:true, modal:true, buttons: '#btnDetalle'" style="top:10%; height:45%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="dtpFechaDet" class="col-sm-12 col-md-3 form-label">Fecha</label>
                            <div class="col-sm-12 col-md-4">
                                <?php echo('<input type="date" class="form-control" id="dtpFechaDet" name="dtpFechaDet" value="' . date('Y-m-d') . '" />'); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtModuloDet" class="col-sm-12 col-md-3 form-label">Módulo</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtModuloDet" name="txtModuloDet" rows="2" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtContenidoDet" class="col-sm-12 col-md-3 form-label">Contenido o temas</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtContenidoDet" name="txtContenidoDet" rows="2" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtObjEspDet" class="col-sm-12 col-md-3 form-label">Objetivo específico</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObjEspDet" name="txtObjEspDet" rows="2" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtFormaDet" class="col-sm-12 col-md-3 form-label">Mediación pedagógica y ejes</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtFormaDet" name="txtFormaDet" rows="2" maxlength="900"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtMediosDet" class="col-sm-12 col-md-3 form-label">Recursos didácticos</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtMediosDet" name="txtMediosDet" rows="2" maxlength="100"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtEvaluacionDet" class="col-sm-12 col-md-3 form-label">Evaluación</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtEvaluacionDet" name="txtEvaluacionDet" rows="2" maxlength="100"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnDetalle">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarDetalle()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgDetalle').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObsDocente" class="col-sm-12 col-md-8 form-label">Observaciones docentes</label>
                        </div>
                        <div class="col-sm-12 col-md-10">
                            <div id="dvObsDocente">
                                <table id="dgObsDocente" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObsDocente', hidden:'true'">codObsDocente</th>
                                            <th data-options="field:'obsDocente',width:'100%',align:'left'">Observación docente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObsDocentePos($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUSPOS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBSDOCENTEPOS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_294"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObsDocente" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObsDocente').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObsDocente()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObsDocente()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObsDocente()">Deshacer</a>
                    </div>

                    <div id="dlgObsDocente" class="easyui-dialog" title="Observación docente" data-options="closed:true, modal:true, buttons: '#btnObsDocente'" style="top:10%; height:15%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtObsDocente" class="col-sm-12 col-md-3 form-label">Observación docente</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObsDocente" name="txtObsDocente" rows="5" maxlength="300"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObsDocente">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObsDocente()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObsDocente').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObsAcademica" class="col-sm-12 col-md-8 form-label">Observaciones de dirección académica</label>
                        </div>
                        <div class="col-sm-12 col-md-10">
                            <div id="dvObsAcademica">
                                <table id="dgObsAcademica" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObsAcademica', hidden:'true'">codObsAcademica</th>
                                            <th data-options="field:'obsAcademica',width:'100%',align:'left'">Observación dirección académica</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObsAcademicaPos($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUSPOS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBSACADEMICAPOS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_295"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObsAcademica" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObsAcademica').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObsAcademica()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObsAcademica()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObsAcademica()">Deshacer</a>
                    </div>

                    <div id="dlgObsAcademica" class="easyui-dialog" title="Observación dirección académica" data-options="closed:true, modal:true, buttons: '#btnObsAcademica'" style="top:10%; height:17%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtObsAcademica" class="col-sm-12 col-md-3 form-label">Observación dirección académica</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObsAcademica" name="txtObsAcademica" rows="5" maxlength="300"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObsAcademica">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObsAcademica()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObsAcademica').dialog('close')">Cerrar</a>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridSyllabusPosgrado.php';" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
            <?php	}
		}
	}
?>
    

</body>
</html>

<script>
window.onload = function() {
    $('#txtObjGral').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('#txtObjMod').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('#txtObsDocente').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('#txtObsAcademica').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });

    $('#dgObjGral').datagrid({
        striped: true,
        footer: '#ftObjGral',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObjGral
    });

    $('#dgObjMod').datagrid({
        striped: true,
        footer: '#ftObjMod',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObjMod
    });

    $('#dgDetalle').datagrid({
        striped: true,
        footer: '#ftDetalle',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellDetalle
    });

    $('#dgObsDocente').datagrid({
        striped: true,
        footer: '#ftObsDocente',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObsDocente
    });

    $('#dgObsAcademica').datagrid({
        striped: true,
        footer: '#ftObsAcademica',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObsAcademica
    });
}

function verificarFormulario() {
    var regObjGral = $('#dgObjGral').datagrid('getRows').length;
    var regObjMod = $('#dgObjMod').datagrid('getRows').length;
    var regObjDetalle = $('#dgDetalle').datagrid('getRows').length;
    var administrador = <?php echo($mbAdministrador) ?>;

    if (regObjGral == 0) {
        $.messager.alert('UMOJN', 'Faltan los Objetivos generales.', 'warning');
        return false;
    }

    if (regObjMod == 0) {
        $.messager.alert('UMOJN', 'Faltan los Objetivos por módulo.', 'warning');
        return false;
    }

    if (regObjDetalle == 0) {
        $.messager.alert('UMOJN', 'Falta el detalle del syllabus.', 'warning');
        return false;
    }
    return true;
}

function llenaCursos()
{
    var planPosgrado = document.getElementById('cboPlanPosgrado').value;
    var datos = new FormData();
    datos.append('planPosgrado', planPosgrado);

    $.ajax({
        url: 'funciones/fxDatosSyllabusPosgrado.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboCurso').innerHTML = response;
        }
    })
}

function llenaPlanPosgrado()
{
    var carrera = document.getElementById('cboCarrera').value;
    var datos = new FormData();
    datos.append('carreraPE', carrera);

    $.ajax({
        url: 'funciones/fxDatosSyllabus.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            var data = JSON.parse(response)
            document.getElementById('cboPlanPosgrado').innerHTML = data.resultado;
            llenaCursos(data.planEstudio);
        }
    })
}

function agregarObjGral(){
    if ($('#txtObjGral').val() == "")
        $.messager.alert('UMOJN', 'Falta el Objetivo general.', 'warning');
    else
    {
        appendObjGral();
        $.messager.alert('UMOJN', 'Objetivo general agregado.', 'info');
    }
}

function agregarObjMod(){
    if ($('#txtModulo').val() == "")
        $.messager.alert('UMOJN', 'Falta el módulo.', 'warning');
    else{
        if ($('#txtObjMod').val() == "")
            $.messager.alert('UMOJN', 'Falta el Objetivo del módulo.', 'warning');
        else
        {
            appendObjMod();
            $.messager.alert('UMOJN', 'Objetivo del módulo agregado.', 'info');
        }
    }
}

function agregarObsDocente(){
    if ($('#txtObsDocente').val() == "")
        $.messager.alert('UMOJN', 'Falta la observación docente.', 'warning');
    else
    {
        appendObsDocente();
        $.messager.alert('UMOJN', 'Observación docente agregada.', 'info');
    }
}

function agregarObsAcademica(){
    if ($('#txtObsAcademica').val() == "")
        $.messager.alert('UMOJN', 'Falta la observación académica.', 'warning');
    else
    {
        appendObsAcademica();
        $.messager.alert('UMOJN', 'Observación académica agregado.', 'info');
    }
}

function agregarDetalle(){
    if ($('#txtModuloDet').val() == "")
        $.messager.alert('UMOJN', 'Falta la módulo.', 'warning');
    else{
        if ($('#txtContenidoDet').val() == "")
            $.messager.alert('UMOJN', 'Falta el contenido.', 'warning');
        else{
            if ($('#txtObjEspDet').val() == "")
                $.messager.alert('UMOJN', 'Falta el objetivo específico.', 'warning');
            else{
                if ($('#txtFormaDet').val() == "")
                    $.messager.alert('UMOJN', 'Falta la forma de enseñanza.', 'warning');
                else{
                    if ($('#txtMediosDet').val() == "")
                        $.messager.alert('UMOJN', 'Faltan los medios o recursos.', 'warning');
                    else{
                        if ($('#txtEvaluacionDet').val() == "")
                            $.messager.alert('UMOJN', 'Falta la evaluación.', 'warning');
                        else
                            {
                                appendDetalle();
                                $.messager.alert('UMOJN', 'Registro agregado.', 'info');
                            }
                    }
                }
            }
        }
    }
}

var editIndexObjGral = undefined;
var lastIndexObjGral;
var editIndexObjMod = undefined;
var lastIndexObjMod;
var editIndexDetalle = undefined;
var lastIndexDetalle;
var editIndexObsDocente = undefined;
var lastIndexObsDocente;
var editIndexObsAcademica = undefined;
var lastIndexObsAcademica;

/*Grid de Objetivo general*/
$('#dgObjGral').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObjGral != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObjGral);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObjGral = rowIndex;
    }
});

function endEditingObjGral() {
    if (editIndexObjGral == undefined) {
        return true
    }
    if ($('#dgObjGral').datagrid('validateRow', editIndexObjGral)) {
        $('#dgObjGral').datagrid('endEdit', editIndexObjGral);
        editIndexObjGral = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellObjGral(index, field) {
    if (editIndexObjGral != index) {
        if (endEditingObjGral()) {
            $('#dgObjGral').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObjGral = index;
        } else {
            setTimeout(function() {
                $('#dgObjGral').datagrid('selectRow', editIndexObjGral);
            }, 0);
        }
    }
}

function appendObjGral() {
    if (endEditingObjGral()) {
        $('#dgObjGral').datagrid('appendRow', {
            objGral: $('#txtObjGral').val()
        });
        editIndexObjGral = $('#dgObjGral').datagrid('getRows').length;
        $('#dgObjGral').datagrid('selectRow', editIndexObjGral).datagrid('beginEdit', editIndexObjGral);
    }
}

function removeitObjGral() {
    if (editIndexObjGral == undefined) {
        return
    }
    $('#dgObjGral').datagrid('cancelEdit', editIndexObjGral)
        .datagrid('deleteRow', editIndexObjGral);
    editIndexObjGral = undefined;
}

function acceptitObjGral() {
    if (endEditingObjGral()) {
        $('#dgObjGral').datagrid('acceptChanges');
    }
}

function rejectObjGral() {
    $('#dgObjGral').datagrid('rejectChanges');
    editIndexObjGral = undefined;
}

/*Grid de Objetivo por modulo*/

$('#dgObjMod').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObjMod != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObjMod);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObjMod = rowIndex;
    }
});

function endEditingObjMod() {
    if (editIndexObjMod == undefined) {
        return true
    }
    if ($('#dgObjMod').datagrid('validateRow', editIndexObjMod)) {
        $('#dgObjMod').datagrid('endEdit', editIndexObjMod);
        editIndexObjMod = undefined;
        return true;
    } else {
        return false;
    }
}

function appendObjMod() {
    if (endEditingObjMod()) {
        $('#dgObjMod').datagrid('appendRow', {
            modulo: $('#txtModulo').val(),
            ObjMod: $('#txtObjMod').val()
        });
        editIndexObjMod = $('#dgObjMod').datagrid('getRows').length;
        $('#dgObjMod').datagrid('selectRow', editIndexObjMod).datagrid('beginEdit', editIndexObjMod);
    }
}

function onClickCellObjMod(index, field) {
    if (editIndexObjMod != index) {
        if (endEditingObjMod()) {
            $('#dgObjMod').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObjMod = index;
        } else {
            setTimeout(function() {
                $('#dgObjMod').datagrid('selectRow', editIndexObjMod);
            }, 0);
        }
    }
}

function removeitObjMod() {
    if (editIndexObjMod == undefined) {
        return
    }
    $('#dgObjMod').datagrid('cancelEdit', editIndexObjMod)
        .datagrid('deleteRow', editIndexObjMod);
    editIndexObjMod = undefined;
}

function acceptitObjMod() {
    if (endEditingObjMod()) {
        $('#dgObjMod').datagrid('acceptChanges');
    }
}

function rejectObjMod() {
    $('#dgObjMod').datagrid('rejectChanges');
    editIndexObjMod = undefined;
}

/*Grid de Detalle*/
$('#dgDetalle').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexDetalle != rowIndex) {
            $(this).datagrid('endEdit', lastIndexDetalle);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexDetalle = rowIndex;
    }
});

function endEditingDetalle() {
    if (editIndexDetalle == undefined) {
        return true
    }
    if ($('#dgDetalle').datagrid('validateRow', editIndexDetalle)) {
        $('#dgDetalle').datagrid('endEdit', editIndexDetalle);
        editIndexDetalle = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellDetalle(index, field) {
    if (editIndexDetalle != index) {
        if (endEditingDetalle()) {
            $('#dgDetalle').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexDetalle = index;
        } else {
            setTimeout(function() {
                $('#dgDetalle').datagrid('selectRow', editIndexDetalle);
            }, 0);
        }
    }
}

function appendDetalle() {
    if (endEditingDetalle()) {
        $('#dgDetalle').datagrid('appendRow', {
            fecha: $('#dtpFechaDet').val(),
            modulo: $('#txtModuloDet').val(),
            contenido: $('#txtContenidoDet').val(),
            objEsp: $('#txtObjEspDet').val(),
            forma: $('#txtFormaDet').val(),
            medios: $('#txtMediosDet').val(),
            evaluacion: $('#txtEvaluacionDet').val()
        });
        editIndexDetalle = $('#dgDetalle').datagrid('getRows').length;
        $('#dgDetalle').datagrid('selectRow', editIndexDetalle).datagrid('beginEdit', editIndexDetalle);
    }
}

function removeitDetalle() {
    if (editIndexDetalle == undefined) {
        return
    }
    $('#dgDetalle').datagrid('cancelEdit', editIndexDetalle)
        .datagrid('deleteRow', editIndexDetalle);
    editIndexDetalle = undefined;
}

function acceptitDetalle() {
    if (endEditingDetalle()) {
        $('#dgDetalle').datagrid('acceptChanges');
    }
}

function rejectDetalle() {
    $('#dgDetalle').datagrid('rejectChanges');
    editIndexDetalle = undefined;
}

/*Grid de Observación docente*/
$('#dgObsDocente').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObsDocente != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObsDocente);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObsDocente = rowIndex;
    }
});

function endEditingObsDocente() {
    if (editIndexObsDocente == undefined) {
        return true
    }
    if ($('#dgObsDocente').datagrid('validateRow', editIndexObsDocente)) {
        $('#dgObsDocente').datagrid('endEdit', editIndexObsDocente);
        editIndexObsDocente = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellObsDocente(index, field) {
    if (editIndexObsDocente != index) {
        if (endEditingObsDocente()) {
            $('#dgObsDocente').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObsDocente = index;
        } else {
            setTimeout(function() {
                $('#dgObsDocente').datagrid('selectRow', editIndexObsDocente);
            }, 0);
        }
    }
}

function appendObsDocente() {
    if (endEditingObsDocente()) {
        $('#dgObsDocente').datagrid('appendRow', {
            obsDocente: $('#txtObsDocente').val()
        });
        editIndexObsDocente = $('#dgObsDocente').datagrid('getRows').length;
        $('#dgObsDocente').datagrid('selectRow', editIndexObsDocente).datagrid('beginEdit', editIndexObsDocente);
    }
}

function removeitObsDocente() {
    if (editIndexObsDocente == undefined) {
        return
    }
    $('#dgObsDocente').datagrid('cancelEdit', editIndexObsDocente)
        .datagrid('deleteRow', editIndexObsDocente);
    editIndexObsDocente = undefined;
}

function acceptitObsDocente() {
    if (endEditingObsDocente()) {
        $('#dgObsDocente').datagrid('acceptChanges');
    }
}

function rejectObsDocente() {
    $('#dgObsDocente').datagrid('rejectChanges');
    editIndexObsDocente = undefined;
}

/*Grid de Observación dirección académica*/
$('#dgObsAcademica').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObsAcademica != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObsAcademica);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObsAcademica = rowIndex;
    }
});

function endEditingObsAcademica() {
    if (editIndexObsAcademica == undefined) {
        return true
    }
    if ($('#dgObsAcademica').datagrid('validateRow', editIndexObsAcademica)) {
        $('#dgObsAcademica').datagrid('endEdit', editIndexObsAcademica);
        editIndexObsAcademica = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellObsAcademica(index, field) {
    if (editIndexObsAcademica != index) {
        if (endEditingObsAcademica()) {
            $('#dgObsAcademica').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObsAcademica = index;
        } else {
            setTimeout(function() {
                $('#dgObsAcademica').datagrid('selectRow', editIndexObsAcademica);
            }, 0);
        }
    }
}

function appendObsAcademica() {
    if (endEditingObsAcademica()) {
        $('#dgObsAcademica').datagrid('appendRow', {
            obsAcademica: $('#txtObsAcademica').val()
        });
        editIndexObsAcademica = $('#dgObsAcademica').datagrid('getRows').length;
        $('#dgObsAcademica').datagrid('selectRow', editIndexObsAcademica).datagrid('beginEdit', editIndexObsAcademica);
    }
}

function removeitObsAcademica() {
    if (editIndexObsAcademica == undefined) {
        return
    }
    $('#dgObsAcademica').datagrid('cancelEdit', editIndexObsAcademica)
        .datagrid('deleteRow', editIndexObsAcademica);
    editIndexObsAcademica = undefined;
}

function acceptitObsAcademica() {
    if (endEditingObsAcademica()) {
        $('#dgObsAcademica').datagrid('acceptChanges');
    }
}

function rejectObsAcademica() {
    $('#dgObsAcademica').datagrid('rejectChanges');
    editIndexObsAcademica = undefined;
}

$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario()) {
        var texto;
        var datos;
        var registros;
        var i;
        var gridObjGral = $('#dgObjGral').datagrid('getData');
        var gridObjMod = $('#dgObjMod').datagrid('getData');
        var gridDetalle = $('#dgDetalle').datagrid('getData');

        texto = '{"txtCodSyllabus":"' + document.getElementById("txtCodSyllabus").value + '", ';
        if (document.getElementById("txtCodSyllabus").value == "")
            texto += '"Operacion":"0", ';
        else
            texto += '"Operacion":"1", ';
        texto += '"dtpFecha":"' + document.getElementById("dtpFecha").value + '", ';
        texto += '"cboDocente":"' + document.getElementById("cboDocente").value + '", ';
        texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
        texto += '"cboPlanPosgrado":"' + document.getElementById("cboPlanPosgrado").value + '", ';
        texto += '"cboCurso":"' + document.getElementById("cboCurso").value + '", ';
        texto += '"txtGrupo":"' + document.getElementById("txtGrupo").value + '", ';
        texto += '"txtCohorte":"' + document.getElementById("txtCohorte").value + '", ';
        texto += '"optRegimen":"' + document.getElementById("optRegimen").value + '", ';
        
        if (document.getElementById("optTurno1").checked)
            texto += '"optTurno":"1", ';
        if (document.getElementById("optTurno2").checked)
            texto += '"optTurno":"2", ';
        if (document.getElementById("optTurno3").checked)
            texto += '"optTurno":"3", ';
        if (document.getElementById("optTurno4").checked)
            texto += '"optTurno":"4", ';
        if (document.getElementById("optTurno5").checked)
            texto += '"optTurno":"5", ';
        if (document.getElementById("optTurno6").checked)
            texto += '"optTurno":"6", ';

        texto += '"txtMediacion":"' + document.getElementById("txtMediacion").value + '", ';
        texto += '"txtEjesValores":"' + document.getElementById("txtEjesValores").value + '", ';

        registros = $('#dgObjGral').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridObjGral": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"objGral":"' + gridObjGral.rows[i].objGral;
                if (i == registros)
                    texto += '"}],';
                else
                    texto += '"},';
            }
        }

        registros = $('#dgObjMod').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridObjMod": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"modulo":"' + gridObjMod.rows[i].modulo + '","ObjMod":"' + gridObjMod.rows[i].ObjMod;
                if (i == registros)
                    texto += '"}],';
                else
                    texto += '"},';
            }
        }

        registros = $('#dgDetalle').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridDetalle": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"fecha":"' + gridDetalle.rows[i].fecha + '","modulo":"' + gridDetalle.rows[i].modulo + '","contenido":"' + gridDetalle.rows[i].contenido + '","objEsp":"' + gridDetalle.rows[i].objEsp + '","forma":"' + gridDetalle.rows[i].forma + '","medios":"' + gridDetalle.rows[i].medios + '","evaluacion":"' + gridDetalle.rows[i].evaluacion;
                if (i == registros)
                    texto += '"}]}';
                else
                    texto += '"},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
                url: 'procSyllabus.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridSyllabus.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
})
</script>