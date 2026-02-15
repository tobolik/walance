<?php
session_start();
unset($_SESSION['walance_admin'], $_SESSION['walance_admin_user_id'], $_SESSION['walance_admin_name']);
header('Location: index.php');
exit;
