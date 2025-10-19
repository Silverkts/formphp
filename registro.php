
<?php
include("conexion_bd.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['new-user']);
    $password = $_POST['new_password'];
    $confirm_password = $_POST['confirm-password'];

    // Validaciones básicas
    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }
    if (strlen($password) < 6) {
        echo "La contraseña debe tener al menos 6 caracteres.";
        exit;
    }

    // Verificar si el usuario ya existe
    $stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE nombre_usuario = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "El usuario ya existe.";
        mysqli_stmt_close($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $fecha_reg = date("Y-m-d");

    // Insertar en la BD
    $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre_usuario, contrasena, fecha_registro) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $fecha_reg);
    if (mysqli_stmt_execute($stmt)) {
        echo "Tu cuenta ha sido creada correctamente. <a href='index.php'>Inicia sesión</a>.";
    } else {
        echo "Ha ocurrido un error: " . mysqli_error($conexion);
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conexion);
?>