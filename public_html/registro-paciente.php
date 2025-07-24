<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$db = 'clinica_dental';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Datos del formulario
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $fechaNacimiento = $_POST['fecha-nacimiento'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $estadoCivil = $_POST['estado-civil'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $telefonoEmergencia = $_POST['telefono-emergencia'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $distrito = $_POST['distrito'] ?? '';
    $provincia = $_POST['provincia'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    // 2. Contacto de emergencia
    $emergencia_nombre = $_POST['emergencia-nombre'] ?? '';
    $emergencia_parentesco = $_POST['emergencia-parentesco'] ?? '';
    $emergencia_telefono = $_POST['emergencia-telefono'] ?? '';
    $emergencia_email = $_POST['emergencia-email'] ?? '';

    // 3. Datos médicos
    $tipo_sangre = $_POST['tipo-sangre'] ?? '';
    $seguro_medico = $_POST['seguro-medico'] ?? '';
    $alergias = $_POST['alergias'] ?? '';
    $medicamentos = $_POST['medicamentos'] ?? '';
    $enfermedades = $_POST['enfermedades'] ?? '';

    // 4. Historial odontológico
    $ultima_consulta = $_POST['ultima-consulta'] ?? null;
    $motivo_consulta = $_POST['motivo-consulta'] ?? '';
    $problemas_dentales = $_POST['problemas-dentales'] ?? '';

    // 5. Hábitos de higiene oral (checkboxes)
    $cepillado_diario = isset($_POST['cepillado-diario']) ? 1 : 0;
    $uso_hilo = isset($_POST['uso-hilo']) ? 1 : 0;
    $enjuague_bucal = isset($_POST['enjuague-bucal']) ? 1 : 0;

    // 6. Consentimientos
    $consentimiento_datos = isset($_POST['privacy']) ? 1 : 0;
    $acepta_terminos = isset($_POST['terms']) ? 1 : 0;
    $consentimiento_medico = isset($_POST['medical-consent']) ? 1 : 0;

    // Validación de contraseña
    if ($password !== $confirmPassword) {
        die("❌ Las contraseñas no coinciden.");
    }

    // Verificar si el correo ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("❌ El correo ya está registrado.");
    }

    try {
        $pdo->beginTransaction();

        // Crear usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (tipo, correo, contrasena) VALUES (?, ?, ?)");
        $stmt->execute([
            'paciente',
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
        $usuario_id = $pdo->lastInsertId();

        // Insertar en pacientes
        $stmt = $pdo->prepare("
            INSERT INTO pacientes (
                usuario_id, nombres, apellidos, dni, fecha_nacimiento, sexo, estado_civil,
                telefono, telefono_emergencia, direccion, distrito, provincia,
                emergencia_nombre, emergencia_parentesco, emergencia_telefono, emergencia_email,
                tipo_sangre, seguro_medico, alergias, medicamentos, enfermedades,
                ultima_consulta, motivo_consulta, problemas_dentales,
                cepillado_diario, uso_hilo, enjuague_bucal,
                consentimiento_datos, acepta_terminos, consentimiento_medico
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt->execute([
            $usuario_id, $nombres, $apellidos, $dni, $fechaNacimiento, $sexo, $estadoCivil,
            $telefono, $telefonoEmergencia, $direccion, $distrito, $provincia,
            $emergencia_nombre, $emergencia_parentesco, $emergencia_telefono, $emergencia_email,
            $tipo_sangre, $seguro_medico, $alergias, $medicamentos, $enfermedades,
            $ultima_consulta, $motivo_consulta, $problemas_dentales,
            $cepillado_diario, $uso_hilo, $enjuague_bucal,
            $consentimiento_datos, $acepta_terminos, $consentimiento_medico
        ]);

        $pdo->commit();
        echo "<script>alert('✅ Registro completo exitoso.'); window.location.href='index.html';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("❌ Error al registrar: " . $e->getMessage());
    }
} else {
    die("❌ Acceso denegado.");
}
?>
