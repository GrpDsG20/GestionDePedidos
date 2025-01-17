<?php
// Iniciar la sesión al principio del archivo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Obtener productos agrupados por categoría
$sql = "
    SELECT p.*, c.nombre AS categoria_nombre, c.orden
    FROM productos p
    JOIN categoria c ON p.categoria_id = c.id
    ORDER BY c.orden, c.nombre, p.nombre        
";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta Digital</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos generales */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            text-align: center;
            color: #333;
        }

        /* Botones */
        .btn-container {
            text-align: center;
            margin: 10px 0;
            font-family: 'Poppins', sans-serif;
        }

        .btn {
            display: inline-block;
            margin: 5px;
            padding: 10px 25px;
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

        .btn:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        /* Contenedor de la carta en formato A4 */
        .carta-container {
            width: 210mm;
            /* Ancho A4 */
            margin: 0 auto;
            padding: 10mm 15mm;
            /* Padding de 20mm arriba y abajo */
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-sizing: border-box;
            overflow: visible;
            font-family: 'Poppins', sans-serif;
        }

        h1 {

            text-align: center;
            color: #222;
            padding: 50px 0 20px 0;
            margin: auto;

        }

        .productos-categoria {
            margin-top: 4mm;
            display: flex;
            flex-direction: column;
            margin-bottom: 10mm;
        }

        .categoria-titulo {
            font-size: 1.2em;
            color:#000;
            margin-bottom: 5mm;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }

        .producto {
            display: flex;
            flex-direction: column;
            /* Cambiar de fila a columna */
            justify-content: flex-start;
            margin: 2mm 0;
            line-height: 1.3;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
            page-break-inside: avoid;
            /* Evitar que los productos se corten en medio */
        }

        /* Fila para nombre y precio */
        .producto-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        .nombre {
            font-weight: bold;
            text-align: left;
            flex: 0.6;
            font-size: 1rem;
        }

        .precio {
            font-weight: bold;
            color: #000;
            text-align: right;
            font-size: 1rem;
            flex: 0.3;
        }

        /* Descripción debajo de nombre y precio */
        .descripcion {
            font-size: 11px;
            color: #555;
            margin-top: 2mm;
            text-align: left;
            font-size: 14px;
            width: 60%;
        }

        /* Estilos para impresión */
        @media print {
            body * {
                visibility: hidden;
            }

            .carta-container,
            .carta-container * {
                visibility: visible;
            }

            .carta-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 210mm;
                /* A4 */
                height: auto;
                /* Ajuste la altura */
                margin: 0;
                overflow: visible;
            }

            @page {
                size: A4;
                margin: 0;
                padding-top: 20mm;
                padding-bottom:10mm;
            }

            @page :first {
                padding-top: 0mm;
            }

            .productos-categoria {
                page-break-before: auto;
            }

            .producto {
                page-break-inside: avoid;
            }

            .producto:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>

    <h1>Carta Digital</h1>

    <!-- Botones -->
    <div class="btn-container">
        <a href="ventas.php" class="btn">Atrás</a>
        <a href="agregar_categoria.php" class="btn">Agregar Categoría</a>
        <button class="btn" onclick="imprimirCarta()">Imprimir</button>
    </div>

    <!-- Contenedor A4 -->
    <div class="carta-container">
        <?php
        if ($resultado->num_rows > 0) {
            $categoria_actual = "";

            while ($producto = $resultado->fetch_assoc()) {
                if ($producto['categoria_nombre'] !== $categoria_actual) {
                    $categoria_actual = $producto['categoria_nombre'];
                    echo "<h2 class='categoria-titulo'>" . htmlspecialchars($categoria_actual) . "</h2>";
                    echo "<div class='productos-categoria'>";
                }

                // Mostrar el producto con nombre y precio en una fila, descripción debajo
                echo "<div class='producto'>";
                echo "<div class='producto-header'>";
                echo "<p class='nombre'>" . htmlspecialchars($producto['nombre']) . "</p>";
                echo "<p class='precio'>$" . number_format($producto['precio'], 0, '.', ',') . "</p>";
                echo "</div>";
                echo "<p class='descripcion'>" . htmlspecialchars($producto['descripcion']) . "</p>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p>No hay platos disponibles.</p>";
        }
        ?>

    </div>

    <script>
    function imprimirCarta() {
        window.print();
    }
</script>


</body>

</html>