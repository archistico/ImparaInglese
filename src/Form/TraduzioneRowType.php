<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TraduzioneRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('testo', TextareaType::class, [
                'label' => 'Testo',
                'attr' => ['rows' => 2],
                'constraints' => [
                    new Assert\NotBlank(message: 'Inserisci la traduzione in inglese.'),
                    new Assert\Length(max: 2000),
                ],
            ])
            ->add('info', TextType::class, [
                'label' => 'Info (opz.)',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                ],
            ]);
    }
}
