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
	require_once ("funciones/fxAsistenciasPosgrado.php");
    $m_cnx_MySQL = fxAbrirConexion();
    $Registro = fxVerificaUsuario();

    if (isset($_SESSION["gsDocente"]))
        $msDocente = $_SESSION["gsDocente"];
    else
        $msDocente = "";
	
	if ($Registro == 0)
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
		$mbPermisoUsuario = fxPermisoUsuario("procAsistenciaPos");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
		{?>
<div class="container text-center">
    <div id="DivContenido">
        <img src="imagenes/errordeacceso.png" />
    </div>
</div>
<?php }
		else
		{
			if (isset($_POST["Operacion"]))
			{
				$mnOperacion = $_POST["Operacion"];
				$msCodigo = $_POST["txtCodAsistencia"];
                $msDocente = $_POST["cboDocente"];
                $msCurso = $_POST["cboCurso"];
                $msCarrera = $_POST["cboCarrera"];
                $mdFecha = $_POST["dtpFechaClase"];
                $mnTurno = $_POST["optTurno"];
                $mnRegimen = $_POST["optRegimen"];
                $msCohorte = $_POST["txtCohorte"];
				$gridEstudiantes = $_POST["gridEstudiantes"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarAsistenciaPos ($msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen);
                    $msBitacora = $msCodigo . "; " . $msCurso . "; " . $mdFecha . "; " . $msCohorte . "; " . $mnTurno . "; " . $mnRegimen;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO300A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarAsistencia ($msCodigo, $msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen);
                    fxBorrarDetAsistencia($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msCurso . "; " . $mdFecha . "; " . $msCohorte . "; " . $mnTurno . "; " . $mnRegimen;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO300A", $msCodigo, "", "Modificar", $msBitacora);
                }
                
				foreach($gridEstudiantes as $mRegistro)
				{
                    $mMatricula = $mRegistro['matricula'];
                    $mEstado = $mRegistro['estado'];
                    switch ($mEstado)
                    {
                        case "Presente":
                            $mnEstado = 0;
                            break;

                        case "Ausente":
                            $mnEstado = 1;
                            break;

                        default:
                            $mnEstado = 2;
                    }
                    fxGuardarDetAsistencia ($msCodigo, $mMatricula, $mnEstado);
                    $itemId++;
				}

				?><meta http-equiv="Refresh" content="0;url=gridAsistenciasPosgrado.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";

				$mRecordSet = fxDevuelveAsistenciaPos (0, "", $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msCarrera = $mFila["CARRERA_REL"];
                    $msCurso = $mFila["CURSO_REL"];
                    $mdFechaClase = $mFila["FECHA_300"];
                    $mnTurno = $mFila["TURNO_300"];
                    $mnRegimen = $mFila["REGIMEN_300"];
                    $msCohorte = $mFila["COHORTE_300"];
                }
                else 
                {
                    $msDocente = "";
                    $msCarrera = "";
                    $msCurso = "";
                    $mdFechaClase = date("Y-m-d");
                    $mnTurno = 1;
                    $mnRegimen = 1;
                    $msCohorte = "";
                }
	?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Asistencia posgrado</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procAsistencia" name="procAsistencia">
                    <div class="form-group row">
                        <label for="txtCodAsistencia" class="col-sm-12 col-md-2 col-form-label">Código de Asistencia</label>
                        <div class="col-sm-12 col-md-2">
                            <?php echo('<input type="text" class="form-control" id="txtCodAsistencia" name="txtCodAsistencia" value="' . $msCodigo . '" readonly />'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFechaClase" class="col-sm-12 col-md-2 col-form-label">Fecha de la clase</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="date" class="form-control" id="dtpFechaClase" name="dtpFechaClase" value="' . $mdFechaClase . '" onchange="verificaAsistencia()" />');
                                else
                                    echo('<input type="date" class="form-control" id="dtpFechaClase" name="dtpFechaClase" value="' . $mdFechaClase . '" readonly />');
                                echo('<input type="hidden" class="form-control" id="txnExisteAsistencia" name="txnExisteAsistencia" value="0" />');
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente" onchange="llenaCursos()">');
                                    
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
                        <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCarrera == "")
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaCursos()">');
                                else
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" disabled>');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 1";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["CARRERA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_040"]);

                                    if ($msCarrera == "")
                                    {
                                        $msCarrera = $mValor;
                                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                    }
                                    else
                                    {
                                        if ($msCarrera == $mValor)
                                            echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        else
                                            echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                    }
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboCurso" class="col-sm-12 col-md-2 col-form-label">Curso</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboCurso" name="cboCurso" onchange="llenaEstudiantes()">');
                                else
                                    echo('<select class="form-control" id="cboCurso" name="cboCurso" disabled>');
                                    
                                $msConsulta = "select distinct UMO240A.CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A, UMO260A, UMO290A where ";
                                $msConsulta .= "UMO240A.CURSOPOSGRADO_REL = UMO260A.CURSOPOSGRADO_REL and UMO240A.CURSOPOSGRADO_REL = UMO290A.CURSOPOSGRADO_REL and ";
                                $msConsulta .= "UMO290A.DOCENTE_REL = ? and COHORTE_290 = ? and ACTIVO_290 = ? order by NOMBRE_240";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msDocente, $msCohorte, 1]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["CURSO_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_240"]);

                                    if ($msCurso == "")
                                    {
                                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        $msCurso = $mValor;
                                    }
                                    else
                                    {
                                        if ($msCurso == $mValor)
                                            echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        else
                                            echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                    }
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtCohorte" class="col-sm-12 col-md-2 col-form-label">Cohorte</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="text" class="form-control" id="txtCohorte" name="txtCohorte" value="' . $msCohorte . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="text" class="form-control" id="txtCohorte" name="txtCohorte" value="' . $msCohorte . '" readonly />');
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
                                        echo('<input type="radio" id="optRegimen1" name="optRegimen" value="1" checked/> Mensual');
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
                                        echo('<input type="radio" id="optRegimen1" name="optRegimen" value="1" disabled /> Memsual');

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
                                        echo('&emsp;<input type="radio" id="optRegimen6" name="optRegimen" value="6" checked disabled /> Intensivo');
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
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked/> Diurno');
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
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" checked disabled /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" disabled /> Dominical');
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dgEST" class="col-sm-12 col-md-2 form-label">Estudiantes</label>
                        <div class="col-sm-12 col-md-8">
                            <div id="dvEST">
                                <table id="dgEST" class="easyui-datagrid table", data-options="iconCls:'icon-edit', toolbar:'#tbEST', singleSelect:true, method:'get', onClickCell: onClickCellEST">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'matricula', hidden:'true'">Matrícula</th>
                                            <th data-options="field:'estudiante', width:'60%', align:'left'">Estudiante</th>
                                            <th data-options="field:'estado', width:'20%', align:'center',
                                                editor: {type:'combobox',
                                                options:{panelHeight:'auto', data:[{value:'Presente',text:'Presente'}, {value:'Ausente',text:'Ausente'}, {value:'Justificado',text:'Justificado'}]}}">Asistencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxDevuelveDetAsistenciaPos($msCodigo, $msCurso, $msCohorte);

                                        while ($mFila = $mDatos->fetch())
                                        {
                                            $msEstudiante = trim($mFila["APELLIDO1_250"]);
                                            if (trim($mFila["APELLIDO2_250"]) != "")
                                                $msEstudiante .= " " . $mFila["APELLIDO2_250"];

                                            $msEstudiante .= ", " . $mFila["NOMBRE1_250"];

                                            if (trim($mFila["NOMBRE2_250"]) != "")
                                                $msEstudiante .= " " . $mFila["NOMBRE2_250"];
                                                
                                            echo('<tr>');
                                            echo('<td>' . rtrim($mFila['MATRICULAPOS_REL']) . '</td>');
                                            echo('<td>' . rtrim($msEstudiante) . '</td>');
                                            switch ($mFila['ESTADO_301'])
                                            {
                                                case 0:
                                                    echo('<td>Presente</td>');
                                                    break;

                                                case 1:
                                                    echo('<td>Ausente</td>');
                                                    break;

                                                default:
                                                    echo('<td>Justificado</td>');
                                            }
                                            echo('</tr>');
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tbEST" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" onclick="acceptitEST()">Salir del Modo de Edición</a>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                        <?php
                            $mdFechaHoy = date('Y-m-d');
                            $HoraHoy = date('H:i:s');

                            if ($mbAdministrador == 1 or $_SESSION["gsDocente"] == "" or $mdFechaClase == $mdFechaHoy)
                                echo('<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />');
                            else
                                echo('<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" disabled />');
                        ?>
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAsistenciasPosgrado.php';" />
                        </div>
                    </div>
                </form>
            </div>
            <?php	}
		}
	}
?>
        </div>
    </div>
</div>
</body>
</html>

<script>
window.onload = function() 
{
    llenaCursos();
    verificaAsistencia();

    if ($('#txtCodAsistencia').val() == "")
    {
        var curso = $('#cboCurso').val();
        llenaEstudiantes();
    }
}

function verificarFormulario() {
    var gridEstudiantes = $('#dgEST').datagrid('getData');
    var registros = $('#dgEST').datagrid('getRows').length - 1;

    if (document.getElementById('txnExisteAsistencia').value == 1 && document.getElementById('txtCodAsistencia').value == "")
    {
        $.messager.alert('UMOJN', 'La asistencia de este curso en esta fecha ya fue ingresada', 'warning');
        return false;
    }
    
    if (registros < 0)
    {
        $.messager.alert('UMOJN', 'Faltan los Estudiantes', 'warning');
        return false;
    }

    for (i = 0; i <= registros; i++) {
        if (gridEstudiantes.rows[i].estado == "")
        {
            $.messager.alert('UMOJN', 'Falta la asistencia de ' + gridEstudiantes.rows[i].estudiante, 'warning');
            return false;
        }
    }

    return true;
}

function llenaCursos()
{
    var docente = $('#cboDocente').val();
    var cohorte = $("#txtCohorte").val();
    var datos = new FormData();
    datos.append('docenteCur', docente);
    datos.append('cohorteCur', cohorte);

    $.ajax({
        url: 'funciones/fxDatosAsistenciaPos.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboCurso').innerHTML = response;
            llenaEstudiantes();
        }
    })
}

function llenaEstudiantes()
{
    var asistencia = document.getElementById('txtCodAsistencia').value;
    var datos = new FormData();
    var curso = document.getElementById('cboCurso').value;
    var cohorte = document.getElementById('txtCohorte').value;

    if (asistencia == "")
    {
        datos.append('curso', curso);
        datos.append('cohorte', cohorte);

        $.ajax({
            url: 'funciones/fxDatosAsistenciaPos.php',
            type: 'post',
            data: datos,
            contentType: false,
            processData: false,
            success: function(response){
                datos = JSON.parse(response);
                $('#dgEST').datagrid({data: datos});
                $('#dgEST').datagrid('reload');
                verificaAsistencia();
            }
        })
    }
}

function verificaAsistencia()
{
    var datos = new FormData();
    var curso = document.getElementById('cboCurso').value;
    var cohorte = document.getElementById('txtCohorte').value;
    var fecha = document.getElementById('dtpFechaClase').value;
    datos.append('curso2', asignatura);
    datos.append('cohorte2', cohorte);
    datos.append('fecha', fecha);

    $.ajax({
        url: 'funciones/fxDatosAsistenciaPos.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('txnExisteAsistencia').value = response;
        }
    })
}

/*Grid de Estudiantes*/
var editIndexEST = undefined;
var lastIndexEST;

$('#dgEST').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexEST != rowIndex) {
            $(this).datagrid('endEdit', lastIndexEST);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexEST = rowIndex;
    }
});

function endEditingEST() {
    if (editIndexEST == undefined) {
        return true
    }
    if ($('#dgEST').datagrid('validateRow', editIndexEST)) {
        $('#dgEST').datagrid('endEdit', editIndexEST);
        editIndexEST = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellEST(index, field) {
    if (editIndexEST != index) {
        if (endEditingEST()) {
            $('#dgEST').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
            editIndexEST = index;
        } else {
            setTimeout(function() {
                $('#dgEST').datagrid('selectRow', editIndexEST);
            }, 0);
        }
    }
}

function acceptitEST() {
    if (endEditingEST()) {
        $('#dgEST').datagrid('acceptChanges');
    }
}

$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario()) {
        var texto;
        var datos;
        var registros;
        var i;
        var gridEstudiantes = $('#dgEST').datagrid('getData');

        texto = '{"txtCodAsistencia":"' + document.getElementById("txtCodAsistencia").value + '", ';
        if (document.getElementById("txtCodAsistencia").value == "")
            texto += '"Operacion":"0", ';
        else
            texto += '"Operacion":"1", ';
        texto += '"cboDocente":"' + document.getElementById("cboDocente").value + '", ';
        texto += '"cboCurso":"' + document.getElementById("cboCurso").value + '", ';
        texto += '"dtpFechaClase":"' + document.getElementById("dtpFechaClase").value + '", ';
        texto += '"txtCohorte":"' + document.getElementById("txtCohorte").value + '", ';

        if (document.getElementById("optRegimen1").checked)
            texto += '"optRegimen":"1", ';
        if (document.getElementById("optRegimen2").checked)
            texto += '"optRegimen":"2", ';
        if (document.getElementById("optRegimen3").checked)
            texto += '"optRegimen":"3", ';
        if (document.getElementById("optRegimen4").checked)
            texto += '"optRegimen":"4", ';
        if (document.getElementById("optRegimen5").checked)
            texto += '"optRegimen":"5", ';
        if (document.getElementById("optRegimen6").checked)
            texto += '"optRegimen":"6", ';

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

        registros = $('#dgEST').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridEstudiantes": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"matricula":"' + gridEstudiantes.rows[i].matricula;
                texto += '","estudiante":"' + gridEstudiantes.rows[i].estudiante;
                texto += '","estado":"' + gridEstudiantes.rows[i].estado;
                if (i == registros)
                    texto += '"}]}';
                else
                    texto += '"},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
                url: 'procAsistenciaPos.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridAsistenciasPosgrado.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
});
</script>