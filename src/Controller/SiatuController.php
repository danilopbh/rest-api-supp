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
        $status = 'sucesso';  // Supondo que o status inicial seja de sucesso
        $mensagemErro = '';   // Variável para armazenar possíveis mensagens de erro

        foreach ($contribuintes as $contribuinteDTO) {
            $contribuenteErrors = (new ContribuinteRules())->validate((array)$contribuinteDTO);
            if (!empty($contribuenteErrors)) {
                $allErrors[] = $contribuenteErrors; 
                $status = 'sucesso';  // Supondo que o status inicial seja de sucesso
                $mensagemErro = 'Erro ao validar contribuinte ' . $contribuinteDTO->nome;   // Variável pa
               // return new JsonResponse(['errors' => $contribuenteErrors], Response::HTTP_BAD_REQUEST);
                continue;
            }
           
            $contribuinte = new ContribuinteSupp();
            //$contribuinte->setId($contribuinteDTO->id);
            $contribuinte->setNome($contribuinteDTO->nome);
            $contribuinte->setCpf($contribuinteDTO->cpf);
            $contribuinte->setEndereco($contribuinteDTO->endereco);
            $contribuinte->setIdContribuinteSiatu($contribuinteDTO->id_contribuinte_siatu);
            //$contribuinte->addCertidaoDividaSupp($contribuinteDTO->);
            $this->entityManager->persist($contribuinte);
            $this->entityManager->flush(); // Gerar o ID do contribuinte


            $contribuinteId = $contribuinte->getId();


            foreach ($certidaoDivida as $certidaoDTO) {
            
                if (!CertidaoDividaRules::validate($certidaoDTO)) {
                    continue;
                }

                $validationErrors = CertidaoDividaRules::validate($certidaoDTO);

                if (!empty($validationErrors)) {
                    // Exemplo: Adiciona os erros ao array de erros
                    $allErrors[] = [
                        'certidaoId' => $certidaoDTO->id,
                        'erros' => $validationErrors
                    ];

                    $status = 'Error';

                    $this->notificarSiatu($status, $mensagemErro,$allErrors);
                    // Ou, se quiser parar o processo imediatamente e retornar o erro:
                    return new JsonResponse([
                        'status' => 'error',
                        'codigo' => '1',
                        'message' => 'Erro de validação na certidão',
                        'detalhes' => $validationErrors
                    ], Response::HTTP_BAD_REQUEST);
                    
                    continue; // Pula para a próxima certidão
                }

                if ($certidaoDTO->id_contribuinte_siatu == $contribuinteDTO->id) {
                    $contribuinteSupp = $this->entityManager->getRepository(ContribuinteSupp::class)->find($contribuinteId);

                    
                    $certidaoExistente = $this->entityManager->getRepository(CertidaoDividaSupp::class)
                    ->findOneBy(['id_certidao_divida_siatu' => $certidaoDTO->id]);

                    if ($certidaoExistente) {
                        // Retornar mensagem de erro informando que a CDA já existe
                        
                        $status = 'erro';
                        $mensagemErro = 'A CDA com o número ' . $certidaoDTO->id . ' já existe no banco de dados.';
            
                        $this->notificarSiatu($status, $mensagemErro,  $allErrors);
                        continue;
                        /*return new JsonResponse([
                            'status' => 'error',
                            'codigo' => '2',
                            'message' => 'A CDA com o numero ' . $certidaoDTO->id . ' ja existe no banco de dados.'
                        ], Response::HTTP_CONFLICT);  // HTTP 409: Conflict*/
                    }


                    $certidao = new CertidaoDividaSupp();
                    //$certidao->setId($certidaoDTO->id);
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


            $this->entityManager->flush();
        }


        if (!empty($allErrors)) {
            $status = 'erro';
            $mensagemErro = 'Erros encontrados na importação';
        }

        /*if (!empty($allErrors)) {
            return new JsonResponse(['errors' => $allErrors], Response::HTTP_BAD_REQUEST);

        } else {

        return new Response('Todos os dados importados com sucesso.');
        }*/

        
        
        
        
        
        
        $this->notificarSiatu($status, $mensagemErro,$allErrors);

        if ($status === 'erro') {
            return new JsonResponse(['errors' => $allErrors], Response::HTTP_BAD_REQUEST);
        } else {
            return new Response('Todos os dados importados com sucesso.');
        }
    
    
    
    
    }

    // Função para enviar POST de notificação ao SIATU
    private function notificarSiatu(string $status, string $mensagemErro, $allErrors): void
    {
        try {
            // Monta os dados para o POST ao SIATU
            $data = [
                'status' => $status,
                'mensagem' => $status === 'sucesso' ? 'Importacao bem-sucedida' : 'Erro na importacao',
                'detalhesErro' => $mensagemErro,
                'DetalheErro' => $allErrors
            ];

            // Envia o POST para o SIATU
           /* $this->httpClient->request('POST', 'http://siatu/api/endpoint', [
                'json' => $data,
            ]);*/


            echo 'Siatu notificado';

            $json = json_encode($data);
            
            echo $json;
            


        } catch (\Exception $e) {
            $this->logger->error('Falha ao enviar notificação ao SIATU: ' . $e->getMessage(), [
                'exception' => $e,
                'status' => $status,
                'mensagemErro' => $mensagemErro,
            ]);
        }
    }
}
