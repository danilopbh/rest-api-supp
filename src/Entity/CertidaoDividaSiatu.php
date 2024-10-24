<?php

namespace App\Entity;

use App\Repository\CertidaoDividaSiatuRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertidaoDividaSiatuRepository::class)]
class CertidaoDividaSiatu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $valor = null;

    #[ORM\Column(type: Types::BLOB)]
    private $pdfdivida;

    #[ORM\Column(length: 255)]
    private ?string $descricao = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data_vencimento = null;

    #[ORM\ManyToOne(inversedBy: 'certidaoDividaSiatu')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ContribuinteSiatu $contribuinte_siatu = null;

    #[ORM\Column(length: 20)]
    private ?string $situacao = 'Ativa';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data_situacao = null;

    public function __construct()
    {
        // Define a data_situacao como a data e hora atual
        $this->data_situacao = new \DateTime(); // Note que o formato de \DateTime será ajustado ao persistir
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): static
    {
        $this->valor = $valor;

        return $this;
    }

    public function getPdfdivida()
    {
        return $this->pdfdivida;
    }

    public function setPdfdivida($pdfdivida): static
    {
        $this->pdfdivida = $pdfdivida;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getDataVencimento(): ?\DateTimeInterface
    {
        return $this->data_vencimento;
    }

    public function setDataVencimento(\DateTimeInterface $data_vencimento): static
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }

    public function getContribuinte(): ?ContribuinteSiatu
    {
        return $this->contribuinte_siatu;
    }

    public function setContribuinte(?ContribuinteSiatu $contribuinte_siatu): static
    {
        $this->contribuinte_siatu = $contribuinte_siatu;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): static
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getDataSituacao(): ?\DateTime
    {
        return $this->data_situacao;
    }

    public function setDataSituacao(\DateTime $dataSituacao): self
    {
        $this->data_situacao = $dataSituacao;

        return $this;
    }
}
