<?php

namespace App\Controller;

use App\Entity\Espressione;
use App\Entity\Frase;
use App\Entity\Traduzione;
use App\Repository\ContestoRepository;
use App\Repository\DirezioneRepository;
use App\Repository\LinguaRepository;
use App\Repository\LivelloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminFrasiController extends AbstractController
{
    #[Route('/admin/nuova', name: 'app_admin_frasi_iten', methods: ['GET', 'POST'])]
    public function nuova(
        Request $request,
        ContestoRepository $contestoRepository,
        LivelloRepository $livelloRepository,
        DirezioneRepository $direzioneRepository,
        LinguaRepository $linguaRepository,
        EntityManagerInterface $em
    ): Response|RedirectResponse {
        $contesti = $contestoRepository->findBy([], ['descrizione' => 'ASC']);
        $livelli = $livelloRepository->findBy([], ['descrizione' => 'ASC']);

        $data = [
            'contestoId' => '',
            'livelloId' => '',
            'it_text' => '',
            'it_info' => '',
            'traduzioni' => '',
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

            $direzione = $direzioneRepository->findOneBy(['descrizione' => 'Italiano -> Inglese']);
            if (!$direzione) {
                $errors[] = 'Direzione "Italiano -> Inglese" non trovata.';
            }

            $linguaIt = $linguaRepository->findOneBy(['descrizione' => 'Italiano']);
            $linguaEn = $linguaRepository->findOneBy(['descrizione' => 'Inglese']);
            if (!$linguaIt || !$linguaEn) {
                $errors[] = 'Lingue Italiano/Inglese non trovate.';
            }

            if (count($errors) === 0 && $contesto && $livello && $direzione && $linguaIt && $linguaEn) {
                $exprIt = (new Espressione())
                    ->setLingua($linguaIt)
                    ->setTesto($data['it_text'])
                    ->setInfo($data['it_info'] !== '' ? $data['it_info'] : null)
                    ->setCorretta(true);
                $em->persist($exprIt);

                $frase = (new Frase())
                    ->setContesto($contesto)
                    ->setDirezione($direzione)
                    ->setLivello($livello)
                    ->setEspressione($exprIt);
                $em->persist($frase);

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

                return $this->redirectToRoute('app_admin_frasi_iten');
            }
        }

        return $this->render('admin/frasi_iten/nuova.html.twig', [
            'title' => 'Admin - Nuova frase (Italiano / Inglese)',
            'contesti' => $contesti,
            'livelli' => $livelli,
            'data' => $data,
            'errors' => $errors,
        ]);
    }
}
