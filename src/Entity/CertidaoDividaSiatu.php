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

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $pdfdivida;

    #[ORM\Column(length: 255)]
    private ?string $descricao = null;

    #[ORM\ManyToOne(inversedBy: 'contribuinte_siatu')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ContribuinteSiatu $contribuinte_siatu = null;

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

    public function getContribuinteSiatu(): ?ContribuinteSiatu
    {
        return $this->contribuinte_siatu;
    }

    public function setContribuinteSiatu(?ContribuinteSiatu $contribuinte_siatu): static
    {
        $this->contribuinte_siatu = $contribuinte_siatu;

        return $this;
    }
}
