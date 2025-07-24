<?php
$pdo = new PDO("mysql:host=localhost;dbname=clinica_dental", "root", ""); // cambia la contraseña si es necesario

$usuarios = [
    ['paciente', 'paciente@ejemplo.com', '123456'],
    ['odontologo', 'odontologo@ejemplo.com', 'abc123'],
    ['administrador', 'admin@ejemplo.com', 'adminpass']
];

foreach ($usuarios as $u) {
    $stmt = $pdo->prepare("INSERT INTO usuarios (tipo, correo, contrasena) VALUES (?, ?, ?)");
    $stmt->execute([$u[0], $u[1], password_hash($u[2], PASSWORD_DEFAULT)]);
}

echo "✅ Usuarios insertados con contraseña segura.";
?>
