<?php
declare(strict_types=1);

/**
 * Admin şifresini PHP ile yeniden üretir (varsayılan hash girişte sorun çıkarırsa).
 * Şifreyi ayarladıktan sonra bu dosyayı sunucudan SİLİN veya yeniden adlandırın.
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/db.php';

$err = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim((string) ($_POST['username'] ?? 'admin'));
    $pass = (string) ($_POST['password'] ?? '');
    if (strlen($pass) < 8) {
        $err = 'Şifre en az 8 karakter olmalı.';
    } elseif ($user === '') {
        $err = 'Kullanıcı adı boş olamaz.';
    } else {
        try {
            $pdo = db();
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE username = ?');
            $stmt->execute([$hash, $user]);
            if ($stmt->rowCount() === 0) {
                $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
                $stmt->execute([$user, $hash]);
            }
            $ok = true;
        } catch (Throwable $e) {
            $err = 'Veritabanı hatası: ayarları kontrol edin.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurulum — Admin şifresi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 p-6">
    <div class="max-w-md mx-auto border border-red-900 bg-red-950/30 rounded-xl p-6">
        <h1 class="text-lg font-bold text-red-300 mb-2">Güvenlik uyarısı</h1>
        <p class="text-sm text-zinc-400 mb-6">Bu sayfa herkese açıktır. İşiniz bitince <strong>install.php</strong> dosyasını sunucudan kaldırın.</p>
        <?php if ($ok): ?>
            <p class="text-emerald-400 text-sm mb-4">Şifre güncellendi. <a href="admin/login.php" class="underline">Admin girişi</a></p>
        <?php else: ?>
            <?php if ($err): ?><p class="text-red-400 text-sm mb-4"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <form method="post" class="space-y-4">
                <div>
                    <label class="text-xs text-zinc-500">Kullanıcı adı</label>
                    <input name="username" value="admin" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                </div>
                <div>
                    <label class="text-xs text-zinc-500">Yeni şifre (min 8)</label>
                    <input type="password" name="password" required minlength="8" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-500 font-semibold py-2 rounded text-sm">Kaydet</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
