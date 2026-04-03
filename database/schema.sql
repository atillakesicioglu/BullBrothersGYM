-- Bull Brothers Gym — phpMyAdmin içe aktarımı
-- Veritabanı: önce boş bir DB oluşturun, sonra bu dosyayı çalıştırın.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `gallery_items`;
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `features`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `admin_users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `admin_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan giriş: kullanıcı adı `admin`, şifre `bullbrothers` — ilk fırsatta değiştirin.
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2b$12$lBO.SmJ2DplUnq.r4Z6WxeE19g6NDqd8mE7moW9xN2ZaQ99OYHfaK');

CREATE TABLE `site_settings` (
  `setting_key` varchar(128) NOT NULL,
  `setting_value` mediumtext,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'BULL BROTHERS GYM'),
('logo_path', 'assets/logo.jpg'),
('hero_title_before', 'ÜSKÜDAR''IN'),
('hero_title_highlight', 'GÜÇLÜ'),
('hero_title_after', 'SALONU'),
('hero_subtitle', 'Profesyonel ekipman, samimi ortam ve hedeflerinize odaklanan antrenörlerle gücünüzü bir üst seviyeye taşıyın.'),
('hero_cta_primary', 'ÜYE OLUN'),
('hero_cta_secondary', 'KEŞFET'),
('nav_cta_label', 'ÜYE OLUN'),
('stat_rating', '5.0 RATİNG'),
('stat_reviews', '30+ REVIEWS'),
('stat_location', 'ÜSKÜDAR / İSTANBUL'),
('stat_hours', 'AÇILIŞ: 11:30'),
('stat_gallery', 'FOTOĞRAFLARIMIZ'),
('why_heading', 'NEDEN BULL BROTHERS?'),
('testimonials_heading_before', 'ÜYELERİMİZ'),
('testimonials_heading_highlight', 'NE DİYOR?'),
('gallery_heading', 'GYM GALLERY'),
('gallery_intro', 'Salonumuzdan kareler — ekipmanlar ve antrenman atmosferi.'),
('cta_title_before', 'GÜCÜNÜ DOĞRU YERDE'),
('cta_title_highlight', 'İNŞA ET.'),
('cta_title_after', ''),
('cta_subtitle', 'Hedeflerine uygun program, düzenli takip ve motive edici bir topluluk seni bekliyor.'),
('cta_cta_primary', 'ÜYE OLUN'),
('cta_cta_secondary', 'KEŞFET'),
('contact_heading', 'İLETİŞİM'),
('contact_address', 'Örnek Mahalle, Spor Sokak No:1, Üsküdar / İstanbul'),
('contact_phone', '+90 555 000 00 00'),
('contact_hours', 'Her gün 11:30 – 23:00'),
('map_embed_src', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.0!2d29.02!3d41.02!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDAxJzEyLjAiTiAyOcKwMDEnMTIuMCJF!5e0!3m2!1str!2str!4v1'),
('footer_about', 'Üsküdar''da modern ekipman ve deneyimli ekip ile güç ve form kazanın.'),
('social_instagram', 'https://instagram.com/'),
('social_twitter', 'https://twitter.com/'),
('social_facebook', 'https://facebook.com/'),
('copyright', '© 2026 Bull Brothers Gym. Tüm hakları saklıdır.');

CREATE TABLE `features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` int NOT NULL DEFAULT 0,
  `icon_emoji` varchar(8) DEFAULT '★',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `features` (`sort_order`, `icon_emoji`, `title`, `description`) VALUES
(1, '🔥', 'SICAK VE SAMİMİ ORTAM', 'Yargısız, motive edici bir atmosferde antrenman yapın; her seviyeye saygı duyuyoruz.'),
(2, '💪', 'İLGİLİ HOCALAR', 'Form ve güvenlik öncelikli, kişiselleştirilmiş yönlendirme ile hedefinize odaklanın.'),
(3, '🏋️', 'MODERN EKİPMAN', 'Serbest ağırlık, fonksiyonel alan ve kardiyo ile tam donanımlı salon.'),
(4, '⏰', 'ESNEK SAATLER', 'Yoğun tempoya uygun çalışma saatleri ile antrenmanını planlamanı kolaylaştırıyoruz.');

CREATE TABLE `testimonials` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` int NOT NULL DEFAULT 0,
  `quote` text NOT NULL,
  `name_title` varchar(255) NOT NULL,
  `avatar_path` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`sort_order`, `quote`, `name_title`) VALUES
(1, 'İlk günden beri hem ekip hem ortam harika. Hedeflerime daha disiplinli yaklaşıyorum.', 'Ayşe K. — Üye'),
(2, 'Hocalar gerçekten ilgili, ekipmanlar temiz ve bakımlı. Üsküdar''daki en iyi tercih.', 'Mehmet T. — Üye'),
(3, 'Güç antrenmanına yeni başladım, her adımda güvenli hissediyorum.', 'Can D. — Üye');

CREATE TABLE `gallery_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` int NOT NULL DEFAULT 0,
  `image_path` varchar(512) NOT NULL,
  `alt_text` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
