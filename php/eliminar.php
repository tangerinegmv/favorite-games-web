<?php
    $ruta = '../';
    require("encabezado.php");
    include 'conexion.php';
    $cnn = conectar();
    if ($cnn && !empty($_GET['id_usuario'])) {
        $id = $_GET['id_usuario'];
        $sql = 'DELETE FROM usuario WHERE id = ?';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'i', $id);
        $resultado = mysqli_stmt_execute($sentencia);
       if ($resultado) {
        header("refresh:3;url=usuario_listado.php");
           echo '<p>Usuario eliminado exitosamente.</p>';

       }else {
        header("refresh:3;url=usuario_listado.php");
           echo '<p>No se pudo eliminar el usuario.</p>';
       }
    }else {
        header("refresh:3;url=usuario_listado.php");
            
            echo '<p>No se realizó la eliminación.</p>';
    }
    desconectar($cnn);
    require("pie.php");
?>


