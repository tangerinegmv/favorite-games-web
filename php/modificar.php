<?php
    $ruta = '../';
    require("encabezado.php");
    require_once("SecurityHelper.php");
    include 'conexion.php';
    $cnn = conectar();
    
    if ($cnn && isset($_GET['id_usuario'])) {
        // Validate user ID is integer
        $id = SecurityHelper::validateInteger($_GET['id_usuario']);
        
        if (!$id) {
            echo '<p>Error: ID de usuario inválido.</p>';
            desconectar($cnn);
            require("pie.php");
            exit;
        }
        
        // Check authorization - user can only edit their own profile or admin can edit anyone
        if ($_SESSION['user_id'] != $id && $_SESSION['user_type'] !== 'Administrador') {
            echo '<p>Error: No tienes permiso para editar este usuario.</p>';
            desconectar($cnn);
            require("pie.php");
            exit;
        }
        
        $sql = 'SELECT usuario, tipo, foto FROM usuario WHERE id_usuario = ?';
        $sentencia = mysqli_prepare($cnn, $sql);
        mysqli_stmt_bind_param($sentencia, 'i', $id);
        $resultado = mysqli_stmt_execute($sentencia);
        mysqli_stmt_bind_result($sentencia, $usu, $tipo, $foto);
        mysqli_stmt_store_result($sentencia);
        $cantFilas = mysqli_stmt_num_rows($sentencia);
        
        if ($cantFilas>0) {
            mysqli_stmt_fetch($sentencia);
            
            // Generate CSRF token
            SecurityHelper::initSecureSession();
            $csrf_token = SecurityHelper::generateCSRFToken();
        
            ?>
            <main class="container">
                <section>
                    <article>
                        <section class="menu_tmp">
                            <h2>Modificar usuario</h2>
                        </section>
                        <form action="aceptar.php" method="post" enctype="multipart/form-data" class="bg-secondary border-info">
                            <legend class="bg-dark border-info text-center">Modificar usuario</legend>     
                            <section>
                                <!-- CSRF Token Protection -->
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>" />
                                
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" name="usuario" id="usuario" value="<?php echo htmlspecialchars($usu, ENT_QUOTES, 'UTF-8'); ?>" class="form-control border-warning" maxlength="45">
                                
                                <label for="pass" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" name="pass" id="pass" placeholder="Contraseña" maxlength="45" class="form-control border-warning">
                                
                                <label for="tipo" class="form-label">Tipo</label>
                                <select name="tipo" id="tipo" class="form-select border-warning">
                                    <option value="Administrador" <?php echo ($tipo === 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="Común" <?php echo ($tipo === 'Común') ? 'selected' : ''; ?>>Común</option>
                                </select>
                                
                                <label for="foto" class="form-label">Foto Nueva</label>
                                <input type="file" accept="image/*" name="foto" id="foto" class="form-control border-warning">
                                
                                <section class="text-center">
                                    <input type="hidden" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" name="id">
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
        desconectar($cnn);
    }
    require("pie.php");
?>