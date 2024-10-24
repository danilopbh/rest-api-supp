<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Resource\SiatuResource;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContribuinteSupp;
use App\Entity\CertidaoDividaSupp;
use App\Rules\CertidaoDividaRules;
use Symfony\Component\Routing\Annotation\Route;
use App\Rules\ContribuinteRules;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class SiatuController extends AbstractController
{
    private SiatuResource $siatuResource;
    private EntityManagerInterface $entityManager;
    private $httpClient;  // Adiciona o HttpClient para enviar o POST ao SIATU
    private $logger;  // Logger para registrar os erros

    public function __construct(SiatuResource $siatuResource, EntityManagerInterface $entityManager, HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->siatuResource = $siatuResource;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;  // Injeta o HttpClient
        $this->logger = $logger;  // Injeção do Logger
    }

    #[Route("/api/importar", methods: ["POST"])]

    public function importarDados(): Response
    {
        // Importar contribuintes
        $contribuintes = $this->siatuResource->getContribuintes();
        $certidaoDivida = $this->siatuResource->getContribuintesCertidaoSupp();

        $allErrors = [];
        $status = 'sucesso';
        $mensagemErro = '';

        foreach ($contribuintes as $contribuinteDTO) {

            $contribuenteErrors = (new ContribuinteRules())->validate((array)$contribuinteDTO);
            if (!empty($contribuenteErrors)) {
                $allErrors[] = $contribuenteErrors;
                $status = 'sucesso';
                $mensagemErro = 'Erro ao validar contribuinte ' . $contribuinteDTO->nome;   // Variável pa
                continue;
            }
            $contribuinteSupp = $this->entityManager->getRepository(ContribuinteSupp::class)->findOneBy(['cpf' => $contribuinteDTO->cpf]);



            if (!$contribuinteSupp){
             
                $contribuinte = new ContribuinteSupp();
                $contribuinte->setNome($contribuinteDTO->nome);
                $contribuinte->setCpf($contribuinteDTO->cpf);
                $contribuinte->setEndereco($contribuinteDTO->endereco);
                $contribuinte->setIdContribuinteSiatu($contribuinteDTO->id_contribuinte_siatu);
                $this->entityManager->persist($contribuinte);
                $this->entityManager->flush();
            

                $contribuinteId = $contribuinte->getId();

            } else {

                $contribuinteId = $contribuinteSupp->getId();
            }

            
            foreach ($certidaoDivida as $certidaoDTO) {
                $validationErrors = CertidaoDividaRules::validate($certidaoDTO);

                // Validação de erros
                if (!empty($validationErrors)) {
                    $allErrors[] = [
                        'certidaoId' => $certidaoDTO->id,
                        'erros' => $validationErrors
                    ];

                    // Atualiza o status e a mensagem de erro corretamente
                    $status = 'erro';
                    $mensagemErro = 'Erro de validacao na certidao ' . $certidaoDTO->id;

                    // Notificar SIATU imediatamente em caso de erro de validação
                    $this->notificarSiatu($status, $mensagemErro, $allErrors);

                    // Retorna a resposta de erro
                    return new JsonResponse([
                        'status' => 'error',
                        'codigo' => '1',
                        'message' => 'Erro de validação na certidao',
                        'detalhes' => $validationErrors
                    ], Response::HTTP_BAD_REQUEST);
                }
             
                // Verifica se o contribuinte corresponde à certidão
                if ($certidaoDTO->id_contribuinte_siatu == $contribuinteDTO->id) {
                
                    $contribuinteSupp = $this->entityManager->getRepository(ContribuinteSupp::class)->find($contribuinteId);
                //    dd($contribuinteId);
                    // Verifica se a certidão de dívida já existe
                    $certidaoExistente = $this->entityManager->getRepository(CertidaoDividaSupp::class)
                        ->findOneBy(['id_certidao_divida_siatu' => $certidaoDTO->id]);

                    if ($certidaoExistente) {
                        // Atualiza o status e a mensagem de erro para duplicata de CDA
                        $status = 'erro';
                        $mensagemErro = 'A CDA com o numero ' . $certidaoDTO->id . ' ja existe no banco de dados.';

                        // Notificar SIATU imediatamente para duplicata
                        $this->notificarSiatu($status, $mensagemErro, $allErrors);

                        // Continua para a próxima certidão (se for o comportamento desejado)
                        continue;
                    }

                    // Processa e persiste a nova certidão se não houver erros
                    $certidao = new CertidaoDividaSupp();
                    $certidao->setContribuinteSupp($contribuinteSupp);
                    $certidao->setValor($certidaoDTO->valor);
                    $certidao->setDescricao($certidaoDTO->descricao);
                    $certidao->setPdfDivida($certidaoDTO->pdfDivida);
                    $certidao->setDataSituacao($certidaoDTO->dataSituacao);
                    $certidao->setIdContribuinteSiatu($certidaoDTO->id_contribuinte_siatu);
                    $certidao->setDataVencimento($certidaoDTO->dataVencimento);
                    $certidao->setIdCertidaoDividaSiatu($certidaoDTO->id);
                    $certidao->setSituacao($certidaoDTO->situacao);

                    $this->entityManager->persist($certidao);
                }
            }

            // Após a execução, faz o flush
            $this->entityManager->flush();
        }


        if (!empty($allErrors)) {
            $status = 'erro';
            $mensagemErro = 'Erros encontrados na importação';
        }


        $this->notificarSiatu($status, $mensagemErro, $allErrors);

        if ($status === 'erro') {
            return new JsonResponse(['errors' => $allErrors], Response::HTTP_BAD_REQUEST);
        } else {
            return new Response('Todos os dados importados com sucesso.');
        }
    }

    private function notificarSiatu(string $status, string $mensagemErro, $allErrors): void
    {
        try {
            // Monta os dados para o POST ao SIATU
            $data = [
                'status' => $status,
                'mensagem' => $status === 'sucesso' ? 'Importacao bem-sucedida' : 'Erro na importacao',
            ];

            // Apenas incluir os erros se o status for 'erro'
            if ($status === 'erro') {
                $data['Erro'] = $mensagemErro;
                $data['DetalhesErro'] = $allErrors;
            }

            // Envia o POST para o SIATU (coloque aqui a chamada real quando estiver pronta)
            /*$this->httpClient->request('POST', 'http://siatu/api/endpoint', [
                'json' => $data,
            ]);*/

            echo 'Siatu notificado';
            $json = json_encode($data);
            echo $json;
        } catch (\Exception $e) {
            $this->logger->error('Falha ao enviar notificacao ao SIATU: ' . $e->getMessage(), [
                'exception' => $e,
                'status' => $status,
                'mensagemErro' => $mensagemErro,
            ]);
        }
    }




    private function adicionarCDAAoContribuinte(ContribuinteSupp $contribuinte, $certidaoDTO)
    {
        $certidao = new CertidaoDividaSupp();
        $certidao->setContribuinteSupp($contribuinte);
        $certidao->setValor($certidaoDTO->valor);
        $certidao->setDescricao($certidaoDTO->descricao);
        $certidao->setPdfDivida($certidaoDTO->pdfDivida);
        $certidao->setDataSituacao($certidaoDTO->dataSituacao);
        $certidao->setIdContribuinteSiatu($certidaoDTO->id_contribuinte_siatu);
        $certidao->setDataVencimento($certidaoDTO->dataVencimento);
        $certidao->setIdCertidaoDividaSiatu($certidaoDTO->id);
        $certidao->setSituacao($certidaoDTO->situacao);

        $this->entityManager->persist($certidao);
    }
}
