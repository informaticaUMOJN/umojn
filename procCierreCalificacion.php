<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
    require_once ("funciones/fxUsuarios.php");
    require_once ("funciones/fxCalificaciones.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$mnRegistro = fxVerificaUsuario();
	
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
        $mbAdministrador = fxVerificaAdministrador();
        $mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("procCierreActas", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
		{ ?>
            <div class="container text-center">
                <div id="DivContenido">
                    <img src="imagenes/errordeacceso.png"/>
                </div>
            </div>
        <?php 
        }
		else
		{
            if (isset($_POST["Cerrar"]))
            {
                $mnAnno = $_POST["txnAnno"];
                $mnSemestre = $_POST["txnSemestre"];
                $mnParcial = $_POST["cboParcial"];
                $mnTurno = $_POST["optTurno"];
                fxCierreCalificacion($_SESSION["gsUsuario"], $mnAnno, $mnSemestre, $mnParcial, $mnTurno);

            }
            else
            {
                $mnAnno = 0;
                $mnSemestre = 0;
                $mnParcial = 0;
                $mnTurno = 0;
            }

            $mnEstaCerrado = fxDevuelveCierreCalificacion($mnAnno, $mnSemestre, $mnParcial, $mnTurno);
        }
		?>
    	<div class="container">
        	<div id="DivContenido">
                <div class = "row">
                    <div class="col-xs-12 col-md-11">
                        <div class="degradado"><strong>Cierre de Calificaciones</strong></div>
                    </div>
                </div>

                <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                    <form id="procCierreActas" name="procCierreActas" action="procCierreCalificacion.php" method = "POST" onsubmit="return verificarFormulario()">
                        <div class="row">
                            <div class="col-auto offset-sm-none col-md-8">
                                <input type="submit" id="Cerrar" name="Cerrar" value="Cerrar actas" class="btn btn-primary" />
                            </div>
                        </div>

                        <div class = "form-group row">
                            <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año académico</label>
                            <div class="col-sm-12 col-md-2">
                                <?php
                                    if ($mnAnno == 0 and $mnSemestre == 0)
                                        echo('<input type="number" style="text-align:right" class="form-control" id="txnAnno" name="txnAnno" value="' . date('Y') . '" onchange="llenar()" />');
                                    else
                                        echo('<input type="number" style="text-align:right" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" onchange="llenar()" />');
                                ?>
                                <input type="hidden" style="text-align:right" class="form-control" id="txnCierre" name="txnCierre" value="0" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="txnSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                            <div class="col-sm-12 col-md-2">
                                <?php
                                    if ($mnAnno == 0 and $mnSemestre == 0)
                                    {
                                        if (date('m') <= 6)
                                            echo('<input type="number" style="text-align:right" class="form-control" id="txnSemestre" name="txnSemestre" value="1" onchange="llenar()" />');
                                        else
                                            echo('<input type="number" style="text-align:right" class="form-control" id="txnSemestre" name="txnSemestre" value="2" onchange="llenar()" />');
                                    }
                                    else
                                        echo('<input type="number" style="text-align:right" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" onchange="llenar()" />');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cboParcial" class="col-sm-12 col-md-2 col-form-label">Parcial</label>
                            <div class="col-sm-12 col-md-3">
                                <select class="form-control" id="cboParcial" name="cboParcial" onchange="llenar()">
                                <?php
                                    if ($mnAnno == 0 and $mnSemestre == 0)
                                    {
                                ?>
                                    <option value='0' selected>Parcial 1</option>
                                    <option value='1'>Parcial 2</option>
                                    <option value='2'>Parcial 3</option>
                                    <option value='3'>Examen Extraordinario</option>
                                    <option value='4'>Curso intersemestral</option>
                                    <option value='5'>Examen de Suficiencia</option>
                                    <option value='6'>Convalidación</option>
                                <?php
                                    }
                                    else
                                    {
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
                                    }
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                            <div class="col-sm-12 col-md-8">
                                <div class="radio">
                                <?php
                                    if ($mnAnno == 0 and $mnSemestre == 0)
                                    {
                                ?>
                                    <input type="radio" id="optTurno1" name="optTurno" value="1" checked /> Diurno &emsp;
                                    <input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino &emsp;
                                    <input type="radio" id="optTurno3" name="optTurno" value="3" /> Vespertino &emsp;
                                    <input type="radio" id="optTurno4" name="optTurno" value="4" /> Nocturno &emsp;
                                    <input type="radio" id="optTurno5" name="optTurno" value="5" /> Sabatino &emsp;
                                    <input type="radio" id="optTurno6" name="optTurno" value="6" /> Dominical
                                <?php
                                    }
                                    else
                                    {
                                        if ($mnTurno == 1)
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked/> Diurno &emsp;');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" /> Diurno &emsp;');

                                        if ($mnTurno == 2)
                                            echo('<input type="radio" id="optTurno2" name="optTurno" value="2" checked/> Matutino &emsp;');
                                        else
                                            echo('<input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino &emsp;');

                                        if ($mnTurno == 3)
                                            echo('<input type="radio" id="optTurno3" name="optTurno" value="3" checked/> Vespertino &emsp;');
                                        else
                                            echo('<input type="radio" id="optTurno3" name="optTurno" value="3" /> Vespertino &emsp;');

                                        if ($mnTurno == 4)
                                            echo('<input type="radio" id="optTurno4" name="optTurno" value="4" checked/> Nocturno &emsp;');
                                        else
                                            echo('<input type="radio" id="optTurno4" name="optTurno" value="4" /> Nocturno &emsp;');

                                        if ($mnTurno == 5)
                                            echo('<input type="radio" id="optTurno5" name="optTurno" value="5" checked/> Sabatino &emsp;');
                                        else
                                            echo('<input type="radio" id="optTurno5" name="optTurno" value="5" /> Sabatino &emsp;');

                                        if ($mnTurno == 6)
                                            echo('<input type="radio" id="optTurno6" name="optTurno" value="6" checked/> Dominical');
                                        else
                                            echo('<input type="radio" id="optTurno6" name="optTurno" value="6" /> Dominical');
                                    }
                                ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-10">
                                <table id="dgCierre" class="easyui-datagrid table" width="100%" height="400px">
                                    <thead>
                                        <th data-options="field:'DOCENTE_REL', hidden:'true'">codDocente</th>
                                        <th data-options="field:'ASIGNATURA_REL', hidden:'true'">codAsignatura</th>
                                        <th data-options="field:'NOMBRE_100', width:'15%', align:'left'">Docente</th>
                                        <th data-options="field:'NOMBRE_060', width:'15%', align:'left'">Asignatura</th>
                                        <th data-options="field:'ANNO_162', width:'10%', align:'left'">Año</th>
                                        <th data-options="field:'SEMESTRE_162', width:'10%', align:'left'">Semestre</th>
                                        <th data-options="field:'PARCIAL_162', width:'15%', align:'left'">Parcial</th>
                                        <th data-options="field:'TURNO_162', width:'10%', align:'left'">Turno</th>
                                        <th data-options="field:'FECHA_162', width:'15%', align:'left'">Cierre</th>
                                        <th data-options="field:'ESTADO_162', width:'10%', align:'left'">Estado</th>
                                    </thead>
                                    <?php
                                        $msConsulta = "select NOMBRE_002, ANNO_162, SEMESTRE_162, (case ESTADO_162 when 0 then 'Abierto' when 1 then 'Cerrado' end) as ESTADO_162, ";
                                        $msConsulta .= "UMO162A.DOCENTE_REL, UMO162A.ASIGNATURA_REL, (case PARCIAL_162 when 0 then '1er. parcial' ";
                                        $msConsulta .= "when 1 then '2do. parcial' when 2 then '3er. parcial' when 3 then 'Ex. Extraordinario' ";
                                        $msConsulta .= "when 4 then 'Intersemestral' when 5 then 'Convalidación' end) as PARCIAL_162, ";
                                        $msConsulta .= "(case TURNO_162 when 1 then 'Diurno' when 2 then 'Matutino' when 3 then 'Vespertino' ";
                                        $msConsulta .= "when 4 then 'Nocturno' when 5 then 'Sabatino' when 6 then 'Dominical' end) as TURNO_162, ";
                                        $msConsulta .= "FECHA_162, NOMBRE_060, NOMBRE_100 from UMO162A join UMO002A on USUARIO_162 = USUARIO_REL join UMO100A on ";
                                        $msConsulta .= "UMO162A.DOCENTE_REL = UMO100A.DOCENTE_REL join UMO060A on UMO162A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
                                        $msConsulta .= "where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and TURNO_162 = ?";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                        $mDatos->execute([$mnAnno, $mnSemestre, $mnParcial, $mnTurno]);

                                        while ($Fila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $Fila["DOCENTE_REL"] . "</td>");
                                            echo ("<td>" . $Fila["ASIGNATURA_REL"] . "</td>");
                                            echo ("<td>" . $Fila["NOMBRE_100"] . "</td>");
                                            echo ("<td>" . $Fila["NOMBRE_060"] . "</td>");
                                            echo ("<td>" . $Fila["ANNO_162"] . "</td>");
                                            echo ("<td>" . $Fila["SEMESTRE_162"] . "</td>");
                                            echo ("<td>" . $Fila["PARCIAL_162"] . "</td>");
                                            echo ("<td>" . $Fila["TURNO_162"] . "</td>");
                                            echo ("<td>" . $Fila["ESTADO_162"] . "</td>");
                                            $fecha = date_create_from_format('Y-m-d H:i:s', $Fila["FECHA_162"]);
                                            echo ("<td style='text-align:center'>" . date_format($fecha, 'd-m-Y H:i:s') . "</td>");
                                            
                                            echo ("</tr>");
                                        }
                                    ?>
                                </table>
                            </div>
                        </div>

                        <div id="tbCierre" style="height:auto">
                            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="cambiaEstado(0)">Abrir</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="cambiaEstado(1)">Cerrar</a>
                        </div>
                    </form>
                </div>
            </div>
    	</div>
<?php } ?>
</body>
</html>
<script>
var editIndex = undefined;
var lastIndex; 

window.onload = function() 
{
    llenaGrid();
    document.querySelectorAll("input[name='optTurno']").forEach(radio => {
        radio.addEventListener("change", function(){llenaCierre(); llenaGrid();});
    });

    $('#dgCierre').datagrid({
        striped: true,
        toolbar: '#tbCierre',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCell
    });
}

function llenar()
{
    llenaGrid();
    llenaCierre();
}

function verificarFormulario() 
{
    var cierre = $('#txnCierre').val();

    if (cierre != 0)
    {
        $.messager.alert('UMOJN', 'El período ya fue cerrado.', 'warning');
        return false;
    }
}

function llenaGrid()
{
    var anno = $('#txnAnno').val();
    var semestre = $('#txnSemestre').val();
    var parcial = $('#cboParcial').val();
    var turno = $("input[name='optTurno']:checked").val();
    var datos = new FormData();
    datos.append('annoCierre', anno);
    datos.append('semestreCierre', semestre);
    datos.append('parcialCierre', parcial);
    datos.append('turnoCierre', turno);

    $.ajax({
        url: 'funciones/fxDatosCierreNotas.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            datos = JSON.parse(response);
            $('#dgCierre').datagrid({data: datos});
            $('#dgCierre').datagrid('reload');
        }
    })
}

function llenaCierre()
{
    var anno = $('#txnAnno').val();
    var semestre = $('#txnSemestre').val();
    var parcial = $('#cboParcial').val();
    var turno = $("input[name='optTurno']:checked").val();
    
    var datos = new FormData();
    datos.append('anno', anno);
    datos.append('semestre', semestre);
    datos.append('parcial', parcial);
    datos.append('turno', turno);

    $.ajax({
        url: 'funciones/fxDatosCierreNotas.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            $('#txnCierre').val(response);
        }
    })
}

function cambiaEstado(estado)
{
    var anno = $('#txnAnno').val();
    var semestre = $('#txnSemestre').val();
    var parcial = $('#cboParcial').val();
    var turno = $("input[name='optTurno']:checked").val();
    var fila = $('#dgCierre').datagrid('getSelected');
    var docente = fila.DOCENTE_REL;
    var asignatura = fila.ASIGNATURA_REL;

    var datos = new FormData();
    datos.append('annoEst', anno);
    datos.append('semestreEst', semestre);
    datos.append('parcialEst', parcial);
    datos.append('turnoEst', turno);
    datos.append('docenteEst', docente);
    datos.append('asignaturaEst', asignatura);
    datos.append('estadoEst', estado);

    $.ajax({
        url: 'funciones/fxDatosCierreNotas.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(){
            llenaGrid();
        }
    })
}

$('#dgCierre').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndex != rowIndex) {
            $(this).datagrid('endEdit', lastIndex);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndex = rowIndex;
    }
});

function endEditing() {
    if (editIndex == undefined) {
        return true
    }
    if ($('#dgCierre').datagrid('validateRow', editIndex)) {
        $('#dgCierre').datagrid('endEdit', editIndex);
        editIndex = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCell(index, field) {
    console.log('onClickCell:', index, field);  

    if (editIndex != index) {
        if (endEditing()) {
            $('#dgCierre').datagrid('selectRow', index).datagrid('beginEdit', index);
            editIndex = index;
        } else { 
            setTimeout(function() {
                $('#dgCierre').datagrid('selectRow', editIndex);
            }, 0);
        }
    }
}
</script>