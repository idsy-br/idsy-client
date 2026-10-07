<?php

namespace Idsy\Client\Http;

/**
 * Assinatura das chamadas, do lado de quem consome a API em PHP.
 *
 * O token sozinho nao basta mais: cada chamada vai assinada com a chave privada do par que
 * pediu aquele token. Token que vaze em log, em dump ou num backup nao serve para ninguem
 * sem a privada.
 *
 * No navegador a chave mora no IndexedDB e e nao-extraivel. Aqui nao ha equivalente: a
 * privada e um arquivo, e a protecao dela e a permissao do sistema de arquivos. Por isso
 * o arquivo nasce 0600 e deve ficar FORA da pasta publica do site.
 *
 * Configuracao, uma vez, por quem usa o pacote:
 *
 *     Assinatura::usarArquivo('/caminho/fora/do/public/idsy-worker.key');
 *
 * Sem isso nada e assinado e o login nao manda chave publica -- a API entao trata o token
 * como token antigo e o aceita sem assinatura. E o que mantem o pacote funcionando sem
 * mudanca em quem ja o usa.
 *
 * O estado e estatico de proposito: cada classe de operacao do pacote tem o seu proprio
 * Request, e quem consome configuraria a chave em cada uma. Como e configuracao -- igual a
 * uma string de conexao --, uma chamada so no comeco do processo vale mais que repeticao.
 */
class Assinatura
{
    private static string $arquivo = '';
    private static mixed $privada = null;
    private static string $publicaB64 = '';

    /**
     * Diz onde mora a chave privada. O par e criado na primeira vez.
     *
     * Chamar de novo com o MESMO caminho nao faz nada: quem consome o pacote costuma
     * configurar no comeco de cada requisicao, e jogar a chave fora ali custaria leitura e
     * parse do arquivo a cada chamada. Caminho diferente, sim, troca a chave.
     */
    public static function usarArquivo(string $e_caminho): void
    {
        if ($e_caminho === self::$arquivo) {
            return;
        }

        self::$arquivo    = $e_caminho;
        self::$privada    = null;
        self::$publicaB64 = '';
    }

    public static function configurada(): bool
    {
        return self::$arquivo !== '';
    }

    /** A chave publica em base64 do SPKI, que e o que a API guarda junto do token. */
    public static function chavePublicaB64(): string
    {
        if (self::configurada() === false) {
            return '';
        }

        self::carregar();

        return self::$publicaB64;
    }

    /**
     * O texto assinado e o MESMO que o Security::assinaturaTexto monta no servidor:
     *
     *     controller|metodo|hora|sha256(publicdata)|sha256(privatedata)
     *
     * Mudar aqui exige mudar la. O metodo vai em minusculas, e os hashes sao do conteudo
     * sem codificacao de URL -- e o que o PHP da API tem na mao depois de decodificar.
     */
    public static function texto(string $e_controller, string $e_metodo, int $e_hora,
                                 string $e_publicData, string $e_privateData): string
    {
        return $e_controller . '|' . strtolower($e_metodo) . '|' . $e_hora . '|' .
               hash('sha256', $e_publicData) . '|' .
               hash('sha256', $e_privateData);
    }

    /**
     * Assina e devolve base64.
     *
     * O openssl_sign produz DER, que e outro formato do que o Web Crypto do navegador
     * manda (r||s cru). A API aceita os dois -- nao ha conversao a fazer aqui.
     */
    public static function assinar(string $e_texto): string
    {
        if (self::configurada() === false) {
            return '';
        }

        self::carregar();

        if (openssl_sign($e_texto, $v_der, self::$privada, OPENSSL_ALGO_SHA256) === false) {
            return '';
        }

        return base64_encode($v_der);
    }

    /** Le a chave do arquivo; na primeira vez, cria o par e grava. */
    private static function carregar(): void
    {
        if (self::$privada !== null) {
            return;
        }

        if (is_file(self::$arquivo) === true) {
            $v_pem = (string)file_get_contents(self::$arquivo);
            $v_chave = openssl_pkey_get_private($v_pem);

            if ($v_chave === false) {
                throw new \Exception('Chave de assinatura ilegível: ' . self::$arquivo);
            }
        } else {
            $v_chave = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC,
                                         'curve_name'       => 'prime256v1']);

            if ($v_chave === false) {
                throw new \Exception('Não foi possível gerar a chave de assinatura.');
            }

            openssl_pkey_export($v_chave, $v_pem);

            $v_pasta = dirname(self::$arquivo);

            if (is_dir($v_pasta) === false) {
                mkdir($v_pasta, 0700, true);
            }

            if (file_put_contents(self::$arquivo, $v_pem) === false) {
                throw new \Exception('Não foi possível gravar a chave em ' . self::$arquivo);
            }

            // no Windows isto nao faz nada; em Linux e o que impede outro usuario de ler
            @chmod(self::$arquivo, 0600);
        }

        self::$privada = $v_chave;

        $v_detalhe = openssl_pkey_get_details($v_chave);
        $v_publica = (string)($v_detalhe['key'] ?? '');

        // do PEM da publica sobra o base64 do SPKI, que e o formato que a API guarda
        self::$publicaB64 = str_replace(["-----BEGIN PUBLIC KEY-----",
                                         "-----END PUBLIC KEY-----", "\r", "\n"],
                                        '', $v_publica);
    }
}
