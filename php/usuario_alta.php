<?php
require_once("SecurityHelper.php");
SecurityHelper::initSecureSession();
SecurityHelper::requireAdmin();
$ruta = '../';
require("encabezado.php");

$csrf_token = SecurityHelper::generateCSRFToken();
?>

<main class="container">
    
    <section class="row">
     <section class="col-3 menu pt-4">
        <?php require("menu.php"); ?>
        </section>
        <article class="col-9 listado pt-2">
            
            <form action="usuario_alta_ok.php" method="post" enctype="multipart/form-data" class="bg-secondary border-info">
                <legend class="bg-dark border-info text-center">Alta usuario</legend>     
                <section>
                    <!-- CSRF Token Protection -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>" />
                    
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" placeholder="Usuario" required maxlength="45" class="form-control border-warning">
                    
                    <label for="pass" class="form-label">Contraseña</label>
                    <input type="password" name="pass" id="pass" placeholder="Contraseña" required maxlength="45" class="form-control border-warning">
                    
                    <label for="tipo" class="form-label">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select border-warning">
                        <option value="Administrador">Administrador</option>
                        <option value="Común">Común</option>
                    </select>
                    
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" accept="image/*" name="foto" id="foto" class="form-control border-warning">
                    
                    <section class="text-center">
                        <input type="submit" name="enviar" value="Confirmar" class="btn btn-dark mt-3 mb-3">
                    </section>
                </section>
            </form>
        </article>
    </section>
</main>

<?php
    require("pie.php");
?>