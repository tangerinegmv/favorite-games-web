<?php
require_once 'SecurityHelper.php';
session_start();
SecurityHelper::initSecureSession();
SecurityHelper::requireLogin();
$ruta = '../';
require("encabezado.php");
include 'conexion.php';
$csrf_token = SecurityHelper::generateCSRFToken();
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
                            $fotoSafe = htmlspecialchars($foto, ENT_QUOTES, 'UTF-8');
                            $usuarioSafe = htmlspecialchars($usu, ENT_QUOTES, 'UTF-8');
                            $tipoSafe = htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8');
                            $idSafe = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

                            echo '<tr>';
                            echo '<td><img src="../img/usuarios/' . $fotoSafe . '" alt="foto perfil"></td>';
                            echo '<td>' . $usuarioSafe . '</td>';
                            echo '<td>' . $tipoSafe . '</td>';
                            echo '<td><a href="modificar.php?id_usuario=' . $idSafe . '"><img src="../img/modificar.png" alt="modificar"></a></td>';
                            echo '<td><a href="confirmar.php?id_usuario=' . $idSafe . '"><img src="../img/eliminar.png" alt="eliminar"></a></td>';
                            echo '<td>';
                            echo '<form action="desactivar.php" method="post" style="display:inline;">';
                            echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') . '" />';
                            echo '<input type="hidden" name="id_usuario" value="' . $idSafe . '" />';
                            echo '<button type="submit" class="btn btn-link p-0 border-0 bg-transparent"><img src="../img/desactivar.png" alt="desactivar"></button>';
                            echo '</form>';
                            echo '</td>';
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
?>