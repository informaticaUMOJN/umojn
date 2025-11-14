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
		$mbPermisoUsuario = fxPermisoUsuario("repInscripcion");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
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
            $msCarrera = "";
            $mdFecha = date("Y-m-d");
            $mnAnno = date("Y");
            if (intval(date("m")) <= 6)
                $mnSemestre = 1;
            else
                $mnSemestre = 2;
            $mnParcial = 0;
            $mnTurno = 1;
        }
    }
?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Asignaturas inscritas</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="frmInscripcion" name="frmInscripcion">
                    <div class="form-group row">
                        <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaEstudiantes()">');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["CARRERA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_040"]);

                                    if ($msCarrera == "")
                                        $msCarrera = $mValor;
                                    
                                    echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" onchange="llenaEstudiantes()" />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" onchange="llenaEstudiantes()" />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                        <div class="col-sm-12 col-md-7">
                            <div class="radio">
                                <?php
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
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12 col-md-2 form-label">Estudiantes</label>
                        <div class="col-sm-12 col-md-6">
                            <div id="dvEST1">
                                <table id="dgEST1" class="easyui-datagrid table" data-options="iconCls:'icon-edit', singleSelect:false, method:'get', onClickCell: onClickCellEST1">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'matricula', width:'20%', align:'left'">Matrícula</th>
                                            <th data-options="field:'estudiante', width:'80%', align:'left'">Estudiante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $msConsulta = "select UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 from UMO030A, UMO010A, UMO050A ";
                                        $msConsulta .= "where UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL ";
                                        $msConsulta .= "and UMO030A.CARRERA_REL = ? and TURNO_050 = ? and ANNOLECTIVO_030 = ? and SEMESTREACADEMICO_030 = ? ";
                                        $msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                        $mDatos->execute([$msCarrera, $mnTurno, $mnAnno, $mnSemestre]);

                                        while ($mFila = $mDatos->fetch())
                                        {
                                            $msEstudiante = trim($mFila["APELLIDO1_010"]);
                                            if (trim($mFila["APELLIDO2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["APELLIDO2_010"];

                                            $msEstudiante .= ", " . $mFila["NOMBRE1_010"];

                                            if (trim($mFila["NOMBRE2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["NOMBRE2_010"];
                                                
                                            echo('<tr>');
                                            echo('<td></td>');
                                            echo('<td>' . rtrim($mFila['MATRICULA_REL']) . '</td>');
                                            echo('<td>' . rtrim($msEstudiante) . '</td>');
                                            echo('</tr>');
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <input type="button" id="Aceptar" name="Aceptar" value="Aceptar" class="btn btn-primary" onclick="imprimir()"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script>
window.onload = function() 
{
    llenaEstudiantes();
}

function verificarFormulario() {
    var semestre = $('#txnSemestre').val();
    
    if (semestre < 1 || semestre > 2)
    {
        $.messager.alert('UMOJN', 'El valor del semestre sólo puede ser 1 ó 2.', 'warning');
        return false;
    }
}

function llenaEstudiantes()
{
    var datos = new FormData();
    var carrera = document.getElementById('cboCarrera').value;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
    var turno;

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
    
    datos.append('carrera', carrera);
    datos.append('turno', turno);
    datos.append('anno', anno);
    datos.append('semestre', semestre);
    
    $.ajax({
        url: 'funciones/fxDatosInscripcion.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            datosGrid = JSON.parse(response);
            $('#dgEST1').datagrid({data: datosGrid});
            $('#dgEST1').datagrid('reload');
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

function imprimir() {
    var i;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
    var rows = $('#dgEST1').datagrid('getSelections');
    var cuenta = rows.length;

    if (cuenta == 0)
        $.messager.alert('UMOJN', 'Seleccione al menos un estudiante.', 'info');
    else
    {
        var mArrMatricula = Array.from({length: cuenta}, () => Array(3));

        for (i=0; i<cuenta; i++)
        {
            matricula = rows[i].matricula;
            mArrMatricula[i][0] = matricula;
            mArrMatricula[i][1] = anno;
            mArrMatricula[i][2] = semestre;
        }

        $.redirect("repInscripcion.php", {msMatricula: mArrMatricula}, "POST", "_blank");
    }
}
</script>