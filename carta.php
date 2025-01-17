<?php
include 'conexion.php';

// Obtener productos de la base de datos
$sql = "SELECT * FROM productos";
$resultado = $conn->query($sql);

// Procesar la acción de agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data['accion'] === 'agregar_carrito') {
        session_start();
        $id = $data['id'];
        $nombre = $data['nombre'];
        $precio = $data['precio'];

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Verificar si el producto ya está en el carrito
        if (isset($_SESSION['carrito'][$id])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Plato ya agregado']);
            exit;
        }

        // Agregar producto al carrito
        $_SESSION['carrito'][$id] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => 1
        ];

        echo json_encode(['exito' => true, 'mensaje' => 'Plato agregado con exito']);
        exit;
    }
}
// Procesar la acción de eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']); // Sanitizar entrada
    $sqlEliminar = "DELETE FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sqlEliminar);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo "<script>alert('Plato eliminado con exito'); window.location.href='carta.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el plato'); window.location.href='carta.php';</script>";
    }

    $stmt->close();
    $conn->close();
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Gestión de Pedidos</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color:#222;

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

        .btn-header {
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

        .btn:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-header:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        table {
            width: 90%;
            margin: 30px auto;
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

        }

        th {
            background-color: #000;
            color: white;

        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn-small {
            padding: 10px 15px;
            font-size: 18px;
            font-family: 'Poppins', sans-serif;
        }

        #header-container {
            width: 90%;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #search-box {
            flex-grow: 1;
            display: flex;
            justify-content: center;
        }

        #search-input {
            width: 80%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            outline: none;
            /* Elimina el contorno predeterminado */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;

        }

        #search-input:focus {

            box-shadow: 0 0 5px rgba(72, 73, 72, 0.5);

        }

        /* Modal */
        #modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            color: #333;
        }

        #modal p {
            margin: 20px 0;
        }

        /* Modal overlay */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        h1 {
            padding: 20px 0px;
        }
    </style>
</head>

<body>

    <h1>Gestión de Pedidos</h1>

    <div id="header-container">
        <a href="agregar_plato.php" class="btn-header">Agregar Plato</a>
        <div id="search-box">
            <input type="text" id="search-input" onkeyup="buscarProductos()" placeholder="Buscar platos...">
        </div>
        <a href="ventas.php" class="btn-header">Ventas</a>
        <a href="carrito.php" class="btn-header"><i class="fa-solid fa-cart-shopping"></i></a>
        <a href="logout.php" class="btn-header" style='font-size: 20px;' onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?');">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>

    </div>

    <table id="productos-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultado->num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    // Formatear el precio antes de imprimirlo
                    $precioFormateado = number_format((float)$producto['precio'], 0, '', '.');

                    echo "<tr>
                    <td>{$producto['nombre']}</td>
                    <td>\${$precioFormateado}</td>
                    <td>{$producto['descripcion']}</td>
                    <td>
                        <a href='editar_plato.php?id={$producto['id']}' class='btn btn-small'>
                         <i class='fa-regular fa-pen-to-square'></i></a>
                        <button class='btn btn-small' style='background-color:#f44336;' onclick=\"confirmarEliminacion('{$producto['id']}')\">
                        <i class='fa-regular fa-trash-can'></i>
                        </button>
                        <button class='btn btn-small' onclick=\"agregarCarrito('{$producto['id']}', '{$producto['nombre']}', {$producto['precio']})\">
                        <i class='fa-solid fa-check'></i>
                        </button>
                    </td>
                  </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No hay productos disponibles</td></tr>";
            }
            ?>
        </tbody>

    </table>

    <div id="modal">
        <p id="modal-message"></p>
    </div>
    <div id="overlay"></div>

    <script>
        function agregarCarrito(id, nombre, precio) {
            fetch('carta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accion: 'agregar_carrito',
                        id: id,
                        nombre: nombre,
                        precio: precio
                    })
                })
                .then(response => response.json())
                .then(data => {
                    mostrarModal(data.mensaje);
                })
                .catch(error => console.error('Error:', error));
        }

        function mostrarModal(mensaje) {
            const modal = document.getElementById('modal');
            const overlay = document.getElementById('overlay');

            // Mostrar el modal con mensaje
            document.getElementById('modal-message').innerText = mensaje;
            modal.style.display = 'block';
            overlay.style.display = 'block';

            // Ocultar el modal después de 2 segundos
            setTimeout(() => {
                modal.style.display = 'none';
                overlay.style.display = 'none';
            }, 1000);
        }

        function buscarProductos() {
            const input = document.getElementById('search-input');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#productos-table tbody tr');

            rows.forEach(row => {
                const nombreProducto = row.querySelector('td:first-child').innerText.toLowerCase();
                if (nombreProducto.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function confirmarEliminacion(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este plato?")) {
                window.location.href = `carta.php?eliminar=${id}`;
            }
        }
    </script>
</body>

</html>