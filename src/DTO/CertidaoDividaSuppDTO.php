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
    public int $id_contribuinte_siatu;
    public string $situacao;
    public \DateTimeInterface  $dataSituacao;
 

    public function __construct(
        int $id, 
        int $contribuinteSuppId, 
        float $valor, 
        $pdfDivida, 
        string $descricao,
        \DateTimeInterface  $dataVencimento, 
        int $id_contribuinte_siatu,
        string $situacao,
        \DateTimeInterface  $dataSituacao,

        )
    {
        $this->id = $id;
        $this->contribuinteSuppId = $contribuinteSuppId;
        $this->valor = $valor;
        $this->pdfDivida = $pdfDivida;
        $this->descricao = $descricao;
        $this->dataVencimento = $dataVencimento;
        $this->id_contribuinte_siatu = $id_contribuinte_siatu;
        $this->situacao = $situacao;
        $this->dataSituacao = $dataSituacao;
    }
}
