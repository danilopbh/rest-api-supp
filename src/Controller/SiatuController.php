<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Resource\SiatuResource;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContribuinteSupp;
use App\Entity\CertidaoDividaSupp;
use App\Rules\CertidaoDividaRules;
use Symfony\Component\Routing\Annotation\Route;

class SiatuController extends AbstractController
{
    private SiatuResource $siatuResource;
    private EntityManagerInterface $entityManager;

    public function __construct(SiatuResource $siatuResource, EntityManagerInterface $entityManager)
    {
        $this->siatuResource = $siatuResource;
        $this->entityManager = $entityManager;
    }

    #[Route("/api/importar", methods: ["POST"])]

    public function importarDados(): Response
    {
        // Importar contribuintes

        $contribuintes = $this->siatuResource->getContribuintes();



        $certidaoDivida = $this->siatuResource->getContribuintesCertidaoSupp();

        foreach ($contribuintes as $contribuinteDTO) {

           
            $contribuinte = new ContribuinteSupp();
            //$contribuinte->setId($contribuinteDTO->id);
            $contribuinte->setNome($contribuinteDTO->nome);
            $contribuinte->setCpf($contribuinteDTO->cpf);
            $contribuinte->setEndereco($contribuinteDTO->endereco);
            $contribuinte->setIdContribuinteSiatu($contribuinteDTO->id_contribuinte_siatu);
            //$contribuinte->addCertidaoDividaSupp($contribuinteDTO->);
            $this->entityManager->persist($contribuinte);
            //$this->entityManager->flush(); // Gerar o ID do contribuinte


            $contribuinteId = $contribuinte->getId();;


            foreach ($certidaoDivida as $certidaoDTO) {
            
                if (!CertidaoDividaRules::validate($certidaoDTO)) {
                    continue;
                }

                if ($certidaoDTO->id_contribuinte_siatu == $contribuinteDTO->id) {
                    $contribuinteSupp = $this->entityManager->getRepository(ContribuinteSupp::class)->find($contribuinteId);

                    

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






        return new Response('Dados importados com sucesso.');
    }
}
