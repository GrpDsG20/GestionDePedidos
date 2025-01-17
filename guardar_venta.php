<?php
include 'conexion.php'; // Conexión a tu base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $numero_comprobante = $data['numero_comprobante'];
    $total = $data['total'];

    // Asegúrate de que $total sea un número float sin formato adicional
    $total = floatval(str_replace('.', '', $total)); // Elimina cualquier punto (miles)

    $stmt = $conn->prepare("INSERT INTO ventas (numero_comprobante, total) VALUES (?, ?)");
    $stmt->bind_param("id", $numero_comprobante, $total);

    if ($stmt->execute()) {
        echo json_encode(['exito' => true]);
    } else {
        echo json_encode(['exito' => false, 'error' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
