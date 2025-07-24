<?php
session_start();

$host = 'localhost';
$db = 'clinica_dental';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    // Redirigir con error
    header("Location: index.php?error=conexion");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['userType'] ?? '';
    $correo = $_POST['email'] ?? '';
    $contrasena = $_POST['password'] ?? '';

    // Validar campos
    if (empty($tipo) || empty($correo) || empty($contrasena)) {
        header("Location: index.php?error=campos");
        exit;
    }

    // Verificar existencia del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: index.php?error=correo");
        exit;
    }

    // Validar tipo
    if ($usuario['tipo'] !== $tipo) {
        header("Location: index.php?error=tipo");
        exit;
    }

    // Validar contraseña
    if (!password_verify($contrasena, $usuario['contrasena'])) {
        header("Location: index.php?error=clave");
        exit;
    }

    // Autenticación correcta → Guardar sesión
    $_SESSION['userType'] = $usuario['tipo'];
    $_SESSION['userEmail'] = $usuario['correo'];

    // Redirigir según tipo (versión robusta)
$tipoUsuario = strtolower(trim($usuario['tipo']));  // normaliza el valor

switch ($tipoUsuario) {
    case 'paciente':
        header("Location: paciente-dashboard.php");
        exit;
    case 'odontologo':
        header("Location: odontologo-dashboard.html");
        exit;
    case 'administrador':
        header("Location: admin-dashboard.html");
        exit;
    default:
        header("Location: index.php?error=desconocido");
        exit;
}

}
?>
