<?php
header("Content-Type: application/json");
require_once("datos.php");
require_once("funciones/fxGeneral.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($usuario) || empty($clave)) {
        echo json_encode(["success" => false, "message" => "Usuario y clave son obligatorios"]);
        exit;
    }

    try {
        $conexion = fxAbrirConexion();

        $msConsulta = $conexion->prepare("SELECT CLAVE_002, NOMBRE_002, MATRICULA_REL FROM UMO002A WHERE USUARIO_REL = ? AND ESTUDIANTE_002 = 1");
        $msConsulta->execute([$usuario]);
        $fila = $msConsulta->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            $claveBD = $fila["CLAVE_002"];

            if (password_verify($clave, $claveBD)) {
                echo json_encode([
                    "success" => true,
                    "usuario" => $usuario,
                    "nombre" => $fila["NOMBRE_002"],
                    "matricula" => $fila["MATRICULA_REL"]
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Usuario no encontrado o inactivo"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en base de datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
?>
