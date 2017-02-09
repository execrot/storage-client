<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('vendor/autoload.php');

\Storage\Storage::setConfig([
    'uri' => 'http://storage.loc'
]);

$account = new \Storage\Account();
$username = $password = sha1(microtime());
$account->register($username, $password);

$user = $account->auth($username, $password);

\Storage\Storage::setToken($user->getTokens()[0]);

$storage = new \Storage\Storage();

$res = $storage->upload('files', \Storage\Storage::FILES);
var_dump($res);

$info = $storage->getInfo($res[0]->getIdentity());
var_dump($info);

$list = $storage->getList(0, 10);
var_dump($list);

$searchResults = $storage->search('search string', 0, 10);
var_dump($searchResults);

$isDeleted = $storage->delete($searchResults[1]->getIdentity());
var_dump($isDeleted);




