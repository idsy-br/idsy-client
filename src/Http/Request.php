<?php
namespace Idsy\Client\Http;

class Request
{
    private string $url;
    private string $controller;
    private string $device;
    private string $publicData;
    private string $publicDataType;
    private string $privateData;
    private string $privateDataType;
    private string $authenticationData;
    private string $authenticationDataType;
    private string $result;
    private int $resultCode;
    private int $timeout;

    public function __construct()
    {
        $this->toClear();
    }

    public function toClear(): void
    {
        $this->url                    = '';
        $this->controller             = '';
        $this->device                 = '';
        $this->publicData             = '';
        $this->publicDataType         = '';
        $this->privateData            = '';
        $this->privateDataType        = '';
        $this->authenticationData     = '';
        $this->authenticationDataType = '';
        $this->result                 = '';
        $this->resultCode             = 0;
        $this->timeout                = 30;
    }

    public function getURL(): string                        { return $this->url; }
    public function setURL(string $value): void             { $this->url = $value; }

    public function getController(): string                 { return $this->controller; }
    public function setController(string $value): void      { $this->controller = $value; }

    public function getDevice(): string                     { return $this->device; }
    public function setDevice(string $value): void          { $this->device = $value; }

    public function getPublicData(): string                 { return $this->publicData; }
    public function setPublicData(string $value): void      { $this->publicData = $value; }

    public function getPublicDataType(): string             { return $this->publicDataType; }
    public function setPublicDataType(string $value): void  { $this->publicDataType = $value; }

    public function getPrivateData(): string                { return $this->privateData; }
    public function setPrivateData(string $value): void     { $this->privateData = $value; }

    public function getPrivateDataType(): string            { return $this->privateDataType; }
    public function setPrivateDataType(string $value): void { $this->privateDataType = substr($value, 0, 10); }

    public function getAuthenticationData(): string                        { return $this->authenticationData; }
    public function setAuthenticationData(string $value): void             { $this->authenticationData = $value; }

    public function getAuthenticationDataType(): string                    { return $this->authenticationDataType; }
    public function setAuthenticationDataType(string $value): void         { $this->authenticationDataType = $value; }

    public function getResult(): string                     { return $this->result; }
    public function setResult(string $value): void          { $this->result = $value; }

    public function getResultCode(): int                    { return $this->resultCode; }
    public function setResultCode(int $value): void         { $this->resultCode = $value; }

    public function getTimeout(): int                       { return $this->timeout; }
    public function setTimeout(int $value): void            { $this->timeout = $value; }

    private function getHeaders(): array
    {
        return [
            'Accept: application/json',
            'controller: ' . $this->controller,
            'public-data-type: ' . $this->publicDataType,
            'private-data-type: ' . $this->privateDataType,
            'authentication-data-type: ' . $this->authenticationDataType,
            'authentication-data: ' . $this->authenticationData,
            'device: ' . $this->device,
        ];
    }

    public function post(): void
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => array_merge($this->getHeaders(), ['Connection: close']),
            CURLOPT_POSTFIELDS     => $this->privateData,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_FRESH_CONNECT  => true,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }

        $this->result     = $response;
        $this->resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

    public function get(): void
    {
        $headers = $this->getHeaders();

        // foreach ($headers as $h) {
        //     if (preg_match("/:\s*$/", $h)) {
        //         throw new \Exception("Header inválido: [$h]");
        //     }
        // }

        $query = $this->publicData !== '' ? '?publicdata=' . urlencode($this->publicData) : '';

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->url . $query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array_merge($headers, ['Connection: close']),
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_FRESH_CONNECT  => true,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }

        $this->result     = $response;
        $this->resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
}
