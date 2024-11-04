<?php

namespace App\Entity;

use App\Repository\AgrupamentosCdaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgrupamentosCdaRepository::class)]
class AgrupamentosCda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_agrupamento = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $data_geracao = null;

    #[ORM\Column]
    private ?int $qtd_cdas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroAgrupamento(): ?int
    {
        return $this->numero_agrupamento;
    }

    public function setNumeroAgrupamento(int $numero_agrupamento): static
    {
        $this->numero_agrupamento = $numero_agrupamento;

        return $this;
    }

    public function getDataGeracao(): ?\DateTimeInterface
    {
        return $this->data_geracao;
    }

    public function setDataGeracao(\DateTimeInterface $data_geracao): static
    {
        $this->data_geracao = $data_geracao;

        return $this;
    }

    public function getQtdCdas(): ?int
    {
        return $this->qtd_cdas;
    }

    public function setQtdCdas(int $qtd_cdas): static
    {
        $this->qtd_cdas = $qtd_cdas;

        return $this;
    }
}
