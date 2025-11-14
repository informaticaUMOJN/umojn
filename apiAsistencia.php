<?php
header("Content-Type: application/json");
require_once("funciones/fxGeneral.php");

if (!isset($_GET['matricula'])) {
    echo json_encode(["error" => "Falta el parámetro: matrícula"]);
    exit;
}

$matricula = trim($_GET['matricula']); // Limpia espacios en blanco
file_put_contents("log_asistencia.txt", "🔹 Matrícula recibida: [$matricula]\n", FILE_APPEND); // Log temprano

try {
    $conn = fxAbrirConexion();

    $sql = "SELECT 
        A.ASISTENCIA_REL,
        A.FECHA_150,
        CASE A.TURNO_150
            WHEN 1 THEN 'Diurno'
            WHEN 2 THEN 'Matutino'
            WHEN 3 THEN 'Vespertino'
            WHEN 4 THEN 'Nocturno'
            WHEN 5 THEN 'Sabatino'
            WHEN 6 THEN 'Dominical'
            ELSE 'Desconocido'
        END AS TURNO_TEXTO,
        A.SEMESTRE_150,
        S.NOMBRE_060 AS NOMBRE_ASIGNATURA,
        P.MATRICULA_REL,
        CASE P.ESTADO_151
            WHEN 0 THEN 'Presente'
            WHEN 1 THEN 'Ausente'
            ELSE 'Desconocido'
        END AS ESTADO_TEXTO 
    FROM UMO150A A
    LEFT JOIN UMO060A S ON A.ASIGNATURA_REL = S.ASIGNATURA_REL
    INNER JOIN UMO151A P ON A.ASISTENCIA_REL = P.ASISTENCIA_REL
    LEFT JOIN UMO030A M ON P.MATRICULA_REL = M.MATRICULA_REL 
    WHERE P.MATRICULA_REL = ?
    ORDER BY A.ASISTENCIA_REL DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$matricula]);

    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    file_put_contents("log_asistencia.txt", "🔸 Resultados obtenidos: " . count($datos) . "\n", FILE_APPEND);

    echo json_encode($datos);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de base de datos: " . $e->getMessage()]);
}
?>