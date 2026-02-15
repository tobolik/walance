<?php
session_start();
unset($_SESSION['walance_admin']);
header('Location: index.php');
exit;
