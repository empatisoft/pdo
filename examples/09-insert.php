<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

require_once "config.php";

$db = new \Empatisoft\EmpatiDB();

/**
 * insert metodunda 3. parametre olarak true gönderilirse, son eklenen verinin id numarası döner.
 * Verilmezse true/false değeri döner.
 */

$string = 'İçerik '.uniqid();

$insert = $db->insert('contents', [
    'parent_id' => 0,
    'created_at' => date('Y-m-d H:s:i'),
    'created_by' => 1,
    'published_at' => date('Y-m-d H:s:i'),
    'published_by' => 1,
    'is_published' => 1
], true);

if($insert != false) {
    $insert_strings = $db->insert('contents_strings', [
        'content_id' => $insert,
        'language_id' => 1,
        'title' => $string,
        'page_title' => $string,
        'url' => $string,
        'meta_title' => $string,
        'meta_description' => $string
    ]);
}

echo "<pre>";
var_dump($insert);
echo "</pre>";
echo "<pre>";
var_dump($insert_strings);
echo "</pre>";
unset($db);
