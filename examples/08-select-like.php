<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

require_once "config.php";

$db = new \Empatisoft\EmpatiDB();

$result = $db
    ->select('title, url')
    ->from('contents_strings')
    ->like('url')
    ->params([
        ['url', '%icerik%']
    ])->get();

echo "<pre>";
print_r($result);
echo "</pre>";
unset($db, $result);
