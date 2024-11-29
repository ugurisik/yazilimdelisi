# Yazılım Delisi Framework

## İçindekiler
- [Kurulum](#kurulum)
- [Yapılandırma](#yapılandırma)
- [Özellikler](#özellikler)
- [Kullanım](#kullanım)

## Kurulum

1. Projeyi bilgisayarınıza klonlayın
2. XAMPP veya benzeri bir web sunucusu kullanıyorsanız, projeyi `htdocs` klasörüne yerleştirin
3. Veritabanını oluşturun ve `config.php` dosyasındaki veritabanı ayarlarını güncelleyin
4. Composer bağımlılıklarını yükleyin (eğer varsa)

## Yapılandırma

### config.php Ayarları
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'veritabani_adi');
define('DB_USER', 'kullanici_adi');
define('DB_PASS', 'sifre');
define('DB_CHARSET', 'utf8');
define('DB_PORT', 3306);
```

## Özellikler

### 1. Debug Bar
Debug Bar, geliştirme sürecinde hata ayıklama ve performans izleme için kullanılan güçlü bir araçtır.

Kullanım örneği:
```php
use App\helpers\utils\DebugBar;

$debugBar = DebugBar::getInstance();
$debugBar->addMessage("Test mesajı", "info");
$debugBar->startMeasure('islem', 'İşlem Süresi');
// ... kodunuz ...
$debugBar->stopMeasure('islem');
```

### 2. Cron Manager
Zamanlanmış görevleri yönetmek için kullanılan bir araçtır.

Kullanım örneği:
```php
use App\helpers\utils\CronManager;

$cronManager = CronManager::getInstance();

// Her 5 dakikada bir çalışacak görev
$cronManager->addJob('gorev_adi', '*/5 * * * *', function() {
    // Görev kodları
});
```

Cron ifadeleri:
- `* * * * *` = Her dakika
- `0 * * * *` = Her saatin başında
- `0 0 * * *` = Her gün gece yarısı
- `*/5 * * * *` = Her 5 dakikada bir

### 3. Rate Limiter
API isteklerini ve kullanıcı işlemlerini sınırlamak için kullanılır.

Kullanım örneği:
```php
use App\helpers\utils\RateLimiter;

$limiter = new RateLimiter(60, 1); // 1 dakikada maksimum 60 istek
if ($limiter->tooManyAttempts('kullanici_ip')) {
    // Limit aşıldı
}
```

### 4. Security Sınıfı
Güvenlik işlemleri için kullanılan yardımcı fonksiyonlar:

```php
use App\helpers\utils\Security;

$ip = Security::getIP();
$os = Security::getOS();
$browser = Security::getBrowser();
```

## Kullanım

### Controller Oluşturma
```php
namespace App\controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->view("tema_adi", "sayfa_adi", $parametreler);
    }
}
```

### View Kullanımı
Views klasöründe tema adı altında sayfalarınızı oluşturabilirsiniz:
```
views/
  ├── tema_adi/
  │   ├── inc/header.php
  │   ├── inc/footer.php
  │   └── sayfa_adi.php
```

### Model Kullanımı
```php
namespace App\models;

use Core\Mysql;

class UserModel extends Mysql
{
    public function getUsers()
    {
        return $this->db->query("SELECT * FROM users");
    }
}
```

## Güvenlik Önlemleri

1. XSS koruması için çıktıları her zaman escape edin
2. SQL Injection'a karşı prepared statements kullanın
3. CSRF tokenlarını formlarınızda kullanın
4. Rate limiting ile API'lerinizi koruyun
5. Hassas bilgileri her zaman şifreleyin

## Hata Ayıklama

Debug modunu açmak için `config.php` dosyasında:
```php
define('DEBUG', true);
```

Debug Bar ile hata ayıklama yapabilirsiniz:
1. Mesajları görüntüleme
2. SQL sorgularını izleme
3. Performans ölçümü
4. Hata ve istisnaları görüntüleme


## TODO List

### Kısa Vadeli Hedefler
- [X] Veritabanı migration sistemi ekleme
- [ ] API authentication sistemi geliştirme
- [ ] Cache sistemi entegrasyonu
- [ ] File upload helper sınıfı ekleme
- [ ] Email gönderme sistemi entegrasyonu
- [ ] Gelişmiş Kullanıcı İşlemi Log sistemi entegrasyonu

### Orta Vadeli Hedefler
- [ ] Admin panel şablonu oluşturma
- [ ] Modüler yapı oluşturma
- [ ] Kullanıcı yetkilendirme sistemi geliştirme
- [ ] Log sistemi geliştirme
- [ ] Queue (kuyruk) sistemi entegrasyonu
- [ ] Unit test altyapısı kurulumu

### Uzun Vadeli Hedefler
- [ ] CLI komut sistemi geliştirme
- [ ] Docker entegrasyonu
- [ ] WebSocket desteği ekleme
- [ ] GraphQL desteği ekleme
- [ ] Mikroservis mimarisi için altyapı hazırlama

### Sürekli İyileştirmeler
- [ ] Kod dokümantasyonunu geliştirme
- [ ] Performans optimizasyonları
- [ ] Güvenlik güncellemeleri
- [ ] Composer paketlerini güncelleme
- [ ] PHP 8.x uyumluluğunu sağlama

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.