<?php
namespace Idsy\Client\Toth;

use Idsy\Client\Http\Request;

class AcervoPostInstagram
{
    public Request $request;

    public function __construct()
    {
        $this->request = new Request();
        $this->toClear();
    }

    public function toClear(): void
    {
        $this->request->toClear();
    }

    public function post(string $authenticationData): void
    {
        $this->request->setController('TOTH_ACERVO_POST_INSTAGRAM');
        $this->request->setPublicDataType('json');
        $this->request->setAuthenticationDataType('text');
        $this->request->setAuthenticationData($authenticationData);
        $this->request->setPrivateDataType('json');
        $this->request->post();
    }
}
