<?php
session_start();
session_unset();      // Elimina todas las variables de sesión
session_destroy();    // Destruye la sesión
header("Location: index.php?mensaje=salida"); // Redirige al login con mensaje
exit;
?>
