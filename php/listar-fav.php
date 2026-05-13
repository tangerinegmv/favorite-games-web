<?php
    $ruta = '../';
    require_once 'SecurityHelper.php';
    session_start();
    SecurityHelper::initSecureSession();
    SecurityHelper::requireLogin();
    require("encabezado.php");
    require("conexion.php");
    $usuario = $_SESSION['usuario'] ?? null;
?>

<main class="container">
    <section class="row">
        <section class="col-3 menu pt-4">
            <?php require("menu.php"); ?>
        </section>
        <article class="col-9 listado pt-2">
            <?php
                $cookieName = $usuario ? htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') : null;

                if ($usuario && isset($_COOKIE[$cookieName]) && !empty($_COOKIE[$cookieName])) {
                    $preferencia = $_COOKIE[$cookieName];
                    $conexion = conectar();

                    if ($conexion) {
                        $sql = 'SELECT titulo, jugadores, lanzamiento, genero, portada FROM juego WHERE genero = ?';
                        $sentencia = mysqli_prepare($conexion, $sql);
                        mysqli_stmt_bind_param($sentencia, 's', $preferencia);
                        $resultado = mysqli_stmt_execute($sentencia);
                        mysqli_stmt_bind_result($sentencia, $titulo, $jugadores, $lanzamiento, $genero, $portada);
                        mysqli_stmt_store_result($sentencia);
                        $cantidad = mysqli_stmt_num_rows($sentencia);

                        if ($cantidad > 0) {
                            while (mysqli_stmt_fetch($sentencia)) {
                                $portada = $portada == '' ? 'portada_default.png' : $portada;
                                ?>
                                <section class="col-5 mt-2 mb-2">
                                    <section class="card">
                                        <img src="../img/portadas/<?php echo htmlspecialchars($portada, ENT_QUOTES, 'UTF-8'); ?>" />
                                        
                                        <section class="card-content p-3">
                                            <h4 class="card-title text-center"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h4>
                                            <p class="">Jugadores: <?php echo htmlspecialchars($jugadores, ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="">Fecha de lanzamiento: <?php echo htmlspecialchars($lanzamiento, ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="btn btn-primary"><?php echo htmlspecialchars($genero, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </section>
                                    </section>
                                </section>
                                <?php
                            }
                        } else {
                            echo '<h2>No hay resultados</h2>';
                        }

                        desconectar($conexion);
                    }
                } else {
                    echo '<p>Sin preferencias</p>';
                }
                ?>
        </article>
    </section>
</main>

<?php
    require("pie.php");
?>