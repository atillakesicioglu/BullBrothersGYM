<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function default_settings(): array
{
    return [
        'site_name' => 'BULL BROTHERS GYM',
        'logo_path' => 'assets/logo.jpg',
        'hero_title_before' => "ÜSKÜDAR'IN",
        'hero_title_highlight' => 'GÜÇLÜ',
        'hero_title_after' => 'SALONU',
        'hero_subtitle' => '',
        'hero_cta_primary' => 'ÜYE OLUN',
        'hero_cta_secondary' => 'KEŞFET',
        'nav_cta_label' => 'ÜYE OLUN',
        'stat_rating' => '5.0 RATİNG',
        'stat_reviews' => '30+ REVIEWS',
        'stat_location' => 'ÜSKÜDAR / İSTANBUL',
        'stat_hours' => 'AÇILIŞ: 11:30',
        'stat_gallery' => 'FOTOĞRAFLARIMIZ',
        'why_heading' => 'NEDEN BULL BROTHERS?',
        'testimonials_heading_before' => 'ÜYELERİMİZ',
        'testimonials_heading_highlight' => 'NE DİYOR?',
        'gallery_heading' => 'GYM GALLERY',
        'gallery_intro' => '',
        'cta_title_before' => 'GÜCÜNÜ DOĞRU YERDE',
        'cta_title_highlight' => 'İNŞA ET.',
        'cta_title_after' => '',
        'cta_subtitle' => '',
        'cta_cta_primary' => 'ÜYE OLUN',
        'cta_cta_secondary' => 'KEŞFET',
        'contact_heading' => 'İLETİŞİM',
        'contact_address' => '',
        'contact_phone' => '',
        'contact_hours' => '',
        'map_embed_src' => '',
        'footer_about' => '',
        'social_instagram' => '',
        'social_twitter' => '',
        'social_facebook' => '',
        'copyright' => '',
    ];
}

function load_settings(PDO $pdo): array
{
    $out = default_settings();
    $stmt = $pdo->query('SELECT setting_key, setting_value FROM site_settings');
    while ($row = $stmt->fetch()) {
        $out[$row['setting_key']] = $row['setting_value'];
    }
    return $out;
}

function load_features(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM features ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function load_testimonials(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function load_gallery(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM gallery_items ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}
