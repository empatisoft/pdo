## EmpatiDB
PDO ile yazılmış olan bu ufak veritabanı sınıfı ile tüm ihtiyaçlarınızı kolaylıkla karşılayabilirsiniz.

## Composer ile kurulum (Terminal)
Proje ana dizininde aşağıdaki komutu çalıştırın.
```
$ composer require empatisoft/pdo:dev-master --prefer-source
```
## Composer ile kurulum (JSON)
composer.json dosyanızın require değerlerine ekleyip "composer update" komutunu çalıştırın.
```
"empatisoft/pdo": "dev-master"
```
## Projenize elle ekleme
Sınıfı indirip proje dizininize kopyalayıp kullanabilirsiniz.

## Örnek Kullanım
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', '_cms');
define('DB_PORT', 3306);
define('DB_CHARSET', "utf8");

define('DIR', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DIR);

require_once ROOT."vendor".DIR."autoload.php";

$db = new \Empatisoft\EmpatiDB();

$result = $db
    ->select('parent_id, content_id, published_at, created_at')
    ->from('contents')
    ->where('is_published')
    ->order('created_at')
    ->limit(15,0)
    ->params([
        ['is_published', '1', PDO::PARAM_INT]
    ])->get('all');

echo "<pre>";
print_r($result);
echo "</pre>";
unset($db, $result);
```

Diğer örnekler için "examples" klasörünü inceleyebilirsiniz.