<?php

namespace App\Rules;

class CertidaoDividaRules
{
    public static function validate($certidao): bool
    {
        // Exemplo de regra de validação
        return $certidao->valor > 0;
    }
}
