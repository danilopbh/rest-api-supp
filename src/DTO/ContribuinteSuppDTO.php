<?php

namespace App\DTO;

class ContribuinteSuppDTO
{
    public int $id;
    public string $nome;
    public string $cpf;
    public string $endereco;
    public int $id_contribuinte_siatu;

    public function __construct(int $id, string $nome, string $cpf, string $endereco, int $id_contribuinte_siatu)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->endereco = $endereco;
        $this->id_contribuinte_siatu = $id_contribuinte_siatu;
    }
}
