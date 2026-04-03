<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/data.php';
require_once dirname(__DIR__) . '/includes/auth.php';

require_admin();

$tab = preg_replace('/[^a-z_]/', '', (string) ($_GET['tab'] ?? 'genel'));
$allowedTabs = ['genel', 'hero', 'istatistik', 'ozellikler', 'yorumlar', 'galeri', 'iletisim', 'sifre'];
if (!in_array($tab, $allowedTabs, true)) {
    $tab = 'genel';
}

$pdo = db();
$s = load_settings($pdo);
$features = load_features($pdo);
$testimonials = load_testimonials($pdo);
$gallery = load_gallery($pdo);

$ok = flash_get('ok');
$err = flash_get('err');
$logoPreview = web_url($s['logo_path'] ?? 'assets/logo.jpg');

function tab_link(string $id, string $label, string $current): string
{
    $active = $id === $current ? 'bg-red-600 text-white' : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700';
    return '<a href="index.php?tab=' . h($id) . '" class="block px-3 py-2 rounded text-sm ' . $active . '">' . h($label) . '</a>';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim — Bull Brothers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen">
    <header class="border-b border-zinc-800 px-4 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="font-bold tracking-wide">Bull Brothers — Admin</div>
        <div class="flex gap-3 text-sm">
            <a href="../index.php" target="_blank" class="text-zinc-400 hover:text-white underline">Siteyi aç</a>
            <a href="logout.php" class="text-red-400 hover:text-red-300">Çıkış</a>
        </div>
    </header>

    <?php if ($ok): ?>
        <div class="mx-4 mt-4 p-3 bg-emerald-950 border border-emerald-800 text-emerald-200 text-sm rounded"><?= h($ok) ?></div>
    <?php endif; ?>
    <?php if ($err): ?>
        <div class="mx-4 mt-4 p-3 bg-red-950 border border-red-800 text-red-200 text-sm rounded"><?= h($err) ?></div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row max-w-7xl mx-auto p-4 gap-6">
        <nav class="md:w-48 shrink-0 flex flex-row md:flex-col flex-wrap gap-2">
            <?= tab_link('genel', 'Genel', $tab) ?>
            <?= tab_link('hero', 'Hero', $tab) ?>
            <?= tab_link('istatistik', 'İstatistik', $tab) ?>
            <?= tab_link('ozellikler', 'Özellikler', $tab) ?>
            <?= tab_link('yorumlar', 'Yorumlar', $tab) ?>
            <?= tab_link('galeri', 'Galeri', $tab) ?>
            <?= tab_link('iletisim', 'İletişim', $tab) ?>
            <?= tab_link('sifre', 'Şifre', $tab) ?>
        </nav>

        <div class="flex-1 min-w-0 space-y-6 pb-16">
            <?php if ($tab === 'genel'): ?>
                <h2 class="text-lg font-semibold">Genel</h2>
                <form action="save.php" method="post" class="space-y-4 max-w-xl">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_settings">
                    <input type="hidden" name="tab" value="genel">
                    <div>
                        <label class="text-xs text-zinc-500">Site adı</label>
                        <input name="site_name" value="<?= h($s['site_name']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Telif / alt bilgi</label>
                        <input name="copyright" value="<?= h($s['copyright']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Footer hakkında</label>
                        <textarea name="footer_about" rows="3" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['footer_about']) ?></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Instagram URL</label>
                        <input name="social_instagram" value="<?= h($s['social_instagram']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Twitter / X URL</label>
                        <input name="social_twitter" value="<?= h($s['social_twitter']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Facebook URL</label>
                        <input name="social_facebook" value="<?= h($s['social_facebook']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded text-sm font-semibold">Kaydet</button>
                </form>
                <div class="border-t border-zinc-800 pt-6 max-w-xl">
                    <h3 class="text-sm font-semibold mb-2">Logo (JPEG / PNG / WebP)</h3>
                    <img src="<?= h($logoPreview) ?>" alt="" class="h-24 object-contain mb-3 bg-zinc-900 rounded p-2">
                    <form action="save.php" method="post" enctype="multipart/form-data" class="flex flex-wrap gap-2 items-end">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="logo_upload">
                        <input type="hidden" name="tab" value="genel">
                        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" required class="text-sm">
                        <button type="submit" class="bg-zinc-700 hover:bg-zinc-600 px-3 py-2 rounded text-sm">Logoyu yükle</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($tab === 'hero'): ?>
                <h2 class="text-lg font-semibold">Hero</h2>
                <form action="save.php" method="post" class="space-y-4 max-w-xl">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_settings">
                    <input type="hidden" name="tab" value="hero">
                    <div>
                        <label class="text-xs text-zinc-500">Başlık (önce)</label>
                        <input name="hero_title_before" value="<?= h($s['hero_title_before']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Vurgulu kelime (kırmızı)</label>
                        <input name="hero_title_highlight" value="<?= h($s['hero_title_highlight']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Başlık (sonra)</label>
                        <input name="hero_title_after" value="<?= h($s['hero_title_after']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Alt metin</label>
                        <textarea name="hero_subtitle" rows="3" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['hero_subtitle']) ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-zinc-500">Birincil buton</label>
                            <input name="hero_cta_primary" value="<?= h($s['hero_cta_primary']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                        </div>
                        <div>
                            <label class="text-xs text-zinc-500">İkincil buton</label>
                            <input name="hero_cta_secondary" value="<?= h($s['hero_cta_secondary']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Menü CTA metni</label>
                        <input name="nav_cta_label" value="<?= h($s['nav_cta_label']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded text-sm font-semibold">Kaydet</button>
                </form>
            <?php endif; ?>

            <?php if ($tab === 'istatistik'): ?>
                <h2 class="text-lg font-semibold">İstatistik şeridi</h2>
                <form action="save.php" method="post" class="space-y-4 max-w-xl">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_settings">
                    <input type="hidden" name="tab" value="istatistik">
                    <?php
                    $stats = ['stat_rating', 'stat_reviews', 'stat_location', 'stat_hours', 'stat_gallery'];
                    $labels = ['Rating', 'Yorumlar', 'Konum', 'Saat', 'Galeri etiketi'];
                    foreach ($stats as $i => $key):
                    ?>
                        <div>
                            <label class="text-xs text-zinc-500"><?= h($labels[$i]) ?></label>
                            <input name="<?= h($key) ?>" value="<?= h($s[$key] ?? '') ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                        </div>
                    <?php endforeach; ?>
                    <div>
                        <label class="text-xs text-zinc-500">Neden biz başlığı</label>
                        <input name="why_heading" value="<?= h($s['why_heading']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Yorumlar başlık (önce)</label>
                        <input name="testimonials_heading_before" value="<?= h($s['testimonials_heading_before']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Yorumlar başlık (vurgu)</label>
                        <input name="testimonials_heading_highlight" value="<?= h($s['testimonials_heading_highlight']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Galeri başlığı</label>
                        <input name="gallery_heading" value="<?= h($s['gallery_heading']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Galeri giriş metni</label>
                        <textarea name="gallery_intro" rows="2" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['gallery_intro']) ?></textarea>
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded text-sm font-semibold">Kaydet</button>
                </form>
            <?php endif; ?>

            <?php if ($tab === 'ozellikler'): ?>
                <h2 class="text-lg font-semibold">Özellikler (Neden Bull Brothers)</h2>
                <div class="space-y-8">
                    <?php foreach ($features as $f): ?>
                        <div class="border border-zinc-800 rounded-lg p-4 max-w-2xl">
                            <form action="save.php" method="post" class="space-y-3">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="feature_save">
                                <input type="hidden" name="tab" value="ozellikler">
                                <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="col-span-1">
                                        <label class="text-xs text-zinc-500">İkon / emoji</label>
                                        <input name="icon_emoji" value="<?= h($f['icon_emoji']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-2 py-2 text-sm mt-1">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="text-xs text-zinc-500">Başlık</label>
                                        <input name="title" value="<?= h($f['title']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-zinc-500">Açıklama</label>
                                    <textarea name="description" rows="3" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($f['description']) ?></textarea>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" class="bg-red-600 hover:bg-red-500 px-3 py-2 rounded text-sm">Güncelle</button>
                                </div>
                            </form>
                            <div class="flex flex-wrap gap-2 mt-3 border-t border-zinc-800 pt-3">
                                <form action="save.php" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="feature_order">
                                    <input type="hidden" name="tab" value="ozellikler">
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <input type="hidden" name="dir" value="up">
                                    <button type="submit" class="text-xs bg-zinc-800 px-2 py-1 rounded">Yukarı</button>
                                </form>
                                <form action="save.php" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="feature_order">
                                    <input type="hidden" name="tab" value="ozellikler">
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <input type="hidden" name="dir" value="down">
                                    <button type="submit" class="text-xs bg-zinc-800 px-2 py-1 rounded">Aşağı</button>
                                </form>
                                <form action="save.php" method="post" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="feature_delete">
                                    <input type="hidden" name="tab" value="ozellikler">
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <button type="submit" class="text-xs text-red-400 bg-zinc-800 px-2 py-1 rounded">Sil</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="border border-dashed border-zinc-700 rounded-lg p-4 max-w-2xl">
                        <h3 class="text-sm font-semibold mb-3">Yeni özellik</h3>
                        <form action="save.php" method="post" class="space-y-3">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="feature_save">
                            <input type="hidden" name="tab" value="ozellikler">
                            <div class="grid grid-cols-4 gap-2">
                                <input name="icon_emoji" placeholder="★" class="bg-zinc-900 border border-zinc-700 rounded px-2 py-2 text-sm">
                                <input name="title" placeholder="Başlık" required class="col-span-3 bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm">
                            </div>
                            <textarea name="description" rows="2" placeholder="Açıklama" required class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm"></textarea>
                            <button type="submit" class="bg-zinc-700 hover:bg-zinc-600 px-3 py-2 rounded text-sm">Ekle</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($tab === 'yorumlar'): ?>
                <h2 class="text-lg font-semibold">Üye yorumları</h2>
                <div class="space-y-8">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="border border-zinc-800 rounded-lg p-4 max-w-2xl">
                            <form action="save.php" method="post" enctype="multipart/form-data" class="space-y-3">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="testimonial_save">
                                <input type="hidden" name="tab" value="yorumlar">
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <div>
                                    <label class="text-xs text-zinc-500">Alıntı</label>
                                    <textarea name="quote" rows="3" required class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($t['quote']) ?></textarea>
                                </div>
                                <div>
                                    <label class="text-xs text-zinc-500">İsim / unvan</label>
                                    <input name="name_title" value="<?= h($t['name_title']) ?>" required class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                                </div>
                                <?php if (!empty($t['avatar_path'])): ?>
                                    <img src="<?= h(web_url($t['avatar_path'])) ?>" alt="" class="h-12 w-12 object-cover rounded">
                                <?php endif; ?>
                                <div>
                                    <label class="text-xs text-zinc-500">Yeni avatar (isteğe bağlı)</label>
                                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="block text-sm mt-1">
                                </div>
                                <button type="submit" class="bg-red-600 hover:bg-red-500 px-3 py-2 rounded text-sm">Güncelle</button>
                            </form>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <form action="save.php" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="testimonial_order">
                                    <input type="hidden" name="tab" value="yorumlar">
                                    <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                    <input type="hidden" name="dir" value="up">
                                    <button type="submit" class="text-xs bg-zinc-800 px-2 py-1 rounded">Yukarı</button>
                                </form>
                                <form action="save.php" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="testimonial_order">
                                    <input type="hidden" name="tab" value="yorumlar">
                                    <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                    <input type="hidden" name="dir" value="down">
                                    <button type="submit" class="text-xs bg-zinc-800 px-2 py-1 rounded">Aşağı</button>
                                </form>
                                <form action="save.php" method="post" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="testimonial_delete">
                                    <input type="hidden" name="tab" value="yorumlar">
                                    <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                    <button type="submit" class="text-xs text-red-400 bg-zinc-800 px-2 py-1 rounded">Sil</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="border border-dashed border-zinc-700 rounded-lg p-4 max-w-2xl">
                        <h3 class="text-sm font-semibold mb-3">Yeni yorum</h3>
                        <form action="save.php" method="post" enctype="multipart/form-data" class="space-y-3">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="testimonial_save">
                            <input type="hidden" name="tab" value="yorumlar">
                            <textarea name="quote" rows="3" required class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm" placeholder="Alıntı"></textarea>
                            <input name="name_title" required class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm" placeholder="İsim / unvan">
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="text-sm">
                            <button type="submit" class="bg-zinc-700 hover:bg-zinc-600 px-3 py-2 rounded text-sm">Ekle</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($tab === 'galeri'): ?>
                <h2 class="text-lg font-semibold">Galeri</h2>
                <form action="save.php" method="post" enctype="multipart/form-data" class="mb-8 max-w-xl">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="gallery_upload">
                    <input type="hidden" name="tab" value="galeri">
                    <label class="text-xs text-zinc-500 block mb-1">Çoklu görsel (JPEG, PNG, WebP, max 5 MB)</label>
                    <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required class="text-sm">
                    <button type="submit" class="mt-3 bg-red-600 hover:bg-red-500 px-4 py-2 rounded text-sm font-semibold block">Yükle</button>
                </form>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($gallery as $g): ?>
                        <div class="border border-zinc-800 rounded-lg overflow-hidden">
                            <img src="<?= h(web_url($g['image_path'])) ?>" alt="" class="w-full h-40 object-cover">
                            <div class="p-3 space-y-2">
                                <form action="save.php" method="post" class="flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="gallery_alt">
                                    <input type="hidden" name="tab" value="galeri">
                                    <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                                    <input name="alt_text" value="<?= h($g['alt_text']) ?>" class="flex-1 bg-zinc-900 border border-zinc-700 rounded px-2 py-1 text-xs">
                                    <button type="submit" class="text-xs bg-zinc-700 px-2 rounded">OK</button>
                                </form>
                                <div class="flex flex-wrap gap-1">
                                    <form action="save.php" method="post" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="gallery_order">
                                        <input type="hidden" name="tab" value="galeri">
                                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                                        <input type="hidden" name="dir" value="up">
                                        <button type="submit" class="text-xs bg-zinc-800 px-2 py-1 rounded">↑</button>
                                    </form>
                                    <form action="save.php" method="post" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="gallery_order">
                                        <input type="hidden" name="tab" value="galeri">
                                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                                        <input type="hidden" name="dir" value="down">
                                        <button type="submit" class="text-xs bg-zinc-800 px-2 py-1 rounded">↓</button>
                                    </form>
                                    <form action="save.php" method="post" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="gallery_delete">
                                        <input type="hidden" name="tab" value="galeri">
                                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                                        <button type="submit" class="text-xs text-red-400 bg-zinc-800 px-2 py-1 rounded">Sil</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($tab === 'iletisim'): ?>
                <h2 class="text-lg font-semibold">İletişim & CTA</h2>
                <form action="save.php" method="post" class="space-y-4 max-w-xl">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_settings">
                    <input type="hidden" name="tab" value="iletisim">
                    <div>
                        <label class="text-xs text-zinc-500">İletişim başlığı</label>
                        <input name="contact_heading" value="<?= h($s['contact_heading']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Adres</label>
                        <textarea name="contact_address" rows="2" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['contact_address']) ?></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Telefon</label>
                        <input name="contact_phone" value="<?= h($s['contact_phone']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Çalışma saatleri</label>
                        <textarea name="contact_hours" rows="2" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['contact_hours']) ?></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Harita embed URL (iframe src)</label>
                        <textarea name="map_embed_src" rows="2" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['map_embed_src']) ?></textarea>
                    </div>
                    <hr class="border-zinc-800">
                    <div>
                        <label class="text-xs text-zinc-500">CTA başlık (önce)</label>
                        <input name="cta_title_before" value="<?= h($s['cta_title_before']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">CTA vurgu</label>
                        <input name="cta_title_highlight" value="<?= h($s['cta_title_highlight']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">CTA başlık (sonra)</label>
                        <input name="cta_title_after" value="<?= h($s['cta_title_after']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">CTA alt metin</label>
                        <textarea name="cta_subtitle" rows="2" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1"><?= h($s['cta_subtitle']) ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-zinc-500">CTA birincil</label>
                            <input name="cta_cta_primary" value="<?= h($s['cta_cta_primary']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                        </div>
                        <div>
                            <label class="text-xs text-zinc-500">CTA ikincil</label>
                            <input name="cta_cta_secondary" value="<?= h($s['cta_cta_secondary']) ?>" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                        </div>
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded text-sm font-semibold">Kaydet</button>
                </form>
            <?php endif; ?>

            <?php if ($tab === 'sifre'): ?>
                <h2 class="text-lg font-semibold">Şifre değiştir</h2>
                <form action="save.php" method="post" class="space-y-4 max-w-sm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="password_change">
                    <input type="hidden" name="tab" value="sifre">
                    <div>
                        <label class="text-xs text-zinc-500">Mevcut şifre</label>
                        <input type="password" name="current_password" required autocomplete="current-password" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Yeni şifre (min 8)</label>
                        <input type="password" name="new_password" required minlength="8" autocomplete="new-password" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500">Yeni şifre tekrar</label>
                        <input type="password" name="new_password2" required minlength="8" autocomplete="new-password" class="w-full bg-zinc-900 border border-zinc-700 rounded px-3 py-2 text-sm mt-1">
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded text-sm font-semibold">Güncelle</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
