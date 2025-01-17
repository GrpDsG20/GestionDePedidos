<?php
session_start();
if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'pasioninka');

    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }

    // Verificar el usuario
    $sql = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contrasena = MD5(?)");
    $sql->bind_param("ss", $usuario, $contrasena);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['usuario'] = $usuario;
        header("Location: Carta.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pasión del Inka</title>
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

        .login-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 70%;
            max-width: 400px;
            height: 400px;
            text-align: center;
        }

        .login-container h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #222;
            font-family: 'Poppins', sans-serif;
        }

        .login-container .subtitle {
            font-size: 18px;
            margin-bottom: 30px;
            color: #666;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: center;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
            outline: none;
            box-sizing: border-box;
        }

        .input-group .icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            font-size: 22px;
            color: #888;
        }

        .login-container button {
            width: 100%;
            background-color: #000;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #333;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Pasión del Inka</h1>
        <p class="subtitle">Bienvenido, por favor inicia sesión</p>
        <form method="POST">
            <div class="input-group">
                <i class='bx bx-user icon'></i>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" required>
            </div>
            <div class="input-group">
                <i class='bx bx-lock-alt icon'></i>
                <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required>
            </div>
            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
    </div>
</body>

</html>