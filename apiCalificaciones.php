<?php
header("Content-Type: application/json");
require_once("funciones/fxGeneral.php");

if (!isset($_GET['matricula'])) {
    echo json_encode(["error" => "Falta el parámetro 'matricula'"]);
    exit;
}

$matricula = $_GET['matricula'];
$modo = isset($_GET['modo']) ? $_GET['modo'] : '';

try {
    $conn = fxAbrirConexion();

    if ($modo === 'historico') {
        // Segunda sentencia (histórico)
        $sql = "SELECT 
                    c.CALIFICACION_REL, 
                    m.MATRICULA_REL,
                    a.CODIGO_060,
                    CONCAT(
                        a.NOMBRE_060, ' -',
                        CASE cal.TURNO_160
                            WHEN 1 THEN 'Diurno'
                            WHEN 2 THEN 'Matutino'
                            WHEN 3 THEN 'Vespertino'
                            WHEN 4 THEN 'Nocturno'
                            WHEN 5 THEN 'Sabatino'
                            WHEN 6 THEN 'Dominical'
                            ELSE 'Desconocido'
                        END, ', ', cal.ANNO_160, '-'
                    ) AS ASIGNATURA_TURNO,
                    CONCAT(e.NOMBRE1_010, ' ', e.NOMBRE2_010, ' ', e.APELLIDO1_010, ' ', e.APELLIDO2_010) AS NOMBRE_COMPLETO,
                    c.NOTA_161,
                    cal.SEMESTRE_160,
                    cal.PARCIAL_160
                FROM 
                    UMO161A c
                JOIN 
                    UMO030A m ON c.MATRICULA_REL = m.MATRICULA_REL
                JOIN 
                    UMO010A e ON m.ESTUDIANTE_REL = e.ESTUDIANTE_REL
                JOIN 
                    UMO160A cal ON c.CALIFICACION_REL = cal.CALIFICACION_REL
                JOIN 
                    UMO060A a ON cal.ASIGNATURA_REL = a.ASIGNATURA_REL
                WHERE  
                    m.MATRICULA_REL LIKE :matricula
                ORDER BY 
                    c.CALIFICACION_REL DESC";
    } else {
        // Primera sentencia (actual)
        $sql = "SELECT 
                    m.MATRICULA_REL, 
                    a.CODIGO_060,
                    a.NOMBRE_060 AS ASIGNATURA,
                    CONCAT(e.NOMBRE1_010, ' ', e.NOMBRE2_010, ' ', e.APELLIDO1_010, ' ', e.APELLIDO2_010) AS NOMBRE_COMPLETO,
                    cal.SEMESTRE_160,
                    MAX(CASE WHEN cal.PARCIAL_160 = 1 THEN c.NOTA_161 END) AS PARCIAL1,
                    MAX(CASE WHEN cal.PARCIAL_160 = 2 THEN c.NOTA_161 END) AS PARCIAL2,
                    MAX(CASE WHEN cal.PARCIAL_160 = 0 THEN c.NOTA_161 END) AS PARCIAL3
                FROM 
                    UMO161A c
                JOIN 
                    UMO030A m ON c.MATRICULA_REL = m.MATRICULA_REL
                JOIN 
                    UMO010A e ON m.ESTUDIANTE_REL = e.ESTUDIANTE_REL
                JOIN 
                    UMO160A cal ON c.CALIFICACION_REL = cal.CALIFICACION_REL
                JOIN 
                    UMO060A a ON cal.ASIGNATURA_REL = a.ASIGNATURA_REL
                WHERE  
                    m.MATRICULA_REL LIKE :matricula
                GROUP BY 
                    m.MATRICULA_REL, a.CODIGO_060, a.NOMBRE_060, NOMBRE_COMPLETO, cal.SEMESTRE_160
                ORDER BY 
                    a.NOMBRE_060, cal.SEMESTRE_160";
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':matricula' => "%$matricula%"
    ]);

    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($datos);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de base de datos: " . $e->getMessage()]);
}