<?php

namespace App\Controller;

use App\Repository\ContestoRepository;
use App\Repository\FraseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrasiEnItController extends AbstractController
{
    #[Route('/frasi_enit/contesti', name: 'app_frasi_enit', methods: ['GET'])]
    public function contesti(
        ContestoRepository $contestoRepository,
        EntityManagerInterface $em
    ): Response {
        $dirId = (int) $em->createQueryBuilder()
            ->select('d.id')
            ->from('App\Entity\Direzione', 'd')
            ->where('d.descrizione = :desc')
            ->setParameter('desc', 'Inglese -> Italiano')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        $rows = $contestoRepository->findContestiWithFrasiCountByDirezione($dirId);

        return $this->render('frasi_enit/contesti.html.twig', [
            'title' => 'Frasi Inglese -> Italiano',
            'rows' => $rows,
        ]);
    }

    #[Route('/frasi_enit/contesto/{contestoId}/inizia', name: 'app_frasi_enit_inizia', requirements: ['contestoId' => '\d+'], methods: ['GET'])]
    public function inizia(
        int $contestoId,
        FraseRepository $fraseRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $dirId = (int) $em->createQueryBuilder()
            ->select('d.id')
            ->from('App\Entity\Direzione', 'd')
            ->where('d.descrizione = :desc')
            ->setParameter('desc', 'Inglese -> Italiano')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        $firstId = $fraseRepository->findRandomIdByContestoAndDirezione($contestoId, $dirId);

        if (!$firstId) {
            return $this->redirectToRoute('app_frasi_enit');
        }

        return $this->redirectToRoute('app_frasi_enit_mostra', ['id' => $firstId]);
    }

    #[Route('/frasi_enit/mostra/{id}', name: 'app_frasi_enit_mostra', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function mostra(
        int $id,
        FraseRepository $fraseRepository
    ): Response {
        $frase = $fraseRepository->findOneForMostra($id);
        if (!$frase) {
            throw $this->createNotFoundException('Frase non trovata');
        }

        $dirDesc = (string) $frase->getDirezione()->getDescrizione();
        if ($dirDesc !== 'Inglese -> Italiano') {
            throw $this->createNotFoundException('Questa frase non appartiene alla direzione Inglese -> Italiano');
        }

        $contestoId = (int) $frase->getContesto()->getId();
        $direzioneId = (int) $frase->getDirezione()->getId();

        $pn = $fraseRepository->findPrevNextIds((int)$frase->getId(), $contestoId, $direzioneId);

        if ($pn['prev'] === null) {
            $pn['prev'] = $fraseRepository->findLastIdByContestoAndDirezione($contestoId, $direzioneId);
        }
        if ($pn['next'] === null) {
            $pn['next'] = $fraseRepository->findFirstIdByContestoAndDirezione($contestoId, $direzioneId);
        }

        return $this->render('frasi_enit/mostra.html.twig', [
            'title' => 'Frasi Inglese -> Italiano',
            'frase' => $frase,
            'prevId' => $pn['prev'],
            'nextId' => $pn['next'],
        ]);
    }
}
