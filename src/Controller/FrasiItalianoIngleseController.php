<?php

namespace App\Controller;

use App\Repository\ContestoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FraseRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class FrasiItalianoIngleseController extends AbstractController
{
    #[Route('/frasi_italiano_inglese/contesti', name: 'app_frasi_italiano_inglese', methods: ['GET'])]
    public function contesti(
        ContestoRepository $contestoRepository,
        EntityManagerInterface $em
    ): Response {
        // Recupero l'ID della direzione "Italiano -> Inglese"
        $dirId = (int) $em->createQueryBuilder()
            ->select('d.id')
            ->from('App\Entity\Direzione', 'd')
            ->where('d.descrizione = :desc')
            ->setParameter('desc', 'Italiano -> Inglese')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        $rows = $contestoRepository->findContestiWithFrasiCountByDirezione($dirId);

        return $this->render('frasi_italiano_inglese/contesti.html.twig', [
            'title' => 'Frasi Italiano → Inglese',
            'rows' => $rows,
        ]);
    }

    #[Route('/frasi_italiano_inglese/contesto/{contestoId}/inizia', name: 'app_frasi_italiano_inglese_inizia', requirements: ['contestoId' => '\d+'], methods: ['GET'])]
public function inizia(
    int $contestoId,
    FraseRepository $fraseRepository,
    EntityManagerInterface $em
): RedirectResponse {
    $dirId = (int) $em->createQueryBuilder()
        ->select('d.id')
        ->from('App\Entity\Direzione', 'd')
        ->where('d.descrizione = :desc')
        ->setParameter('desc', 'Italiano -> Inglese')
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleScalarResult();

    $firstId = $fraseRepository->findFirstIdByContestoAndDirezione($contestoId, $dirId);

    if (!$firstId) {
        // Se un contesto è vuoto, torniamo alla lista contesti
        return $this->redirectToRoute('app_frasi_italiano_inglese');
    }

    return $this->redirectToRoute('app_frasi_italiano_inglese_mostra', ['id' => $firstId]);
}
}
