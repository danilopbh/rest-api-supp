<?php

namespace App\Entity;

use App\Repository\ContribuinteSiatuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContribuinteSiatuRepository::class)]
class ContribuinteSiatu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 25)]
    private ?string $cpf = null;

    #[ORM\Column(length: 255)]
    private ?string $endereco = null;

    /**
     * @var Collection<int, CertidaoDividaSiatu>
     */
    #[ORM\OneToMany(targetEntity: CertidaoDividaSiatu::class, mappedBy: 'contribuinte_siatu')]
    private Collection $certidaoDividaSiatu;

    public function __construct()
    {
        $this->certidaoDividaSiatu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, CertidaoDividaSiatu>
     */
    public function getCertidaoDividaSiatu(): Collection
    {
        return $this->certidaoDividaSiatu;
    }

    public function addCertidaoDividaSiatu(CertidaoDividaSiatu $certidaoDividaSiatu): static
    {
        if (!$this->certidaoDividaSiatu->contains($certidaoDividaSiatu)) {
            $this->certidaoDividaSiatu->add($certidaoDividaSiatu);
            $certidaoDividaSiatu->setContribuinte($this);
        }

        return $this;
    }

    public function removeCertidaoDividaSiatu(CertidaoDividaSiatu $certidaoDividaSiatu): static
    {
        if ($this->certidaoDividaSiatu->removeElement($certidaoDividaSiatu)) {
            // set the owning side to null (unless already changed)
            if ($certidaoDividaSiatu->getContribuinte() === $this) {
                $certidaoDividaSiatu->setContribuinte(null);
            }
        }

        return $this;
    }
}
