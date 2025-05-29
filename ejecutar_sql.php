<?php
// Conexion con phpmyadmin 
$mysqli = new mysqli("localhost", "root", "", "");

// Verificacion de conexion
if ($mysqli->connect_errno) {
    die("Error de conexion: " . $mysqli->connect_error);
}

// Cargargamos el sql
$sql = file_get_contents("script_instalacion.sql");

if (!$sql) {
    die("No es un archivo sql.");
}

// Separamos las sentencias ya que hay varias
$queries = array_filter(array_map('trim', explode(";", $sql)));

// Ejecutamos una por una
$errores = 0;
foreach ($queries as $query) {
    if (!empty($query)) {
        if (!$mysqli->query($query)) {
            echo "Error en sentencia: <pre>$query</pre><br>errormysql: " . $mysqli->error . "<br><br>";
            $errores++;
        }
    }
}

if ($errores === 0) {
    echo "Generacion correcta";
} else {
    echo "Hubo un error al ejecutar";
}

$mysqli->close();
?>
