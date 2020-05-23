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
    ->between('published_at')
    ->order('created_at')
    ->limit(15,0)
    ->params([
        ['start', '2020-05-10'],
        ['end', '2020-05-31']
    ])->get('all');

echo "<pre>";
print_r($result);
echo "</pre>";
unset($db, $result);
