# Bull Brothers Gym — tek sayfa site + admin

PHP 8+ ve MySQL ile çalışan, koyu temalı tek sayfalık spor salonu sitesi. Metinler, logo, galeri ve yorumlar admin panelinden yönetilir.

## Kurulum

1. Dosyaları hosting’de **web köküne** (ör. `public_html`) veya alt klasöre yükleyin.
2. `includes/config.local.example.php` dosyasını kopyalayıp `includes/config.local.php` olarak kaydedin; veritabanı bilgilerinizi girin.
3. phpMyAdmin’de boş bir veritabanı oluşturun, ardından `database/schema.sql` dosyasını **içe aktarın**.
4. `uploads` klasörünün yazılabilir olduğundan emin olun (chmod 755 veya 775).
5. Tarayıcıdan sitenizi açın. Admin: `https://alanadiniz.com/admin/login.php`

### Varsayılan giriş

- Kullanıcı adı: `admin`
- Şifre: `bullbrothers`

Giriş yaptıktan sonra **Şifre** sekmesinden şifreyi değiştirin. Giriş olmazsa bir kez `install.php` ile şifreyi yenileyin; ardından **`install.php` dosyasını sunucudan silin**.

## Yapı

| Yol | Açıklama |
|-----|----------|
| `index.php` | Ziyaretçi tek sayfa |
| `admin/` | Yönetim paneli |
| `includes/` | PDO, oturum, yardımcılar |
| `assets/` | Logo, `js/site.js` |
| `uploads/` | Yüklenen görseller |
| `database/schema.sql` | MySQL şeması + örnek veri |

## Notlar

- Harita için Google Maps’te **Paylaş → Harita yerleştir** ile alınan `iframe` **src** URL’sini admin panelinde **İletişim** bölümüne yapıştırın.
- Tailwind bu projede CDN ile yüklenir; canlı site internete çıkmalıdır (veya CDN erişimi olmalıdır).
