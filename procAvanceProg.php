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
	require_once ("funciones/fxAvanceProg.php");
    $m_cnx_MySQL = fxAbrirConexion();
    $Registro = fxVerificaUsuario();
    $msDocente = $_SESSION["gsDocente"];
	
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
        $mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("procAvanceProg");
		
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
				$msCodigo = $_POST["txtCodAvance"];
                $msDocente = $_POST["cboDocente"];
                $msCarrera = $_POST["cboCarrera"];
                $msAsignatura = $_POST["cboAsignatura"];
                $mdFecha = $_POST["dtpFecha"];
                $mnAnno = $_POST["txnAnno"];
                $mnTurno = $_POST["optTurno"];
                $mnSemestre = $_POST["txnSemestre"];
				$gridAvance = $_POST["gridAvance"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarAvanceProg($msDocente, $msCarrera, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno);
                    $msBitacora = $msCodigo . "; " . $msDocente . "; " . $msCarrera . "; " . $msAsignatura . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnSemestre . "; " . $mnTurno;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO170A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarAvanceProg($msCodigo, $msDocente, $msCarrera, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno);
                    fxBorrarDetAvance($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msDocente . "; " . $msCarrera . "; " . $msAsignatura . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnSemestre . "; " . $mnTurno;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO170A", $msCodigo, "", "Modificar", $msBitacora);
                }
                
                $itemId = 1;
				foreach($gridAvance as $mRegistro)
				{
                    $fecha = date_create_from_format('d/m/Y', $mRegistro["FechaP"]);
                    $mFechaP = date_format($fecha, "Y-m-d");
                    $mUnidadP = $mRegistro['UnidadP'];
                    $mContenidoP = $mRegistro['ContenidoP'];
                    $fecha = date_create_from_format('d/m/Y', $mRegistro["FechaE"]);
                    $mFechaE = date_format($fecha, "Y-m-d");
                    $mUnidadE = $mRegistro['UnidadE'];
                    $mContenidoE = $mRegistro['ContenidoE'];
                    $mObservaciones = $mRegistro['OBSERVACIONES_171'];
                    fxGuardarDetAvance($msCodigo, $itemId, $mFechaP, $mFechaE, $mUnidadP, $mUnidadE, $mContenidoP, $mContenidoE, $mObservaciones);
                    $itemId++;
				}

				?><meta http-equiv="Refresh" content="0;url=gridAvanceProg.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";

				$mRecordSet = fxDevuelveAvanceProg(0, $msDocente, $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msCarrera = $mFila["CARRERA_REL"];
                    $msAsignatura = $mFila["ASIGNATURA_REL"];
                    $mdFecha = $mFila["FECHA_170"];
                    $mnAnno = $mFila["ANNO_170"];
                    $mnSemestre = $mFila["SEMESTRE_170"];
                    $mnTurno = $mFila["TURNO_170"];
                }
                else 
                {
                    $msDocente = "";
                    $msCarrera = "";
                    $msAsignatura = "";
                    $mdFecha = date("Y-m-d");
                    $mnAnno = date('Y');
                    if (intval(date('m')) <= 6)
                        $mnSemestre = 1;
                    else
                        $mnSemestre = 2;
                    $mnTurno = 1;
                }
	?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Avance programático</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procAvanceProg" name="procAvanceProg">
                    <div class="form-group row">
                        <label for="txtCodAvance" class="col-sm-12 col-md-2 col-form-label">Código del Avance</label>
                        <div class="col-sm-12 col-md-2">
                            <?php 
                                echo('<input type="text" class="form-control" id="txtCodAvance" name="txtCodAvance" value="' . $msCodigo . '" readonly />');
                                echo('<input type="hidden" class="form-control" id="txnExisteAvance" name="txnExisteAvance" value="" />');
                            ?>
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
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" onchange="llenaAvance()">');
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
                        <label for="dtpFecha" class="col-sm-12 col-md-2 col-form-label">Fecha</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" onchange="llenaAvance()" />');
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
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" onchange="llenaAvance()" />');
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
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" onclick="llenaAvance()" checked/> Diurno');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" onclick="llenaAvance()" /> Diurno');

                                        if ($mnTurno == 2)
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" onclick="llenaAvance()" checked /> Matutino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" onclick="llenaAvance()" /> Matutino');

                                        if ($mnTurno == 3)
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" onclick="llenaAvance()" checked /> Vespertino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" onclick="llenaAvance()" /> Vespertino');

                                        if ($mnTurno == 4)
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" onclick="llenaAvance()" checked /> Nocturno');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" onclick="llenaAvance()" /> Nocturno');

                                        if ($mnTurno == 5)
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" onclick="llenaAvance()" checked /> Sabatino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" onclick="llenaAvance()" /> Sabatino');

                                        if ($mnTurno == 6)
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" onclick="llenaAvance()" checked /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" onclick="llenaAvance()" /> Dominical');
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
                        <div class="col-sm-12 col-md-10">
                            <div id="dvAVN">
                                <table id="dgAVN" class="easyui-datagrid table", data-options="iconCls:'icon-edit', toolbar:'#tbAVN', singleSelect:true, nowrap:false, method:'get', onClickCell: onClickCellAVN">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'FechaP', width:'10%', align:'left'">Fecha<br>programada</th>
                                            <th data-options="field:'UnidadP', width:'15%', align:'left'">Unidad<br>programada</th>
                                            <th data-options="field:'ContenidoP', width:'15%', align:'left'">Contenido<br>programado</th>
                                            <th data-options="field:'FechaE', width:'10%', align:'left', editor:{type:'datebox',options:{formatter:myformatter,parser:myparser}}">Fecha<br>ejecutada</th>
                                            <th data-options="field:'UnidadE', width:'15%', align:'left', editor:{type:'textbox'}">Unidad<br>ejecutada</th>
                                            <th data-options="field:'ContenidoE', width:'15%', align:'left', editor:{type:'textbox'}">Contenido<br>ejecutado</th>
                                            <th data-options="field:'OBSERVACIONES_171', width:'20%', align:'left', editor:{type:'textbox'}">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxDevuelveDetAvance($msCodigo, $msAsignatura, $mnAnno, $mnSemestre);

                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo('<tr>');
                                            $fechaP = date_create_from_format('Y-m-d', $mFila["FechaP"]);
								            echo ("<td>" . date_format($fechaP, 'd/m/Y') . "</td>");
                                            echo('<td>' . rtrim($mFila['UnidadP']) . '</td>');
                                            echo('<td>' . rtrim($mFila['ContenidoP']) . '</td>');
                                            $fechaE = date_create_from_format('Y-m-d', $mFila["FechaE"]);
								            echo ("<td>" . date_format($fechaE, 'd/m/Y') . "</td>");
                                            echo('<td>' . rtrim($mFila['UnidadE']) . '</td>');
                                            echo('<td>' . rtrim($mFila['ContenidoE']) . '</td>');
                                            echo('<td>' . rtrim($mFila['OBSERVACIONES_171']) . '</td>');
                                            echo('</tr>');
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tbAVN" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" onclick="acceptitAVN()">Salir del Modo de Edición</a>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAvanceProg.php';" />
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
window.onload = function(){
    llenaAsignaturas();
}
function myformatter(date){
    var y = date.getFullYear();
    var m = date.getMonth()+1;
    var d = date.getDate();
    return (d<10?('0'+d):d)+'/'+(m<10?('0'+m):m)+'/'+y;
}
function myparser(s){
    if (!s) return new Date();
    var ss = (s.split('/'));
    var y = parseInt(ss[2],10);
    var m = parseInt(ss[1],10);
    var d = parseInt(ss[0],10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
        return new Date(y,m-1,d);
    } else {
        return new Date();
    }
}
function verificarFormulario() {
    var semestre = $('#txnSemestre').val();
    var gridAvance = $('#dgAVN').datagrid('getData');
    var registros = $('#dgAVN').datagrid('getRows').length - 1;
    var existeAvance = document.getElementById('txnExisteAvance').value
    var observaciones = "";
    var fecha;

    if (semestre < 1 || semestre > 2)
    {
        $.messager.alert('UMOJN', 'El valor del semestre sólo puede ser 1 ó 2.', 'warning');
        return false;
    }

    if (parseInt(existeAvance) > 0 && document.getElementById('txtCodAvance').value=="")
    {
        $.messager.alert('UMOJN', 'El avance programático de esta asignatura ya existe', 'warning');
        return false;
    }

    if (registros < 0)
    {
        $.messager.alert('UMOJN', 'Faltan los datos del avance', 'warning');
        return false;
    }

    for (i=0; i<=registros; i++)
    {
        observaciones = gridAvance.rows[i].OBSERVACIONES_171;
        fecha = gridAvance.rows[i].FechaP;
        if (observaciones.length > 500)
        {
            $.messager.alert('UMOJN', 'El texto de la observación de la fecha ' + fecha + ' supera la longitud permitida.', 'warning');
            return false;
        }
    }

    return true;
}

function llenaAsignaturas()
{
    var carrera = $('#cboCarrera').val();
    var docente = $('#cboDocente').val();
    var asignatura = '<?php echo($msAsignatura) ?>';
    var datos = new FormData();
    datos.append('carreraAsg', carrera);
    datos.append('docenteAsg', docente);

    $.ajax({
        url: 'funciones/fxDatosAvanceProg.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
            document.getElementById('cboAsignatura').value = asignatura;
            if (document.getElementById('txtCodAvance').value=="") {llenaAvance();}
        }
    })
}

function llenaAvance()
{
    var avance = document.getElementById('txtCodAvance').value;
    var datos = new FormData();
    var asignatura = document.getElementById('cboAsignatura').value;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
    var turno = 0;

    if (avance == "")
    {
        if (document.getElementById('optTurno1').checked) {turno=1}
        if (document.getElementById('optTurno2').checked) {turno=2}
        if (document.getElementById('optTurno3').checked) {turno=3}
        if (document.getElementById('optTurno4').checked) {turno=4}
        if (document.getElementById('optTurno5').checked) {turno=5}
        if (document.getElementById('optTurno6').checked) {turno=6}
        datos.append('asignatura', asignatura);
        datos.append('anno', anno);
        datos.append('semestre', semestre);
        datos.append('turno', turno);

        $.ajax({
            url: 'funciones/fxDatosAvanceProg.php',
            type: 'post',
            data: datos,
            contentType: false,
            processData: false,
            success: function(response){
                var texto = "";
                var msGrid = "";
                for (i=0; i<response.length; i++){
                    caracter = response.charAt(i);
                    switch (caracter){
                        case "%":
                            msGrid = texto;
                            texto = "";
                            break;
                        case "#":
                            mnText = texto;
                            texto = "";
                            break;
                        default:
                            texto += caracter;
                    }
                }

                if (msGrid != ""){
                    datosGrid = JSON.parse(msGrid);
                    $('#dgAVN').datagrid({data: datosGrid});
                    $('#dgAVN').datagrid('reload');
                }
                document.getElementById('txnExisteAvance').value = parseInt(mnText);
            }
        })
    }
}

/*Grid de Avances*/
var editIndexAVN = undefined;
var lastIndexAVN;

$('#dgAVN').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexAVN != rowIndex) {
            $(this).datagrid('endEdit', lastIndexAVN);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexAVN = rowIndex;
    }
});

function endEditingAVN() {
    if (editIndexAVN == undefined) {
        return true
    }
    if ($('#dgAVN').datagrid('validateRow', editIndexAVN)) {
        $('#dgAVN').datagrid('endEdit', editIndexAVN);
        editIndexAVN = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellAVN(index, field) {
    if (editIndexAVN != index) {
        if (endEditingAVN()) {
            $('#dgAVN').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
            editIndexAVN = index;
        } else {
            setTimeout(function() {
                $('#dgAVN').datagrid('selectRow', editIndexAVN);
            }, 0);
        }
    }
}

function acceptitAVN() {
    if (endEditingAVN()) {
        $('#dgAVN').datagrid('acceptChanges');
    }
}

$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario()) {
        var texto;
        var datos;
        var registros;
        var i;
        var gridAvances = $('#dgAVN').datagrid('getData');

        texto = '{"txtCodAvance":"' + document.getElementById("txtCodAvance").value + '", ';
        if (document.getElementById("txtCodAvance").value == "")
            texto += '"Operacion":"0", ';
        else
            texto += '"Operacion":"1", ';
        texto += '"cboDocente":"' + document.getElementById("cboDocente").value + '", ';
        texto += '"cboAsignatura":"' + document.getElementById("cboAsignatura").value + '", ';
        texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
        texto += '"dtpFecha":"' + document.getElementById("dtpFecha").value + '", ';
        texto += '"txnAnno":"' + document.getElementById("txnAnno").value + '", ';
        texto += '"txnSemestre":"' + document.getElementById("txnSemestre").value + '", ';

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
        
        registros = $('#dgAVN').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridAvance": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"FechaP":"' + gridAvances.rows[i].FechaP;
                texto += '","UnidadP":"' + gridAvances.rows[i].UnidadP;
                texto += '","ContenidoP":"' + gridAvances.rows[i].ContenidoP;
                texto += '","FechaE":"' + gridAvances.rows[i].FechaE;
                texto += '","UnidadE":"' + gridAvances.rows[i].UnidadE;
                texto += '","ContenidoE":"' + gridAvances.rows[i].ContenidoE;
                texto += '","OBSERVACIONES_171":"' + gridAvances.rows[i].OBSERVACIONES_171;
                if (i == registros)
                    texto += '"}]}';
                else
                    texto += '"},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
                url: 'procAvanceProg.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridAvanceProg.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
});
</script>