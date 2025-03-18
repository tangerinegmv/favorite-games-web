<?php
    $ruta = '../';
    require("encabezado.php");
    include 'conexion.php';
    $cnn = conectar();
    if ($cnn && isset($_GET['id_usuario'])) {
        $id = $_GET['id_usuario'];
        $sql = 'SELECT usuario, tipo, foto FROM usuario WHERE id_usuario = ?';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'i', $id);
        $resultado = mysqli_stmt_execute($sentencia);
        mysqli_stmt_bind_result($sentencia, $usu, $tipo, $foto);
        mysqli_stmt_store_result($sentencia);
        $cantFilas = mysqli_stmt_num_rows($sentencia);
        
        if ($cantFilas>0) {
            mysqli_stmt_fetch($sentencia);
        
            ?>
            <main class="container">
                <section>
                    <article>
                        <section class="menu_tmp">
                            <h2>Modificar usuario</h2>
                        </section>
                        <form action="aceptar.php" method="post" enctype="multipart/form-data" class="bg-secondary border-info">
                            <legend class="bg-dark border-info text-center">Alta usuario</legend>     
                            <section>
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" name="usuario" id="usuario"  value="<?php echo $usu ?>" class="form-control border-warning">
                                <label for="pass" class="form-label">Contraseña</label>
                                <input type="password" name="pass" id="pass" placeholder="Contraseña" value="" required maxlength="45" class="form-control border-warning">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select name="tipo" id="tipo" class="form-select border-warning">
                                    <option value="Administrador">Administrador</option>
                                    <option value="Común">Común</option>
                                </select>
                                <label for="foto" class="form-label">Foto Nueva</label>
                                <input type="file" accept="image/*" name="foto" id="foto"  class="form-control border-warning">
                                <section class="text-center">
                                    <input type="hidden" value="<?php echo $id ?>" name="id">
                                    <input type="submit" value="Actualizar" class="btn btn-dark mt-3 mb-3">
                                    <a href="usuario_listado.php" class="btn btn-dark mt-3 mb-3">Cancelar</a>
                                </section>
                            </section>
                        </form>
                    </article>
                </section>
            </main>
            <?php
           
           
            
            
        }

      
    }
    desconectar($cnn);
    require("pie.php");
?>