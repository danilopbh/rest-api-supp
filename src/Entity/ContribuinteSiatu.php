<?php

namespace App\Entity;

use App\Repository\ContribuinteSiatuRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ContribuinteSiatuRepository::class)]
class ContribuinteSiatu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 255)]
    private ?string $cpf = null;

    #[ORM\Column(length: 255)]
    private ?string $endereco = null;

    #[ORM\OneToMany(targetEntity: CertidaoDividaSiatu::class, mappedBy: 'contribuinte_siatu')]
    private Collection $certidaoDividaSiatu;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): static
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function setEndereco(string $endereco): static
    {
        $this->endereco = $endereco;

        return $this;
    }

    public function getCertidaoDividaSiatu(): Collection
    {
        return $this->certidaoDividaSiatu;
    }
}
