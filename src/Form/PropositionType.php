<?php

namespace AcMarche\MeriteSportif\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class PropositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('categorie')
            ->remove('validate')
            ->add(
                'description',
                TextareaType::class,
                [
                    'help' => 'Son parcours / Historique du candidat',
                    'attr' => ['rows' => 5]
                ]
            )
            ->add(
                'palmares',
                TextareaType::class,
                [
                    'attr' => ['rows' => 5]
                ]
            );
    }

    public function getParent(): ?string
    {
        return CandidatType::class;
    }


}
