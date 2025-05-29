<?php
// Configuración conexión MySQL (XAMPP)
$servername = "localhost";
$username = "root";      // usuario MySQL (por defecto root)
$password = "";          // contraseña MySQL (por defecto vacía en XAMPP)
$dbname = "repositorioseguridad";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$ver = $_POST['ver'] ?? null; // 'normal' o 'segura'

// Obtener datos de la tabla productos
$sql = "SELECT idProducto, nombre, stock, region, proveedor FROM productos";
$result = $conn->query($sql);

$datos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
}
$conn->close();

// Función para enmascarar correo (proveedor) en vista segura
function enmascararCorreo($correo) {
    if (strpos($correo, '@') !== false) {
        $partes = explode('@', $correo);
        $parteOculta = str_repeat('X', strlen($partes[0]));
        return $parteOculta . '@' . $partes[1];
    }
    return 'XXXXX';
}

function enmascararStock($stock) {
    // Convertimos a string, con 2 decimales siempre
    $str = number_format((float)$stock, 2, '.', '');
    // Ejemplo: "2500.00"

    // Vamos a reemplazar con '*', excepto:
    // - El primer carácter (posición 0)
    // - El penúltimo carácter (posición strlen-2), que es el decimal antes del último
    // - El último carácter (posición strlen-1)

    $resultado = '';
    $len = strlen($str);

    for ($i = 0; $i < $len; $i++) {
        if ($i == 0 || $i == $len - 2 || $i == $len - 1) {
            $resultado .= $str[$i]; // Mostrar este carácter
        } elseif ($str[$i] == '.') {
            $resultado .= '.'; // Mostrar el punto decimal
        } else {
            $resultado .= '$'; // Enmascarar con asterisco
        }
    }

    return $resultado;
}




?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>TALLER SEGURIDAD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">

    <h1 class="mb-4">VISUALIZACION ENMASCARADA</h1>

    <form method="post" class="mb-4">
        <button type="submit" name="ver" value="normal" class="btn btn-primary me-2">Vista Admin</button>
        <button type="submit" name="ver" value="segura" class="btn btn-secondary">Vista Segura</button>
    </form>

    <h2>Tabla de productos</h2>

    <?php if ($ver === null): ?>
        <p>Haz clic en un botón para mostrar los datos.</p>
    <?php elseif (empty($datos)): ?>
        <p>No se encontraron datos.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Región</th>
                    <th>Proveedor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($datos as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['idProducto']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td>
                             <?php 
                            if ($ver === 'segura') {
                                echo enmascararStock($fila['stock']);
                            } else {
                                echo htmlspecialchars($fila['stock']);
                            }
                            ?>
                    </td>
                        <td><?= htmlspecialchars($fila['region']) ?></td>
                        <td>
                            <?php 
                            if ($ver === 'segura') {
                                echo enmascararCorreo($fila['proveedor']);
                            } else {
                                echo htmlspecialchars($fila['proveedor']);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
