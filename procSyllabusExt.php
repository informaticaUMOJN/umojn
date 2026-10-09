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
    require_once ("funciones/fxSyllabus.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("procSyllabus");
		
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
				$msCodigo = $_POST["txtCodSyllabus"];
                if ($msCodigo == "")
                {
                    $msPlanEstudio = $_POST["cboPlanEstudio"];
                    $msDocente = $_POST["cboDocente"];
                    $msAsignatura = $_POST["cboAsignatura"];
                    $mdFecha = $_POST["dtpFecha"];
                    $mnTurno = $_POST["optTurno"];
                    $mnAnno = $_POST["txnAnno"];
                    $mnSemestre = $_POST["txnSemestre"];
                    $msGrupo = $_POST["txtGrupo"];
                }
                $msMediacion = "";
                $msEjesValores = "";
                $mbExterno = 1;

                if ($msCodigo == "")
                {
                    $msCodigo = fxGuardarSyllabus($msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno, $msGrupo, $msMediacion, $msEjesValores, $mbExterno);
                    $msBitacora = $msCodigo . "; " . $msPlanEstudio . "; " . $msDocente . "; " . $msAsignatura . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnSemestre . "; " . $mnTurno . "; " . $msGrupo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO070A", $msCodigo, "", "Agregar", $msBitacora);
                }

                if (is_array($_FILES) && count($_FILES) > 0) 
                {
                    $msArchivo = $_FILES['archivo']['name'];
                    $msRuta = 'syllabus/'.$msCodigo."/".$_FILES['archivo']['name'];
                    $miCarpeta = 'syllabus/'.$msCodigo;
                    if (!file_exists($miCarpeta)) {
                        mkdir($miCarpeta, 0777, true);
                    }
                    else
                    {
                        $mArchivos = glob($miCarpeta . '/*');
                        foreach ($mArchivos as $archivo) 
                        {
                            if (unlink($archivo))
                            {
                                fxBorrarDetExterno ($msCodigo);
                            }
                        }
                    }

                    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $miCarpeta. "/" . $msArchivo))
                    {
                        fxGuardarDetExterno ($msCodigo, 1, $msRuta);
                    }
                }
                
				?><meta http-equiv="Refresh" content="0;url=gridSyllabus.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = $_POST["UMOJN"];
                else
                    $msCodigo = "";

				$mRecordSet = fxDevuelveSyllabus (0, "", $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msPlanEstudio = $mFila["PLANESTUDIO_REL"];
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msAsignatura = $mFila["ASIGNATURA_REL"];
                    $mdFecha = $mFila["FECHA_070"];
                    $mnAnno = $mFila["ANNO_070"];
                    $mnSemestre = $mFila["SEMESTRE_070"];
                    $mnTurno = $mFila["TURNO_070"];
                    $msGrupo = $mFila["GRUPO_070"];
                    $msMediacion = $mFila["RECOMENDACIONES_070"];
                    $msEjesValores = $mFila["EJESVALORES_070"];
                    $mbExterno = $mFila["EXTERNO_070"];

                    $mAuxiliar = fxObtenerDetExterno($msCodigo);
                    $mRow = $mAuxiliar->fetch();
                    $msRuta = $mRow["RUTA_076"];
                }
                else
                {
                    $msPlanEstudio = "";
                    $msDocente = "";
                    $msCarrera = "";
                    $msAsignatura = "";
                    $mdFecha = date('Y-m-d');
                    $mnAnno = date('Y');
                    if (intval(date('m'))<= 6)
                        $mnSemestre = 1;
                    else
                        $mnSemestre = 2;
                    $mnTurno = 1;
                    $msGrupo = "";
                    $msMediacion = "";
                    $msEjesValores = "";
                    $mbExterno = 1;
                    $msRuta = "";
                }
	?>

<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
			<div class="col-xs-12 col-md-11">
				<div class="degradado"><strong>Syllabus</strong></div>
			</div>
		</div>

        <div class="row">
            <div class="col-xs-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procSyllabus" name="procSyllabus" action="procSyllabusExt.php" enctype="multipart/form-data" onsubmit="return verificarFormulario()" method="post">
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
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaPlanEstudio()">');
                                else
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" disabled>');

                                if ($msAsignatura != ""){
                                    $msConsulta = "select CARRERA_REL from UMO060A where ASIGNATURA_REL = ?";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute([$msAsignatura]);
                                    $mFila = $mDatos->fetch();
                                    $msCarrera = $mFila["CARRERA_REL"];
                                }

                                $mDatos = fxDevuelveCarrera(1);
                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = rtrim($mFila["CARRERA_REL"]);
                                    $msTexto = rtrim($mFila["NOMBRE_040"]);
                                    $mbPosgrado = $mFila["POSGRADO_040"];

                                    if ($mbPosgrado == 0)
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
                        <label for="cboPlanEstudio" class="col-sm-auto col-md-2 col-form-label">Plan de estudio</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboPlanEstudio" name="cboPlanEstudio" onchange="llenaAsignaturas()">');
                                else
                                    echo('<select class="form-control" id="cboPlanEstudio" name="cboPlanEstudio" disabled>');
                                
                                $msConsulta = "Select PLANESTUDIO_REL, PERIODO_050, ACTIVO_050 from UMO050A where CARRERA_REL = ?";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msCarrera]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = trim($mFila["PLANESTUDIO_REL"]);
                                    $msTexto = "Período " . trim($mFila["PERIODO_050"]);
                                    $mbActivo = $mFila["ACTIVO_050"];

                                    if ($msPlanEstudio == "")
                                    {
                                        if ($mbActivo == 1)
                                        {
                                            echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                            $msPlanEstudio = $msValor;
                                        }
                                    }
                                    else
                                    {
                                        if ($msPlanEstudio == $msValor)
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
                        <label for="cboAsignatura" class="col-sm-auto col-md-2 col-form-label">Asignatura</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura">');
                                else
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" disabled>');
                                
                                $msConsulta = "Select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO051A where UMO051A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and PLANESTUDIO_REL = ? order by NOMBRE_060";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msPlanEstudio]);
                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = rtrim($mFila["ASIGNATURA_REL"]);
                                    $msTexto = rtrim($mFila["NOMBRE_060"]);
                                    if ($msAsignatura == "")
                                    {
                                        echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                        $msAsignatura = $msValor;
                                    }
                                    else
                                    {
                                        if ($msAsignatura == $msValor)
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
                        <label for="txtGrupo" class="col-sm-12 col-md-2 col-form-label">Grupo</label>
                        <div class="col-sm-12 col-md-3">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="text" class="form-control" id="txtGrupo" name="txtGrupo" value="' . $msGrupo . '" />');
                                else
                                    echo('<input type="text" class="form-control" id="txtGrupo" name="txtGrupo" value="' . $msGrupo . '" disabled />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" disabled />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" disabled />');
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
                        <label class="col-sm-auto col-md-2 form-label">Subir archivo</label>
                        <div class="col-sm-12 col-md-6">
                            <input id="txtRutaLocal" class="form-control" readonly>
                        </div>
                        <div class="col-sm-12 col-md-2">
                            <label for="archivo" style="padding:0.5%" data-toggle="tooltip" data-placement="top" title="Agregar o reemplazar archivo">
                            <img src="imagenes/imageAdd.png" height="100%" style="cursor:pointer" /></label>

							<input type="file" accept="application/pdf" id="archivo" name="archivo" style="display:none" onchange="llenaArchivo()" />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div id="dvPDF" class="col-sm-12 col-md-10">
                            <?php echo('<embed src="' . $msRuta . '" type="application/pdf" style="width: 100%; height: 80vh;" />'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none offset-md-2 col-md-8">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridSyllabus.php';" />
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
function verificarFormulario() {
    var semestre = $('#txnSemestre').val();

    if (semestre < 1 || semestre > 2) {
        $.messager.alert('UMOJN', 'El valor del semestre sólo puede ser 1 ó 2.', 'warning');
        return false;
    }

    if ($('#txtRutaLocal').val() == '') 
    {
        $.messager.alert('UMOJN', 'Falta el archivo de la imagen.', 'warning');
        return false;
    }

    return true;
}

function llenaArchivo() {
    $('#txtRutaLocal').val($('#archivo')[0].files[0].name);
}

function borrarArchivo() 
{
    var datos = new FormData();
    var syllabus = $('#txtCodSyllabus').val();
    datos.append('codSyllabus', syllabus);
    
    $.ajax({
        url: 'funciones/fxSyllabusDoc.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response != 0) {
                document.getElementById('dvPDF').innerHTML = '';
            } else {
                $.messager.alert('UMOJN', 'Error en la eliminación del archivo.', 'warning');
            }
        }
    });
}

function llenaAsignaturas()
{
    var planEstudio = document.getElementById('cboPlanEstudio').value;
    var datos = new FormData();
    datos.append('planEstudio', planEstudio);

    $.ajax({
        url: 'funciones/fxDatosSyllabus.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
        }
    })
}

function llenaPlanEstudio()
{
    var syllabus = document.getElementById('txtCodSyllabus').value;
    var carrera = document.getElementById('cboCarrera').value;
    var datos = new FormData();
    datos.append('carreraPE', carrera);
    datos.append('syllabusPE', syllabus);

    $.ajax({
        url: 'funciones/fxDatosSyllabus.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            var data = JSON.parse(response)
            document.getElementById('cboPlanEstudio').innerHTML = data.resultado;
            llenaAsignaturas(data.planEstudio);
        }
    })
}
</script>