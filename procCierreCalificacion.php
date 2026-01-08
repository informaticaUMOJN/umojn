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
		$mbPermisoUsuario = fxPermisoUsuario("procCierreCalificacion", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
                    <form id="procCierreCalificacion" name="procCierreCalificacion" action="procCierreCalificacion.php" method = "POST">
                        <div class="row">
                            <div class="col-auto offset-sm-none col-md-8">
                                <input type="submit" id="Cerrar" name="Cerrar" value="Cerrar actas" class="btn btn-primary" />
                            </div>
                        </div>

                        <div class = "form-group row">
                            <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año académico</label>
                            <div class="col-sm-12 col-md-2">
                                <?php
                                    echo('<input type="number" style="text-align:right" class="form-control" id="txnAnno" name="txnAnno" value="' . date('Y') . '" />');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="txnSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                            <div class="col-sm-12 col-md-2">
                                <?php
                                    if (date('m') <= 6)
                                        echo('<input type="number" style="text-align:right" class="form-control" id="txnSemestre" name="txnSemestre" value="1" />');
                                    else
                                        echo('<input type="number" style="text-align:right" class="form-control" id="txnSemestre" name="txnSemestre" value="2" />');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cboParcial" class="col-sm-12 col-md-2 col-form-label">Parcial</label>
                            <div class="col-sm-12 col-md-3">
                                <select class="form-control" id="cboParcial" name="cboParcial">
                                    <option value='0' selected>Parcial 1</option>
                                    <option value='1'>Parcial 2</option>
                                    <option value='2'>Parcial 3</option>
                                    <option value='3'>Examen Extraordinario</option>
                                    <option value='4'>Curso intersemestral</option>
                                    <option value='5'>Examen de Suficiencia</option>
                                    <option value='6'>Convalidación</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                            <div class="col-sm-12 col-md-8">
                                <div class="radio">
                                    <input type="radio" id="optTurno1" name="optTurno" value="1" checked/> Diurno &emsp;
                                    <input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino &emsp;
                                    <input type="radio" id="optTurno3" name="optTurno" value="3" /> Vespertino &emsp;
                                    <input type="radio" id="optTurno4" name="optTurno" value="4" /> Nocturno &emsp;
                                    <input type="radio" id="optTurno5" name="optTurno" value="5" /> Sabatino &emsp;
                                    <input type="radio" id="optTurno6" name="optTurno" value="6" /> Dominical
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-10">
                                <table id="dgCierre" class="easyui-datagrid table" data-options="singleSelect:true" width="100%">
                                    <thead>
                                        <th data-options="field:'NOMBRE_002', width:'35%', align:'left'">Usuario</th>
                                        <th data-options="field:'ANNO_162', width:'10%', align:'left'">Año</th>
                                        <th data-options="field:'SEMESTRE_162', width:'10%', align:'left'">Semestre</th>
                                        <th data-options="field:'PARCIAL_162', width:'15%', align:'left'">Parcial</th>
                                        <th data-options="field:'TURNO_162', width:'10%', align:'left'">Turno</th>
                                        <th data-options="field:'FECHA_162', width:'20%', align:'left'">Cierre</th>
                                    </thead>
                                    <?php
                                        $msConsulta = "select NOMBRE_002, ANNO_162, SEMESTRE_162, (case PARCIAL_162 when 0 then '1er. parcial' ";
                                        $msConsulta .= "when 1 then '2do. parcial' when 2 then '3er. parcial' when 3 then 'Ex. Extraordinario' ";
                                        $msConsulta .= "when 4 then 'Intersemestral' when 5 then 'Convalidación' end) as PARCIAL_162, ";
                                        $msConsulta .= "(case TURNO_162 when 1 then 'Diurno' when 2 then 'Matutino' when 3 then 'Vespertino' ";
                                        $msConsulta .= "when 4 then 'Nocturno' when 5 then 'Sabatino' when 6 then 'Dominical' end) as TURNO_162, ";
                                        $msConsulta .= "FECHA_162 from UMO162A join UMO002A on USUARIO_162 = USUARIO_REL where ANNO_162 = year(NOW()) ";
                                        $msConsulta .= "order by SEMESTRE_162 desc, PARCIAL_162 desc";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                        $mDatos->execute();

                                        while ($Fila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $Fila["NOMBRE_002"] . "</td>");
                                            echo ("<td>" . $Fila["ANNO_162"] . "</td>");
                                            echo ("<td>" . $Fila["SEMESTRE_162"] . "</td>");
                                            echo ("<td>" . $Fila["PARCIAL_162"] . "</td>");
                                            echo ("<td>" . $Fila["TURNO_162"] . "</td>");
                                            $fecha = date_create_from_format('Y-m-d H:i:s', $Fila["FECHA_162"]);
                                            echo ("<td style='text-align:center'>" . date_format($fecha, 'd-m-Y H:i:s') . "</td>");
                                            
                                            echo ("</tr>");
                                        }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    	</div>
<?php } ?>
</body>
</html>
<script>
function llenaGrid()
{
    var anno = $('#txnAnno').val();
    var datos = new FormData();
    datos.append('annoCierre', anno);

    $.ajax({
        url: 'funciones/fxDatosCierreNotas.php',
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
</script>