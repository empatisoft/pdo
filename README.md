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

## ->get() Metodu
Veritabanından veri çekme işleminde kullanılır, son parametre olarak kullanılmalıdır. 2 adet parametre alabilir.
1. Tek satır mı, birden fazla satır mı çekileceğini belirtir. Varsayılan olarak "all" işaretlidir. Birden fazla kayıt çeker. Bu değer dışında kullanılanlar tek satır çektirir.
2. PDO FETCH türünü belirtir. Varsayılan olarak "PDO::FETCH_OBJ" olarak işaretlidir.

Her iki parametre de zorunlu değildir.

## ->lastId() Metodu
Son eklenen verinin birincil anahtar değerini döndürür.

## ->nextId() Metodu
Belirtilen kriterlere göre sonraki verinin birincil anahtar değerini döndürür.
"from, join, where, group, order, params" metodları ile birlikte kullanılabilir.
İki adet parametre alabilir.
1. Birincil anahtar sütun adı
2. params metoduna gönderilecek olan anahtar kelime. Varsayılan olarak "current" işaretli

```php
$result = $db
    ->from('contents', 'c')
    ->join('contents_strings', 's', ['c.content_id', 's.content_id'])
    ->where('c.is_published', 'is_published')
    ->params([
        ['is_published', '1', PDO::PARAM_INT],
        ['current', '5', PDO::PARAM_INT]
    ])->nextId('c.content_id');

echo "<pre>";
var_dump($result);
echo "</pre>";
unset($db, $result);
```

## ->prevId() Metodu
Belirtilen kriterlere göre önceki verinin birincil anahtar değerini döndürür.
"from, join, where, group, order, params" metodları ile birlikte kullanılabilir.
İki adet parametre alabilir.
1. Birincil anahtar sütun adı
2. params metoduna gönderilecek olan anahtar kelime. Varsayılan olarak "current" işaretli

```php
$result = $db
    ->from('contents', 'c')
    ->join('contents_strings', 's', ['c.content_id', 's.content_id'])
    ->where('c.is_published', 'is_published')
    ->params([
        ['is_published', '1', PDO::PARAM_INT],
        ['current', '5', PDO::PARAM_INT]
    ])->prevId('c.content_id');

echo "<pre>";
var_dump($result);
echo "</pre>";
unset($db, $result);
```

## ->total() Metodu
Belirtilen kriterlere göre toplam veri sayısını döndürür.
"from, join, where, group, params" metodları ile birlikte kullanılabilir.
1 adet parametre alabilir. Sayılacak olan sütun adı 

```php
$result = $db
    ->from('contents', 'c')
    ->join('contents_strings', 's', ['c.content_id', 's.content_id'])
    ->where('c.is_published', 'is_published')
    ->where('c.parent_id', 'parent_id')
    ->params([
        ['is_published', '1', PDO::PARAM_INT],
        ['parent_id', '0', PDO::PARAM_INT]
    ])->total('c.content_id');

echo "<pre>";
var_dump($result);
echo "</pre>";
unset($db, $result);
```

## ->insert() Metodu
Veri eklemek için kullanılabilir. 3 adet parametre alabilir.
1. Tablo adı
2. Eklenecek olan veriler dizi olarak tanımlanır.
3. Eklenen verinin birincil anahtar değerinin döndürülüp döndürülmeyeceği. Varsayılan olarak "false" işaretli. Verilmezse true/false değeri döner.

```php
$result = $db->insert('contents', [
            'is_published' => '1',
            'created_by' => '1111',
            'published_at' => date('Y-m-d H:s:i')
        ], true);

echo "<pre>";
var_dump($result);
echo "</pre>";
unset($db, $result);
```

## ->update() Metodu
Veri düzenlemek için kullanılabilir. "where, params" metotları ile birlikte kullanılabilir.

2 adet parametre alabilir.
1. Tablo adı
2. Düzenlenecek olan veriler dizi olarak tanımlanır.

```php
$result = $db
              ->where('content_id')
              ->params([
                  ['content_id', '13', PDO::PARAM_INT]
              ])
              ->update('contents', [
                  'updated_at' => date('Y-m-d H:s:i'),
                  'updated_by' => 1
              ]);

echo "<pre>";
var_dump($result);
echo "</pre>";
unset($db, $result);
```

## ->delete() Metodu
Veri silmek için kullanılabilir. "where, params" metotları ile birlikte kullanılabilir.
Tablo adı parametre olarak verilmelidir.

```php
$result = $db
              ->where('content_id')
              ->params([
                  ['content_id', '14', PDO::PARAM_INT]
              ])
              ->delete('contents');

echo "<pre>";
var_dump($result);
echo "</pre>";
unset($db, $result);
```