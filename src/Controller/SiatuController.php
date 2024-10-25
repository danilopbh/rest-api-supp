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
use TCPDF;

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

            if (!$contribuinteSupp) {

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

            // Para garantir que geramos apenas 3 arquivos por certidão
            $certidaoCount = 0;

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
            // Gera o PDF 3 vezes para cada certidão
            for ($i = 0; $i < 3; $i++) {
                $this->generateAndExportPdf($certidao, $i);
            }

            $certidaoCount += 3; // Contabiliza 3 certidões geradas
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

    private function generateAndExportPdf(CertidaoDividaSupp $certidao, int $index): void
    {
        // Criar diretório para armazenar os PDFs, se não existir
        $userName = getenv('USERNAME') ?: getenv('USER');
        $directory = '/home/' . $userName . '/certidoes_geradas/';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Permissões para criar a pasta
        }

        // Gerar PDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema Automático');
        $pdf->SetTitle('Certidão de Dívida - ' . $index);
        $pdf->SetSubject('Certidão de Dívida Ativa');
        $pdf->SetKeywords('Certidão, Dívida, PDF, Sistema');

        $pdf->AddPage();

        // Definir o conteúdo dinâmico do PDF com os dados do banco
        $html = "
        <h1>Certidão de Dívida Ativa</h1>
        <p><strong>Contribuinte:</strong> " . $certidao->getContribuinteSupp()->getNome() . "</p>
        <p><strong>CPF:</strong> " . $certidao->getContribuinteSupp()->getCpf() . "</p>
        <p><strong>Descrição:</strong> " . $certidao->getDescricao() . "</p>
        <p><strong>Data de Vencimento:</strong> " . $certidao->getDataVencimento()->format('d/m/Y') . "</p>
        <p><strong>Valor:</strong> R$ " . number_format($certidao->getValor(), 2, ',', '.') . "</p>
        <p><strong>Situação:</strong> " . $certidao->getSituacao() . "</p>
        <p><strong>Atualização da Situação:</strong> " . $certidao->getDataSituacao()->format('d/m/Y') . "</p>";

        $pdf->writeHTML($html, true, false, true, false, '');

        // Salvar o PDF no diretório especificado
        $pdfFilePath = $directory . 'certidao_' . $certidao->getId() . '_' . $index . '.pdf';
        $pdf->Output($pdfFilePath, 'F'); // 'F' indica que o PDF deve ser salvo em um arquivo
    }

    public function exportPdfToFile(string $pdfContent, $certidaoId): void
    {
        // Obter o nome do usuário do sistema operacional
        $userName = getenv('USERNAME') ?: getenv('USER');

        // Verificar se o diretório existe, se não, criar
        $directory = '/home/' . $userName . '/certidoes_geradas';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Permissões para criar a pasta
        }

        // Definir o nome do arquivo com base no ID ou outro atributo
        $filePath = $directory . 'certidao_' . $certidaoId . '.pdf';

        // Salvar o conteúdo binário no arquivo
        file_put_contents($filePath, $pdfContent);
    }
}