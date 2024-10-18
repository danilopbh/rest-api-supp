<?php

namespace App\Mapper;

use App\DTO\ContribuinteSuppDTO;

class ContribuinteSuppMapper
{
    public static function map(array $data): ContribuinteSuppDTO
    {
        return new ContribuinteSuppDTO(
            $data['id'],
            $data['nome'],
            $data['cpf'],
            $data['endereco'],
            $data['id_contribuinte_siatu'],
        );
    }
}