<?php

if (!empty($_POST['usuario']) && !empty($_POST['pass']) && !empty($_POST['tipo']) && !empty($_FILES['foto']['size'])) {
    //hacer cuestiones
   include 'conexion.php';
    $cnn = conectar();
    if ($cnn) {
        $usuario = $_POST['usuario'];
        $clave = sha1($_POST['pass']);
        $tipo = $_POST['tipo'];
        $foto_nombre = $_FILES['foto']['name'];
        $sql = 'INSERT INTO usuario(usuario, pass, tipo, foto) VALUES (?, ?, ?, ?)';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'ssss', $usuario, $clave, $tipo, $foto_nombre);
        $resultado = mysqli_stmt_execute($sentencia);
        if ($resultado) {
          echo '<p>Guardado exitosamente.</p>';
           }else {
            //header("refresh:3;url=../index.php");
            
            echo '<p>Error al guardar datos.</p>';
           }
            
    }desconectar($cnn);
}
    


?>