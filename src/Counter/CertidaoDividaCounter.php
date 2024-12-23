<?php

namespace App\Counter;

class CertidaoDividaCounter
{
    public function contarCertidoesValidas(array $certidoes): int
    {
        return count(array_filter($certidoes, fn($certidao) => $certidao->valor >= 0));
    }
}
