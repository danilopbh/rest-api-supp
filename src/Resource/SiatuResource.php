<?php
namespace App\Resource;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Mapper\ContribuinteSuppMapper;
use App\Mapper\CertidaoDividaSuppMapper;

class SiatuResource
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getContribuintes(): array
    {
        $response = $this->httpClient->request('GET', 'http://localhost:8000/api/contribuinte/siatu');
        $data = $response->toArray();

        return array_map([ContribuinteSuppMapper::class, 'map'], $data);
    }

    public function getContribuintesCertidaoSupp(): array
    {
        $response = $this->httpClient->request('GET', 'http://localhost:8000/api/contribuinte/siatu');
        $data = $response->toArray();


        $certidoesDivida = array();

// Percorre o array original para extrair as certidões de dívida
        foreach ($data as $contribuinte) {
            if (isset($contribuinte['certidoesDivida'])) {
                foreach ($contribuinte['certidoesDivida'] as $certidao) {
                    $certidoesDivida[] = $certidao;
                }
            }
        }



        return array_map([CertidaoDividaSuppMapper::class, 'map'], $certidoesDivida);
    }

/*    public function getCertidoes(): array
    {
        $response = $this->httpClient->request('GET', 'http://localhost:8000/api/contribuinte/siatu/certidoes');
        $data = $response->toArray();

        return array_map([CertidaoDividaSuppMapper::class, 'map'], $data);
    }
        */
}
