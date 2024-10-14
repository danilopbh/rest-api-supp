<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ContribuinteSuppController extends AbstractController
{
    #[Route('/contribuinte/supp', name: 'app_contribuinte_supp')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ContribuinteSuppController.php',
        ]);
    }
}
