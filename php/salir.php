<?php
require_once 'SecurityHelper.php';

SecurityHelper::initSecureSession();
if (!empty($_SESSION['usuario'])) {
    SecurityHelper::destroySession();
}

header('refresh:1;url=../index.php');
exit;