<?php

namespace App\Controller;

use App\Repository\LivelloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminLivelliController extends AbstractController
{
    #[Route('/admin/livelli', name: 'app_admin_livelli', methods: ['GET'])]
    public function index(LivelloRepository $livelloRepository): Response
    {
        $livelli = $livelloRepository->findBy([], ['descrizione' => 'ASC']);

        return $this->render('admin/livelli/index.html.twig', [
            'title' => 'Admin — Livelli',
            'livelli' => $livelli,
        ]);
    }

    #[Route('/admin/livelli/{id}/modifica', name: 'app_admin_livelli_modifica', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function modifica(
        int $id,
        Request $request,
        LivelloRepository $livelloRepository,
        EntityManagerInterface $em
    ): Response|RedirectResponse {
        $livello = $livelloRepository->find($id);
        if (!$livello) {
            throw $this->createNotFoundException('Livello non trovato');
        }

        $data = [
            'descrizione' => (string)$livello->getDescrizione(),
        ];
        $errors = [];

        if ($request->isMethod('POST')) {
            $data['descrizione'] = trim((string)$request->request->get('descrizione', ''));
            if ($data['descrizione'] === '') {
                $errors[] = 'La descrizione è obbligatoria.';
            }

            if (count($errors) === 0) {
                $livello->setDescrizione($data['descrizione']);
                $em->flush();

                $this->addFlash('success', 'Livello aggiornato correttamente.');
                return $this->redirectToRoute('app_admin_livelli');
            }
        }

        return $this->render('admin/livelli/modifica.html.twig', [
            'title' => 'Admin — Modifica livello',
            'livello' => $livello,
            'data' => $data,
            'errors' => $errors,
        ]);
    }
}
