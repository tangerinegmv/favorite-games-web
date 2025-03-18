<?php
    $ruta = '../';
    require("encabezado.php");
    include 'conexion.php';
    $cnn = conectar();
    if ($cnn && !empty($_GET['id_usuario'])) {
        $id = $_GET['id_usuario'];
        $sql = 'SELECT usuario, tipo, foto FROM usuario WHERE id_usuario = ?';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'i', $id);
        mysqli_stmt_execute($sentencia);
        mysqli_stmt_bind_result($sentencia, $usu, $tipo, $foto);
        mysqli_stmt_store_result($sentencia);
        $cantFilas = mysqli_stmt_num_rows($sentencia);
        
        if ($cantFilas>0) {
            echo '<h1>Eliminar usuario</h1>';
            
            while (mysqli_stmt_fetch($sentencia)) {
                echo '<p>¿Está seguro que quiere eliminar el usuario ' . $usu . '?</p>';
                echo '<a href="eliminar.php?id_usuario=' . $id . '">Aceptar</a>
                <a href="eliminar.php">Cancelar</a>';
            }
            
        }
    }
    desconectar($cnn);
    require("pie.php");
?>


