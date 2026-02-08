<?php

namespace App\Controller;

use App\Entity\Espressione;
use App\Entity\Traduzione;
use App\Repository\ContestoRepository;
use App\Repository\FraseRepository;
use App\Repository\LinguaRepository;
use App\Repository\LivelloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminFrasiGestioneController extends AbstractController
{
    #[Route('/admin/frasi', name: 'app_admin_frasi', methods: ['GET'])]
    public function index(FraseRepository $fraseRepository): Response
    {
        $frasi = $fraseRepository->findAllForAdminFrasiList();

        return $this->render('admin/frasi/index.html.twig', [
            'title' => 'Admin — Frasi',
            'frasi' => $frasi,
        ]);
    }

    #[Route('/admin/frasi/{id}/modifica', name: 'app_admin_frasi_modifica', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function modifica(
        int $id,
        Request $request,
        FraseRepository $fraseRepository,
        ContestoRepository $contestoRepository,
        LivelloRepository $livelloRepository,
        LinguaRepository $linguaRepository,
        EntityManagerInterface $em
    ): Response|RedirectResponse {
        $frase = $fraseRepository->findOneForMostra($id);
        if (!$frase) {
            throw $this->createNotFoundException('Frase non trovata');
        }

        $contesti = $contestoRepository->findBy([], ['descrizione' => 'ASC']);
        $livelli = $livelloRepository->findBy([], ['descrizione' => 'ASC']);

        $traduzioniLines = [];
        foreach ($frase->getTraduzioni() as $t) {
            $text = (string)$t->getEspressione()->getTesto();
            $info = (string)$t->getEspressione()->getInfo();
            $traduzioniLines[] = $info !== '' ? ($text . ' | ' . $info) : $text;
        }

        $data = [
            'contestoId' => (string)$frase->getContesto()->getId(),
            'livelloId' => (string)$frase->getLivello()->getId(),
            'it_text' => (string)$frase->getEspressione()->getTesto(),
            'it_info' => (string)$frase->getEspressione()->getInfo(),
            'traduzioni' => implode("\n", $traduzioniLines),
        ];
        $errors = [];

        if ($request->isMethod('POST')) {
            $data['contestoId'] = trim((string)$request->request->get('contesto_id', ''));
            $data['livelloId'] = trim((string)$request->request->get('livello_id', ''));
            $data['it_text'] = trim((string)$request->request->get('it_text', ''));
            $data['it_info'] = trim((string)$request->request->get('it_info', ''));
            $data['traduzioni'] = trim((string)$request->request->get('traduzioni', ''));

            if ($data['contestoId'] === '') {
                $errors[] = 'Seleziona un contesto.';
            }
            if ($data['livelloId'] === '') {
                $errors[] = 'Seleziona un livello.';
            }
            if ($data['it_text'] === '') {
                $errors[] = 'Inserisci il testo della frase in italiano.';
            }

            $traduzioniList = [];
            if ($data['traduzioni'] !== '') {
                $lines = preg_split('/\R/', $data['traduzioni']);
                foreach ($lines as $line) {
                    $line = trim((string)$line);
                    if ($line === '') {
                        continue;
                    }
                    $text = $line;
                    $info = null;
                    if (str_contains($line, '|')) {
                        [$left, $right] = array_map('trim', explode('|', $line, 2));
                        if ($left !== '') {
                            $text = $left;
                            $info = $right !== '' ? $right : null;
                        }
                    }
                    if ($text !== '') {
                        $traduzioniList[] = ['text' => $text, 'info' => $info];
                    }
                }
            }

            if (count($traduzioniList) === 0) {
                $errors[] = 'Inserisci almeno una traduzione (una per riga).';
            }

            $contesto = null;
            $livello = null;
            if ($data['contestoId'] !== '') {
                $contesto = $contestoRepository->find((int)$data['contestoId']);
                if (!$contesto) {
                    $errors[] = 'Contesto non valido.';
                }
            }
            if ($data['livelloId'] !== '') {
                $livello = $livelloRepository->find((int)$data['livelloId']);
                if (!$livello) {
                    $errors[] = 'Livello non valido.';
                }
            }

            $linguaEn = $linguaRepository->findOneBy(['descrizione' => 'Inglese']);
            if (!$linguaEn) {
                $errors[] = 'Lingua Inglese non trovata.';
            }

            if (count($errors) === 0 && $contesto && $livello && $linguaEn) {
                $frase->setContesto($contesto);
                $frase->setLivello($livello);
                $frase->getEspressione()
                    ->setTesto($data['it_text'])
                    ->setInfo($data['it_info'] !== '' ? $data['it_info'] : null);

                foreach ($frase->getTraduzioni() as $oldTrad) {
                    $em->remove($oldTrad);
                }

                foreach ($traduzioniList as $t) {
                    $exprEn = (new Espressione())
                        ->setLingua($linguaEn)
                        ->setTesto($t['text'])
                        ->setInfo($t['info'])
                        ->setCorretta(true);
                    $em->persist($exprEn);

                    $trad = (new Traduzione())
                        ->setFrase($frase)
                        ->setEspressione($exprEn);
                    $em->persist($trad);
                }

                $em->flush();

                $this->addFlash('success', 'Frase aggiornata correttamente.');
                return $this->redirectToRoute('app_admin_frasi');
            }
        }

        return $this->render('admin/frasi/modifica.html.twig', [
            'title' => 'Admin — Modifica frase',
            'frase' => $frase,
            'contesti' => $contesti,
            'livelli' => $livelli,
            'data' => $data,
            'errors' => $errors,
        ]);
    }
}
