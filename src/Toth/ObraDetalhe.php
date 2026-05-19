<?php
namespace Idsy\Client\Toth;

use Idsy\Client\Http\Request;

class ObraDetalhe
{
    public Request $request;
    private string $chave;

    public function __construct()
    {
        $this->request = new Request();
        $this->toClear();
    }

    public function toClear(): void
    {
        $this->request->toClear();
        $this->chave = '';
    }

    public function setChave(string $chave): void
    {
        $this->chave = $chave;
    }

    public function getChave(): string
    {
        return $this->chave;
    }    

    public function get(string $authenticationData): void
    {
        $this->request->setController('TOTH_OBRA_DETALHE');
        $this->request->setPublicDataType('text');
        $this->request->setPublicData($this->chave);
        $this->request->setAuthenticationDataType('text');
        $this->request->setAuthenticationData($authenticationData);
        $this->request->setPrivateDataType('json');
        $this->request->get();
    }
}
