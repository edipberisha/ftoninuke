# 🚀 FtoniNuke

> **Modern PHP-Nuke** — Klasik PHP-Nuke ruhunu 2026 standartlarında yeniden yorumlayan, açık kaynak CMS.

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)](https://php.net)
[![MariaDB](https://img.shields.io/badge/MariaDB-12.x-003545?logo=mariadb)](https://mariadb.org)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker)](https://docker.com)
[![License](https://img.shields.io/badge/License-GPL--2.0-blue)](LICENSE)

---

## 📖 FtoniNuke Nedir?

FtoniNuke, **20 yıllık PHP-Nuke mirasını** modern standartlarda yeniden yazan bir içerik yönetim sistemidir (CMS). Eski PHP-Nuke sitelerini modern dünyaya taşımak ve yeni projeler için hafif, hızlı, güvenli bir alternatif sunmak amacıyla geliştirilmektedir.

### Neden FtoniNuke?

- 🎯 **Klasik PHP-Nuke ruhu** — Bloklar, kategoriler, `nuke_` tabloları
- 🛡️ **Modern güvenlik** — PDO Prepared Statements, BCRYPT, CSRF, 4 katmanlı yorum koruması
- 🐳 **Docker-ready** — Tek komutla kurulum
- 📱 **Responsive** — Mobil uyumlu admin panel ve ön yüz
- 🌍 **Çok dilli** — Kurulum sihirbazı 7 dilde (🇹🇷 🇬🇧 🇦🇱 🇩🇪 🇫🇷 🇮🇹 🇪🇸)
- 🔓 **Açık kaynak** — GPL v2 lisansı

---

## ✨ Özellikler

### İçerik Yönetimi
- ✅ **Haber CRUD** — Quill WYSIWYG editör
- ✅ **Kategori sistemi** — Slug URL'ler, haber sayısı badge'i
- ✅ **Medya kütüphanesi** — Görsel yükleme, picker, otomatik resize
- ✅ **Öne çıkan görsel** — Client-side resize (max 1600px)
- ✅ **Statik sayfalar** — Hakkımızda, Gizlilik, İletişim
- ✅ **İletişim formu** — DB kaydı + SMTP mail bildirim

### Site Yönetimi
- ✅ **Blok sistemi** — 4 tip: HTML, Sosyal Medya, Reklam, PHP dosya
- ✅ **Dinamik menü** — Hiyerarşik, dropdown, mobil uyumlu
- ✅ **Slider** — Son 5 haber, otomatik geçiş
- ✅ **İstatistik bloğu** — Canlı sayılar

### Yorum Sistemi
- ✅ **Threaded yorumlar** — Yanıtla özelliği
- ✅ **4 katmanlı güvenlik:**
  - Honeypot (gizli tuzak)
  - Zaman kontrolü (min 3 sn)
  - Matematik CAPTCHA
  - Rate limit (IP başına saatte 5)
- ✅ **Admin moderasyonu** — Onayla / Reddet / IP Ban / Sil

### Kullanıcı Yönetimi
- ✅ **3 rol:** 👑 Admin / ✏️ Editör / 👤 Üye
- ✅ **Frontend kayıt** — Opsiyonel (ayarlardan açılır)
- ✅ **Self-koruma** — Son admin silinemez

### SEO
- ✅ **Slug URL** — `/public/quill-test-haberi-1`
- ✅ **301 yönlendirme** — Eski `?sid=X` URL'ler
- ✅ **Sitemap.xml** — Dinamik
- ✅ **robots.txt** — Admin dizinleri korumalı

### İçeri Aktarma (Import)
- ✅ **PHP-Nuke Import** — Kategoriler, kullanıcılar, haberler, yorumlar
- ✅ **WordPress Import** — DB direct (WXR + REST API planlı)
- ✅ **Dry-run modu** — Test et, sonra aktar
- ✅ **Import log + rapor**

### Ayarlar
- ✅ **Genel** — Site adı, tema
- ✅ **Üyelik** — Kayıt aç/kapat, varsayılan rol
- ✅ **Yorumlar** — Onay, CAPTCHA, rate limit
- ✅ **Mail (SMTP)** — PHPMailer 7 + test maili

### Altyapı
- ✅ **Migration sistemi** — Versiyonlu DB, rollback
- ✅ **PSR-4** autoload
- ✅ **Composer** bağımlılıkları
- ✅ **Docker** imajı (`ftoninuke-app`)

---

## 🚀 Hızlı Kurulum (Docker)

### 1. MariaDB Başlat

```yaml
version: '3.8'
services:
  db:
    image: mariadb:latest
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: 'guclu-root-sifre'
      MYSQL_DATABASE: ftominuke_db
      MYSQL_USER: ftoni
      MYSQL_PASSWORD: 'guclu-sifre'
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - '3306:3306'

volumes:
  db_data:

docker compose up -d

FtoniNuke'u Klonla ve Build Et
git clone https://github.com/edipberisha/ftoninuke.git
cd ftominuke
docker build -t ftominuke-app:latest .

Container'ı Çalıştır
docker run -d \
  --name ftominuke_app \
  --network database-server_default \
  --restart unless-stopped \
  -p 8085:8000 \
  -v $(pwd):/var/www/html \
  ftominuke-app:latest

Tarayıcıda Çalıştır
http://localhost:8085/install/

5 adımda kurulum tamamlanır. 🌍 7 dil seçeneği mevcuttur.
📚 Dokümantasyon

    📘 Kullanıcı Kılavuzu (TR + EN)

    📗 Geliştirici Kılavuzu (yakında)

    📙 Öğrenci Ders Planı (yakında)

🗂️ Klasör Yapısı
ftominuke/
├── bin/migrate.php           # Migration CLI
├── migrations/                # DB migration dosyaları
├── public/php-modernized/
│   ├── public/               # Web kök dizini
│   │   ├── index.php         # Front controller
│   │   ├── sitemap.php
│   │   └── robots.txt
│   ├── src/
│   │   ├── Core/             # Bootstrap, Database, Router
│   │   ├── Controller/       # Home, Admin, Auth, Page
│   │   ├── Repository/       # News, User, Comment...
│   │   ├── Service/          # Media, Mail, Import
│   │   ├── Views/            # PHP templates
│   │   └── Blocks/           # PHP blok dosyaları
│   ├── config/
│   ├── install/              # Kurulum sihirbazı
│   └── vendor/               # Composer paketleri
├── Dockerfile
└── README.md

🤝 Katkıda Bulunma

Katkılar memnuniyetle karşılanır!

    Fork et
    Feature branch aç: git checkout -b ozellik/yeni-blok
    Commit at: git commit -m "feat: yeni X bloğu"
    Push: git push origin ozellik/yeni-blok
    Pull Request aç

Commit kuralları:
    feat: — Yeni özellik
    fix: — Bug düzeltmesi
    docs: — Doküman
    chore: — Yapılandırma


📋 Yol Haritası

    ☑

    Kurulum + Auth + Migration
    ☑

    Haber CRUD + Medya + Quill
    ☑

    Blok sistemi (4 tip)
    ☑

    3 sütunlu layout + slider
    ☑

    Yorum sistemi (4 katman güvenlik)
    ☑

    SEO URL + sitemap + robots
    ☑

    Ayarlar sistemi (Genel/Üyelik/Yorumlar/Mail)
    ☑

    Kullanıcı yönetimi + frontend kayıt
    ☑

    Statik sayfalar + iletişim formu
    ☑

    PHP-Nuke Import
    ☑

    WordPress Import (DB direct)
    □

    WordPress WXR dosyası desteği
    □

    Tema sistemi (multi-theme)
    □

    REST API (mobil uygulama için)
    □

    Çok dillilik (i18n tam)
    □

    Test coverage
    □

    v1.0 yayını

📄 Lisans
GPL v2 — Orijinal PHP-Nuke ile uyumlu. Detaylar için LICENSE dosyasına bakın (yakında eklenecek).

🙏 Teşekkürler

    PHP-Nuke topluluğuna — 20 yıllık miras için

    PHPMailer, Quill, Tailwind CSS, Swiper.js ekiplerine

FtoniNuke © 2026 — Modern PHP-Nuke
