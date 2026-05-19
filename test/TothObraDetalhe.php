<?php

include_once "../vendor/autoload.php";

use Idsy\Client\Toth\ObraDetalhe;
use Idsy\Client\Control\Login;

// login
$login = new Login();
$login->request->setURL('http://localhost:8080/idsy-api/public_html/index.php');
$login->setLogin('toth-site');
$login->setPassword('1');
$login->setTeam('toth');
$login->setKey('');
$login->get();

$data = json_decode($login->request->getResult(), true);
$token = $data['result'];

$call = new ObraDetalhe();
$call->setChave('e381bd058baff75eb34cd4c0ed2d6e2f399ebe238effee409c625e1a2d8f5033631840e6ce1dcb64');
$call->request->setURL('http://localhost:8080/idsy-api/public_html/index.php');
$call->get($token);

echo nl2br('');
echo nl2br($call->request->getResultCode());
echo nl2br($call->request->getResult());
echo nl2br('');
