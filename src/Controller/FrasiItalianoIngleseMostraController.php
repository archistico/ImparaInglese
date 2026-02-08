<?php

namespace App\Controller;

use App\Repository\FraseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrasiItalianoIngleseMostraController extends AbstractController
{
    #[Route('/frasi_italiano_inglese/mostra/{id}', name: 'app_frasi_italiano_inglese_mostra', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function mostra(
        int $id,
        FraseRepository $fraseRepository,
        EntityManagerInterface $em
    ): Response {
        $frase = $fraseRepository->findOneForMostra($id);
        if (!$frase) {
            throw $this->createNotFoundException('Frase non trovata');
        }

        // Verifica direzione IT->EN (come in fixtures)
        $dirDesc = (string) $frase->getDirezione()->getDescrizione();
        if ($dirDesc !== 'Italiano -> Inglese') {
            throw $this->createNotFoundException('Questa frase non appartiene alla direzione Italiano -> Inglese');
        }

        $contestoId = (int) $frase->getContesto()->getId();

        // Direzione id (evito query in più usando entity già caricata)
        $direzioneId = (int) $frase->getDirezione()->getId();

        $pn = $fraseRepository->findPrevNextIds((int)$frase->getId(), $contestoId, $direzioneId);

        return $this->render('frasi_italiano_inglese/mostra.html.twig', [
            'title' => 'Frasi Italiano → Inglese',
            'frase' => $frase,
            'prevId' => $pn['prev'],
            'nextId' => $pn['next'],
        ]);
    }
}
