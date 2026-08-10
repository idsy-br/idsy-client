<?php

include_once "../vendor/autoload.php";

use Idsy\Client\Toth\ArtistaPublic;
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

$call = new ArtistaPublic();
$call->request->setURL('http://localhost:8080/idsy-api/public_html/index.php');
$call->setNome('eric');
$call->setOrderBy('1');
$call->setLines('30');
$call->get($token);

echo nl2br('');
echo nl2br($call->request->getResultCode());
echo nl2br($call->request->getResult());
echo nl2br('');
