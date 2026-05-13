<?php
/**
 * UPDATED Login Form with Security Features
 * 
 * Security improvements:
 * - CSRF token included
 * - Secure session initialization
 * - Input field limits
 * - Autocomplete attributes
 */

$ruta = '';
require_once('php/SecurityHelper.php');
require('php/encabezado.php');

// Initialize secure session
SecurityHelper::initSecureSession();

// Generate CSRF token
$csrf_token = SecurityHelper::generateCSRFToken();

?>
<main class="container">
    <article class="container py-5 h-100">
        <section class="row d-flex justify-content-center align-items-center h-100">
            <form action="php/logueo.php" method="post" class="col-12 col-md-8 col-lg-6 col-xl-5">
                <fieldset class="card bg-dark text-white" style="border-radius: 1rem;">
                    <section class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-2">INICIAR SESIÓN</h2>
                        <p class="text-white-50 mb-5">Ingrese su usuario y contraseña</p>

                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>" />

                        <section class="form-outline form-white mb-4">
                            <input type="text" 
                                   id="user" 
                                   name="usuario" 
                                   required 
                                   maxlength="45"
                                   class="form-control form-control-lg"
                                   placeholder="Usuario"
                                   autocomplete="username" />
                            <label class="form-label" for="user">Usuario</label>
                        </section>

                        <section class="form-outline form-white mb-4">
                            <input type="password" 
                                   id="pass" 
                                   name="pass" 
                                   required 
                                   maxlength="45"
                                   class="form-control form-control-lg"
                                   placeholder="Contraseña"
                                   autocomplete="current-password" />
                            <label class="form-label" for="pass">Contraseña</label>
                        </section>

                        <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                    </section>
                </fieldset>
            </form>
        </section>
    </article>
</main>
<?php
    require('php/pie.php');
?>