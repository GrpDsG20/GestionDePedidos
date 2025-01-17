<?php
// Iniciar la sesión al principio del archivo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Obtener las ventas por día y mes
$sql = "SELECT DATE(fecha) as fecha, MONTH(fecha) as mes, YEAR(fecha) as anio, SUM(total) as total_dia 
        FROM ventas 
        GROUP BY fecha 
        ORDER BY fecha";
$result = $conn->query($sql);

$data = [];
$monthlySales = [];
$dates = [];
$totals = [];
foreach ($result as $row) {
    $data[] = [
        'fecha' => $row['fecha'],
        'mes' => $row['mes'],
        'anio' => $row['anio'],
        'total_dia' => (float)$row['total_dia']
    ];
    $dates[] = $row['fecha'];
    $totals[] = (float)$row['total_dia'];
    $monthlySales[$row['mes']][$row['anio']][] = (float)$row['total_dia'];
}

// Calcular la variación del día en comparación con el mes anterior
$variationIndicator = [];
for ($i = 1; $i < count($totals); $i++) {
    $change = (($totals[$i] - $totals[$i - 1]) / $totals[$i - 1]) * 100;
    $variationIndicator[] = round($change, 2);
}

// Obtener las ventas totales de hoy (sumando las ventas de la fecha actual)
$sqlHoy = "SELECT SUM(total) as total_dia FROM ventas WHERE DATE(fecha) = CURDATE()";
$resultHoy = $conn->query($sqlHoy);
$rowHoy = $resultHoy->fetch_assoc();
$ventasTotalesHoy = $rowHoy['total_dia'] ? (float)$rowHoy['total_dia'] : 0;

// Encontrar el mejor mes con las mayores ventas
$bestMonth = [];
foreach ($monthlySales as $month => $years) {
    foreach ($years as $year => $sales) {
        $totalMonth = array_sum($sales);
        if (!isset($bestMonth[$year]) || $totalMonth > $bestMonth[$year]['sales']) {
            $bestMonth[$year] = ['month' => $month, 'sales' => $totalMonth];
        }
    }
}

// Arreglo con los nombres de los meses en español
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Ventas</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

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

        #chart-container {
            width: 80%;
            margin: auto;
        }

        .btn {
            display: inline-block;
            margin: 20px;
            padding: 15px 25px;
            background-color: #000;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 18px;
            font-family: 'Poppins', sans-serif;
        }

        .btn:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        .stat-panel {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .stat-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 45%;
            text-align: center;
        }

        .stat-box p {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .stat-box .increase {
            color: green;
        }

        .stat-box .decrease {
            color: red;
        }

        .increase-arrow {
            color: green;
            font-size: 30px;
        }

        .decrease-arrow {
            color: red;
            font-size: 30px;
        }
    </style>
</head>
<body>
    <h1>Estadísticas de Ventas Diarias</h1>
    <div id="chart-container">
        <a href="ventas.php" class="btn">Volver a Ventas</a>
        <canvas id="ventasChart"></canvas>
    </div>

    <div class="stat-panel">
        <div class="stat-box">
            <p>Ventas Totales Hoy: $<?= number_format($ventasTotalesHoy, 0) ?></p>
        </div>
        <?php if (count($variationIndicator) > 0) : ?>
        <div class="stat-box">
            <p>Variación Hoy:
                <span class="<?= $variationIndicator[count($variationIndicator) - 1] >= 0 ? 'increase' : 'decrease' ?>">
                    <?= $variationIndicator[count($variationIndicator) - 1] ?>%
                </span>
                <span class="<?= $variationIndicator[count($variationIndicator) - 1] >= 0 ? 'increase-arrow' : 'decrease-arrow' ?>">
                    <?= $variationIndicator[count($variationIndicator) - 1] >= 0 ? '↑' : '↓' ?>
                </span>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <div class="stat-panel">
        <div class="stat-box">
            <p>Mejor Mes: <?= $meses[$bestMonth[date('Y')]['month']] ?> con $<?= number_format($bestMonth[date('Y')]['sales'], 0) ?> en ventas</p>
        </div>
    </div>

    <script>
        const data = <?= json_encode($data) ?>;

        const labels = data.map(item => item.fecha);
        const totals = data.map(item => item.total_dia);

        const ctx = document.getElementById('ventasChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas Diarias',
                    data: totals,
                    borderColor: '#4BC0C0',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#4BC0C0',
                    pointBorderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuad'
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Total: $${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        },
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Ventas ($)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
