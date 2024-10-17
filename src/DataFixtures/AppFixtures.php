<?php


namespace App\DataFixtures;

use App\Entity\CertidaoDividaSiatu;
use App\Entity\ContribuinteSiatu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       
        $faker = Factory::create('pt_BR'); // Para gerar dados no formato brasileiro

        for ($i = 0; $i < 20; $i++) {
            // Criar um Contribuinte
            $contribuinte = new ContribuinteSiatu();
            $contribuinte->setNome($faker->firstName . ' ' . $faker->lastName);
            $contribuinte->setCpf($faker->cpf(false));
            $contribuinte->setEndereco($faker->address);
            
          

            // Criar Certidões de Dívida para o Contribuinte
            for ($j = 0; $j < 3; $j++) {
                $certidao = new CertidaoDividaSiatu();
                $certidao->setDescricao( $faker->text() .' - '. $j);
                $certidao->setDataVencimento($faker->dateTimeThisDecade);
                $certidao->setValor($faker->randomFloat(2, 500, 10000));
                $certidao->setContribuinte($contribuinte); // Estabelecer a relação
                $pdfFileName = 'certidao_divida_' . $j . '.pdf';
                // Gerar PDF em base64
                $pdfBase64 = $this->generatePdfBase64($certidao->getDescricao());
                $certidao->setPdfDivida($pdfBase64);
                $manager->persist($certidao);
            }

            $manager->persist($contribuinte);

            // Persistir todos os dados no banco de dados
            $manager->flush();
        }
    }

    private function generatePdfBase64(string $content): string
    {
        // Criar conteúdo do PDF

        $pdfContent = "Conteúdo da dívida: " . $content ;
    

        // Criar um arquivo PDF temporário
        $pdfPath = tempnam(sys_get_temp_dir(), 'pdf') . '.pdf';
        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, $pdfContent);
        $pdf->Output($pdfPath, 'F');

        // Converter o PDF para base64
        $pdfBase64 = base64_encode(file_get_contents($pdfPath));
        unlink($pdfPath); // Deletar o arquivo temporário

        return $pdfBase64;
    }
}
