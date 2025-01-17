<?php
// Iniciar la sesión al principio del archivo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['accion'] === 'actualizar_cantidad') {
        $id = $data['id'];
        $cantidad = $data['cantidad'];
        $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
        echo json_encode(['exito' => true]);
        exit;
    }

    if ($data['accion'] === 'eliminar') {
        $id = $data['id'];
        unset($_SESSION['carrito'][$id]);
        echo json_encode(['exito' => true]);
        exit;
    }

    if ($data['accion'] === 'vaciar_carrito') {
        $_SESSION['carrito'] = [];
        echo json_encode(['exito' => true]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
            font-family: 'Poppins', sans-serif;

        }

        .btn {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
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

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="number"] {
            width: 60px;
            height: 35px;
            text-align: center;
            font-size: 1rem;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            outline: none;
            font-family: 'Poppins', sans-serif;
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

        .btn {
            padding: 10px 20px;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
    </style>
    <script>
        function actualizarCantidad(input) {
            const id = input.getAttribute('data-id');
            const cantidad = parseInt(input.value); // Asegúrate de que la cantidad sea un entero
            const precio = parseFloat(input.getAttribute('data-precio').replace('.', '').replace(',', '.')); // Convierte el precio al formato numérico

            // Realiza el cálculo del subtotal
            const subtotal = cantidad * precio;

            // Actualiza el subtotal en el DOM con el formato correcto
            document.querySelector(`[data-subtotal-id='${id}']`).innerText = `$${subtotal.toLocaleString('es-ES', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;

            // Llama a actualizar el total
            actualizarTotal();

            // Envía la actualización al servidor
            fetch('carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accion: 'actualizar_cantidad',
                        id: id,
                        cantidad: cantidad
                    })
                })
                .then(response => response.json())
                .catch(error => console.error('Error:', error));
        }

        function actualizarTotal() {
            let total = 0;
            // Recorre los subtotales y suma
            document.querySelectorAll('[data-subtotal]').forEach(subtotal => {
                // Limpia el formato y convierte el subtotal a un número
                const valor = parseFloat(subtotal.innerText.replace('$', '').replace('.', '').replace(',', '.'));
                total += valor;
            });

            // Actualiza el total general en el DOM con el formato correcto
            document.getElementById('total-general').innerText = `$${total.toLocaleString('es-ES', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
        }

        function eliminarDelCarrito(id) {
            fetch('carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accion: 'eliminar',
                        id: id
                    })
                })
                .then(response => response.json())
                .then(() => location.reload())
                .catch(error => console.error('Error:', error));
        }

        function vaciarCarrito() {
            fetch('carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accion: 'vaciar_carrito'
                    })
                })
                .then(response => response.json())
                .then(() => location.reload())
                .catch(error => console.error('Error:', error));
        }

        function imprimirFactura() {
            const items = Array.from(document.querySelectorAll('tbody tr')).map(row => {
                const cantidad = row.querySelector('input[type="number"]').value;
                const producto = row.querySelector('td:first-child').innerText;
                const precioUnit = row.querySelector('td:nth-child(2)').innerText.replace('$', '');
                const subtotal = row.querySelector('td[data-subtotal]').innerText.replace('$', '');
                return `${cantidad.padEnd(6)}${producto.padEnd(20)}${precioUnit.padStart(10)}${subtotal.padStart(10)}`;
            });

            const total = document.getElementById('total-general').innerText;
            const numeroFactura = Math.floor(Math.random() * 1000000); // Genera número de factura único

            // Guarda la venta en la base de datos
            fetch('guardar_venta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        numero_comprobante: numeroFactura,
                        total: total.replace('$', '').replace(',', '')
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.exito) {
                        fetch('carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                accion: 'vaciar_carrito'
                            })
                        }).then(() => {
                            alert('Venta confirmada con éxito');
                            window.location.href = 'carta.php';
                        }).catch(error => console.error('Error:', error));
                    } else {
                        alert('Error al registrar la venta');
                    }
                }).catch(error => console.error('Error:', error));

            // Mostrar e imprimir la factura
            const facturaHTML = `
<div id="factura-imprimir">
    <h3 style="text-align: center;">Restaurante - PASION DEL INKA</h3>
    <p style="text-align: center;">Victoria 141, Villa Alemana - Valparaiso</p>
    <p style="text-align: center;">Tel: +56 959501542 | pasiondelinka3@gmail.com</p>
    <p style="text-align: center;">RUC: 1234567894</p>
    <hr>
    <p>Comprobante de pago #${numeroFactura}</p>
    <p>Fecha: ${new Date().toLocaleDateString()} Hora: ${new Date().toLocaleTimeString()}</p>
    <hr>
    <pre>
Cant. Producto               P. Unit.  Subtotal
-----------------------------------------------
${items.join('\n')}
-----------------------------------------------
Total:                          ${total}
-----------------------------------------------
    </pre>
    <p style="text-align: center; margin-top: 10px; margin-bottom: 5px;">¡Gracias por preferirnos!</p>
    <p style="text-align: center; margin-top: 5px; margin-bottom: 20px;">Síguenos en Instagram: @pasiondelinka_cl</p>
</div>


        `;

            // Crear una ventana temporal para impresión
            const ventanaImpresion = window.open('', '', 'width=400,height=600');
            ventanaImpresion.document.write(`
            <html>
                <head>
                    <style>
                        @media print {
                            body {
                                margin: 0;
                                font-family: Arial, sans-serif;
                                font-size: 12px;
                            }
                            #factura-imprimir {
                                width: 80mm; /* Establecer el ancho de 80mm */
                                padding: 10px;
                                box-sizing: border-box;
                            }
                            h3, p {
                                margin: 0;
                                text-align: center;
                            }
                            pre {
                                font-family: monospace;
                            }
                        }
                    </style>
                </head>
                <body>${facturaHTML}</body>
            </html>
        `);
            ventanaImpresion.document.close();
            ventanaImpresion.focus();
            ventanaImpresion.print();
            ventanaImpresion.close();
        }
    </script>

</head>

<body>
    <h1>Gestionar Pedido</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                foreach ($_SESSION['carrito'] as $producto) {
                    // Asegúrate de que los valores no tengan separadores y sean numéricos.
                    $precio = floatval(str_replace(['.', ','], ['', '.'], $producto['precio']));
                    $cantidad = intval($producto['cantidad']);
                    $subtotal = $precio * $cantidad;

                    $total += $subtotal;

                    echo "<tr>
                    <td>{$producto['nombre']}</td>
                    <td>\$" . number_format($precio, 0, ',', '.') . "</td>
                    <td>
                        <input type='number' value='{$cantidad}' min='1'
                               data-id='{$producto['id']}' data-precio='{$precio}'
                               onchange='actualizarCantidad(this)'>
                    </td>
                    <td data-subtotal data-subtotal-id='{$producto['id']}'>\$" . number_format($subtotal, 0, ',', '.') . "</td>
                    <td>
                       <button class='btn' 
        onclick='eliminarDelCarrito({$producto['id']})' 
        style='background-color: #f44336;'>
    Eliminar
</button>

                    </td>
                  </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>El carrito está vacío</td></tr>";
            }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total:</td>
                <td id="total-general">$<?php echo number_format($total, 0); ?></td>
                <td>
                    <button class="btn" onclick="vaciarCarrito()">Vaciar Carrito</button>
                </td>
            </tr>
        </tfoot>
    </table>
    <div style="text-align: center; margin-top: 20px;">
        <button class="btn" onclick="imprimirFactura()" style="background-color: #f44336;">Confirmar pedido</button>
        <button class="btn" onclick="window.history.back()">Seguir Agregando</button>
    </div>
</body>

</html>