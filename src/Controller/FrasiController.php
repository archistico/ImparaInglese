<?php

namespace App\Controller;

use App\Repository\ContestoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FraseRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class FrasiController extends AbstractController
{
    #[Route('/frasi_iten/contesti', name: 'app_frasi_iten', methods: ['GET'])]
    public function contesti(
        ContestoRepository $contestoRepository,
        EntityManagerInterface $em
    ): Response {
        // Recupero l'ID della direzione "Italiano -> Inglese"
        $dirId = (int) $em->createQueryBuilder()
            ->select('d.id')
            ->from('App\\Entity\\Direzione', 'd')
            ->where('d.descrizione = :desc')
            ->setParameter('desc', 'Italiano -> Inglese')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        $rows = $contestoRepository->findContestiWithFrasiCountByDirezione($dirId);

        return $this->render('frasi_iten/contesti.html.twig', [
            'title' => 'Frasi Italiano → Inglese',
            'rows' => $rows,
        ]);
    }

    #[Route('/frasi_iten/contesto/{contestoId}/inizia', name: 'app_frasi_iten_inizia', requirements: ['contestoId' => '\\d+'], methods: ['GET'])]
    public function inizia(
        int $contestoId,
        FraseRepository $fraseRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $dirId = (int) $em->createQueryBuilder()
            ->select('d.id')
            ->from('App\\Entity\\Direzione', 'd')
            ->where('d.descrizione = :desc')
            ->setParameter('desc', 'Italiano -> Inglese')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        $firstId = $fraseRepository->findRandomIdByContestoAndDirezione($contestoId, $dirId);

        if (!$firstId) {
            // Se un contesto è vuoto, torniamo alla lista contesti
            return $this->redirectToRoute('app_frasi_iten');
        }

        return $this->redirectToRoute('app_frasi_iten_mostra', ['id' => $firstId]);
    }

    #[Route('/frasi_iten/mostra/{id}', name: 'app_frasi_iten_mostra', requirements: ['id' => '\\d+'], methods: ['GET'])]
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

        if ($pn['prev'] === null) {
            $pn['prev'] = $fraseRepository->findLastIdByContestoAndDirezione($contestoId, $direzioneId);
        }
        if ($pn['next'] === null) {
            $pn['next'] = $fraseRepository->findFirstIdByContestoAndDirezione($contestoId, $direzioneId);
        }

        return $this->render('frasi_iten/mostra.html.twig', [
            'title' => 'Frasi Italiano → Inglese',
            'frase' => $frase,
            'prevId' => $pn['prev'],
            'nextId' => $pn['next'],
        ]);
    }

    #[Route('/frasi_iten/contesto/{contestoId}/lista', name: 'app_frasi_iten_contesto_lista', requirements: ['contestoId' => '\d+'], methods: ['GET'])]
    public function listaContesto(
        int $contestoId,
        ContestoRepository $contestoRepository,
        FraseRepository $fraseRepository,
        EntityManagerInterface $em
    ): Response {
        $contesto = $contestoRepository->find($contestoId);
        if (!$contesto) {
            throw $this->createNotFoundException('Contesto non trovato');
        }

        $dirId = (int) $em->createQueryBuilder()
            ->select('d.id')
            ->from('App\Entity\Direzione', 'd')
            ->where('d.descrizione = :desc')
            ->setParameter('desc', 'Italiano -> Inglese')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        $frasi = $fraseRepository->findAllForContestoDirezione($contestoId, $dirId);

        return $this->render('frasi_iten/contesto_lista.html.twig', [
            'title' => 'Frasi Italiano -> Inglese',
            'contesto' => $contesto,
            'frasi' => $frasi,
        ]);
    }

    #[Route('/frasi_iten/lista', name: 'app_frasi_iten_lista', methods: ['GET'])]
    public function lista(FraseRepository $fraseRepository): Response
    {
        $frasi = $fraseRepository->findAllForItenList();

        return $this->render('frasi_iten/lista.html.twig', [
            'title' => 'Lista Frasi Italiano → Inglese',
            'frasi' => $frasi,
        ]);
    }
}
