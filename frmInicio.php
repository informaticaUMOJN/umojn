<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
    $m_cnx_MySQL = fxAbrirConexion();
?>
    <div class="container">
        <div id="DivContenido">
        	<div class = "row">
            	<div class="col-xs-12 col-md-12">
            		<div class="degradado">
                		<strong><?php echo($_SESSION["gsNombre"]) ?></strong>
                    </div>
                </div>
            </div>
            
        	<div class = "row">
                <div class="col-xs-4 col-md-2">
                	<div class="divBotonInicio">
    				<a href="gridEstudiantes.php"><img src="imagenes/btnAlumno.png" style="border-radius:10%" width="100%" /></a>
                    </div>
            	</div>
                <div class="col-xs-4 col-md-2">
                	<div class="divBotonInicio">
    				<a href="gridAlumnosPosgrado.php"><img src="imagenes/btnPosgrado.png" style="border-radius:10%" width="100%" /></a>
                    </div>
            	</div>
                <div class="col-xs-4 col-md-2">
                	<div class="divBotonInicio">
    				<a href="gridAlumnos.php"><img src="imagenes/btnCursosLibres.png" style="border-radius:10%" width="100%" /></a>
                    </div>
            	</div>
                <div class="col-xs-4 col-md-2">
                	<div class="divBotonInicio">
    				<a href="gridPagos.php"><img src="imagenes/btnPagos.png" style="border-radius:10%" width="100%" /></a>
                    </div>
             	</div>
                <div class="col-xs-4 col-md-2">
                	<div class="divBotonInicio">
    				<a href="gridMatricula.php"><img src="imagenes/btnMatricula.png" style="border-radius:10%" width="100%" /></a>
                    </div>
             	</div>
                <div class="col-xs-4 col-md-2">
                	<div class="divBotonInicio">
    				<a href="frmSolventes.php"><img src="imagenes/btnSolventes.png" style="border-radius:10%" width="100%" /></a>
                    </div>
             	</div>
            </div>

            <div class = "row" style="margin-top:2%">
                <div class="col-xs-12 col-md-6">
                    <div class = "row">
                    <div class="col-xs-12 col-md-12">
                        <div class="degradado">
                            <strong>Ultimos accesos</strong>
                        </div>
                    </div>
                    </div>
                    
                    <div class = "row">
                        <div class="col-12">
                            <table id="dgAccesos" class="easyui-datagrid table" width="100%" height="300px">
                            <thead>
                                <th data-options="field:'NOMBRE_002', width:'60%', align:'left'">Usuario</th>
                                <th data-options="field:'FECHA_000', width:'40%', align:'center'">Fecha y hora</th>
                            </thead>
                            <?php
                                $msConsulta = "select NOMBRE_002, FECHA_000 from UMO000A join UMO002A on USUARIO_000 = USUARIO_REL where OPERACION_000 = 'Sesión inicio' order by FECHA_000 desc limit 10";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();
                                while ($mFila = $mDatos->fetch())
                                {
                                    echo ("<tr>");
                                    echo ("<td>" . $mFila["NOMBRE_002"] . "</td>");
                                    echo ("<td style='text-align:center'>" . date_format(date_create($mFila["FECHA_000"]), 'd-m-Y h:i:s A') . "</td>");
                                    echo ("</tr>");
                                }
                            ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
</body>
</html>
<script>
    window.onload = function() {
        var dgA = $('#dgAccesos');
        dgA.datagrid({striped: true});

        var tdA1 = dgA.datagrid('getPanel').find('div.datagrid-header td[field="NOMBRE_002"]');
        var tdA2 = dgA.datagrid('getPanel').find('div.datagrid-header td[field="FECHA_000"]');

        tdA1.css({'background-color':'#0000ff', 'color':'#ffffff'});
        tdA2.css({'background-color':'#0000ff', 'color':'#ffffff'});
	}
</script>