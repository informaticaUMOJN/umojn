<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["gsUsuario"])) {
    echo json_encode(["error" => "No hay sesión activa"]);
    exit;
}

echo json_encode([
    "usuario" => $_SESSION["gsUsuario"],
    "nombre" => $_SESSION["gsNombre"],
    "matricula" => $_SESSION["gsMatricula"]
]);
?>
