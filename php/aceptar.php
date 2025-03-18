<?php

if (!empty($_POST['usuario']) && !empty($_POST['pass']) && !empty($_POST['tipo'])) {
   
   include 'conexion.php';
    $cnn = conectar();
    if ($cnn  && isset($_POST['id'])) {
        $usuario = $_POST['usuario'];
        $clave = sha1($_POST['pass']);
        $tipo = $_POST['tipo'];
        $id = $_POST['id']; 
        
        
        $foto_nombre1 = explode('.',$_FILES['foto']['name']) ;
        if (!empty($_FILES['foto']['size']) ) {
            
            $foto_nombre = $usuario . '.' . $foto_nombre1[1] ;
            $rutaO= $_FILES['foto']['tmp_name'];
            $destino = '../img/usuarios/'. $foto_nombre;
            move_uploaded_file($rutaO, $destino);
        }else {
            $foto_nombre = '';
          
        
        }
      
        $sql = 'UPDATE usuario SET usuario = ?, pass= ?, tipo = ?, foto =? WHERE id_usuario = ?';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'ssssi', $usuario, $clave, $tipo, $foto_nombre, $id);
        $resultado = mysqli_stmt_execute($sentencia);
        if ($resultado) {
            header("refresh:3;url=usuario_listado.php");
          echo '<p>Actualizado exitosamente.</p>';

           }else {
            header("refresh:3;url=usuario_listado.php");
            
            echo '<p>Error al guardar datos.</p>';
           }
            
    }desconectar($cnn);
}
    


?>