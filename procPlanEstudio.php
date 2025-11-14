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
    require_once ("funciones/fxAsignaturas.php");
    require_once ("funciones/fxPlanEstudio.php");
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
<?php }
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("procPlanEstudio");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{?>
		<div class="container text-center">
			<div id="DivContenido">
				<img src="imagenes/errordeacceso.png" />
			</div>
		</div>
		<?php }
		else
		{
			if (isset($_POST["txtCodPlanEstudio"]))
			{
				$msCodigo = $_POST["txtCodPlanEstudio"];
				$msCarrera = $_POST["cboCarrera"];
				$msPeriodo = $_POST["txtPeriodo"];
				$msGrado = $_POST["txtGrado"];
				$mnHoras = $_POST["txnHoras"];
                $mnCreditos = $_POST["txnCreditos"];
                $mnTurno = $_POST["optTurno"];
                $mnModalidad = $_POST["optModalidad"];
				$mnRegimen = $_POST["optRegimen"];
                $mbActivo = $_POST["optActivo"];
                if (isset($_POST["gridDetalle"]))
                    $gridDetalle = $_POST["gridDetalle"];
                
                if ($msCodigo == "")
                {
                    $msCodigo = fxGuardarPlanEstudio($msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo);
                    $msBitacora = $msCodigo . "; " . $msCarrera . "; " . $msPeriodo . "; " . $msGrado . "; " . $mnHoras . "; " . $mnCreditos . "; " . $mnTurno . "; " . $mnRegimen . "; " . $mnModalidad . "; " . $mbActivo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "KDSA050A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarPlanEstudio($msCodigo, $msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo);
                    fxBorrarDetPlanEstudio ($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msCarrera . "; " . $msPeriodo . "; " . $msGrado . "; " . $mnHoras . "; " . $mnCreditos . "; " . $mnTurno . "; " . $mnRegimen . "; " . $mnModalidad . "; " . $mbActivo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "KDSA050A", $msCodigo, "", "Modificar", $msBitacora);
                }
				
				$itemId = 1;
				foreach($gridDetalle as $mRegistro)
				{
                    $msAsignatura = $mRegistro['codAsignatura'];
                    $msRequisito = $mRegistro['codRequisito'];
                    $mnSemestre = $mRegistro['semestre'];
                    $mnHPresenciales = $mRegistro['hPresenciales'];
                    $mnHAutoestudio = $mRegistro['hAutoestudio'];
                    $mnHTrabajo = $mRegistro['hTrabajo'];
                    $mnHTotales = $mRegistro['hTotales'];
                    $mnCreditos = $mRegistro['creditos'];
                    fxGuardarDetPlanEstudio($msCodigo, $itemId, $msAsignatura, $msRequisito, $mnSemestre, $mnHPresenciales, $mnHAutoestudio, $mnHTrabajo, $mnHTotales, $mnCreditos);
                    $itemId++;
				}
				?>
<meta http-equiv="Refresh" content="0;url=gridPlanEstudio.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";
                
                if ($msCodigo != "")
                {
                    $mRecordSet = fxDevuelvePlanEstudio(0, $msCodigo);
                    $mFila = $mRecordSet->fetch();
                    $msCarrera = $mFila["CARRERA_REL"];
                    $msPeriodo = $mFila["PERIODO_050"];
                    $msGrado = $mFila["GRADO_050"];
                    $mnHoras = $mFila["HORAS_050"];
                    $mnCreditos = $mFila["CREDITOS_050"];
                    $mnTurno = $mFila["TURNO_050"];
                    $mnRegimen = $mFila["REGIMEN_050"];
                    $mnModalidad = $mFila["MODALIDAD_050"];
                    $mbActivo = $mFila["ACTIVO_050"];
                }
                else
                {
                    $msCarrera = "";
                    $msPeriodo = "";
                    $msGrado = "";
                    $mnHoras = 0;
                    $mnCreditos = 0;
                    $mnTurno = 1;
                    $mnRegimen = 1;
                    $mnModalidad = 1;
                    $mbActivo = 0;
                }
	?>
<div class="container text-left">
    <div id="DivContenido">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <form id="procPlanEstudio" name="procPlanEstudio">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary"  onclick="location.href='gridPlanEstudio.php';" />
                        </div>
                    </div>

                    <div class="easyui-tabs tabs-narrow" style="width:100%;height:auto">
                        <!--Inicio del DIV de Tabs-->
                        <div title="Generales" style="padding:10px">
                            <!--Inicio del DIV de Tab GENERALES-->
                            <div class="col-sm-auto offset-sm-0 col-md-11 offset-md-1">
                                <div class="form-group row">
                                    <label for="txtCodPlanEstudio" class="col-sm-auto col-md-2 col-form-label">Código del Plan</label>
                                    <div class="col-sm-12 col-md-3">
                                        <?php echo('<input type="text" class="form-control" id="txtCodPlanEstudio" name="txtCodPlanEstudio" value="' . $msCodigo . '" readonly />'); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="cboCarrera" class="col-sm-auto col-md-2 col-form-label">Carrera</label>
                                    <div class="col-sm-12 col-md-7">
                                        <select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaAsignaturas(this.value)">
                                        <?php
                                            $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 0 order by NOMBRE_040";
                                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
		                                    $mDatos->execute();
                                            while ($mFila = $mDatos->fetch())
                                            {
                                                $msValor = rtrim($mFila["CARRERA_REL"]);
                                                $msTexto = rtrim($mFila["NOMBRE_040"]);
                                                if ($msCodigo == "")
                                                {
                                                    echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                                    if ($msCarrera == "")
                                                        $msCarrera = $msValor;
                                                }
                                                else
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
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="txtPeriodo" class="col-sm-auto col-md-2 col-form-label">Período</label>
                                    <div class="col-sm-12 col-md-4">
                                        <?php echo('<input type="text" class="form-control" id="txtPeriodo" name="txtPeriodo" maxlength="30" value="' . $msPeriodo . '" />'); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="txtGrado" class="col-sm-auto col-md-2 form-label">Grado</label>
                                    <div class="col-sm-12 col-md-4">
                                        <?php echo('<input type="text" class="form-control" id="txtGrado" name="txtGrado" value="' . $msGrado . '" />'); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="txnHoras" class="col-sm-auto col-md-2 form-label">Horas</label>
                                    <div class="col-sm-12 col-md-2">
                                        <?php echo('<input type="number" class="form-control" id="txnHoras" name="txnHoras" value="' . $mnHoras . '" readonly />'); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="txnCreditos" class="col-sm-auto col-md-2 form-label">Créditos totales</label>
                                    <div class="col-sm-12 col-md-2">
                                        <?php echo('<input type="number" class="form-control" id="txnCreditos" name="txnCreditos" value="' . $mnCreditos . '" readonly />'); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="optModalidad" class="col-sm-auto col-md-2 form-label">Modalidad</label>
                                    <div class="col-sm-12 col-md-8">
                                        <div class="radio">
                                            <?php
                                                if($mnModalidad==1)
                                                    echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" checked /> Presencial');
                                                else
                                                    echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" /> Presencial');

                                                if($mnModalidad==2)
                                                    echo('&emsp;<input type="radio" id="optModalidad2" name="optModalidad" value="2" checked /> Por encuentro');
                                                else
                                                    echo('&emsp;<input type="radio" id="optModalidad2" name="optModalidad" value="2" /> Por encuentro');

                                                if($mnModalidad==3)
                                                    echo('&emsp;<input type="radio" id="optModalidad3" name="optModalidad" value="3" checked /> Virtual');
                                                else
                                                    echo('&emsp;<input type="radio" id="optModalidad3" name="optModalidad" value="3" /> Virtual');

                                                if($mnModalidad==4)
                                                    echo('&emsp;<input type="radio" id="optModalidad4" name="optModalidad" value="4" checked /> Mixta');
                                                else
                                                    echo('&emsp;<input type="radio" id="optModalidad4" name="optModalidad" value="4" /> Mixta');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="optRegimen" class="col-sm-auto col-md-2 form-label">Régimen</label>
                                    <div class="col-sm-12 col-md-8">
                                        <div class="radio">
                                            <?php
                                                if ($mnRegimen == 1)
                                                    echo('<input type="radio" id="optRegimen1" name="optRegimen" value="0" checked /> Mensual');
                                                else
                                                    echo('<input type="radio" id="optRegimen1" name="optRegimen" value="0" /> Mensual');

                                                if ($mnRegimen == 2)
                                                    echo('&emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="1" checked /> Bimestral');
                                                else
                                                    echo('&emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="1" /> Bimestral');
                                                
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
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                                    <div class="col-sm-12 col-md-8">
                                        <div class="radio">
                                            <?php
                                                if ($mnTurno == 1)
                                                    echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked /> Diurno');
                                                else
                                                    echo('<input type="radio" id="optTurno1" name="optTurno" value="1" /> Diurno');

                                                if ($mnTurno == 2)
                                                    echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" checked /> Matutino');
                                                else
                                                    echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino');

                                                if ($mnTurno == 3)
                                                    echo('&emsp;<input type="radio" id="optTurno3" name="optoptTurno" value="3" checked /> Vespertino');
                                                else
                                                    echo('&emsp;<input type="radio" id="optTurno3" name="optoptTurno" value="3" /> Vespertino');

                                                if ($mnTurno == 4)
                                                    echo('&emsp;<input type="radio" id="optTurno4" name="optoptTurno" value="4" checked /> Nocturno');
                                                else
                                                    echo('&emsp;<input type="radio" id="optTurno4" name="optoptTurno" value="4" /> Nocturno');

                                                if ($mnTurno == 5)
                                                    echo('&emsp;<input type="radio" id="optTurno5" name="optoptTurno" value="5" checked /> Sabatino');
                                                else
                                                    echo('&emsp;<input type="radio" id="optTurno5" name="optoptTurno" value="5" /> Sabatino');

                                                if ($mnTurno == 6)
                                                    echo('&emsp;<input type="radio" id="optTurno6" name="optoptTurno" value="6" checked /> Dominical');
                                                else
                                                    echo('&emsp;<input type="radio" id="optTurno6" name="optoptTurno" value="6" /> Dominical');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="optActivo" class="col-sm-auto col-md-2 form-label">Activo</label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="radio">
                                            <?php
                                                if ($mbActivo == 0)
                                                    echo('<input type="radio" id="optActivo1" name="optActivo" value="0" checked/> No');
                                                else
                                                    echo('<input type="radio" id="optActivo1" name="optActivo" value="0" /> No');

                                                if ($mbActivo == 1)
                                                    echo('&emsp;<input type="radio" id="optActivo2" name="optActivo" value="1" checked/> Si');
                                                else
                                                    echo('&emsp; <input type="radio" id="optActivo2" name="optActivo" value="1" /> Si');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Fin del DIV de Tab GENERALES-->

                        <div title="Detalle de asignaturas" style="padding:10px">
                            <!--Inicio del DIV de Tab ASIGNATURAS-->
                            <div class="col-xs-auto col-md-12">
                                <div class="form-group row">
                                    <div class="col-sm-auto col-md-12">
                                        <div id="dvASG">
                                            <table id="dgASG" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbASG', footer:'#ftASG', singleSelect:true, method:'get', onClickCell: onClickCellASG">
                                                <thead>
                                                    <tr>
                                                        <th data-options="field:'codPlanEstudio', hidden:'true'">codPlan</th>
                                                        <th data-options="field:'codConsecutivo', hidden:'true'">codConsecutivo</th>
                                                        <th data-options="field:'codAsignatura', hidden:'true'">codAsignatura</th>
                                                        <th data-options="field:'codRequisito', hidden:'true'">codRequisito</th>
                                                        <th data-options="field:'semestre',width:'6%',align:'left'">Semestre</th>
                                                        <th data-options="field:'asignatura',width:'18%',align:'left'">Asignatura</th>
                                                        <th data-options="field:'requisito',width:'18%',align:'left'">Requisito</th>
                                                        <th data-options="field:'hPresenciales',width:'12%',align:'left'">Presenciales/Teóricas</th>
                                                        <th data-options="field:'hAutoestudio',width:'12%',align:'left'">Autoestudio/Prácticas</th>
                                                        <th data-options="field:'hTrabajo',width:'12%',align:'left'">Trabajo independiente</th>
                                                        <th data-options="field:'hTotales',width:'12%',align:'left'">Horas totales</th>
                                                        <th data-options="field:'creditos',width:'10%',align:'left'">Créditos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                    $mDatos = fxObtenerDetPlanEstudio($msCodigo);
                                                    while ($mFila = $mDatos->fetch())
                                                    {
                                                        echo ("<tr>");
                                                        echo ("<td>" . $mFila["PLANESTUDIO_REL"] . "</td>");
                                                        echo ("<td>" . $mFila["CONSECUTIVO_REL"] . "</td>");
                                                        echo ("<td>" . $mFila["ASIGNATURA_REL"] . "</td>");
                                                        echo ("<td>" . $mFila["UMO_ASIGNATURA_REL"] . "</td>");
                                                        echo ("<td>" . $mFila["SEMESTRE_051"] . "</td>");
                                                        echo ("<td>" . $mFila["ASIGNATURA"] . "</td>");
                                                        echo ("<td>" . $mFila["REQUISITO"] . "</td>");
                                                        echo ("<td>" . $mFila["HPRESENCIALES_051"] . "</td>");
                                                        echo ("<td>" . $mFila["HAUTOESTUDIO_051"] . "</td>");
                                                        echo ("<td>" . $mFila["HTRABAJO_051"] . "</td>");
                                                        echo ("<td>" . $mFila["HTOTALES_051"] . "</td>");
                                                        echo ("<td>" . $mFila["CREDITOS_051"] . "</td>");
                                                        echo ("</tr>");
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="tbASG" style="height:auto; padding-top:1%; padding-bottom:2%">
                                    <table width="100%">
                                        <tr>
                                            <td width="15%">Semestre</td>
                                            <td width="40%"><input type="number" id="txnSemestre" class="form-control" style="width:25%" value="0"></td>

                                            <td width="15%">Horas presenciales / Horas teóricas</td>
                                            <td width="30%"><input type="number" id="txnHPresenciales" class="form-control" style="width:30%" value="0" onchange="sumaHoras()"></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Asignatura</td>
                                            <td width="40%">
                                                <select id="cboAsignatura" name="cboAsignatura" class="form-control" style="width:80%">
                                                <?php
                                                    $msConsulta = "select ASIGNATURA_REL, NOMBRE_060 from UMO060A where CARRERA_REL = ? order by NOMBRE_060";
                                                    $mAsignaturas = $m_cnx_MySQL->prepare($msConsulta);
                                                    $mAsignaturas->execute([$msCarrera]);
                                                    while ($mAuxFila = $mAsignaturas->fetch())
                                                    {
                                                        echo("<option value='" . $mAuxFila["ASIGNATURA_REL"] . "'>" . $mAuxFila["NOMBRE_060"] . "</option>");
                                                    }
                                                ?>
                                                </select>
                                            </td>

                                            <td width="15%">Horas autoestudio / Horas prácticas</td>
                                            <td width="30%"><input type="number" id="txnHAutoestudio" class="form-control" style="width:30%" value="0" onchange="sumaHoras()"></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">¿Tiene Requisito?</td>
                                            <td width="40%">
                                                <input type="radio" name="optRequisito" id="optRequisito0" value="0" onchange="activaRequisito()" checked> Si&emsp;
                                                <input type="radio" name="optRequisito" id="optRequisito1" value="1" onchange="activaRequisito()"> No
                                            </td>

                                            <td width="15%">Horas de trabajo independiente</td>
                                            <td width="30%"><input type="number" id="txnHTrabajo" class="form-control" style="width:30%" value="0" onchange="sumaHoras()"></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Requisito</td>
                                            <td width="40%">
                                                <select id="cboRequisito" name="cboRequisito" class="form-control" style="width:80%">
                                                <?php
                                                    $msConsulta = "select ASIGNATURA_REL, NOMBRE_060 from UMO060A where CARRERA_REL = ? order by NOMBRE_060";
                                                    $mAsignaturas = $m_cnx_MySQL->prepare($msConsulta);
                                                    $mAsignaturas->execute([$msCarrera]);
                                                    while ($mAuxFila = $mAsignaturas->fetch())
                                                    {
                                                        echo("<option value='" . $mAuxFila["ASIGNATURA_REL"] . "'>" . $mAuxFila["NOMBRE_060"] . "</option>");
                                                    }
                                                ?>
                                                </select>
                                            </td>

                                            <td width="15%">Horas totales</td>
                                            <td width="30%"><input type="number" id="txnHTotales" class="form-control" style="width:30%" value="0" readonly></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td width="15%">Créditos</td>
                                            <td width="30%"><input type="number" id="txnCreditosAs" class="form-control" style="width:30%" value="0"></td>
                                        </tr>
                                    </table>
                                </div>

                                <div id="ftASG" style="height:auto">
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="appendASG()">Agregar</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitASG()">Borrar</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitASG()">Aceptar</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectASG()">Deshacer</a>
                                </div>
                            </div>
                        </div>
                        <!--Fin del DIV de Tab ASIGNATURAS-->
                    </div>
                    <!--Fin del DIV de Tabs-->
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
    var dgAsignaturas = $('#dgASG');
    dgAsignaturas.datagrid({striped: true});

    $('.datagrid-wrap').width('100%');
    $('.datagrid-view').height('200px');
}

function activaRequisito(){
    if (document.getElementById("optRequisito1").checked)
        document.getElementById("cboRequisito").disabled = true;
    else
        document.getElementById("cboRequisito").disabled = false;
}

function validaRegistro(){
    var gridAsignaturas = $('#dgASG').datagrid('getData');
    var asignatura = document.getElementById("cboAsignatura").value;
    var requisito = document.getElementById("cboRequisito").value;
    var registros = $('#dgASG').datagrid('getRows').length;

    if (document.getElementById("txnSemestre").value <= 0)
    {
        $.messager.alert('UMOJN','El semestre está en cero.','warning');
		return false;
    }

    for (i=0; i<registros; i++)
    {
        if (gridAsignaturas.rows[i].codAsignatura == asignatura)
        {
            $.messager.alert('UMOJN','La asignatura ya fue ingresada en este plan de estudios.','warning');
			return false;
        }
    }

    if (document.getElementById("optRequisito0").checked)
    {
        if (asignatura == requisito)
        {
            $.messager.alert('UMOJN','La asignatura y el requisito son iguales.','warning');
			return false;
        }
    }
    
    if (document.getElementById("txnHPresenciales").value <= 0)
    {
        $.messager.alert('UMOJN','Las horas presenciales están en cero.','warning');
		return false;
    }

    if (document.getElementById("txnHAutoestudio").value <= 0)
    {
        $.messager.alert('UMOJN','Las horas de autoestudio están en cero.','warning');
		return false;
    }

    if (document.getElementById("txnCreditosAs").value <= 0)
    {
        $.messager.alert('UMOJN','Los créditos están en cero.','warning');
		return false;
    }

    return true
}

function sumaHoras()
{
    var presenciales = document.getElementById("txnHPresenciales").value;
    var autoestudio = document.getElementById("txnHAutoestudio").value;
    var trabajo = document.getElementById("txnHTrabajo").value;
    var totales = parseInt(presenciales) + parseInt(autoestudio) + parseInt(trabajo);
    document.getElementById("txnHTotales").value = totales;
}

function sumaHorasCreditos()
{
    var gridAsignaturas = $('#dgASG').datagrid('getData');
    var registros = $('#dgASG').datagrid('getRows').length - 1;
    var hPresenciales = 0;
    var hAutoestudio = 0;
    var hTrabajo = 0;
    var hTotales = 0;
    var creditos = 0;

    if (registros >= 0) {
        for (i = 0; i <= registros; i++) {
            hPresenciales += parseInt(gridAsignaturas.rows[i].hPresenciales);
            hAutoestudio += parseInt(gridAsignaturas.rows[i].hAutoestudio);
            hTrabajo += parseInt(gridAsignaturas.rows[i].hTrabajo);
            creditos += parseInt(gridAsignaturas.rows[i].creditos);
        }

        hTotales = hPresenciales + hAutoestudio + hTrabajo;
        $('#txnHoras').val(hTotales);
        $('#txnCreditos').val(creditos);
    }
}

function llenaAsignaturas (carrera)
{
    var datos = new FormData();
    datos.append('carrera', carrera);

    $.ajax({
        url: 'funciones/fxDatosPlanEstudio.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
            document.getElementById('cboRequisito').innerHTML = response;
        }
    })
}

function verificarFormulario() {
    if (document.getElementById('txtPeriodo').value == "") {
        $.messager.alert('UMOJN', 'Falta el período.', 'warning');
        return false;
    }

    if (document.getElementById('txtGrado').value == "") {
        $.messager.alert('UMOJN', 'Falta el grado adquirido.', 'warning');
        return false;
    }

    if (document.getElementById('txnHoras').value <= 0) {
        $.messager.alert('UMOJN', 'Faltan las horas-clase.', 'warning');
        return false;
    }

    if (document.getElementById('txnCreditos').value <= 0) {
        $.messager.alert('UMOJN', 'Faltan los créditos.', 'warning');
        return false;
    }

    if ($('#dgASG').datagrid('getRows').length <= 0) {
        $.messager.alert('UMOJN', 'Falta el detalle de las asignaturas.', 'warning');
        return false;
    }

    return true;
}

/*Grid de Asignaturas*/
var editIndexASG = undefined;
var lastIndexASG;

$('#dgASG').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexASG != rowIndex) {
            $(this).datagrid('endEdit', lastIndexASG);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexASG = rowIndex;
    }
});

function endEditingASG() {
    if (editIndexASG == undefined) {
        return true
    }
    if ($('#dgASG').datagrid('validateRow', editIndexASG)) {
        $('#dgASG').datagrid('endEdit', editIndexASG);
        editIndexASG = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellASG(index, field) {
    if (editIndexASG != index) {
        if (endEditingASG()) {
            $('#dgASG').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
            editIndexASG = index;
        } else {
            setTimeout(function() {
                $('#dgASG').datagrid('selectRow', editIndexASG);
            }, 0);
        }
    }
}

function appendASG() {
    if (document.getElementById("optRequisito0").checked==true)
    {
        idRequisito = $('#cboRequisito').val();
        txRequisito = $('select[name="cboRequisito"] option:selected').text();
    }
    else
    {
        idRequisito = "";
        txRequisito = "";
    }
    txAsignatura = $('select[name="cboAsignatura"] option:selected').text();

    if (validaRegistro())
    {
        if (endEditingASG()) {
            $('#dgASG').datagrid('appendRow', {
                codPlanEstudio: $('#txtCodPlanEstudio').val(),
                codAsignatura: $('#cboAsignatura').val(),
                codRequisito: idRequisito,
                semestre: $('#txnSemestre').val(),
                asignatura: txAsignatura,
                requisito: txRequisito,
                hPresenciales: $('#txnHPresenciales').val(),
                hAutoestudio: $('#txnHAutoestudio').val(),
                hTrabajo: $('#txnHTrabajo').val(),
                hTotales: $('#txnHTotales').val(),
                creditos: $('#txnCreditosAs').val()
            });
            editIndexASG = $('#dgASG').datagrid('getRows').length;
            $('#dgASG').datagrid('selectRow', editIndexASG).datagrid('beginEdit', editIndexASG);
        }
        sumaHorasCreditos();
    }
}

function removeitASG() {
    if (editIndexASG == undefined) {
        return
    }
    $('#dgASG').datagrid('cancelEdit', editIndexASG)
        .datagrid('deleteRow', editIndexASG);
    editIndexASG = undefined;
    sumaHorasCreditos();
}

function acceptitASG() {
    if (endEditingASG()) {
        $('#dgASG').datagrid('acceptChanges');
    }
}

function rejectASG() {
    $('#dgASG').datagrid('rejectChanges');
    editIndexASG = undefined;
}

$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario() == true) {
        var texto;
        var datos;
        var registros;
        var i;
        var gridDetalle = $('#dgASG').datagrid('getData');

        texto = '{"txtCodPlanEstudio":"' + document.getElementById("txtCodPlanEstudio").value + '", ';
        texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
        texto += '"txtPeriodo":"' + document.getElementById("txtPeriodo").value + '", ';
        texto += '"txtGrado":"' + document.getElementById("txtGrado").value + '", ';
        texto += '"txnHoras":"' + document.getElementById("txnHoras").value + '", ';
        texto += '"txnCreditos":"' + document.getElementById("txnCreditos").value + '", ';

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

        if (document.getElementById("optModalidad1").checked)
            texto += '"optModalidad":"1", ';
        if (document.getElementById("optModalidad2").checked)
            texto += '"optModalidad":"2", ';
        if (document.getElementById("optModalidad3").checked)
            texto += '"optModalidad":"3", ';
        if (document.getElementById("optModalidad4").checked)
            texto += '"optModalidad":"4", ';

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


        if (document.getElementById("optActivo1").checked)
            texto += '"optActivo":"0", ';
        else
            texto += '"optActivo":"1", ';

        /*ASIGNATURAS*/
        registros = $('#dgASG').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridDetalle": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"codPlanEstudio":"' + gridDetalle.rows[i].codPlanEstudio + '",';
                texto += '"codConsecutivo":"' + i + '",';
                texto += '"codAsignatura":"' + gridDetalle.rows[i].codAsignatura + '",';
                texto += '"codRequisito":"' + gridDetalle.rows[i].codRequisito + '",';
                texto += '"requisito":"' + gridDetalle.rows[i].requisito + '",';
                texto += '"semestre":"' + gridDetalle.rows[i].semestre + '",';
                texto += '"asignatura":"' + gridDetalle.rows[i].asignatura + '",';
                texto += '"hPresenciales":"' + gridDetalle.rows[i].hPresenciales + '",';
                texto += '"hAutoestudio":"' + gridDetalle.rows[i].hAutoestudio + '",';
                texto += '"hTrabajo":"' + gridDetalle.rows[i].hTrabajo + '",';
                texto += '"hTotales":"' + gridDetalle.rows[i].hTotales + '",';
                texto += '"creditos":"' + gridDetalle.rows[i].creditos + '"';

                if (i == registros)
                    texto += '}]}';
                else
                    texto += '},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
            url: 'procPlanEstudio.php',
            type: 'post',
            data: datos,
            beforeSend: function() {
                console.log(datos)
            }
        })
        .done(function() {
            location.href = "gridPlanEstudio.php";
        })
        .fail(function() {
            console.log('Error')
        });
    }
});
</script>