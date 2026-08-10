<?php
namespace Idsy\Client\Toth;

use Idsy\Client\Http\Request;

class ArtistaPublic
{
    public Request $request;
    private int $id;
    private string $nome;
    private string $orderBy;
    private string $lines;

    public function __construct()
    {
        $this->request = new Request();
        $this->toClear();
    }

    public function toClear(): void
    {
        $this->request->toClear();
        $this->id      = 0;
        $this->nome    = '';
        $this->orderBy = '1';
        $this->lines   = '0';
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setNome(string $nome): void
    {
        $this->nome = substr($nome, 0, 50);
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function setLines(string $lines): void
    {
        $this->lines = $lines;
    }

    public function getLines(): string
    {
        return $this->lines;
    }

    public function get(string $authenticationData): void
    {
        $publicData = [
            'nome'    => $this->nome,
            'orderBy' => $this->orderBy,
            'lines'   => $this->lines,
        ];

        $this->request->setController('TOTH_ARTISTA_PUBLIC');
        $this->request->setPublicDataType('json');
        $this->request->setPublicData(json_encode($publicData));
        $this->request->setAuthenticationDataType('text');
        $this->request->setAuthenticationData($authenticationData);
        $this->request->setPrivateDataType('json');
        $this->request->get();
    }
}
