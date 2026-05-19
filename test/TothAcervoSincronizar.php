<?php

include_once "../vendor/autoload.php";

use Idsy\Client\Toth\AcervoSincronizar;
use Idsy\Client\Control\Login;

// login
$login = new Login();
$login->request->setURL('http://localhost:8080/idsy-api/public_html/index.php');
$login->setLogin('worker');
$login->setPassword('123456');
$login->setTeam('control');
$login->setKey('');
$login->get();

$data = json_decode($login->request->getResult(), true);
$token = $data['result'];

$call = new AcervoSincronizar();
$call->request->setURL('http://localhost:8080/idsy-api/public_html/index.php');
$call->post($token);

echo $token;
echo nl2br('');
echo nl2br($call->request->getResultCode());
echo nl2br($call->request->getResult());
echo nl2br('');
