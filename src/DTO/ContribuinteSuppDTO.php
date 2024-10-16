<?php

namespace App\DTO;

class ContribuinteSuppDTO
{
    public int $id;
    public string $nome;
    public string $cpf;
    public string $endereco;

    public function __construct(int $id, string $nome, string $cpf, string $endereco)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->endereco = $endereco;
    }
}