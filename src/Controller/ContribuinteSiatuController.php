<?php

namespace App\Controller;

use App\Repository\ContribuinteSiatuRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContribuinteSiatuController extends AbstractController
{
    #[Route('/contribuinte/siatu', name: 'app_contribuinte_siatu')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ContribuinteSiatuController.php',
        ]);
    }

    #[Route('/api/contribuinte/siatu', name: 'app_contribuintes_siatu', methods: ['GET'])]
    public function getContribuintesSiatu(ContribuinteSiatuRepository $contribuinteSiatuRepository): JsonResponse
    {
        // Fetch all Contribuintes with their CertidaoDivida relations
        $contribuintes = $contribuinteSiatuRepository->findAll();



        $data = [];

        foreach ($contribuintes as $contribuinte) {

            $certidoes = [];

            foreach ($contribuinte->getCertidaoDividaSiatu() as $certidaoDivida) {
            
               
                // Obter o PDF como string
                $pdfDivida = $certidaoDivida->getPdfDivida();
                $pdfContent = '';

                // Verificar se o PDF é um recurso
                if (is_resource($pdfDivida)) {
                    $pdfContent = stream_get_contents($pdfDivida); // Lê o conteúdo do recurso
                    fclose($pdfDivida); // Fecha o recurso após a leitura
                } elseif (is_string($pdfDivida)) {
                    $pdfContent = $pdfDivida; // Já é uma string
                }

                $certidoes[] = [
                    'id' => $certidaoDivida->getId(),
                    'contribuinte_supp_id' => $certidaoDivida->getContribuinte()->getId(), 
                    'descricao' => $certidaoDivida->getDescricao(),
                    'dataVencimento' => $certidaoDivida->getDataVencimento()->format('Y-m-d'),
                    'pdfDivida' => base64_encode($pdfContent),
                    'valor' => $certidaoDivida->getValor(),
                    
                    
                ];
            }
            $data[] = [
                'id' => $contribuinte->getId(),
                'nome' => $contribuinte->getNome(),
                'cpf' => $contribuinte->getCpf(),
                'endereco' => $contribuinte->getEndereco(),
                'certidoesDivida' => $certidoes,
            ];
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }
}
