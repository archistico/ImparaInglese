<?php

namespace App\Controller;

use App\Repository\ContestoRepository;
use App\Repository\FraseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminContestiController extends AbstractController
{
    #[Route('/admin/contesti', name: 'app_admin_contesti', methods: ['GET'])]
    public function index(ContestoRepository $contestoRepository): Response
    {
        $contesti = $contestoRepository->findBy([], ['descrizione' => 'ASC']);

        return $this->render('admin/contesti/index.html.twig', [
            'title' => 'Admin — Contesti',
            'contesti' => $contesti,
        ]);
    }

    #[Route('/admin/contesti/{id}', name: 'app_admin_contesti_mostra', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function mostra(int $id, ContestoRepository $contestoRepository, FraseRepository $fraseRepository): Response
    {
        $contesto = $contestoRepository->find($id);
        if (!$contesto) {
            throw $this->createNotFoundException('Contesto non trovato');
        }

        $frasi = $fraseRepository->findAllForAdminContesto($id);

        return $this->render('admin/contesti/mostra.html.twig', [
            'title' => 'Admin — Frasi per contesto',
            'contesto' => $contesto,
            'frasi' => $frasi,
        ]);
    }

    #[Route('/admin/contesti/{id}/modifica', name: 'app_admin_contesti_modifica', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function modifica(
        int $id,
        Request $request,
        ContestoRepository $contestoRepository,
        EntityManagerInterface $em
    ): Response|RedirectResponse {
        $contesto = $contestoRepository->find($id);
        if (!$contesto) {
            throw $this->createNotFoundException('Contesto non trovato');
        }

        $data = [
            'descrizione' => (string)$contesto->getDescrizione(),
        ];
        $errors = [];

        if ($request->isMethod('POST')) {
            $data['descrizione'] = trim((string)$request->request->get('descrizione', ''));
            if ($data['descrizione'] === '') {
                $errors[] = 'La descrizione è obbligatoria.';
            }

            if (count($errors) === 0) {
                $contesto->setDescrizione($data['descrizione']);
                $em->flush();

                $this->addFlash('success', 'Contesto aggiornato correttamente.');
                return $this->redirectToRoute('app_admin_contesti');
            }
        }

        return $this->render('admin/contesti/modifica.html.twig', [
            'title' => 'Admin — Modifica contesto',
            'contesto' => $contesto,
            'data' => $data,
            'errors' => $errors,
        ]);
    }
}
