<?php

namespace App\Rules;

class ContribuinteRules
{
    public function validate(array $data): array
    {


        //print_r($data);
        $errors = [];
        
        if (empty($data['id'])) {
            $errors['id'] = 'O campo id e obrigatório.';
        } 
        if (empty($data['nome'])) {
            $errors['nome'] = 'O campo nome e obrigatorio. ' .$data['id'];
        }
        if (empty($data['cpf'])) {
            $errors['cpf'] = 'O campo cpf e obrigatorio.';
        } elseif (!preg_match('/^\d{11}$/', $data['cpf'])) {
            $errors['cpf'] = 'O campo cpf deve conter exatamente 11 digitos.';
        }
        if (empty($data['endereco'])) {
            $errors['endereco'] = 'O campo endereco e obrigatorio.';
        }

        if (!empty($errors)){
            if (!empty($data['id'])){
                $errors['id'] = $data['id'];
            }
        }

        return $errors;
    }
}