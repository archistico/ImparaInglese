<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminFrasiController extends AbstractController
{
    #[Route('/admin/nuova', name: 'app_admin_frasi_iten', methods: ['GET'])]
    public function nuova(): Response
    {
        return $this->render('admin/frasi_iten/nuova.html.twig', [
            'title' => 'Admin — Nuova frase (Italiano → Inglese)',
        ]);
    }
}
