<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class VoteType extends AbstractType
{
    public function __construct(private CandidatRepository $candidatRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                    'label' => 'Point(s) attribué(s)'
                ]
            );
    }
}
