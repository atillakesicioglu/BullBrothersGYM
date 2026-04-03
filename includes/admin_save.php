<?php
declare(strict_types=1);

require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/helpers.php';

function setting_keys_allowed(): array
{
    return [
        'site_name', 'logo_path',
        'hero_title_before', 'hero_title_highlight', 'hero_title_after', 'hero_subtitle',
        'hero_cta_primary', 'hero_cta_secondary', 'nav_cta_label',
        'stat_rating', 'stat_reviews', 'stat_location', 'stat_hours', 'stat_gallery',
        'why_heading', 'testimonials_heading_before', 'testimonials_heading_highlight',
        'gallery_heading', 'gallery_intro',
        'cta_title_before', 'cta_title_highlight', 'cta_title_after', 'cta_subtitle',
        'cta_cta_primary', 'cta_cta_secondary',
        'contact_heading', 'contact_address', 'contact_phone', 'contact_hours', 'map_embed_src',
        'footer_about', 'social_instagram', 'social_twitter', 'social_facebook', 'copyright',
    ];
}

function upsert_setting(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

function admin_handle_post(PDO $pdo): string
{
    $action = $_POST['action'] ?? '';
    $tab = preg_replace('/[^a-z_]/', '', (string) ($_POST['tab'] ?? 'genel'));

    try {
        switch ($action) {
            case 'save_settings':
                foreach (setting_keys_allowed() as $key) {
                    if (!array_key_exists($key, $_POST)) {
                        continue;
                    }
                    $val = is_string($_POST[$key]) ? $_POST[$key] : '';
                    upsert_setting($pdo, $key, $val);
                }
                flash_set('ok', 'Ayarlar kaydedildi.');
                break;

            case 'logo_upload':
                if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new RuntimeException('Dosya seçilmedi.');
                }
                $path = store_uploaded_image($_FILES['logo']);
                upsert_setting($pdo, 'logo_path', $path);
                flash_set('ok', 'Logo güncellendi.');
                $tab = 'genel';
                break;

            case 'feature_save':
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                $title = trim((string) ($_POST['title'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));
                $icon = trim((string) ($_POST['icon_emoji'] ?? '★'));
                if ($title === '') {
                    throw new RuntimeException('Başlık zorunlu.');
                }
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE features SET icon_emoji=?, title=?, description=? WHERE id=?');
                    $stmt->execute([$icon, $title, $description, $id]);
                    flash_set('ok', 'Özellik güncellendi.');
                } else {
                    $max = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM features')->fetchColumn();
                    $stmt = $pdo->prepare('INSERT INTO features (sort_order, icon_emoji, title, description) VALUES (?,?,?,?)');
                    $stmt->execute([$max, $icon, $title, $description]);
                    flash_set('ok', 'Özellik eklendi.');
                }
                $tab = 'ozellikler';
                break;

            case 'feature_delete':
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0) {
                    $pdo->prepare('DELETE FROM features WHERE id=?')->execute([$id]);
                    flash_set('ok', 'Özellik silindi.');
                }
                $tab = 'ozellikler';
                break;

            case 'feature_order':
                reorder_row($pdo, 'features', (int) ($_POST['id'] ?? 0), (string) ($_POST['dir'] ?? ''));
                flash_set('ok', 'Sıra güncellendi.');
                $tab = 'ozellikler';
                break;

            case 'testimonial_save':
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                $quote = trim((string) ($_POST['quote'] ?? ''));
                $nameTitle = trim((string) ($_POST['name_title'] ?? ''));
                if ($quote === '' || $nameTitle === '') {
                    throw new RuntimeException('Alıntı ve isim zorunlu.');
                }
                $avatarPath = null;
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $avatarPath = store_uploaded_image($_FILES['avatar']);
                }
                if ($id > 0) {
                    if ($avatarPath) {
                        delete_upload_if_owned($pdo, 'testimonials', $id, 'avatar_path');
                        $stmt = $pdo->prepare('UPDATE testimonials SET quote=?, name_title=?, avatar_path=? WHERE id=?');
                        $stmt->execute([$quote, $nameTitle, $avatarPath, $id]);
                    } else {
                        $stmt = $pdo->prepare('UPDATE testimonials SET quote=?, name_title=? WHERE id=?');
                        $stmt->execute([$quote, $nameTitle, $id]);
                    }
                    flash_set('ok', 'Yorum güncellendi.');
                } else {
                    $max = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM testimonials')->fetchColumn();
                    $stmt = $pdo->prepare('INSERT INTO testimonials (sort_order, quote, name_title, avatar_path) VALUES (?,?,?,?)');
                    $stmt->execute([$max, $quote, $nameTitle, $avatarPath]);
                    flash_set('ok', 'Yorum eklendi.');
                }
                $tab = 'yorumlar';
                break;

            case 'testimonial_delete':
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0) {
                    delete_upload_if_owned($pdo, 'testimonials', $id, 'avatar_path');
                    $pdo->prepare('DELETE FROM testimonials WHERE id=?')->execute([$id]);
                    flash_set('ok', 'Yorum silindi.');
                }
                $tab = 'yorumlar';
                break;

            case 'testimonial_order':
                reorder_row($pdo, 'testimonials', (int) ($_POST['id'] ?? 0), (string) ($_POST['dir'] ?? ''));
                flash_set('ok', 'Sıra güncellendi.');
                $tab = 'yorumlar';
                break;

            case 'gallery_upload':
                if (empty($_FILES['images'])) {
                    throw new RuntimeException('Dosya seçilmedi.');
                }
                $files = $_FILES['images'];
                $n = is_array($files['name']) ? count($files['name']) : 0;
                $count = 0;
                for ($i = 0; $i < $n; $i++) {
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $single = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i],
                    ];
                    $path = store_uploaded_image($single);
                    $max = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM gallery_items')->fetchColumn();
                    $alt = 'Galeri';
                    $stmt = $pdo->prepare('INSERT INTO gallery_items (sort_order, image_path, alt_text) VALUES (?,?,?)');
                    $stmt->execute([$max, $path, $alt]);
                    $count++;
                }
                flash_set('ok', $count . ' görsel yüklendi.');
                $tab = 'galeri';
                break;

            case 'gallery_alt':
                $id = (int) ($_POST['id'] ?? 0);
                $alt = trim((string) ($_POST['alt_text'] ?? ''));
                if ($id > 0) {
                    $pdo->prepare('UPDATE gallery_items SET alt_text=? WHERE id=?')->execute([$alt, $id]);
                    flash_set('ok', 'Alt metin güncellendi.');
                }
                $tab = 'galeri';
                break;

            case 'gallery_delete':
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0) {
                    $stmt = $pdo->prepare('SELECT image_path FROM gallery_items WHERE id=?');
                    $stmt->execute([$id]);
                    $row = $stmt->fetch();
                    if ($row && str_starts_with($row['image_path'], 'uploads/')) {
                        $full = project_root() . '/' . $row['image_path'];
                        if (is_file($full)) {
                            unlink($full);
                        }
                    }
                    $pdo->prepare('DELETE FROM gallery_items WHERE id=?')->execute([$id]);
                    flash_set('ok', 'Görsel silindi.');
                }
                $tab = 'galeri';
                break;

            case 'gallery_order':
                reorder_row($pdo, 'gallery_items', (int) ($_POST['id'] ?? 0), (string) ($_POST['dir'] ?? ''));
                flash_set('ok', 'Sıra güncellendi.');
                $tab = 'galeri';
                break;

            case 'password_change':
                $cur = (string) ($_POST['current_password'] ?? '');
                $new = (string) ($_POST['new_password'] ?? '');
                $again = (string) ($_POST['new_password2'] ?? '');
                if (strlen($new) < 8) {
                    throw new RuntimeException('Yeni şifre en az 8 karakter olmalı.');
                }
                if ($new !== $again) {
                    throw new RuntimeException('Yeni şifreler eşleşmiyor.');
                }
                $aid = (int) ($_SESSION['admin_id'] ?? 0);
                $stmt = $pdo->prepare('SELECT password_hash FROM admin_users WHERE id=?');
                $stmt->execute([$aid]);
                $row = $stmt->fetch();
                if (!$row || !password_verify($cur, $row['password_hash'])) {
                    throw new RuntimeException('Mevcut şifre hatalı.');
                }
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE admin_users SET password_hash=? WHERE id=?')->execute([$hash, $aid]);
                flash_set('ok', 'Şifre değiştirildi.');
                $tab = 'sifre';
                break;

            default:
                flash_set('err', 'Bilinmeyen işlem.');
        }
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
    }

    return $tab;
}

function delete_upload_if_owned(PDO $pdo, string $table, int $id, string $col): void
{
    $stmt = $pdo->prepare("SELECT `$col` AS p FROM `$table` WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['p']) && str_starts_with((string) $row['p'], 'uploads/')) {
        $full = project_root() . '/' . $row['p'];
        if (is_file($full)) {
            unlink($full);
        }
    }
}

function reorder_row(PDO $pdo, string $table, int $id, string $dir): void
{
    if ($id <= 0 || ($dir !== 'up' && $dir !== 'down')) {
        return;
    }
    $stmt = $pdo->prepare("SELECT id, sort_order FROM `$table` WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        return;
    }
    $order = (int) $row['sort_order'];
    if ($dir === 'up') {
        $q = "SELECT id, sort_order FROM `$table` WHERE sort_order < ? ORDER BY sort_order DESC LIMIT 1";
        $stmt = $pdo->prepare($q);
        $stmt->execute([$order]);
    } else {
        $q = "SELECT id, sort_order FROM `$table` WHERE sort_order > ? ORDER BY sort_order ASC LIMIT 1";
        $stmt = $pdo->prepare($q);
        $stmt->execute([$order]);
    }
    $other = $stmt->fetch();
    if (!$other) {
        return;
    }
    $pdo->prepare("UPDATE `$table` SET sort_order=? WHERE id=?")->execute([$other['sort_order'], $id]);
    $pdo->prepare("UPDATE `$table` SET sort_order=? WHERE id=?")->execute([$order, $other['id']]);
}
