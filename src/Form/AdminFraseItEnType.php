<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AdminFraseItEnType extends AbstractType
{
    public function __construct(
        private readonly \Doctrine\ORM\EntityManagerInterface $em
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Choices da DB (semplice: id => label)
        $contesti = $this->em->getRepository(\App\Entity\Contesto::class)
            ->createQueryBuilder('c')->orderBy('c.descrizione', 'ASC')->getQuery()->getResult();

        $livelli = $this->em->getRepository(\App\Entity\Livello::class)
            ->createQueryBuilder('l')->orderBy('l.id', 'ASC')->getQuery()->getResult();

        $builder
            ->add('contestoId', ChoiceType::class, [
                'label' => 'Contesto',
                'choices' => array_combine(
                    array_map(fn($c) => $c->getDescrizione(), $contesti),
                    array_map(fn($c) => $c->getId(), $contesti),
                ),
                'placeholder' => '— seleziona —',
                'constraints' => [
                    new Assert\NotBlank(message: 'Seleziona un contesto.'),
                ],
            ])
            ->add('livelloId', ChoiceType::class, [
                'label' => 'Livello',
                'choices' => array_combine(
                    array_map(fn($l) => $l->getDescrizione(), $livelli),
                    array_map(fn($l) => $l->getId(), $livelli),
                ),
                'placeholder' => '— seleziona —',
                'constraints' => [
                    new Assert\NotBlank(message: 'Seleziona un livello.'),
                ],
            ])
            ->add('italianoTesto', TextareaType::class, [
                'label' => 'Frase in italiano',
                'attr' => ['rows' => 2],
                'constraints' => [
                    new Assert\NotBlank(message: 'Inserisci la frase in italiano.'),
                    new Assert\Length(max: 2000),
                ],
            ])
            ->add('italianoInfo', TextType::class, [
                'label' => 'Info (opz.)',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('traduzioni', LiveCollectionType::class, [
                'entry_type' => TraduzioneRowType::class,
                'constraints' => [
                    new Assert\Callback(function ($rows, ExecutionContextInterface $context) {
                        $hasText = false;
            
                        foreach ($rows as $row) {
                            if (!empty(trim((string)($row['testo'] ?? '')))) {
                                $hasText = true;
                                break;
                            }
                        }
            
                        if (!$hasText) {
                            $context
                                ->buildViolation('Inserisci almeno una traduzione in inglese.')
                                ->addViolation();
                        }
                    }),
                ],
            ]);
    }
}
