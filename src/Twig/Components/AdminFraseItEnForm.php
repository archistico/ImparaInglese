<?php

namespace App\Twig\Components;

use App\Entity\Contesto;
use App\Entity\Direzione;
use App\Entity\Espressione;
use App\Entity\Frase;
use App\Entity\Lingua;
use App\Entity\Livello;
use App\Entity\Traduzione;
use App\Form\AdminFraseItEnType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('AdminFraseItEnForm')]
final class AdminFraseItEnForm
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ValidatableComponentTrait;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $em,
    ) {}

    #[LiveProp]
    public bool $submitted = false;

    public ?string $success = null;

    protected function instantiateForm(): FormInterface
    {
        // data iniziale
        $data = [
            'contestoId' => null,
            'livelloId' => null,
            'italianoTesto' => '',
            'italianoInfo' => null,
            'traduzioni' => [
                ['testo' => '', 'info' => null],
            ],
        ];

        return $this->formFactory->create(AdminFraseItEnType::class, $data);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->success = null;
        $this->submitted = true;

        // 1) submit form con i dati live
        $this->submitForm();

        // 2) valida (popola gli errori nel form)
        if (!$this->isValid()) {
            return;
        }

        /** @var array $data */
        $data = $this->getForm()->getData();

        $contesto = $this->em->find(Contesto::class, (int)$data['contestoId']);
        $livello  = $this->em->find(Livello::class, (int)$data['livelloId']);

        $it = $this->em->getRepository(Lingua::class)->findOneBy(['descrizione' => 'Italiano']);
        $en = $this->em->getRepository(Lingua::class)->findOneBy(['descrizione' => 'Inglese']);
        $dirItEn = $this->em->getRepository(Direzione::class)->findOneBy(['descrizione' => 'Italiano -> Inglese']);

        if (!$contesto || !$livello || !$it || !$en || !$dirItEn) {
            // errore "globale" del form
            $this->getForm()->addError(new \Symfony\Component\Form\FormError('Setup non valido (controlla fixtures).'));
            return;
        }

        $this->em->wrapInTransaction(function () use ($data, $contesto, $livello, $it, $en, $dirItEn) {
            $exprIt = (new Espressione())
                ->setLingua($it)
                ->setTesto(trim($data['italianoTesto']))
                ->setInfo($data['italianoInfo'] ? trim((string)$data['italianoInfo']) : null)
                ->setCorretta(true);
            $this->em->persist($exprIt);

            $frase = (new Frase())
                ->setContesto($contesto)
                ->setDirezione($dirItEn)
                ->setLivello($livello)
                ->setEspressione($exprIt);
            $this->em->persist($frase);

            foreach ($data['traduzioni'] as $row) {
                $txt = trim((string)($row['testo'] ?? ''));
                if ($txt === '') {
                    continue;
                }

                $exprEn = (new Espressione())
                    ->setLingua($en)
                    ->setTesto($txt)
                    ->setInfo(!empty($row['info']) ? trim((string)$row['info']) : null)
                    ->setCorretta(true);
                $this->em->persist($exprEn);

                $tr = (new Traduzione())
                    ->setFrase($frase)
                    ->setEspressione($exprEn);
                $this->em->persist($tr);
            }
        });

        $this->success = 'Salvato!';

        // reset form dopo salvataggio
        $this->resetForm();
    }
}
