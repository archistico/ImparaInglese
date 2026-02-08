<?php

namespace App\Controller;

use App\Entity\Espressione;
use App\Entity\Contesto;
use App\Entity\Livello;
use App\Entity\Traduzione;
use App\Repository\ContestoRepository;
use App\Repository\DirezioneRepository;
use App\Repository\FraseRepository;
use App\Repository\LinguaRepository;
use App\Repository\LivelloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    #[Route('/admin/frasi/{id}/elimina', name: 'app_admin_frasi_elimina', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function elimina(
        int $id,
        Request $request,
        FraseRepository $fraseRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $frase = $fraseRepository->findOneForMostra($id);
        if (!$frase) {
            throw $this->createNotFoundException('Frase non trovata');
        }

        if (!$this->isCsrfTokenValid('delete_frase_' . $id, (string)$request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF non valido.');
            return $this->redirectToRoute('app_admin_frasi');
        }

        foreach ($frase->getTraduzioni() as $oldTrad) {
            $em->remove($oldTrad);
        }
        $em->remove($frase);
        $em->flush();

        $this->addFlash('success', 'Frase eliminata correttamente.');
        return $this->redirectToRoute('app_admin_frasi');
    }

    #[Route('/admin/frasi/export', name: 'app_admin_frasi_export', methods: ['GET'])]
    public function export(FraseRepository $fraseRepository): Response
    {
        $frasi = $fraseRepository->findAllForAdminFrasiList();

        $payload = [
            'version' => 1,
            'frasi' => [],
        ];

        foreach ($frasi as $f) {
            $traduzioni = [];
            foreach ($f->getTraduzioni() as $t) {
                $traduzioni[] = [
                    'testo' => $t->getEspressione()->getTesto(),
                    'info' => $t->getEspressione()->getInfo(),
                ];
            }

            $payload['frasi'][] = [
                'contesto' => $f->getContesto()->getDescrizione(),
                'livello' => $f->getLivello()->getDescrizione(),
                'direzione' => $f->getDirezione()->getDescrizione(),
                'frase' => [
                    'testo' => $f->getEspressione()->getTesto(),
                    'info' => $f->getEspressione()->getInfo(),
                ],
                'traduzioni' => $traduzioni,
            ];
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $response = new Response($json ?? '[]');
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'frasi-export.json'
        );
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    #[Route('/admin/frasi/import', name: 'app_admin_frasi_import', methods: ['GET', 'POST'])]
    public function import(
        Request $request,
        FraseRepository $fraseRepository,
        ContestoRepository $contestoRepository,
        LivelloRepository $livelloRepository,
        DirezioneRepository $direzioneRepository,
        LinguaRepository $linguaRepository,
        EntityManagerInterface $em
    ): Response|RedirectResponse {
        $data = [
            'json' => '',
        ];
        $errors = [];

        if ($request->isMethod('POST')) {
            $data['json'] = trim((string)$request->request->get('json', ''));
            $file = $request->files->get('json_file');
            if ($data['json'] === '' && $file) {
                $data['json'] = trim((string)@file_get_contents($file->getPathname()));
            }

            if ($data['json'] === '') {
                $errors[] = 'Inserisci o carica un JSON valido.';
            } else {
                $decoded = json_decode($data['json'], true);
                if (!is_array($decoded)) {
                    $errors[] = 'JSON non valido.';
                } else {
                    $items = $decoded['frasi'] ?? $decoded;
                    if (!is_array($items)) {
                        $errors[] = 'Formato JSON non valido: manca "frasi".';
                    }
                }
            }

            $linguaIt = $linguaRepository->findOneBy(['descrizione' => 'Italiano']);
            $linguaEn = $linguaRepository->findOneBy(['descrizione' => 'Inglese']);
            if (!$linguaIt || !$linguaEn) {
                $errors[] = 'Lingue Italiano/Inglese non trovate.';
            }

            if (count($errors) === 0) {
                $items = $decoded['frasi'] ?? $decoded;
                $created = 0;

                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $contestoName = trim((string)($item['contesto'] ?? ''));
                    $livelloName = trim((string)($item['livello'] ?? ''));
                    $direzioneName = trim((string)($item['direzione'] ?? 'Italiano -> Inglese'));
                    $fraseData = $item['frase'] ?? [];
                    $fraseText = trim((string)($fraseData['testo'] ?? $fraseData['text'] ?? ''));
                    $fraseInfo = $fraseData['info'] ?? null;
                    $traduzioni = $item['traduzioni'] ?? [];

                    if ($contestoName === '' || $livelloName === '' || $fraseText === '' || !is_array($traduzioni)) {
                        continue;
                    }

                    $contesto = $contestoRepository->findOneBy(['descrizione' => $contestoName]);
                    if (!$contesto) {
                        $contesto = (new Contesto())->setDescrizione($contestoName);
                        $em->persist($contesto);
                    }

                    $livello = $livelloRepository->findOneBy(['descrizione' => $livelloName]);
                    if (!$livello) {
                        $livello = (new Livello())->setDescrizione($livelloName);
                        $em->persist($livello);
                    }

                    $direzione = $direzioneRepository->findOneBy(['descrizione' => $direzioneName]);
                    if (!$direzione) {
                        $errors[] = 'Direzione non trovata: ' . $direzioneName;
                        continue;
                    }

                    $fraseExpr = (new Espressione())
                        ->setLingua($direzioneName === 'Italiano -> Inglese' ? $linguaIt : $linguaEn)
                        ->setTesto($fraseText)
                        ->setInfo($fraseInfo !== '' ? $fraseInfo : null)
                        ->setCorretta(true);
                    $em->persist($fraseExpr);

                    $frase = (new \App\Entity\Frase())
                        ->setContesto($contesto)
                        ->setDirezione($direzione)
                        ->setLivello($livello)
                        ->setEspressione($fraseExpr);
                    $em->persist($frase);

                    foreach ($traduzioni as $t) {
                        if (!is_array($t)) {
                            continue;
                        }
                        $tText = trim((string)($t['testo'] ?? $t['text'] ?? ''));
                        if ($tText === '') {
                            continue;
                        }
                        $tInfo = $t['info'] ?? null;
                        $tradExpr = (new Espressione())
                            ->setLingua($direzioneName === 'Italiano -> Inglese' ? $linguaEn : $linguaIt)
                            ->setTesto($tText)
                            ->setInfo($tInfo !== '' ? $tInfo : null)
                            ->setCorretta(true);
                        $em->persist($tradExpr);

                        $trad = (new Traduzione())
                            ->setFrase($frase)
                            ->setEspressione($tradExpr);
                        $em->persist($trad);
                    }

                    $created++;
                }

                if (count($errors) === 0) {
                    $em->flush();
                    $this->addFlash('success', 'Import completato. Frasi create: ' . $created);
                    return $this->redirectToRoute('app_admin_frasi');
                }
            }
        }

        return $this->render('admin/frasi/import.html.twig', [
            'title' => 'Admin — Import Frasi',
            'data' => $data,
            'errors' => $errors,
        ]);
    }
}
