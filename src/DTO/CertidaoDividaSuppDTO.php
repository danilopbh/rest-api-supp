<?php

namespace App\DTO;

class CertidaoDividaSuppDTO
{
    public int $id;
    public int $contribuinteSuppId;
    public float $valor;
    public string $descricao;
    public $pdfDivida; // Aqui o blob é tratado como um dado genérico
    public \DateTimeInterface  $dataVencimento;

    public function __construct(int $id, int $contribuinteSuppId, float $valor, $pdfDivida, string $descricao, \DateTimeInterface  $dataVencimento)
    {
        $this->id = $id;
        $this->contribuinteSuppId = $contribuinteSuppId;
        $this->valor = $valor;
        $this->pdfDivida = $pdfDivida;
        $this->descricao = $descricao;
        $this->dataVencimento = $dataVencimento;
    }
}
