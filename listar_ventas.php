<?php
include 'conexion.php';

$result = $conn->query("SELECT * FROM ventas ORDER BY fecha DESC");

echo "<table border='1' style='width: 80%; margin: 20px auto; text-align: center;'>";
echo "<tr><th>Comprobante</th><th>Total</th><th>Fecha</th></tr>";

while ($row = $result->fetch_assoc()) {
  echo "<tr>
            <td>{$row['numero_comprobante']}</td>
            <td>\${$row['total']}</td>
            <td>{$row['fecha']}</td>
          </tr>";
}

echo "</table>";

$conn->close();
