<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (admin_logged_in()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $err = 'Güvenlik doğrulaması başarısız.';
    } else {
        $user = trim((string) ($_POST['username'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        if (attempt_login($user, $pass)) {
            redirect('index.php');
        }
        $err = 'Kullanıcı adı veya şifre hatalı.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş — Bull Brothers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 flex items-center justify-center p-4">
    <div class="w-full max-w-sm border border-zinc-800 rounded-xl p-8 bg-zinc-900">
        <h1 class="text-xl font-bold tracking-wide mb-6">Bull Brothers — Yönetim</h1>
        <?php if (!empty($err)): ?>
            <p class="text-red-400 text-sm mb-4"><?= h($err) ?></p>
        <?php endif; ?>
        <form method="post" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs text-zinc-500 mb-1">Kullanıcı adı</label>
                <input name="username" required autocomplete="username" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-zinc-500 mb-1">Şifre</label>
                <input type="password" name="password" required autocomplete="current-password" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-500 font-semibold py-2 rounded text-sm">Giriş</button>
        </form>
        <p class="text-zinc-600 text-xs mt-6"><a href="../index.php" class="underline hover:text-zinc-400">Siteye dön</a></p>
    </div>
</body>
</html>
