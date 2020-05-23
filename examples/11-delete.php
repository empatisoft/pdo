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

$delete = $db
    ->where('content_id')
    ->params([
        ['content_id', 15, PDO::PARAM_INT]
    ])
    ->delete('contents');

echo "<pre>";
var_dump($delete);
echo "</pre>";
unset($db);
