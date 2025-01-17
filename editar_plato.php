<?php
// Iniciar la sesión al principio del archivo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Obtener las categorías de la base de datos
$sqlCategorias = "SELECT id, nombre FROM categoria";
$resultadoCategorias = $conn->query($sqlCategorias);

// Obtener el ID del plato a editar (supongo que el ID se pasa como parámetro en la URL)
if (isset($_GET['id'])) {
    $id_plato = $_GET['id'];
    // Obtener los datos actuales del plato
    $sqlPlato = "SELECT * FROM productos WHERE id = $id_plato";
    $resultadoPlato = $conn->query($sqlPlato);
    
    if ($resultadoPlato->num_rows > 0) {
        $plato = $resultadoPlato->fetch_assoc();
    } else {
        echo "<script>alert('Plato no encontrado'); window.location.href = 'carta.php';</script>";
        exit();
    }
}

// Procesar el formulario de editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $categoria_id = $_POST['categoria']; // Obtener la categoría seleccionada

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && !empty($precio) && !empty($descripcion) && !empty($categoria_id)) {
        // Actualizar el plato en la base de datos
        $sql = "UPDATE productos SET nombre = '$nombre', precio = '$precio', descripcion = '$descripcion', categoria_id = '$categoria_id' WHERE id = $id_plato";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('Plato actualizado con éxito');
                window.location.href = 'carta.php';
            </script>";
        } else {
            echo "<script>alert('Error al actualizar el plato: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Todos los campos son obligatorios');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plato - Pasión del Inka</title>
    <link rel="icon" type="image/png" href="logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            padding: 20px 40px;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }

        .form-container h1 {
            font-size: 30px;
            padding: 0 0 20px 0;
            color: #222;
        }

        .form-container label {
            font-size: 1rem;
            font-weight: 500;
            color: #555;
            text-align: left;
            display: block;
            margin-top: 10px;
        }

        .form-container input,
        .form-container textarea,
        .form-container button,
        .form-container select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
            outline: none;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .form-container select {
            background-color: #f9f9f9;
        }

        .form-container textarea {
            resize: none;
        }

        .form-container button {
            background-color: #000;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #333;
        }

        .form-container .btn-cancel {
            background-color: #f44336;
        }

    </style>
</head>

<body>
    <div class="form-container">
        <h1>Editar Plato</h1>
        <form method="POST" action="editar_plato.php?id=<?php echo $id_plato; ?>">
            <label for="nombre">Nombre del Plato</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $plato['nombre']; ?>" required>

            <label for="precio">Precio del Plato</label>
            <input type="number" id="precio" name="precio" value="<?php echo $plato['precio']; ?>" step="0.01" required>

            <label for="descripcion">Descripción del Plato</label>
            <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $plato['descripcion']; ?></textarea>

            <label for="categoria">Categoría del Plato</label>
            <select id="categoria" name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php
                if ($resultadoCategorias->num_rows > 0) {
                    while ($categoria = $resultadoCategorias->fetch_assoc()) {
                        $selected = ($categoria['id'] == $plato['categoria_id']) ? 'selected' : '';
                        echo "<option value='{$categoria['id']}' $selected>{$categoria['nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay categorías disponibles</option>";
                }
                ?>
            </select>

            <button type="submit">Actualizar Plato</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='carta.php';">Cancelar</button>
        </form>
    </div>
</body>

</html>
