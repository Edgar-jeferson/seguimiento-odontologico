<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db   = 'clinica_dental';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos obligatorios
    $campos = [
        'nombres', 'apellidos', 'dni', 'telefono', 'email', 'colegiatura',
        'especialidad', 'universidad', 'anio_graduacion', 'experiencia',
        'password', 'confirm_password'
    ];


    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            die("⚠️ Campo obligatorio faltante: $campo");
        }
    }

    // Recolectar datos
    $nombres     = trim($_POST['nombres']);
    $apellidos   = trim($_POST['apellidos']);
    $dni         = trim($_POST['dni']);
    $telefono    = trim($_POST['telefono']);
    $email       = trim($_POST['email']);
    $colegiatura = trim($_POST['colegiatura']);
    $especialidad = trim($_POST['especialidad']);
    $universidad = trim($_POST['universidad']);
    $anio        = trim($_POST['anio_graduacion']);
    $experiencia = trim($_POST['experiencia']);
    $password    = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];

    // Validaciones adicionales
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico no válido");
    }

    if (!preg_match('/^\d{8}$/', $dni)) {
        die("DNI inválido. Debe contener 8 dígitos.");
    }

    if (strlen($password) < 8) {
        die("La contraseña debe tener al menos 8 caracteres.");
    }

    if ($password !== $confirmPass) {
        die("Las contraseñas no coinciden.");
    }

    // Verificar si el correo ya está registrado
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        die("Este correo ya está registrado.");
    }

    // Hashear contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en tabla usuarios
    $stmt = $pdo->prepare("INSERT INTO usuarios (correo, contrasena, tipo) VALUES (?, ?, ?)");
    $tipo = 'odontologo';
    $stmt->execute([$email, $passwordHash, strtolower(trim($tipo))]);

    $usuario_id = $pdo->lastInsertId();
    // 🔽 AQUÍ ESTÁ BIEN COLOCADO


    // Crear directorio de uploads si no existe
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Manejo de archivos
    function guardarArchivo($campo, $prefijo, $uploadDir) {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
            die("Error al subir archivo: $campo");
        }

        $archivo = $_FILES[$campo];
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);

        if (strtolower($ext) !== 'pdf') {
            die("El archivo $campo debe ser PDF.");
        }

        $nombreUnico = $uploadDir . $prefijo . '_' . time() . '_' . basename($archivo['name']);
        if (!move_uploaded_file($archivo['tmp_name'], $nombreUnico)) {
            die("No se pudo guardar el archivo $campo.");
        }

        return $nombreUnico;
    }

    $cvPath = guardarArchivo('cv', 'cv', $uploadDir);
    $certPath = guardarArchivo('colegiatura_cert', 'cert', $uploadDir);

    // Insertar en tabla odontologos
        $stmt = $pdo->prepare("INSERT INTO odontologos (
        usuario_id, nombres, apellidos, dni, telefono, colegiatura, especialidad, 
        universidad, anio_graduacion, experiencia, cv_pdf, certificado_pdf
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


    $stmt->execute([
        $usuario_id, $nombres, $apellidos, $dni, $telefono, $colegiatura,
        $especialidad, $universidad, $anio, $experiencia, $cvPath, $certPath
    ]);

    // Redirección final
    header("Location: index.php?mensaje=odontologo_registrado");



    exit;
} else {
    die("Acceso denegado");
}
?>
