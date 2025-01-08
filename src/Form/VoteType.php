<?php

namespace AcMarche\MeriteSportif\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'candidat',
                CandidatHiddenType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'point',
                IntegerType::class,
                [
                    'attr' => ['min' => 0, 'max' => 2],
                    'required' => false,
                    'label' => 'Point(s) attribué(s)',
                ]
            );
    }
}
