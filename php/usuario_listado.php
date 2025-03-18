<?php
session_start();
    $ruta = '../';
    require("encabezado.php");
    include 'conexion.php';
    if (!empty($_SESSION['usuario'])) {
    $cnn = conectar();
    
?>

<main class="container">
    <section>
        <article class="row">
        <section class="col-3 menu pt-4">
            <?php require("menu.php"); ?>
        </section>
        <section class="col-9 listado pt-2">

        
            <section class="menu_tmp ">
                <a class="btn btn-dark" href="usuario_alta.php">+ Alta usuario</a>
            </section>
            <table class="table table-bordered table-hover table-striped w-auto">
                <caption class="caption-top text-center bg-dark">Listado de usuarios</caption>
                <tr>
                    <th class="bg-secondary text-white">Foto</th>
                    <th class="bg-secondary text-white">Usuario</th>
                    <th class="bg-secondary text-white">Tipo</th>
                    <th class="bg-secondary text-white">Modificar</th>
                    <th class="bg-secondary text-white">Eliminar</th>
                    <th class="bg-secondary text-white">Desactivar</th>
                </tr>

                <?php
                   if ($cnn) {
                    $sql = 'SELECT id_usuario, usuario, tipo, foto FROM usuario WHERE activado = "S"';
                    $sentencia = mysqli_prepare($cnn, $sql);
                    $resultado = mysqli_stmt_execute($sentencia);
                    mysqli_stmt_bind_result($sentencia,$id,$usu,$tipo,$foto);
                 
                    if ($resultado) {
                        while (mysqli_stmt_fetch($sentencia)) {
                            if ($foto == '') {
                            $foto = 'usuario_default.png';
                            }
                            echo '<tr><td><img src="../img/usuarios/' . $foto . '" alt="foto perfil"></td><td>' . $usu . '</td><td>' . $tipo . '</td><td><a href="modificar.php?id_usuario=' . $id . '"><img src="../img/modificar.png" alt="modificar"></a></td><td><a href="confirmar.php?id_usuario=' . $id . '"><img src="../img/eliminar.png" alt="eliminar"></a></td><td><a href="desactivar.php?id_usuario=' . $id . '"><img src="../img/desactivar.png" alt="desactivar"></a></td>';
                            echo '</tr>';
                        }
                    }
                   } 
                   desconectar($cnn);
                
                ?>
                
            </table>
                   
        </article>
    </section>
</section>
</main>

<?php
    require("pie.php");
}else {
    header("refresh:0;url=../index.php");
}
?>