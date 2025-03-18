<?php
    $ruta = '../';
    require("encabezado.php");
    include 'conexion.php';
    $cnn = conectar();
    if ($cnn && isset($_GET['id_usuario'])) {
        $id = $_GET['id_usuario'];
        $sql = 'UPDATE usuario SET activado = "N" WHERE id_usuario = ?';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'i', $id);
        $resultado = mysqli_stmt_execute($sentencia);
       if ($resultado) {
        header("refresh:3;url=usuario_listado.php");
           echo '<p>Usuario desactivado exitosamente.</p>';

       }else {
        header("refresh:3;url=usuario_listado.php");
           echo '<p>No se pudo eliminar el usuario.</p>';
       }
    }
    desconectar($cnn);
    require("pie.php");
?>