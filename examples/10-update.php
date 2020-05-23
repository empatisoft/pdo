<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

require_once "config.php";

$db = new \Empatisoft\EmpatiDB();

/**
 * Birden fazla where parametresi gönderilebilir. Değerleri params içinde verilmelidir.
 */

$update = $db
    ->where('content_id')
    ->params([
        ['content_id', 15, PDO::PARAM_INT]
    ])
    ->update('contents', [
    'parent_id' => 14,
    'updated_at' => date('Y-m-d H:s:i'),
    'updated_by' => 15,
    'is_published' => 0
]);


echo "<pre>";
var_dump($update);
echo "</pre>";
unset($db);
