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
		$mbPermisoUsuario = fxPermisoUsuario("repMatriculados");
		
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
                <div class="degradado"><strong>Estudiantes matriculados</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="frmMatriculados" name="frmMatriculados">
                    <div class="form-group row">
                        <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboCarrera" name="cboCarrera">');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 0";
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
                        <label for="cboAnno" class="col-sm-12 col-md-2 col-form-label">Año académico</label>
                        <div class="col-sm-12 col-md-2">
                            <select class="form-control" id="cboAnno" name="cboAnno">
                                <option value="0" selected>Todos</option>
                                <option value="1">1er. año</option>
                                <option value="2">2do. año</option>
                                <option value="3">3er. año</option>
                                <option value="4">4to. año</option>
                                <option value="5">5to. año</option>
                            </select>
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
function verificarFormulario() {
    var semestre = $('#txnSemestre').val();
    
    if (semestre < 1 || semestre > 2)
    {
        $.messager.alert('UMOJN', 'El valor del semestre sólo puede ser 1 ó 2.', 'warning');
        return false;
    }
}

function imprimir() {
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
    var carrera = document.getElementById('cboCarrera').value;
    var academico = document.getElementById('cboAnno').value;
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
        turno = 6

    $.redirect("repMatriculados.php", {msCarrera: carrera, mnTurno: turno, mnSemestre: semestre, mnAnno: anno, mnAcademico: academico}, "POST", "_blank");
}
</script>