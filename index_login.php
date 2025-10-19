 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Registro y Login</title>
    <link rel="stylesheet" type="text/css" href="Style.css">
</head>
<body>
    <form action="conexión_bd.php" method="post">
    <div class="container">
        <div id="register-form" class="form-container">
            <h2>Crear Cuenta</h2>
            <div id="register-message" class="message hidden"></div>
            
            <div class="form-group">
                <label for="new-username">Usuario:</label>
                <input type="text" id="new-username" name="new-user" placeholder="Elige un nombre de usuario" required>
            </div>
            
            <div class="form-group">
                <label for="new-password">Contraseña:</label>
                <input type="password" id="new-password" name="new_password" placeholder="Crea una contraseña segura" required>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirmar Contraseña:</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite tu contraseña" required>
            </div>
            
            <button type="button" name ="registro" onclick="register()">Crear Cuenta</button>
            
            <div class="toggle-form">
                <span>¿Ya tienes cuenta? </span>
                <button type="button" class="toggle-btn" onclick="showLogin()">Iniciar Sesión</button>
            </div>
        </div>
        </form>
        <form action="registro.php" method="post">
        <div id="login-form" class="form-container hidden">
            <h2>Iniciar Sesión</h2>
            <div id="login-message" class="message hidden"></div>
            
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" placeholder="Ingresa tu usuario" name ="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" placeholder="Ingresa tu contraseña" name="Password" required>
            </div>
            
            <button type="button" name ="registrar " onclick="login()">Iniciar Sesión</button>
            
            <div class="toggle-form">
                <span>¿No tienes cuenta? </span>
                <button type="button" class="toggle-btn" onclick="showRegister()">Registrarse</button>
            </div>
        </div>

        <div id="welcome-screen" class="form-container hidden">
            <h2>¡Bienvenido!</h2>
            <p id="welcome-text" style="text-align: center; margin: 20px 0;">
                <?php echo htmlspecialchars($mensaje); ?></p> 
                <!-- Muestra el mensaje simple de PHP -->
            <button type="button" onclick="logout()">Cerrar Sesión</button>
        </div>
    </div>
    </form>
<?php
include("conexion_bd.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "Usuario y contraseña son obligatorios.";
        exit;
    }

    // Verificar usuario y contraseña
    $stmt = mysqli_prepare($conexion, "SELECT contrasena FROM usuarios WHERE nombre_usuario = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashed_password);
    if (mysqli_stmt_fetch($stmt) && password_verify($password, $hashed_password)) {
        $_SESSION['username'] = $username;
        header("Location: index.php"); // Redirige a la página principal
        exit;
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conexion);
?> 
    <script>
        let users = JSON.parse(localStorage.getItem('users')) || {};

        function showRegister() {
            document.getElementById('register-form').classList.remove('hidden');
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('welcome-screen').classList.add('hidden');
            clearMessages();
        }

        function showLogin() {
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('welcome-screen').classList.add('hidden');
            clearMessages();
        }

        function showWelcome(username) {
            document.getElementById('welcome-screen').classList.remove('hidden');
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('welcome-text').textContent = `Hola ${username}, ¡has iniciado sesión correctamente!`;
        }

        function clearMessages() {
            document.getElementById('register-message').classList.add('hidden');
            document.getElementById('login-message').classList.add('hidden');
        }

        function showSuccess(message, elementId) {
            const messageElement = document.getElementById(elementId);
            messageElement.textContent = message;
            messageElement.className = 'message success';
            messageElement.classList.remove('hidden');
        }

        function showError(message, elementId) {
            const messageElement = document.getElementById(elementId);
            messageElement.textContent = message;
            messageElement.className = 'message error';
            messageElement.classList.remove('hidden');
        }

        function register() {
            const username = document.getElementById('new-username').value;
            const password = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (!username || !password || !confirmPassword) {
                showError('Todos los campos son obligatorios', 'register-message');
                return;
            }

            if (password !== confirmPassword) {
                showError('Las contraseñas no coinciden', 'register-message');
                return;
            }

            if (password.length < 6) {
                showError('La contraseña debe tener al menos 6 caracteres', 'register-message');
                return;
            }

            if (users[username]) {
                showError('El usuario ya existe', 'register-message');
                return;
            }

            users[username] = password;
            localStorage.setItem('users', JSON.stringify(users));

            showSuccess('¡Cuenta creada exitosamente! Ahora puedes iniciar sesión', 'register-message');
            
            document.getElementById('new-username').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-password').value = '';

            setTimeout(showLogin, 2000);
        }

        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if (!username || !password) {
                showError('Usuario y contraseña son obligatorios', 'login-message');
                return;
            }

            if (!users[username]) {
                showError('Usuario no encontrado', 'login-message');
                return;
            }

            if (users[username] !== password) {
                showError('Contraseña incorrecta', 'login-message');
                return;
            }

            showWelcome(username);
            
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        }

        function logout() {
            showLogin();
        }

        showRegister();
    </script>
</body>
</html>