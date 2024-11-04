<?php

namespace App\Entity;

use App\Repository\CertidaoDividaAtivaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertidaoDividaAtivaRepository::class)]
class CertidaoDividaAtiva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $uuid = null;

    #[ORM\Column(length: 14)]
    private ?string $num_certidao = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data_geracao = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_cancel = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $post_it = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $update_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $apagado_em = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getNumCertidao(): ?string
    {
        return $this->num_certidao;
    }

    public function setNumCertidao(string $num_certidao): static
    {
        $this->num_certidao = $num_certidao;

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

    public function getDataCancel(): ?\DateTimeInterface
    {
        return $this->data_cancel;
    }

    public function setDataCancel(?\DateTimeInterface $data_cancel): static
    {
        $this->data_cancel = $data_cancel;

        return $this;
    }

    public function getPostIt(): ?string
    {
        return $this->post_it;
    }

    public function setPostIt(?string $post_it): static
    {
        $this->post_it = $post_it;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTimeInterface $update_at): static
    {
        $this->update_at = $update_at;

        return $this;
    }

    public function getApagadoEm(): ?\DateTimeInterface
    {
        return $this->apagado_em;
    }

    public function setApagadoEm(?\DateTimeInterface $apagado_em): static
    {
        $this->apagado_em = $apagado_em;

        return $this;
    }
}
