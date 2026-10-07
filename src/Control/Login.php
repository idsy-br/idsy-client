<?php

namespace Idsy\Client\Control;

use Idsy\Client\Http\Request;

class Login
{
    public Request $request;

    private string $login;
    private string $password;
    private string $team;
    private string $key;

    public function __construct()
    {
        $this->request = new Request();
        $this->toClear();
    }

    public function toClear(): void
    {
        $this->login    = '';
        $this->password = '';
        $this->team     = '';
        $this->key      = '';
        $this->request->toClear();
    }

    public function getLogin(): string
    {
        return $this->login;
    }
    public function setLogin(string $value): void
    {
        $this->login = substr($value, 0, 100);
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $value): void
    {
        $this->password = substr($value, 0, 100);
    }

    public function getTeam(): string
    {
        return $this->team;
    }
    public function setTeam(string $value): void
    {
        $this->team = substr($value, 0, 100);
    }

    public function getKey(): string
    {
        return $this->key;
    }
    public function setKey(string $value): void
    {
        $this->key = substr($value, 0, 100);
    }

    public function get(): void
    {
        /* A credencial vai no CORPO, nao no header.

           Header e a parte da requisicao que a infraestrutura registra sem pedir licenca:
           LogFormat do Apache, proxy, CDN, dump de erro que despeja os headers. A senha
           ficava ali. Corpo de requisicao nenhum desses guarda por padrao.

           O header vai vazio de proposito. A API le o corpo primeiro e so cai no header
           para atender cliente antigo, entao os dois formatos funcionam durante a troca.

           Junto vai a chave PUBLICA, e e ela que amarra o token a esta instalacao: das
           proximas chamadas em diante a API so aceita o token se a chamada vier assinada
           pela privada do mesmo par. Sem chave configurada ela vai vazia e o token nasce
           sem assinatura, como antes. */
        $privateData = [
            'login'         => $this->login,
            'password'      => $this->password,
            'team'          => $this->team,
            'key'           => $this->key,
            'chave_publica' => \Idsy\Client\Http\Assinatura::chavePublicaB64(),
        ];

        $this->request->setController('CONTROL_LOGIN');
        $this->request->setPublicDataType('json');
        $this->request->setAuthenticationDataType('text');
        $this->request->setAuthenticationData('');
        $this->request->setPrivateDataType('json');
        $this->request->setPrivateData(json_encode($privateData));
        $this->request->post();
    }
}
