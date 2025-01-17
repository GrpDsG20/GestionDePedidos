<?php
// Iniciar la sesión al principio del archivo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Procesar eliminación de venta
if (isset($_POST['eliminar_venta'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Filtros de ventas
$diaFiltro = $_GET['dia'] ?? '';
$mesFiltro = $_GET['mes'] ?? '';
$anioFiltro = $_GET['anio'] ?? '';

// Consultar ventas
$sql = "SELECT * FROM ventas";
$filters = [];
$params = [];

if ($anioFiltro) {
    $filters[] = "YEAR(fecha) = ?";
    $params[] = $anioFiltro;
}
if ($mesFiltro) {
    $filters[] = "MONTH(fecha) = ?";
    $params[] = $mesFiltro;
}
if ($diaFiltro) {
    $filters[] = "DAY(fecha) = ?";
    $params[] = $diaFiltro;
}

if (count($filters) > 0) {
    $sql .= " WHERE " . implode(" AND ", $filters);
}

// Agregar ordenación por fecha (más reciente primero)
$sql .= " ORDER BY fecha DESC";

$stmt = $conn->prepare($sql);

if (count($params) > 0) {
    $types = str_repeat("i", count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Calcular el total
$total = 0;
$ventas = [];
while ($venta = $resultado->fetch_assoc()) {
    $ventas[] = $venta;
    $total += (float)$venta['total'];
}

// Obtener listas de días, meses y años
$dias = range(1, 31);
$meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];
$stmt = $conn->query("SELECT DISTINCT YEAR(fecha) as anio FROM ventas ORDER BY anio DESC");
$anios = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Ventas</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #222;
            padding: 30px 0;
            margin: auto;
        }

        .btn {
            display: inline-block;
            margin: 5px;
            padding: 15px 25px;
            background-color: #000;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
        }

        .btn-small-delete {
            padding: 10px 15px;
            font-size: 1rem;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
        }

        th {
            background-color: #000;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px auto;
            width: 90%;
            font-family: 'Poppins', sans-serif;
        }

        #filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
        }

        #filter-form select {
            padding: 5px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        #filter-form button {
            padding: 8px 18px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: #fff;
            background: #000;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #000;
            color: white;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        #total-container {
            width: 80%;
            margin: 25px auto;
            text-align: right;
            font-size: 18px;
            color: #333;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>
    <h1>Historial de Ventas</h1>

    <div id="header-container">
        <a href="carta.php" class="btn"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="button-group">
            <form id="filter-form" method="get">
                <select name="dia">
                    <option value="">Día</option>
                    <?php foreach ($dias as $dia): ?>
                        <option value="<?= $dia ?>" <?= $diaFiltro == $dia ? 'selected' : '' ?>><?= $dia ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="mes">
                    <option value="">Mes</option>
                    <?php foreach ($meses as $numMes => $nombreMes): ?>
                        <option value="<?= $numMes ?>" <?= $mesFiltro == $numMes ? 'selected' : '' ?>><?= $nombreMes ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="anio">
                    <option value="">Año</option>
                    <?php foreach ($anios as $anio): ?>
                        <option value="<?= $anio['anio'] ?>" <?= $anioFiltro == $anio['anio'] ? 'selected' : '' ?>><?= $anio['anio'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-small">Filtrar</button>
            </form>
            <a href="ventas_estadisticas.php" class="btn">
                <i class="fa-solid fa-chart-line"></i>
                Estadísticas
            </a>
            <a href="carta_digital.php" class="btn">
                <i class="fa-solid fa-list"></i>
                Carta
            </a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Número de Comprobante</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($ventas) > 0): ?>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['numero_comprobante']) ?></td>
                        <td>$<?= number_format((float)$venta['total'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($venta['fecha']) ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($venta['id']) ?>">
                                <button type="submit"
                                    name="eliminar_venta"
                                    class="btn-small-delete"
                                    style="background-color: #f44336;"
                                    onclick="return confirm('¿Estás seguro que deseas eliminar esta venta?');">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay ventas registradas para los filtros seleccionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="total-container">
        <strong>Total: $<?= number_format($total, 0, ',', '.') ?></strong>
    </div>
</body>

</html>
