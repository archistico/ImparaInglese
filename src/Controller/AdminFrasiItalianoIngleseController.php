<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminFrasiItalianoIngleseController extends AbstractController
{
    #[Route('/admin/nuova', name: 'app_admin_frasi_italiano_inglese', methods: ['GET'])]
    public function nuova(): Response
    {
        return $this->render('admin/frasi_italiano_inglese/nuova.html.twig', [
            'title' => 'Admin — Nuova frase (Italiano → Inglese)',
        ]);
    }
}
