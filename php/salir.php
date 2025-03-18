<?php
session_start();
if (!empty($_SESSION['usuario'])) {
    header("refresh:1;url=../index.php"); 
    session_destroy();
}