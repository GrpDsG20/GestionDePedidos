<?php
// Iniciar la sesión al principio del archivo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Agregar una nueva categoría
if (isset($_POST['agregar_categoria'])) {
    $nombre_categoria = $_POST['nombre_categoria'];
    $orden = $_POST['orden_categoria'];

    // Verificar si el número de orden ya existe
    $sql_check_orden = "SELECT * FROM categoria WHERE orden = '$orden'";
    $resultado_check_orden = $conn->query($sql_check_orden);

    if ($resultado_check_orden->num_rows > 0) {
        echo "<script>alert('Error: El número de orden ya existe. Prueba agregar un número.');</script>";
    } else {
        // Verificar si la categoría ya existe
        $sql_check = "SELECT * FROM categoria WHERE nombre = '$nombre_categoria'";
        $resultado_check = $conn->query($sql_check);

        if ($resultado_check->num_rows > 0) {
            echo "<script>alert('Error: La categoría ya existe.');</script>";
        } else {
            // Insertar nueva categoría
            $sql_insert = "INSERT INTO categoria (nombre, orden) VALUES ('$nombre_categoria', '$orden')";
            if ($conn->query($sql_insert)) {
                // Redirigir después de agregar la categoría
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error al agregar categoría: " . $conn->error;
            }
        }
    }
}

// Editar una categoría
if (isset($_POST['editar_categoria'])) {
    $id_categoria = $_POST['id_categoria'];
    $nombre_categoria = $_POST['nombre_categoria'];
    $orden = $_POST['orden_categoria'];

    // Verificar si el número de orden ya existe para otras categorías
    $sql_check_orden = "SELECT * FROM categoria WHERE orden = '$orden' AND id != $id_categoria";
    $resultado_check_orden = $conn->query($sql_check_orden);

    if ($resultado_check_orden->num_rows > 0) {
        echo "<script>alert('Error: El número de orden ya existe.');</script>";
    } else {
        // Actualizar la categoría
        $sql_update = "UPDATE categoria SET nombre = '$nombre_categoria', orden = '$orden' WHERE id = $id_categoria";
        if ($conn->query($sql_update)) {
            echo "<script>alert('Categoría actualizada con exito.');</script>";
        } else {
            echo "Error al actualizar categoría: " . $conn->error;
        }
    }
}

// Eliminar una categoría
if (isset($_GET['eliminar_categoria'])) {
    $id_categoria = $_GET['eliminar_categoria'];

    // Eliminar la categoría
    $sql_delete = "DELETE FROM categoria WHERE id = $id_categoria";
    if ($conn->query($sql_delete)) {
        echo "<script>alert('Categoría eliminada con exito.');</script>";
    } else {
        echo "Error al eliminar categoría: " . $conn->error;
    }
}

// Obtener categorías para mostrar en la tabla
$sql = "SELECT * FROM categoria ORDER BY orden ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Gestionar Categorías</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }

        h1 {
            text-align: center;
            color: #333;
            padding: 30px 0;
            margin: auto;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: 0 auto;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            background-color: #000;
            color: white;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
        }

        .btn:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        .table-container {
            width: 80%;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            margin-top: 20px;
        }

        th,
        td {
            padding: 15px;
            text-align: center;
            /* Centra el texto dentro de las celdas */
            border: 1px solid #ddd;
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
        }

        th {
            background-color: #000;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        .btn-editar,
        .btn-eliminar {
            display: inline-block;
            padding: 10px 25px;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            background: #000;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .btn-eliminar:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-editar:hover {
            transform: scale(1.05);
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-eliminar {
            background-color: #dc3545;
        }

        .formulario input {
            width: 250px;
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .formulario input:focus {
            box-shadow: 0 0 5px rgba(72, 73, 72, 0.5);
        }

        .table-container input {
            width: 150px;
            font-family: 'Poppins', sans-serif;
            padding: 8px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            margin-right: 10px;
            /* Espaciado entre inputs y botón */
        }

        .table-container input:focus {
            box-shadow: 0 0 5px rgba(72, 73, 72, 0.5);
        }
    </style>

</head>

<body>

    <h1>Gestionar Categorías</h1>
    <br>
    <div class="header-container">
        <a href="carta_digital.php" class="btn">
            <i class="fa-solid fa-list"></i> Carta
        </a>
        <form method="POST" class="formulario" style="display: flex; gap: 10px;">
            <input type="text" name="nombre_categoria" placeholder="Nombre de la categoría" required>
            <input type="number" name="orden_categoria" placeholder="Orden (número único)" required>
            <button type="submit" name="agregar_categoria" class="btn">Agregar
            </button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($categoria = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $categoria['nombre']; ?></td>
                        <td><?php echo $categoria['orden']; ?></td>
                        <td>
                            <form style="display:inline;" method="POST">
                                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id']; ?>">
                                <input type="text" name="nombre_categoria" value="<?php echo $categoria['nombre']; ?>" required>
                                <input type="number" name="orden_categoria" value="<?php echo $categoria['orden']; ?>" required>
                                <button type="submit" name="editar_categoria" class="btn-editar">Actualizar</button>
                            </form>
                            <a href="?eliminar_categoria=<?php echo $categoria['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>

</html>