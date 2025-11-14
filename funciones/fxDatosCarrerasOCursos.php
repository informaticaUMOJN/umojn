<?php
require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion();

$tipo = $_POST["tipo"]; // 0 = Carrera, 1 = Cursos libres

if ($tipo == "carrera") {
    $consulta = "select CARRERA_REL as VALOR, NOMBRE_040 as TEXTO from UMO040A order by NOMBRE_040";
} else if ($tipo == "curso") {
    $consulta = "select CURSOS_REL as VALOR, NOMBRE_190 as TEXTO from UMO190A order by NOMBRE_190";
}

$stmt = $m_cnx_MySQL->prepare($consulta);
$stmt->execute();

while ($fila = $stmt->fetch()) {
    $valor = htmlspecialchars(trim($fila["VALOR"]));
    $texto = htmlspecialchars(trim($fila["TEXTO"]));
    echo "<option value='$valor'>$texto</option>";
}
?>