<?php
$foto = $_SESSION['foto'];
$foto = $foto == '' ? 'usuario_default.png' : $foto;
?>

<section class="border-bottom mb-2 pb-2">
    <!--Modificar la linea siguiente: agregue la foto, el nombre de usuario en el p y el enlace a la página de cerrar sesión-->
    <img src="../img/usuarios/<?php echo $foto; ?>" alt="foto usuario"> <p><?php echo $_SESSION['usuario']; ?></p> <a href="salir.php" class="btn btn-secondary border">cerrar sesión</a>
</section>
<ul class="navbar-nav text-center">
    <li class="nav-item bg-warning mb-2">
        <a href="juego_listado.php" class="nav-link bi-controller"> Listado Juegos</a>
    </li>
    <li class="nav-item bg-warning mb-2">
        <a href="usuario_alta.php" class="nav-link bi-person-plus-fill"> Alta de Usuario</a>
    </li>
    <li class="nav-item bg-warning mb-2">
        <a href="usuario_listado.php" class="nav-link bi-person-fill"> Listado Usuarios</a>
    </li>
    <li class="nav-item bg-warning mb-2">
        <a href="preferencias.php" class="nav-link bi-gear-fill"> Preferencias</a>
    </li>
    <li class="nav-item bg-warning mb-2">
        <a href="listar-fav.php" class="nav-link bi-star-fill"> Listar favoritos</a>
    </li>
    <!-- clases para los iconos de los menús que hay que agregar:
         Preferencias: 
         Listar favoritos:  -->
</ul>