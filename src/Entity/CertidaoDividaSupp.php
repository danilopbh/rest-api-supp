<?php

namespace App\Entity;

use App\Repository\CertidaoDividaSuppRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertidaoDividaSuppRepository::class)]
class CertidaoDividaSupp
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

    #[ORM\ManyToOne(inversedBy: 'certidaoDividaSupp')]
    private ?ContribuinteSupp $contribuinte_supp = null;

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

    public function getContribuinteSupp(): ?ContribuinteSupp
    {
        return $this->contribuinte_supp;
    }

    public function setContribuinteSupp(?ContribuinteSupp $contribuinte_supp): static
    {
        $this->contribuinte_supp = $contribuinte_supp;

        return $this;
    }
}