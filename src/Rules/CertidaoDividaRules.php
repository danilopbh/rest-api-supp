<?php

namespace App\Rules;

class CertidaoDividaRules
{
    public static function validate($certidao): array
    {
        $errors = [];

        if ($certidao->valor <= 0) {
            $errors['cda'] = $certidao->id;
            $errors['contribuinte'] = $certidao->id_contribuinte_siatu;
            $errors['valor'] = 'O valor deve ser maior que 0';
            
        } 
        // Exemplo de regra de validação
        return $errors;
    }
}
