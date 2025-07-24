<?php


session_start(); // <-- ESTA LÍNEA FALTABA

$userEmail = $_SESSION['userEmail'];

$host = 'localhost';
$db = 'clinica_dental';
$user = 'root';
$pass = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$stmt = $pdo->prepare("
    SELECT p.*, u.id as usuario_id, u.correo
    FROM usuarios u
    JOIN pacientes p ON u.id = p.usuario_id
    WHERE u.correo = ?
");
$stmt->execute([$userEmail]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    die("No se encontraron datos del paciente.");
}

extract($paciente);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard <?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?> - UNAP FINESI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --unap-blue: #1e3a8a;
            --dental-blue: #0ea5e9;
            --dental-teal: #14b8a6;
            --success-green: #16a34a;
            --warning-orange: #f59e0b;
            --danger-red: #dc2626;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: var(--unap-blue) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            padding: 0;
        }
        
        .sidebar-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            background: var(--dental-blue);
            color: white;
        }
        
        .content-area {
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: var(--dental-blue);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--dental-blue), var(--dental-teal));
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .appointment-card {
            border-left: 4px solid var(--dental-blue);
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pendiente { background: var(--warning-orange); color: white; }
        .status-confirmada { background: var(--success-green); color: white; }
        .status-completada { background: var(--dental-teal); color: white; }
        .status-cancelada { background: var(--danger-red); color: white; }
        
        .btn-dental {
            background: var(--dental-blue);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        .btn-dental:hover {
            background: var(--dental-teal);
            transform: translateY(-2px);
            color: white;
        }
        
        .hidden { display: none; }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 76px;
                left: -250px;
                width: 250px;
                z-index: 1000;
                transition: left 0.3s;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .content-area {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler d-lg-none" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">
                <i class="fas fa-tooth me-2"></i>
                Sistema Odontológico - UNAP
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>
                    <span id="userName"><?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?></span>
                </span>
                <button class="btn btn-outline-light btn-sm" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 sidebar" id="sidebar">
                <div class="sidebar-item active" onclick="showSection('dashboard')">
                    <i class="fas fa-home me-2"></i> Dashboard
                </div>
                <div class="sidebar-item" onclick="showSection('citas')">
                    <i class="fas fa-calendar-alt me-2"></i> Mis Citas
                </div>
                <div class="sidebar-item" onclick="showSection('historial')">
                    <i class="fas fa-file-medical me-2"></i> Historial Médico
                </div>
                <div class="sidebar-item" onclick="showSection('tratamientos')">
                    <i class="fas fa-procedures me-2"></i> Tratamientos
                </div>
                <div class="sidebar-item" onclick="showSection('perfil')">
                    <i class="fas fa-user-edit me-2"></i> Mi Perfil
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9 col-md-8 content-area">
                <!-- Dashboard Section -->
                <div id="dashboard-section">
                    <h2 class="mb-4">Dashboard del <?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?></h2>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-number">3</div>
                                <div>Citas Programadas</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-number">2</div>
                                <div>Tratamientos Activos</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-number">8</div>
                                <div>Citas Completadas</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-number">1</div>
                                <div>Pendiente de Pago</div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximas Citas -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Próximas Citas</h5>
                        </div>
                        <div class="card-body">
                            <div class="appointment-card card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1">Control de Ortodoncia</h6>
                                            <p class="text-muted mb-1">Dr. María González</p>
                                            <small class="text-muted">25 de Julio, 2025 - 10:00 AM</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <span class="status-badge status-confirmada">Confirmada</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Citas Section -->
                <div id="citas-section" class="hidden">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Mis Citas</h2>
                        <button class="btn btn-dental" onclick="showNewAppointment()">
                            <i class="fas fa-plus me-2"></i>Nueva Cita
                        </button>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Historial de Citas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Odontólogo</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="citasTable">
                                        <tr>
                                            <td>25/07/2025</td>
                                            <td>10:00 AM</td>
                                            <td>Dr. María González</td>
                                            <td>Control Ortodoncia</td>
                                            <td><span class="status-badge status-confirmada">Confirmada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Ver</button>
                                                <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>20/07/2025</td>
                                            <td>2:00 PM</td>
                                            <td>Dr. Juan Pérez</td>
                                            <td>Limpieza</td>
                                            <td><span class="status-badge status-completada">Completada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Ver</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial Section -->
                <div id="historial-section" class="hidden">
                    <h2 class="mb-4">Historial Médico</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?></p>
                                    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($fecha_nacimiento); ?></p>
                                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($correo); ?></p>
                                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?></p>
                                    <p><strong>Seguro:</strong> SIS</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Historial de Tratamientos</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6>Ortodoncia - Fase 1</h6>
                                        <p class="text-muted mb-1">Dr. María González</p>
                                        <p>Colocación de brackets metálicos superiores e inferiores.</p>
                                        <small class="text-muted">Fecha: 15/01/2025</small>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6>Limpieza Dental</h6>
                                        <p class="text-muted mb-1">Dr. Juan Pérez</p>
                                        <p>Profilaxis y fluorización.</p>
                                        <small class="text-muted">Fecha: 20/12/2024</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tratamientos Section -->
                <div id="tratamientos-section" class="hidden">
                    <h2 class="mb-4">Tratamientos Actuales</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Ortodoncia</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Odontólogo:</strong> Dr. María González</p>
                                    <p><strong>Inicio:</strong> 15/01/2025</p>
                                    <p><strong>Duración estimada:</strong> 18 meses</p>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: 35%">35%</div>
                                    </div>
                                    <p><strong>Próximo control:</strong> 25/07/2025</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Endodoncia</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Odontólogo:</strong> Dr. Ana Quispe</p>
                                    <p><strong>Pieza:</strong> Molar superior derecho</p>
                                    <p><strong>Sesiones:</strong> 2 de 3 completadas</p>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: 67%">67%</div>
                                    </div>
                                    <p><strong>Próxima sesión:</strong> 30/07/2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perfil Section -->
                <div id="perfil-section" class="hidden">
                    <h2 class="mb-4">Mi Perfil</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <form id="perfilForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" value="1995-03-15" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" value="<?php echo htmlspecialchars($telefono); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($correo); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($direccion); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Seguro Médico</label>
                                            <select class="form-control">
                                                <option value="SIS" <?php if ($seguro_medico == "SIS") echo "selected"; ?>>SIS</option>
                                                <option value="EsSalud" <?php if ($seguro_medico == "EsSalud") echo "selected"; ?>>EsSalud</option>
                                                <option value="Privado" <?php if ($seguro_medico == "Privado") echo "selected"; ?>>Privado</option>
                                                <option value="Ninguno" <?php if ($seguro_medico == "Ninguno") echo "selected"; ?>>Ninguno</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dental">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       // Mostrar nombre del usuario directamente con PHP (ya está en el HTML)
// No necesitas usar sessionStorage en este contexto

        // Navegación entre secciones
        function showSection(sectionName) {
            // Ocultar todas las secciones
            document.querySelectorAll('[id$="-section"]').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById(sectionName + '-section').classList.remove('hidden');
            
            // Actualizar sidebar activo
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Toggle sidebar en móvil
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Nueva cita
        function showNewAppointment() {
            alert('Función para agendar nueva cita - Aquí se abriría un modal o formulario');
        }

        // Cerrar sesión
        function logout() {
            sessionStorage.clear();
            window.location.href = 'logout.php';
        }

        // Guardar perfil
        document.getElementById('perfilForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Perfil actualizado correctamente');
        });
    </script>
</body>
</html>