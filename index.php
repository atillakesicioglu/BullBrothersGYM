<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/db.php';

$settings = default_settings();
$features = [];
$testimonials = [];
$gallery = [];
$dbError = null;

try {
    $pdo = db();
    $settings = load_settings($pdo);
    $features = load_features($pdo);
    $testimonials = load_testimonials($pdo);
    $gallery = load_gallery($pdo);
} catch (Throwable $e) {
    $dbError = 'Veritabanı bağlantısı kurulamadı. `includes/config.local.php` ayarlarını ve phpMyAdmin içe aktarımını kontrol edin.';
}

$s = $settings;
$siteName = $s['site_name'] ?? 'BULL BROTHERS GYM';
$logoUrl = web_url($s['logo_path'] ?? 'assets/logo.jpg');
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { red: '#dc2626', dark: '#0a0a0a', card: '#141414' }
                    },
                    fontFamily: {
                        display: ['"Bebas Neue"', 'sans-serif'],
                        body: ['"DM Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <style>
        .hero-bull-bg {
            background-image: radial-gradient(ellipse 80% 60% at 50% 40%, rgba(220, 38, 38, 0.08) 0%, transparent 55%);
        }
        .cta-bull-bg {
            background-image: radial-gradient(ellipse 70% 80% at 70% 50%, rgba(59, 130, 246, 0.12) 0%, transparent 50%);
        }
    </style>
</head>
<body class="bg-brand-dark text-zinc-100 font-body antialiased">
<?php if ($dbError): ?>
    <div class="bg-red-950 text-red-200 px-4 py-3 text-center text-sm"><?= h($dbError) ?></div>
<?php endif; ?>

<header class="fixed top-0 left-0 right-0 z-50 border-b border-zinc-800/80 bg-brand-dark/95 backdrop-blur">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-16 md:h-18">
        <a href="#hero" class="font-display text-lg md:text-xl tracking-wider text-white shrink-0"><?= h($siteName) ?></a>
        <nav class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest text-zinc-300">
            <a href="#hero" class="hover:text-white transition">ANA SAYFA</a>
            <a href="#yorumlar" class="hover:text-white transition">EKİP</a>
            <a href="#neden" class="hover:text-white transition">HİZMETLER</a>
            <a href="#galeri" class="hover:text-white transition">GALERİ</a>
            <a href="#iletisim" class="hover:text-white transition">İLETİŞİM</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="#iletisim" class="hidden sm:inline-flex bg-brand-red hover:bg-red-600 text-white text-xs font-bold tracking-widest px-4 py-2 rounded transition"><?= h($s['nav_cta_label']) ?></a>
            <button type="button" id="nav-toggle" class="md:hidden p-2 text-zinc-300 hover:text-white" aria-label="Menü">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div id="nav-mobile" class="hidden md:hidden border-t border-zinc-800 bg-brand-dark px-4 py-4 space-y-3 text-sm font-semibold tracking-widest">
        <a href="#hero" class="block text-zinc-300">ANA SAYFA</a>
        <a href="#yorumlar" class="block text-zinc-300">EKİP</a>
        <a href="#neden" class="block text-zinc-300">HİZMETLER</a>
        <a href="#galeri" class="block text-zinc-300">GALERİ</a>
        <a href="#iletisim" class="block text-zinc-300">İLETİŞİM</a>
        <a href="#iletisim" class="block bg-brand-red text-center py-2 rounded"><?= h($s['nav_cta_label']) ?></a>
    </div>
</header>

<main class="pt-16">
    <section id="hero" class="relative min-h-[88vh] flex flex-col items-center justify-center px-4 hero-bull-bg overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-[0.07]" style="background-image: url('<?= h($logoUrl) ?>'); background-size: min(90vw, 520px); background-position: center 35%; background-repeat: no-repeat;"></div>
        <div class="relative z-10 text-center max-w-3xl mx-auto">
            <div class="mb-8 inline-block rounded-lg border border-zinc-700/50 bg-black/40 p-3 shadow-xl">
                <img src="<?= h($logoUrl) ?>" alt="<?= h($siteName) ?>" class="h-28 md:h-36 w-auto mx-auto object-contain" width="280" height="200">
            </div>
            <h1 class="font-display text-4xl sm:text-5xl md:text-7xl tracking-wide text-white leading-tight">
                <?= h($s['hero_title_before']) ?>
                <span class="text-brand-red"><?= h($s['hero_title_highlight']) ?></span>
                <?= h($s['hero_title_after']) ?>
            </h1>
            <p class="mt-6 text-zinc-400 text-base md:text-lg max-w-xl mx-auto leading-relaxed"><?= nl2br(h($s['hero_subtitle'])) ?></p>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="#iletisim" class="inline-flex bg-brand-red hover:bg-red-600 text-white font-bold tracking-widest text-sm px-8 py-3 rounded transition"><?= h($s['hero_cta_primary']) ?></a>
                <a href="#neden" class="inline-flex border border-zinc-500 hover:border-white text-white font-bold tracking-widest text-sm px-8 py-3 rounded transition bg-zinc-900/50"><?= h($s['hero_cta_secondary']) ?></a>
            </div>
        </div>
    </section>

    <section id="istatistikler" class="border-y border-zinc-800 bg-zinc-900/50">
        <div class="max-w-6xl mx-auto px-4 py-4 grid grid-cols-2 md:grid-cols-5 gap-4 text-center text-[11px] md:text-xs font-semibold tracking-wider text-zinc-400">
            <div class="flex flex-col md:flex-row items-center justify-center gap-2"><span class="text-brand-red">★</span> <?= h($s['stat_rating']) ?></div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-2"><span class="text-brand-red">💬</span> <?= h($s['stat_reviews']) ?></div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-2"><span class="text-brand-red">📍</span> <?= h($s['stat_location']) ?></div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-2"><span class="text-brand-red">🕐</span> <?= h($s['stat_hours']) ?></div>
            <div class="col-span-2 md:col-span-1 flex flex-col md:flex-row items-center justify-center gap-2">
                <a href="#galeri" class="text-brand-red hover:text-red-400 transition"><?= h($s['stat_gallery']) ?></a>
            </div>
        </div>
    </section>

    <section id="neden" class="py-20 md:py-28 px-4 max-w-6xl mx-auto">
        <h2 class="font-display text-4xl md:text-5xl text-white text-center mb-14 tracking-wide"><?= h($s['why_heading']) ?></h2>
        <div class="grid sm:grid-cols-2 gap-8">
            <?php foreach ($features as $f): ?>
                <div class="rounded-xl border border-zinc-800 bg-brand-card p-8 hover:border-zinc-700 transition">
                    <div class="text-2xl mb-4"><?= h($f['icon_emoji']) ?></div>
                    <h3 class="font-bold text-lg text-white tracking-wide mb-3"><?= h($f['title']) ?></h3>
                    <p class="text-zinc-400 text-sm leading-relaxed"><?= nl2br(h($f['description'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="yorumlar" class="py-20 md:py-28 px-4 bg-zinc-900/30 border-y border-zinc-800">
        <div class="max-w-6xl mx-auto">
            <h2 class="font-display text-4xl md:text-5xl text-center mb-14 tracking-wide">
                <?= h($s['testimonials_heading_before']) ?>
                <span class="text-brand-red"><?= h($s['testimonials_heading_highlight']) ?></span>
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($testimonials as $t): ?>
                    <article class="rounded-xl border border-zinc-800 bg-brand-card p-8 flex flex-col">
                        <div class="text-brand-red text-sm mb-4">★★★★★</div>
                        <p class="text-zinc-300 text-sm leading-relaxed flex-1"><?= nl2br(h($t['quote'])) ?></p>
                        <div class="mt-6 flex items-center gap-3">
                            <?php if (!empty($t['avatar_path'])): ?>
                                <img src="<?= h(web_url($t['avatar_path'])) ?>" alt="" class="w-10 h-10 rounded object-cover">
                            <?php else: ?>
                                <span class="w-10 h-10 bg-brand-red/80 rounded shrink-0"></span>
                            <?php endif; ?>
                            <span class="text-sm font-semibold text-white"><?= h($t['name_title']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="galeri" class="py-20 md:py-28 px-4 max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <h2 class="font-display text-4xl md:text-5xl text-white tracking-wide"><?= h($s['gallery_heading']) ?></h2>
            <p class="text-zinc-400 text-sm max-w-md"><?= nl2br(h($s['gallery_intro'])) ?></p>
        </div>
        <?php if (count($gallery) === 0): ?>
            <p class="text-zinc-500 text-center py-16 border border-dashed border-zinc-700 rounded-xl">Galeri görselleri admin panelinden eklenebilir.</p>
        <?php else: ?>
            <div class="gallery-grid grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-4 md:gap-3">
                <?php
                $first = $gallery[0] ?? null;
                $rest = array_slice($gallery, 1);
                ?>
                <?php if ($first): ?>
                    <button type="button" class="gallery-item md:row-span-2 relative group overflow-hidden rounded-xl border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-brand-red"
                        data-full="<?= h(web_url($first['image_path'])) ?>" data-alt="<?= h($first['alt_text']) ?>">
                        <img src="<?= h(web_url($first['image_path'])) ?>" alt="<?= h($first['alt_text']) ?>" class="w-full h-full min-h-[240px] md:min-h-[420px] object-cover grayscale group-hover:grayscale-0 transition duration-500">
                    </button>
                <?php endif; ?>
                <div class="flex flex-col gap-3 md:col-span-2">
                    <?php if (isset($rest[0])): $g = $rest[0]; ?>
                        <button type="button" class="gallery-item relative group overflow-hidden rounded-xl border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-brand-red"
                            data-full="<?= h(web_url($g['image_path'])) ?>" data-alt="<?= h($g['alt_text']) ?>">
                            <img src="<?= h(web_url($g['image_path'])) ?>" alt="<?= h($g['alt_text']) ?>" class="w-full h-48 object-cover grayscale group-hover:grayscale-0 transition duration-500">
                        </button>
                    <?php endif; ?>
                    <?php if (isset($rest[1])): $g = $rest[1]; ?>
                        <button type="button" class="gallery-item relative group overflow-hidden rounded-xl border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-brand-red"
                            data-full="<?= h(web_url($g['image_path'])) ?>" data-alt="<?= h($g['alt_text']) ?>">
                            <img src="<?= h(web_url($g['image_path'])) ?>" alt="<?= h($g['alt_text']) ?>" class="w-full h-48 object-cover grayscale group-hover:grayscale-0 transition duration-500">
                        </button>
                    <?php endif; ?>
                </div>
                <?php if (isset($rest[2])): $g = $rest[2]; ?>
                    <button type="button" class="gallery-item md:col-span-3 relative group overflow-hidden rounded-xl border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-brand-red"
                        data-full="<?= h(web_url($g['image_path'])) ?>" data-alt="<?= h($g['alt_text']) ?>">
                        <img src="<?= h(web_url($g['image_path'])) ?>" alt="<?= h($g['alt_text']) ?>" class="w-full h-52 object-cover grayscale group-hover:grayscale-0 transition duration-500">
                    </button>
                <?php endif; ?>
                <?php for ($i = 3; $i < count($rest); $i++): $g = $rest[$i]; ?>
                    <button type="button" class="gallery-item md:col-span-1 relative group overflow-hidden rounded-xl border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-brand-red"
                        data-full="<?= h(web_url($g['image_path'])) ?>" data-alt="<?= h($g['alt_text']) ?>">
                        <img src="<?= h(web_url($g['image_path'])) ?>" alt="<?= h($g['alt_text']) ?>" class="w-full h-48 object-cover grayscale group-hover:grayscale-0 transition duration-500">
                    </button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </section>

    <section id="cta" class="relative py-20 md:py-28 px-4 border-y border-zinc-800 bg-zinc-900/40 overflow-hidden cta-bull-bg">
        <div class="absolute inset-0 pointer-events-none opacity-[0.06]" style="background-image: url('<?= h($logoUrl) ?>'); background-size: min(70vw, 400px); background-position: 85% center; background-repeat: no-repeat;"></div>
        <div class="relative z-10 max-w-3xl mx-auto text-center">
            <h2 class="font-display text-3xl md:text-5xl text-white tracking-wide leading-tight">
                <?= h($s['cta_title_before']) ?>
                <span class="text-brand-red"><?= h($s['cta_title_highlight']) ?></span>
                <?= h($s['cta_title_after']) ?>
            </h2>
            <p class="mt-6 text-zinc-400"><?= nl2br(h($s['cta_subtitle'])) ?></p>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="#iletisim" class="inline-flex bg-brand-red hover:bg-red-600 text-white font-bold tracking-widest text-sm px-8 py-3 rounded transition"><?= h($s['cta_cta_primary']) ?></a>
                <a href="#galeri" class="inline-flex border border-zinc-500 hover:border-white text-white font-bold tracking-widest text-sm px-8 py-3 rounded transition bg-black/30"><?= h($s['cta_cta_secondary']) ?></a>
            </div>
        </div>
    </section>

    <section id="iletisim" class="py-20 md:py-28 px-4 max-w-6xl mx-auto">
        <h2 class="font-display text-4xl md:text-5xl text-white mb-12 tracking-wide"><?= h($s['contact_heading']) ?></h2>
        <div class="grid md:grid-cols-2 gap-10">
            <div class="space-y-8 text-zinc-400">
                <div class="flex gap-4">
                    <span class="text-brand-red text-xl shrink-0">📍</span>
                    <div>
                        <div class="text-white font-semibold text-sm tracking-wider mb-1">ADRES</div>
                        <p class="text-sm leading-relaxed"><?= nl2br(h($s['contact_address'])) ?></p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <span class="text-brand-red text-xl shrink-0">📞</span>
                    <div>
                        <div class="text-white font-semibold text-sm tracking-wider mb-1">TELEFON</div>
                        <a href="tel:<?= h(preg_replace('/\s+/', '', $s['contact_phone'])) ?>" class="text-sm hover:text-white transition"><?= h($s['contact_phone']) ?></a>
                    </div>
                </div>
                <div class="flex gap-4">
                    <span class="text-brand-red text-xl shrink-0">🕐</span>
                    <div>
                        <div class="text-white font-semibold text-sm tracking-wider mb-1">ÇALIŞMA SAATLERİ</div>
                        <p class="text-sm"><?= nl2br(h($s['contact_hours'])) ?></p>
                    </div>
                </div>
                <div class="flex gap-4 pt-2">
                    <?php if ($s['social_instagram'] !== ''): ?>
                        <a href="<?= h($s['social_instagram']) ?>" class="text-zinc-500 hover:text-brand-red text-sm" target="_blank" rel="noopener">Instagram</a>
                    <?php endif; ?>
                    <?php if ($s['social_twitter'] !== ''): ?>
                        <a href="<?= h($s['social_twitter']) ?>" class="text-zinc-500 hover:text-brand-red text-sm" target="_blank" rel="noopener">Twitter</a>
                    <?php endif; ?>
                    <?php if ($s['social_facebook'] !== ''): ?>
                        <a href="<?= h($s['social_facebook']) ?>" class="text-zinc-500 hover:text-brand-red text-sm" target="_blank" rel="noopener">Facebook</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="relative rounded-xl overflow-hidden border border-zinc-800 bg-zinc-900 min-h-[280px]">
                <?php if (!empty($s['map_embed_src'])): ?>
                    <iframe title="Konum" src="<?= h($s['map_embed_src']) ?>" class="absolute inset-0 w-full h-full grayscale contrast-125 opacity-90" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <?php endif; ?>
                <a href="<?= h($s['map_embed_src'] ? 'https://maps.google.com/?q=' . rawurlencode($s['contact_address']) : '#') ?>" target="_blank" rel="noopener" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-brand-red hover:bg-red-600 text-white text-xs font-bold tracking-widest px-6 py-2 rounded shadow-lg">KONUM</a>
            </div>
        </div>
    </section>
</main>

<footer class="border-t border-zinc-800 bg-black py-16 px-4">
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-12 text-sm">
        <div>
            <div class="font-display text-xl text-white tracking-wider mb-4"><?= h($siteName) ?></div>
            <p class="text-zinc-500 leading-relaxed"><?= nl2br(h($s['footer_about'])) ?></p>
        </div>
        <div>
            <div class="text-white font-semibold tracking-widest text-xs mb-4">HIZLI ERİŞİM</div>
            <ul class="space-y-2 text-zinc-500">
                <li><a href="#hero" class="hover:text-brand-red transition">Ana Sayfa</a></li>
                <li><a href="#yorumlar" class="hover:text-brand-red transition">Ekip / Yorumlar</a></li>
                <li><a href="#neden" class="hover:text-brand-red transition">Hizmetler</a></li>
                <li><a href="#galeri" class="hover:text-brand-red transition">Galeri</a></li>
                <li><a href="#iletisim" class="hover:text-brand-red transition">İletişim</a></li>
            </ul>
        </div>
        <div>
            <div class="text-white font-semibold tracking-widest text-xs mb-4">SOSYAL MEDYA</div>
            <ul class="space-y-2 text-zinc-500">
                <?php if ($s['social_instagram'] !== ''): ?>
                    <li><a href="<?= h($s['social_instagram']) ?>" class="hover:text-brand-red transition" target="_blank" rel="noopener">Instagram</a></li>
                <?php endif; ?>
                <?php if ($s['social_twitter'] !== ''): ?>
                    <li><a href="<?= h($s['social_twitter']) ?>" class="hover:text-brand-red transition" target="_blank" rel="noopener">Twitter</a></li>
                <?php endif; ?>
                <?php if ($s['social_facebook'] !== ''): ?>
                    <li><a href="<?= h($s['social_facebook']) ?>" class="hover:text-brand-red transition" target="_blank" rel="noopener">Facebook</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <p class="text-center text-zinc-600 text-xs mt-12"><?= h($s['copyright']) ?></p>
</footer>

<div id="lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 p-4" role="dialog" aria-modal="true">
    <button type="button" id="lightbox-close" class="absolute top-4 right-4 text-white text-2xl z-10 hover:text-brand-red">&times;</button>
    <img id="lightbox-img" src="" alt="" class="max-h-[90vh] max-w-full object-contain rounded-lg border border-zinc-700">
</div>

<script src="<?= h(web_url('assets/js/site.js')) ?>"></script>
</body>
</html>
