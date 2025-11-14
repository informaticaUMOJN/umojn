<?php
require_once ("funciones/fxGeneral.php");
include 'Funciones/fxCbrEstudiantes.php';

$matricula = $_GET['matricula'];
$resultado = fxObtenerMostrarC($matricula);
$datos = $resultado->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($datos);

?>