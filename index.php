<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Taller Seguridad</title>
    <!-- Bootstrap para dar formato a la tabla -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4">VISUALIZACION EMASCARADA</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Base de Datos</h5>
            <button class="btn btn-secondary" onclick="ejecutarScript()">Generar Vistas</button>
            <div id="resultado" class="mt-3 text-monospace text-muted small"></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Elige una Vista</h5>
            <form method="post" class="mb-3">
                <!-- Vista normal es administrador, osea como root, y la vista segura es la que se hace con usuariovista -->
                <button type="submit" name="ver" value="normal" class="btn btn-warning">ADMIN</button>
                <button type="submit" name="ver" value="segura" class="btn btn-success">SEGURA</button>
            </form>

            <!-- Botón para ejecutar auditoría y subir el archivo .sql a GitHub -->
            <form class="mb-3">
                <button type="button" onclick="ejecutarAuditoria()" class="btn btn-dark">EJECUTAR AUDITORÍA</button>
            </form>
            <div id="resultadoAuditoria" class="mt-2 text-muted small"></div>

            <?php
            $ver = $_POST['ver'] ?? 'normal';

            if ($ver === 'segura') {
                $username = 'usuariovista';
                $password = 'clave123'; 
                $tabla = 'vista_productos_segura';
            } else {
                $username = 'root';
                $password = '';
                $tabla = 'productos';
            }

            $conn = new mysqli('localhost', $username, $password, 'repositorioseguridad');
            if ($conn->connect_error) {
                echo '<div class="alert alert-danger">Error de conexión: ' . $conn->connect_error . '</div>';
            } else {
                $sql = "SELECT * FROM $tabla";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo "<h6 class='text-muted'>Tipo de vista: " . ($ver === 'segura' ? 'Segura' : 'Admin') . "</h6>";
                    echo "<div class='table-responsive'><table class='table table-bordered table-hover'>
                            <thead class='table-light'>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Región</th>
                                <th>Proveedor</th>
                            </tr>
                            </thead><tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['idProducto']}</td>
                                <td>{$row['nombre']}</td>
                                <td>{$row['stock']}</td>
                                <td>{$row['region']}</td>
                                <td>{$row['proveedor']}</td>
                              </tr>";
                    }

                    echo "</tbody></table></div>";
                } else {
                    echo "<div class='alert alert-info'>No hay resultados en la vista seleccionada.</div>";
                }

                $conn->close();
            }
            ?>
        </div>
    </div>
</div>

<!-- JavaScript para AJAX y traer el script al frente sin actualizar la página -->
<script>
    function ejecutarScript() {
        const resultado = document.getElementById("resultado");
        resultado.innerHTML = "<div class='text-muted'>Ejecutando...</div>";
        fetch("ejecutar_sql.php")
            .then(response => response.text())
            .then(data => {
                resultado.innerHTML = `<div class='alert alert-info'>${data}</div>`;
            })
            .catch(error => {
                resultado.innerHTML = "<div class='alert alert-danger'>Error.</div>";
                console.error(error);
            });
    }

    function ejecutarAuditoria() {
        const resultado = document.getElementById("resultadoAuditoria");
        resultado.innerHTML = "<div class='text-muted'>Ejecutando auditoría...</div>";
        fetch("auditoria_to_git.php")
            .then(response => response.text())
            .then(data => {
                resultado.innerHTML = `<div class='alert alert-info'>${data}</div>`;
            })
            .catch(error => {
                resultado.innerHTML = "<div class='alert alert-danger'>Error al ejecutar auditoría.</div>";
                console.error(error);
            });
    }
</script>

</body>
</html>
