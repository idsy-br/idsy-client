<?php
namespace Idsy\Client\Toth;

use Idsy\Client\Http\Request;

class ArtistaObraPublic
{
    public Request $request;
    private int $id_artista;
    private string $obra_nome;
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
        $this->id_artista = 0;
        $this->obra_nome = '';
        $this->orderBy   = '1';
        $this->lines     = '0';
    }

    public function setIdArtista(string $id_artista): void
    {
        $this->id_artista = (int)substr($id_artista, 0, 50);
    }

    public function getIdArtista(): int
    {
        return $this->id_artista;
    }

    public function setObraNome(string $obra_nome): void
    {
        $this->obra_nome = substr($obra_nome, 0, 50);
    }

    public function getObraNome(): string
    {
        return $this->obra_nome;
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
            'id_artista' => $this->id_artista,
            'obra_nome' => $this->obra_nome,
            'orderBy'   => $this->orderBy,
            'lines'     => $this->lines,
        ];

        $this->request->setController('TOTH_ARTISTA_OBRA_PUBLIC');
        $this->request->setPublicDataType('json');
        $this->request->setPublicData(json_encode($publicData));
        $this->request->setAuthenticationDataType('text');
        $this->request->setAuthenticationData($authenticationData);
        $this->request->setPrivateDataType('json');
        $this->request->get();
    }
}
