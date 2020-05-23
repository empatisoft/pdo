<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

require_once "config.php";

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
