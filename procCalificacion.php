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
	require_once ("funciones/fxCalificaciones.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$Registro = fxVerificaUsuario();
	
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
		$mbPermisoUsuario = fxPermisoUsuario("procCalificacion");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
		{
        ?>
            <div class="container text-center">
                <div id="DivContenido">
                    <img src="imagenes/errordeacceso.png" />
                </div>
            </div>
<?php   }
		else
		{
			if (isset($_POST["Operacion"]))
			{
				$mnOperacion = $_POST["Operacion"];
				$msCodigo = $_POST["txtCodCalificacion"];
                $msDocente = $_POST["cboDocente"];
                $msAsignatura = $_POST["cboAsignatura"];
                $msCarrera = $_POST["cboCarrera"];
                $mdFecha = $_POST["dtpFecha"];
                $mnAnno = $_POST["txnAnno"];
                $mnSemestre = $_POST["txnSemestre"];
                $mnParcial = $_POST["cboParcial"];
                $mnTurno = $_POST["optTurno"];
				$gridEstudiantes = $_POST["gridEstudiantes"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarCalificacion($msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnAnno, $mnSemestre, $mnParcial, $mnTurno);
                    $msBitacora = $msCodigo . "; " . $msDocente . "; " . $msAsignatura . "; " . $msCarrera . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnSemestre . "; " . $mnParcial . "; " . $mnTurno;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO160A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarCalificacion($msCodigo, $msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnAnno, $mnSemestre, $mnParcial, $mnTurno);
                    fxBorrarDetCalificacion($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msDocente . "; " . $msAsignatura . "; " . $msCarrera . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnSemestre . "; " . $mnParcial . "; " . $mnTurno;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO160A", $msCodigo, "", "Modificar", $msBitacora);
                }
                
				foreach($gridEstudiantes as $mRegistro)
				{
                    $mMatricula = $mRegistro['matricula'];
                    $mNota = $mRegistro['nota'];
                    fxGuardarDetCalificacion($msCodigo, $mMatricula, $mNota);
				}

				?><meta http-equiv="Refresh" content="0;url=gridCalificaciones.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";

				$mRecordSet = fxDevuelveCalificacion(0, "", $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msCarrera = $mFila["CARRERA_REL"];
                    $msAsignatura = $mFila["ASIGNATURA_REL"];
                    $mdFecha = $mFila["FECHA_160"];
                    $mnAnno = $mFila["ANNO_160"];
                    $mnSemestre = $mFila["SEMESTRE_160"];
                    $mnParcial = $mFila["PARCIAL_160"];
                    $mnTurno = $mFila["TURNO_160"];
                }
                else 
                {
                    $msDocente = "";
                    $msCarrera = "";
                    $msAsignatura = "";
                    $mdFecha = date("Y-m-d");
                    $mnAnno = date("Y");
                    if (intval(date("m")) <= 6)
                        $mnSemestre = 1;
                    else
                        $mnSemestre = 2;
                    $mnParcial = 0;
                    $mnTurno = 1;
                }
	?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Calificaciones</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procCalificacion" name="procCalificacion">
                    <div class="form-group row">
                        <label for="txtCodCalificacion" class="col-sm-12 col-md-2 col-form-label">Código de Calificación</label>
                        <div class="col-sm-12 col-md-2">
                            <?php echo('<input type="text" class="form-control" id="txtCodCalificacion" name="txtCodCalificacion" value="' . $msCodigo . '" readonly />'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente" onchange="llenaAsignaturas()">');
                                    
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
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaAsignaturas()">');
                                else
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" disabled>');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 0";
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
                        <label for="cboAsignatura" class="col-sm-12 col-md-2 col-form-label">Asignatura</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" onchange="llenaEstudiantes()">');
                                else
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" disabled>');
                                    
                                $msConsulta = "select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO070A where UMO060A.ASIGNATURA_REL = UMO070A.ASIGNATURA_REL and CARRERA_REL = ? and DOCENTE_REL = ? and ACTIVO_070 = ? order by NOMBRE_060";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msCarrera, $msDocente, 1]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["ASIGNATURA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_060"]);

                                    if ($msAsignatura == "")
                                    {
                                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        $msAsignatura = $mValor;
                                    }
                                    else
                                    {
                                        if ($msAsignatura == $mValor)
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
                        <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" readonly />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" readonly />');
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
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" onclick="llenaEstudiantes()" checked/> Diurno');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" onclick="llenaEstudiantes()" /> Diurno');

                                        if ($mnTurno == 2)
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" onclick="llenaEstudiantes()" checked /> Matutino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" onclick="llenaEstudiantes()" /> Matutino');

                                        if ($mnTurno == 3)
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" onclick="llenaEstudiantes()" checked /> Vespertino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" onclick="llenaEstudiantes()" /> Vespertino');

                                        if ($mnTurno == 4)
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" onclick="llenaEstudiantes()" checked /> Nocturno');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" onclick="llenaEstudiantes()" /> Nocturno');

                                        if ($mnTurno == 5)
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" onclick="llenaEstudiantes()" checked /> Sabatino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" onclick="llenaEstudiantes()" /> Sabatino');

                                        if ($mnTurno == 6)
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" onclick="llenaEstudiantes()" checked /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" onclick="llenaEstudiantes()" /> Dominical');
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
                        <label for="cboParcial" class="col-sm-12 col-md-2 col-form-label">Parcial</label>
                        <div class="col-sm-12 col-md-3">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboParcial" name="cboParcial" onchange="cambiaGrid()">');
                                else
                                    echo('<select class="form-control" id="cboParcial" name="cboParcial" disabled>');

                                if ($mnParcial == 0)
                                    echo("<option value='0' selected>Parcial 1</option>");
                                else
                                    echo("<option value='0'>Parcial 1</option>");

                                if ($mnParcial == 1)
                                    echo("<option value='1' selected>Parcial 2</option>");
                                else
                                    echo("<option value='1'>Parcial 2</option>");

                                if ($mnParcial == 2)
                                    echo("<option value='2' selected>Parcial 3</option>");
                                else
                                    echo("<option value='2'>Parcial 3</option>");

                                if ($mnParcial == 3)
                                    echo("<option value='3' selected>Examen Extraordinario</option>");
                                else
                                    echo("<option value='3'>Examen Extraordinario</option>");

                                if ($mnParcial == 4)
                                    echo("<option value='4' selected>Curso intersemestral</option>");
                                else
                                    echo("<option value='4'>Curso intersemestral</option>");

                                if ($mnParcial == 5)
                                    echo("<option value='5' selected>Examen de Suficiencia</option>");
                                else
                                    echo("<option value='5'>Examen de Suficiencia</option>");

                                if ($mnParcial == 6)
                                    echo("<option value='6' selected>Convalidación</option>");
                                else
                                    echo("<option value='6'>Convalidación</option>");
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFecha" class="col-sm-12 col-md-2 col-form-label">Fecha</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />');
                                echo('<input type="hidden" class="form-control" id="txnExisteCalificacion" name="txnExisteCalificacion" value="0" />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12 col-md-2 form-label">Estudiantes</label>
                        <div class="col-sm-12 col-md-6">
                            <div id="dvEST1">
                                <table id="dgEST1" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbEST1', singleSelect:true, method:'get', onClickCell: onClickCellEST1">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'matricula', hidden:'true'">Matrícula</th>
                                            <th data-options="field:'estudiante', width:'80%', align:'left'">Estudiante</th>
                                            <th data-options="field:'nota', width:'20%', align:'center', editor:{type:'numberbox'}">Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxDevuelveDetCalificacion($msCodigo, $msAsignatura, $mnSemestre);

                                        while ($mFila = $mDatos->fetch())
                                        {
                                            $msEstudiante = trim($mFila["APELLIDO1_010"]);
                                            if (trim($mFila["APELLIDO2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["APELLIDO2_010"];

                                            $msEstudiante .= ", " . $mFila["NOMBRE1_010"];

                                            if (trim($mFila["NOMBRE2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["NOMBRE2_010"];
                                                
                                            echo('<tr>');
                                            echo('<td>' . rtrim($mFila['MATRICULA_REL']) . '</td>');
                                            echo('<td>' . rtrim($msEstudiante) . '</td>');
                                            echo('<td>' . $mFila['NOTA_161'] . '</td>');
                                            echo('</tr>');
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <div id="dvEST2">
                                <table id="dgEST2" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbEST2', singleSelect:true, method:'get', onClickCell: onClickCellEST2">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'matricula', hidden:'true'">Matrícula</th>
                                            <th data-options="field:'estudiante', width:'80%', align:'left'">Estudiante</th>
                                            <th data-options="field:'nota', width:'20%', align:'center', editor: {type:'combobox', options:{panelHeight:'auto', data:[{value:'No aplica',text:'No aplica'}, {value:'Reprobado',text:'Reprobado'}, {value:'Reprobado',text:'Aprobado'}]}}">Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxDevuelveDetCalificacion($msCodigo, $msAsignatura, $mnTurno, $mnAnno, $mnSemestre);

                                        while ($mFila = $mDatos->fetch())
                                        {
                                            $msEstudiante = trim($mFila["APELLIDO1_010"]);
                                            if (trim($mFila["APELLIDO2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["APELLIDO2_010"];

                                            $msEstudiante .= ", " . $mFila["NOMBRE1_010"];

                                            if (trim($mFila["NOMBRE2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["NOMBRE2_010"];
                                                
                                            echo('<tr>');
                                            echo('<td>' . rtrim($mFila['MATRICULA_REL']) . '</td>');
                                            echo('<td>' . rtrim($msEstudiante) . '</td>');

                                            if ($mnParcial <= 2)
                                                echo('<td>' . $mFila['NOTA_161'] . '</td>');
                                            else
                                            {
                                                switch(intval($mFila['NOTA_161']))
                                                {
                                                    case 0:
                                                        echo('<td>No aplica</td>');
                                                        break;
                                                    case 1:
                                                        echo('<td>Reprobado</td>');
                                                        break;
                                                    case 2:
                                                        echo('<td>Aprobado</td>');
                                                        break;
                                                }
                                            }
                                            echo('</tr>');
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tbEST1" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" onclick="acceptitEST1()">Salir del Modo de Edición</a>
                    </div>

                    <div id="tbEST2" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" onclick="acceptitEST2()">Salir del Modo de Edición</a>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCalificaciones.php';" />
                        </div>
                    </div>
                </form>
            </div>
            <?php
            }
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
    llenaAsignaturas();
    verificaCalificacion();
    cambiaGrid();

    if ($('#txtCodCalificacion').val() == "")
    {
        var asignatura = $('#cboAsignatura').val();
        llenaEstudiantes();
    }
}

function verificarFormulario() {
    var semestre = $('#txnSemestre').val();
    
    if (semestre < 1 || semestre > 2)
    {
        $.messager.alert('UMOJN', 'El valor del semestre sólo puede ser 1 ó 2.', 'warning');
        return false;
    }

    if (document.getElementById("cboParcial").value <= 2)
    {
        var gridEstudiantes = $('#dgEST1').datagrid('getData');
        var registros = $('#dgEST1').datagrid('getRows').length - 1;;
    }
    else
    {
        var gridEstudiantes = $('#dgEST2').datagrid('getData');
        var registros = $('#dgEST2').datagrid('getRows').length - 1;;
    }

    if (document.getElementById('txnExisteCalificacion').value == 1 && document.getElementById('txtCodCalificacion').value == "")
    {
        $.messager.alert('UMOJN', 'Las calificaciones para esta asignatura fueron ingresadas', 'warning');
        return false;
    }
    
    if (registros < 0)
    {
        $.messager.alert('UMOJN', 'Faltan los Estudiantes', 'warning');
        return false;
    }

    for (i = 0; i <= registros; i++) {
        if (gridEstudiantes.rows[i].nota == "" || gridEstudiantes.rows[i].nota < 0)
        {
            $.messager.alert('UMOJN', 'Falta la calificación de ' + gridEstudiantes.rows[i].estudiante, 'warning');
            return false;
        }
    }

    return true;
}

function llenaAsignaturas()
{
    var carrera = $('#cboCarrera').val();
    var docente = $('#cboDocente').val();
    var anno = $("#txnAnno").val();
    var semestre = $("#txnSemestre").val();
    var turno;
    
    if (document.getElementById('optTurno1').checked)
        turno = 1;
    if (document.getElementById('optTurno2').checked)
        turno = 2;
    if (document.getElementById('optTurno3').checked)
        turno = 3;
    if (document.getElementById('optTurno4').checked)
        turno = 4;
    if (document.getElementById('optTurno5').checked)
        turno = 5;
    if (document.getElementById('optTurno6').checked)
        turno = 6;
    
    var datos = new FormData();
    datos.append('carreraAsg', carrera);
    datos.append('docenteAsg', docente);
    datos.append('annoAsg', anno);
    datos.append('semestreAsg', semestre);
    datos.append('turnoAsg', turno);

    $.ajax({
        url: 'funciones/fxDatosCalificacion.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
            if (document.getElementById('txtCodCalificacion').value == "") {llenaEstudiantes();}
        }
    })
}

function cambiaGrid()
{
    if (document.getElementById('cboParcial').value <= 2)
    {
        document.getElementById('dvEST1').hidden = false;
        document.getElementById('dvEST2').hidden = true;
    }
    else
    {
        document.getElementById('dvEST1').hidden = true;
        document.getElementById('dvEST2').hidden = false;
    }
    llenaEstudiantes();
}

function llenaEstudiantes()
{
    var calificacion = document.getElementById('txtCodCalificacion').value;
    var datos = new FormData();
    var asignatura = document.getElementById('cboAsignatura').value;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
    var parcial = document.getElementById('cboParcial').value;
    var turno;

    if (calificacion == "")
    {
        if (document.getElementById("optTurno1").checked)
            turno = 1;
        if (document.getElementById("optTurno2").checked)
            turno = 2;
        if (document.getElementById("optTurno3").checked)
            turno = 3;
        if (document.getElementById("optTurno4").checked)
            turno = 4;
        if (document.getElementById("optTurno5").checked)
            turno = 5;
        if (document.getElementById("optTurno6").checked)
            turno = 6;
        
        datos.append('asignatura', asignatura);
        datos.append('semestre', semestre);
        datos.append('anno', anno);
        datos.append('turno', turno);
        datos.append('parcial', parcial);
        
        $.ajax({
            url: 'funciones/fxDatosCalificacion.php',
            type: 'post',
            data: datos,
            contentType: false,
            processData: false,
            success: function(response){
                datosGrid = JSON.parse(response);
                $('#dgEST1').datagrid({data: datosGrid});
                $('#dgEST1').datagrid('reload');
                $('#dgEST2').datagrid({data: datosGrid});
                $('#dgEST2').datagrid('reload');
                verificaCalificacion();
            }
        })
    }
}

function verificaCalificacion()
{
    var datos = new FormData();
    var asignatura = document.getElementById('cboAsignatura').value;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
    var parcial = document.getElementById('cboParcial').value;
    var turno = 0;

    if (document.getElementById('optTurno1').checked)
        turno = 1;
    if (document.getElementById('optTurno2').checked)
        turno = 2;
    if (document.getElementById('optTurno3').checked)
        turno = 3;
    if (document.getElementById('optTurno4').checked)
        turno = 4;
    if (document.getElementById('optTurno5').checked)
        turno = 5;
    if (document.getElementById('optTurno6').checked)
        turno = 6;

    datos.append('asignatura2', asignatura);
    datos.append('anno', anno);
    datos.append('semestre', semestre);
    datos.append('parcial', parcial);
    datos.append('turno', turno);

    $.ajax({
        url: 'funciones/fxDatosCalificacion.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('txnExisteCalificacion').value = response;
        }
    })
}

/*Grid de Estudiantes 1*/
var editIndexEST1 = undefined;
var lastIndexEST1;

$('#dgEST1').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexEST1 != rowIndex) {
            $(this).datagrid('endEdit', lastIndexEST1);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexEST1 = rowIndex;
    }
});

function endEditingEST1() {
    if (editIndexEST1 == undefined) {
        return true
    }
    if ($('#dgEST1').datagrid('validateRow', editIndexEST1)) {
        $('#dgEST1').datagrid('endEdit', editIndexEST1);
        editIndexEST1 = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellEST1(index, field) {
    if (editIndexEST1 != index) {
        if (endEditingEST1()) {
            $('#dgEST1').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
            editIndexEST1 = index;
        } else {
            setTimeout(function() {
                $('#dgEST1').datagrid('selectRow', editIndexEST1);
            }, 0);
        }
    }
}

function acceptitEST1() {
    if (endEditingEST1()) {
        $('#dgEST1').datagrid('acceptChanges');
    }
}

/*Grid de Estudiantes 2*/
var editIndexEST2 = undefined;
var lastIndexEST2;

$('#dgEST2').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexEST2 != rowIndex) {
            $(this).datagrid('endEdit', lastIndexEST2);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexEST2 = rowIndex;
    }
});

function endEditingEST2() {
    if (editIndexEST2 == undefined) {
        return true
    }
    if ($('#dgEST2').datagrid('validateRow', editIndexEST2)) {
        $('#dgEST2').datagrid('endEdit', editIndexEST2);
        editIndexEST2 = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellEST2(index, field) {
    if (editIndexEST2 != index) {
        if (endEditingEST2()) {
            $('#dgEST2').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
            editIndexEST2 = index;
        } else {
            setTimeout(function() {
                $('#dgEST2').datagrid('selectRow', editIndexEST2);
            }, 0);
        }
    }
}

function acceptitEST2() {
    if (endEditingEST2()) {
        $('#dgEST2').datagrid('acceptChanges');
    }
}

$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario()) {
        var texto;
        var datos;
        var i;

        if (document.getElementById("cboParcial").value <= 2)
        {
            var registros = $('#dgEST1').datagrid('getRows').length - 1;;
            var gridEstudiantes = $('#dgEST1').datagrid('getData');
        }
        else
        {
            var registros = $('#dgEST2').datagrid('getRows').length - 1;;
            var gridEstudiantes = $('#dgEST2').datagrid('getData');
        }

        texto = '{"txtCodCalificacion":"' + document.getElementById("txtCodCalificacion").value + '", ';
        if (document.getElementById("txtCodCalificacion").value == "")
            texto += '"Operacion":"0", ';
        else
            texto += '"Operacion":"1", ';
        texto += '"cboDocente":"' + document.getElementById("cboDocente").value + '", ';
        texto += '"cboAsignatura":"' + document.getElementById("cboAsignatura").value + '", ';
        texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
        texto += '"txnAnno":"' + document.getElementById("txnAnno").value + '", ';
        texto += '"txnSemestre":"' + document.getElementById("txnSemestre").value + '", ';
        texto += '"cboParcial":"' + document.getElementById("cboParcial").value + '", ';
        texto += '"dtpFecha":"' + document.getElementById("dtpFecha").value + '", ';

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

        if (registros >= 0) {
            texto += '"gridEstudiantes": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"matricula":"' + gridEstudiantes.rows[i].matricula;
                texto += '","estudiante":"' + gridEstudiantes.rows[i].estudiante;

                if (document.getElementById("cboParcial").value <= 2)
                    texto += '","nota":"' + gridEstudiantes.rows[i].nota;
                else
                {
                    if (gridEstudiantes.rows[i].nota == "No aplica")
                        texto += '","nota":"0';

                    if (gridEstudiantes.rows[i].nota == "Reprobado")
                        texto += '","nota":"1';
                    
                    if (gridEstudiantes.rows[i].nota == "Aprobado")
                        texto += '","nota":"2';
                }

                if (i == registros)
                    texto += '"}]}';
                else
                    texto += '"},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
                url: 'procCalificacion.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridCalificaciones.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
});
</script>