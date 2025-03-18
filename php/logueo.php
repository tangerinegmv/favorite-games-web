<?php
session_start();
if (!empty($_POST['usuario']) && !empty($_POST['pass'])) {
    //hacer cuestiones
   include 'conexion.php';
    $cnn = conectar();
    if ($cnn) {
       
        $sql = 'SELECT foto FROM usuario WHERE usuario = ? AND pass = ? AND activado=\'S\'';
        $sentencia = mysqli_prepare($cnn, $sql);
        $usuForm = $_POST['usuario'];
        $claveForm = sha1($_POST['pass']);
        mysqli_stmt_bind_param($sentencia, 'ss', $usuForm, $claveForm);
        mysqli_stmt_execute($sentencia);
        mysqli_stmt_bind_result($sentencia, $foto);
        mysqli_stmt_store_result($sentencia);
        $cantFilas = mysqli_stmt_num_rows($sentencia);

        if ($cantFilas == 1) {
            mysqli_stmt_fetch($sentencia);
            header("refresh:1;url=juego_listado.php");
            $_SESSION['usuario'] = $usuForm;
            $_SESSION['foto'] = $foto;
        }else {
            header("refresh:3;url=../index.php");
            
            echo '<p>Usuario y contraseña no encontrados en la base de datos.</p>';
           }
            
        }
    }
    desconectar($cnn);


?>