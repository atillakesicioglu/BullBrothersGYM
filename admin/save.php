<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_save.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash_set('err', 'Geçersiz istek.');
    redirect('index.php');
}

if (!csrf_verify()) {
    flash_set('err', 'Oturum doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.');
    redirect('index.php');
}

$pdo = db();
$tab = admin_handle_post($pdo);
redirect('index.php?tab=' . rawurlencode($tab));
