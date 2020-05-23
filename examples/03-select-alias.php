<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

require_once "config.php";

$db = new \Empatisoft\EmpatiDB();

$result = $db
    ->select('c.parent_id, c.content_id, c.published_at, c.created_at')
    ->from('contents', 'c')
    ->where('c.is_published', 'is_published')
    ->order('c.created_at', 'desc')
    ->limit(15,0)
    ->params([
        ['is_published', '1', PDO::PARAM_INT]
    ])->get('all');

echo "<pre>";
print_r($result);
echo "</pre>";
unset($db, $result);
