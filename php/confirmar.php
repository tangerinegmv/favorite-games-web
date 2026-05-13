<?php
require_once 'SecurityHelper.php';
require_once 'conexion.php';

SecurityHelper::initSecureSession();
SecurityHelper::requireAdmin();

$cnn = conectar();
if (!$cnn) {
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error: No se pudo conectar a la base de datos.</p>';
    exit;
}

$id = SecurityHelper::validateInteger($_GET['id_usuario'] ?? null);
if (!$id) {
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error: ID de usuario inválido.</p>';
    desconectar($cnn);
    exit;
}

$sql = 'SELECT usuario FROM usuario WHERE id_usuario = ? AND activado = "S"';
$sentencia = mysqli_prepare($cnn, $sql);
mysqli_stmt_bind_param($sentencia, 'i', $id);
mysqli_stmt_execute($sentencia);
mysqli_stmt_bind_result($sentencia, $usu);
mysqli_stmt_store_result($sentencia);
$cantFilas = mysqli_stmt_num_rows($sentencia);

if ($cantFilas > 0) {
    mysqli_stmt_fetch($sentencia);
    $usuarioSafe = htmlspecialchars($usu, ENT_QUOTES, 'UTF-8');
    $csrf_token = SecurityHelper::generateCSRFToken();
    ?>
    <main class="container">
        <section>
            <article>
                <h1>Eliminar usuario</h1>
                <p>¿Está seguro que quiere eliminar el usuario <?php echo $usuarioSafe; ?>?</p>
                <form action="eliminar.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>" />
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" />
                    <button type="submit" class="btn btn-danger">Aceptar</button>
                    <a href="usuario_listado.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </article>
        </section>
    </main>
    <?php
} else {
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error: Usuario no encontrado.</p>';
}

desconectar($cnn);
require("pie.php");
?>


