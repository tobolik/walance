<?php
/**
 * Přihlášení do CRM administrace
 */
session_start();
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/db.php';
$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['walance_admin'] = true;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Nesprávné heslo.';
}

if (isset($_SESSION['walance_admin'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALANCE Admin - Přihlášení</title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap?v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">WALANCE CRM</h1>
        <p class="text-slate-500 text-sm mb-8">Přihlaste se pro správu kontaktů</p>
        <?php if (isset($error)): ?>
            <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-4 text-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Heslo</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-lg transition-colors">
                Přihlásit se
            </button>
        </form>
        <p class="mt-6 text-xs text-slate-400 text-center">
            <a href="../" class="text-teal-600 hover:underline">← Zpět na web</a>
        </p>
    </div>
</body>
</html>
