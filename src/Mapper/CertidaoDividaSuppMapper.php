<?php

namespace App\Mapper;

use App\DTO\CertidaoDividaSuppDTO;

class CertidaoDividaSuppMapper
{
    public static function map(array $data): CertidaoDividaSuppDTO
    {
        return new CertidaoDividaSuppDTO(
            $data['id'],
            $data['contribuinte_supp_id'],
            $data['valor'],
            $data['pdfDivida'],
            $data['descricao'],
            new \DateTime($data['dataVencimento']),
            $data['id_contribuinte_siatu']
        );
    }
}
