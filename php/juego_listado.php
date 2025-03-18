<?php
    $ruta = '../';
    session_start();
    require("encabezado.php");
   
    if (!empty($_SESSION['usuario'])) {
    
  
?>

<main class="container">
    <section class="row">
        <section class="col-3 menu pt-4">
            <?php require("menu.php"); ?>
        </section>
        <article class="col-9 listado pt-2">
            <?php
               
                require("conexion.php");
                $conexion = conectar();
                if ($conexion) {
                    //consulta SQL para obtener los juegos
                    $sql = 'SELECT titulo, jugadores, lanzamiento, genero, portada FROM juego';
                    $sentencia = mysqli_prepare($conexion, $sql);
                    $resultado = mysqli_stmt_execute($sentencia);
                    mysqli_stmt_bind_result($sentencia,$titulo,$jugadores,$lanzamiento,$genero,$portada);
                    mysqli_stmt_store_result($sentencia);
                    $cantidad = mysqli_stmt_num_rows($sentencia);
                    if ($cantidad > 0) {
                        while (mysqli_stmt_fetch($sentencia)) {
                            $portada = $portada == '' ? 'portada_default.png' : $portada;
                            ?>
                            <section class="col-5 mt-2 mb-2">
                                <section class="card">
                                    <img src="../img/portadas/<?php echo $portada; ?>" />
                                    
                                    <section class="card-content p-3">
                                        <h4 class="card-title text-center"><?php echo $titulo; ?></h4>
                                        <p class="">Jugadores: <?php echo $jugadores; ?></p>
                                        <p class="">Fecha de lanzamiento: <?php echo $lanzamiento; ?></p>
                                        <p class="btn btn-primary"><?php echo $genero; ?></p>
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
            
                ?>
        </article>
    </section>
</main>

<?php
    
    require("pie.php");
}else {
    header("refresh:0;url=../index.php");
}
?>