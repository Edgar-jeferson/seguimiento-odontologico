<?php
// Este bloque queda listo por si luego usas sesiones
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Seguimiento de Pacientes Odontológicos - UNAP FINESI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --unap-blue: #1e3a8a;
            --unap-light-blue: #3b82f6;
            --unap-green: #16a34a;
            --dental-blue: #0ea5e9;
            --dental-teal: #14b8a6;
        }

        body {
            background: linear-gradient(135deg, var(--unap-blue) 0%, var(--dental-blue) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
        }

        .login-left {
            background: linear-gradient(45deg, var(--unap-blue), var(--dental-blue));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .login-right {
            padding: 40px;
        }

        .logo {
            width: 200px;
            height: 200px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn-unap {
            background: var(--unap-blue);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-unap:hover {
            background: var(--unap-light-blue);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-medico {
            background: var(--dental-teal);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-medico:hover {
            background: #0d9488;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-admin {
            background: var(--unap-green);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-admin:hover {
            background: #15803d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 15px;
        }

        .form-control:focus {
            border-color: var(--dental-blue);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .register-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .register-buttons .btn {
            flex: 1;
            min-width: 200px;
        }

        @media (max-width: 768px) {
            .register-buttons {
                flex-direction: column;
            }

            .register-buttons .btn {
                min-width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="login-card">
                <div class="row g-0">
                    <!-- Panel Izquierdo -->
                    <div class="col-lg-6 login-left">
                        <div class="logo">
                            <i class="fas fa-tooth" style="font-size: 100px; color: var(--dental-blue);"></i>
                        </div>
                        <h2 class="mb-3">SISTEMA DE SEGUIMIENTO DE PACIENTES ODONTOLÓGICOS</h2>
                        <p class="mb-4">Facultad de Ingeniería de Sistemas e Informática<br>Escuela de Estadística e Informática</p>
                        <div class="stats-grid">
                            <div class="stat-card"><div class="stat-number">250+</div><div>Pacientes Registrados</div></div>
                            <div class="stat-card"><div class="stat-number">95%</div><div>Seguridad de Datos</div></div>
                            <div class="stat-card"><div class="stat-number">12</div><div>Odontólogos Activos</div></div>
                            <div class="stat-card"><div class="stat-number">48</div><div>Tratamientos Activos</div></div>
                        </div>
                    </div>

                    <!-- Panel Derecho -->
                    <div class="col-lg-6 login-right">
                        <div class="text-center mb-4">
                            <h3 class="text-dark">Bienvenido</h3>
                            <p class="text-muted">Ingresa a tu cuenta para continuar</p>
                        </div>

                        <!-- ✅ Mensaje de sesión cerrada -->
                        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'salida'): ?>
                            <div class="alert alert-success text-center">
                                ✅ Sesión cerrada correctamente.
                            </div>
                        <?php endif; ?>

                        <!-- ⚠️ Mensajes de error -->
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger text-center">
                                <?php
                                switch ($_GET['error']) {
                                    case 'conexion': echo '❌ Error al conectar con la base de datos.'; break;
                                    case 'campos': echo '⚠️ Por favor completa todos los campos.'; break;
                                    case 'correo': echo '❌ Correo no registrado.'; break;
                                    case 'tipo': echo '❌ El tipo de usuario no coincide.'; break;
                                    case 'clave': echo '❌ Contraseña incorrecta.'; break;
                                    case 'desconocido': echo '⚠️ Tipo de usuario desconocido.'; break;
                                    default: echo '⚠️ Error desconocido.'; break;
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de login -->
                        <form id="loginForm" method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="userType" class="form-label">Tipo de Usuario</label>
                                <select class="form-control" id="userType" name="userType" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="paciente">Paciente</option>
                                    <option value="odontologo">Odontólogo</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Recordar sesión</label>
                            </div>
                            <button type="submit" class="btn btn-unap w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </form>

                        <!-- Registro -->
                        <div class="text-center">
                            <p class="mb-3">¿No tienes cuenta?</p>
                            <div class="register-buttons">
                                <button class="btn btn-outline-primary" onclick="showPacienteRegister()">
                                    <i class="fas fa-user-plus me-2"></i>Registrarse como Paciente
                                </button>
                                <button class="btn btn-medico" onclick="showOdontologoRegister()">
                                    <i class="fas fa-user-md me-2"></i>Registrarse como Odontólogo
                                </button>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <small class="text-muted">
                                <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a> | 
                                <a href="#" class="text-decoration-none">Contacto</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showPacienteRegister() {
            window.location.href = 'registro-paciente.html';
        }

        function showOdontologoRegister() {
            window.location.href = 'registro-odontologo.html';
        }

        function animateNumbers() {
            const numbers = document.querySelectorAll('.stat-number');
            numbers.forEach(num => {
                const target = parseInt(num.textContent);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    num.textContent = Math.floor(current) + (num.textContent.includes('%') ? '%' : num.textContent.includes('+') ? '+' : '');
                }, 40);
            });
        }

        window.addEventListener('load', animateNumbers);
    </script>
</body>
</html>
