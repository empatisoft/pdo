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
    ->find('parent_id')
    ->order('created_at')
    ->limit(15,0)
    ->params([
        ['parent_id', implode(',', [1,2,3,0])]
    ])->get('all');

echo "<pre>";
print_r($result);
echo "</pre>";
unset($db, $result);
